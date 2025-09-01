<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$id = $_POST['id'];

if (empty($id) || !is_numeric($id)) {
    http_response_code(400);
    exit('Invalid date index');
}

header('Content-Type: application/json');

$conn = new First1DB;

$sql = 'SELECT
            sId,
            sApplicant,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as applicantName,
            sLeaveId,
            (SELECT sLeaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) as leaveName,
            (SELECT pDep FROM tPeopleInfo WHERE pId = a.sApplicant) as pDep,
            (SELECT dColor FROM tDepartment WHERE dId = pDep) as dColor,
            sLeaveFromDateTime,
            sLeaveToDateTime,
            sLeaveFromTmestamp,
            sLeaveToTimestamp,
            sTotalHoursOfLeave,
            sApplyReason,
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
            sId = :id;';
$leaveData = $conn->all($sql, ['id' => $id]);

$rs = $conn->one($sql, ['id' => $id]);
if (empty($rs)) {
    exit(json_encode([], JSON_UNESCAPED_UNICODE));
}
$rs['sApplyReason'] = empty($rs['sApplyReason']) ? '' : $rs['sApplyReason'];
$rs['agentName']    = empty($rs['agentName']) ? '' : $rs['agentName'];

$code             = urlencode(base64_encode($rs['sId']));
$rs['attachment'] = empty($rs['sLeaveAttachment']) ? '無' : '<a href="Javascript:void(0);" onclick="attachment(\'' . $code . '\')">檢視</a>';

exit(json_encode($rs, JSON_UNESCAPED_UNICODE));