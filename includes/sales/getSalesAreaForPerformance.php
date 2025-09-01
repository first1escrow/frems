<?php
###############共同#####################

function getSignSales($cat, $id, $sales)
{
    global $conn;

    $salesArray = [];

    $sql   = "SELECT sSales,(SELECT pJob FROM tPeopleInfo WHERE pId = sSales) AS job FROM tSalesSign WHERE sType = '" . $cat . "' AND sStore ='" . $id . "' AND sSales != 0";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    while (! $rs->EOF) {
        $salesArray[] = ($rs->fields['sSales'] != 3) ? $sales : $rs->fields['sSales'];
        $rs->MoveNext();
    }

    if ($total == 0) { //無資料時
        foreach ($sales as $value) {
            $salesArray[] = $value;
        }
    }

    return $salesArray;
}
#######################################

###############仲介店###################
//店家區域
function getBranchzip($bid)
{
    global $conn;

    $sql = "SELECT * FROM tBranch WHERE bId ='" . $bid . "'";
    $rs  = $conn->Execute($sql);

    return $rs->fields;
}

//店家資料
function getBranch($z, $sales)
{
    global $conn;

    $i   = 0;
    $sql = "SELECT
        bId,
        bStore,
        bManager,
        bTelArea,
        bTelMain,
        bMobileNum,
        bAddress,
        CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as bCode,
        (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city,
        (SELECT zArea FROM tZipArea WHERE zZip=bZip) AS area,
        (SELECT bName FROM tBrand AS br WHERE br.bId = bBrand) AS brand,
        case bStatus when '1' then '啟用' else '停用' END as bStatus
      FROM
        tBranch
      WHERE
        bZip ='" . $z . "' AND bStatus = 1";
    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $list[$i] = $rs->fields;
        $i++;

        $rs->MoveNext();
    }

    return $list;
}

function getBranchSales($bId)
{
    global $conn;

    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS SalesName ,bSales FROM tBranchSalesForPerformance WHERE bBranch = '" . $bId . "'";
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        $arr[] = $rs->fields;
        $rs->MoveNext();
    }

    return $arr;
}

//仲介業務寫入
function setBranchSales($bid, $sales, $cat = '')
{
    global $conn;
    $sql = "DELETE FROM tBranchSalesForPerformance WHERE bBranch = '" . $bid . "'";
    $conn->Execute($sql);

    foreach ($sales as $value) {
        $sql = "INSERT INTO tBranchSalesForPerformance(bId, bSales, bBranch, bCreatedAt) VALUES(UUID(), '" . $value . "', '" . $bid . "', NOW())";
        $conn->Execute($sql);
    }
}

//LOG紀錄
function setSalesAreaLog($bid, $sales, $zip, $type)
{
    global $conn;

    $sql = "INSERT INTO tSalesAreaPerformanceLog (sType,sZip,sBranch,sSales) VALUES('" . $type . "','" . $zip . "','" . $bid . "','" . $sales . "')";
    $conn->Execute($sql);
}

//確認勾選狀態(縣市)
function checkCityCheck($arr, $sales)
{
    $total = count($arr);
    foreach ($arr as $key => $value) {
        $tmp[$value]++;
    }

    if ($tmp['checked'] == $total) { //區域的全選=區域總數
        $type = 'checked';
    } elseif ($tmp['checked'] > 0 || $tmp['half_checked'] > 0) {
        $type = 'half_checked';
    } else {
        $type = '';
    }

    return $type;
}

//確認勾選狀態(區域)
function checkAreaCheck($arr)
{
    $branchtotal = count($arr);

    for ($i = 0; $i < $branchtotal; $i++) {
        if ($arr[$i]['ck'] != '') {
            $branchcount++;
        }
    }

    if ($branchtotal > $branchcount && $branchcount > 0) { //區域有店但不是所有的店都是同個業務
        $type = 'half_checked';
    } elseif ($branchtotal == $branchcount && $branchcount > 0) { //區域有店所有店都同個業務
        $type = 'checked';
    } else {
        $type = '';
    }

    return $type;
}

###############仲介店###################

