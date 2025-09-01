<?php
include_once '../sms/sms_function.php';
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once 'class/income.class.php';
include_once '../openadodb.php';
include_once 'income_function.php';

$sms      = new SMS_Gateway();
$contract = new Contract();

$send = trim($_POST['mail']);

$id = empty($_POST["id"])
? $_GET["id"]
: $_POST["id"];

$cid = empty($_POST["cid"])
? $_GET["cid"]
: $_POST["cid"];
//4間都買賣方
// $id = '694712';
// $cid = '090239641';

//調帳
// $id = '602010';
// $cid = '100040829';

##income2
// $id = '712551';
// $cid = '101748317';

//調帳
// $id = '661291';
// $cid = '090118386';
//
// $id = '605965';
// $cid = '080096236';

$sql = 'SELECT
		*
	FROM
		tExpense
	WHERE
		id="' . $id . '" ;';

$rs          = $conn->Execute($sql);
$eDepAccount = $rs->fields['eDepAccount'];
$eTradeCode  = $rs->fields['eTradeCode'];

$data_case = $contract->GetRealstate($cid);
$data_sc   = $contract->GetScrivener($cid);

//檢查是否有明細
$target = 'income';
if (checkExpenseDetailSms($id) == true) {
    $target = 'income2';
}
// $target = 'income';
// echo $target;exit;

$list = $sms->sendIncome(substr($eDepAccount, 2), $data_sc['cScrivener'], $data_case['cBranchNum'], $target, $id, 'n');

// echo "<pre>";
// print_r($list);exit;
// $list = $sms->getSendIncomeInfo(substr($eDepAccount,2), $data_sc['cScrivener'], $data_case['cBranchNum'], 'income2', $id , 'n', 0);

// if (checkExpenseDetailSms($id) == true) { //有明細 走新制
//     $list = $sms->getSendIncomeInfo(substr($eDepAccount,2), $data_sc['cScrivener'], $data_case['cBranchNum'], 'income2', $id , 'n', 0);

// }else{
//     $list = $sms->getSendIncomeInfo(substr($eDepAccount,2), $data_sc['cScrivener'], $data_case['cBranchNum'], 'income', $id , 'n', 0);

// }

// $count = count($list);
// $smstxt = $list[$count-1];
// unset($list[$count-1]);

$smarty->assign('list', $list);
$smarty->assign('smstxt', $smstxt);
$smarty->assign('id', $id);
$smarty->assign('cid', $cid);

$smarty->display('formsmslist.inc.tpl', '', 'income');
