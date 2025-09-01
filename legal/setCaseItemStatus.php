<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


$_POST = escapeStr($_POST) ;

if (is_numeric($_POST['id'])) {
	$sql = "UPDATE tLegalCaseDetail SET lStatus = 1 WHERE lId = '".$_POST['id']."'";
	// echo $sql;
	if ($conn->Execute($sql)) {
		echo '需辦事項已結案';
	}else{
		echo '失敗請重新再試';
	}

	$conn->close();
}else{
	echo '失敗請重新再試';
}


?>