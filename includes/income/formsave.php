<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../../class/income.class.php';   // 修正路徑，確保正確載入 Income 類別
include_once '../../class/contract.class.php'; // 修正路徑，確保正確載入 Contract 類別
// include_once 'sms/sms_function.php';

include_once '../../openadodb.php';
include_once '../../session_check.php';

// $testMail = new SMS_Gateway();
$income   = new Income();
$contract = new Contract();

$sql = '
	SELECT
		*
	FROM
		tExpense
	WHERE
		id="' . $_POST['id'] . '" ;
';
$rs          = $conn->Execute($sql);
$eDepAccount = $rs->fields['eDepAccount'];
$eTradeCode  = $rs->fields['eTradeCode'];

if (($eTradeCode != '1912') && ($eTradeCode != '1920') && ($eTradeCode != '1560')) {
    $income->SaveContract($_POST);
}
$income->SaveIncome($_POST);

if ($_POST['edsId']) {
    $income->SaveIncomeSms($_POST);
    if (is_array($_POST['otherTitle'])) {
        // echo '-----------------';
        $income->AddIncomeSmsOther($_POST);
    }

    if (is_array($_POST['otherId'])) {
        // print_r($_POST['otherId']);
        // die;
        $income->SaveIncomeSmsOther($_POST);
    }
} else {
    $income->AddIncomeSms($_POST);

    if (is_array($_POST['otherTitle'])) {
        $income->AddIncomeSmsOther($_POST);
    }
}

$data_case = $contract->GetRealstate($_POST["cCertifiedId"]);
$data_sc   = $contract->GetScrivener($_POST["cCertifiedId"]);

// $mag = $testMail->send(substr($eDepAccount,2), $data_sc['cScrivener'], $data_case['cBranchNum'], 'income', $_POST['id'], 'n', 0);
// $str_msg = "\n發送對象有︰\n";
// foreach ($mag as $k => $v) {
//     $str_msg .= $v['mName'] . "(" . $v['mMobile'] . ")\n";
// }
// $str_msg = preg_replace("/、$/","",$str_msg) ;

// echo $str_msg ;
