<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/includes/maintain/feedBackData.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/includes/IDCheck.php';

//取得地政士所屬業務
function getScrivenerSales($store)
{
    global $conn;

    $sql = 'SELECT sSales FROM tScrivenerSalesForPerformance WHERE sScrivener = ' . $store . ';';
    $rs  = $conn->execute($sql);

    $sales = [];
    while (!$rs->EOF) {
        $sales[] = $rs->fields['sSales'];
        $rs->MoveNext();
    }

    return implode(',', $sales);
}
##
function getScrivener($store)
{
    global $conn;

    $sql = 'SELECT
                (SELECT CONCAT(zCity, zArea) FROM tZipArea WHERE zZip = s.sZip1  ) AS city,
                sAddress,
                (SELECT pName FROM tScrivenerSalesForPerformance AS P LEFT JOIN tPeopleInfo AS I ON P.sSales = I.pId WHERE P.sScrivener = s.sId) AS sales
            FROM
                tScrivener AS s
            WHERE
                sId = ' . $store . ';';
    $rs = $conn->execute($sql);

    return [
        'brand' => '',
        'addr'  => $rs->fields['city'] . $rs->fields['sAddress'],
        'sales' => $rs->fields['sales']
    ];
}

//取得仲介所屬業務
function getBranchSales($store)
{
    global $conn;

    $sql = 'SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = ' . $store . ';';
    $rs  = $conn->execute($sql);

    $sales = [];
    while (!$rs->EOF) {
        $sales[] = $rs->fields['bSales'];
        $rs->MoveNext();
    }

    return implode(',', $sales);
}
##

function getBranch($store)
{
    global $conn;

    $sql = 'SELECT
                (SELECT bName FROM tBrand WHERE bId = b.bBrand ) AS brand,
                (SELECT CONCAT(zCity, zArea) FROM tZipArea WHERE zZip = b.bZip  ) AS city,
                bAddress,
                (SELECT pName FROM tBranchSalesForPerformance AS P LEFT JOIN tPeopleInfo AS I ON P.bSales = I.pId  WHERE bBranch = b.bId) AS sales
            FROM
                tBranch AS b
            WHERE
                bId = ' . $store . ';
            ';
    $rs = $conn->execute($sql);

    $data = [
        'brand' => $rs->fields['brand'],
        'addr'  => $rs->fields['city'] . $rs->fields['bAddress'],
        'sales' => $rs->fields['sales']
    ];

    return $data;
}

// $sales = 57;

$qstr = ' sf.sDelete = 0';

if ($status != 'a') {
    if ($status == 1) {
        if ($caseStatus == 'Y') {
            $qstr .= " AND sf.sStatus = '3'";
        } else if ($caseStatus == 'N') {
            // $qstr .= " AND sf.sStatus < '3'";
            $qstr .= " AND sf.sStatus IN (1, 2)";
        } else {
            $qstr .= " AND sf.sStatus >= '" . $status . "'";
        }
    } else {
        $qstr .= " AND sf.sStatus = '" . $status . "'";
    }
} else {
    if (!$scrivener && !$branch) {
        $qstr .= " AND sf.sStatus = '0'"; //未發布
    }
}

//加盟=1、直營=2、地政士=3
if ($bCategory) {
    if (!$scrivener && !$branch && !$brand) {
        $qstr .= " AND sf.sCategory IN (" . $bCategory . ") ";
    } else {
        $qstr2 = '';

        if ($scrivener) {
            $qstr2 .= " OR (sf.sType = 1 AND sf.sStoreId IN (" . $scrivener . ")) ";
        }

        if ($branch) {
            $qstr .= " OR (sf.sType = 2 AND sf.sStoreId IN (" . $branch . ")) ";
        }

        $qstr .= " AND (sf.sCategory IN (" . $bCategory . ") " . $qstr2 . " ) ";

        $qstr2 = null;unset($qstr2);
    }
} else {
    $_qstr = '';
    if ($scrivener) {
        $_qstr[] = "(sf.sType = 1 AND sf.sStoreId IN (" . $scrivener . "))";
    }

    if ($branch) {
        $_qstr[] = "(sf.sType = 2 AND sf.sStoreId IN (" . $branch . "))";
    }

    if (!empty($_qstr)) {
        $qstr .= " AND (" . implode(' OR ', $_qstr) . ") ";
    }

    $_qstr = null;unset($_qstr);
}

