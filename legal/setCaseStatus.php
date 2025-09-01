<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
// include_once dirname(dirname(__FILE__)).'/class/lineMessage.php';
require_once dirname(dirname(__FILE__)).'/includes/encode.php' ;
header("Content-Type:text/html; charset=utf-8"); 
$_POST = escapeStr($_POST) ;
$_REQUEST = escapeStr($_REQUEST) ;

if ($_POST['cat'] == 2) {
	

	require_once '../includes/escrow/transferCaseMsg.php';

	if ($send_check) {
		echo '已通知經辦';
	}else{
		echo "請重新再試";
	}
	
}elseif ($_REQUEST['v']) { //經辦點了連結
	$tmp = explode('&', deCrypt($_REQUEST['v']));
	
	foreach ($tmp as $k) {
	    parse_str($k) ;
	}

	$sql = "UPDATE tLegalCase SET lStatus = 2 WHERE lCertifiedId = '".$id."'";
	
	if ($conn->Execute($sql)) {
		echo "移轉成功，請關閉視窗";
	}
	
	
}

$conn->close();
?>