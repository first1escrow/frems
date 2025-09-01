<?php
include_once '../configs/config.class.php';
include_once '../session_check.php' ;
include_once '../openadodb.php';
// include_once '../opendb.php';
/* Database connection information */
// $gaSql['user'] = $GLOBALS['DB_ESCROW_USER'];
// $gaSql['password'] = $GLOBALS['DB_ESCROW_PASSWORD'];
// $gaSql['db'] = $GLOBALS['DB_ESCROW_NAME'];
// $gaSql['server'] = $GLOBALS['DB_ESCROW_LOCATION'];
// $_POST['sSearch'] = '4258986';
/* injection */
$_POST['sSearch'] = $_POST['sSearch'];
$_REQUEST['identity'] = $_REQUEST['identity'];


if($_SESSION['member_pDep'] == 5 && $_SESSION['member_id'] != 1){ //
    $str = " s.sUndertaker1 = ".$_SESSION['member_id']."  AND la.lIdentity = 'S'";
    $tbl = "LEFT JOIN tScrivener AS s ON s.sId = SUBSTR(la.lTargetCode,3)";
}else if($_SESSION['member_pDep'] == 7){
    if ($_REQUEST['identity'] == 2) {
        $tbl = "LEFT JOIN tBranchSales AS bs ON bs.bBranch = SUBSTR(la.lTargetCode,3)";
        $str = " la.lIdentity = 'R'  AND bs.bSales ='".$_SESSION['member_id']."'";
    }elseif($_REQUEST['identity'] == 4){
        
        $str = " la.lIdentity = 'B' AND la.lServiceSales = '".$_SESSION['member_id']."'";
    }else{
        $tbl = "LEFT JOIN tScrivenerSales AS ss ON ss.sScrivener = SUBSTR(la.lTargetCode,3)";
        $str = " la.lIdentity = 'S' AND ss.sSales ='".$_SESSION['member_id']."'";
    }
}else{
    if ($_REQUEST['identity'] == 2) {
        $str = " la.lIdentity = 'R'";
    }elseif($_REQUEST['identity'] == 3){
        $str = " la.lIdentity = 'O'";
    }elseif($_REQUEST['identity'] == 4){
        $str = " la.lIdentity = 'B'";
    }else{
        $str = " la.lIdentity = 'S'";
    }
}



/* Database parameter */
$aColumns       = array('lId',
                        'lIdentity',
						'lNickName',
                        'lCaseMobile', 
                        'lTargetCode',
                        'lStage1Auth',
                        'lStage2Auth',
                        'lStatus',
                        'lModifyTime'
                        );
$sIndexColumn   = "lId";
//1=地政士、2=仲介
//已完成認證：1=完成、空白=未完成認證
$sTable         = " 
(
	SELECT
		la.lId,
        CASE la.lIdentity WHEN 'S' then '地政士' WHEN 'R' then '仲介店' WHEN 'B' then '經紀人' ELSE  '其他' END AS lIdentity,
        la.lNickName,
        la.lCaseMobile,
        la.lTargetCode,
        la.lStage1Auth,
        la.lStage2Auth,
        la.lStatus,
        la.lModifyTime
	FROM 
	    `tLineAccount` AS la
    ".$tbl."
    WHERE
	   

       ".$str."
	
) tb
";
// echo $sTable ; exit ;
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



$sOrder = " Order by lId desc ";

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS lId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";

$rResult = $conn->Execute($sQuery);//echo $sQuery ; exit ;
/* Data set length after filtering */
$sQuery = "
        SELECT FOUND_ROWS() AS FOUND_ROWS
    ";
$rResultFilterTotal = $conn->Execute($sQuery);
$iFilteredTotal = $rResultFilterTotal->fields['FOUND_ROWS'];

/* Total data set length */
$sQuery = "
        SELECT COUNT(" . $sIndexColumn . ") AS totalCount
        FROM   $sTable
    ";
$rResultTotal = $conn->Execute($sQuery);
$iTotal = $rResultTotal->fields['totalCount'];



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

    // Add the row ID and class to the object
    $row['DT_RowId'] = 'row_' . $aRow['lId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {
       $row[] = $aRow[$aColumns[$i]];
        
    }


    $output['aaData'][] = $row;

    $rResult->MoveNext();
}

// echo "<pre>";

// print_r($output);

// echo "</pre>";

echo json_encode($output);
?>