<?php
require_once dirname(dirname(__DIR__)) . '/HR/attendanceLock.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

header('Content-Type: application/json');

$log = new TraceLog('/HR/attendance');
$log->log($_SESSION['attendance'], print_r($_POST, true), '新增打卡紀錄', 'ADD');

$staffId = empty($_POST['staffId']) ? null : $_POST['staffId'];
$date    = empty($_POST['date']) ? null : $_POST['date'];
$inOut   = empty($_POST['inOut']) ? null : $_POST['inOut'];
$time    = empty($_POST['time']) ? null : $_POST['time'];

if (empty($staffId) || ! is_numeric($staffId)) {
    exit(json_encode(['error' => true, 'message' => '無法確認員工身分']));
}

if (empty($date) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    exit(json_encode(['error' => true, 'message' => '請輸入查詢日期時間']));
}

if (empty($inOut) || ! in_array($inOut, ['IN', 'OUT'])) {
    exit(json_encode(['error' => true, 'message' => '請選擇上下班狀態']));
}

if (empty($time) || ! preg_match('/^\d{2}:\d{2}$/', $time)) {
    exit(json_encode(['error' => true, 'message' => '請輸入正確的時間格式']));
}
$time = date('H:i:s', strtotime($time));

$conn = new First1DB;

$sql  = 'INSERT INTO tStaffCheckIn (sStaffId, sDateTime, sInOut, sFrom, sIp, sCreated_at) VALUES (:staffId, :dateTime, :inOut, 5, :ip, NOW());';
$bind = [
    'staffId'  => $staffId,
    'dateTime' => $date . ' ' . $time,
    'inOut'    => $inOut,
    'ip'       => $_SERVER['REMOTE_ADDR'],
];
if ($conn->exeSql($sql, $bind)) {
    //20250708 刪除異常提醒紀錄
    $sql = 'DELETE FROM tStaffCheckInAlert WHERE sStaffId = :staffId AND sDate = :date;';
    $conn->exeSql($sql, [
        'staffId' => $staffId,
        'date'    => $date,
    ]);

    $log->log($_SESSION['attendance'], '打卡記錄新增成功', '新增寫入打卡紀錄', 'INFO');
    exit(json_encode(['success' => true, 'message' => '打卡記錄已新增']));
}

$log->log($_SESSION['attendance'], '打卡記錄新增失敗', '新增寫入打卡紀錄', 'ERROR');
exit(json_encode(['error' => true, 'message' => '打卡記錄新增失敗']));
