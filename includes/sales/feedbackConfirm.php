<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/util.class.php';
require_once dirname(dirname(__DIR__)) . '/class/confirmFeedback.class.php';

use First1\V1\ConfirmFeedback\ConfirmFeedback;
use First1\V1\Util\Util;

//傳入參數
$cId = $_POST['cId']; //保證號碼
$fId = $_POST['fId']; //流水編號
$fTarget = $_POST['fTarget']; //R 仲介,S 代書
if (!preg_match("/^\d{9}$/", $cId)) {
    exit(Util::jsonResponse(400, 'Invalid cId'));
}

//更新(儲存)回饋金資料
$conn = new first1DB;

$confirmFeedback = new ConfirmFeedback;
$confirmFeedback->updateSalesConfirmTime($fId, $cId, $fTarget);

exit(Util::jsonResponse(200, 'OK'));
##
