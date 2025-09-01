<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$tId = $_POST['tId'];


if ($tId) {
	$sql = "UPDATE tSMS_Check SET tUndertakerCheck= 1 WHERE tTaskID = '".$tId."'";
	// echo $sql;
	if ($conn->Execute($sql)) {
		
		echo '已完成';


	}else{
		echo "請重新再試";
	}
}

?>