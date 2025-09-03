<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

//預載log物件
$logs = new Intolog();
$tlog = new TraceLog();
##
$_POST       = escapeStr($_POST);
$bank        = trim($_POST['bank']);
$sn          = trim($_POST['sn']);
$undertaker  = trim($_POST['undertaker']);
$buyer       = trim($_POST['buyer']);
$owner       = trim($_POST['owner']);
$scrivener   = trim($_POST['scrivener']);
$brand       = trim($_POST['brand']);
$branch      = trim($_POST['branch']);
$signdate    = trim($_POST['signdate']);
$sign2date   = trim($_POST['sign2date']);
$status      = trim($_POST['status']);
$enddate     = trim($_POST['enddate']);
$zip         = trim($_POST['zip']);
$undertaker  = trim($_POST['undertaker']);
$owner_agent = trim($_POST['owner_agent']);
$buyer_agent = trim($_POST['buyer_agent']);
// Check if 'addr' key exists in POST and is not null before using it
$addr = isset($_POST['addr']) ? trim($_POST['addr']) : '';
$uid  = trim($_POST['uid']);

$buyer1     = $buyer;
$owner1     = $owner;
$scrivener1 = $scrivener;
$branch1    = $branch;
$signdate1  = $signdate;
$signdate2  = $sign2date;
$enddate1   = $enddate;

$_POST['total_page']   = empty($_POST['total_page']) ? 0 : $_POST['total_page'];
$_POST['current_page'] = empty($_POST['current_page']) ? 0 : $_POST['current_page'];
$_POST['record_limit'] = empty($_POST['record_limit']) ? 0 : $_POST['record_limit'];

$total_page   = trim((int) $_POST['total_page']) + 1 - 1;
$current_page = trim((int) $_POST['current_page']) + 1 - 1;
$record_limit = trim((int) $_POST['record_limit']) + 1 - 1;

if (! $record_limit) {
    $record_limit = 10;
}

$query = '';

$functions  = '';
$conditions = '';

//特殊專案例外
if (! in_array($_SESSION['member_id'], [1, 3, 12, 13, 36, 84, 90, 6])) {
    if ($query) {$query .= " AND ";}
    $query .= ' cas.cCertifiedId != "130119712" ';
}

// 搜尋條件-銀行別
if ($bank) {
    if ($query) {$query .= " AND ";}
    $query .= ' cas.cBank="' . $bank . '" ';
    $conditions .= ' 銀行別="' . $bank . '" ';
}

// 搜尋條件-保證號碼
if ($sn) {
    if ($query) {$query .= " AND ";}
    $query .= ' cas.cCertifiedId="' . $sn . '" ';
    $conditions .= ' 保證號碼="' . $sn . '" ';
}

// 搜尋條件-承辦人
if ($undertaker) {
    if ($query) {$query .= " AND ";}

    $query .= ' scr.sUndertaker1="' . $undertaker . '" ';

    $conditions .= ' 經辦="' . $undertaker . '" ';

}

// 搜尋條件-買方

if ($buyer && ! $owner) {
    $tmp       = explode(')', $buyer);
    $buyer     = trim($tmp[0]);
    $buyerName = $tmp[0];
    unset($tmp);

    $tmp   = explode('(', $buyer);
    $buyer = trim($tmp[1]);
    unset($tmp);

    if ($buyer) {
        if ($query) {$query .= " AND ";}
        $query .= ' buy.cIdentifyId="' . $buyer . '" OR (other.cIdentifyId="' . $buyer . '" AND  other.cIdentity = 1)';
        $conditions .= ' 買方 ID="' . $buyer . '" ';
        $conditions .= ' 其他買方 ID="' . $buyer . '" ';
    } else {
        if ($query) {$query .= " AND ";}
        $query .= ' (buy.cName="' . $buyerName . '" OR (other.cName="' . $buyerName . '" AND  other.cIdentity = 1)) ';
        $conditions .= ' 買方 Name="' . $buyerName . '" 其他買方="' . $buyerName . '"';
    }
}

// 搜尋條件-買方代理人
if ($buyer_agent) {
    $tmp         = explode('*', $buyer_agent);
    $buyer_agent = trim($tmp[1]);
    unset($tmp);

    if ($query) {$query .= " AND ";}
    $query .= ' buy.cContactName LIKE "' . $buyer_agent . '" ';
    $conditions .= ' 買方代理人="' . $buyer_agent . '" ';
}

