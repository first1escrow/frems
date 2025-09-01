<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(dirname(__DIR__))) . '/lib/rc4/crypt.php';

//加密文字
function enCrypt($str, $seed = 'firstfeedSms')
{
    $encode = '';
    $rc     = new Crypt_RC4;
    $rc->setKey($seed);
    $encode = $rc->encrypt($str);

    return $encode;
}

$id = $_POST['id'];

if (empty($id) || !preg_match("/^\d+$/", $id)) {
    http_response_code(400);
    exit('無法確認審核申請資訊');
}

$conn = new first1DB;

$sql   = 'SELECT sStaffId, (SELECT pName FROM tPeopleInfo WHERE pId = a.sStaffId) as staffName, sApplyDate, sApplyType FROM tStaffCheckInApply AS a WHERE sId = :id;';
$apply = $conn->one($sql, ['id' => $id]);

if (empty($apply)) {
    http_response_code(400);
    exit('無法確認審核申請資訊');
}

//發送通知
$code = json_encode([
    'caseId' => $id,
    'ts'     => time(),
], JSON_UNESCAPED_UNICODE);

$code = enCrypt($code);

$url = 'https://www.first1.com.tw/line/confirm/checkInConfirm.php?code=' . $code;
exit($url);
