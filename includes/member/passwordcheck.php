<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/member.class.php';
require_once dirname(dirname(__DIR__)) . '/class/intolog.php';
require_once dirname(__DIR__) . '/lib.php';

$member = new Member();
$logs   = new Intolog();

$_POST = escapeStr($_POST);

// 檢查 remembered 是否存在，避免未定義陣列鍵值錯誤
$remembered = isset($_POST["remembered"]) ? $_POST["remembered"] : "";

// set cookies - 修正 preg_match null 參數問題
if (! empty($remembered) && preg_match("/1/", $remembered)) {
    setcookie("act", $_POST["account"], time() + (3600 * 24 * 30), "/");
    setcookie("psd", $_POST['password'], time() + (3600 * 24 * 30), "/");
} else {
    setcookie("act", $_POST["account"], time() - 3600, "/");
    setcookie("psd", $_POST['password'], time() - 3600, "/");
}
##

$is_pass = $member->CheckPassword($_POST["account"], $_POST['password']);

if ($is_pass) {
    //header('Location: http://' . $GLOBALS['DOMAIN'] . '/inquire/buyerownerinquery.php');
    $logs->writelog('loginOK');

    if ($_SESSION['member_searchcase'] == 1) {
        if ($_SESSION['member_pDep'] == 7) {
            header('Location: ../../sales/salesTracking.php');
        } else {

            header('Location: ../../inquire/buyerownerinquery.php');
        }
    } else {
        header('Location: ../../others/welcome.php');
    }

    exit();
} else {
    //header('Location: http://' . $GLOBALS['DOMAIN']);
    $logs->writelog('loginNG', $_POST["account"]);
    //header('Location: /');
    //exit();
    echo '
		<script>
		alert("帳號密碼錯誤!!請重新輸入!!") ;
		location = "/" ;
		</script>
	';
}

//echo $_COOKIE['act'] ;
//print_r($_COOKIE) ;
