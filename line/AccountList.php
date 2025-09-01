<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_REQUEST = escapeStr($_REQUEST) ;

$class = '';
$class1 = '';
$class2 = '';
$class3 = '';
if ($_REQUEST['identity'] == 2) {
	$class1 = 'focus_end' ;
}else if($_REQUEST['identity'] == 3){	
	$class2 = 'focus_end' ;
}elseif($_REQUEST['identity'] == 4){
	$class3 = 'focus_end' ;
}else{
	$class = 'focus_end' ;
	
}
// echo $_REQUEST['identity'];
###
$smarty->assign('identity',$_REQUEST['identity']);
$smarty->assign('class',$class);
$smarty->assign('class1',$class1);
$smarty->assign('class2',$class2);
$smarty->assign('class3',$class3);
$smarty->assign('list',$list);
$smarty->display('AccountList.inc.tpl', '', 'line');
?>
