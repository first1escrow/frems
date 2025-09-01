<?php
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);

require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$path = dirname(__DIR__) . '/log2';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}

$fw = fopen($path . '/linemsg.log', 'a+');
if ($_POST['save']) {

    $sql = "SELECT pId,pName,pLineUserId FROM tPeopleInfo WHERE pJob = 1 AND pDep = 5 AND pLineUserId !=''";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $lineToken = $rs->fields['pLineUserId'];
        $msg       = $_POST['txt'];
        $msg       = urlencode($msg);

        $url   = "http://www.design.e-twlink.com/first1/lineBot/first1LinePush.php?userId=" . $lineToken . "&msg=" . $msg;
        $check = file_get_contents($url);

        $rs->MoveNext();
    }

    $lineToken = 'U42a053dde4940102bf8c9c7b750bb9a1';
    $msg       = $_POST['txt'];

    $url = "http://www.design.e-twlink.com/first1/lineBot/first1LinePush.php?userId=" . $lineToken . "&msg=" . $msg;
    file_get_contents($url);
    fwrite($fw, date('Y-m-d H:i:s') . "_" . $msg);

    if ($check == 1) {
        $script = "<script>alert('傳送成功');</script>";
    }
    fclose($fw);
}

$smarty->assign('script', $script);
$smarty->display('LineMsg.inc.tpl', '', 'other');
