<?php

$pay_case = [];

$pay_case['vr_code']       = '55006110050011'; //20240215 取消一銀桃園利息帳戶，全部採用城東分行版本
$title_bank['id']          = 7;
$title_bank['cBankName']   = '一銀';
$title_bank['cBranchName'] = '城東';
$title_bank['bank_no']     = '007';

//轉換保證號碼資訊
$_sql = 'SELECT cBankMain, cBankBranch, cTrustAccountName, cTrustAccountNameEC FROM tContractBank WHERE cBankVR LIKE "' . substr($vr_code, 0, 5) . '%";';
$_rs  = $conn->Execute($_sql);

if (!$_rs->EOF) {
    $pay_case['cBankMain']         = $_rs->fields['cBankMain']; //總行代碼
    $pay_case['cBankBranch']       = $_rs->fields['cBankBranch']; //分行代碼
    $pay_case['cBankTrustAccount'] = $vr_code; //信託帳號
    $pay_case['cTrustAccountName'] = $_rs->fields['cTrustAccountNameEC']; //信託帳號戶名

    $pay_case['_vr_bank'] = $title_bank['cBankName']; //銀行名稱
    $pay_case['branch']   = $title_bank['cBranchName']; //分行名稱
    $pay_case['bank_no']  = $title_bank['bank_no'];
}

$_rs = $_sql = $_contract_bank = null;
unset($_rs, $_sql, $_contract_bank);
##

$i     = 1;
$index = 0;

$_a[$index]   = ''; //角色選擇
$_an[$index]  = $pay_case['cTrustAccountName']; //戶名
$_ac[$index]  = $pay_case['cBankTrustAccount']; //帳號
$_ab3[$index] = $pay_case['cBankMain']; //解匯行
$_ab4[$index] = $pay_case['cBankBranch']; //分行別
$_ab5[$index] = ''; //金額
$index++;

$i = count($_a);

$total_money     = empty($total_money) ? 0 : $total_money;
$CertifiedMoney  = empty($CertifiedMoney) ? 0 : $CertifiedMoney;
$CommitmentMoney = empty($CertifiedMoney) ? 0 : $CertifiedMoney;
$realty_charge   = 0;
