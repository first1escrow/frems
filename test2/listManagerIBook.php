<?php
include_once '../../configs/config.class.php';
include_once '../../session_check.php' ;
include_once '../../opendb2.php' ;
/* Database connection information */

// $_POST['sSearch'] = '4258986';
/* injection */
 $_POST = escapeStr($_POST) ;
$_POST['sSearch'] = $_POST['sSearch'];
// $_zip = $_REQUEST['sZip'] ;
// $_brand = $_REQUEST['sBrand'] ;
// $_salesman = $_REQUEST['salesman'] ;
// $_manager = $_REQUEST['manager'];
if($_SESSION['pBankBook'] == 2){
    $str =" AND (bStatus = 1 OR bStatus = 2)";

    $str .= "AND IF(bCategory != '1',bBookId != '',bBookId != '' OR bBookId = '')";
   
}

// if ($_manager) {
//     $_manager = base64_decode($_manager);
//     $str .= ' AND bManager LIKE "%'.$_manager.'%"';
//     // echo $str;
// }



/* Database parameter */
$aColumns       = array('bDate',
						'cBankName',
                        'bBookId',
                        'CategoryName', 
                        'bMoney', 
                        'bStatusName',
                        'bCreatName',
                        'bModifyName2',
                        'bModifyName3',
                        'bBank2',
                        'bBank',
                        'bCategory',
                        'cBranchName',
                        'bStatus'
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


$sTable         = " 
(
	SELECT
        *,
        bBank AS bBank2,
		(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
		(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
		(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName,
        case bStatus when 0 then '待確認' when 1 then '待審核' when 2 then '已審核'  else '未知' END as bStatusName
        
FROM 
    `tBankTrankBook` 
WHERE bDel = 0	".$str."
) tb
";
// echo $sTable ; exit ;
 /* MySQL connection  */
// $gaSql['link'] = mysqli_pconnect($gaSql['server'], $gaSql['user'], $gaSql['password']) or
//         die('Could not open connection to server');

// mysqli_select_db($gaSql['db'], $gaSql['link']) or
//         die('Could not select database ' . $gaSql['db']);
		
// mysqli_query('SET NAMES utf8');
// mysqli_query('SET CHARACTER_SET_CLIENT=utf8');
// mysqli_query('SET CHARACTER_SET_RESULTS=utf8');

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
        $sWhere .= $aColumns[$i] . " LIKE '%" . $_POST['sSearch_' . $i]. "%' ";
    }
}

// if ($_brand) {
// 	if ($sWhere == '') $sWhere .= "WHERE " ;
// 	else $sWhere .= " AND " ;
// 	$sWhere .= "bStoreId=".$_brand.' ' ;
// }

// if ($_zip) {
// 	if ($sWhere == '') $sWhere .= "WHERE " ;
// 	else $sWhere .= " AND " ;
// 	$sWhere .= "bZip='".$_zip."' " ;
// }

$sOrder = " Order by bCreatTime desc,bStatus ASC,bDate ASC ";

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

$rResult = mysqli_query($link,$sQuery) or die(mysqli_error());
//echo $sQuery ; exit ;
/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
	";
$rResultFilterTotal = mysqli_query($link,$sQuery) or die(mysqli_error());
$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable
	";
$rResultTotal = mysqli_query($link,$sQuery) or die(mysqli_error());
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
    $row['pdf'] ='';

    for ($i = 0; $i < count($aColumns); $i++) {
        $bank = 0;
        if ($aColumns[$i] == "version") {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
        } else if ($aColumns[$i] != ' ') {
            /* General output */

            if($aColumns[$i] == 'cBankName'){
                
                if($aRow['bBank'] == 4 || $aRow['bBank'] ==6)
                {
                    // echo $aRow[$aColumns[$i]];
                    $aRow[$aColumns[$i]] .= $aRow['cBranchName'];
                    
                }

            }

            if($aColumns[$i] == 'bBank2'){
                //$aRow['bId']
               // $bank = $aRow[$aColumns[$i]];
               // bBank
                $link = '<a href="javascript:void(0)" onclick="bModify('.$aRow['bId'].')">編輯</a>';

               if($_SESSION['pBankBook'] == 2){
                    $link .= '&nbsp;&nbsp;|&nbsp;&nbsp; ';
                }
                    
                if($aRow[$aColumns[$i]] == 4 || $aRow[$aColumns[$i]] ==6){

                        
                    if($aRow['bCategory'] == 1)
                    {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'sinopac01_pdf.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 2) {
                        // $aRow['bBank2'] = $link.'&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)" onclick="pdfBook('.$aRow['bId'].',"sinopac02_pdf.php")">預覽</a>';
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'sinopac02_pdf.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 3 || $aRow['bCategory'] == 4 || $aRow['bCategory'] == 5) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'sinopac03_pdf.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 6) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'sinopac05_pdf.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 7|| $aRow['bCategory'] == 8 || $aRow['bCategory'] == 9) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'sinopac04_pdf.php')\">預覽</a>";

                    }else {
                        $aRow['bBank2'] = '未知';

                    }   
                }elseif ($aRow[$aColumns[$i]] == 1) {
                    if($aRow['bCategory'] == 1)
                    {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'firstInform2.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 6) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'firstInform3.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 3 || $aRow['bCategory'] == 4 || $aRow['bCategory'] == 5) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'firstInform1.php')\">預覽</a>";
                    }elseif ($aRow['bCategory'] == 7|| $aRow['bCategory'] == 8 ) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'firstInform4.php')\">預覽</a>";

                    }elseif($aRow['bCategory'] == 11){
                         $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'firstInform11.php')\">預覽</a>";
                    }elseif($aRow['bCategory'] == 12){
                         $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'firstInform12.php')\">預覽</a>";
                    }else {
                        $aRow['bBank2'] = '未知';

                    }  
                }elseif($aRow[$aColumns[$i]] == 5){ //不用預覽 因為是實體紙本
                         
                    if($aRow['bCategory'] == 1){
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'taishin01_pdf.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 3 || $aRow['bCategory'] == 4 || $aRow['bCategory'] == 5) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'taishin03_pdf.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 6) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'taishin06_pdf.php')\">預覽</a>";

                    }elseif ($aRow['bCategory'] == 7|| $aRow['bCategory'] == 8) {
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'taishin07_pdf.php')\">預覽</a>";

                    }elseif($aRow['bCategory'] == 10){
                        $aRow['bBank2'] = $link."&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"pdfBook(".$aRow['bId'].",'taishin10_pdf.php')\">預覽</a>";
                    }else {
                        $aRow['bBank2'] = '未知';

                    }   
                }else{
                    $aRow['bBank2'] = '未知';
                }

                if($aRow['bBank2'] != '未知'){
                    $aRow['bBank2'] .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)" onclick="bDel('.$aRow['bId'].')">刪除</a>';
            
                }else{
                    $aRow['bBank2'] = '<a href="javascript:void(0)" onclick="bDel('.$aRow['bId'].')">刪除</a>';
            
                }
            }


            

            $row[] = $aRow[$aColumns[$i]];
        }

        
        if($aColumns[$i] == 'bStatusName'){
            if($aRow[$aColumns[$i]]=='已審核'){
                $row['DT_RowClass'] = 'close';
            }
        }


       

        // if ($aColumns[$i] ==  'bStatus')
        // {
        //     if($aRow[$aColumns[$i]]=='停用')
        //     {
        //         $row['DT_RowClass'] = 'close';
        //     }elseif ($aRow[$aColumns[$i]]=='暫停') {
        //          $row['DT_RowClass'] = 'close';
        //     }
        // }
        
    }


    $output['aaData'][] = $row;
}

// echo "<pre>";
// print_r($output);
// echo "</pre>";
echo json_encode($output);
?>