<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_REQUEST = escapeStr($_REQUEST) ;


###
$smarty->assign('list',$list);
$smarty->display('QuestionList.inc.tpl', '', 'line');
?>
