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


$str = "1=1";
/* Database parameter */
$aColumns       = array('cId',
                        'cName',
                        'cManagerArea',
                        'cManagerCity'
                        );
$sIndexColumn   = "cId";
//1=地政士、2=仲介
//已完成認證：1=完成、空白=未完成認證
$sTable         = " 
(
	SELECT
		cId,
        cName,
        cManagerArea,
        cManagerCity
	FROM 
	    `tCategoryTaxGoverment` 
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



$sOrder = " Order by cId desc ";

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS cId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
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
    $row['DT_RowId'] = 'row_' . $aRow['cId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {



       

        if($aColumns[$i] == 'cManagerArea'){
            

            $zip = explode(',', $aRow[$aColumns[$i]]);
            $zipArray = array();
            foreach ($zip as $value) {
               $sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip = '".$value."'";
               $rs = $conn->Execute($sql);
               array_push($zipArray, $rs->fields['zCity'].$rs->fields['zArea']);
            }
            $aRow['cManagerArea'] = $aRow['cManagerCity'];
            $aRow[$aColumns[$i]] .= implode(',', $zipArray);
            // if(!empty($aRow['cManagerCity'])){
            //     $aRow[$aColumns[$i]] .= implode(',', $zipArray);
            // }else{
            //     $aRow[$aColumns[$i]] = implode(',', $zipArray);
            // }

            unset($zipArray);unset($zip);
            
        }
         
           

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