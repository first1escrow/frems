<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once __DIR__ . '/leaveFunction.php';

$sId = empty($_POST['id']) ? '' : $_POST['id'];
if (empty($sId)) {
    http_response_code(400);
    exit('無法取得請假資料');
}

//取得請假資料
$leaveData = getLeaveData($sId);
if (empty($leaveData)) {
    http_response_code(400);
    exit('無法取得請假資料');
}

// if ($leaveData['sLeaveFromDateTime'] <= date('Y-m-d H:i:s')) {
//     http_response_code(400);
//     exit('請假已經開始，無法撤銷');
// }

$conn = new first1DB;

$status = !empty($leaveData['sAgentApprovalDateTime']) ? 'A' : 'U'; //代理人簽核過就通知代理人, 否則通知部門主管
$status = ($leaveData['sApplicant'] == '3') ? 'M' : $status; //副總申請就通知總經理

$sql = 'INSERT INTO
            tStaffLeaveApplyRevoke
            (
                sLeaveApplyId,
                sProcessing,
                sStatus,
                sCreatedAt
            ) VALUES (
                :sLeaveApplyId,
                :sProcessing,
                :sStatus,
                :sCreatedAt
            );';

$bind = [
    'sLeaveApplyId' => $leaveData['sId'],
    'sProcessing'   => $status,
    'sStatus'       => "N",
    'sCreatedAt'    => date('Y-m-d H:i:s'),
];

if ($conn->exeSql($sql, $bind)) {
    $insertId = $leaveData['sId'];

    if (in_array($status, ['A', 'U', 'M'])) {
        $response = '';

        if ($status == 'A') {
            $response = sendAgentApply($leaveData, $insertId, false, '撤銷');
        }

        if ($status == 'U') {
            $response = sendUnitApply($leaveData, $insertId, false, '撤銷');
        }

        if ($status == 'M') {
            $response = sendManagerApply($leaveData, $insertId, false, '撤銷');
        }

        if (!empty($response)) {
            http_response_code(200);
            exit('撤銷申請已提出(' . $status . ')');
        }
    }

    http_response_code(400);
    exit('無法確認簽核人員');
}

http_response_code(400);
exit('無法送出申請');