<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/leaveHourCount.class.php';

use First1\V1\Staff\LeaveHourCount;

$leaveId   = $_POST['leaveId'];
$dateFrom  = $_POST['dateFrom'];
$dateTo    = $_POST['dateTo'];
$timeFrom  = $_POST['timeFrom'];
$timeTo    = $_POST['timeTo'];
$member_id = $_POST['member_id'];
$dateAll   = $_POST['dateAll'];
$reason    = $_POST['reason'];

if ($_SESSION['member_id'] != $member_id) {
    exit('無法確認員工身分');
}

if (empty($leaveId) || ! is_numeric($leaveId)) {
    exit('無法取得請假資料');
}

if (empty($dateFrom) || empty($dateTo) || empty($member_id)) {
    exit('請填寫完整請假時間(E)');
}

if (! empty($timeFrom) && ! preg_match('/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $timeFrom)) {
    exit('請填寫正確請假時間(B)');
}

if (! empty($timeTo) && ! preg_match('/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $timeTo)) {
    exit('請填寫正確請假時間(E)');
}

if (! in_array($dateAll, ['A', 'S'])) {
    exit('請填寫完整請假時間(A)');
}

if (empty($reason)) {
    exit('請填寫請假事由');
}

if ($dateAll == 'A') {
    $timeFrom = '09:00:00';
    $timeTo   = '18:00:00';
}

$leaveDateFrom = $dateFrom . ' ' . $timeFrom;
$leaveDateTo   = $dateTo . ' ' . $timeTo;

$totalHours = LeaveHourCount::getLeaveHours(new DateTime($leaveDateFrom), new DateTime($leaveDateTo), $dateAll);

$conn = new first1DB;

$sql = 'SELECT
            sLeaveDefault,
            sLeaveBalance
        FROM
            tStaffLeaveDefault
        WHERE
            sStaffId = :member_id
            AND sLeaveId = :leave_id;';

if ($leaveId == 1) {
    $sql = 'SELECT
            SUM(sLeaveDefault) as sLeaveDefault,
            SUM(sLeaveBalance) as sLeaveBalance
        FROM
            tStaffLeaveDefault
        WHERE
            sStaffId = ' . $member_id . '
            AND sLeaveId IN (1, 2);';
}

$bind = [
    'member_id' => $member_id,
    'leave_id'  => $leaveId,
];

$leave = $conn->one($sql, $bind);
if (empty($leave)) {
    exit('OK');
}

$sLeaveBalance = $leave['sLeaveBalance'];
if ($sLeaveBalance < $totalHours) {
    exit('請假時數不足');
}

exit('OK');
