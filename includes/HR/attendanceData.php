<?php
require_once dirname(dirname(__DIR__)) . '/HR/attendanceLock.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

header('Content-Type: application/json');

$staffId = empty($_POST['staffId']) ? null : $_POST['staffId'];
$date    = empty($_POST['date']) ? null : $_POST['date'];

if (empty($staffId) || ! is_numeric($staffId)) {
    exit(json_encode(['error' => true, 'message' => '無法確認員工身分']));
}

if (empty($date) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    exit(json_encode(['error' => true, 'message' => '請輸入查詢日期時間']));
}

$conn = new First1DB;

$sql = 'SELECT
            sId,
            sStaffId,
            (SELECT pName FROM tPeopleInfo WHERE pId = sStaffId) AS sStaffName,
            sDateTime,
            DATE_FORMAT(sDateTime, "%H:%i:%s") AS attendanceTime,
            sInOut,
            CASE sInOut WHEN "IN" THEN "上班" WHEN "OUT" THEN "下班" ELSE "未知" END AS sInOutText
            FROM tStaffCheckIn WHERE sStaffId = :staffId AND sDateTime >= :fromDate AND sDateTime <= :toDate ORDER BY sDateTime, sInOut ASC;';
$bind = [
    'staffId'  => $staffId,
    'fromDate' => $date . ' 00:00:00',
    'toDate'   => $date . ' 23:59:59',
];
$data = $conn->all($sql, $bind);
if (empty($data)) {
    exit(json_encode(['success' => true, 'data' => [], 'message' => '查無考勤記錄']));
}

echo json_encode(['success' => true, 'data' => $data]);
exit;
