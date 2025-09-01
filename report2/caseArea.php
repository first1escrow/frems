<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['ck'] == 1) {
	
	include_once 'caseAreaExcel.php';
	
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
//品牌
$sql = "SELECT bId,bName FROM tBrand";
$rs = $conn->Execute($sql);
$menuBrand[0] = '全部';
while (!$rs->EOF) {
	$menuBrand[$rs->fields['bId']] = $rs->fields['bName'];

	$rs->MoveNext();
}

##
$smarty->assign('year',date('Y'));
$smarty->assign('citys', $citys) ;
$smarty->assign('menuBrand',$menuBrand);
$smarty->display('caseArea.inc.tpl', '', 'report2') ;
?> 
