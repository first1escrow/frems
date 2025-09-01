<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/staffDefaultLeave.class.php';

use First1\V1\Staff\StaffDefaultLeave;

$staffDefaultLeave = StaffDefaultLeave::getInstance();

$rs = $staffDefaultLeave->getStaffs();

// $staffs = [0 => '全部'];
foreach ($rs as $key => $value) {
    $staffs[$value['sStaffId']] = $value['sStaffName'];
}

$smarty->assign('staffs', $staffs);
$smarty->assign('staffSelected', 3);
$smarty->display('staffDefaultLeaveHistory.inc.tpl', '', 'HR');
