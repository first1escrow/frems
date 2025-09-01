<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$reviewType = $_POST['reviewType'];
$sId        = $_POST['sId'];

if (empty($reviewType) || !in_array($reviewType, ['checkIn', 'leave', 'leaveRevoke', 'overtime', 'overtimeRevoke'])) {
    http_response_code(400);
    exit('無法確認審核資訊');
}

if (empty($sId) || !is_numeric($sId)) {
    http_response_code(400);
    exit('無法取得欲審核紀錄');
}

$conn = new first1DB;

if ($reviewType == 'checkIn') {
    require_once __DIR__ . '/checkInNotify.php';
    exit;
}

if ($reviewType == 'leave') {
    require_once __DIR__ . '/leaveNotify.php';
    exit;
}

if ($reviewType == 'leaveRevoke') {
    require_once __DIR__ . '/leaveRevokeNotify.php';
    exit;
}

if ($reviewType == 'overtime') {
    require_once __DIR__ . '/overtimeNotify.php';
    exit;
}

if ($reviewType == 'overtimeRevoke') {
    require_once __DIR__ . '/overtimeRevokeNotify.php';
    exit;
}

http_response_code(400);
exit('無法確認審核資訊');