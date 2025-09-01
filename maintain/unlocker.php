<?php
date_default_timezone_set("Asia/Taipei");

require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$id   = $_POST['id'];
$type = $_POST['tp'];

if (!preg_match("/^\d+$/", $id)) {
    die('D');
} else if (!preg_match("/^[b|s]{1}$/i", $type)) {
    die('P');
} else {
    //設定底線時間
    $validate = strtotime("-3 months");
    $validate = date("Y-m-d H:i:s", strtotime("+ 3 days", $validate));
    ##

    //決定資料表
    if ($type == 'b') {
        $sql = 'UPDATE tBranch SET bLoginTime="' . $validate . '" WHERE bId="' . $id . '";';
    } else if ($type == 's') {
        $sql = 'UPDATE tScrivener SET sLoginTime="' . $validate . '" WHERE sId="' . $id . '";';
    }

    ##

    //

    if ($conn->Execute($sql)) {
        echo 'T';
    } else {
        echo 'F';
    }

    ##
}
