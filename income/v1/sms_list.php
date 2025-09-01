<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/sms/SMS.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(__DIR__) . '/incomeFunction.php';

use First1\V1\SMS\SMS;

$expenseId = empty($_POST["id"]) ? $_GET["id"] : $_POST["id"];
$cId       = empty($_POST["cid"]) ? $_GET["cid"] : $_POST["cid"];

if (empty($expenseId) || empty($cId)) {
    throw new Exception('id or cid is empty');
}

if (! is_numeric($expenseId) || ! is_numeric($cId)) {
    throw new Exception('id or cid is invalid');
}

//檢查是否有明細
$target = (checkExpenseDetailSms($expenseId) === true) ? 'income2' : 'income';

$conn = new First1DB;

//履保帳號
$sql = 'SELECT eDepAccount FROM tExpense WHERE id = ' . $expenseId . ';';
$rs  = $conn->one($sql);
if ($rs === false) {
    throw new Exception('Failed to get eDepAccount');
}

$eDepAccount = $rs['eDepAccount'];

//地政士與第一家仲介店編號
$sId = null;
$bId = null;

$sql = 'SELECT a.cScrivener as sId, b.cBranchNum as bId FROM tContractScrivener AS a JOIN tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId  WHERE cCertifiedId = ' . $cId . ';';
$rs  = $conn->one($sql);
if ($rs === false) {
    throw new Exception('Failed to get sId and bId');
}

$sId = $rs['sId'];
$bId = $rs['bId'];

//收款簡訊類別
$targetType = 'income';
if (checkExpenseDetailSms($expenseId) == true) {
    $targetType = 'income2'; //有明細
}

$sms  = new SMS(new First1DB);
$list = $sms->incomeSMS(substr($eDepAccount, 2), $sId, $bId, $targetType, $expenseId);

$smarty->assign('list', $list);
$smarty->assign('id', $expenseId);
$smarty->assign('cId', $cId);
$smarty->assign('targetType', $targetType);

$smarty->display('formsmslist1.inc.tpl', '', 'income');
