<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/scrivener.class.php';

$conn = new first1DB;

/* Database parameter */
$aColumns = array(
    'id',
    'certifiedId',
    'total',
    'identity',
    'bankMain',
    'bankBranch',
    'bankAccount',
    'bankAccountName',
    'fTargetId',
    'fNHIpay',
    'fTax',
    'fNHI',
    'salesName'
);

$sTable = '(
    SELECT
        a.fId AS id,
        a.fCertifiedId AS certifiedId,
        (SELECT pName FROM tPeopleInfo WHERE pId = a.fSales) AS salesName,
        a.fSalesConfirmDate AS confirmDate,
        a.fDetail AS total,
        a.fTargetId,
        (CASE WHEN b.fType = 1 THEN "----"
              WHEN b.fType = 2 THEN "身分證編號"
              WHEN b.fType = 3 THEN "統一編號"
              WHEN b.fType = 4 THEN "居留證編號" END) AS identity,
        (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fBankMain AND bBank4 = "") AS bankMain,
        (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fBankMain AND bBank4 = b.fBankBranch) AS bankBranch,
        b.fBankAccount AS bankAccount,
        b.fBankAccountName AS bankAccountName,
        a.fNHIpay AS fNHIpay,
        a.fTax,
        a.fNHI,
        c.bPayOk
    FROM
        tFeedBackMoneyPayByCase AS a
    LEFT JOIN
        tFeedBackMoneyPayByCaseAccount AS b ON a.fCertifiedId = b.fCertifiedId AND b.fTarget = "S" AND a.fId = b.fPayByCaseId
    LEFT JOIN
        tBankTransRelay AS c ON (a.fCertifiedId = c.bCertifiedId AND c.bKind = "地政士回饋金")
    WHERE
        (c.bPayOk = 2 OR c.bPayOk IS NULL) AND
        a.fSalesConfirmDate IS NOT NULL
        AND a.fAccountantConfirmDate IS NOT NULL
) AS tb';
$sales_filter = null;unset($sales_filter);

/* injection */
$_POST['sSearch'] = $_POST['sSearch'];

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . $_POST['iDisplayStart'] . ", " .
        $_POST['iDisplayLength'];
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

// $sOrder = " Order by bId desc ";

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS id, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
$rResult = $conn->all($sQuery);

/* Data set length after filtering */
$sQuery             = "SELECT FOUND_ROWS() AS FOUND_ROWS;";
$rResultFilterTotal = $conn->one($sQuery);
$iFilteredTotal     = $rResultFilterTotal['FOUND_ROWS'];

/* Total data set length */
$sQuery = "
		SELECT COUNT(*) AS totalCount
		FROM   $sTable
	";
$rResultTotal = $conn->one($sQuery);
$iTotal       = $rResultTotal['totalCount'];

/* Output */
$output = array(
    "sEcho"                => intval($_POST['sEcho']),
    "iTotalRecords"        => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData"               => array(),
);

if (empty($rResult)) {
    exit(json_encode($output));
}

$scrivener = new Scrivener();
foreach ($rResult as $aRow) {
    $row = [];

    $row['DT_RowId'] = 'row_' . $aRow['id'];

    $aRow['total'] = json_decode($aRow['total'])->total;

    if($aRow['total'] == 0) {
        $row[] = $aRow['certifiedId'];
    } else {
        $row[] = '<a href="Javascript:void(0)" onclick="showPDF(\'' . $aRow['certifiedId'] . '\', \'' . $aRow['fTargetId'] . '\')">' . $aRow['certifiedId'] . '</a>';
    }
    $row[] = $aRow['salesName'];

    $scrivenerInfo = $scrivener->GetScrivenerInfo($aRow['fTargetId']);

    $row[]              = $scrivenerInfo['sName'];
    $row[]              = $aRow['total'];
    $row[]              = $aRow['bankMain'] . ' ' . $aRow['bankBranch'] . '<br>' . $aRow['bankAccount'] . '<br>' . $aRow['bankAccountName'];
    $row[]              = $aRow['fNHIpay'];
    $row[]              = $aRow['total'] - $aRow['fTax'] - $aRow['fNHI'];
    $output['aaData'][] = $row;
}

exit(json_encode($output));
