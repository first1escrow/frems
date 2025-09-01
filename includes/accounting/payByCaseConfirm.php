<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/util.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';

use First1\V1\PayByCase\PayByCase;
use First1\V1\Util\Util;

//傳入參數
$cId     = $_POST['cId']; //保證號碼
$fNHIpay = $_POST['fNHIpay'];

if (! preg_match("/^\d{9}$/", $cId)) {
    exit(Util::jsonResponse(400, 'Invalid cId'));
}
##

//更新(儲存)回饋金資料
$conn = new first1DB;

$conn->beginTransaction();

try {
    //紀錄回饋金帳戶會計確認
    $paybycase = new PayByCase;

    $feedBackMoneyInfo = $paybycase->getPayByCase($cId);
    $tax               = $paybycase->feedbackIncomeTax($feedBackMoneyInfo['detail']['total'], $feedBackMoneyInfo['fType']);
    if ('Y' == $fNHIpay) {
        $NHI = $paybycase->feedbackNHITax($feedBackMoneyInfo['detail']['total'], $feedBackMoneyInfo['fType']);
    }

    $paybycase->updateAccountingConfirmTime($cId, $_SESSION['member_id'], $fNHIpay, $tax, 'S', $NHI);
    ##

    $conn->endTransaction();
    exit(Util::jsonResponse(200, 'OK'));
} catch (Exception $e) {
    $conn->cancelTransaction();
    exit(Util::jsonResponse(401, $e->getMessage()));
}
##
