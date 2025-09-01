<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once __DIR__ . '/attendanceLock.php';

$log = new TraceLog('/HR/attendance');
// $log->log($_SESSION['attendance'], print_r($_GET, true), '新增打卡紀錄頁面', 'GET');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($id) || ! is_numeric($id)) {
    $log->log($_SESSION['attendance'], '無法確認打卡記錄ID', '編輯打卡紀錄', 'ERROR');
    exit('無法確認打卡記錄ID');
}

$conn = new first1DB;

$sql = 'SELECT
            sId,
            sStaffId,
            (SELECT pName FROM tPeopleInfo WHERE pId = sStaffId) AS staffName,
            sDateTime,
            sInOut,
            sFrom
        FROM
            tStaffCheckIn
        WHERE
            sId = :id;';
$data = $conn->one($sql, ['id' => $id]);

if (empty($data)) {
    $log->log($_SESSION['attendance'], '無法找到打卡記錄', '編輯打卡紀錄頁面', 'ERROR');
    exit('無法找到打卡記錄');
}
$data['date'] = date('Y-m-d', strtotime($data['sDateTime']));
$data['time'] = date('H:i:s', strtotime($data['sDateTime']));
// echo '<pre>' . print_r($data, true) . '</pre>';exit;

$smarty->assign('data', $data);

$smarty->display('attendanceModify.inc.tpl', '', 'HR');