// 搜尋條件-賣方
if ($owner && ! $buyer) {
    $tmp       = explode(')', $owner);
    $owner     = trim($tmp[0]);
    $ownerName = $tmp[0];
    unset($tmp);

    $tmp   = explode('(', $owner);
    $owner = trim($tmp[1]);
    unset($tmp);

    if ($owner) {
        if ($query) {$query .= " AND ";}
        $query .= ' own.cIdentifyId="' . $owner . '" OR (other.cIdentifyId="' . $owner . '" AND other.cIdentity = 2) ';
        $conditions .= ' 賣方 ID="' . $owner . '" ';
        $conditions .= ' 其他賣方 ID="' . $owner . '" ';
    } else {
        if ($query) {$query .= " AND ";}
        $query .= ' (own.cName="' . $ownerName . '" OR (other.cName="' . $ownerName . '" AND other.cIdentity = 2)) ';
        $conditions .= ' 賣方 Name="' . $ownerName . '" 其他買方="' . $ownerName . '"';
    }
}

// 搜尋條件-賣方代理人
if ($owner_agent) {
    $tmp         = explode('*', $owner_agent);
    $owner_agent = trim($tmp[1]);
    unset($tmp);

    if ($query) {$query .= " AND ";}
    $query .= ' own.cContactName LIKE "' . $owner_agent . '" ';
    $conditions .= ' 賣方代理人="' . $owner_agent . '" ';
}

//條件搜尋買+賣

if ($buyer && $owner) {
    $tmp = explode(')', $buyer);

    $buyer = ($tmp[1]) ? $tmp[1] : $tmp[0];
    unset($tmp);

    $tmp   = explode(')', $owner);
    $owner = ($tmp[1]) ? $tmp[1] : $tmp[0];

    unset($tmp);

    if ($query) {$query .= " AND ";}
    $query .= ' (buy.cName="' . $buyer . '" OR (other.cName="' . $buyer . '" AND  other.cIdentity = 1)) ';
    $conditions .= ' 買方 Name="' . $buyer . '" 其他買方="' . $buyer . '"';

    if ($query) {$query .= " AND ";}
    $query .= ' (own.cName="' . $owner . '" OR (other.cName="' . $owner . '" AND  other.cIdentity = 2)) ';
    $conditions .= ' 賣方 Name="' . $owner . '" 其他賣方 ="' . $owner . '"';
}

// 搜尋條件-地政士
if ($scrivener) {
    $tmp          = explode(')', $scrivener);
    $scrivener_id = trim($tmp[0]);
    $scrivener    = $tmp[1];
    unset($tmp);

    $tmp          = explode('(', $scrivener_id);
    $scrivener_id = trim($tmp[1]);
    unset($tmp);

    $scr_id = substr($scrivener_id, 2);

    if ($query) {$query .= " AND ";}

    $scr_id += 1 - 1;
    $query .= ' csc.cScrivener="' . $scr_id . '" ';
    $conditions .= ' 地政士="' . $scr_id . '" ';
}

// 搜尋條件-仲介品牌
if ($brand) {
    if ($query) {$query .= " AND ";}
    $query .= ' rea.cBrand="' . $brand . '" ';
    $conditions .= ' 仲介品牌="' . $brand . '" ';

    $query2 = ' AND rea.cBrand="' . $brand . '" ';
}

// 搜尋條件-仲介店
if ($branch) {
    $tmp       = explode(')', $branch);
    $branch_id = trim($tmp[0]);
    $branch    = trim($tmp[1]);
    unset($tmp);

    $tmp       = explode('(', $branch_id);
    $branch_id = trim($tmp[1]);
    unset($tmp);

    if ($query) {$query .= " AND ";}

    $bcode     = substr($branch_id, 0, 2);
    $branch_id = substr($branch_id, 2, 5);

    $branch_id += 1 - 1;
    $query .= ' (rea.cBranchNum="' . $branch_id . '" OR rea.cBranchNum1="' . $branch_id . '" OR rea.cBranchNum2="' . $branch_id . '") ';
    $conditions .= ' 仲介店="' . $branch_id . '"';
}

// 搜尋條件-簽約日期
if ($signdate) {
    $tmp      = explode('-', $signdate);
    $signdate = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);

    if ($query) {$query .= " AND ";}
    $query .= ' cas.cSignDate>="' . $signdate . ' 00:00:00" ';
    $conditions .= ' 簽約日期(起):"' . $signdate . ' 00:00:00" ';
}
if ($sign2date) {
    $tmp       = explode('-', $sign2date);
    $sign2date = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);

    if ($query) {$query .= " AND ";}
    $query .= ' cas.cSignDate<="' . $sign2date . ' 23:59:59" ';
    $conditions .= ' 簽約日期(迄):"' . $sign2date . ' 23:59:59" ';
}

