<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/util.class.php';

use First1\V1\Util\Util;

//傳入參數
$cId = $_POST['cId']; //保證號碼
if (!preg_match("/^\d{9}$/", $cId)) {
    exit(Util::jsonResponse(400, 'Invalid cId'));
}

$target = $_POST['target']; //對象 fReceipt, fAccountantClose
if (!in_array($target, ['receipt', 'close'])) {
    exit(Util::jsonResponse(400, 'Invalid target'));
}
$target = ($target == 'receipt') ? 'fReceipt' : 'fAccountantClose';

$action = $_POST['action']; //值 Y,N
if (!in_array($action, ['Y', 'N'])) {
    exit(Util::jsonResponse(400, 'Invalid action value'));
}
##

//更新隨案出款會計清單確認
$conn = new first1DB;

$sql = 'UPDATE tFeedBackMoneyPayByCase SET ' . $target . ' = "' . $action . '" WHERE fCertifiedId = "' . $cId . '" AND fTarget = "S";';
if ($conn->exeSql($sql)) {
    exit(Util::jsonResponse(200, 'OK'));
} else {
    exit(Util::jsonResponse(400, 'DB operation failed'));
}
##
