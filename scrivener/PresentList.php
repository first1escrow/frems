<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../sms/sms_function_manually.php' ;

$sms = new SMS_Gateway();

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$sql = "SELECT * FROM tGift WHERE gDel = 0 ORDER BY gId DESC";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	# code...
	$list[] = $rs->fields;
	$rs->MoveNext();
}

##
$smarty->assign('list',$list);
$smarty->display('PresentList.inc.tpl', '', 'scrivener');
?>
