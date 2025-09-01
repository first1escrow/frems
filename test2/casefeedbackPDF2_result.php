<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../includes/maintain/feedBackData.php';
// include_once 'feedBackData.php';
include_once '../session_check.php' ;
require_once dirname(dirname(__FILE__)).'/includes/IDCheck.php' ;
// print_r($pdo);

// phpinfo();
// die;


//預載log物件
$logs = new Intolog() ;
##
$_POST = escapeStr($_POST) ;
$bank = $_POST['bank'] ;//查詢銀行系統
$bStoreClass = $_POST['bStoreClass'] ;				//查詢店身份 (總店:1、單店:2)
$sales_year = $_POST['sales_year'] ;				//查詢回饋年度
$sales_season = $_POST['sales_season'] ;			//查詢回饋季
$sales_year_end = $_POST['sales_year_end'];
$sales_season_end = $_POST['sales_season_end'];
$certifiedid = $_POST['certifiedid'] ;				//查詢保證號碼
$bCategory = $_POST['bCategory'] ;					//查詢仲介商類型 (加盟:1、直營:2)
$branch = $_POST['branch'];
$scrivener = $_POST['scrivener'];
$storeSearch = $_POST['bck'];
$filetype =$_POST['filetype'];
$status = $_POST['status'];
$act = $_POST['act'];
$brand = $_POST['bd'];
$timeCategory = $_POST['timeCategory'];
##

##
if ($act == 'pdf') {
	include_once 'casefeedbackPDF2_resultPDF.php';
	// echo 'GO';
}

$qstr = ' sDelete = 0';

if($status != 'a'){
	if ($status == 1) {
		$qstr .= " AND sStatus >= '".$status."'";
	}else{
		$qstr .= " AND sStatus = '".$status."'";
	}
   
}else{
	if (!$scrivener && !$branch) {
		$qstr .= " AND sStatus = '0'"; //未發布
	}
	
}



//加盟1
//直營2
//地政士3
if ($bCategory) {
	if (!$scrivener && !$branch && !$brand) {
		$qstr .= " AND sCategory IN (".$bCategory.") ";
	}else  {
		$qstr2 = '';
		if ($scrivener) {
			$qstr2 .= " OR (sType = 1 AND sStoreId IN (".$scrivener.")) ";
		}

		if ($branch) {
			$qstr .= " OR (sType = 2 AND sStoreId IN (".$branch.")) ";
		}

		

		$qstr .= " AND (sCategory IN (".$bCategory.") ".$qstr2." ) ";

		unset($qstr2);
	}
}else{
	if ($scrivener) {
		$qstr .= " AND sType = 1 AND sStoreId IN (".$scrivener.") ";
	}

	if ($branch) {
		$qstr .= " AND sType = 2 AND sStoreId IN (".$branch.") ";
	}

	// if ($brand) {
	// 	$sql = "SELECT bCode FROM tBrand WHERE bId = '".$brand."'";
	// 	$rs = $conn->Execute($sql);
	// 	$qstr .= " AND sType = 2 AND sStoreCode = '".$rs->fields['bCode']."'";
	// }
}

// 年度季別
$time_search = '';
if ($sales_year && $sales_season ) {	
	if ($qstr) {
		$qstr .= ' AND ' ;
	}
	
	switch ($sales_season) {
		case 'S1' : 
				$date_start = $sales_year."-01-01";
				$sales_season1 = ($sales_year-1911).'年第01季' ;
				// $date_start = ($sales_year-1911)."年第01季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= ' sEndTime >= "'.$date_start.'"' ;
				break ;
		case 'S2' :
				$date_start = $sales_year."-04-01";
				$sales_season1 = ($sales_year-1911).'年第02季' ;
				// $date_start = ($sales_year-1911)."年第02季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= ' sEndTime >= "'.$date_start.'"' ;
				
				break ;
		case 'S3' :
				$date_start = $sales_year."-07-01";
				$sales_season1 = ($sales_year-1911).'年第03季' ;
				// $date_start = ($sales_year-1911)."年第03季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= ' sEndTime >= "'.$date_start.'"' ;
				break ;
		case 'S4' :
				$date_start = $sales_year."-10-01";
				$sales_season1 = ($sales_year-1911).'年第04季' ;
				// $date_start = ($sales_year-1911)."年第04季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= ' sEndTime >= "'.$date_start.'"' ;
			
				break ;
		default :
				$date_start = $sales_year."-".$sales_season."-01";
				$qstr .= ' sEndTime >= "'.$date_start.'"' ;
				$sales_season1 = ($sales_year-1911).'年'.str_pad($sales_season, 2,'0',STR_PAD_LEFT).'月' ;

				// if ($qstr) {
				// 	$qstr .= ' AND ' ;
				// }
				// $date_end = $sales_year."-".$sales_season."-".date('t',$sales_year."-".$sales_season);
				// $qstr .= 'sEndTime2 >= "'.$date_start.'" AND sEndTime2 <= "'.$date_end.'"' ;
				break ;
	}

	
}

// echo $sales_year_end."_".$sales_season_end;

