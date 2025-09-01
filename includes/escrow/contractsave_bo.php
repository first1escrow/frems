<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
require_once dirname(dirname(__DIR__)) . '/class/intolog.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/bank/report/calTax.php';

require_once dirname(__DIR__) . '/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/lib.php';
require_once dirname(__DIR__) . '/writelog.php';

require_once __DIR__ . '/contractbank.php';

//預載log物件
$logs  = new Intolog();
$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);
$tlog  = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '案件修改儲存 -買賣');
##

$contract = new Contract();
/* 日期轉換 */

$_POST["owner_birthdayday"] = date_convert($_POST["owner_birthdayday"]);
$_POST["buy_birthdayday"]   = date_convert($_POST["buy_birthdayday"]);

$_POST["owner_payment_date"] = date_convert($_POST["owner_payment_date"]);
$_POST["buyer_payment_date"] = date_convert($_POST["buyer_payment_date"]);

//埋log紀錄 20140624
$id = empty($_POST["id"])
? $_GET["id"]
: $_POST["id"];
$data_case = $contract->GetContract($id);

$contract->SaveBuyer($_POST);
$contract->SaveExpenditure($_POST);
//銀行儲存
updateBankData($conn, $_POST, $id, 1); //買方
updateBankData($conn, $_POST, $id, 2);

addBankData($conn, $_POST, $id, 1); //買方
addBankData($conn, $_POST, $id, 2); //賣方

$data = $_POST;
$sql  = " UPDATE `tContractOwner` SET
            `cIdentifyId` = '" . $data['owner_identifyid'] . "',
            `cCategoryIdentify` = '" . $data['owner_categoryidentify'] . "',
            `cName` = '" . $data['owner_name'] . "',
            `cCountryCode` = '" . strtoupper($data['owner_country']) . "',
            `cTaxtreatyCode` = '" . strtoupper($data['owner_taxtreaty']) . "',
            `cResidentLimit` = '" . $data['owner_resident_limit'] . "',
            `cPaymentDate` = '" . $data['owner_payment_date'] . "',
            `cNHITax` = '" . $data['owner_NHITax'] . "',
            `cOther` = '" . $data['owner_other'] . "',
            `cBirthdayDay` = '" . $data['owner_birthdayday'] . "',
            `cContactName` = '" . $data['owner_contactname'] . "',
            `cMobileNum` = '" . $data['owner_mobilenum'] . "',
            `sAgentName1` = '" . $data['owner_agentname1'] . "',
            `sAgentName2` = '" . $data['owner_agentname2'] . "',
            `sAgentName3` = '" . $data['owner_agentname3'] . "',
            `sAgentName4` = '" . $data['owner_agentname4'] . "',
            `sAgentMobile1` = '" . $data['owner_agentmobile1'] . "',
            `sAgentMobile2` = '" . $data['owner_agentmobile2'] . "',
            `sAgentMobile3` = '" . $data['owner_agentmobile3'] . "',
            `sAgentMobile4` = '" . $data['owner_agentmobile4'] . "',
            `cTelArea1` = '" . $data['owner_telarea1'] . "',
            `cTelMain1` = '" . $data['owner_telmain1'] . "',
            `cTelArea2` = '" . $data['owner_telarea2'] . "',
            `cTelMain2` = '" . $data['owner_telmain2'] . "',
            `cRegistZip` = '" . $data['owner_registzip'] . "',
            `cRegistAddr` = '" . $data['owner_registaddr'] . "',
            `cBaseZip` = '" . $data['owner_basezip'] . "',
            `cBaseAddr` = '" . $data['owner_baseaddr'] . "',
            `cMoney1` = '" . $data['owner_money1'] . "',
            `cMoney2` = '" . $data['owner_money2'] . "',
            `cMoney3` = '" . $data['owner_money3'] . "',
            `cMoney4` = '" . $data['owner_money4'] . "',
            `cMoney5` = '" . $data['owner_money5'] . "',
            `cBankKey2` = '" . $data['owner_bankkey'] . "',
            `cBankBranch2` = '" . $data['owner_bankbranch'] . "',
            `cBankAccName` = '" . $data['owner_bankaccname'] . "',
            `cBankAccNumber` = '" . $data['owner_bankaccnumber'] . "',
            `cOtherName` = '" . $data['owner_othername'] . "',
            `cChecklistBank` = '" . $data['owner_cklist'] . "',
            `cPassport` = '" . $data['owner_passport'] . "'
            WHERE  `cCertifiedId` = '" . $data['certifiedid'] . "'";
$conn->Execute($sql);
$tlog->updateWrite($_SESSION['member_id'], $sql, '案件修改儲存 -買賣');

##
//其他回饋金
updateFeedBackMoney($_POST);
insertFeedBackMoney($_POST);
##

write_log($_POST['certifiedid'] . '編修案件,' . $_POST['realestate_branch'] . '-' . $_POST['cCaseFeedback'] . ',' . $_POST['cCaseFeedBackMoney'] . ',' . $_POST['cFeedbackTarget'] . ';' . $_POST['realestate_branch1'] . '-' . $_POST['cCaseFeedback1'] . ',' . $_POST['cCaseFeedBackMoney1'] . ',' . $_POST['cFeedbackTarget1'] . ';' . $_POST['realestate_branch2'] . '-' . $_POST['cCaseFeedback2'] . ',' . $_POST['cCaseFeedBackMoney2'] . ',' . $_POST['cFeedbackTarget2'], 'escrowSave');

echo "儲存完成";
##
