<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php';
include_once '../session_check.php' ;
include_once '../report/getBranchType.php';
include_once '../includes/sales/getSalesInfo.php'; //function 都在這
include_once '../includes/maintain/feedBackData.php';
// include_once 'getSalesInfo.php'; //function 都在這
##
$_POST = escapeStr($_POST) ;
foreach ($menu_sales as $k => $v) {
	if ($k == 3) unset($menu_sales[$k]) ;
}

// $sales = $_SESSION['member_id'];
// $sales = 25;

if (empty($_POST['sales'])) {
	if ($_SESSION['member_id'] && $_SESSION['member_pDep'] == 7) {
		$sales = $_SESSION['member_id'];
	}
}else{
	$sales = $_POST['sales'];
}

//時間下拉
$yr = trim(addslashes($_POST['dateYear'])) ;
$mn = trim(addslashes($_POST['dateMonth'])) ;
$ok = trim(addslashes($_POST['ex'])) ;
$trace = trim(addslashes($_POST['traceXls'])) ;

if ($trace == 'trace') {
	
	require_once 'traceXls.php' ;
}

if (!$yr) $yr = date("Y") - 1911 ;
// if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")-1))) ;
if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")))) ;

$grade = 0;
##

//年度顯示
$y = '' ;
for ($i = 0 ; $i < 100 ; $i ++) {
	$patt = $i + 100 ;
	
	// if (($patt == $yr) && ($mn != '12')) { $sl = " selected='selected'" ; echo 'a'; }
	// else if ((($patt+1)==$yr)&&($mn=='12')) { $sl = " selected='selected'" ; echo 'b';}
	if (($patt == $yr) ) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$y .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

//月份顯示
$m = '' ;
for ($i = 0 ; $i < 12 ; $i ++) {
	$patt = $i + 1 ;
	
	if ($patt==$mn) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$m .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

##

$sql = "SELECT * FROM tSalesReportPercent WHERE pCreatTime < '".($yr+1911)."-".$mn."-31"."' ORDER BY pCreatTime DESC";
$rs = $conn->Execute($sql);
$percent = $rs->fields;


##
if ($_SESSION['member_pDep'] != 7) {
	if ($yr == 107) {
		$showTTT = 107;

	}
	include_once '../includes/sales/salesReportFor'.$yr.'.php';

	if ($showTTT) {
		$yr = $showTTT;

	}

}else{
	if ($yr <= 105) {
		// echo 'GO105';
		include_once '../includes/sales/salesReportFor105.php';
	}else{
		// echo 'GO106';
		include_once '../includes/sales/salesReportFor106.php';
	}
}

// echo $showTTT;
// include_once '../includes/sales/salesReportFor107.php';



// echo sprintf("%d",date('Y')).":".$yr;
if (sprintf("%d",date('Y')) > $yr ) {
	$now_check = '1';
}
$now_month = sprintf("%d",date('m')) ;
###########
if ($ok=='ok') {

	$sql= "SELECT pName FROM tPeopleInfo WHERE pId = '".$sales."'";
	$rs = $conn->Execute($sql);
	$sales_name = $rs->fields['pName'];
	// echo $sales_name;
	include_once 'salesReportExcel.php';

}

unset($tmp_use);


##

$smarty->assign('now_check',$now_check);
$smarty->assign('sess',$sess);
$smarty->assign('script',$script);
$smarty->assign('menu_sales',$menu_sales);
$smarty->assign('sales',$sales);
$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign("now_month",$now_month);
$smarty->assign('season1',$season1);
$smarty->assign('season2',$season2);
$smarty->assign('now_year',($yr));
$smarty->assign('summary1Table', $summary1Table) ;
$smarty->assign('summary1Table', $summary1Table) ;
$smarty->assign('BranchCount',$BranchCount);
$smarty->assign('Branch',$Branch);
$smarty->assign('ScrivenerCount',$ScrivenerCount);
$smarty->assign('Scrivener',$Scrivener);
$smarty->assign('target',$target);
$smarty->assign('group',$group);
$smarty->assign('use',$use);
$smarty->assign('grade',$grade);
$smarty->assign('summary1',$summary1);
$smarty->assign('contribution',$contribution);
$smarty->assign('seasontarget',$seasontarget);
$smarty->assign('seasongroup',$seasongroup);
$smarty->assign('seasonuse',$seasonuse);
$smarty->assign('showseason',$showseason);
$smarty->assign('seasoncontribution',$seasoncontribution);
$smarty->assign('oseasontarget',$oseasontarget);
$smarty->assign('oseasongroup',$oseasongroup);
$smarty->assign('oseasonuse',$oseasonuse);
$smarty->assign('oshowseason',$oshowseason);
$smarty->assign('oseasoncontribution',$oseasoncontribution);
$smarty->assign('effect_type',$effect_type);
$smarty->assign('eff1',$eff1);
$smarty->assign('eff2',$eff2);
$smarty->assign('groupTW',$groupTW);
$smarty->assign('groupUnTW',$groupUnTW);
$smarty->assign('seasongroupTW',$seasongroupTW);
$smarty->assign('seasongroupUnTW',$seasongroupUnTW);
$smarty->assign('percent',$percent);
//$contribution = $season1[$i]['contribution'];


if ($yr <= 106 || $_SESSION['member_pDep']  == 7) {
	$smarty->display('salesReport.inc.tpl', '', 'report') ;
	// $smarty->display('salesReport_107.inc.tpl', '', 'report') ;
}elseif ($yr >= 107) {
	$smarty->display('salesReport_'.$yr.'.inc.tpl', '', 'report') ;
}

?>