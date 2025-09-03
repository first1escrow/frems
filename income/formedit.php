<?php
error_reporting(E_ERROR | E_PARSE); // 僅顯示錯誤訊息，隱藏警告和通知

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php'; // 修正路徑
require_once dirname(__DIR__) . '/class/advance.class.php';    // 修正路徑
require_once dirname(__DIR__) . '/class/contract.class.php';   // 修正路徑
require_once dirname(__DIR__) . '/class/scrivener.class.php';  // 修正路徑
require_once dirname(__DIR__) . '/class/income.class.php';     // 修正路徑
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/openadodb.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查看案件單筆入款明細');

if (! class_exists('Advance')) {
    die('Error: Advance class not found. Please check the file path and class definition.');
}

$advance   = new Advance();
$contract  = new Contract();
$scrivener = new Scrivener();
$income    = new Income();

$data_income = $income->GetIncomeInfo($_POST["id"]);

$data_income_sms = $income->GetIncomeSms($_POST['certifyid'], $_POST['id']);

$data_income_sms_other = $income->GetIncomeSmsOther($_POST['id']);

$menu_title = $income->GetIncomeTitle();

$trade_code               = $data_income['eTradeCode'];
$data_case                = $contract->GetContract($_POST["certifyid"]);
$list_banktran            = $income->GetChangeCreditList($_POST["certifyid"], $data_income['eLender']);
$menu_banktran            = $income->ConvertOption($list_banktran, 'tId', 'dshow', true);
$has_case                 = empty($data_case);
$data_scrivener           = $contract->GetScrivener($_POST["certifyid"]);
$money_b                  = $income->GetBankTransMoney($data_income['eBankTransId']);
$money_e                  = $income->GetExpenseLender($data_income['id']);
$data_income['remainder'] = $money_e - $money_b;
$list_material            = $contract->GetMaterialsList();
$menu_material            = $contract->ConvertOption($list_material, 'bTypeId', 'bTypeName');
$list_objkind             = $contract->GetObjKind();
$menu_objkind             = $contract->ConvertOption($list_objkind, 'oTypeId', 'oTypeName');
$list_ObjUse              = $contract->GetObjUse();
$menu_objUse              = $contract->ConvertOption($list_ObjUse, 'uId', 'uName');
$list_statuscontract      = $contract->GetStatusContract();
$menu_statuscontract      = $contract->ConvertOption($list_statuscontract, 'sId', 'sName');
$list_statusexpenditure   = $contract->GetStatusExpenditure();
$menu_statusexpenditure   = $contract->ConvertOption($list_statusexpenditure, 'sId', 'sName');
$list_statusincome        = $contract->GetStatusIncome();
$menu_statusincome        = $contract->ConvertOption($list_statusincome, 'sId', 'sName');
$list_categroyrealestate  = $contract->GetCategroyRealestate();
$menu_categroyrealestate  = $contract->ConvertOption($list_categroyrealestate, 'cId', 'cName');
$list_peoplelist          = $contract->GetPeopleList();
$menu_peoplelist          = $contract->ConvertOption($list_peoplelist, 'pId', 'pName');
$list_categroyexception   = $contract->GetCategoryException();
$menu_categroyexception   = $contract->ConvertOption($list_categroyexception, 'sId', 'sName');
$list_categroyprocession  = $contract->GetCategoryProcession();
$menu_categroyprocession  = $contract->ConvertOption($list_categroyprocession, 'sId', 'sName');
$list_categorybank        = $contract->GetCategoryBank();
$menu_categorybank        = $contract->ConvertOption($list_categorybank, 'cId', 'cBankName');
$list_scrivener           = $scrivener->GetListScrivener();
$menu_scrivener           = $scrivener->ConvertOption($list_scrivener, 'sId', 'sName', true);
$menu_budlevel            = $scrivener->GetBudLevel();
$list_categoryincome      = $scrivener->GetCategoryIncome();
$menu_categoryincome      = $contract->ConvertOption($list_categoryincome, 'sId', 'sName');

