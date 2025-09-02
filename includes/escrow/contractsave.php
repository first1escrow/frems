<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
require_once dirname(dirname(__DIR__)) . '/class/intolog.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/bank/report/calTax.php';
require_once dirname(dirname(__DIR__)) . '/class/checkFeedbackMoney.class.php';
require_once dirname(dirname(__DIR__)) . '/class/confirmFeedback.class.php';

require_once dirname(__DIR__) . '/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/lib.php';
require_once dirname(__DIR__) . '/writelog.php';

require_once __DIR__ . '/contractbank.php';
require_once __DIR__ . '/appraisal.php';

//預載log物件
$logs = new Intolog();

$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '案件修改儲存');
##

$contract = new Contract();

/* 日期轉換 */
$_POST["case_signdate"]      = date_convert($_POST["case_signdate"]);
$_POST["case_finishdate"]    = date_convert($_POST["case_finishdate"]);
$_POST["case_finishdate2"]   = date_convert($_POST["case_finishdate2"]);
$_POST["owner_birthdayday"]  = date_convert($_POST["owner_birthdayday"]);
$_POST["buy_birthdayday"]    = date_convert($_POST["buy_birthdayday"]);
$_POST["case_cEndDate"]      = date_convert($_POST["case_cEndDate"]);
$_POST["owner_payment_date"] = date_convert($_POST["owner_payment_date"]);
$_POST["buyer_payment_date"] = date_convert($_POST["buyer_payment_date"]);
$_POST["case_affixdate"]     = date_convert($_POST["case_affixdate"]);
$_POST['case_firstdate']     = date_convert($_POST["case_firstdate"]);

if ($_POST['rent_rentdate'] != '0000-00-00') {
    $_POST['rent_rentdate'] = date_convert($_POST["rent_rentdate"]);
}

$id = empty($_POST["id"])
? $_GET["id"]
: $_POST["id"];

$data_case = $contract->GetContract($id);

$sql = "SELECT cNote FROM tContractNote WHERE cCertifiedId = '" . $id . "' AND cCategory = 5 ORDER BY cCreatTime DESC";
$rs  = $conn->Execute($sql);

if ($_POST['income_reason'] != $rs->fields['cNote']) {
    //主管未審核過才能更改2018-12-25
    $sql = "SELECT cId FROM tContractIncome WHERE cInspetor2 = ''  AND cCertifiedId = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    if ($rs->fields['cId']) {
        $sql = "UPDATE tContractNote SET cDel = 1 WHERE cCertifiedId = '" . $id . "' AND cCategory = 5";
        $rs  = $conn->Execute($sql);

        $sql = "INSERT INTO
				tContractNote
				(
					cCertifiedId,
					cCategory,
					cNote,
					cCreator,
					cCreatTime
				)
			VALUES (
				'" . $id . "',
				'5',
				'" . $_POST['income_reason'] . "',
				'" . $_SESSION['member_id'] . "',
				'" . date('Y-m-d H:i:s') . "'
			)";
        $conn->Execute($sql);
    }
}

if ($data_case['cCaseStatus'] > 2 && $_POST['case_status'] == 2) {
    switch ($data_case['cCaseStatus']) {
        case '3':
            $case_status = '已結案';
            break;
        case '4':
            $case_status = '解除契約';
            break;
        case '6':
            $case_status = '異常';
            break;
        case '8':
            $case_status = '作廢';
            break;
        case '10':
            $case_status = '已結案有保留款';
            break;
    }
    $_POST["case_cEndDate"] = ''; //如果狀態改為進行中狀態日期為空

    write_log($_POST['certifiedid'] . ',更改狀態,進行中,' . $_POST["case_cEndDate"], 'escrowStatus');
} elseif ($_POST['case_status'] > 2 && $data_case['cCaseStatus'] == 2) {
    $sql = "UPDATE tContractCase SET cFinishDate3 = '" . date('Y-m-d H:i:s') . "' WHERE cCertifiedId = '" . $id . "'";
    $conn->Execute($sql);
} elseif ($_POST['case_status'] == 2) {
    $sql = "UPDATE tContractCase SET cFinishDate3 = '0000-00-00 00:00:00' WHERE cCertifiedId = '" . $id . "'";
    $conn->Execute($sql);
}

