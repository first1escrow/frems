<?php
/**
 * 顧代書系統串接程式
 */
require_once dirname(__DIR__) . '/session_check.php';

$cId = $_POST['cId'];

if (empty($cId) || ! preg_match("/^\d{9}$/", $cId)) {
    http_response_code(400);
    exit;
}

header("Content-Type:text/csv;");
header('Content-Disposition: attachment; filename="ku_' . time() . '.csv"');

$url      = 'http://10.10.1.199/case/' . $cId . '/ku/export/' . getAuth($cId);
$response = file_get_contents($url);
echo $response;

exit;

//取得時間驗證
function getAuth($cId)
{
    $key = 'first1Escrow2TimeAuthKey3';

    $ts   = time();
    $time = floor($ts / 300);

    // 如果時間不同步，可以嘗試前後幾個時間段
    $possibleTimes = [
        $time - 2, // 10 分鐘前
        $time - 1, // 5 分鐘前
        $time,     // 當前
        $time + 1, // 5 分鐘後
        $time + 2, // 10 分鐘後
    ];

    // 先嘗試當前時間
    return md5($key . '|' . $cId . '|' . $time);
}
