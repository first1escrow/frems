<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['id']) {
	$sql = "UPDATE tGift SET gDel  = '1' WHERE gId = '".$_POST['id']."'";
	
	if ($conn->Execute($sql)) {
		echo '成功';
	}else{
		echo '失敗';
	}
	

}




?>
