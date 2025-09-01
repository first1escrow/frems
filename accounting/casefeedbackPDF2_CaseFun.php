<?php
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

$_POST = escapeStr($_POST) ;

$id = $_POST['id'];
$cat = $_POST['cat'];
if ($cat == 1) {
	$sql = "SELECT sLock FROM tStoreFeedBackMoneyFrom WHERE sId = '".$id."'";
	$rs = $conn->Execute($sql);

	$lock = ($rs->fields['sLock'] == 1)?2:1;

	$sql = "UPDATE tStoreFeedBackMoneyFrom SET sLock = '".$lock."' WHERE sId = '".$id."'";

	if ($conn->Execute($sql)) {
		echo '成功';
	}
}elseif ($cat == 2) {
	


    $sql = "UPDATE tStoreFeedBackMoneyFrom SET sLock = '0', sDelete = '1', sDeleteName = '".$_SESSION['member_id']."' WHERE sId = '".$id."'";


	if ($conn->Execute($sql)) {
		echo '成功';
	}
}






?>