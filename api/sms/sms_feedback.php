<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/sms/sms_function_v2.php';
require_once dirname(dirname(__DIR__)) . '/notify/sendNotify.php';

//建立 log
$fh = dirname(dirname(__DIR__)) . '/log/api/sms';
if (!is_dir($fh)) {
    mkdir($fh, 0777, true);
}
$fh .= '/sms_queue_' . date("Ymd") . '.log';
##

//傳入參數檢核
$json = file_get_contents('php://input');
if (empty($json)) {
    $message = 'No Data';

    file_put_contents($fh, date("Y-m-d H:i:s ") . print_r($message, true) . "\n", FILE_APPEND);
    exit($message);
}

$data = json_decode($json, true);
if (empty($data) || !is_array($data)) {
    $message = 'Invalid data format';

    file_put_contents($fh, date("Y-m-d H:i:s ") . print_r($message, true) . "\n" . print_r($data, true) . "\n", FILE_APPEND);
    exit($message);
}

$member      = empty($data['member']) ? '' : $data['member'];
$uuid        = empty($data['uuid']) ? '' : $data['uuid'];
$batch       = empty($data['batch']) ? '' : $data['batch'];
$mobile_tel  = empty($data['mobile_tel']) ? '' : $data['mobile_tel'];
$mobile_name = empty($data['mobile_name']) ? '' : $data['mobile_name'];
$sms_txt     = empty($data['sms_txt']) ? '' : $data['sms_txt'];
$target      = empty($data['target']) ? '' : $data['target'];
$pid         = empty($data['pid']) ? '' : $data['pid'];
$tid         = empty($data['tid']) ? '' : $data['tid'];
##

$sms      = new SMS_Gateway_V2();
$response = $sms->sms_send($mobile_tel, $mobile_name, $sms_txt, $target, $pid, $tid);
// sleep(1);
// $response = 's';

file_put_contents($fh, date("Y-m-d H:i:s ") . 'Request: ' . print_r($data, true) . "\n" . 'Response: ' . print_r($response, true) . "\n", FILE_APPEND);

$notify = new SendNotify();

if ($response == 's') {
    $conn = new first1DB;
    $sql  = 'DELETE FROM `tSMSWaitSend` WHERE `uuid` = :uuid;';

    if ($conn->exeSql($sql, ['uuid' => $uuid])) {
        $sql = 'SELECT `uuid` FROM `tSMSWaitSend` WHERE `sBatch` = :batch;';
        $rs  = $conn->one($sql, ['batch' => $batch]);

        $conn = null;
        unset($conn);

        if (empty($rs)) {
            $message = "回饋金簡訊已全部發出完成\r\nuuid={$uuid}\r\nbatch={$batch}";
        } else {
            exit; //尚有待發出的簡訊
        }
    } else {
        $conn = null;
        unset($conn);

        $message = "回饋金簡訊異常\r\nuuid={$uuid}\r\nbatch={$batch}\r\nmobile={$mobile_tel}";
    }
} else {
    $message = "回饋金簡訊異常\r\nuuid={$uuid}\r\nbatch={$batch}\r\ntarget={$target}\r\nname={$mobile_name}\r\nmobile={$mobile_tel}\r\n";
}

if (!empty($notify->getTargetToken($member))) {
    $notify->send($member, $message);
}
