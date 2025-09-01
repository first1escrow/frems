<?php
include_once '../../configs/config.class.php';
include_once '../../session_check.php' ;
// include_once '../../opendb2.php' ;
include_once 'openadodb.php';


$_POST = escapeStr($_POST) ;
/* Database parameter */
$aColumns       = array(
                        'bId',
                        'bName', 
                        'bStore' 
                        );
$sIndexColumn   = "bId";
$sTable         = "tBranchGroup";

/* injection */
$_POST['sSearch'] = $_POST['sSearch'];

 /* MySQL connection  */


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

$sOrder = " Order by bId desc ";

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS bId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
$rResult = $conn->Execute($sQuery);
// $rResult = mysqli_query($link,$sQuery) or die(mysqli_error());

/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS() AS FOUND_ROWS
	";
// $rResultFilterTotal = mysqli_query($link,$sQuery) or die(mysqli_error());
// $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
// $iFilteredTotal = $aResultFilterTotal[0];
$rResultFilterTotal = $conn->Execute($sQuery);
$iFilteredTotal = $rResultFilterTotal->fields['FOUND_ROWS'];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" . $sIndexColumn . ") AS totalCount
		FROM   $sTable
	";
$rResultTotal = $conn->Execute($sQuery);
$iTotal = $rResultTotal->fields['totalCount'];
// $rResultTotal = mysqli_query($link,$sQuery) or die(mysqli_error());
// $aResultTotal = mysqli_fetch_array($rResultTotal);
// $iTotal = $aResultTotal[0];



/* Output */
$output = array(
    "sEcho" => intval($_POST['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

while (!$rResult->EOF) {
    $aRow = $rResult->fields;

    $row = array();

    $row['DT_RowId'] = 'row_' . $aRow['bId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;

    $rResult->MoveNext();
}


echo json_encode($output);
?>