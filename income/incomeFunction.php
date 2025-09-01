<?php
require_once dirname(__DIR__) . '/first1DB.php';

//入帳資訊是否有其他部分定義
function checkExpenseDetailSms($expenseId)
{
    $conn = new First1DB;

    $sql = 'SELECT eSignMoney, eAffixMoney, eDutyMoney, eEstimatedMoney, eEstimatedMoney2, eCompensationMoney, eServiceFee, eExtraMoney, eExchangeMoney FROM tExpenseDetailSms WHERE eExpenseId = "' . $expenseId . '" ORDER BY eId DESC LIMIT 1;';
    $rs  = $conn->one($sql);

    if (! empty($rs)) {
        //簽約款
        if ($rs['eSignMoney'] > 0) {
            return true;
        }

        //用印款
        if ($rs['eAffixMoney'] > 0) {
            return true;
        }

        //完稅款
        if ($rs['eDutyMoney'] > 0) {
            return true;
        }

        //尾款
        if ($rs['eEstimatedMoney'] > 0) {
            return true;
        }

        //尾款差額
        if ($rs['eEstimatedMoney2'] > 0) {
            return true;
        }

        //代償後餘額
        if ($rs['eCompensationMoney'] > 0) {
            return true;
        }

        //買方仲介服務費
        if ($rs['eServiceFee'] > 0) {
            return true;
        }

        //溢入款
        if ($rs['eExtraMoney'] > 0) {
            return true;
        }

        //換約款
        if ($rs['eExchangeMoney'] > 0) {
            return true;
        }
    }

    $sql = 'SELECT eId FROM tExpenseDetailSmsOther WHERE eExpenseId = ' . $expenseId . ' AND eDel = 0;';
    $rs  = $conn->all($sql);

    return empty($rs) ? false : true;
}
