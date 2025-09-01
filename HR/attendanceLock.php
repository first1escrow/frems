<?php
if (session_status() != 2) {
    session_start();
}

require_once dirname(__DIR__) . '/first1DB.php';

$allowed = [13, 129]; // Allowed user IDs for attendance access

if (empty($_SESSION['attendance']) || ! in_array($_SESSION['attendance'], $allowed)) {
    $_SESSION['attendance_allowed'] = $allowed;

    require_once __DIR__ . '/attendanceLogin.php';
    exit;
}
// $attendance = $_SESSION['attendance'];
// unset($_SESSION['attendance'], $_SESSION['attendance_allowed']);
