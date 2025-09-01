<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$id = $_POST['id'];

$sql = "UPDATE tUndertakerCalendar SET uDel = 1 WHERE uId = '" . $id . "'";
if ($conn->Execute($sql)) {
    echo 'ok';
}