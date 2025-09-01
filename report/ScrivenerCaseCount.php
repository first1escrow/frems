<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['ck'] == 1) {
	
	include_once 'ScrivenerCaseCountExcel.php';

}

//縣市
$sql = 'SELECT zCity FROM tZipArea WHERE 1=1  GROUP BY zCity ORDER BY zZip,zCity ASC;' ;
$rs = $conn->Execute($sql);
$citys = '<option selected="selected" value="">全部</option>'."\n" ;
while (!$rs->EOF) {
	# code...
	$citys .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;
	$rs->MoveNext();
}
##

##
//業務
$sql = "SELECT pName,pId FROM tPeopleInfo WHERE pDep IN (4,7) AND pJob = 1";
$rs = $conn->Execute($sql);
$menuSales[0] = '全部';
while (!$rs->EOF) {
	
	$menuSales[$rs->fields['pId']] = $rs->fields['pName'];

	$rs->MoveNext();
}

//地政是
$sql = "SELECT CONCAT('SC',LPAD(sId,4,'0')) as code,sId,sName FROM tScrivener ";
$menuScrivener[0] = '全部';
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuScrivener[$rs->fields['sId']] = $rs->fields['code'].$rs->fields['sName'];

	$rs->MoveNext();
}

##
$smarty->assign('year',date('Y'));
$smarty->assign('citys', $citys) ;
$smarty->assign('menuScrivener',$menuScrivener);
$smarty->assign('menuBrand',$menuBrand);
$smarty->assign('menuSales',$menuSales);
$smarty->display('ScrivenerCaseCount.inc.tpl', '', 'report2') ;
?> 
