<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once __DIR__ . '/attendanceLock.php';

$conn = new first1DB();

$sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pJob = 1 AND pId NOT IN (2, 3, 6, 8, 66) ORDER BY pOnBoard ASC;';
$rs  = $conn->all($sql);

$menu_staff = [0 => ''];
if (! empty($rs)) {
    foreach ($rs as $row) {
        $menu_staff[$row['pId']] = $row['pName'];
    }
}

$smarty->assign('menu_staff', $menu_staff);
$smarty->assign('today', date('Y-m-d', strtotime('yesterday')));

$smarty->display('staffAttendance.inc.tpl', '', 'HR');
