<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;

$_POST = escapeStr($_POST) ;
$startYear = empty($_POST['startYear'])?'0':($_POST['startYear']+1911);
$startMonth = $_POST['startMonth'];

$endYear = empty($_POST['endYear'])?'0':($_POST['endYear']+1911);
$endMonth = $_POST['endMonth'];

//建經
$sDate = $startYear.'-'.$startMonth.'-01' ;
$eDate = $endYear.'-'.$endMonth.'-31' ;

//內政部rDate
$sDate1 = $startYear.$startMonth;
$eDate1 = $endYear.$endMonth;
if ($startYear != 0 && $endYear != 0 && !empty($startMonth) && !empty($endMonth)) {
	include_once 'caseTransExcel.php';
}


$menu_Year = array();
for ($i=100; $i <= (date('Y')-1911); $i++) { 
	$menu_Year[$i] = $i;
}
##
$smarty->assign('menu_Year',$menu_Year);
$smarty->assign('startYear',$startYear);
$smarty->assign('endYear',$endYear);
$smarty->assign('startMonth',$startMonth);
$smarty->assign('endMonth',$endMonth);

$smarty->display('caseTransSearch.inc.tpl', '', 'report');
?>