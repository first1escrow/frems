<?php
include_once '../configs/config.class.php';
include_once '../session_check.php' ;
include_once '../openadodb.php';
/* Database connection information */
// $_POST['sSearch'] = '4258986';

if($_SESSION['member_id'] != 6 && $_SESSION['member_id'] != 1 && $_SESSION['member_id'] != 22){
    // $memberId = $_SESSION['member_id']; //
    $str = " s.sUndertaker1 = ".$_SESSION['member_id']."  AND a.aIdentity = 1";
}else{
    if ($_REQUEST['identity'] == 2) {
        $str = " a.aIdentity = 2";
    }else{
        // if($_SESSION['member_id'] != 6){
        //     $str = " s.sUndertaker1 = ".$_SESSION['member_id']."  AND a.aIdentity = 1";
        // }else{
            
        // }
        $str = " a.aIdentity = 1";
    }
    
}


/* Database parameter */
$aColumns       = array('aId',
                        'aIdentity',
						'aName',
                        'aParentId', 
                        'aModel', 
                        'aOK',
                        'aStatus',
                        'aLastModify'
                        );
$sIndexColumn   = "aId";
//1=地政士、2=仲介
//已完成認證：1=完成、空白=未完成認證
$sTable         = " 
(
	SELECT
		a.aId,
        CASE a.aIdentity WHEN 1 then '地政士' ELSE '仲介' END AS aIdentity,
        a.aName,
        a.aParentId, 
        a.aModel,
        CASE a.aOK WHEN 1 then '完成' ELSE '未完成認證' END AS aOK,
        CASE a.aStatus WHEN 1 then '有效' ELSE '凍結' END AS aStatus,
        a.aLastModify
	FROM 
	    `tAppAccount2020` AS a
    LEFT JOIN
        tScrivener AS s ON s.sId = SUBSTR(aParentId,3)
    WHERE
	   

       ".$str."
	
) tb
";
// echo $sTable ; exit ;
 /* MySQL connection  */


/* injection */
$_POST['sSearch'] = $_POST['sSearch'];
$_REQUEST['identity'] = $_REQUEST['identity'];

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " .$_POST['iDisplayStart'] . ", " .
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
        $sWhere .= $aColumns[$i] . " LIKE '%" .$_POST['sSearch_' . $i] . "%' ";
    }
}

if ($_brand) {
	if ($sWhere == '') $sWhere .= "WHERE " ;
	else $sWhere .= " AND " ;
	$sWhere .= "bStoreId=".$_brand.' ' ;
}

if ($_zip) {
	if ($sWhere == '') $sWhere .= "WHERE " ;
	else $sWhere .= " AND " ;
	$sWhere .= "bZip='".$_zip."' " ;
}

$sOrder = " Order by aId desc ";

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS aId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";

$rResult = $conn->Execute($sQuery);
//echo $sQuery ; exit ;
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
    $row['DT_RowId'] = 'row_' . $aRow['aId'];
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