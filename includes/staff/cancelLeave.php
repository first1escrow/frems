<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
// require_once dirname(dirname(__DIR__)) . '/class/staffNotify.class.php';

$sId = empty($_POST['id']) ? '' : $_POST['id'];
if (empty($sId)) {
    http_response_code(400);
    exit('無法取得請假資料');
}

$conn = new first1DB;
$sql  = 'SELECT
            sId,
            sApplicant,
            sLeaveId,
            sLeaveFromDateTime,
            sLeaveToDateTime,
            sAgentApprovalDateTime,
            sUnitApprovalDateTime,
            sManagerApprovalDateTime,
            sProcessing,
            sStatus,
            sCreatedAt
        FROM
            tStaffLeaveApply
        WHERE
            sId = :sId;';
$bind      = ['sId' => $sId];
$leaveData = $conn->one($sql, $bind);

if (empty($leaveData)) {
    http_response_code(400);
    exit('無法取得請假資料');
}

if (empty($leaveData['sAgentApprovalDateTime']) && empty($leaveData['sUnitApprovalDateTime']) && empty($leaveData['sManagerApprovalDateTime'])) {
    $sql  = 'UPDATE tStaffLeaveApply SET sStatus = "C", sProcessing="F" WHERE sId = :sId;';
    $bind = ['sId' => $sId];
    if ($conn->exeSql($sql, $bind)) {
        exit('請假申請已經取消了');
    }

    http_response_code(500);
    exit('請假申請取消失敗');
}

if (!empty($leaveData['sAgentApprovalDateTime']) || !empty($leaveData['sUnitApprovalDateTime']) || !empty($leaveData['sManagerApprovalDateTime'])) {
    http_response_code(400);
    exit('請假申請已經有人簽核了');
}

http_response_code(400);
exit('請假申請取消失敗');
