<?php
include_once dirname(dirname(__FILE__)).'/configs/config.class.php';
include_once dirname(dirname(__FILE__)).'/openadodb.php';

$_POST = escapeStr($_POST) ;
/* Database parameter */
$aColumns       = array('id',
                        'brand',
                        'name',
                        'office', 
                        'sn',
						'sales') ;
						
$sIndexColumn   = "id";

$sTable         = "
(
	SELECT 
		CONCAT((d.bCode),LPAD(a.bId,5,'0')) as id,
		d.bName as brand, 
		a.bName as name, 
		a.bStore as office,
		a.bSerialnum as sn,
		c.pName as sales
	FROM 
		tBranch AS a
	JOIN
		tBrand AS d ON a.bBrand=d.bId
	LEFT JOIN
		tBranchSales AS b ON a.bId=b.bBranch
	LEFT JOIN
		tPeopleInfo AS c ON b.bSales=c.pId
	WHERE
		a.bStatus = '1'
		AND b.bStage <> '2'

	UNION
	
	SELECT 
		CONCAT('SC',LPAD(e.sId,4,'0')) as id,
		CONCAT('') as brand, 
		e.sName as name, 
		e.sOffice as office,
		e.sSerialnum as sn,
		g.pName as sales
	FROM 
		tScrivener AS e
	LEFT JOIN
		tScrivenerSales AS f ON e.sId=f.sScrivener
	LEFT JOIN
		tPeopleInfo AS g ON f.sSales=g.pId
	WHERE
		e.sStatus = '1'
		AND f.sStage <> '2'
) tb  ";

$_POST['sSearch'] = $_POST['sSearch'];

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
        $sOrder = "   ";
    }
} 
else {
	$sOrder = " ORDER BY a.bId ASC ";
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
            $sWhere = "WHERE  ";
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
		SELECT SQL_CALC_FOUND_ROWS id, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
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
    $row['DT_RowId'] = $aRow['id'];
    //$row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        }else if ($aColumns[$i] != ' ') {
            /* General output */
			
			if ($aColumns[$i] == 'nStartDate') {
				if (preg_match("/^0000-00-00/",$aRow['nStartDate'])) $aRow['nStartDate'] = '-' ;
				else if ($aRow['nStartDate']) {
					$tmp = explode('-',substr($aRow['nStartDate'],0,10)) ;
					$aRow['nStartDate'] = ($tmp[0] - 1911).'-'.$tmp[1].'-'.$tmp[2] ;
					unset($tmp) ;
				}
			}
			
			if ($aColumns[$i] == 'nEndDate') {
				if (preg_match("/^0000-00-00/",$aRow['nEndDate'])) $aRow['nEndDate'] = '-' ;
				else {
					$tmp = explode('-',substr($aRow['nEndDate'],0,10)) ;
					$aRow['nEndDate'] = ($tmp[0] - 1911).'-'.$tmp[1].'-'.$tmp[2] ;
					unset($tmp) ;
				}
            }
			
			$row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;

    $rResult->MoveNext();
}


echo json_encode($output);
?>