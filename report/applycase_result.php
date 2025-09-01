<?php
ini_set('memory_limit', '256M');

if ($_SESSION['member_id'] == 6) {
    ini_set("display_errors", "On");
    error_reporting(E_ALL & ~E_NOTICE);
}

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/report/getBranchType.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';

//載入class
$advance = new Advance();
$tlog    = new TraceLog();

$_POST = escapeStr($_POST);

//取得仲介店名
function getRealtyName($no = 0)
{
    global $conn;
    if ($no > 0) {
        $sql = 'SELECT bStore FROM tBranch WHERE bId="' . $no . '";';
        $rs  = $conn->Execute($sql);

        return $rs->fields['bStore'];
    } else {
        return false;
    }
}

//取得仲介店編號
function getRealtyNo($lnk, $no = 0)
{ //找舊有的品牌(20150908)
    global $conn;

    if ($no > 0) {
        $sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="' . $no . '";';
        $rs  = $conn->Execute($sql);

        return empty($rs) ? false : strtoupper($rs->fields['bCode']) . str_pad($rs->fields['bId'], 5, '0', STR_PAD_LEFT);
    } else {
        return false;
    }
}

function dateCg($val)
{
    $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $val));
    $tmp = explode('-', $val);

    if (preg_match("/0000/", $tmp[0])) {
        $tmp[0] = '000';
    } else {
        $tmp[0] -= 1911;
    }

    $val = $tmp[0] . '/' . $tmp[1] . '/' . $tmp[2];
    $tmp = null;unset($tmp);

    return $val;
}

function checkSales($arr, $pId)
{
    global $conn;

    if ($_SESSION['member_pDep'] != 7) {
        return true;
    }

    $twhgCount = 0; //業務不能看直營的案件
    $branch[]  = $arr['branch'];
    if ($arr['brand'] == 1 && $arr['category'] == 2) { //仲介台屋直營
        $twhgCount++;
    }

    if ($arr['branch1'] > 0) {
        $branch[] = $arr['branch1'];
        if ($arr['brand1'] == 1 && $arr['category1'] == 2) { //仲介台屋直營
            $twhgCount++;
        }
    }

    if ($arr['branch2'] > 0) {
        $branch[] = $arr['branch2'];
        if ($arr['brand2'] == 1 && $arr['category2'] == 2) { //仲介台屋直營
            $twhgCount++;
        }
    }

    if ($twhgCount == count($branch)) { //直營不可以給業務看
        return false;
    }

    if ($_SESSION['member_test'] != 0) {
        return true;
    }

    $salesCount = 0;

    $sql = "SELECT bSales FROM tBranchSales WHERE bBranch IN(" . @implode(',', $branch) . ") AND bSales = '" . $pId . "'";
    $rs  = $conn->Execute($sql);

    $salesCount += $rs->RecordCount();

    $sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener =" . $arr['cScrivener'] . " AND sSales='" . $pId . "'";
    $rs  = $conn->Execute($sql);

    $salesCount += $rs->RecordCount();

    return ($salesCount > 0) ? true : false;
}

function getBranchSales($id)
{
    global $conn;

    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS Name FROM tBranchSales WHERE bBranch = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    $sales = [];
    while (!$rs->EOF) {
        $sales[] = $rs->fields['Name'];
        $rs->MoveNext();
    }

    return empty($sales) ? '' : implode('_', $sales);
}

function getScrivenerSales($id)
{
    global $conn;

    $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    $sales = [];
    while (!$rs->EOF) {
        $sales[] = $rs->fields['Name'];
        $rs->MoveNext();
    }

    return empty($sales) ? '' : implode('_', $sales);
}

function convertDateFormat($date)
{
    $date = explode('-', $date);
    return ($date[0] + 1911) . '-' . $date[1] . '-' . $date[2];
}

//取得所有出款保證費紀錄
function getCertifiedMoney(&$conn, $cIds)
{
    $export_data = [];

    $sql = 'SELECT
                DISTINCT tMemo,
                tMoney,
                tKind,
                tBankLoansDate
            FROM
                tBankTrans
            WHERE
                tMemo IN ("' . implode('","', $cIds) . '")
                AND tAccount IN ("' . getContractBank($conn) . '")
                AND tKind <> "利息"
                AND tPayOk=1;';
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $export_data[$rs->fields['tMemo']]['money'] = $rs->fields['tMoney'];
        $export_data[$rs->fields['tMemo']]['date']  = ($rs->fields['tKind'] == '保證費') ? $rs->fields['tBankLoansDate'] : '';

        $rs->MoveNext();
    }

    return $export_data;
}

//取得合約銀行活儲帳號
function getContractBank(&$conn)
{
    $sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow = 1 GROUP BY cBankAccount
            UNION
            SELECT cBankAccount FROM tContractRelayBank WHERE cUse = "Y";';
    $rs = $conn->Execute($sql);

    $Savings = array();
    while (!$rs->EOF) {
        $Savings[] = $rs->fields['cBankAccount'];
        $rs->MoveNext();
    }

    return implode('","', $Savings);
}

