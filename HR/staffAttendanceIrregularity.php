<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$fromDate = date('Y-m-d', strtotime('-30 day'));
$toDate   = date('Y-m-d');

$smarty->assign('fromDate', $fromDate);
$smarty->assign('toDate', $toDate);

$smarty->display('staffAttendanceIrregularity.inc.tpl', '', 'HR');