// 搜尋條件-案件狀態
if ($status) {
    if ($query) {$query .= " AND ";}
    $query .= ' cas.cCaseStatus="' . $status . '" ';
    $conditions .= ' 案件狀態="' . $status . '" ';
}

// 搜尋條件-狀態日期
if ($enddate) {
    $tmp     = explode('-', $enddate);
    $enddate = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);

    if ($query) {$query .= " AND ";}
    $query .= ' cas.cEndDate>="' . $enddate . ' 00:00:00" AND cas.cEndDate<="' . $enddate . ' 23:59:59" ';
    $conditions .= ' 狀態日期:"' . $enddate . ' 00:00:00" ~ "' . $enddate . ' 23:59:59" ';
}

// 搜尋條件-地區
if ($zip) {
    if ($query) {$query .= " AND ";}
    $query .= ' pro.cZip="' . $zip . '" ';
    $conditions .= ' 郵遞區號="' . $zip . '" ';
}

//搜尋條件地址
if ($addr) {
    if ($query) {$query .= " AND ";}
    $query .= ' pro.cAddr LIKE "%' . $addr . '%"';
}

if ($uid) {
    if ($query) {$query .= " AND ";}
    $query .= ' (buy.cIdentifyId = "' . $uid . '" OR own.cIdentifyId = "' . $uid . '" OR other.cIdentifyId = "' . $uid . '")';
}

//埋log紀錄
if ($conditions == '') {
    $conditions = '無預設條件!!';
}

$logs->writelog('buyerownerinquery', '查詢條件(' . $conditions . ')');
##

///////////////////////////////////////////////////////////////////
//特殊用
if ($_SESSION['member_test'] != 0) {
    if ($sn == '') {
        if ($query) {$query .= " AND ";}
        $sql = "SELECT
					b.bId
				FROM
					`tZipArea` AS za
				JOIN
					tBranch AS b ON b.bZip = za.zZip
				WHERE
					za.zTrainee = '" . $_SESSION['member_test'] . "'";
        $rs = $conn->Execute($sql);
        while (! $rs->EOF) {
            $test_tmp[] = "'" . $rs->fields['bId'] . "'";

            $rs->MoveNext();
        }

        $sql = "SELECT
					s.sId
				FROM
					`tZipArea` AS za
				JOIN
					tScrivener AS s ON s.sCpZip1 = za.zZip
				WHERE
					za.zTrainee = '" . $_SESSION['member_test'] . "'";
        $rs = $conn->Execute($sql);
        while (! $rs->EOF) {
            $test_tmp2[] = "'" . $rs->fields['sId'] . "'";

            $rs->MoveNext();
        }

        $query .= "(rea.cBranchNum IN(" . implode(',', $test_tmp) . ") OR rea.cBranchNum1 IN(" . implode(',', $test_tmp) . ") OR rea.cBranchNum2 IN(" . implode(',', $test_tmp) . ") OR csc.cScrivener IN (" . implode(',', $test_tmp2) . "))";
        unset($test_tmp);unset($test_tmp2);
    }

}
//
if ($query) {$query = ' WHERE ' . $query;}

##先計算數量## 在顯示陣列
$query = '
SELECT
    cas.cCertifiedId,
    rea.cBranchNum,
    rea.cBranchNum1,
    rea.cBranchNum2,
    rea.cBrand,
    rea.cBrand1,
    rea.cBrand2,
    csc.cScrivener AS sId,
    scr.sCategory as scrivenerCategory,
    (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) category,
    (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) category1,
    (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) category2
FROM
    tContractCase AS cas
JOIN
    tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
JOIN
    tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
JOIN
    tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
JOIN
    tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
JOIN
    tScrivener AS scr ON scr.sId=csc.cScrivener
LEFT JOIN
    tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
LEFT JOIN
    tPeopleInfo AS peo ON peo.pId=scr.sUndertaker1
LEFT JOIN
    tContractOthers  AS  other ON other.cCertifiedId=cas.cCertifiedId
' . $query . '
GROUP BY cas.cCertifiedId
;
';
$rs = $conn->Execute($query);

