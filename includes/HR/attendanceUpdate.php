<?php
require_once dirname(dirname(__DIR__)) . '/HR/attendanceLock.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

header('Content-Type: application/json');

$log = new TraceLog('/HR/attendance');
$log->log($_SESSION['attendance'], print_r($_POST, true), '更新打卡紀錄', 'UPDATE');

$id    = empty($_POST['id']) ? null : $_POST['id'];
$inOut = empty($_POST['inOut']) ? null : $_POST['inOut'];
$time  = empty($_POST['time']) ? null : $_POST['time'];

if (empty($id) || ! is_numeric($id)) {
    exit(json_encode(['error' => true, 'message' => '無法確認更新紀錄']));
}

if (empty($inOut) || ! in_array($inOut, ['IN', 'OUT'])) {
    exit(json_encode(['error' => true, 'message' => '請選擇上下班狀態']));
}

if (empty($time) || ! preg_match('/^\d{2}:\d{2}$/', $time)) {
    exit(json_encode(['error' => true, 'message' => '請輸入正確的時間格式']));
}
$time = date('H:i:s', strtotime($time));

$conn = new First1DB;

$sql  = 'SELECT sStaffId, sDateTime, sInOut, sFrom, sIp FROM tStaffCheckIn WHERE sId = :id;';
$data = $conn->one($sql, ['id' => $id]);

if (empty($data)) {
    $log->log($_SESSION['attendance'], '無法找到打卡記錄', '更新打卡紀錄', 'ERROR');
    exit(json_encode(['error' => true, 'message' => '無法找到打卡記錄']));
}

$dateTime = date('Y-m-d', strtotime($data['sDateTime'])) . ' ' . $time;

$sql = 'UPDATE tStaffCheckIn SET sInOut = :inOut, sDateTime = :dateTime WHERE sId = :id;';

$bind = [
    'id'       => $id,
    'inOut'    => $inOut,
    'dateTime' => $dateTime,
];

if ($conn->exeSql($sql, $bind)) {
    //20250708 刪除異常提醒紀錄
    $sql = 'DELETE FROM tStaffCheckInAlert WHERE sStaffId = :staffId AND sDate = :date;';
    $conn->exeSql($sql, [
        'staffId' => $data['sStaffId'],
        'date'    => date('Y-m-d', strtotime($data['sDateTime'])),
    ]);

    $log->log($_SESSION['attendance'], '打卡記錄更新成功', '更新打卡紀錄', 'INFO');
    exit(json_encode(['success' => true, 'message' => '打卡記錄已更新']));
}

$log->log($_SESSION['attendance'], '打卡記錄更新失敗', '更新打卡紀錄', 'ERROR');
exit(json_encode(['error' => true, 'message' => '打卡記錄更新失敗']));
