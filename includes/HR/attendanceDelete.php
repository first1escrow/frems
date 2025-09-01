<?php
require_once dirname(dirname(__DIR__)) . '/HR/attendanceLock.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

header('Content-Type: application/json');

$log = new TraceLog('/HR/attendance');
$log->log($_SESSION['attendance'], print_r($_POST, true), '刪除打卡紀錄', 'DELETE');

$id = empty($_POST['id']) ? null : $_POST['id'];

if (empty($id) || ! is_numeric($id)) {
    exit(json_encode(['error' => true, 'message' => '無法確認刪除紀錄']));
}

$conn = new First1DB;

$sql  = 'SELECT sStaffId, sDateTime, sInOut, sFrom, sIp FROM tStaffCheckIn WHERE sId = :id;';
$data = $conn->one($sql, ['id' => $id]);

if (empty($data)) {
    $log->log($_SESSION['attendance'], '無法找到打卡記錄', '刪除打卡紀錄', 'ERROR');
    exit(json_encode(['error' => true, 'message' => '無法找到打卡記錄']));
}

$sql = 'DELETE FROM tStaffCheckIn WHERE sId = :id;';
if ($conn->exeSql($sql, ['id' => $id])) {
    //20250708 刪除異常提醒紀錄
    $sql = 'DELETE FROM tStaffCheckInAlert WHERE sStaffId = :staffId AND sDate = :date;';
    $conn->exeSql($sql, [
        'staffId' => $data['sStaffId'],
        'date'    => date('Y-m-d', strtotime($data['sDateTime'])),
    ]);

    $log->log($_SESSION['attendance'], '打卡記錄刪除成功', '刪除打卡紀錄', 'INFO');
    exit(json_encode(['success' => true, 'message' => '打卡記錄已刪除']));
}

$log->log($_SESSION['attendance'], '打卡記錄刪除失敗', '刪除打卡紀錄', 'ERROR');
exit(json_encode(['error' => true, 'message' => '打卡記錄刪除失敗']));
