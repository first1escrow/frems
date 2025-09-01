<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/util.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';

use First1\V1\PayByCase\PayByCase;
use First1\V1\Util\Util;

//傳入參數
$cId = $_POST['cId']; //保證號碼
$fId = $_POST['fId']; //流水號碼
if (!preg_match("/^\d{9}$/", $cId)) {
    exit(Util::jsonResponse(400, 'Invalid cId'));
}
##

//更新(儲存)回饋金資料
$conn = new first1DB;

try {
    $paybycase = new PayByCase;

    //更新業務審核時間
    $paybycase->updateSalesConfirmTimeLog($cId, $fId);
    ##

    exit(Util::jsonResponse(200, 'OK'));
} catch (Exception $e) {
    $conn->cancelTransaction();
    exit(Util::jsonResponse(401, $e->getMessage()));
}
##
