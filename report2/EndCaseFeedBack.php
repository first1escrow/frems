<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

if ($_SESSION['member_id'] !=6 && $_SESSION['member_id'] !=1) {
	
	header("location:http://www.first1.com.tw/");
}




###
$smarty->display('EndCaseFeedBack.inc.tpl', '', 'report2');
?>