<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once __DIR__ . '/leaveFunction.php';
require_once __DIR__ . '/overtimeFunction.php';

$case = getOvertimeData($sId);
if (empty($case)) {
    http_response_code(400);
    exit('無法取得欲審核紀錄');
}

if ($case['sProcessing'] == 'U') {
    $response = sendOvertimeApplyNotify($case, $sId, true);

    if (!empty($response)) {
        http_response_code(200);
        exit($response);
    }

    http_response_code(400);
    exit('審核訊息已發出失敗');
}

http_response_code(400);
exit('無法確認審核資訊');