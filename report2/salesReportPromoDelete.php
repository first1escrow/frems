<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

$uid = $_POST['id'];
if (!preg_match("/^\w{8}\-\w{4}\-\w{4}\-\w{4}\-\w{12}$/i", $uid)) {
    http_response_code(400);
    exit;
}

$conn = new first1DB;

$sql = 'DELETE FROM tSalesReportPromo WHERE sId = :sId;';
$conn->exeSql($sql, ['sId' => $uid]);

exit;
