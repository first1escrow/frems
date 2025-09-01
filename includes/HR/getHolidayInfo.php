<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staff.class.php';

$id = $_POST['id'];

if (empty($id) || !is_numeric($id)) {
    http_response_code(400);
    exit('Invalid date index');
}

header('Content-Type: application/json');

$conn = new First1DB;

$sql = 'SELECT hId, hName, hMakeUpWorkday, hFromDate, hToDate, hFromTime, hToTime FROM tHoliday WHERE hId = :id';
$rs  = $conn->one($sql, ['id' => $id]);
if (empty($rs)) {
    exit(json_encode([], JSON_UNESCAPED_UNICODE));
}

exit(json_encode($rs, JSON_UNESCAPED_UNICODE));