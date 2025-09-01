<?php
include_once '../session_check.php' ;
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_REQUEST), '查看出款未審核案件明細') ;

$_POST = escapeStr($_POST) ;

// print_r($_POST);
$count = 0;
$txt = '';
for ($i=0; $i < count($_POST['check']); $i++) { 
	# code...

	$_time = date("Y-m-d H:i:s");
	$sql = "update tBankTrans set tOk='1' , tOk_date='$_time' where tId='".$_POST['check'][$i]."'";
	// echo $sql."\r\n";
	
	if ($conn->Execute($sql)) {
		$count++;
	}
}

if ($count == count($_POST['check'])) {
	echo '更改成功';
}else{
	echo '失敗';
}
?>