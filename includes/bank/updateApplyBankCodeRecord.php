<?php
require_once dirname(dirname(__DIR__)) . '/configs/contract.setting.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

$log = new TraceLog();
$log->log($_SESSION['member_id'], print_r($_POST, true), '變更地政士合約申請狀態', 'update');

$id     = (empty($_POST['id']) || ! is_numeric($_POST['id'])) ? null : $_POST['id'];
$action = empty($_POST['action']) ? null : $_POST['action'];

if (empty($id)) {
    http_response_code(400);
    exit('Invalid parameters');
}

$conn = new first1DB;

if (empty($action) || ! in_array($action, [2, 3, 5, 6])) {
    http_response_code(400);
    exit('Invalid action');
}

$sql = 'UPDATE tApplyBankCode SET aProcessed = :status, aUpdatedAt = NOW() WHERE aId = :id;';
if ($conn->exeSql($sql, ['status' => $action, 'id' => $id])) {
    exit('success');
} else {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Failed to update record']));
}
