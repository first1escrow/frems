<?php

include_once dirname(dirname(dirname(__FILE__))).'/configs/config.class.php';
include_once dirname(dirname(dirname(__FILE__))).'/web_addr.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;

$_POST = escapeStr($_POST) ;

$cat = $_POST['cat'];
$id = $_POST['id'];
$sort = $_POST['sort'];

// print_r($sort);

if ($cat == 'up') {
	$sql = "UPDATE tBankBanner SET bSort = '".$sort."' WHERE bId='".$id."'";

	if ($conn->Execute($sql)) {
		$ok = 1;
	}
	
}elseif ($cat == 'down') {
	$sql = "UPDATE tBankBanner SET bSort = '".$sort."' WHERE bId='".$id."'";
	if ($conn->Execute($sql)) {
		$ok = 1;
	}
}elseif ($cat == 'del') {
	$sql = "UPDATE tBankBanner SET bDel = '1' WHERE bId ='".$id."'";
	if ($conn->Execute($sql)) {
		$ok = 1;
	}
}elseif ($cat == 'ok') {
	$sql = "UPDATE tBankBanner SET bOk = '1' WHERE bId='".$id."'";
	if ($conn->Execute($sql)) {
		$ok = 1;
	}
}elseif ($cat == 'no') {
	$sql = "UPDATE tBankBanner SET bOk = '0' WHERE bId='".$id."'";
	if ($conn->Execute($sql)) {
		$ok = 1;
	}
}elseif ($cat == 'ok2') {
	$sql = "UPDATE tBankBanner SET bOk2 = '1' WHERE bId='".$id."'";
	if ($conn->Execute($sql)) {
		$ok = 1;
	}
}elseif ($cat == 'no2') {
	$sql = "UPDATE tBankBanner SET bOk2 = '0' WHERE bId='".$id."'";
	if ($conn->Execute($sql)) {
		$ok = 1;
	}
}
// echo $sql;
echo $ok;

?>