// 年度季別
$time_search = '';
if ($sales_year && $sales_season) {
    if ($qstr) {
        $qstr .= ' AND ';
    }

    switch ($sales_season) {
        case 'S1':
            $date_start    = $sales_year . "-01-01";
            $sales_season1 = ($sales_year - 1911) . '年第01季';
            $qstr .= ' sf.sEndTime >= "' . $date_start . '"';
            break;
        case 'S2':
            $date_start    = $sales_year . "-04-01";
            $sales_season1 = ($sales_year - 1911) . '年第02季';
            $qstr .= ' sf.sEndTime >= "' . $date_start . '"';
            break;
        case 'S3':
            $date_start    = $sales_year . "-07-01";
            $sales_season1 = ($sales_year - 1911) . '年第03季';
            $qstr .= ' sf.sEndTime >= "' . $date_start . '"';
            break;
        case 'S4':
            $date_start    = $sales_year . "-10-01";
            $sales_season1 = ($sales_year - 1911) . '年第04季';
            $qstr .= ' sf.sEndTime >= "' . $date_start . '"';
            break;
        default:
            $date_start = $sales_year . "-" . $sales_season . "-01";
            $qstr .= ' sf.sEndTime >= "' . $date_start . '"';
            $sales_season1 = ($sales_year - 1911) . '年' . str_pad($sales_season, 2, '0', STR_PAD_LEFT) . '月';
            break;
    }
}

if ($sales_year_end && $sales_season_end) {
    $qstr .= empty($qstr) ? '' : ' AND ';

    switch ($sales_season_end) {
        case 'S1':
            // $qstr .= 'sf.sEndTime2 >= "' . $date_start . '" AND sf.sEndTime2 <= "' . $sales_year_end . '-03-31"';
            $qstr .= 'sf.sEndTime2 <= "' . $sales_year_end . '-03-31"';
            break;
        case 'S2':
            // $qstr .= 'sf.sEndTime2 >= "' . $date_start . '" AND sf.sEndTime2 <= "' . $sales_year_end . '-06-30"';
            $qstr .= 'sf.sEndTime2 <= "' . $sales_year_end . '-06-30"';
            break;
        case 'S3':
            // $qstr .= 'sf.sEndTime2 >= "' . $date_start . '" AND sf.sEndTime2 <= "' . $sales_year_end . '-09-30"';
            $qstr .= 'sf.sEndTime2 <= "' . $sales_year_end . '-09-30"';
            break;
        case 'S4':
            // $qstr .= 'sf.sEndTime2 >= "' . $date_start . '" AND sf.sEndTime2 <= "' . $sales_year_end . '-12-31"';
            $qstr .= 'sf.sEndTime2 <= "' . $sales_year_end . '-12-31"';
            break;
        default:
            $date_end = $sales_year_end . "-" . $sales_season_end . "-" . date('t', $sales_year_end . "-" . $sales_season_end);
            // $qstr .= 'sf.sEndTime2 >= "' . $date_start . '" AND sf.sEndTime2 <= "' . $date_end . '"';
            $qstr .= 'sf.sEndTime2 <= "' . $date_end . '"';
            break;
    }
}

if ($timeCategory == 1) {
    $qstr .= empty($qstr) ? '' : ' AND ';
    $qstr .= " sf.sSeason = '" . $sales_season1 . "'";
}

if (!empty($_exception)) {
    $qstr .= empty($qstr) ? '' : ' AND ';
    $qstr .= " ((sf.sStoreName NOT LIKE '%" . $_exception . "%') OR (sf.sStoreName = '大師直營/總管理處業務部') OR (sf.sStoreCode = 'MS' AND sf.sStoreId = 2628) OR (sf.sStoreCode = 'TH' AND sf.sStoreId = 7433))";
    $_exception = null;unset($_exception);
}

