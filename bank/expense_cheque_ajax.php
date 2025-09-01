<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_REQUEST), '查看銀行存入支票列表-更改成不寄送') ;

$_POST = escapeStr($_POST) ;

$id = $_POST['id'];
$status = 2;

$sql= "SELECT eSms FROM tExpense_cheque WHERE id = '".$id."'";

$rs = $conn->Execute($sql);

if ($rs->fields['eSms'] == 2) {
	$status = 0;
}elseif ($rs->fields['eSms'] == 1) {
	$status = 1;
}

$sql = "UPDATE tExpense_cheque SET eSms = '".$status."' WHERE id ='".$id."'";


if ($conn->Execute($sql)) {
	echo '1';
}
?>