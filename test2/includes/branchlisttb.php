<?php
#顯示錯誤
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../configs/config.class.php';
include_once '../../session_check.php' ;
include_once '../../opendb2.php' ;
include_once '../../openadodb.php' ;

$_POST = escapeStr($_POST) ;
/* injection */
$_POST['sSearch'] = $_POST['sSearch'];
$_zip = $_REQUEST['sZip'] ;
$_brand = $_REQUEST['sBrand'] ;
$_salesman = $_REQUEST['salesman'] ;
$_manager = $_REQUEST['manager'];
$_city = urldecode($_REQUEST['city']);
// echo $_city;

if($_SESSION['member_branch'] == 1) //是否可以看到停用店家
{
    $str = " AND b.bStatus = '1'";
}

if ($_salesman) {
	$_salesmanStr = ' AND a.bSales="'.$_salesman.'" ' ;
}

// if ($_manager) {
//     $_manager = base64_decode($_manager);
//     $str .= ' AND bManager LIKE "%'.$_manager.'%"';
//     // echo $str;
// }
//特殊用
if ($_SESSION['member_pDep'] == 7  && ($_SESSION['member_test'] != 1 && $_SESSION['member_test'] != 2 && $_SESSION['member_test'] != 3)) { 

     if ($_salesman != '' &&  ($_salesman != $_SESSION['member_id'])) {
            
            $_salesmanStr = ' AND a.bSales="-" ' ; //不給查
        }else{
            $_salesmanStr = ' AND a.bSales="'.$_SESSION['member_id'].'" ' ;
        }
    // if ($_SESSION['member_id'] == 25) {
    //     $sp = " OR zScrivenerSales ='".$_SESSION['member_id']."'";
    // }
    // if ($sn == '') {
    //     if ($str) { $str .= " AND " ; }

    //     $sql = "SELECT zZip FROM `tZipArea` WHERE zSales = '".$_SESSION['member_id']."'".$sp;

    //     $rs = $conn->Execute($sql);

    //     while (!$rs->EOF) {
    //         $test_tmp[] = "'".$rs->fields['zZip']."'";

    //         $rs->MoveNext();
    //     }
    //     $str .= "b.bZip IN(".implode(',', $test_tmp).")";
    // }


    
}

if($_SESSION['member_test'] == 1){
    $str .= " AND " ;

        $sql = "SELECT zZip FROM `tZipArea` WHERE `zCity` LIKE  '%苗栗%'  OR `zCity` LIKE  '%新竹%'";
        $rs = $conn->Execute($sql);

        while (!$rs->EOF) {
            $test_tmp[] = "'".$rs->fields['zZip']."'";

            $rs->MoveNext();

        }
        $str .= "b.bZip IN(".implode(',', $test_tmp).")";
}elseif($_SESSION['member_test'] == 2){
    if ($str) { $str .= " AND " ; }

        $sql = "SELECT zZip FROM `tZipArea` WHERE `zCity` LIKE  '%台中%' OR `zCity` LIKE  '%彰化%' OR `zCity` LIKE  '%南投%'";
        $rs = $conn->Execute($sql);

        while (!$rs->EOF) {
            $test_tmp[] = "'".$rs->fields['zZip']."'";

            $rs->MoveNext();

        }
        $str .= "b.bZip IN(".implode(',', $test_tmp).")";
}elseif($_SESSION['member_test'] == 3){
    if ($str) { $str .= " AND " ; }

        $sql = "SELECT zZip FROM `tZipArea` WHERE `zCity` LIKE  '%新北%' OR `zCity` LIKE  '%台北%' OR `zCity` LIKE  '%桃園%'";
        $rs = $conn->Execute($sql);

        while (!$rs->EOF) {
            $test_tmp[] = "'".$rs->fields['zZip']."'";

            $rs->MoveNext();

        }
        $str .= "b.bZip IN(".implode(',', $test_tmp).")";
}


/* Database parameter */
$aColumns       = array('bCode',
						'bBrand',
                        'bStore', 
                        'bName', 
                        'bCooperationHas', 
                        'bServiceOrderHas', 
                        'bStatus', 
                        'tCategory',
                        'bTelMain',
                        'bFaxMain',
                        'bManager'
                        );
$sIndexColumn   = "bId";
/*
$sTable         = " 
    (SELECT bId, CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ), LPAD(bId,5,'0') ) bCode, bStore,
    (Select bName From `tBrand` a Where a.bId = b.bBrand ) bBrand, `bName`, `bIdentityNumber`, case `bCashierOrderHas` when 1 then '是' Else '否' end bCashierOrderHas,
    case `bStatus` when 1 then '啟用' else '停用' end bStatus,
    case `bCategory` when 1 then '加盟' else '特許加盟店' End tCategory
FROM 
    `tBranch` b  ) tb
";
*/

//2013-07-04 要求剔除停用狀態店家
$sTable         = " 
(
	SELECT
		b.bId,
		CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode,
		b.bStore,
		b.bBrand as bStoreId,
		b.bZip,
		(Select bName From `tBrand` a Where a.bId = b.bBrand ) as bBrand,
		b.bName,
		b.bSerialnum as bIdentityNumber,
		case `bServiceOrderHas` when 1 then '有' Else '無' END as bServiceOrderHas,
		case `bStatus` when 1 then '啟用' when 3 then '暫停' else '停用' END as bStatus,
		case `bCategory` when 1 then '加盟' else '特許加盟店' END as tCategory,
        CONCAT(bFaxArea,b.bFaxMain) AS bFaxMain,
        CONCAT(bTelArea,b.bTelMain) AS bTelMain,
        b.bManager,
        case `bCooperationHas` when 1 then '有' Else '無' END as bCooperationHas      
    FROM 
        `tBranch` AS b  
    LEFT JOIN
    	tBranchSales AS a ON b.bId=a.bBranch
    WHERE
    	b.bId != 0 ".$str.$_salesmanStr." GROUP BY b.bId
) tb
";
// echo $sTable ; exit ;

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
}elseif ($_city) {
    $sql = "SELECT * FROM tZipArea WHERE zCity = '".$_city."'";
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
       $tmp_zip[] = '"'.$rs->fields['zZip'].'"';

        $rs->MoveNext();
    }

     if ($sWhere == '') $sWhere .= "WHERE " ;
        else $sWhere .= " AND " ;
        $sWhere .= "bZip IN (".@implode(',', $tmp_zip).") " ;

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

$rResult = mysqli_query($link, $sQuery) or die(mysqli_error());
//echo $sQuery ; exit ;
/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
	";
$rResultFilterTotal = mysqli_query($link, $sQuery) or die(mysqli_error());
$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable
	";
$rResultTotal = mysqli_query($link, $sQuery) or die(mysqli_error());
$aResultTotal = mysqli_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];



/* Output */
$output = array(
    "sEcho" => intval($_POST['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

while ($aRow = mysqli_fetch_array($rResult)) {
    $row = array();

    // Add the row ID and class to the object
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
}

// echo "<pre>";

// print_r($output);

// echo "</pre>";

echo json_encode($output);
?>