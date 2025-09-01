<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staff.class.php';
require_once dirname(dirname(__DIR__)) . '/class/staffNotify.class.php';
require_once dirname(dirname(dirname(__DIR__))) . '/lib/rc4/crypt.php';
require_once __DIR__ . '/leaveConfig.php';

use First1\V1\Staff\StaffNotify;

//狀態顯示
function breakOut($text)
{
    echo '
    <!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>休假申請</title>
        <!------------------------- RWD open ------------------------->
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    </head>
    <body>
        <div style="text-align:center;">
            <p><h3>' . $text . '</h3></p>
        </div>
    </body>
    </html>
    ';
    exit;
}

//加密文字
function enCrypt($str, $seed = 'firstfeedSms')
{
    $encode = '';
    $rc     = new Crypt_RC4;
    $rc->setKey($seed);
    $encode = $rc->encrypt($str);

    return $encode;
}

//取得請假紀錄相關資訊
function getLeaveData($id)
{
    $conn = new first1DB;
    $sql  = 'SELECT
                sId,
                sApplicant,
                (SELECT pName FROM tPeopleInfo WHERE pId = a.sApplicant) as applicant,
                (SELECT CASE WHEN sMemo = "" OR sMemo IS NULL THEN sLeaveName ELSE sMemo END AS leaveName FROM tStaffLeaveType WHERE sId = a.sLeaveId) as leaveName,
                sLeaveFromDateTime,
                sLeaveToDateTime,
                sTotalHoursOfLeave,
                sLeaveAttachment,
                sAgentApproval,
                sAgentApprovalDateTime,
                sUnitApproval,
                sUnitApprovalDateTime,
                sManagerApproval,
                sManagerApprovalDateTime,
                sProcessing,
                sStatus,
                sCreatedAt
            FROM
                tStaffLeaveApply as a
            WHERE
                sId = :sId;';
    $bind = ['sId' => $id];
    return $conn->one($sql, $bind);
}

//取得撤銷請假紀錄相關資訊
function getLeaveRevokeData($sId)
{
    $conn = new first1DB;
    $sql  = 'SELECT sLeaveApplyId, sAgentApprovalDateTime, sUnitApprovalDateTime, sManagerApprovalDateTime, sProcessing, sStatus FROM tStaffLeaveApplyRevoke WHERE sId = :sId AND sStatus = "N";';
    $rs   = $conn->one($sql, ['sId' => $sId]);

    if (empty($rs['sLeaveApplyId'])) {
        return false;
    }

    $data = getLeaveData($rs['sLeaveApplyId']);
    return empty($data) ? false : array_merge($data, ['revoke' => $rs]);
}

//通知代理人
function sendAgentApply(&$case, $insertId, $html = false, $revoke = '')
{
    if (empty($case) || empty($case['sAgentApproval'])) {
        $message = '無法取得' . $revoke . '請假資料';

        if (empty($html)) {
            http_response_code(400);
            exit($message);
        }

        breakOut($message);
    }

    $staffId = $case['sAgentApproval'];
    return notifyApproval($case, $insertId, 'A', $staffId, $html, $revoke);
}

//通知部門主管
function sendUnitApply(&$case, $insertId, $html = false, $revoke = '')
{
    if (empty($case) || empty($case['sUnitApproval'])) {
        $message = '無法取得' . $revoke . '請假資料';

        if (empty($html)) {
            http_response_code(400);
            exit($message);
        }

        breakOut($message);
    }

    $staffId = $case['sUnitApproval'];
    return notifyApproval($case, $insertId, 'U', $staffId, $html, $revoke);
}

//通知總經理
function sendManagerApply(&$case, $insertId, $html = false, $revoke = '')
{
    if (empty($case) || empty($case['sManagerApproval'])) {
        $message = '無法取得' . $revoke . '請假資料';

        if (empty($html)) {
            http_response_code(400);
            exit($message);
        }

        breakOut($message);
    }

    $staffId = $case['sManagerApproval'];
    return notifyApproval($case, $insertId, 'M', $staffId, $html, $revoke);
}

//發送通知
function notifyApproval(&$case, $insertId, $target, $targetId, $html = false, $revoke = '')
{
    $code = json_encode([
        'caseId' => $insertId,
        'target' => $target,
        'revoke' => $revoke,
        'ts'     => time(),
    ], JSON_UNESCAPED_UNICODE);

    $code     = enCrypt($code);
    $endPoint = empty($revoke) ? 'leaveConfirm.php' : 'leaveRevokeConfirm.php';

    if (empty($html) || ! empty($case['apply'])) {
        $message = '您有一筆新的' . $revoke . '請假申請、請確認(' . $target . ')。' . "\n";
        $message .= '請假人：' . $case['applicant'] . "\n";
        $message .= '假別：' . $case['leaveName'] . "\n";
        $message .= '日期：' . substr($case['sLeaveFromDateTime'], 0, 10) . '~' . substr($case['sLeaveToDateTime'], 0, 10) . "\n";
        $message .= WWWHOST . '/line/confirm/' . $endPoint . '?code=' . $code;

        $response = StaffNotify::send($targetId, $message);
    }

    $message = '您有一筆新的' . $revoke . '請假申請、請確認(' . $target . ')。<br>' . "\n";
    $message .= '請假人：' . $case['applicant'] . "<br>\n";
    $message .= '假別：' . $case['leaveName'] . "<br>\n";
    $message .= '日期：' . substr($case['sLeaveFromDateTime'], 0, 10) . '~' . substr($case['sLeaveToDateTime'], 0, 10) . "<br>\n";
    $message .= '<a href="' . WWWHOST . '/line/confirm/' . $endPoint . '?code=' . $code . '" target="_blank">審核連結</a>';

    return empty($html) ? $response : $message;
}

//取得主管id
function getSupervisor($staffId)
{
    $conn = new first1DB;
    $sql  = 'SELECT sStaffId FROM tSupervisor AS a JOIN tPeopleInfo AS b ON a.sDepartment = b.pDep WHERE a.sStatus = "Y" AND b.pId = :staff;';
    $bind = ['staff' => $staffId];
    $rs   = $conn->one($sql, $bind);

    return empty($rs['sStaffId']) ? null : $rs['sStaffId'];
}
