<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../../tracelog.php' ;

// $smarty->assign('opStaus',array(0=>'待確認',1=>'待審核',2=>'已審核'));
$smarty->display('IBookManagerList.inc.tpl', '', 'bank') ;
?>