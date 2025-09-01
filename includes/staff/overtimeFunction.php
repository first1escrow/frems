<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staffNotify.class.php';
require_once dirname(dirname(__DIR__)) . '/includes/staff/leaveFunction.php';

use First1\V1\Staff\StaffNotify;

//取得加班紀錄相關資訊
function getOvertimeData($id)
{
    $conn = new first1DB;
    $sql  = 'SELECT
                sId,
                sApplicant,
                (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as applicant,
                sOvertimeFromDateTime,
                sOvertimeToDateTime,
                sTotalHoursOfOvertime,
                sUnitApproval,
                sUnitApprovalDateTime,
                sProcessing,
                sStatus,
                sCreatedAt
            FROM
                tStaffOvertimeApply as a
            WHERE
                sId = :sId;';
    $bind = ['sId' => $id];
    return $conn->one($sql, $bind);
}

//取得撤銷請加班紀錄相關資訊
function getOvertimeRevokeData($sId)
{
    $conn = new first1DB;
    $sql  = 'SELECT sOvertimeApplyId, sUnitApprovalDateTime, sProcessing, sStatus FROM tStaffOvertimeApplyRevoke WHERE sId = :sId AND sStatus = "N";';
    $rs   = $conn->one($sql, ['sId' => $sId]);

    if (empty($rs['sOvertimeApplyId'])) {
        return false;
    }

    $data = getOvertimeData($rs['sOvertimeApplyId']);
    return empty($data) ? false : array_merge($data, ['revoke' => $rs]);
}

//加班、撤銷加班紀錄審核發送通知
function sendOvertimeApplyNotify($case, $insertId, $html = false, $revoke = '')
{
    if (empty($case) || empty($case['sUnitApproval'])) {
        $message = '無法取得' . $revoke . '加班申請資料';

        if (empty($html)) {
            http_response_code(400);
            exit($message);
        }

        breakOut($message);
    }

    $code = json_encode([
        'caseId' => $insertId,
        'target' => 'U',
        'revoke' => $revoke,
        'ts'     => time(),
    ], JSON_UNESCAPED_UNICODE);

    $code     = enCrypt($code);
    $endPoint = empty($revoke) ? 'overtimeConfirm.php' : 'overtimeRevokeConfirm.php';

    $targetId = $case['sUnitApproval'];

    if (empty($html) || ! empty($case['apply'])) {
        $message = '您有一筆新的' . $revoke . '加班申請、請確認。' . "\n";
        $message .= '申請人：' . $case['applicant'] . "\n";
        $message .= '日期：' . substr($case['sOvertimeFromDateTime'], 0, 10) . '~' . substr($case['sOvertimeToDateTime'], 0, 10) . "\n";
        $message .= WWWHOST . '/line/confirm/' . $endPoint . '?code=' . $code;

        $response = StaffNotify::send($targetId, $message);
    }

    $message = '您有一筆新的' . $revoke . '加班申請、請確認。<br>' . "\n";
    $message .= '申請人：' . $case['applicant'] . "<br>\n";
    $message .= '日期：' . substr($case['sOvertimeFromDateTime'], 0, 10) . '~' . substr($case['sOvertimeToDateTime'], 0, 10) . "<br>\n";
    $message .= '<a href="' . WWWHOST . '/line/confirm/' . $endPoint . '?code=' . $code . '" target="_blank">審核連結</a>';

    return empty($html) ? $response : $message;
}
