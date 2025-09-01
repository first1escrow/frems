<?php
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$smarty->display('salesAgents.inc.tpl', '', 'sales') ;
?>
