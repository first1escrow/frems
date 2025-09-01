<?php
include_once dirname(dirname(__FILE__)).'/configs/config.class.php';
function checklist_log($txt){
	$usrName = $_SESSION['member_name'] ;					
	$usrAccount = $_SESSION['member_acc'] ;					
	$usrIP = '' ;											
	$usrDate = date("Y-m-d H:i:s") ;

	//IP
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$usrIP = $_SERVER['HTTP_CLIENT_IP'] ;
	}
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$usrIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
	}
	else {
		$usrIP = $_SERVER['REMOTE_ADDR'] ;
	}

	$line = $usrName.','.$usrAccount.','.$usrIP.','.$usrDate ;



	if ($txt) {
		$line .= ','.$txt ;
	}						

	$fw = fopen($GLOBALS['LOG_PATH'].'/checklist/checklist.log', 'a+');

	fwrite($fw, $line."\r\n");

	fclose($fw);
}


?>