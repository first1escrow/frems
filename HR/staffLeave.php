<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$fromDate = date('Y-m-d', strtotime('-2 days'));
$toDate   = date('Y-m-d', strtotime('+1 month'));

$conn = new First1DB();

$sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pJob = 1 AND pId NOT IN (2, 6, 8, 66) ORDER BY pOnBoard ASC;';
$rs  = $conn->all($sql);

$staffs        = [];
$staffSelected = 0;

$staffs[0] = '全部人員';
foreach ($rs as $row) {
    $staffs[$row['pId']] = $row['pName'];
}

$smarty->assign('fromDate', $fromDate);
$smarty->assign('toDate', $toDate);

$smarty->assign('staffs', $staffs);
$smarty->assign('staffSelected', $staffSelected);

$smarty->display('staffLeave.inc.tpl', '', 'HR');
