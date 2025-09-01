<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

if (empty($_SESSION['HR_lock']) || ($_SESSION['HR_lock'] != $_SESSION['member_id'])) {
    $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];

    require_once __DIR__ . '/HRMenuLogin.php';
    exit;
}

unset($_SESSION['HR_lock']);
