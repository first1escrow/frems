<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
    http_response_code(400);
    exit('無法取得解鎖案件資料(I)');
}

if ($_SESSION['member_pDep'] != '6') {
    http_response_code(400);
    exit('您無權限解鎖案件');
}

$conn = new first1DB;

$sql       = 'SELECT tId FROM tBankTrans WHERE tId = :tId;';
$bind      = ['tId' => $_POST['id']];
$transData = $conn->one($sql, $bind);
if (empty($transData)) {
    http_response_code(400);
    exit('無法取得解鎖案件資料(D)');
}

$datetime = date('Y-m-d H:i:s');
$json     = json_encode([
    'unlock' => [
        'datetime' => $datetime,
        'staff'    => $_SESSION['member_name'],
        'staffId'  => $_SESSION['member_id'],
    ],
], JSON_UNESCAPED_UNICODE);

$sql  = 'UPDATE tBankTrans SET tLegalAllow = "2", tLegalAllowDetail = :json WHERE tId = :tId;';
$bind = ['tId' => $_POST['id'], 'json' => $json];
if ($conn->exeSql($sql, $bind)) {
    exit('案件已解鎖');
}

exit('案件解鎖失敗');
