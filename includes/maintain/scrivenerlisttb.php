<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

/* injection */
$_POST['sSearch'] = $_POST['sSearch'];
$_zip             = $_REQUEST['sZip'];
$_salesman        = $_REQUEST['salesman'];

$str = "1=1";
if ($_SESSION['member_scrivener'] == 1) { //是否可以看到停用店家
    $str = " a.sStatus = '1'";
}

if ($_salesman) {
    $_salesmanStr = ' AND c.sSales="' . $_salesman . '" ';
}

$_city = urldecode($_REQUEST['city']);

//特殊用
if ($_SESSION['member_pDep'] == 7 && ($_SESSION['member_test'] == 0)) {
    if ($sn == '') {
        if ($_salesman != '' && ($_salesman != $_SESSION['member_id'])) {
            if (in_array($_SESSION['member_id'], [38, 72]) && in_array($_salesman, [38, 72])) {
                $_salesmanStr = ' AND c.sSales="' . $_salesman . '" ';
            } else {
                $_salesmanStr = ' AND c.sSales="-" '; //不給查
            }
        } else {
            $_salesmanStr = ' AND c.sSales="' . $_SESSION['member_id'] . '" ';
        }
    }
}

if ($_SESSION['member_test'] != 0) {
    $str .= " AND ";

    $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '" . $_SESSION['member_test'] . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $test_tmp[] = "'" . $rs->fields['zZip'] . "'";
        $rs->MoveNext();
    }

    $str .= "a.sZip1 IN(" . implode(',', $test_tmp) . ")";
}

if (isset($_GET['feedDateCat']) && $_GET['feedDateCat'] != '') {
    $str .= " AND a.sFeedDateCat = '" . $_GET['feedDateCat'] . "'";
}

/* Database parameter */
$aColumns = array('sCode2',
    'sName',
    'sOffice',
    'sSerialnum',
    'pName',
    'sStatus2',
    'TelMain',
    'TelMain2',
    'FaxMain',
    'sMobileNum',
    'sIdentifyId',
    'sAccount4',
    'sAccount41',
    'sAccount42',
    'address',
);
$sIndexColumn = "sId";
$sTable       = "
(
    SELECT
        a.*,
        case a.sStatus when 1 then '啟用' when 3 then '重複建檔' when 4 then '未簽約' else '停用' END as sStatus2,
        (SELECT pName FROM tPeopleInfo as b Where b.pId = a.sUndertaker1) as pName,
        CONCAT('SC', LPAD(a.sId,4,'0')) as sCode2,
        CONCAT(a.sTelArea,a.sTelMain) AS TelMain,
        CONCAT(a.sTelArea2,a.sTelMain2) AS TelMain2,
        CONCAT(a.sFaxArea,a.sFaxMain) AS FaxMain,
        CONCAT(
            (SELECT zCity FROM tZipArea WHERE zZip = a.sCpZip1),
            (SELECT zArea FROM tZipArea WHERE zZip = a.sCpZip1),
            a.sCpAddress
        ) as address
    FROM
        tScrivener AS a
    LEFT JOIN
        tScrivenerSales AS c ON a.sId=c.sScrivener
    WHERE
         " . $str . $_salesmanStr . "
    GROUP BY a.sId
) tb
";
// exit($sTable);
/* MySQL connection  */
/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . $_POST['iDisplayStart'] . ", " . $_POST['iDisplayLength'];
}

/* Ordering */
$sOrder = "  ";
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

if ($_zip) {
    if ($sWhere == '') {
        $sWhere .= "WHERE ";
    } else {
        $sWhere .= " AND ";
    }

    $sWhere .= "sZip1='" . $_zip . "' ";
} elseif ($_city) {
    $sql = "SELECT * FROM tZipArea WHERE zCity = '" . $_city . "'";
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $tmp_zip[] = '"' . $rs->fields['zZip'] . '"';

        $rs->MoveNext();
    }

    if ($sWhere == '') {
        $sWhere .= "WHERE ";
    } else {
        $sWhere .= " AND ";
    }

    $sWhere .= "sZip1 IN (" . implode(',', $tmp_zip) . ") ";

}

$sOrder = " Order by sId desc ";

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS sId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
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

    $row = array();

    $row['DT_RowId']    = 'row_' . $aRow['sId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == 'sStatus2') {
            if ($aRow[$aColumns[$i]] != '啟用') {
                $row['DT_RowClass'] = 'close';
            }

        }
    }
    $output['aaData'][] = $row;

    $rResult->MoveNext();
}

echo json_encode($output);
