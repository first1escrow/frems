<?php
require_once dirname(dirname(__DIR__)).'/configs/config.class.php';
require_once dirname(dirname(__DIR__)).'/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)).'/openadodb.php';
require_once dirname(dirname(__DIR__)).'/session_check.php';
require_once dirname(dirname(__DIR__)).'/tracelog.php' ;

$_POST = escapeStr($_POST);

//
$_POST['id'] = str_replace('o', '', $_POST['id']);

$sql = "UPDATE
			tBankTrankBookDetail
		SET 
			bDel = 1
		WHERE 
			bId ='".$_POST['id']."'
		";

if ($conn->Execute($sql)) {
	echo 'OK';
}

?>