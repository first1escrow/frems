<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_REQUEST), '查看銀行資料列表') ;

$t = $_REQUEST['t'] ;
$f = $_REQUEST['f'] ;

$yr = array() ;
for ($i = (date("Y") - 1911) ; $i >= 101 ; $i --) {
	$yr['f'] .= '<option value='.$i ;
	$yr['t'] .= '<option value='.$i ;
	
	if ($i == $f) $yr['f'] .= ' selected="selected"' ;
	if ($i == $t) $yr['t'] .= ' selected="selected"' ;
	
	$yr['f'] .= '>'.$i."</option>\n" ;
	$yr['t'] .= '>'.$i."</option>\n" ;
}

$smarty->assign('yr',$yr) ;
$smarty->assign('web_addr',$web_addr) ;
$smarty->display('listinspection.inc.tpl', '', 'income');
?> 
