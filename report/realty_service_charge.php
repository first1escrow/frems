<?php
include_once '../configs/config.class.php' ;
include_once 'class/SmartyMain.class.php' ;
include_once 'class/intolog.php' ;
include_once '../session_check.php' ;

//預載log物件
$logs = new Intolog() ;
##

if ($_REQUEST['xls'] == 'ok') {
	$logs->writelog('realtyServiceChargeExcel') ;
	include_once 'realty_service_charge_excel.php' ;
}

$yr = date("Y") - 1911 ;
$mn = date("m",mktime(0,0,0,(date("m")-1))) ;

// 年度顯示
$y = '' ;
for ($i = 0 ; $i < 100 ; $i ++) {
	$patt = $i + 100 ;
	
	if (($patt==$yr)&&($mn!='12')) { $sl = " selected='selected'" ; }
	else if ((($patt+1)==$yr)&&($mn=='12')) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$y .= "<option value='".$patt."'".$sl.">".$patt."年度</option>\n" ;
}

// 月份顯示
$m = '' ;
for ($i = 0 ; $i < 12 ; $i ++) {
	$patt = $i + 1 ;
	
	if ($patt==$mn) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$m .= "<option value='".$patt."'".$sl.">".$patt."月份</option>\n" ;
}


$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;

$smarty->display('realty_service_charge.inc.tpl', '', 'report');
?>
