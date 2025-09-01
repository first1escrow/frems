<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


if ($_POST['check']==1) {

	include_once 'import_feedSmsListFile.php';
}


$smarty->display('import_feedSmsList.inc.tpl', '', 'accounting');
?>