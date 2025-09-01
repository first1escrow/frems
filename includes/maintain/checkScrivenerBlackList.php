<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/scrivener.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

require_once dirname(__DIR__) . '/lib.php';
require_once dirname(__DIR__) . '/writelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '地政士黑名單檢查');

$_POST = escapeStr($_POST);

$search_str = '';

if (!empty($_POST['name'])) {
    if ($search_str) {$search_str .= ' OR ';}
    $search_str .= "sName = '" . $_POST['name'] . "'";
}

if (!empty($_POST['office'])) {
    if ($search_str) {$search_str .= ' OR ';}
    $search_str .= "sOffice = '" . $_POST['office'] . "'";
}

if (!empty($_POST['identifyId'])) {
    if ($search_str) {$search_str .= ' OR ';}
    $search_str .= "sIdentifyId = '" . $_POST['identifyId'] . "'";
}

if (!empty($_POST['address'])) {
    if ($search_str) {$search_str .= ' OR ';}
    $search_str .= "(sZip = '" . $_POST['zip'] . "' AND sAddress = '" . $_POST['address'] . "')";
}

//查詢資料
$sql = "SELECT * FROM tScrivenerBlackList WHERE  (" . $search_str . ") AND sDelete = 0";
$rs  = $conn->Execute($sql);

$jsonArray = array();
if (!$rs->EOF) {
    $sql = "UPDATE tScrivener SET sBlackListId = '" . $rs->fields['sId'] . "' WHERE sId = '" . $_POST['id'] . "'";
    $conn->Execute($sql);
    $jsonArray['code'] = 201;
    $jsonArray['msg']  = "該地政士符合黑名單資料";
} else {
    $jsonArray['code'] = 200;
    $jsonArray['msg']  = "";
}

echo json_encode($jsonArray);
exit;
