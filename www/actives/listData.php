<?php
require_once dirname(dirname(__DIR__)).'/first1DB.php';

/* Database parameter */
$aColumns       = array(
                        'nSubject',
                        'rField',
                        'nField',
                        'nowpeople',
                        'nStartDate',
                        'nEndDate',
                        'rActivity'
                        );
                        
$sIndexColumn   = "rActivity";

$nowDate = date("Y-m-d").' 00:00:00' ;

$sTable         = "
( 
    SELECT 
        ro.rField,
       (SELECT nSubject FROM tNews AS a WHERE a.nId = ro.rActivity) AS nSubject,
       (SELECT nStartDate FROM tNews AS a WHERE a.nId = ro.rActivity) AS nStartDate,
       (SELECT nEndDate FROM tNews AS a WHERE a.nId = ro.rActivity) AS nEndDate,
       (SELECT nField FROM tNews AS a WHERE a.nId = ro.rActivity) AS nField,
       rActivity,
       SUM(rNo) AS nowpeople
    FROM 
        `tRegistOnline` AS ro
    GROUP BY ro.rActivity,ro.rField
    ORDER BY
        ro.rActivity
    DESC
) tb  ";

/* injection */
$_POST['sSearch'] = addslashes($_POST['sSearch']);

/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . addslashes($_POST['iDisplayStart']) . ", " .
            addslashes($_POST['iDisplayLength']);
}

/* Ordering */
$sOrder = "";
if (isset($_POST['iSortCol_0'])) {
   $sOrder = "ORDER BY  ";
    for ($i = 0; $i < intval($_POST['iSortingCols']); $i++) {
        if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_POST['iSortCol_' . $i])] . "
                    " . addslashes($_POST['sSortDir_' . $i]) . ", ";
        }
    }
    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "   ";
    }
} 
else {
    $sOrder = " ORDER BY rActivity DESC ";
}

/* Filtering */
$sWhere = "";
if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($aColumns); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . addslashes($_POST['sSearch']) . "%' OR ";
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
        $sWhere .= $aColumns[$i] . " LIKE '%" . addslashes($_POST['sSearch_' . $i]) . "%' ";
    }
}

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS rActivity, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
        FROM   $sTable
        $sWhere 
        $sOrder
        $sLimit
    ";
$conn = new first1DB;
$rResult = $conn->all($sQuery);

/* Data set length after filtering */
$iFilteredTotal = $conn->found_rows();

/* Total data set length */
$sQuery = "
        SELECT COUNT(" . $sIndexColumn . ") as total
        FROM   $sTable
    ";
$iTotal = $conn->one($sQuery)['total'];

/* Output */
$output = array(
    "sEcho" => intval($_POST['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();

    // Add the row ID and class to the object
    $row['DT_RowId'] = 'row_' . $aRow['rActivity'].'_'.$aRow['rField'];

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

            if ($aColumns[$i] == 'nField'){
                $tmp = explode(',', $aRow['nField']);
               
                foreach ($tmp as $k => $v) {
                    if(preg_match("/^".$aRow['rField']."/", $v)){
                        $tmp2 = explode(':', $v) ;
                        $aRow['nField'] = $tmp2[1];
                        unset($tmp2);
                    }
                }
                
                unset($tmp);
            }
            
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>