//檢查是否有異動店家
$data_realestate = $contract->GetRealstate($id);
if ($data_realestate['cBranchNum'] != $_POST['realestate_branchnum'] || $data_realestate['cBranchNum1'] != $_POST['realestate_branchnum1'] || $data_realestate['cBranchNum2'] != $_POST['realestate_branchnum2'] || $data_realestate['cBranchNum3'] != $_POST['realestate_branchnum3']) { //
    if ($_POST['realestate_bRecall1'] == '') {$_POST['realestate_bRecall1'] = 0;}
    if ($_POST['realestate_bRecall2'] == '') {$_POST['realestate_bRecall2'] = 0;}
    if ($_POST['realestate_bScrRecall'] == '') {$_POST['realestate_bScrRecall'] = 0;}
    if ($_POST['realestate_bScrRecall1'] == '') {$_POST['realestate_bScrRecall1'] = 0;}
    if ($_POST['realestate_bScrRecall2'] == '') {$_POST['realestate_bScrRecall2'] = 0;}
    if ($_POST['realestate_bScrRecall3'] == '') {$_POST['realestate_bScrRecall3'] = 0;}

    if ($_POST['scrivener_BrandScrRecall'] == '') {$_POST['scrivener_BrandScrRecall'] = 0;}
    if ($_POST['scrivener_BrandScrRecall1'] == '') {$_POST['scrivener_BrandScrRecall1'] = 0;}
    if ($_POST['scrivener_BrandScrRecall2'] == '') {$_POST['scrivener_BrandScrRecall2'] = 0;}
    if ($_POST['scrivener_BrandScrRecall3'] == '') {$_POST['scrivener_BrandScrRecall3'] = 0;}

    if ($_POST['scrivener_BrandRecall'] == '') {$_POST['scrivener_BrandRecall'] = 0;}
    if ($_POST['scrivener_BrandRecall1'] == '') {$_POST['scrivener_BrandRecall1'] = 0;}
    if ($_POST['scrivener_BrandRecall2'] == '') {$_POST['scrivener_BrandRecall2'] = 0;}
    if ($_POST['scrivener_BrandRecall3'] == '') {$_POST['scrivener_BrandRecall3'] = 0;}

    $sql = "UPDATE
				tContractCase
			SET
				cBranchRecall = '" . $_POST['realestate_bRecall'] . "',
				cBranchRecall1 = '" . $_POST['realestate_bRecall1'] . "',
				cBranchRecall2 = '" . $_POST['realestate_bRecall2'] . "',
				cBranchScrRecall = '" . $_POST['realestate_bScrRecall'] . "',
				cBranchScrRecall1 = '" . $_POST['realestate_bScrRecall1'] . "',
				cBranchScrRecall2 = '" . $_POST['realestate_bScrRecall2'] . "',
				cBranchScrRecall3 = '" . $_POST['realestate_bScrRecall3'] . "',
				cScrivenerRecall = '" . $_POST['sRecall'] . "',
				cScrivenerSpRecall = '" . $_POST['scrivener_sSpRecall'] . "',
				cScrivenerSpRecall2 = '" . $_POST['cScrivenerSpRecall2'] . "',
				cBrandScrRecall = '" . $_POST['scrivener_BrandScrRecall'] . "',
				cBrandScrRecall1 = '" . $_POST['scrivener_BrandScrRecall1'] . "',
				cBrandScrRecall2 = '" . $_POST['scrivener_BrandScrRecall2'] . "',
				cBrandScrRecall3 = '" . $_POST['scrivener_BrandScrRecall3'] . "',
				cBrandRecall = '" . $_POST['scrivener_BrandRecall'] . "',
				cBrandRecall1 = '" . $_POST['scrivener_BrandRecall1'] . "',
				cBrandRecall2 = '" . $_POST['scrivener_BrandRecall2'] . "',
				cBrandRecall3 = '" . $_POST['scrivener_BrandRecall3'] . "'
			WHERE cCertifiedId ='" . $id . "'";
    $conn->Execute($sql);
}
###

