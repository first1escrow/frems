<?php
include_once '../configs/config.class.php';
include_once '../session_check.php' ;
include_once '../openadodb.php';
/* Database connection information */

// $_POST['sSearch'] = '4258986';


$str = " AND b.bStatus = '1'";


/* Database parameter */
$aColumns       = array('aId',
						'aTitle',
                        'aCreator', 
                        'aCreatDate', 
                        'aEditor', 
                        'aModifyDate'
                        );
$sIndexColumn   = "aId";

$sTable         = " 
(
	SELECT
		aId,
		aTitle,
		(SELECT pName FROM tPeopleInfo WHERE pId = aCreator ) AS aCreator,
		(SELECT pName FROM tPeopleInfo WHERE pId = aEditor ) AS aEditor,
		aCreatDate,
		aModifyDate
	FROM 
	    `tAppNews`
	
	WHERE
		aDel = 0
) tb
";

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

/* Data set length after filtering */
$sQuery = "
        SELECT FOUND_ROWS() AS FOUND_ROWS
    ";
$rResultFilterTotal = $conn->Execute($sQuery);
$iFilteredTotal = $rResultFilterTotal->fields['FOUND_ROWS'];;

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
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] ==  'bStatus')
        {
            if($aRow[$aColumns[$i]]=='停用')
            {
                $row['DT_RowClass'] = 'close';
            }elseif ($aRow[$aColumns[$i]]=='暫停') {
                 $row['DT_RowClass'] = 'close';
            }
        }
        
    }


    $output['aaData'][] = $row;

    $rResult->MoveNext();
}

// echo "<pre>";

// print_r($output);

// echo "</pre>";

echo json_encode($output);
?>