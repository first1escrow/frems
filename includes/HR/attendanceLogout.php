<?php
require_once dirname(dirname(__DIR__)) . '/HR/attendanceLock.php';

unset($_SESSION['attendance'], $_SESSION['attendance_allowed']);

header('Content-Type: application/json');
exit(json_encode(['success' => true, 'message' => '已登出']));
