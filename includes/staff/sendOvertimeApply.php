<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/leaveHourCount.class.php';
require_once dirname(dirname(__DIR__)) . '/class/staffNotify.class.php';
require_once dirname(dirname(__DIR__)) . '/includes/staff/leaveFunction.php';
require_once dirname(dirname(__DIR__)) . '/includes/staff/overtimeFunction.php';
require_once dirname(dirname(dirname(__DIR__))) . '/lib/rc4/crypt.php';

use First1\V1\Staff\LeaveHourCount;
use First1\V1\Staff\StaffNotify;

$staffId = $_POST['member_id'];
if (empty($staffId) || ($staffId != $_SESSION['member_id'])) {
    breakOut('無法取得員工資料');
}

$overtimeDate = $_POST['overtime-date'];
$timeFrom     = $_POST['time-from'];
$timeTo       = $_POST['time-to'];
$overtimeType = $_POST['overtime-type'];
$sApplyReason = $_POST['apply-reason'];

if (empty($overtimeDate) || empty($timeFrom) || empty($timeTo)) {
    breakOut('請填寫完整加班申請時間');
}

$overtimeDateFrom = $overtimeDate . ' ' . $timeFrom;
$overtimeDateTo   = $overtimeDate . ' ' . $timeTo;
$timestampFrom    = strtotime($overtimeDateFrom);
$timestampTo      = strtotime($overtimeDateTo);

$totalHours = LeaveHourCount::getOvertimeHours($overtimeDateFrom, $overtimeDateTo);

$unitApproval = getSupervisor($staffId);
if ($unitApproval == $staffId) { //主管是自己
    $unitApproval = MANAGER;         //20241216 家津請示過雄哥後，主管直接由總經理簽核
}

$conn = new first1DB;
$sql  = 'INSERT INTO
            tStaffOvertimeApply
            (
                sApplicant,
                sOvertimeType,
                sOvertimeFromDateTime,
                sOvertimeToDateTime,
                sTotalHoursOfOvertime,
                sApplyReason,
                sUnitApproval,
                sProcessing,
                sStatus,
                sCreatedAt
            ) VALUES (
                :sApplicant,
                :sOvertimeType,
                :sOvertimeFromDateTime,
                :sOvertimeToDateTime,
                :sTotalHoursOfOvertime,
                :sApplyReason,
                :sUnitApproval,
                :processing,
                :status,
                :createdAt
            );';

$bind = [
    'sApplicant'            => $staffId,
    'sOvertimeType'         => $overtimeType,
    'sOvertimeFromDateTime' => $overtimeDateFrom,
    'sOvertimeToDateTime'   => $overtimeDateTo,
    'sTotalHoursOfOvertime' => $totalHours,
    'sApplyReason'          => $sApplyReason,
    'sUnitApproval'         => $unitApproval,
    'processing'            => 'U',
    'status'                => 'N',
    'createdAt'             => date('Y-m-d H:i:s'),
];

if ($conn->exeSql($sql, $bind)) {
    $insertId     = $conn->lastInsertId();
    $overtimeData = getOvertimeData($insertId);
    $overtimeData = array_merge($overtimeData, ['apply' => $_SESSION['member_id']]);
    $response     = sendOvertimeApplyNotify($overtimeData, $insertId, true);

    breakOut('已送出申請');
}

breakOut('無法送出申請');