$max = 0;
##
$cCertifiedId = [];
while (! $rs->EOF) {
    if (checkSales($rs->fields, $_SESSION['member_id'])) { //業務只能看自己的案件
        $max++;
        $cCertifiedId[] = '"' . $rs->fields['cCertifiedId'] . '"';
    }

    $rs->MoveNext();
}
##

$tbl = '';

##
//一筆直接進案件內
if ($max == 1) {
    echo $max . "," . str_replace('"', '', $cCertifiedId[0]);
    die;
}

# 計算總頁數
if (($max % $record_limit) == 0) {
    $total_page = $max / $record_limit;
} else {
    $total_page = floor($max / $record_limit) + 1;
}
##

# 設定目前頁數顯示範圍
if ($current_page) {
    if ($current_page >= ($max / $record_limit)) {
        if ($max % $record_limit == 0) {
            $current_page = floor($max / $record_limit);
        } else {
            $current_page = floor($max / $record_limit) + 1;
        }
    }
    $i_end   = $current_page * $record_limit;
    $i_begin = $i_end - $record_limit;
    if ($i_end > $max) {
        $i_end = $max;
    }
    if ($i_end > $max) {$i_end = $max;}
} else {
    $i_end = $record_limit;
    if ($i_end > $max) {$i_end = $max;}
    $i_begin      = 0;
    $current_page = 1;
}

$j = 1;

