<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once __DIR__ . '/leaveFunction.php';
require_once __DIR__ . '/overtimeFunction.php';

$sId = empty($_POST['id']) ? '' : $_POST['id'];
if (empty($sId)) {
    http_response_code(400);
    exit('無法取得加班資料');
}

//取得加班資料
$overtimeData = getOvertimeData($sId);
if (empty($overtimeData)) {
    http_response_code(400);
    exit('無法取得加班資料');
}

$conn = new first1DB;

$sql = 'INSERT INTO
            tStaffOvertimeApplyRevoke
            (
                sOvertimeApplyId,
                sProcessing,
                sStatus,
                sCreatedAt
            ) VALUES (
                :sOvertimeApplyId,
                :sProcessing,
                :sStatus,
                :sCreatedAt
            );';

$bind = [
    'sOvertimeApplyId' => $overtimeData['sId'],
    'sProcessing'      => 'U',
    'sStatus'          => 'N',
    'sCreatedAt'       => date('Y-m-d H:i:s'),
];

if ($conn->exeSql($sql, $bind)) {
    $insertId = $overtimeData['sId'];
    $response = sendOvertimeApplyNotify($overtimeData, $insertId, false, '撤銷');

    http_response_code(200);
    exit('撤銷申請已提出');
}

http_response_code(400);
exit('無法送出申請');