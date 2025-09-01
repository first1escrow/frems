<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/includes/staff/leaveFunction.php';

$code = empty($_GET['code']) ? '' : $_GET['code'];
if (empty($code)) {
    breakOut('連結異常');
    exit;
}

$caseId = base64_decode(urldecode($code));

$case = [];

$conn = new First1DB;

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
    breakOut('查無此申請');
    exit;
}

if (empty($case['sLeaveAttachment'])) {
    breakOut('無附件');
    exit;
}

$attachment = dirname(dirname(__DIR__)) . '/uploads/leaveApply/' . $case['sLeaveAttachment'];
if (!is_file($attachment)) {
    breakOut('附件不存在');
    exit;
}

//設定檔頭
$extension = pathinfo($attachment, PATHINFO_EXTENSION);

$header = 'image/jpeg';
$header = ($extension == 'bmp') ? 'image/bmp' : $header;
$header = ($extension == 'png') ? 'image/png' : $header;
$header = ($extension == 'gif') ? 'image/gif' : $header;
$header = 'content-type: ' . $header;

header($header);
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$fileContents = file_get_contents($attachment);
if ($fileContents === false) {
    breakOut('無法讀取附件');
    exit;
}

echo $fileContents;

exit;