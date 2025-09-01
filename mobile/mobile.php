<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;



###
$smarty->assign('list',$list);
$smarty->display('mobile.inc.tpl', '', 'mobile');
?>
