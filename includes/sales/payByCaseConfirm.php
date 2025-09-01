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
$fId = $_POST['fId']; //PayByCaseId
if (!preg_match("/^\d{9}$/", $cId)) {
    exit(Util::jsonResponse(400, 'Invalid cId'));
}

$bank = $_POST['bank'];

$matches = [];
if (!preg_match("/^(\d{1})\_(\d{3})\_(\d{4})\_(\d+)\_(.*)\_(.*)\_(\d+)$/iuU", $bank, $matches)) {
    exit(Util::jsonResponse(400, 'Invalid bank'));
}

$bank_identity     = $matches[1]; //身分別：1=未知、2=身份證編號、3=統一編號、4=居留證號碼
$bank_main         = $matches[2]; //總行
$bank_branch       = $matches[3]; //分行
$bank_account      = $matches[4]; //帳號
$bank_account_name = $matches[5]; //戶名
$bank_identity_no  = $matches[6]; //證件號碼
$bank_fid          = $matches[7]; //fid

$bank = $matches = null;
unset($bank, $matches);
##

//更新(儲存)回饋金資料
$conn = new first1DB;

$conn->beginTransaction();

try {
    //紀錄回饋金帳戶
    $paybycase = new PayByCase;
    $paybycase->savePayByCaseAccount(
        $cId,
        [
            'identity'    => $bank_identity,
            'main'        => $bank_main,
            'branch'      => $bank_branch,
            'account'     => $bank_account,
            'accountName' => $bank_account_name,
            'idNumber'    => $bank_identity_no,
            'bankId'      => $bank_fid
        ],
        'S',$fId
    );
    ##

    //更新業務審核時間
    $paybycase->updateSalesConfirmTime($cId);
    ##

    //通知會計
    $paybycase->needAccountingConfirm($cId);
    ##

    $conn->endTransaction();
    exit(Util::jsonResponse(200, 'OK'));
} catch (Exception $e) {
    $conn->cancelTransaction();
    exit(Util::jsonResponse(401, $e->getMessage()));
}
##
