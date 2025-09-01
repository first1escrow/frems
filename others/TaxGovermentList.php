<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_REQUEST = escapeStr($_REQUEST) ;


// echo $_REQUEST['identity'];
###


$smarty->display('TaxGovermentList.inc.tpl', '', 'other');
?>