if ($max > 0) {

    $query = '
        SELECT
            cas.cCertifiedId cCertifiedId,
            cas.cSignDate as cSignDate,
            (SELECT cBankName FROM tContractBank WHERE cBankCode=cas.cBank ) as cBank,
            (SELECT zCity FROM tZipArea WHERE zZip=pro.cZip) city,
            (SELECT zArea FROM tZipArea WHERE zZip=pro.cZip) area,
            (SELECT bName FROM tBrand WHERE bId=rea.cBrand) brand,
            (SELECT bName FROM tBrand WHERE bId=rea.cBrand1) brand1,
            (SELECT bName FROM tBrand WHERE bId=rea.cBrand2) brand2,
            rea.cBranchNum,
            rea.cBranchNum1,
            rea.cBranchNum2,
            (SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum) branch,
            (SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum1) branch1,
            (SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum2) branch2,
            scr.sName AS scrivener,
            buy.cName AS buyer,
            own.cName AS owner,
            peo.pName AS undertaker,
            (SELECT sName FROM tStatusCase WHERE sId=cas.cCaseStatus) status

        FROM
            tContractCase AS cas
        JOIN
            tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
        JOIN
            tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
        JOIN
            tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
        JOIN
            tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
        JOIN
            tScrivener AS scr ON scr.sId=csc.cScrivener
        LEFT JOIN
            tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId AND pro.cItem = 0
        LEFT JOIN
            tPeopleInfo AS peo ON peo.pId=scr.sUndertaker1

        WHERE
           cas.cCertifiedId IN(' . @implode(',', $cCertifiedId) . ')
           ORDER BY
        cas.cCaseStatus ASC,
        cas.cSignDate,cas.cApplyDate DESC LIMIT ' . $i_begin . ',' . $record_limit . '
    ';
    $rs = $conn->Execute($query);

    while (! $rs->EOF) {
        $arr[] = $rs->fields;
        $rs->MoveNext();
    }

    for ($i = 0; $i < count($arr); $i++) {

        if ($i % 2 == 0) {$color_index = "#FFFFFF";} else { $color_index = "#F8ECE9";}

        $sql = "SELECT
					(SELECT zCity FROM tZipArea WHERE zZip=cZip) city,
            		(SELECT zArea FROM tZipArea WHERE zZip=cZip) area
				FROM
					tContractProperty WHERE cCertifiedId = '" . $arr[$i]['cCertifiedId'] . "'";
        $rs = $conn->Execute($sql);

        // 檢查查詢是否成功且有返回結果
        if ($rs && ! $rs->EOF) {
            $arr[$i]['city'] = $rs->fields['city'];
            $arr[$i]['area'] = $rs->fields['area'];
        } else {
            $arr[$i]['city'] = '';
            $arr[$i]['area'] = '';
        }

        $sql = "SELECT * FROM tContractOthers WHERE cCertifiedId='" . $arr[$i]['cCertifiedId'] . "'";
        $rs  = $conn->Execute($sql);
        while (! $rs->EOF) {
            if ($rs->fields['cIdentity'] == 1) {
                $tmp_buy[] = $rs->fields['cName'];
            } elseif ($rs->fields['cIdentity'] == 2) {
                $tmp_owner[] = $rs->fields['cName'];
            }

            $rs->MoveNext();
        }

        $buyer_count = '';
        $owner_count = '';
        $b           = 0;
        $o           = 0;
        $buyer       = '';
        $owner       = '';
        if (! empty($tmp_buy) && is_array($tmp_buy)) {
            $b           = count($tmp_buy) + 1;
            $buyer_count = "等" . $b . "人";
            $buyer       = '其他買方:' . implode(',', $tmp_buy);

            unset($tmp_buy);
        }

        if (! empty($tmp_owner) && is_array($tmp_owner)) {
            $o           = count($tmp_owner) + 1;
            $owner_count = "等" . $o . "人";
            $owner       = '其他賣方:' . implode(',', $tmp_owner);

            unset($tmp_owner);
        }
        unset($tmp);

        if ($arr[$i]['cSignDate'] == '0000-00-00 00:00:00') {
            $arr[$i]['cSignDate'] = '';
        }

        if ($arr[$i]['cSignDate']) {
            $arr[$i]['cSignDate'] = substr($arr[$i]['cSignDate'], 0, 10);
            $tmp                  = explode('-', $arr[$i]['cSignDate']);
            if ($tmp[0]) {
                $arr[$i]['cSignDate'] = ($tmp[0] - 1911) . '-' . $tmp[1] . '-' . $tmp[2];
            }

            unset($tmp);
        }

        $st1 = $arr[$i]['brand'];
        $st2 = $arr[$i]['branch'];

        if ($arr[$i]['cBranchNum1'] > 0) {
            $st1 = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $arr[$i]['brand'];
            $st1 .= '<bR><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $arr[$i]['brand1'];

            $st2 = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $arr[$i]['branch'];
            $st2 .= '<bR><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $arr[$i]['branch1'];
        }

        if ($arr[$i]['cBranchNum2'] > 0) {
            $st1 .= '<bR><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $arr[$i]['brand2'];

            $st2 .= '<bR><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>' . $arr[$i]['branch2'];

        }

        $tbl .= '
		<tr style="text-align:center;background-color:' . $color_index . ';">
			<td style="font-size:10pt;">
				<a href="#" onclick=contract("' . $arr[$i]['cCertifiedId'] . '")>' . $arr[$i]['cCertifiedId'] . '</a>
			</td>
			<td style="font-size:10pt;">&nbsp;' . $arr[$i]['cBank'] . '&nbsp;</td>
			<td style="font-size:10pt;">&nbsp;' . $arr[$i]['cSignDate'] . '&nbsp;</td>
			<td style="font-size:10pt;">&nbsp;' . $arr[$i]['city'] . $arr[$i]['area'] . '&nbsp;</td>
			<td style="font-size:10pt;">&nbsp;' . $st1 . '&nbsp;</td>
			<td style="font-size:10pt;">&nbsp;' . $st2 . '&nbsp;</td>
			<td style="font-size:10pt;">&nbsp;' . $arr[$i]['scrivener'] . '&nbsp;</td>
			<td style="font-size:10pt;" title="' . $buyer . '">&nbsp;' . $arr[$i]['buyer'] . $buyer_count . '&nbsp;</td>
			<td style="font-size:10pt;" title="' . $owner . '">&nbsp;' . $arr[$i]['owner'] . $owner_count . '&nbsp;</td>
			<td style="font-size:10pt;">&nbsp;' . $arr[$i]['undertaker'] . '&nbsp;</td>
			<td style="font-size:10pt;">&nbsp;' . $arr[$i]['status'] . '&nbsp;</td>
		</tr>
		';
    }

    unset($arr);
} else {
    $color_index = "#FFFFFF";
    $tbl .= '
		<tr style="text-align:center;background-color:' . $color_index . '">
			<td colspan="10" style="height:20px;text-align:left;"><span style="font-size:9pt;color:red;">目前尚無任何資料！</span></td>
		</tr>
	';

}

$records_limit = empty($records_limit) ? '' : $records_limit;
if ($record_limit == 10) {$records_limit .= '<option value="10" selected="selected">10</option>' . "\n";} else { $records_limit .= '<option value="10">10</option>' . "\n";}
if ($record_limit == 50) {$records_limit .= '<option value="50" selected="selected">50</option>' . "\n";} else { $records_limit .= '<option value="50">50</option>' . "\n";}
if ($record_limit == 100) {$records_limit .= '<option value="100" selected="selected">100</option>' . "\n";} else { $records_limit .= '<option value="100">100</option>' . "\n";}
if ($record_limit == 150) {$records_limit .= '<option value="150" selected="selected">150</option>' . "\n";} else { $records_limit .= '<option value="150">150</option>' . "\n";}
if ($record_limit == 200) {$records_limit .= '<option value="200" selected="selected">200</option>' . "\n";} else { $records_limit .= '<option value="200">200</option>' . "\n";}

