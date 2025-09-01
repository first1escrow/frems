<?php
require_once __DIR__ . '/first1DB.php';

if (session_status() != 2) {
    session_start();
}

//20220721 若有 cookie 則讀取後從資料庫更新 pJob 狀態後，重新寫入 $_SESSION
$_conn = new first1DB;

if (empty($_SESSION['member_id']) && !empty($_COOKIE['member_session'])) {
    $_sessions = json_decode($_COOKIE['member_session'], true);

    if (!empty($_sessions)) {
        $_sessions['member_id'] = empty($_sessions['member_id']) ? '' : $_sessions['member_id'];

        $_sql = 'SELECT pJob FROM tPeopleInfo WHERE pId = :pid;';
        $_rs  = $_conn->one($_sql, ['pid' => $_sessions['member_id']]);

        if (!empty($_rs['pJob'])) {
            $_sessions['member_job'] = $_rs['pJob'];
        }

        foreach ($_sessions as $_k => $_v) {
            $_SESSION[$_k] = $_v;
        }

        $_SESSION['member_id']   = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
        $_SESSION['member_pDep'] = empty($_SESSION['member_pDep']) ? '' : $_SESSION['member_pDep'];

        setcookie('member_id', $_SESSION['member_id'], 0, "/");
        setcookie('member_pDep', $_SESSION['member_pDep'], 0, "/");
        setcookie('member_session', json_encode($_SESSION), 0, "/");

        $_sql = $_rs = $_k = $_v = null;
        unset($_sql, $_rs, $_k, $_v);
    }

    $_sessions = null;unset($_sessions);
}

$_member_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
$_sql       = 'SELECT pJob FROM tPeopleInfo WHERE pId = :pid AND pJob = 1;';
$_rs        = $_conn->one($_sql, ['pid' => $_member_id]);

if (empty($_rs)) {
    session_destroy();
    unset($_COOKIE['member_id'], $_COOKIE['member_pDep'], $_COOKIE['member_session']);

    setcookie('member_id', null, -1, '/');
    setcookie('member_pDep', null, -1, '/');
    setcookie('member_session', null, -1, '/');

    header('Location: /index.php');
    exit;
}

$_conn = $_sql = $_rs = $_member_id = null;
unset($_conn, $_sql, $_rs, $_member_id);

require_once __DIR__ . '/includes/makeLogDir.php'

?>