//取得所有仲介店資料
function getBranches(&$conn)
{
    $sql = 'SELECT a.bId, a.bBrand, a.bName, a.bStore, (SELECT bName FROM tBrand WHERE bId = a.bBrand) as brand FROM tBranch AS a;';
    $rs  = $conn->Execute($sql);

    $realty = [];
    while (!$rs->EOF) {
        $realty[$rs->fields['bId']] = $rs->fields;
        $rs->MoveNext();
    }

    return $realty;
}

//取得履保費出款日範圍內的cid
function getBankLoansDateCid(&$conn, $sbankLoansDate, $ebankLoansDate)
{
    $sql = "SELECT tMemo, tBankLoansDate 
            FROM `tBankTrans` 
            WHERE tKind ='保證費' AND tPayOk = 1";
    if($sbankLoansDate) {
        $sql .= " AND tBankLoansDate >= '".$sbankLoansDate."'";
    }
    if($ebankLoansDate) {
        $sql .= " AND tBankLoansDate <= '".$ebankLoansDate."'";
    }

    $rs = $conn->Execute($sql);
    $cId = [];
    while (!$rs->EOF) {
        $cId[] = $rs->fields['tMemo'];
        $rs->MoveNext();
    }
    unset($rs);

    $sql = "SELECT cCertifiedId 
            FROM `tContractCase` WHERE 1";
    if($sbankLoansDate) {
        $sql .= " AND cBankList >= '".$sbankLoansDate."'";
    }
    if($ebankLoansDate) {
        $sql .= " AND cBankList <= '".$ebankLoansDate."'";
    }
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $cId[] = $rs->fields['cCertifiedId'];
        $rs->MoveNext();
    }

    return $cId;
}

$xls = $_POST['xls'];

$bank               = $_POST['bank'];
$sApplyDate         = $_POST['sApplyDate'];
$eApplyDate         = $_POST['eApplyDate'];
$sEndDate           = $_POST['sEndDate'];
$eEndDate           = $_POST['eEndDate'];
$sSignDate          = $_POST['sSignDate'];
$eSignDate          = $_POST['eSignDate'];
$sbankLoansDate     = $_POST['sbankLoansDate'];
$ebankLoansDate     = $_POST['ebankLoansDate'];
$branch             = $_POST['branch'];
$scrivener          = $_POST['scrivener'];
$zip                = $_POST['zip'];
$citys              = $_POST['citys'];
$branchZip          = $_POST['branchZip'];
$branchCitys        = $_POST['branchCitys'];
$scrivenerZip       = $_POST['scrivenerZip'];
$scrivenerCitys     = $_POST['scrivenerCitys'];
$brand              = $_POST['brand'];
$undertaker         = $_POST['undertaker'];
$status             = $_POST['status'];
$realestate         = $_POST['realestate'];
$cCertifiedId       = $_POST['cCertifiedId'];
$buyer              = $_POST['buyer'];
$owner              = $_POST['owner'];
$show_hide          = $_POST['show_hide'];
$scrivener_category = $_POST['scrivener_category'];
$scrivenerBrand     = $_POST['scrivenerBrand'];
$report             = $_POST['report'];
$sales_performance  = $_POST['sales_performance'];

$sales = $_POST['sales'];
if (empty($sales) && empty($sales_performance)) {
    $sales = $_SESSION['member_id'];
}

$total_page   = $_POST['total_page'] + 1 - 1;
$current_page = $_POST['current_page'] + 1 - 1;
$record_limit = $_POST['record_limit'] + 1 - 1;

if (!$record_limit) {
    $record_limit = 10;
}

$query     = '';
$functions = '';

$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"'; //005030342 電子合約書測試用沒有刪的樣子

// 搜尋條件-銀行別
if ($bank) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cBank="' . $bank . '" ';
}

// 搜尋條件-進案日期
if ($sApplyDate) {
    $sApplyDate = convertDateFormat($sApplyDate);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cApplyDate>="' . $sApplyDate . ' 00:00:00" ';
}
if ($eApplyDate) {
    $eApplyDate = convertDateFormat($eApplyDate);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cApplyDate<="' . $eApplyDate . ' 23:59:59" ';
}

// 搜尋條件-結案日期
if ($sEndDate) {
    $sEndDate = convertDateFormat($sEndDate);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cEndDate>="' . $sEndDate . ' 00:00:00" ';
}

if ($eEndDate) {
    $eEndDate = convertDateFormat($eEndDate);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cEndDate<="' . $eEndDate . ' 23:59:59" ';
}

// 搜尋條件-簽約日期
if ($sSignDate) {
    $sSignDate = convertDateFormat($sSignDate);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cSignDate>="' . $sSignDate . ' 00:00:00" ';
}

if ($eSignDate) {
    $eSignDate = convertDateFormat($eSignDate);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cSignDate<="' . $eSignDate . ' 23:59:59" ';
}

// 搜尋條件-履保費出款日
if ($sbankLoansDate or $ebankLoansDate) {
    $sbankLoansDate = convertDateFormat($sbankLoansDate);
    $ebankLoansDate = convertDateFormat($ebankLoansDate);

    $cId = getBankLoansDateCid($conn, $sbankLoansDate, $ebankLoansDate);

    if(!empty($cId)) {
        $query .= empty($query) ? '' : ' AND ';
        $query .= 'cas.cCertifiedId IN (' . implode(",",$cId) .')';
    }
}