$functions = '';

if ($max == 0) {
    $i_begin = 0;
    $i_end   = 0;
} else {
    $i_begin += 1;
}
##
function checkSales($arr, $pId)
{
    global $conn;

    if ($_SESSION['member_pDep'] != 7) {
        return true;
    }

    if ($pId != '68') { //20240621 下班後耀哥來電要求讓廷蔚開放可以查詢直營案件
        $twhgCount = 0;     //業務不能看直營的案件
        $branch[]  = $arr['cBranchNum'];
        if ($arr['cBrand'] == 1 && $arr['category'] == 2) { //仲介台屋直營
            $twhgCount++;
        }

        if ($arr['cBranchNum1'] > 0) {
            $branch[] = $arr['cBranchNum1'];
            if ($arr['cBrand1'] == 1 && $arr['category1'] == 2) { //仲介台屋直營
                $twhgCount++;
            }
        }

        if ($arr['cBranchNum2'] > 0) {
            $branch[] = $arr['cBranchNum2'];
            if ($arr['cBrand2'] == 1 && $arr['category2'] == 2) { //仲介台屋直營
                $twhgCount++;
            }
        }

        if ($arr['cBranchNum3'] > 0) {
            $branch[] = $arr['cBranchNum3'];
            if ($arr['cBrand3'] == 1 && $arr['category3'] == 2) { //仲介台屋直營
                $twhgCount++;
            }
        }

        if ($twhgCount == count($branch)) { //直營不可以給業務看
            return false;
        }
    }

    //試用期業務
    if ($_SESSION['member_test'] != 0) {
        return true;
    }

    $pId = in_array($pId, [38, 72]) ? '38,72' : $pId;

    //20240621 下班後耀哥來電要求讓廷蔚開放可以查詢直營案件
    if ($pId == '68') {
        $branch[] = $arr['cBranchNum'];
        if ($arr['cBranchNum1'] > 0) {
            $branch[] = $arr['cBranchNum1'];
        }

        if ($arr['cBranchNum2'] > 0) {
            $branch[] = $arr['cBranchNum2'];
        }

        if ($arr['cBranchNum3'] > 0) {
            $branch[] = $arr['cBranchNum3'];
        }

    }

    $salesCount = 0;
    $sql        = "SELECT bSales FROM tBranchSales WHERE bBranch IN(" . @implode(',', $branch) . ") AND bSales IN (" . $pId . ")";
    $rs         = $conn->Execute($sql);
    $salesCount += $rs->RecordCount();

    $sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener =" . $arr['sId'] . " AND sSales IN (" . $pId . ")";
    $rs  = $conn->Execute($sql);
    $salesCount += $rs->RecordCount();

    return ($salesCount > 0) ? true : false;
}

# 頁面資料
$smarty->assign('i_begin', $i_begin);
$smarty->assign('i_end', $i_end);
$smarty->assign('current_page', $current_page);
$smarty->assign('total_page', $total_page);
$smarty->assign('record_limit', $records_limit);
$smarty->assign('max', $max);

# 搜尋資訊
$smarty->assign('bank', $bank);
$smarty->assign('sn', $sn);
$smarty->assign('undertaker', $undertaker);
$smarty->assign('buyer', $buyer1);
$smarty->assign('owner', $owner1);
$smarty->assign('scrivener', $scrivener1);
$smarty->assign('brand', $brand);
$smarty->assign('branch', $branch1);
$smarty->assign('signdate', $signdate1);
$smarty->assign('sign2date', $signdate2);
$smarty->assign('status', $status);
$smarty->assign('enddate', $enddate1);
$smarty->assign('zip', $zip);
$smarty->assign('uid', $uid);
//$smarty->assign('vr',$vr) ;
$smarty->assign('owner_agent', $owner_agent);
$smarty->assign('buyer_agent', $buyer_agent);

# 搜尋結果
$smarty->assign('tbl', $tbl);

# 其他
$smarty->assign('functions', $functions);

$smarty->display('buyerownerinquery_result.inc.tpl', '', 'report');