##
//契約書用印仲介店
if ($_POST['cAffixBranch']) {
    $checkAffix = $_POST['cAffixBranch'];
    if (is_array($checkAffix)) {
        $_POST['cAffixBranch']  = in_array('b', $checkAffix) ? '1' : '0';
        $_POST['cAffixBranch1'] = in_array('b1', $checkAffix) ? '1' : '0';
        $_POST['cAffixBranch2'] = in_array('b2', $checkAffix) ? '1' : '0';
        $_POST['cAffixBranch3'] = in_array('b3', $checkAffix) ? '1' : '0';
    } else {
        $_POST['cAffixBranch']  = ($checkAffix == 'b') ? '1' : '0';
        $_POST['cAffixBranch1'] = ($checkAffix == 'b1') ? '1' : '0';
        $_POST['cAffixBranch2'] = ($checkAffix == 'b2') ? '1' : '0';
        $_POST['cAffixBranch3'] = ($checkAffix == 'b3') ? '1' : '0';
    }
    $checkAffix = null;unset($checkAffix);
}
###

$contract->SaveContract($_POST);
$contract->SaveRealstate($_POST);
$contract->SaveScrivener($_POST);

//20231030 轉換土地使用分區
$land_category_options = $contract->GetCategoryAreaMenuList();
// 檢查 land_category 是否存在且是字符串
if (isset($_POST['land_category']) && is_string($_POST['land_category'])) {
    foreach ($land_category_options as $id => $name) {
        if ($_POST['land_category'] == $name) {
            $_POST['land_category'] = $id;
            break;
        }
    }
}
$land_category_options = null;unset($land_category_options);

if (! $contract->CheckLand($_POST['certifiedid'], 0)) {
    $contract->AddLand($_POST, 0);

    for ($i = 0; $i < count($_POST['land_movedate']); $i++) {
        $data['cCertifiedId'] = $id;
        $data['cLandItem']    = 0;
        $data['cItem']        = $i;
        $data['cMoveDate']    = date_convert($_POST['land_movedate'][$i]) . "-00";
        $data['cLandPrice']   = str_replace(',', '', $_POST['land_landprice'][$i]);
        $data['cPower1']      = $_POST['land_power1'][$i];
        $data['cPower2']      = $_POST['land_power2'][$i];

        $contract->addLandPrice($data);
    }
} else {
    $contract->SaveLand($_POST, 0);
    for ($i = 0; $i < count($_POST['land_movedate']); $i++) {
        $data['cMoveDate']  = date_convert($_POST['land_movedate'][$i]) . "-00";
        $data['cLandPrice'] = str_replace(',', '', $_POST['land_landprice'][$i]);
        $data['cPower1']    = $_POST['land_power1'][$i];
        $data['cPower2']    = $_POST['land_power2'][$i];
        $data['cId']        = $_POST['land_id'][$i];

        $contract->saveLandPrice($data);
    }
}

$contract->SaveIncome($_POST);
$contract->SaveExpenditure($_POST);
$contract->SaveInvoice($_POST);
$contract->SaveOwner($_POST);
$contract->saveOwnerSales($_POST);
$contract->SaveBuyer($_POST);
$contract->SaveBuyerSales($_POST);
$contract->SaveContractRent($_POST);

// 確保 LandFee 有值
if (! isset($_POST['LandFee']) || empty($_POST['LandFee'])) {
    $_POST['LandFee'] = '1'; // 預設為 1 (買方負擔)
}

if ($contract->GetContractLandCategory($_POST['certifiedid'])) {
    $contract->SavelandCategoryLand($_POST);
} else {
    $contract->AddlandCategoryLand($_POST);
}

##傢具
$data_furniture = $contract->GetFurniture($_POST['certifiedid']);

if ($data_furniture) {
    $contract->SaveFurniture($_POST);
} else {
    $contract->AddContractFurniture($_POST);
}

$data_ascription = $contract->GetAscription($_POST['certifiedid']);

if ($data_ascription) {
    $contract->SaveAscription($_POST);
} else {
    $contract->AddContractAscription($_POST);
}

##建物
$update_count = count($_POST['property_Item']);

