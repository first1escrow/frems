<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/util.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';

use First1\V1\PayByCase\PayByCase;
use First1\V1\Util\Util;

$code_msg = array(0 => '無資料', 1 => '確認成功', 2 => '部份成功', 3 => '確認失敗');
$output['code'] = 0;
$error_detail = [];
$error_counter = 0;

$case_data = json_decode(file_get_contents('php://input'), true);
$all_counter = count($case_data);

//更新(儲存)回饋金資料
$conn = new first1DB;

foreach ($case_data as $item) {
    $cId = $item['cid'];
    $bank = $item['bank'];
    $fId = $item['fid'];
    $matches = [];

    if (!preg_match("/^\d{9}$/", $cId)) {
        $error_detail[] = 'Invalid cId(' . $cId . ')';
        $error_counter++;
    } else if (!preg_match("/^(\d{1})\_(\d{3})\_(\d{4})\_(\d+)\_(.*)\_(.*)\_(\d+)$/iuU", $bank, $matches)) {
        $error_detail[] = 'Invalid bank(' . $cId . ')';
        $error_counter++;
    } else {
        $bank_identity = $matches[1]; //身分別：1=未知、2=身份證編號、3=統一編號、4=居留證號碼
        $bank_main = $matches[2]; //總行
        $bank_branch = $matches[3]; //分行
        $bank_account = $matches[4]; //帳號
        $bank_account_name = $matches[5]; //戶名
        $bank_identity_no = $matches[6]; //證件號碼
        $bank_fid = $matches[7]; //fid

        $bank = $matches = null;
        unset($bank, $matches);
        ##

        $conn->beginTransaction();

        try {
            //紀錄回饋金帳戶
            $paybycase = new PayByCase;
            $paybycase->savePayByCaseAccount(
                $cId,
                [
                    'identity' => $bank_identity,
                    'main' => $bank_main,
                    'branch' => $bank_branch,
                    'account' => $bank_account,
                    'accountName' => $bank_account_name,
                    'idNumber' => $bank_identity_no,
                    'bankId' => $bank_fid
                ],
                'S', $fId
            );
            ##

            //更新業務審核時間
            $paybycase->updateSalesConfirmTime($cId);
            ##

            //通知會計
            $paybycase->needAccountingConfirm($cId);
            ##

            $conn->endTransaction();
        } catch (Exception $e) {
            $conn->cancelTransaction();
            $error_detail[] = $e->getMessage() . '(' . $cId . ')';
            $error_counter++;
        }
        ##
    }
}

if ($all_counter == 0) {
    $output['code'] = 0;
} else if ($error_counter == 0) {
    $output['code'] = 1;
} else {
    $output['code'] = ($error_counter == $all_counter) ? 3 : 2;
}
$output['message'] = $code_msg[$output['code']];
$output['error_detail'] = (count($error_detail) > 0) ? implode('，', $error_detail) : '';

echo json_encode($output);

exit;