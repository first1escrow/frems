<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;


//確認 URL 來源為IP或Domain
$url = $_SERVER['HTTP_HOST'] ;
if ((!preg_match("/^first[2]?.twhg.com.tw$/",$url)) && (!preg_match("/^first[2]?.nhg.tw$/",$url))) {		//非 first & first2 後台網址
//if (!preg_match("/^first[2]?.twhg.com.tw$/",$url)) {		//非 first & first2 後台網址
//if (!preg_match("/^first.twhg.com.tw$/",$url)) {			//非 first 後台網址
	header('Location: http://www.first1.com.tw') ;
	exit ;
}
##

// //print_r($_COOKIE) ;
// $remembered = '' ;
// if (isset($_COOKIE['act'])&&isset($_COOKIE['psd'])) {
// 	$remembered = ' checked="checked"' ;
// }

//$smarty->assign('act',$_COOKIE['act']) ;
// $smarty->assign('act',$_COOKIE["act"]) ;
// $smarty->assign('psd',$_COOKIE['psd']) ;
// $smarty->assign('remembered',$remembered) ;

$smarty->display('welcome.inc.tpl', '', 'others');
?>
