<?php
header('Content-Type: application/json');

require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$cId = $_POST['cId'];
if (!preg_match("/^\d{9}$/", $cId)) {
    exit(json_encode(['status' => 400, 'message' => 'Invalid format(C)']));
}

$checked = $_POST['checked'];
if (!in_array($checked, ['Y', 'N'])) {
    exit(json_encode(['status' => 400, 'message' => 'Invalid format(Y)']));
}

$conn = new first1DB;

$sql = 'UPDATE tLegalNotify SET lStatus = :checked WHERE lCertifiecId = :cId;';
$rs  = $conn->exeSql($sql, ['cId' => $cId, 'checked' => $checked]);

if (empty($rs)) {
    throw new Exception('Database Insert Failed!');
}

exit(json_encode(['status' => 200, 'message' => 'OK']));
