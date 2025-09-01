<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

if (empty($_POST['id'])) {
    http_response_code(400);
    exit('Missing required parameter: id');
}

$id = $_POST['id'];

if (! is_numeric($id)) {
    http_response_code(400);
    exit('Invalid ID format');
}

$log  = new TraceLog();
$conn = new First1DB();

$sql      = 'SELECT sId, sBankMain, sBankBranch, sBankAccountNo, sBankAccountName, sUnUsed, sScrivener FROM tScrivenerBank WHERE sId = :id;';
$bankData = $conn->one($sql, ['id' => $id]);

if (empty($bankData)) {
    http_response_code(404);
    exit('Bank data not found for the given ID');
}

$log->log($_SESSION['member_id'], '(' . json_encode($bankData, JSON_UNESCAPED_UNICODE) . ')', '刪除地政士解匯銀行資料（ID：' . $id . '）', 'DELETE');

$sql = 'DELETE FROM tScrivenerBank WHERE sId = :id;';
if ($conn->exeSql($sql, ['id' => $id])) {
    exit('ok');
}

http_response_code(500);
exit('Failed to delete bank data');