##############地政士####################
function getScrivenerzip($sid)
{
    global $conn;

    $sql = "SELECT * FROM tScrivener WHERE sId ='" . $sid . "'";
    $rs  = $conn->Execute($sql);

    return $rs->fields;
}

function getScrivener($z, $sales)
{
    global $conn;

    $i   = 0;
    $sql = "SELECT
        s.sId,
        s.sName,
        s.sOffice,
        sAddress,
        sMobileNum,
        sTelArea,
        sTelMain,
        CONCAT('SC',LPAD(sId,4,'0')) as sCode,
        (SELECT zCity FROM tZipArea WHERE zZip=sZip1) AS city,
        (SELECT zArea FROM tZipArea WHERE zZip=sZip1) AS area,
        case s.sStatus when '1' then '啟用' else '停用' END as sStatus
      FROM
        tScrivener AS s
      WHERE
        s.sZip1 ='" . $z . "' AND s.sStatus = 1";
    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        if ($rs->fields['sales'] == $sales) { //查詢業務跟店家業務是同一個就checked
            $rs->fields['ck'] = 'checked';
        } else {
            $rs->fields['ck'] = '';
        }

        $list[$i] = $rs->fields;
        $i++;

        $rs->MoveNext();
    }

    return $list;
}

function setScrivenerSales($sid, $sales, $cat = '')
{
    global $conn;

    $sql = "DELETE FROM tScrivenerSalesForPerformance WHERE sScrivener = '" . $sid . "'";
    $conn->Execute($sql);

    foreach ($sales as $value) {
        $sql = "INSERT INTO tScrivenerSalesForPerformance(sId, sSales, sScrivener, sCreatedAt) VALUES(UUID(), '" . $value . "', '" . $sid . "', NOW())";
        $conn->Execute($sql);
    }
}

##############地政士####################
function getScrivenerSales($id)
{
    global $conn;

    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS SalesName,sSales FROM tScrivenerSalesForPerformance WHERE sScrivener = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        $tmp[] = $rs->fields;
        $rs->MoveNext();
    }

    return $tmp;
}

#########
function checkSales($id, $sales, $b = 'b')
{
    global $conn;

    if ($b == 'b') {
        $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS SalesName ,bSales FROM tBranchSalesForPerformance WHERE bBranch = '" . $id . "' AND bSales ='" . $sales . "'";
    } else {
        $sql = "SELECT * FROM tScrivenerSalesForPerformance WHERE sScrivener ='" . $id . "' AND sSales ='" . $sales . "'";
    }
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total > 0) {
        return true;
    } else {
        return false;
    }
}

function getSalesHtml($id, $cat = 'b')
{
    global $conn;

    if ($cat == 'b') {
        $tmp = getBranchSales($id);

        for ($i = 0; $i < count($tmp); $i++) {
            // $txt .= "<span class=\"btnC\" title=\"點擊X可刪除\"><span onclick=\"delSales(" . $id . "," . $tmp[$i]['bSales'] . ",'R')\" class=\"del\" >X</span>" . $tmp[$i]['SalesName'] . "</span>";
            // $txt .= "<span class=\"btnC\ style=\"padding-right:28px;\">" . $tmp[$i]['SalesName'] . "</span>";
            $txt .= "<span>" . $tmp[$i]['SalesName'] . "</span>";
        }
    } elseif ($cat == 's') {
        $tmp = getScrivenerSales($id);

        for ($i = 0; $i < count($tmp); $i++) {
            // $txt .= "<span class=\"btnC\" title=\"點擊X可刪除\"><span onclick=\"delSales(" . $id . "," . $tmp[$i]['sSales'] . ")\" class=\"del\">X</span>" . $tmp[$i]['SalesName'] . "</span>";
            // $txt .= "<span class=\"btnC\" style=\"padding-right:28px;\">" . $tmp[$i]['SalesName'] . "</span>";
            $txt .= "<span>" . $tmp[$i]['SalesName'] . "</span>";
        }
    }

    return $txt;
}
########
#####################作廢######################

//FRO 真簽約店的
function setSingSales($sales, $bid, $type)
{
    global $conn;
}

###############################################
