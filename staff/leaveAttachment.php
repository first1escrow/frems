<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$caseId = empty($_GET['code']) ? '' : $_GET['code'];
if (empty($caseId) || !is_numeric($caseId)) {
    exit('Invalid access'); //非法存取
}

$case = [];

$conn = new first1DB;

$sql = 'SELECT
            sApplicant,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as staffName,
            sLeaveId,
            (SELECT CASE WHEN sMemo = "" OR sMemo IS NULL THEN sLeaveName ELSE sMemo END AS name FROM tStaffLeaveType WHERE sId = a.sLeaveId) as leaveName,
            sLeaveFromDateTime,
            sLeaveToDateTime,
            sTotalHoursOfLeave,
            sLeaveAttachment,
            sAgentApproval,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.sAgentApproval) as agent,
            sAgentApprovalDateTime,
            sUnitApproval,
            sUnitApprovalDateTime,
            sManagerApproval,
            sManagerApprovalDateTime,
            sProcessing,
            sStatus
        FROM
            tStaffLeaveApply AS a
        WHERE
            sId = ' . $caseId . ';';
$case = $conn->one($sql);
if (empty($case)) {
    exit('查無此申請');
}

if (empty($case['sLeaveAttachment'])) {
    if (!is_file($attachment)) {
        exit('附件不存在');
    }
}

$attachment = dirname(dirname(__DIR__)) . '/uploads/leaveApply/' . $case['sLeaveAttachment'];
if (!is_file($attachment)) {
    exit('附件不存在');
}

//設定檔頭
$extension = pathinfo($attachment, PATHINFO_EXTENSION);

$header = 'image/jpeg';
$header = ($extension == 'bmp') ? 'image/bmp' : $header;
$header = ($extension == 'png') ? 'image/png' : $header;
$header = ($extension == 'gif') ? 'image/gif' : $header;
$header = 'content-type: ' . $header;

header($header);
echo file_get_contents($attachment);

exit;
