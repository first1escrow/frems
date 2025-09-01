<?php

require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

// $year = ($_REQUEST['sYear']) ? ($_REQUEST['sYear'] + 1911) : date('Y');
$year = ($_REQUEST['sYear']) ? ($_REQUEST['sYear'] + 1911) : '';

if (empty($year)) {
    $str = " 1=1 ";
} else {
    if ($_SESSION['member_ScrivenerLevel'] == 2) { //政要想要看下一年申請中的
        $str = " ((sl.sYear = '" . $year . "' OR sl.sYear = '" . ($year + 1) . "') )";
    } else {
        $str = " (sl.sYear = '" . $year . "' )";
    }
}

##達標##
if ($_REQUEST['target'] == 1) {
    $str .= ' AND sl.sLevel = 0';
} elseif ($_REQUEST['target'] == 2) {
    $str .= ' AND sl.sLevel > 0';
}

##收據是否繳回##

if ($_REQUEST['receipt'] == '0') {
    $str .= ' AND sl.sReceipt = 0';
} elseif ($_REQUEST['receipt'] == '1') {
    $str .= ' AND sl.sReceipt = 1';
}

$order = '';

$nonsales_sql = '';

##各權限資料顯示資料顯示
if ($_SESSION['member_ScrivenerLevel'] == 1) {

    if ($_SESSION['member_test'] != 0) {
        $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '" . $_SESSION['member_test'] . "'";
        $rs  = $conn->Execute($sql);
        while (!$rs->EOF) {
            $tmpZip[] = "'" . $rs->fields['zZip'] . "'";

            $rs->MoveNext();
        }
        $str .= " AND sl.sStatus > 0 AND s.sCpZip1 IN (" . @implode(",", $tmpZip) . ")";
        unset($tmpZip);

    } elseif ($_SESSION['member_id'] == 90 || $_SESSION['member_id'] == 65 || $_SESSION['member_id'] == 103) {
        $sql = "SELECT zZip FROM tZipArea WHERE FIND_IN_SET (" . $_SESSION['member_id'] . ",zSales)";
        $rs  = $conn->Execute($sql);
        $zip = array();
        while (!$rs->EOF) {
            array_push($zip, $rs->fields['zZip']);

            $rs->MoveNext();
        }

        $str .= ' AND sl.sStatus > 0 AND (sl.sApplicant="' . $_SESSION['member_id'] . '" OR s.sCpZip1 IN ("' . @implode('","', $zip) . '"))';
        unset($zip);
    } else {
        // $str .= ' AND sl.sStatus > 0 AND sl.sApplicant="'.$_SESSION['member_id'].'"' ;
        $str .= ' AND sl.sStatus > 0 AND ss.sSales = "' . $_SESSION['member_id'] . '"';
        $nonsales_sql = '
            LEFT JOIN
                tScrivenerSales AS ss ON s.sId = ss.sScrivener
        ';
    }

} elseif ($_SESSION['member_ScrivenerLevel'] == 2) {
    $str .= ' AND sl.sStatus >= 1';

} elseif ($_SESSION['member_ScrivenerLevel'] == 3) { //20190905會計開放看申請中的
    $str .= ' AND sl.sStatus >= 1';
} else {
    $str .= ' AND sl.sStatus > 0';
}

##業務##
if ($_REQUEST['salesman']) {
    // if($str){ $str .=''}
    $str .= ' AND sl.sApplicant ="' . $_REQUEST['salesman'] . '"';
}
##
//狀態
if ($_REQUEST['status']) {
    $str .= ' AND sl.sStatus ="' . $_REQUEST['status'] . '"';
}
##
// $sales = ;

/* Database connection information */
// $gaSql['user'] = $GLOBALS['DB_ESCROW_USER'];
// $gaSql['password'] = $GLOBALS['DB_ESCROW_PASSWORD'];
// $gaSql['db'] = $GLOBALS['DB_ESCROW_NAME'];
// $gaSql['server'] = $GLOBALS['DB_ESCROW_LOCATION'];

