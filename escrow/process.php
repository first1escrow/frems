<?php
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);
include_once dirname(dirname(__FILE__)) . '/openadodb.php';
include_once dirname(dirname(__FILE__)) . '/openpdodb.php';

//取得時間驗證
function getAuth($cId)
{
    $key = 'first1Escrow2TimeAuthKey3';

    $ts   = time();
    $time = floor($ts / 300);

    return md5($key . '|' . $cId . '|' . $time);
}

$_POST = escapeStr($_POST);

if (preg_match("/^n[1|2]{1}$/", $_POST['cat'])) {
    $target = ($_POST['cat'] == 'n1') ? 'buyer' : 'owner';
    $url    = 'http://10.10.1.199/case/' . $_POST['cCertifiedId'] . '/' . $target . '/' . getAuth($_POST['cCertifiedId']);
    header('Location: ' . $url);
    exit;
}

$pdo = $pdolink;

include_once 'processPrint.php';
