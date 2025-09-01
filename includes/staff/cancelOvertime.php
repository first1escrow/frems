<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$sId = empty($_POST['id']) ? '' : $_POST['id'];
if (empty($sId)) {
    http_response_code(400);
    exit('無法取得加班申請資料');
}

$conn = new first1DB;
$sql  = 'SELECT
            sId,
            sApplicant,
            sOvertimeFromDateTime,
            sOvertimeToDateTime,
            sTotalHoursOfOvertime,
            sApplyReason,
            sUnitApprovalDateTime,
            sProcessing,
            sStatus,
            sCreatedAt
        FROM
            tStaffOvertimeApply
        WHERE
            sId = :sId;';
$bind         = ['sId' => $sId];
$overtimeData = $conn->one($sql, $bind);

if (empty($overtimeData)) {
    http_response_code(400);
    exit('無法取得加班申請資料');
}

if (empty($overtimeData['sUnitApprovalDateTime']) && ($overtimeData['sStatus'] == 'N')) {
    $sql  = 'UPDATE tStaffOvertimeApply SET sStatus = "C", sProcessing="F" WHERE sId = :sId;';
    $bind = ['sId' => $sId];
    if ($conn->exeSql($sql, $bind)) {
        exit('加班申請已經取消了');
    }

    http_response_code(500);
    exit('加班申請取消失敗');
}

if (!empty($overtimeData['sUnitApprovalDateTime']) || ($overtimeData['sStatus'] != 'N')) {
    http_response_code(400);
    exit('加班申請已經簽核了');
}

http_response_code(400);
exit('加班申請取消失敗');