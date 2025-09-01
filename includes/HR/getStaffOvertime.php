<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once __DIR__ . '/overtimeFunction.php';

header('Content-Type: application/json');

$fromDate = $_POST['fromDate'];
if (empty($fromDate) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $fromDate)) {
    throw new Exception('from data abnormal');
}

$toDate = $_POST['toDate'];
if (empty($toDate) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $toDate)) {
    throw new Exception('to data abnormal');
}

$from = $fromDate . ' 00:00:00';
$to   = $toDate . ' 23:59:59';

$conn = new first1DB;

//取得人員姓名資訊
$staffInfo = getStaffs($conn);

//取得部門資訊
$departments = getDepartments($conn);

//取得加班申請紀錄
$rs = getOvertimeData($conn, $from, $to);
if (! $rs) {
    exit(json_encode(['data' => []]));
}

//去除重複日期
$staffs = [];
foreach ($rs as $v) {
    $staffs[$v['sApplicant']][] = $v['date'];
    $staffs[$v['sApplicant']]   = array_unique($staffs[$v['sApplicant']]);
}

$output = [];
foreach ($staffs as $staff_id => $dates) {
    $data = getCheckInOutData($conn, $staff_id, $dates);
    if (empty($data)) {
        continue;
    }

    foreach ($data as $date => $v) {
        if (! empty($v['hours'])) {
            $output[] = [
                'date'       => $date,
                'department' => $departments[$staffInfo[$staff_id]['pDep']]['dDep'],
                'staffName'  => $staffInfo[$staff_id]['pName'],
                'fromTime'   => $v['IN'],
                'toTime'     => $v['OUT'],
                'totalHours' => $v['hours'],
            ];
        }
    }

}

exit(json_encode(['data' => $output]));