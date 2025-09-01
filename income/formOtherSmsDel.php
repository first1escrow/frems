<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$id = $_POST['id'];

$sql = "SELECT * FROM tExpenseDetailSmsOther WHERE WHERE eId ='".$id."'";
$rs = $conn->Execute($sql);
$data = $rs->fields;
##

$sql = "UPDATE tExpenseDetailSmsOther SET eDel = 1 WHERE eId ='".$id."'";

if ($conn->Execute($sql)) {
	$sql = "DELETE FROM tExpenseDetail WHERE eOtherId = '".$id."' AND eOK =''";
	$conn->Execute($sql);
	echo 'OK';
}
die;
?>