<?php
require_once dirname(dirname(__DIR__)) . '/sms/sms_function_v2.php';

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

$data['pid']    = empty($data['pid']) ? '' : $data['pid'];
$data['sid']    = empty($data['sid']) ? '' : $data['sid'];
$data['bid']    = empty($data['bid']) ? '' : $data['bid'];
$data['target'] = empty($data['target']) ? '' : $data['target'];
$data['tid']    = empty($data['tid']) ? '' : $data['tid'];
$data['ok']     = empty($data['ok']) ? 'n' : strtolower($data['ok']);
$data['realty'] = empty($data['realty']) ? '' : $data['realty'];
$data['arr']    = empty($data['arr']) ? '' : $data['arr'];
$data['stxt']   = empty($data['stxt']) ? '' : $data['stxt'];

if (!preg_match("/^[y|n]{1}$/", $data['ok'])) {
    $message = 'Invalid sms send mode(' . $data['ok'] . ')';

    file_put_contents($fh, date("Y-m-d H:i:s ") . print_r($message, true) . "\n" . print_r($data, true) . "\n", FILE_APPEND);
    exit($message);
}
##

$sms = new SMS_Gateway_V2();

// $response = $sms->send($data['pid'], $data['sid'], $data['bid'], $data['target'], $data['tid'], $data['ok'], $data['realty'], $data['arr'], $data['stxt']);
$response = $sms->send($data['pid'], $data['sid'], $data['bid'], $data['target'], $data['tid'], 'n', $data['realty'], $data['arr'], $data['stxt']);

file_put_contents($fh, date("Y-m-d H:i:s ") . 'Request: ' . print_r($data, true) . "\n" . 'Response: ' . print_r($response, true) . "\n", FILE_APPEND);
print_r($response);