if ($_POST['branchGroup']) {
    $branchGroupData = array();

    $sql = "SELECT bId FROM tBranch WHERE bGroup = '" . $_POST['branchGroup'] . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        array_push($branchGroupData, $rs->fields['bId']);
        $rs->MoveNext();
    }

    $query .= empty($query) ? '' : ' AND ';
    $query .= '(rea.cBranchNum IN (' . @implode(',', $branchGroupData) . ') OR rea.cBranchNum1 IN (' . @implode(',', $branchGroupData) . ') OR rea.cBranchNum2 IN (' . @implode(',', $branchGroupData) . ') OR rea.cBranchNum3 IN (' . @implode(',', $branchGroupData) . '))';
}

// 搜尋條件-仲介店
if ($branch) {
    for ($i = 0; $i < count($branch); $i++) {
        $branch[$i] = str_replace('b', '', $branch[$i]);
    }

    $query .= empty($query) ? '' : ' AND ';
    $query .= '(rea.cBranchNum IN (' . @implode(',', $branch) . ') OR rea.cBranchNum1 IN (' . @implode(',', $branch) . ') OR rea.cBranchNum2 IN (' . @implode(',', $branch) . ') OR rea.cBranchNum3 IN (' . @implode(',', $branch) . '))';
}

// 搜尋條件-地政士
if ($scrivener) {
    for ($i = 0; $i < count($scrivener); $i++) {
        $scrivener[$i] = str_replace('s', '', $scrivener[$i]);
    }

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' csc.cScrivener IN (' . @implode(',', $scrivener) . ') ';
}

// 搜尋條件-買方姓名
if ($buyer) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' buy.cId="' . $buyer . '" ';
}

// 搜尋條件-賣方姓名
if ($owner) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' own.cId="' . $owner . '" ';
}

// 搜尋條件-保證號碼
if ($cCertifiedId) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cCertifiedId="' . $cCertifiedId . '" ';
}

// 搜尋條件-仲介品牌
if ((($brand != '') && !in_array($realestate, [11, 12, 13, 14, 5])) || ($report == 3)) {
    $brand = ($report == 3) ? 72 : $brand; //群義報表選擇後，強制 brand = 群義品牌(72)

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' (rea.cBrand="' . $brand . '" OR rea.cBrand1="' . $brand . '" OR rea.cBrand2="' . $brand . '" OR rea.cBrand3="' . $brand . '") ';
}

// 搜尋條件-地區
if ($zip) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' pro.cZip="' . $zip . '" ';
} else if ($citys) {
    $sql = 'SELECT zZip FROM tZipArea WHERE zCity="' . $citys . '" ORDER BY zCity,zZip ASC;';
    $rs  = $conn->Execute($sql);

    $zipArr = array();
    while (!$rs->EOF) {
        $zipArr[] = $rs->fields['zZip'];
        $rs->MoveNext();
    }

    $zipStr = implode('","', $zipArr);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' pro.cZip IN ("' . $zipStr . '") ';

    $zipArr = $zipStr = null;
    unset($zipArr, $zipStr);
} else if ($_SESSION['member_test'] != 0) {
    if ($sn == '') {
        $query .= empty($query) ? '' : ' AND ';

        $sql = "SELECT b.bId FROM `tZipArea` AS za JOIN	tBranch AS b ON b.bZip = za.zZip WHERE za.zTrainee = '" . $_SESSION['member_test'] . "'";
        $rs  = $conn->Execute($sql);

        while (!$rs->EOF) {
            $test_tmp[] = "'" . $rs->fields['bId'] . "'";
            $rs->MoveNext();
        }

        $sql = "SELECT s.sId FROM `tZipArea` AS za JOIN tScrivener AS s ON s.sCpZip1 = za.zZip WHERE za.zTrainee = '" . $_SESSION['member_test'] . "'";
        $rs  = $conn->Execute($sql);

        while (!$rs->EOF) {
            $test_tmp2[] = "'" . $rs->fields['sId'] . "'";
            $rs->MoveNext();
        }

        $query .= "(rea.cBranchNum IN(" . implode(',', $test_tmp) . ") OR rea.cBranchNum1 IN(" . implode(',', $test_tmp) . ") OR rea.cBranchNum2 IN(" . implode(',', $test_tmp) . ") OR csc.cScrivener IN (" . implode(',', $test_tmp2) . "))";

        $test_tmp = $test_tmp2 = null;
        unset($test_tmp, $test_tmp2);
    }
}

// 搜尋條件-績效業務
if(!empty($sales_performance)){
    $sql = 'SELECT zZip FROM tZipArea WHERE zPerformanceSales="' . $sales_performance . '" ORDER BY zCity,zZip ASC;';
    $rs  = $conn->Execute($sql);

    $zipArr = array();
    while (!$rs->EOF) {
        $zipArr[] = $rs->fields['zZip'];
        $rs->MoveNext();
    }

    $sql = 'SELECT zZip FROM tZipArea WHERE zPerformanceScrivenerSales="' . $sales_performance . '" ORDER BY zCity,zZip ASC;';
    $rs  = $conn->Execute($sql);

    $zipArr2 = array();
    while (!$rs->EOF) {
        $zipArr2[] = $rs->fields['zZip'];
        $rs->MoveNext();
    }

    $zipStr = implode('","', $zipArr);
    $zipStr2 = implode('","', $zipArr2);

    $query .= empty($query) ? '' : ' AND ';
    $query .= '(rea.cZip IN ("' . @implode('","', $zipArr) . '") OR rea.cZip1 IN ("' . @implode('","', $zipArr) . '") OR rea.cZip2 IN ("' . @implode('","', $zipArr) . '") OR rea.cZip3 IN ("' . @implode('","', $zipArr) . '") OR scr.sZip1 IN ("' . $zipStr2 . '"))';


    $zipArr = null;
    unset($zipArr);
}

