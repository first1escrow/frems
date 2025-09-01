<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
// require_once dirname(__DIR__) . '/staff/HRMenuLock.php';

$smarty->display('leaveCalender.inc.tpl', '', 'HR');