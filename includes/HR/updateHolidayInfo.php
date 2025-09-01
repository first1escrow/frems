<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staff.class.php';

$hId = $_POST['hId'];
if (empty($hId) || !is_numeric($hId)) {
    http_response_code(400);
    exit('Invalid date input');
}

$conn = new First1DB;

if (!empty($_POST['action']) && $_POST['action'] == 'delete') {
    $sql = 'DELETE FROM tHoliday WHERE hId = :id';
    if ($conn->exeSql($sql, ['id' => $hId])) {
        exit('OK');
    }

    http_response_code(400);
    exit('Failed to delete date');
}

$hName          = $_POST['hName'];
$hMakeUpWorkday = $_POST['hMakeUpWorkday'];
$hFromDate      = $_POST['hFromDate'];
$hFromTime      = $_POST['hFromTime'];
$hToTime        = $_POST['hToTime'];

if (empty($hName) || empty($hMakeUpWorkday) || empty($hFromDate) || empty($hFromTime) || empty($hToTime)) {
    http_response_code(400);
    exit('Invalid date input');
}

$sql = 'SELECT hId FROM tHoliday WHERE hId = :id';
$rs  = $conn->one($sql, ['id' => $hId]);
if (empty($rs)) {
    http_response_code(400);
    exit('No date founded');
}

$hToDate = $hFromDate;

$from_ts = strtotime($hFromDate . ' ' . $hFromTime);
$to_ts   = strtotime($hToDate . ' ' . $hToTime);

$sql = 'UPDATE tHoliday SET hName = :name, hMakeUpWorkday = :makeUpWorkday, hFromDate = :fromDate, hFromTime = :fromTime, hToDate = :toDate, hToTime = :toTime, hFromTimestamp = :from_ts, hToTimestamp = :to_ts WHERE hId = :id';
if ($conn->exeSql($sql, [
    'name'          => $hName,
    'makeUpWorkday' => $hMakeUpWorkday,
    'fromDate'      => $hFromDate,
    'fromTime'      => $hFromTime,
    'toDate'        => $hToDate,
    'toTime'        => $hToTime,
    'from_ts'       => $from_ts,
    'to_ts'         => $to_ts,
    'id'            => $hId,
])) {
    exit('OK');
}

http_response_code(400);
exit('Failed to update date');