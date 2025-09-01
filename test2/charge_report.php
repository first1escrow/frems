<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php';

// if($_SESSION['member_id']!=6)
// {
// 	echo '建置中';
// 	die();
// }

##時間下拉
$yr = date("Y") - 1911 ;
$mn = date("m",mktime(0,0,0,(date("m")-1))) ;

// 年度顯示
$y = '' ;
for ($i = 0 ; $i < 100 ; $i ++) {
	$patt = $i + 100 ;
	
	if (($patt==$yr)&&($mn!='12')) { $sl = " selected='selected'" ; }
	else if ((($patt+1)==$yr)&&($mn=='12')) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$y .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

// 月份顯示
$m = '' ;
for ($i = 0 ; $i < 12 ; $i ++) {
	$patt = $i + 1 ;
	
	if ($patt==$mn) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$m .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

##
##







$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign('menu_sales', $menu_sales) ;
$smarty->display('charge_report.inc.tpl', '', 'report') ;
?>