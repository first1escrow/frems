<?php
require_once dirname(dirname(__DIR__)) . '/configs/contract.setting.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$conn = new first1DB();

$sql = '';
if ($_SESSION['member_pDep'] == 5) {
    $sql = ' c.pId = ' . $_SESSION['member_id'] . ' ';
}

if (in_array($_SESSION['member_id'], [1, 6, 12, 48])) {
    $sql = '';
}

$sql = empty($sql) ? ' 1 = 1 ' : $sql;

/* Database parameter */
$aColumns = [
    'scrivenereId',
    'sName',
    'date',
    'brand',
    'application',
    'aQuantity',
    'pName',
    'processed',
    'aId',
];
$sIndexColumn = "aId";

$sTable = "";
foreach ($contractSetting['process'] as $key => $value) {
    $sTable .= "WHEN a.aProcessed = '{$key}' THEN '{$value}'\n";
}

$sTable = "
(
    SELECT
        a.aId,
        a.aScrivenerId,
        CONCAT('SC', LPAD(a.aScrivenerId, 4, '0')) AS scrivenereId,
        a.aFrom,
        a.aBrand as brand,
        a.aApplication,
        CASE
            WHEN a.aApplication = '1' THEN '土地'
            WHEN a.aApplication = '2' THEN '建物'
            WHEN a.aApplication = '3' THEN '預售屋'
            ELSE '未知'
        END AS application,
        a.aEscrowBank,
        a.aQuantity,
        a.aApplyDateTime,
        REPLACE(SUBSTRING(a.aApplyDateTime, 1, 10), '-', '/') AS date,
        a.aProcessed,
        CASE
            " . $sTable . "
            ELSE '-'
        END AS processed,
        b.sId,
        b.sName,
        b.sOffice,
        b.sUndertaker1,
        c.pId,
        c.pName
    FROM
        tApplyBankCode AS a
    JOIN
        tScrivener AS b ON a.aScrivenerId = b.sId
    LEFT JOIN
        tPeopleInfo AS c ON b.sUndertaker1 = c.pId
    WHERE
         " . $sql . "
    ORDER BY a.aProcessed ASC, a.aApplyDateTime DESC
) tb
";
// echo $sTable;
// exit;
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

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
$rResult = $conn->all($sQuery);

/* Data set length after filtering */
$iFilteredTotal = $conn->found_rows();

/* Total data set length */

$sQuery = "
        SELECT COUNT(" . $sIndexColumn . ") AS totalCount
        FROM   $sTable
    ";

$rResultTotal = $conn->one($sQuery);
$iTotal       = $rResultTotal['totalCount'];

/* Output */
$output = [
    "sEcho"                => intval($_POST['sEcho']),
    "iTotalRecords"        => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData"               => [],
];

if (! empty($rResult)) {
    foreach ($rResult as $aRow) {
        $row = [];

        $row['DT_RowId'] = 'row_' . $aRow['aId'];
        // $row['DT_RowClass'] = 'grade' . $aRow['grade'];

        for ($i = 0; $i < count($aColumns); $i++) {
            if ($aColumns[$i] == 'brand') {
                $row[] = (! empty($aRow[$aColumns[$i]]) && in_array($aRow[$aColumns[$i]], array_keys($contractSetting['brand']))) ? $contractSetting['brand'][$aRow[$aColumns[$i]]] : '未知';
            } else if ($aColumns[$i] != ' ') {
                /* General output */
                $row[] = $aRow[$aColumns[$i]];
            }
        }

        $output['aaData'][] = $row;
    }
}

echo json_encode($output);
