<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], ' ', '查看仲介品牌維護列表');

$smarty->display('listbrand.inc.tpl', '', 'income');
