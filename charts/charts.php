<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], ' ', '檢視圖表統計') ;

$smarty->display('charts.inc.tpl', '', 'charts') ;
?> 
