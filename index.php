<?php
require_once __DIR__ . '/configs/config.class.php';
require_once __DIR__ . '/class/SmartyMain.class.php';
require_once __DIR__ . '/class/member.class.php';
require_once __DIR__ . '/first1DB.php';

if (session_status() != 2) {
    session_start();
}

if (! empty($_SESSION['member_job']) && ($_SESSION['member_job'] == 1) && ! empty($_SESSION['member_id']) && ! empty($_SESSION['member_pDep'])) {
    //
    $conn = new first1DB;
    $sql  = 'SELECT pId FROM `tPeopleInfo` WHERE `pId` = :pid AND `pJob` = 1;';
    $rs   = $conn->one($sql, ['pid' => $_SESSION['member_id']]);

    $conn = $sql = null;
    unset($conn, $sql);
    ##

    if (! empty($rs)) {
        $rs = null;unset($rs);

        if ($_SESSION['member_pDep'] == 7) {
            header('Location: /sales/salesTracking.php');
        } else {
            header('Location: /inquire/buyerownerinquery.php');
        }

        exit;
    }

    $rs = null;unset($rs);
}

session_destroy();
unset($_COOKIE['member_id'], $_COOKIE['member_pDep'], $_COOKIE['member_session']);

setcookie('member_id', '', -1, '/');
setcookie('member_pDep', '', -1, '/');
setcookie('member_session', '', -1, '/');

$remembered = '';
if (isset($_COOKIE['act']) && isset($_COOKIE['psd'])) {
    $remembered = ' checked="checked"';
}

$_act = empty($_COOKIE['act']) ? '' : $_COOKIE['act'];
$_psd = empty($_COOKIE['psd']) ? '' : $_COOKIE['psd'];

$smarty->assign('act', $_act);
$smarty->assign('psd', $_psd);
$smarty->assign('remembered', $remembered);

$_act = $_psd = $remembered = null;
unset($_act, $_psd, $remembered);

$smarty->display('index.inc.tpl', '', '');
