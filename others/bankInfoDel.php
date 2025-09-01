<?php
include_once '../openadodb.php';
include_once '../session_check.php' ;


$_POST = escapeStr($_POST) ;

if ($_POST['id']) {
	$sql = "UPDATE tBankInfo SET bDel = '1',bModifyName='".$_SESSION['member_id']."' WHERE bId ='".$_POST['id']."'";

	$conn->Execute($sql);


}

?>