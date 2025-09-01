<?php
// require_once dirname(dirname(__DIR__)) . '/first1DB.php';

// $tIds    = [1042086, 1042087, 1042088, 1042089, 1042090, 1042091, 1042093];
// $db_conn = new first1DB;

require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';
require_once dirname(dirname(__DIR__)) . '/includes/bank/relayBankFunction.php';

use First1\V1\PayByCase\PayByCase;

/**
 * 20231122 建立儲存中繼資訊
 */

//
// $sql = 'SELECT tVR_Code, tMemo, tKind, tMoney FROM tBankTrans WHERE tId IN (' . implode(',', $tIds) . ') AND tObjKind IN ("點交(結案)", "解除契約", "建經發函終止", "預售屋");';
$sql = 'SELECT
            a.tVR_Code,
            a.tMemo,
            a.tObjKind,
            a.tKind,
            a.tMoney,
            a.tExport_time,
            b.cCertifiedMoney,
            c.cFeedbackDate
        FROM
            tBankTrans AS a
        JOIN
            tContractIncome AS b ON a.tMemo = b.cCertifiedId
        JOIN 
            tContractCase AS c ON a.tMemo = c.cCertifiedId
        WHERE
            a.tId IN (' . implode(',', $tIds) . ')
            AND a.tObjKind IN ("點交(結案)", "解除契約", "建經發函終止", "預售屋")
            AND (a.tKind = "保證費" or a.tInvoice IS NOT NULL)
            AND b.cCertifiedMoney > 0;';
$all = $db_conn->all($sql);
// print_r($all);

if (!empty($all)) {
    $cIds = array_unique(array_column($all, 'tMemo'));

    $cases = [];
    foreach ($cIds as $cId) {
        $cases[$cId] = [
            'cId'             => $cId,
            'certified_money' => 0,
            'vr_code'         => '',
            'incoming_money'  => 0,
        ];

        foreach ($all as $k => $v) {
            if ($v['tMemo'] == $cId) {
                $cases[$cId]['vr_code'] = $v['tVR_Code'];
                $cases[$cId]['tExport_time'] = $v['tExport_time'];

                if ($v['tKind'] == '保證費') {
                    $cases[$cId]['certified_money'] = $v['tMoney'];
                    $cases[$cId]['incoming_money']  = $v['tMoney'];

                    break;
                }
            }
        }
    }

    $cIds = $all = null;
    unset($cIds, $all);
}
// echo '<pre>';
// print_r($cases);exit;

if (!empty($cases)) {
    $paybycase = new PayByCase;

    $paybycase->writeLog('tBankTrans.tId：' . implode(',', $tIds), json_encode($cases), '中繼帳戶資料明細');
    foreach ($cases as $v) {
        $pay_by_case = $paybycase->getPayByCase($v['cId']);

        $feedBackTotal = empty($pay_by_case['detail']['total']) ? 0 : $pay_by_case['detail']['total'];
        //確認是否為隨案回饋案件
        if (empty($pay_by_case) or $feedBackTotal == 0) {
            continue;
        }

        //確認紀錄是否已存在
        $sql = 'SELECT bUid, bCertifiedId, bKind, bMoney FROM tBankTransRelay WHERE bCertifiedId = :cId;';
        $_rs = $db_conn->all($sql, ['cId' => $v['cId']]);

        if (!empty($_rs)) { //已有紀錄存在
            continue;
        }

        /**
         * 地政士回饋金紀錄
         */
        $_money = empty($pay_by_case['detail']['total']) ? 0 : $pay_by_case['detail']['total']; //金額

        $_money -= (empty($pay_by_case)) ? 0 : $pay_by_case['fTax']; //代扣稅款
        $_money -= (empty($pay_by_case)) ? 0 : $pay_by_case['fNHI']; //代扣二代健保

        //相減後回饋金額仍大於 0，則記錄至中繼帳號出款
        if ($_money > 0) {
            $_bank_code = empty($pay_by_case['fBankMain']) ? '' : $pay_by_case['fBankMain']; //總行代碼
            $_bank_code .= empty($pay_by_case['fBankBranch']) ? '' : $pay_by_case['fBankBranch']; //分行代碼

            $_bank_account      = empty($pay_by_case['fBankAccount']) ? '' : $pay_by_case['fBankAccount']; //帳號
            $_bank_account_name = empty($pay_by_case['fBankAccountName']) ? '' : $pay_by_case['fBankAccountName']; //帳戶

            $_txt = $v['cId'] . '地政士回饋金';


            saveDB($db_conn, [
                'cId'               => $v['cId'],
                'vr_code'           => $v['vr_code'],
                'date'              => date("Y-m-d"),
                'kind'              => "地政士回饋金",
                'bank_code'         => $_bank_code,
                'bank_account'      => $_bank_account,
                'bank_account_name' => $_bank_account_name,
                'money'             => $_money,
                'txt'               => $_txt,
                'incoming_money'    => $v['incoming_money'],
                'order_time'        => $v['tExport_time'],
            ]);
        }

        $_bank_code = $_bank_account = $_bank_account_name = $_txt = null;
        unset($_bank_code, $_bank_account, $_bank_account_name, $_txt);

        /**
         * 履保費紀錄
         */
        $realCertifiedMoney = empty($v['certified_money']) ? 0 : $v['certified_money']; //取得履保費金額
        $_money             = $realCertifiedMoney - $_money; //履保費 = 履保費 - 地政士回饋金

        //相減後履保費仍大於 0，則記錄至中繼帳號出款
        if ($_money > 0) {
            $first_bank = getFirstBank($db_conn, substr($v['vr_code'], 0, 5)); //第一建經信託帳號對應活儲資訊

            $_bank_code = empty($first_bank['cBankMain']) ? '' : $first_bank['cBankMain']; //總行代碼
            $_bank_code .= empty($first_bank['cBankBranch']) ? '' : $first_bank['cBankBranch']; //分行代碼

            $_bank_account      = empty($first_bank['cBankAccount']) ? '' : $first_bank['cBankAccount']; //帳號
            $_bank_account_name = empty($first_bank['cAccountName']) ? '' : $first_bank['cAccountName']; //帳戶

            $_txt = $v['cId'] . '保證費';

            saveDB($db_conn, [
                'cId'               => $v['cId'],
                'vr_code'           => $v['vr_code'],
                'date'              => date("Y-m-d"),
                'kind'              => "保證費",
                'bank_code'         => $_bank_code,
                'bank_account'      => $_bank_account,
                'bank_account_name' => $_bank_account_name,
                'money'             => $_money,
                'txt'               => $_txt,
                'incoming_money'    => 0,
                'order_time'        => $v['tExport_time'],
            ]);
        }

        $_money = $first_bank = $_bank_code = $_bank_account = $_bank_account_name = $_txt = null;
        unset($_money, $first_bank, $_bank_code, $_bank_account, $_bank_account_name, $_txt);
    }
}
