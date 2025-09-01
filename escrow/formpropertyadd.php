<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;

$smarty->display('formproperty.inc.tpl', '', 'escrow');
?>
