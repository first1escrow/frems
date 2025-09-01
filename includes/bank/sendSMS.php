<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/sms/sms_function_manually.php';

$uids = $_POST['sms-uid'];
if (empty($uids) || !is_array($uids)) {
    http_response_code(400);
    exit('Invalid uid');
}

$conn = new first1DB;

$sql = 'SELECT
            a.bUid,
            a.bCertifiedId,
            a.bDate,
            a.bKind,
            a.bAccountName,
            a.bMoney,
            a.bExport_time,
            b.fTargetId,
            c.sMobile
        FROM
            tBankTransRelay AS a
        JOIN
            tFeedBackMoneyPayByCase AS b ON a.bCertifiedId = b.fCertifiedId
        JOIN
            tScrivenerFeedSms AS c ON b.fTargetId = c.sScrivener
        WHERE
            a.bUid IN ("' . implode('","', $uids) . '")
            AND b.fTarget = "S";';
$rs = $conn->all($sql);

$total = count($rs);
$sent  = 0;

$sms = new SMS_Gateway;
foreach ($rs as $row) {
    $date          = $row['bExport_time'];
    $date          = date('m/d', strtotime($date));
    $mobile        = $row['sMobile'];
    $mobile_target = $row['bAccountName'];
    $sender        = empty($_SESSION['member_name']) ? '' : $_SESSION['member_name'];
    $txt           = '第一建經通知：保證號碼' . $row['bCertifiedId'] . '回饋金:' . $row['bMoney'] . '已於' . $date . '匯入台端指定帳戶，敬請確認查收，感謝您的支持!';
    $ok            = 'y'; // 是否發送簡訊 (y/n)
    $pid           = '00000' . $row['bCertifiedId'];

    $response = $sms->manual_send($mobile, $txt, $ok, $sender, '隨案回饋金', $mobile_target, $pid);
    if ($response == '系統開始發送簡訊') {
        $sent++;

        $sql = 'UPDATE tBankTransRelay SET bSms = NOW() WHERE bUid = :uid;';
        $conn->exeSql($sql, ['uid' => $row['bUid']]);
    }

    unset($date, $mobile, $mobile_target, $sender, $txt, $ok, $response, $pid);
    $date = $mobile = $mobile_target = $sender = $txt = $response = $pid = null;
}

if ($sent == $total) {
    exit('簡訊已發送');
}

if ($sent == 0) {
    exit('簡訊發送失敗');
}

exit('簡訊發送異常，詳情請查詢簡訊明細');