if ($sales_year_end && $sales_season_end) {
	
	if ($qstr) { $qstr .= ' AND ' ;}

	switch ($sales_season_end) {
		case 'S1' : 
				// $date_start = $sales_year."-01-01";
				// $date_start = ($sales_year-1911)."年第01季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= 'sEndTime2 >= "'.$date_start.'" AND sEndTime2 <= "'.$sales_year_end.'-03-31"' ;
				
				break ;
		case 'S2' :
				// $date_start = $sales_year."-04-01";
				// $date_start = ($sales_year-1911)."年第02季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= 'sEndTime2 >= "'.$date_start.'" AND sEndTime2 <= "'.$sales_year_end.'-06-30"' ;
				
				
				break ;
		case 'S3' :
				// $date_start = $sales_year."-07-01";
				// $date_start = ($sales_year-1911)."年第03季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= 'sEndTime2 >= "'.$date_start.'" AND sEndTime2 <= "'.$sales_year_end.'-09-30"' ;
				
				break ;
		case 'S4' :
				// $date_start = $sales_year."-10-01";
				// $date_start = ($sales_year-1911)."年第04季";
				// $qstr .= ' sSeason = "'.$date_start.'"' ;
				$qstr .= 'sEndTime2 >= "'.$date_start.'" AND sEndTime2 <= "'.$sales_year_end.'-12-31"' ;
			
				break ;
		default :
				$date_end = $sales_year_end."-".$sales_season_end."-".date('t',$sales_year_end."-".$sales_season_end);
				// echo $date_end;
				$qstr .= 'sEndTime2 >= "'.$date_start.'" AND sEndTime2 <= "'.$date_end.'"' ;
				break ;
	}
}
//$sales_season1

if ($timeCategory == 1) { 
	
		if ($qstr) { $qstr .= ' AND ' ;}
		$qstr .= " sSeason = '".$sales_season1."'";
	
}


$sql = "SELECT * FROM
			tStoreFeedBackMoneyFrom as sf
		LEFT JOIN
			tStoreFeedBackMoneyFrom_Money AS sfm ON sfm.sFromId = sf.sId
		WHERE ".$qstr." ORDER BY sf.sType DESC,sf.sStoreId ASC";

if ($_SESSION['member_id'] == 6) {
	// echo $sql;
	// print_r($_POST);
}
// echo $sql;
$rs = $conn->Execute($sql);
$i = 0;
$list = array();
while (!$rs->EOF) {

	$list[$i] = $rs->fields;
	$list[$i]['code'] = ($rs->fields['sType'] == 1)? $rs->fields['sStoreCode'].str_pad($rs->fields['sStoreId'], 4,'0',STR_PAD_LEFT):$rs->fields['sStoreCode'].str_pad($rs->fields['sStoreId'], 5,'0',STR_PAD_LEFT);

	if ($list[$i]['sStatus'] == 1) {
		$list[$i]['status'] = '已發佈';
	}else if($list[$i]['sStatus'] == 2){
		$list[$i]['status'] = '店家已申請';
	}elseif($list[$i]['sStatus'] == 3){
		$list[$i]['status'] = '已完成';
	}else{
		$list[$i]['status'] = '未發佈';
	}

	//1公司2事務所3個人
	if ($list[$i]['sMethod'] == 1) {
		$list[$i]['method'] = '公司';
	}elseif ($list[$i]['sMethod'] == 2) {
		$list[$i]['method'] = '事務所';
	}elseif($list[$i]['sMethod'] == 3){
		$list[$i]['method'] = '個人';
	}

	//LOCK
	if ($list[$i]['sLock'] == 1) {
		$list[$i]['Lock'] = '關閉';
	}else{
		$list[$i]['Lock'] = '開啟';
	}
	//
	$list[$i]['sEndTime'] = str_replace('-', '/', (substr($list[$i]['sEndTime'], 0,4)-1911).substr($list[$i]['sEndTime'],4)) ;
	$list[$i]['sEndTime2'] = str_replace('-', '/', (substr($list[$i]['sEndTime2'], 0,4)-1911).substr($list[$i]['sEndTime2'],4)) ;
	$list[$i]['sCreatTime'] =  str_replace('-', '/', (substr($list[$i]['sCreatTime'], 0,4)-1911).substr($list[$i]['sCreatTime'],4)) ;

	//金額

	$i++;
	$rs->MoveNext();
}


# 搜尋資訊
$smarty->assign('bank',$bank) ;
$smarty->assign('bStoreClass',$bStoreClass) ;
$smarty->assign('sales_year',$sales_year) ;
$smarty->assign('sales_year_end',$sales_year_end) ;
$smarty->assign('sales_season',$sales_season) ;
$smarty->assign('sales_season_end',$sales_season_end) ;
$smarty->assign('certifiedid',$certifiedid) ;
$smarty->assign('bCategory',$bCategory) ;
$smarty->assign('branch',$branch) ;
$smarty->assign('scrivener',$scrivener) ;
$smarty->assign('storeSearch',$storeSearch) ;
$smarty->assign('filetype',$filetype);
$smarty->assign('status',$status);
$smarty->assign('act',$act);
$smarty->assign('brand',$brand);
$smarty->assign('timeCategory',$timeCategory);
# 搜尋資訊
$smarty->assign('list',$list);
$smarty->display('casefeedbackPDF2_result.inc.tpl', '', 'report');
?>