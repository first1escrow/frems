<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/staff.class.php';

use First1\V1\Staff\Staff;

$from = $_POST['from'];
$to   = $_POST['to'];

if (empty($from) || empty($to)) {
    http_response_code(400);
    exit('Invalid date range');
}

$from = date('Y-m-d', strtotime($from));
$to   = date('Y-m-d', strtotime($to));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
    http_response_code(400);
    exit('Invalid date format');
}

header('Content-Type: application/json');

$staff = new Staff;
$staff->getHolidayData($from . ' 00:00:00', $to . ' 23:59:59');
$holidayData = $staff->holidayData;
if (empty($holidayData)) {
    exit('[]');
}

$holiday = [];
foreach ($holidayData as $data) {
    $holiday[] = [
        'id'              => $data['hId'],
        'title'           => $data['hName'],
        'makeUpWorkday'   => $data['hFromDate'],
        'start'           => $data['hFromDate'],
        'end'             => $data['hToDate'],
        'backgroundColor' => ($data['hMakeUpWorkday'] == 'Y') ? '#dc3545' : '#28a745',
    ];
}

exit(json_encode($holiday, JSON_UNESCAPED_UNICODE));