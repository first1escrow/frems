<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$path = dirname(__DIR__) . '/log2';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}
$fw = fopen($path . '/creatcId.log', "a+");

$cat      = $_POST['cat'];
$brand    = $_POST['br'];
$category = $_POST['cat'];
$app      = $_POST['app'];

if ($_POST['cId'] != '') {
    $col = "bBrand = '" . $brand . "',bApplication = '" . $app . "',bCategory = '" . $category . "'";
    $sql = "UPDATE tBankCode SET " . $col . " WHERE bAccount LIKE '%" . $_POST['cId'] . "'";

    fwrite($fw, $sql . ";\r\n");
    if ($conn->Execute($sql)) {
        echo 'ok';
    }
}

fclose($fw);
