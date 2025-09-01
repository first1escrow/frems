<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;

$smarty->display('menu.inc.tpl', '', 'escrow');
?>
