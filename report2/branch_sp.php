<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;


$check = trim($_REQUEST['ck']);

if ($check==1) {

	$month = trim($_REQUEST['month']) ;
	$years = trim($_REQUEST['years']) ;
	
	include_once 'branch_sp_excel.php';

}


//月份
for ($i=1; $i <=12; $i++) { 
	
	if ($i<10) {
		$i = "0".$i;
	}

	$menu_scrivener .="<option value='".$i."'>".$i."</option>";
	

}


//年度
$years = '' ;
for ($i = 2011 ; $i <= date("Y") ; $i ++) { 
	$years .= '<option value="'.$i.'"' ;
	if ($i == date("Y")) $years .= ' selected="selected"' ;
	$years .= '>'.($i - 1911)."</option>\n" ;
}



$smarty->assign('years',$years);
$smarty->assign('menu_scrivener',$menu_scrivener);
$smarty->display('branch_sp.inc.tpl', '', 'report2');

?>