// 搜尋條件-仲介地區
if($branchZip) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= '(rea.cZip IN ("' . $branchZip . '") OR rea.cZip1 IN ("' . $branchZip . '") OR rea.cZip2 IN ("' . $branchZip . '") OR rea.cZip3 IN ("' . $branchZip . '"))';
} elseif ($branchCitys) {
    $sql = 'SELECT zZip FROM tZipArea WHERE zCity="' . $branchCitys . '" ORDER BY zCity,zZip ASC;';
    $rs  = $conn->Execute($sql);

    $zipArr = array();
    while (!$rs->EOF) {
        $zipArr[] = $rs->fields['zZip'];
        $rs->MoveNext();
    }

    $query .= empty($query) ? '' : ' AND ';
    $query .= '(rea.cZip IN ("' . @implode('","', $zipArr) . '") OR rea.cZip1 IN ("' . @implode('","', $zipArr) . '") OR rea.cZip2 IN ("' . @implode('","', $zipArr) . '") OR rea.cZip3 IN ("' . @implode('","', $zipArr) . '"))';

    $zipArr = null;
    unset($zipArr);
}

// 搜尋條件-地政士地區
if($scrivenerZip) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' scr.sZip1="' . $scrivenerZip . '" ';
} elseif ($scrivenerCitys) {
    $sql = 'SELECT zZip FROM tZipArea WHERE zCity="' . $scrivenerCitys . '" ORDER BY zCity,zZip ASC;';
    $rs  = $conn->Execute($sql);

    $zipArr = array();
    while (!$rs->EOF) {
        $zipArr[] = $rs->fields['zZip'];
        $rs->MoveNext();
    }

    $zipStr = implode('","', $zipArr);

    $query .= empty($query) ? '' : ' AND ';
    $query .= ' scr.sZip1 IN ("' . $zipStr . '") ';

    $zipArr = $zipStr = null;
    unset($zipArr, $zipStr);
}

// 搜尋條件-承辦人
if ($undertaker) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' scr.sUndertaker1="' . $undertaker . '" ';
}

// 搜尋條件-案件狀態
$query .= empty($query) ? '' : ' AND ';
$query .= empty($status) ? ' cas.cCaseStatus<>"8" ' : ' cas.cCaseStatus="' . $status . '" ';

$t_day = ($status == '3') ? '結案日期' : '簽約日期';

//地政士類別
if ($scrivener_category) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' (find_in_set(2,scr.sBrand) AND scr.sCategory=1)';
}

//地政士合作仲介品牌
if ($scrivenerBrand) {
    if (is_array($scrivenerBrand)) {
        $scrivenerBrandTxt = @implode(',', $scrivenerBrand);
    } else {
        $scrivenerBrandTxt = $scrivenerBrand;

        $scrivenerBrandArr = explode(',', $scrivenerBrand);

        $scrivenerBrand = null;unset($scrivenerBrand);
        $scrivenerBrand = $scrivenerBrandArr;

        $scrivenerBrandArr = null;unset($scrivenerBrandArr);
    }

    $scrivenerStr = '';
    foreach ($scrivenerBrand as $k => $v) {
        $scrivenerStr .= empty($scrivenerStr) ? '' : ' OR ';
        $scrivenerStr .= ' find_in_set(' . $v . ',sBrand)';
    }

    $sql = "SELECT sId FROM tScrivener WHERE (" . $scrivenerStr . ") AND sCategory = 1 ";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $tmp[] = 'csc.cScrivener =' . $rs->fields['sId'];
        $rs->MoveNext();
    }

    $query .= empty($query) ? '' : ' AND ';
    $query .= "(" . @implode(' OR ', $tmp) . ")";

    $scrivenerStr = $tmp = null;
    unset($scrivenerStr, $tmp);
}
//報表樣式
if($report == 1 or $report == 3) {
    $query .= empty($query) ? '' : ' AND ';
    $query .= ' cas.cCaseReport = "0" ';
}

$branchGroupData = null;unset($branchGroupData);

if ($query) {
    $query = ' WHERE ' . $query;
}

