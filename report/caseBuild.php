<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['ck'] == 1) {
	
	include_once 'caseBuildExcel.php';
	
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
$smarty->assign('year',date('Y'));
$smarty->assign('citys', $citys) ;
$smarty->assign('menuBrand',$menuBrand);
$smarty->display('caseBuild.inc.tpl', '', 'report2') ;
?> 
