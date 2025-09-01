<?php
if (session_status() != 2) {
    session_start();
}

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';

unset($_SESSION['attendance']);

$conn = new first1DB;

$alert = '';

if (empty($_SESSION['attendance_allowed'])) {
    header('Location: ' . $_SESSION['REQUEST_URI']);
    exit;
}

if (! empty($_POST['access-code'])) {
    $access_code = md5($_POST['access-code']);

    $sql  = 'SELECT pId, pAccessCode FROM tPeopleInfo WHERE pAccessCode = :access AND pId IN (' . implode(',', $_SESSION['attendance_allowed']) . ') AND pJob = 1;';
    $bind = ['access' => $access_code];
    $rs   = $conn->one($sql, $bind);

    if (empty($rs)) {
        $alert = 'alert("無權限!!");';
    }

    if (! empty($rs)) {
        if (($access_code == $rs['pAccessCode']) && in_array($rs['pId'], $_SESSION['attendance_allowed'])) {
            $alert                  = '';
            $_SESSION['attendance'] = $rs['pId'];

            header('Location: /HR/staffAttendance.php');
            exit;
        }
    }
}

$smarty->assign('alert', $alert);
$smarty->display('attendanceLogin.inc.tpl', '', 'HR');
