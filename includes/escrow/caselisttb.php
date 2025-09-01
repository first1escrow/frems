<?php
// include_once '../../configs/config.class.php';
require_once dirname(dirname(__DIR__)).'/first1DB.php' ;
require_once dirname(dirname(__DIR__)).'/session_check.php' ;

$aColumns       = array('cBankName',
                        'cCertifiedId', 
                        'buyer', 
                        'owner', 
                        'cScrivener',
                        'pName',
                        'cApplyDate');
$sIndexColumn   = "cCertifiedId";


/* Get Time Range */
$time_limit = '' ;
$time_limit = addslashes($_POST['time_limit']);

/* injection */
$_POST['sSearch'] = addslashes($_POST['sSearch']);


/* Paging */
$sLimit = "";
if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . addslashes($_POST['iDisplayStart']) . ", " .
            addslashes($_POST['iDisplayLength']);
}

/* Ordering */
$sOrder = "  ";
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
        $sOrder = "";
    }
} 

// $sOrder = "Order by cId desc ";

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
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i] . " LIKE '%" . addslashes($_POST['sSearch_' . $i]) . "%' ";
    }
}



if ($time_limit) {
	if ($time_limit=='13') {
		// $time_limit = " AND a.cApplyDate>='".date("Y",mktime(0,0,0,0,0,(date("Y")-1)))."-01-01 00:00:00' AND a.cApplyDate<='".date("Y",mktime(0,0,0,0,0,(date("Y")-1)))."-12-31 23:59:59' " ;
		$time_limit = " AND a.cApplyDate>='".date("Y", strtotime("-1 year"))."-01-01 00:00:00' AND a.cApplyDate<='".date("Y", strtotime("-1 year"))."-12-31 23:59:59' " ;
	} else if ($time_limit=='14') {
		$time_limit = " AND a.cApplyDate>='".date("Y")."-01-01 00:00:00' AND a.cApplyDate<='".date("Y")."-12-31 23:59:59' " ;
	} else if ($time_limit=='15') {
		$time_limit = "" ;
	} else {
		$time_limit = " AND a.cApplyDate>='".date("Y")."-".$time_limit."-01 00:00:00' AND a.cApplyDate<='".date("Y")."-".$time_limit."-31 23:59:59' " ;
	}
} else {
	$time_limit = " AND a.cApplyDate>='".date("Y")."-01-01 00:00:00' AND a.cApplyDate<='".date("Y")."-12-31 23:59:59' " ;
}

$sTable = '
    (
        SELECT 
            a.`cCertifiedId`,
            a.`cCaseStatus`,
            a.`cApplyDate`,
            f.`pName`,
            g.`cBankName`,
            b.`cName` as `buyer`,
            c.`cName` as `owner`,
            e.`sName` as `cScrivener`
        FROM
            `tContractCase` AS a
        LEFT JOIN
            `tContractBuyer` AS b ON a.`cCertifiedId` = b.`cCertifiedId`
        LEFT JOIN
            `tContractOwner` AS c ON a.`cCertifiedId` = c.`cCertifiedId`
        LEFT JOIN
            `tContractScrivener` AS d ON a.`cCertifiedId` = d.`cCertifiedId`
        LEFT JOIN
            `tScrivener` AS e ON d.`cScrivener` = e.`sId`
        LEFT JOIN
            `tPeopleInfo` AS f ON a.`cUndertakerId` = f.`pId`
        LEFT JOIN
            `tCategoryBank` AS g ON a.`cBank` = g.`cId`
        WHERE
            a.`cCaseStatus` <> 8 
            AND a.`cCaseStatus` <> 3 
            '.$time_limit.'
            AND a.`cCaseMoney` > 0
    ) as tb
';

/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS cCertifiedId, " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";

$conn = new first1DB;
$rs = $conn->all($sQuery);
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

foreach ($rs as $aRow) {
    $row = array();

    // Add the row ID and class to the object
    $row['DT_RowId'] = 'row_' . $aRow['cCertifiedId'];
    $row['DT_RowClass'] = 'grade' . $aRow['grade'];

    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] == 'cApplyDate') {
            $a_day = explode('-', substr($aRow[$aColumns[$i]], 0, 10));
            $row[] = ($a_day[0] - 1911).'-'.$a_day[1].'-'.$a_day[2];
            $a_day = null; unset($a_day);
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;
}

echo json_encode($output);
?>