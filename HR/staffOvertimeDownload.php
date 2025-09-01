<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/HumanResource.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

use First1\V1\HR\HR;

$fromDate = empty($_GET['fromDate']) ? '' : $_GET['fromDate'];
$toDate   = empty($_GET['toDate']) ? '' : $_GET['toDate'];

$hr = HR::getInstance();

// $staffs = $hr->dumpStaffData(null, 7);
$staffs = $hr->dumpStaffData();

$staff_menu = [0 => '全部'];
foreach ($staffs as $staff) {
    $staff_menu[$staff['pId']] = $staff['pName'];
}

$smarty->assign('staff_menu', $staff_menu);
$smarty->assign('staff_selected', 0);
$smarty->assign('fromDate', $fromDate);
$smarty->assign('toDate', $toDate);

$smarty->display('staffOvertimeDownload.inc.tpl', '', 'HR');
