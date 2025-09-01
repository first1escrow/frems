<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staff.class.php';

$conn = new First1DB;

$hName          = $_POST['hName'];
$hMakeUpWorkday = $_POST['hMakeUpWorkday'];
$hFromDate      = $_POST['hFromDate'];
$hFromTime      = $_POST['hFromTime'];
$hToTime        = $_POST['hToTime'];

if (empty($hName) || empty($hMakeUpWorkday) || empty($hFromDate) || empty($hFromTime) || empty($hToTime)) {
    http_response_code(400);
    exit('Invalid date input');
}

$hToDate = $hFromDate;

$from_ts = strtotime($hFromDate . ' ' . $hFromTime);
$to_ts   = strtotime($hToDate . ' ' . $hToTime);

$sql = 'INSERT INTO tHoliday (hName, hMakeUpWorkday, hFromDate, hFromTime, hToDate, hToTime, hFromTimestamp, hToTimestamp, hCreatedAt) VALUES (:name, :makeUpWorkday, :fromDate, :fromTime, :toDate, :toTime, :from_ts, :to_ts, NOW())';
if ($conn->exeSql($sql, [
    'name'          => $hName,
    'makeUpWorkday' => $hMakeUpWorkday,
    'fromDate'      => $hFromDate,
    'fromTime'      => $hFromTime,
    'toDate'        => $hToDate,
    'toTime'        => $hToTime,
    'from_ts'       => $from_ts,
    'to_ts'         => $to_ts,
])) {
    exit('OK');
}

http_response_code(400);
exit('Failed to add date');