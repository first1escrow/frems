<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once __DIR__ . '/leaveFunction.php';

$case = getLeaveData($sId);
if (empty($case)) {
    http_response_code(400);
    exit('無法取得欲審核紀錄');
}

if (in_array($case['sProcessing'], ['A', 'U', 'M'])) {
    $response = '';

    if ($case['sProcessing'] == 'A') {
        $response = sendAgentApply($case, $sId, true, '');
    }

    if ($case['sProcessing'] == 'U') {
        $response = sendUnitApply($case, $sId, true, '');

    }

    if ($case['sProcessing'] == 'M') {
        $response = sendManagerApply($case, $sId, true, '');
    }

    if (!empty($response)) {
        http_response_code(200);
        // exit('審核訊息已發出，請點擊連結審核回覆');
        exit($response);
    }

    http_response_code(400);
    exit('審核訊息已發出失敗');
}

http_response_code(400);
exit('無法確認審核資訊');
