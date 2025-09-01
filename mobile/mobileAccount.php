<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_REQUEST = escapeStr($_REQUEST) ;



if ($_REQUEST['identity'] == 2) {
	$class = '';
	$class1 = 'focus_end' ;
}else{
	$class = 'focus_end' ;
	$class1 = '';

}
// echo $_REQUEST['identity'];
###
$smarty->assign('identity',$_REQUEST['identity']);
$smarty->assign('class',$class);
$smarty->assign('class1',$class1);
$smarty->assign('list',$list);
$smarty->display('mobileAccount.inc.tpl', '', 'mobile');
?>