$sql = "SELECT
            sf.*
        FROM
			tStoreFeedBackMoneyFrom as sf
		LEFT JOIN
			tStoreFeedBackMoneyFrom_Money AS sfm ON sfm.sFromId = sf.sId
		WHERE
            " . $qstr . "
        ORDER BY
            sf.sType DESC, sf.sStoreId ASC";
$rs = $conn->Execute($sql);

//debug用
if (!empty($_debug)) {
    echo $sql . '<br>';
}
##

$i    = 0;
$list = array();
while (!$rs->EOF) {
    if (!empty($sales)) {
        $_sales = ($rs->fields['sType'] == 1) ? getScrivenerSales($rs->fields['sStoreId']) : getBranchSales($rs->fields['sStoreId']);
        // $_sales = ($rs->fields['sType'] == 1) ? $rs->fields['sSales'] : $rs->fields['bSales']; //1:地政士、2:仲介

        $_salesArray = explode(",", $_sales);
        if (!in_array($sales, $_salesArray)) {
            $_sales = null;unset($_sales);

            $rs->MoveNext();
            continue;
        }
    }

    $list[$i]         = $rs->fields;
    $list[$i]['code'] = ($rs->fields['sType'] == 1) ? $rs->fields['sStoreCode'] . str_pad($rs->fields['sStoreId'], 4, '0', STR_PAD_LEFT) : $rs->fields['sStoreCode'] . str_pad($rs->fields['sStoreId'], 5, '0', STR_PAD_LEFT);

    if ($rs->fields['sType'] == 1) {
        $scrivenerData     = getScrivener($rs->fields['sStoreId']);
        $list[$i]['brand'] = $scrivenerData['brand'];
        $list[$i]['addr']  = $scrivenerData['addr'];
        $list[$i]['sales']  = $scrivenerData['sales'];
        $scrivenerData     = null;unset($scrivenerData);
    } else {
        $branchData        = getBranch($rs->fields['sStoreId']);
        $list[$i]['brand'] = $branchData['brand'];
        $list[$i]['addr']  = $branchData['addr'];
        $list[$i]['sales']  = $branchData['sales'];
        $branchData        = null;unset($branchData);
    }

    if ($list[$i]['sStatus'] == 1) {
        $list[$i]['status'] = '待請款';
    } else if ($list[$i]['sStatus'] == 2) {
        $list[$i]['status'] = '待收取憑證';
        if ($list[$i]['sMethod'] == 3) {
            $list[$i]['status'] = '處理中';
        }
    } else if ($list[$i]['sStatus'] == 3) {
        $list[$i]['status'] = '已完成';
    } else {
        $list[$i]['status'] = '未發佈';
    }

    //1公司2事務所3個人
    if ($list[$i]['sMethod'] == 1) {
        $list[$i]['method'] = '公司';
    } else if ($list[$i]['sMethod'] == 2) {
        $list[$i]['method'] = '事務所';
    } else if ($list[$i]['sMethod'] == 3) {
        $list[$i]['method'] = '個人';
    }

    //LOCK
    if ($list[$i]['sLock'] == 1) {
        $list[$i]['Lock'] = '關閉';
    } else {
        $list[$i]['Lock'] = '開啟';
    }

    //
    $list[$i]['sEndTime']   = str_replace('-', '/', (substr($list[$i]['sEndTime'], 0, 4) - 1911) . substr($list[$i]['sEndTime'], 4));
    $list[$i]['sEndTime2']  = str_replace('-', '/', (substr($list[$i]['sEndTime2'], 0, 4) - 1911) . substr($list[$i]['sEndTime2'], 4));
    $list[$i]['sCreatTime'] = str_replace('-', '/', (substr($list[$i]['sCreatTime'], 0, 4) - 1911) . substr($list[$i]['sCreatTime'], 4));

    //金額

    $i++;

    $rs->MoveNext();
}
