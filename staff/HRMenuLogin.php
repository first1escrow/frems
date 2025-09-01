<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

unset($_SESSION['HR_lock']);

$conn = new first1DB;

$alert = '';

if (! empty($_POST['access-code'])) {
    $access_code = md5($_POST['access-code']);

    $sql  = 'SELECT pAccessCode FROM tPeopleInfo WHERE pId = :pId;';
    $bind = ['pId' => $_SESSION['member_id']];
    $rs   = $conn->one($sql, $bind);

    $alert = 'alert("存取碼錯誤");';
    if ($access_code == $rs['pAccessCode']) {
        $alert = '';
        if ($_SESSION['REQUEST_URI']) {
            $_SESSION['HR_lock'] = $_SESSION['member_id'];
            header('Location: ' . $_SESSION['REQUEST_URI']);
            exit;
        }
    }
}

$smarty->assign('alert', $alert);
$smarty->display('HRMenuLogin.inc.tpl', '', 'staff');
