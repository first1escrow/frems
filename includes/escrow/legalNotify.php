<?php
header('Content-Type: application/json');

require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$cId = $_POST['cId'];
if (!preg_match("/^\d{9}$/", $cId)) {
    exit(json_encode(['status' => 400, 'message' => 'Invalid format(C)']));
}

$item = $_POST['item'];
if (!preg_match("/^\d+$/", $item)) {
    exit(json_encode(['status' => 400, 'message' => 'Invalid format(I)']));
}

$date = $_POST['date'];
if (!preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $date)) {
    exit(json_encode(['status' => 400, 'message' => 'Invalid format(D)']));
}

$remark = $_POST['remark'];
$remark = trim($remark);

$conn = new first1DB;

$sql = 'INSERT INTO tLegalNotify (lCertifiecId, lItem, lDate, lRemark, lCreated_at) VALUES (:cid, :item, :date, :remark, NOW()) ON DUPLICATE KEY UPDATE lItem = :item, lDate = :date, lRemark = :remark;';
$rs  = $conn->exeSql($sql, [
    'cid'    => $cId,
    'item'   => $item,
    'date'   => $date,
    'remark' => $remark,
]);

if (empty($rs)) {
    throw new Exception('Database Insert Failed!');
}

exit(json_encode(['status' => 200, 'message' => 'OK']));
