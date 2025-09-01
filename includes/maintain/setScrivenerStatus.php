<?php
include_once '../../openadodb.php';
include_once '../../session_check.php' ;


$_POST = escapeStr($_POST) ;
$count = 0;
if (!empty($_POST['id'])) {
	foreach ($_POST['id'] as $val) {

		

		$sql = "UPDATE tScrivenerLevel SET sStatus = 2,sInspetor='".$_SESSION['member_id']."',sTime2='".date("Y-m-d H:i:s")."' WHERE sId = '".$val."'";
		
		if ($conn->Execute($sql)) {
			$count++;
		}
			
		
	
	}

	if ($count == count($_POST['id'])) {
		echo "成功";
	}else{
		echo "異常，請重新再試";
	}
}
?>