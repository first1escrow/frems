<?php
require_once dirname(__DIR__) . '/session_check.php';

//取得時間驗證
function getAuth($cId)
{
    $key = 'first1Escrow2TimeAuthKey3';

    $ts   = time();
    $time = floor($ts / 300);

    return md5($key . '|' . $cId . '|' . $time);
}

// 取得保證號碼
$cCertifiedId = $_GET['cCertifiedId'];
$target       = empty($_GET['target']) ? '' : $_GET['target'];

if (empty($cCertifiedId) || !preg_match("/^[0-9]{9}\$/", $cCertifiedId)) {
    http_response_code(400);
    exit('Invalid cCertifiedId');
}

if (empty($target) || !in_array($target, ['buyer', 'owner'])) {
    http_response_code(400);
    exit('Invalid target');
}

$url = 'http://10.10.1.199/case/' . $cCertifiedId . '/checklist/' . getAuth($cCertifiedId) . '?target=' . $target;
header('Location: ' . $url);
exit;
