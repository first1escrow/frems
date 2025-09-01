<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once __DIR__ . '/attendanceLock.php';

$log = new TraceLog('/HR/attendance');
// $log->log($_SESSION['attendance'], print_r($_GET, true), '新增打卡紀錄頁面', 'GET');

$staffId = isset($_GET['staffId']) ? intval($_GET['staffId']) : 0;
$date    = isset($_GET['date']) ? $_GET['date'] : null;

if (empty($staffId) || ! is_numeric($staffId)) {
    $log->log($_SESSION['attendance'], '無法確認員工身分', '新增打卡紀錄', 'ERROR');
    exit('無法確認員工身分');
}

if (empty($date) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $log->log($_SESSION['attendance'], '請輸入查詢日期時間', '新增打卡紀錄', 'ERROR');
    exit('請輸入查詢日期時間');
}

$conn = new first1DB;

$sql   = 'SELECT pId, pName FROM tPeopleInfo WHERE pId = :id;';
$staff = $conn->one($sql, ['id' => $staffId]);
if (empty($staff)) {
    $log->log($_SESSION['attendance'], '無法找到員工資料', '新增打卡紀錄', 'ERROR');
    exit('無法找到員工資料');
}

$smarty->assign('staff', $staff);
$smarty->assign('date', $date);

$smarty->display('attendanceAdd.inc.tpl', '', 'HR');