for ($i = 0; $i < $update_count; $i++) {
    $_POST["property_closingday" . $_POST['property_Item'][$i]] = date_convert($_POST["property_closingday" . $_POST['property_Item'][$i]]);
    $_POST["property_builddate" . $_POST['property_Item'][$i]]  = date_convert($_POST["property_builddate" . $_POST['property_Item'][$i]]);
    $_POST["property_rentdate" . $_POST['property_Item'][$i]]   = date_convert($_POST["property_rentdate" . $_POST['property_Item'][$i]]);

    if ($contract->CheckProperty($_POST['certifiedid'], $_POST['property_Item'][$i])) {
        $contract->SaveProperty2($_POST, $_POST['property_Item'][$i]);
    }

    $contract->SaveProperty2BuildingLanNo($_POST['property_Item'][$i], $_POST);
}

$contract->AddProperty2($_POST);

//20220725 記錄呼叫一銀貸款成數API案件
$appraisal = new Appraisal;
$appraisal->registerCase($id);
$appraisal = null;unset($appraisal);
##

if ($_POST['changeLand'] == 1) {
    $cal = calCase($_POST['certifiedid']);

    $sql = "UPDATE tContractIncome SET cAddedTaxMoney = '" . $cal . "' WHERE cCertifiedId = '" . $_POST['certifiedid'] . "'";
    $conn->Execute($sql);
}

//銀行儲存
updateBankData($conn, $_POST, $id, 1); //買方
updateBankData($conn, $_POST, $id, 2);

addBankData($conn, $_POST, $id, 1); //買方
addBankData($conn, $_POST, $id, 2); //賣方
##

//其他回饋金
updateFeedBackMoney($_POST);
insertFeedBackMoney($_POST);
##

//沒有保證費金額&不是進行中案件 刪除回饋金
if ($_POST['income_certifiedmoney'] == 0 and $_POST['case_status'] > 2) {
    deleteFeedBackMoney($_POST['certifiedid']);
}

//埋log紀錄
write_log($_POST['certifiedid'] . '編修案件,' . $_POST['realestate_branch'] . '-' . $_POST['cCaseFeedback'] . ',' . $_POST['cCaseFeedBackMoney'] . ',' . $_POST['cFeedbackTarget'] . ';' . $_POST['realestate_branch1'] . '-' . $_POST['cCaseFeedback1'] . ',' . $_POST['cCaseFeedBackMoney1'] . ',' . $_POST['cFeedbackTarget1'] . ';' . $_POST['realestate_branch2'] . '-' . $_POST['cCaseFeedback2'] . ',' . $_POST['cCaseFeedBackMoney2'] . ',' . $_POST['cFeedbackTarget2'], 'escrowSave');
##

//群義編號
$sql = "UPDATE tBankCode SET bNo72 = '" . $_POST['data_bankcode_No72'] . "' WHERE bAccount = '" . $data_case['cEscrowBankAccount'] . "'";
$conn->Execute($sql);
##

//20230322 判定通知業務是否審核
$paybycase = new First1\V1\PayByCase\PayByCase;

$paybycase->salesConfirmList($_POST['certifiedid']);
$paybycase = null;unset($paybycase);
##

//20250124 檢查案件回饋金是否有異常
$checkFeedbackMoney = new First1\V1\CheckFeedbackMoney\CheckFeedbackMoney;
$checkFeedbackMoney->check($_POST['certifiedid']);
$checkFeedbackMoney = null;unset($checkFeedbackMoney);
##

//20250303 標記仲介案件回饋
$confirmFeedback = new First1\V1\ConfirmFeedback\ConfirmFeedback;
$confirmFeedback->salesConfirmList($_POST['certifiedid'], $data_realestate['cBranchNum'], $data_realestate['cBranchNum1'], $data_realestate['cBranchNum2'], $data_realestate['cBranchNum3'], $_POST['scrivener_id']);
$confirmFeedback = null;unset($confirmFeedback);
##

//20250410 執行單案件總部回饋計算
$log = dirname(dirname(__DIR__)) . '/log/escrow/shell';
if (! is_dir($log)) {
    mkdir($log, 0777, true);
}
$log .= '/contractSave_' . date('Ymd') . '.log';

$data = json_encode(['certifiedId' => $id], JSON_UNESCAPED_UNICODE);
$data = base64_encode($data);

$cmd = '/usr/bin/php -f ' . FIRST198 . '/sales/setBranchHQFeedback.php ' . $data . ' > /dev/null 2>&1 &';
shell_exec($cmd);
file_put_contents($log, date('Y-m-d H:i:s') . ' ' . $cmd . PHP_EOL, FILE_APPEND);
##

echo "儲存完成";