//20250321 地政士入帳簡訊是否使用新版(1:新版, 0:舊版)
$scrivenerInfo = $scrivener->GetScrivenerInfo($data_scrivener['cScrivener']); //取得地政士資訊
$sms_path      = (! empty($scrivenerInfo['sSmsIncome']) && ($scrivenerInfo['sSmsIncome'] == 1)) ? '/income/v1/' : '';
$scrivenerInfo = null;unset($scrivenerInfo);

//取得經辦資料
$sql = '
	SELECT
		peo.pName as cUndertaker,
		peo.pId as cUndertakerId
	FROM
		tBankCode AS bkc
	JOIN
		tScrivener AS scr ON scr.sId=bkc.bSID
	JOIN
		tPeopleInfo AS peo ON peo.pId=scr.sUndertaker1
	WHERE
		bkc.bAccount LIKE "%' . $_POST["certifyid"] . '"
';

$rs                         = $conn->Execute($sql);
$data_case['cUndertakerId'] = $rs->fields['cUndertakerId'];

unset($tmp);
##

//永豐連動轉帳沖正無法確認問題之修改
if (($data_income['CertifiedId'] == '000000000') && ($data_income['eAccount'] == '10401810001999' || $data_income['eAccount'] == '12601800015999')) {
    $sql = '
		SELECT
			*
		FROM
			tExpense
		WHERE
			eDepAccount = "0000000000000000"
			AND eAccount = "' . $data_income['eAccount'] . '"
			AND eTradeStatus IN ("1","9")
			AND eTradeDate = "' . $data_income['eTradeDate'] . '"
			AND eTradeNum = "' . $data_income['eTradeNum'] . '"
	';
    $rs = $conn->Execute($sql);

    $trade_code = '1560';
    unset($tmp);
}
##

$ChangeMoney = $data_income['eLender'];

$sql = 'SELECT tMoney,tMemo FROM tBankTrans WHERE tChangeExpense="' . $data_income['id'] . '" ;';

$rs = $conn->Execute($sql);
while (! $rs->EOF) {
    $ChangeMoney -= $rs->fields['tMoney'];
    $ChangeExpense[] = $rs->fields;
    $rs->MoveNext();
}

if ($ChangeMoney > 0 && $ChangeMoney != $data_income['eLender']) {
    $data_income['eChangeMoney'] = $ChangeMoney;
}

#####檢查時否有開點交單
$checkChecklist = 0;
if ($data_income['eStatusIncome'] == 1) {
    $sql            = "SELECT tId FROM tUploadFile WHERE tCertifiedId = '" . $_POST["certifyid"] . "'";
    $rs             = $conn->Execute($sql);
    $checkChecklist = $rs->RecordCount();
}

#####
$smarty->assign('checkChecklist', $checkChecklist);
$smarty->assign('ChangeExpense', $ChangeExpense);
$smarty->assign('menu_material', $menu_material);
$smarty->assign('menu_objkind', $menu_objkind);
$smarty->assign('menu_objuse', $menu_objUse);
$smarty->assign('menu_statuscontract', $menu_statuscontract);
$smarty->assign('menu_statusincome', $menu_statusincome);
$smarty->assign('menu_statusexpenditure', $menu_statusexpenditure);
$smarty->assign('menu_categroyexception', $menu_categroyexception);
$smarty->assign('menu_categroyrealestate', $menu_categroyrealestate);
$smarty->assign('menu_categroyprocession', $menu_categroyprocession);
$smarty->assign('menu_peoplelist', $menu_peoplelist);
$smarty->assign('menu_categorybank', $menu_categorybank);
$smarty->assign('menu_scrivener', $menu_scrivener);
$smarty->assign('menu_budlevel', $menu_budlevel);
$smarty->assign('menu_categoryincome', $menu_categoryincome);
$smarty->assign('list_bankacc', $list_bankacc);
$smarty->assign('menu_banktran', $menu_banktran);
$smarty->assign('menu_title', $menu_title);
$smarty->assign('data_income', $data_income);
$smarty->assign('data_case', $data_case);
$smarty->assign('data_scrivener', $data_scrivener);
$smarty->assign('has_case', $has_case);
$smarty->assign('trade_code', $trade_code);
$smarty->assign('data_income_sms', $data_income_sms);
$smarty->assign('data_income_sms_other', $data_income_sms_other);
$smarty->assign('sms_path', $sms_path);

$smarty->display('forminspection.inc.tpl', '', 'income');
