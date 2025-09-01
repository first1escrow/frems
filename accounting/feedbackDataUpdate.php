<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


if ($_POST['check']==1) {

	include_once 'feedbackDataUpdate_file.php';
	
}


$smarty->display('feedbackDataUpdate_file.inc.tpl', '', 'accounting');
?>