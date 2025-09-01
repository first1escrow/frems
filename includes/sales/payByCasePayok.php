<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/scrivener.class.php';

$conn = new first1DB;

/* Database parameter */
$aColumns = array(
    'certifiedId',
    'salesName',
    'ConfirmedSalesName',
    'confirmDate',
    'scrivener',
    'total',
    'accountantConfirmDate',
    'bankMain',
    'bankBranch',
    'bankAccount',
    'bankAccountName',
    'fTargetId',
    'fNHIpay',
    'fTax',
    'fNHI',
    'id',
);
$sales_filter = (($_SESSION['member_pDep'] == 7) && preg_match("/^\d+$/", $_SESSION['member_id'])) ? ' AND a.fSales = "' . $_SESSION['member_id'] . '" ' : '';
$sTable       = '(
    SELECT
        a.fId AS id,
        a.fCertifiedId AS certifiedId,
        (SELECT pName FROM tPeopleInfo WHERE pId = a.fSales) AS salesName,
        a.fSalesConfirmDate AS confirmDate,
        (SELECT pName FROM tPeopleInfo WHERE pId = a.fSalesConfirmId) AS ConfirmedSalesName,
        a.fAccountantConfirmDate AS accountantConfirmDate,
        a.fDetail AS scrivener,
        a.fDetail AS scrivenerId,
        a.fDetail AS total,
        a.fTargetId,
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
        tBankTransRelay AS c ON (a.fCertifiedId = c.bCertifiedId AND c.bKind = "地政士回饋金" AND c.bAccount = b.fBankAccount)
    WHERE
        c.bPayOk = 1
      AND
        a.fSalesConfirmDate IS NOT NULL ' . $sales_filter . '

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
    if (($aRow['total'] > 20000 and null == $aRow['accountantConfirmDate']) or $aRow['total'] == 0) {
        $row[] = $aRow['certifiedId'];
    } else {
        $row[] = '<a href="Javascript:void(0)" onclick="showPDF(\'' . $aRow['certifiedId'] . '\', \'' . $aRow['fTargetId'] . '\')">' . $aRow['certifiedId'] . '</a>';
    }

    $row[] = $aRow['salesName'];
    $row[] = (empty($aRow['ConfirmedSalesName'])) ? '' : '<span title="確認時間：'.$aRow['confirmDate'].'" style="cursor: pointer;">'.$aRow['ConfirmedSalesName'].'</span>';

    $scrivenerInfo = $scrivener->GetScrivenerInfo($aRow['fTargetId']);

    $row[]              = $scrivenerInfo['sName'];
    $row[]              = $aRow['total'];
    $row[]              = $aRow['bankMain'] . ' ' . $aRow['bankBranch'] . '<br>' . $aRow['bankAccount'] . '<br>' . $aRow['bankAccountName'];
    $row[]              = $aRow['fNHIpay'];
    $row[]              = $aRow['total'] - $aRow['fTax'] - $aRow['fNHI'];
    $output['aaData'][] = $row;
}

exit(json_encode($output));
