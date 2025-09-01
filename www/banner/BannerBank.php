<?php

ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once '../../web_addr.php' ;
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;


$sql = "SELECT * FROM tBankBannerArea ORDER BY bId";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$bank[] = $rs->fields;

	$rs->MoveNext();
}

// print_r($bank);

##
$smarty->assign('bank',$bank);
$smarty->display('BannerBank.inc.tpl', '', 'www');
?>