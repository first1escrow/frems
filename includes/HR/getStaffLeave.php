<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/HumanResource.class.php';
require_once dirname(dirname(__DIR__)) . '/includes/staffHRBeginDate.php';

use First1\V1\HR\HR;

$from = $_POST['from'];
$to   = $_POST['to'];

if (empty($from) || empty($to)) {
    http_response_code(400);
    exit('Invalid date range');
}

$from = date('Y-m-d', strtotime($from));
$to   = date('Y-m-d', strtotime($to));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
    http_response_code(400);
    exit('Invalid date format');
}

header('Content-Type: application/json');

$conn = new First1DB;

$hr            = HR::getInstance();
$hr->BEGINDATE = BEGINDATE;

$dept = $_SESSION['member_pDep'];

//雄哥與家津不限制部門
if ($_SESSION['pHRCalender'] >= 3) {
    $dept = null;
}

//會計部門
if ($dept == 10) {
    $dept = [9, 10];
}

//履保主管兼看行政部門
if (in_array($_SESSION['member_id'], [1, 12])) {
    $dept = [5, 11];
}

$staffs = $hr->dumpStaffData(null, $dept);

if (empty($staffs)) {
    throw new Exception('No staff data found');
}

$sql = 'SELECT
            sId,
            sApplicant,
            sLeaveId,
            (SELECT sLeaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) as leaveName,
            (SELECT pDep FROM tPeopleInfo WHERE pId = a.sApplicant) as pDep,
            (SELECT dColor FROM tDepartment WHERE dId = pDep) as dColor,
            sLeaveFromDateTime,
            sLeaveToDateTime,
            sLeaveFromTmestamp,
            sLeaveToTimestamp,
            sTotalHoursOfLeave,
            sLeaveAttachment,
            sAgentApproval,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sAgentApproval) as agentName,
            sAgentApprovalDateTime,
            sUnitApproval,
            sUnitApprovalDateTime,
            sManagerApproval,
            sManagerApprovalDateTime,
            sProcessing
        FROM
            tStaffLeaveApply AS a
        WHERE
            sApplicant IN (' . implode(',', array_keys($staffs)) . ')
            AND sLeaveFromTmestamp <= :to
            AND sLeaveToTimestamp >= :from
            AND sStatus = "Y";';
$leaveData = $conn->all($sql, ['from' => strtotime($from), 'to' => strtotime($to)]);

if (empty($leaveData)) {
    exit('[]');
}

foreach ($leaveData as $data) {
    if (!isset($staffs[$data['sApplicant']])) {
        continue;
    }

    $staffs[$data['sApplicant']]['leave'][] = $data;
}

$data = [];
foreach ($staffs as $v) {
    if ($v['leave']) {
        foreach ($v['leave'] as $leave) {
            $start = date('Y-m-d', $leave['sLeaveFromTmestamp']);
            $end   = date('Y-m-d', $leave['sLeaveToTimestamp']);
            if ($start < $end) { // 跨日時 end date 要 +1
                $end = date('Y-m-d', strtotime($end . ' +1 day'));
            }

            $staff = $v['pName'];
            $staff .= empty($leave['agentName']) ? '' : ' (' . $leave['agentName'] . ')';
            $data[] = [
                'id'              => $leave['sId'],
                'title'           => $staff,
                'start'           => $start,
                'end'             => $end,
                'backgroundColor' => $leave['dColor'],
            ];
        }
    }
}
exit(json_encode($data, JSON_UNESCAPED_UNICODE));