/* Database parameter */
if ($_SESSION['member_pDep'] == 4 || $_SESSION['member_pDep'] == 1) {
    $aColumns = array(
        'sId',
        'sCode2',
        'sName',
        'sBirthday',
        'sGift',
        'sMoney',
        'sApplicant',
        'Inspetor',
        'sStatus',
        'Receipt',
        'sTime',

    );
} else {
    $aColumns = array(
        'sCode2',
        'sName',
        'sBirthday',
        'sGift',
        'sMoney',
        'sApplicant',
        'Inspetor',
        'sStatus',
        'Receipt',
        'sTime',
        'sId',
    );
}

$sIndexColumn = "sId";
$sTable       = "
(
    SELECT
        sl.sId AS sId,
        SUBSTR(sl.sTime,1,10) AS sTime,
        sl.sTime AS CreatTime,
        CONCAT('SC', LPAD(s.sId,4,'0')) as sCode2,
        sl.sScrivener,
        s.sName,
        s.sOffice,
        sl.sMoney,
        (SELECT gName FROM tGift WHERE gId = sl.sGift) as sGift,
        (SELECT pName FROM tPeopleInfo WHERE pId = sl.sApplicant) AS sApplicant,
        (SELECT pName FROM tPeopleInfo WHERE pId = sl.sInspetor) AS Inspetor,
        SUBSTR(s.sBirthday,6) AS sBirthday,
        case sl.sReceipt when 1 then '已繳回' else '未繳回' END as Receipt,
        case sl.sStatus when 1 then '申請中' when 2 then '主管審核通過' when 3 then '主管審核不通過' when 4 then '已處理' when 5 then '取消申請' else '不核准' END as sStatus
    FROM
        tScrivenerLevel AS sl
    LEFT JOIN
        tScrivener AS s ON sl.sScrivener = s.sId
    " . $nonsales_sql . "
    WHERE

    " . $str . "

) tb
";

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . $_POST['iDisplayStart'] . ", " .
        $_POST['iDisplayLength'];
}

/* Ordering */
$sOrder = "";
if (isset($_POST['iSortCol_0'])) {
    $sOrder = "ORDER BY  ";
    for ($i = 0; $i < intval($_POST['iSortingCols']); $i++) {
        if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_POST['iSortCol_' . $i])] . "
                    " . $_POST['sSortDir_' . $i] . ", ";
        }
    }
    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
    }
}

/* Filtering */
$sWhere = "";
if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($aColumns); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . $_POST['sSearch'] . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if (isset($_POST['bSearchable_' . $i]) && $_POST['bSearchable_' . $i] == "true" && $_POST['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i] . " LIKE '%" . $_POST['sSearch_' . $i] . "%' ";
    }
}

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS sScrivener, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
$rResult = $conn->Execute($sQuery);

/* Data set length after filtering */
$sQuery = "
        SELECT FOUND_ROWS() AS FOUND_ROWS
    ";

$rResultFilterTotal = $conn->Execute($sQuery);
$iFilteredTotal     = $rResultFilterTotal->fields['FOUND_ROWS'];

/* Total data set length */
$sQuery = "
        SELECT COUNT(" . $sIndexColumn . ") AS totalCount
        FROM   $sTable
    ";

$rResultTotal = $conn->Execute($sQuery);
$iTotal       = $rResultTotal->fields['totalCount'];

/* Output */
$output = array(
    "sEcho"                => intval($_POST['sEcho']),
    "iTotalRecords"        => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData"               => array(),
);

while (!$rResult->EOF) {
    $aRow = $rResult->fields;
    $row  = array();

    // Add the row ID and class to the object
    $row['DT_RowId']    = 'row_' . $aRow['sId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } elseif ($aColumns[$i] == 'sId' && ($_SESSION['member_pDep'] == 4 || $_SESSION['member_pDep'] == 1)) {

            $row[] = ($aRow['sStatus'] != '已處理' && $aRow['sStatus'] != '取消申請') ? '<input type="checkbox" name="checkId[]" value="' . $aRow['sId'] . '">' : '';

        } else if ($aColumns[$i] != ' ') {
            $row[] = $aRow[$aColumns[$i]];
        }

    }

    $output['aaData'][] = $row;

    $rResult->MoveNext();
}

echo json_encode($output);