$query = 'SELECT
                cas.cCertifiedId as cCertifiedId,
                cas.cApplyDate as cApplyDate,
                cas.cSignDate as cSignDate,
                cas.cFinishDate as cFinishDate,
                cas.cEndDate as cEndDate,
                cas.cEscrowBankAccount as cEscrowBankAccount,
                buy.cName as buyer,
                own.cName as owner,
                inc.cTotalMoney as cTotalMoney,
                inc.cCertifiedMoney as cCertifiedMoney,
                csc.cScrivener as cScrivener,
                (SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener,
                (SELECT b.sOffice FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as sOffice,
                (SELECT b.sCategory FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivenerCategory,
                pro.cAddr as cAddr,
                pro.cZip as cZip,
                zip.zCity as zCity,
                zip.zArea as zArea,
                CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
                CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
                CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2,
                CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand3 ),LPAD(rea.cBranchNum3,5,"0")) as bCode3,
                (SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
                (SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
                (SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
                (SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,
                (SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brandname3,
                rea.cBrand as brand,
                rea.cBrand1 as brand1,
                rea.cBrand2 as brand2,
                rea.cBrand2 as brand3,
                rea.cBranchNum as branch,
                rea.cBranchNum1 as branch1,
                rea.cBranchNum2 as branch2,
                rea.cBranchNum3 as branch3,
                rea.cServiceTarget,
                rea.cServiceTarget1,
                rea.cServiceTarget2,
                rea.cServiceTarget3,
                (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) category,
                (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) category1,
                (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) category2,
                (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum3) category3,
                (SELECT bName FROM tBranch WHERE bId=rea.cBranchNum) branchName,
                (SELECT bName FROM tBranch WHERE bId=rea.cBranchNum1) branchName1,
                (SELECT bName FROM tBranch WHERE bId=rea.cBranchNum2) branchName2,
                (SELECT bName FROM tBranch WHERE bId=rea.cBranchNum3) branchName3,
                scr.sBrand as scr_brand,
                scr.sCategory as scr_cat,
                cas.cCaseFeedBackMoney,
                cas.cCaseFeedBackMoney1,
                cas.cCaseFeedBackMoney2,
                cas.cCaseFeedBackMoney3,
                cas.cSpCaseFeedBackMoney,
                cas.cCaseFeedback,
                cas.cCaseFeedback1,
                cas.cCaseFeedback2,
                cas.cCaseFeedback3,
                cas.cCaseMoney,
                cas.cCaseReport,
                (SELECT pName FROM tPeopleInfo WHERE pId = scr.sUndertaker1) sUndertaker1
            FROM
                tContractCase AS cas
            LEFT JOIN
                tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
            LEFT JOIN
                tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
            LEFT JOIN
                tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
            LEFT JOIN
                tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
            LEFT JOIN
                tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
            LEFT JOIN
                tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
            LEFT JOIN
                tZipArea AS zip ON zip.zZip=pro.cZip
            LEFT JOIN
                tScrivener AS scr ON scr.sId = csc.cScrivener
            ' . $query . '
            GROUP BY
                cas.cCertifiedId
            ORDER BY
                cas.cApplyDate,cas.cId,cas.cSignDate ASC;';
$rs = $conn->Execute($query);

$tlog->selectWrite($_SESSION['member_id'], $query, '案件統計表搜尋');

$data = array();
while (!$rs->EOF) {
    if (checkSales($rs->fields, $sales)) {
        array_push($data, $rs->fields);
    }

    $rs->MoveNext();
}

$export_data = getCertifiedMoney($conn, array_column($data, 'cCertifiedId'));

$tbl = '';

//取得所有仲介店名
if (count($data) > 0) {
    $realty = getBranches($conn);
}

//取得所有資料
$totalMoney     = 0;
$certifiedMoney = 0;
$transMoney     = 0;
$j              = 0;
$max            = count($data);

if ($xls == 'admin') {

    $tlog->exportWrite($_SESSION['member_id'], json_encode($_POST), '案件統計表匯出行政報表');
    require_once __DIR__ . '/excel_admin.php';
}
$cCaseFeedBackMoney = 0;
for ($i = 0; $i < $max; $i++) {
    //取得仲介品牌
    $brand_111 = $data[$j]['brandname'];
    if ($data[$j]['branch1'] > 0) {
        $brand_111 = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $brand_111;
        $brand_111 .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $data[$j]['brandname1'];
    }

    if ($data[$j]['branch2'] > 0) {
        $brand_111 .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $data[$j]['brandname2'];
    }

    $data[$j]['bBrand'] = $brand_111;

    //取得各仲介店姓名
    $bStore = $realty[$data[$j]['branch']]['bStore'];
    if ($data[$j]['branch1'] > 0) {
        $bStore = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $bStore;
        $bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $realty[$data[$j]['branch1']]['bStore'];
    }

    if ($data[$j]['branch2'] > 0) {
        $bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $realty[$data[$j]['branch2']]['bStore'];
    }

    if ($data[$j]['branch3'] > 0) {
        $bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $realty[$data[$j]['branch3']]['bStore'];
    }

    $data[$j]['bStore'] = $bStore;

    // 簽約日期
    $data[$j]['cSignDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($data[$j]['cSignDate'], Base::REPORT_DATE_FORMAT_NUM_DATE));

    // 取得匯款金額
    $data[$j]['tMoney'] = $export_data[$data[$j]['cCertifiedId']]['money'];

    //取得實際出款日
    $data[$j]['tBankLoansDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($export_data[$data[$j]['cCertifiedId']]['date'], Base::REPORT_DATE_FORMAT_NUM_DATE));

    // 進案日期
    $data[$j]['cApplyDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($data[$j]['cApplyDate'], Base::REPORT_DATE_FORMAT_NUM_DATE));

    // 結案日期
    $data[$j]['cEndDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($data[$j]['cEndDate'], Base::REPORT_DATE_FORMAT_NUM_DATE));

    if ($data[$j]['branch'] > 0) {
        $tmp_sales[] = ($data[$j]['branch'] != 505) ? $data[$j]['sales'] : $data[$j]['Scrsales'];
    }

    if ($data[$j]['branch1'] > 0) {
        $tmp_sales[] = $data[$j]['sales1'];
    }

    if ($data[$j]['branch2'] > 0) {
        $tmp_sales[] = $data[$j]['sales2'];
    }

    $data[$j]['salesName'] = @implode(',', $tmp_sales);

    $tmp_sales = null;unset($tmp_sales);

    $j++;

}
$export_data = null;unset($export_data);

//決定是否剔除過濾仲介類型
if ($realestate) {
    $list = array();
    $j    = 0;

    $max = count($data);
    for ($i = 0; $i < $max; $i++) {
        $type = branch_type($conn, $data[$i]);
        if ($realestate == '11' && $type == 'O') {
            //$cat = '加盟其他品牌' ;
            $list[$j++] = $data[$i];
        } else if ($realestate == '6' && in_array($type, ['O', '3'])) {
            //他牌+非仲
            $list[$j++] = $data[$i];
        } else if ($realestate == '5' && in_array($type, ['T', 'U', '2'])) {
            //台屋集團
            $list[$j++] = $data[$i];
        } else if ($realestate == '12' && $type == 'T') {
            //$cat = '加盟台灣房屋' ;
            $list[$j++] = $data[$i];
        } else if ($realestate == '13' && $type == 'U') {
            //$cat = '加盟優美地產' ;
            $list[$j++] = $data[$i];
        } else if ($realestate == '14' && $type == 'F') {
            //$cat = '加盟永春不動產' ;
            $list[$j++] = $data[$i];
        } else if ($realestate == '1' && in_array($type, ['O', 'T', 'U', 'F'])) {
            //$cat = '所有加盟(其他品牌、台灣房屋、優美地產)' ;
            $list[$j++] = $data[$i];
        } else if ($realestate == '2' && $type == '2') {
            //$cat = '直營' ;
            //$list[$j++] = $data[$i] ;
            $list[$j++] = $data[$i];
        } else if ($realestate == '3' && $type == '3') {
            //$cat = '非仲介成交' ;
            $list[$j++] = $data[$i];
        } else if ($realestate == '4' && $type == 'N') {
            $list[$j++] = $data[$i];
        }
    }
    $data = null;unset($data);

    $data = array();
    $data = array_merge($list);

    $list = null;unset($list);
}

//計算總額
$max = count($data);
for ($i = 0; $i < $max; $i++) {
    $totalMoney += $data[$i]['cTotalMoney'];
    $certifiedMoney += $data[$i]['cCertifiedMoney'];
    $transMoney += $data[$i]['tMoney'];

    if ($branch || $brand) {
        if ($data[$i]['branch'] > 0) {
            $tmp_Store['b' . $data[$i]['branch']]['cat'] = $data[$i]['branch'];
        }

        if ($data[$i]['branch1'] > 0) {
            $tmp_Store['b' . $data[$i]['branch1']]['cat'] = $data[$i]['branch1'];
        }

        if ($data[$i]['branch2'] > 0) {
            $tmp_Store['b' . $data[$i]['branch2']]['cat'] = $data[$i]['branch2'];
        }

        if ($data[$i]['branch3'] > 0) {
            $tmp_Store['b' . $data[$i]['branch3']]['cat'] = $data[$i]['branch3'];
        }

        if ($branch) { //複選
            if (in_array($data[$i]['branch'], $branch)) {
                if ($data[$i]['cCaseFeedback'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney'];
                }
            }

            if (in_array($data[$i]['branch1'], $branch)) {
                if ($data[$i]['cCaseFeedback1'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney1'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney1'];
                }
            }

            if (in_array($data[$i]['branch2'], $branch)) {
                if ($data[$i]['cCaseFeedback2'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney2'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney2'];
                }
            }

            if (in_array($data[$i]['branch3'], $branch)) {
                if ($data[$i]['cCaseFeedback3'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney3'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney3'];
                }
            }
        } else if ($brand) {
            if ($brand == $data[$i]['brand']) {
                if ($data[$i]['cCaseFeedback'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney'];
                }
            }
            if ($brand == $data[$i]['brand1']) {
                if ($data[$i]['cCaseFeedback1'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney1'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney1'];
                }
            }

            if ($brand == $data[$i]['brand2']) {
                if ($data[$i]['cCaseFeedback2'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney2'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney2'];
                }
            }

            if ($brand == $data[$i]['brand3']) {
                if ($data[$i]['cCaseFeedback3'] == 0) {
                    $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney3'];
                    $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney3'];
                }
            }
        }

        if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
            $cCaseFeedBackMoney += $data[$i]['cSpCaseFeedBackMoney'];
            $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cSpCaseFeedBackMoney'];
        }

        //總回饋金額
        $tmp = getOtherFeed3($data[$i]['cCertifiedId']);
        if (is_array($tmp)) {
            foreach ($tmp as $k => $v) {
                if ($v['fType'] == 2) { //仲介
                    if ($branch) {
                        if (in_array($v['fStoreId'], $branch)) {
                            $cCaseFeedBackMoney += $v['fMoney'];
                            $arr[$i]['showcCaseFeedBackMoney'] += $v['fMoney'];
                        }
                    } elseif ($brand) {
                        if ($v['storeType'] == $brand) {
                            $cCaseFeedBackMoney += $v['fMoney'];
                            $arr[$i]['showcCaseFeedBackMoney'] += $v['fMoney'];
                        }
                    }
                }
            }
        }
        $tmp = null;unset($tmp);

        //計算總保證費
        // 保證費 要依回饋對像來看
        // 如果AB店配
        // 1.回饋給A或B 那麼保證費就算給A或B
        // 2.回饋給AB 那麼保證費就除以2各半
        $tmp = getcCertifiedMoney($data[$i]['cCertifiedMoney'], $tmp_Store);
        if (is_array($tmp)) {
            foreach ($tmp as $k => $v) {
                if ($branch) {
                    if (in_array($v['cat'], $branch)) {
                        $cCertifiedMoney += $v['money'];
                    }
                } elseif ($v['cat'] == $brand) {
                    $cCertifiedMoney += $v['money'];
                }
            }
        } else {
            //正常情況應該是不會發生沒有店家的問題
            $cCertifiedMoney += $data[$i]['cCertifiedMoney'];
        }

        $tmp = $tmp_Store = null;
        unset($tmp, $tmp_Store);
    } else {
        //總回饋金額
        $tmp = getOtherFeedMoney($data[$i]['cCertifiedId']);

        if ($data[$i]['brand'] > 0) {
            if ($data[$i]['cCaseFeedback'] == 0) {
                $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney'];
                $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney'];
            }
        }

        if ($data[$i]['brand1'] > 0) {
            if ($data[$i]['cCaseFeedback1'] == 0) {
                $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney1'];
                $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney1'];
            }
        }

        if ($data[$i]['brand2'] > 0) {
            if ($data[$i]['cCaseFeedback2'] == 0) {
                $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney2'];
                $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney2'];
            }
        }

        if ($data[$i]['brand3'] > 0) {
            if ($data[$i]['cCaseFeedback3'] == 0) {
                $cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney3'];
                $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney3'];
            }
        }

        if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
            $cCaseFeedBackMoney += $data[$i]['cSpCaseFeedBackMoney'];
            $data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cSpCaseFeedBackMoney'];
        }

        if ($tmp['fMoney'] > 0) {
            $cCaseFeedBackMoney += $tmp['fMoney'];
            $data[$i]['showcCaseFeedBackMoney'] += $tmp['fMoney'];
        }

        $tmp = null;unset($tmp);
    }
}

//產出excel檔
if ($xls == 'ok') {
    $tlog->exportWrite($_SESSION['member_id'], json_encode($_POST), '案件統計表excel匯出');

    //報表樣式
    if ($report == 1) { //品牌報表
        require_once __DIR__ . '/excel_brand.php';
    } else if ($report == 2) { //統計表
        require_once __DIR__ . '/excel_analysis.php';
    } else if ($report == 3) { //群義品牌報表
        require_once __DIR__ . '/excel_ci.php';
    } else { //預設
        require_once __DIR__ . '/excel.php';
    }
}

//計算總頁數
$total_page = (($max % $record_limit) == 0) ? $max / $record_limit : floor($max / $record_limit) + 1;

# 設定目前頁數顯示範圍
if ($current_page) {
    if ($current_page >= ($max / $record_limit)) {
        $current_page = ($max % $record_limit == 0) ? floor($max / $record_limit) : floor($max / $record_limit) + 1;
    }

    $i_end   = $current_page * $record_limit;
    $i_begin = $i_end - $record_limit;

    if ($i_end > $max) {
        $i_end = $max;
    }
} else {
    $i_end = $record_limit;
    if ($i_end > $max) {
        $i_end = $max;
    }

    $i_begin      = 0;
    $current_page = 1;
}

$j = 1;
for ($i = $i_begin; $i < $i_end; $i++) {
    $color_index = ($i % 2 == 0) ? '#FFFFFF' : '#F8ECE9';

    $zc                = $data[$i]['zCity'];
    $data[$i]['cAddr'] = preg_replace("/$zc/", "", $data[$i]['cAddr']);

    $zc                = $data[$i]['zArea'];
    $data[$i]['cAddr'] = preg_replace("/$zc/", '', $data[$i]['cAddr']);

    $data[$i]['cAddr'] = $data[$i]['zCity'] . $data[$i]['zArea'] . $data[$i]['cAddr'];

    $data[$i]['cCertifiedMoney'] = $data[$i]['cCertifiedMoney'];
    $tmp                         = round(($data[$i]['cTotalMoney'] - $data[$i]['cFirstMoney']) * 0.0006); //萬分之六
    $tmp2                        = round(($data[$i]['cTotalMoney'] - $data[$i]['cFirstMoney']) * 0.0006) * 0.1;

    if (($tmp - $tmp2) > $data[$i]['cCertifiedMoney']) { //合約保證費 如果未達6/10000的合約保證費  在合約保證費的金額位置 加註星星
        $data[$i]['cCertifiedMoney'] = '*' . $data[$i]['cCertifiedMoney'];
    } else {
        $data[$i]['cCertifiedMoney'] = $data[$i]['cCertifiedMoney'];
    }

    $tbl .= '
	<tr style="text-align:center;background-color:' . $color_index . '">
		<td>' . ($j++) . '</td>
		<td><a href="#" onclick=contract("' . $data[$i]['cCertifiedId'] . '")>' . $data[$i]['cCertifiedId'] . '</a>&nbsp;</td>
		<td>' . $data[$i]['bBrand'] . '</td>
		<td>' . $data[$i]['bStore'] . '&nbsp;</td>
		<td>' . $data[$i]['owner'] . '&nbsp;</td>
		<td>' . $data[$i]['buyer'] . '&nbsp;</td>
		<td style="text-align:right;">' . number_format($data[$i]['cTotalMoney']) . '&nbsp;</td>
		<td style="text-align:right;">' . $data[$i]['cCertifiedMoney'] . '&nbsp;</td>
		';

    if ($status == '3') {
        $tbl .= '<td>' . $data[$i]['cEndDate'] . '&nbsp;</td>';
    } else {
        $tbl .= '<td>' . $data[$i]['cSignDate'] . '&nbsp;</td>';
    }

    $tbl .= '
		<td>' . $data[$i]['cApplyDate'] . '&nbsp;</td>
		<td>' . $data[$i]['scrivener'] . '&nbsp;</td>
		<td>' . $data[$i]['status'] . '&nbsp;</td>
	</tr>
	';
}

if ($record_limit == 10) {$records_limit .= '<option value="10" selected="selected">10</option>' . "\n";} else { $records_limit .= '<option value="10">10</option>' . "\n";}
if ($record_limit == 50) {$records_limit .= '<option value="50" selected="selected">50</option>' . "\n";} else { $records_limit .= '<option value="50">50</option>' . "\n";}
if ($record_limit == 100) {$records_limit .= '<option value="100" selected="selected">100</option>' . "\n";} else { $records_limit .= '<option value="100">100</option>' . "\n";}
if ($record_limit == 150) {$records_limit .= '<option value="150" selected="selected">150</option>' . "\n";} else { $records_limit .= '<option value="150">150</option>' . "\n";}
if ($record_limit == 200) {$records_limit .= '<option value="200" selected="selected">200</option>' . "\n";} else { $records_limit .= '<option value="200">200</option>' . "\n";}

if ($max > 0) {
    $functions = '<span id="a_tag"><a href=# onclick="list()">檢視明細</a></span>';
} else {
    $functions = '－';
}

$i_begin += 1;
if ($max == 0) {
    $i_begin = 0;
    $i_end   = 0;
}

$conn->close();

# 頁面資料
$smarty->assign('i_begin', $i_begin);
$smarty->assign('i_end', $i_end);
$smarty->assign('current_page', $current_page);
$smarty->assign('total_page', $total_page);
$smarty->assign('record_limit', $records_limit);
$smarty->assign('max', number_format($max));

# 搜尋資訊
$smarty->assign('bank', $bank);
$smarty->assign('sApplyDate', $_POST['sApplyDate']);
$smarty->assign('eApplyDate', $_POST['eApplyDate']);
$smarty->assign('sEndDate', $_POST['sEndDate']);
$smarty->assign('eEndDate', $_POST['eEndDate']);
$smarty->assign('sSignDate', $_POST['sSignDate']);
$smarty->assign('sbankLoansDate', $_POST['sbankLoansDate']);
$smarty->assign('ebankLoansDate', $_POST['ebankLoansDate']);
$smarty->assign('eSignDate', $_POST['eSignDate']);
$smarty->assign('branch', $_POST['branch']);
$smarty->assign('scrivener', $_POST['scrivener']);
$smarty->assign('zip', $zip);
$smarty->assign('citys', $citys);
$smarty->assign('branchZip', $branchZip);
$smarty->assign('branchCitys', $branchCitys);
$smarty->assign('scrivenerZip', $scrivenerZip);
$smarty->assign('scrivenerCitys', $scrivenerCitys);
$smarty->assign('brand', $brand);
$smarty->assign('undertaker', $undertaker);
$smarty->assign('status', $status);
$smarty->assign('cCertifiedId', $cCertifiedId);
$smarty->assign('buyer', $byr);
$smarty->assign('owner', $owr);
$smarty->assign('scrivener_category', $scrivener_category);
$smarty->assign('sales', $sales);
$smarty->assign('scrivenerBrand', $scrivenerBrandTxt);
$smarty->assign('report', $report);
$smarty->assign('branchGroup', $_POST['branchGroup']);
$smarty->assign('sales_performance', $_POST['sales_performance']);

# 搜尋結果
$smarty->assign('tbl', $tbl);
$smarty->assign('totalMoney', $totalMoney);
$smarty->assign('certifiedMoney', $certifiedMoney);
$smarty->assign('cCertifiedMoney', $cCertifiedMoney); //只查詢店家跟仲介
$smarty->assign('cCaseFeedBackMoney', $cCaseFeedBackMoney);
$smarty->assign('transMoney', number_format($transMoney));
$smarty->assign('show_hide', $show_hide);
$smarty->assign('realestate', $realestate);

# 其他
$smarty->assign('functions', $functions);
$smarty->assign('t_day', $t_day);

$smarty->display('applycase_result.inc.tpl', '', 'report');
