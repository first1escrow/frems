<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;


if ($_POST['time'] == 'm') {
	// include_once 'banktrans_report_excel.php';
	include_once 'banktrans_report_excel.php';
}


$s_year = ($_POST['s_year'])?$_POST['s_year']:date('Y')-1911;
$e_year = ($_POST['e_year'])?$_POST['e_year']:date('Y')-1911;
for ($i=105; $i <= (date('Y')-1911) ; $i++) { 
	$year_option[$i] =$i;
}

$conn->close();
###
$smarty->assign('year_option',$year_option);
$smarty->assign('s_year',$s_year);
$smarty->assign('e_year',$e_year);
$smarty->display('banktrans_report2.inc.tpl', '', 'banktrans') ;
?>