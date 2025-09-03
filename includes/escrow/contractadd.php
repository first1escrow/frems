<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
require_once dirname(dirname(__DIR__)) . '/class/sms.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/includes/writelog.php';
require_once dirname(dirname(__DIR__)) . '/bank/report/calTax.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

require_once dirname(__DIR__) . '/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/lib.php';

require_once __DIR__ . '/appraisal.php';

$contract = new Contract();
$sms      = new Sms();

$tlog = new TraceLog();
$tlog->insertWrite($_SESSION['member_id'], json_encode($_POST), '案件新增儲存');

/* 日期轉換 */
$_POST["case_signdate"]    = date_convert($_POST["case_signdate"]);
$_POST["case_finishdate"]  = date_convert($_POST["case_finishdate"]);
$_POST["case_finishdate2"] = date_convert($_POST["case_finishdate2"]);
$_POST["case_affixdate"]   = date_convert($_POST["case_affixdate"]);
$_POST["case_firstdate"]   = date_convert($_POST["case_firstdate"]);

if ($_POST['rent_rentdate'] != '') {
    $_POST['rent_rentdate'] = date_convert($_POST['rent_rentdate']);
}

$certified_id = substr(trim(addslashes($_POST['scrivener_bankaccount'])), -9);

$contract->AddContract($_POST);

//改成已簽約時間抓取業務 (暫時改回來)
if (! empty($_POST['realestate_branch'])) {
    $i = 0;

    if ($i == 0) {
        if ($_POST['realestate_branch'] == 505 || $_POST['cFeedbackTarget'] == 2) {
            //地政士業務
            $sql = 'SELECT
                        a.sId,
                        a.sSales AS Sales,
                        (SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
                        b.sOffice
                    FROM
                        tScrivenerSales AS a,
                        tScrivener AS b
                    WHERE
                        a.sScrivener=' . $_POST['scrivener_id'] . ' AND
                        b.sId=a.sScrivener
                    ORDER BY
                        sId
                    ASC';
        } else {
            $sql = 'SELECT
                        a.bId,
                        a.bSales AS Sales,
                        (SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
                        b.bName,
                        b.bStore
                    FROM
                        tBranchSales AS a,
                        tBranch AS b
                    WHERE
                        bBranch=' . $_POST['realestate_branch'] . ' AND
                        b.bId=a.bBranch
                    ORDER BY
                        bId
                    ASC';
        }
        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $contract->AddContract_Sales($_POST['scrivener_bankaccount'], $_POST['cFeedbackTarget'], $rs->fields['Sales'], $_POST['realestate_branch']);
            write_log('程式帶' . $_POST['scrivener_bankaccount'] . ':target' . $_POST['cFeedbackTarget'] . ",sales" . $rs->fields['sSales'] . ",branch" . $_POST['realestate_branch'] . "," . $sql, 'escrowSalse');

            $i++;
            $rs->MoveNext();
        }
    }
}

if ($_POST['realestate_branch2'] > 0) {
    $i = 0;
    if ($i == 0) {
        if ($_POST['realestate_branch2'] == 505 || $_POST['cFeedbackTarget2'] == 2) {
            //地政士業務
            $sql = 'SELECT
                        a.sId,
                        a.sSales AS Sales,
                        (SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
                        b.sOffice
                    FROM
                        tScrivenerSales AS a,
                        tScrivener AS b
                    WHERE
                        a.sScrivener=' . $_POST['scrivener_id'] . ' AND
                        b.sId=a.sScrivener
                    ORDER BY
                        sId
                    ASC';
        } else {
            $sql = 'SELECT
                        a.bId,
                        a.bSales AS Sales,
                        (SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
                        b.bName,
                        b.bStore
                    FROM
                        tBranchSales AS a,
                        tBranch AS b
                    WHERE
                        bBranch=' . $_POST['realestate_branch2'] . ' AND
                        b.bId=a.bBranch

                    ORDER BY
                        bId
                    ASC';
        }
        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $contract->AddContract_Sales($_POST['scrivener_bankaccount'], $_POST['cFeedbackTarget2'], $rs->fields['Sales'], $_POST['realestate_branch2']);
            write_log('程式帶' . $_POST['scrivener_bankaccount'] . ':target' . $_POST['cFeedbackTarget2'] . ",sales" . $rs->fields['Sales'] . ",branch" . $_POST['realestate_branch2'], 'escrowSalse');

            $i++;
            $rs->MoveNext();
        }
    }

}

if ($_POST['realestate_branch3'] > 0) {
    $i = 0;

    if ($i == 0) {
        if ($_POST['realestate_branch3'] == 505 || $_POST['cFeedbackTarget3'] == 2) {
            //地政士業務
            $sql = 'SELECT
						a.sId,
						a.sSales AS Sales,
						(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
						b.sOffice
					FROM
						tScrivenerSales AS a,
						tScrivener AS b
					WHERE
						a.sScrivener=' . $_POST['scrivener_id'] . ' AND
						b.sId=a.sScrivener
					ORDER BY
						sId
					ASC';
        } else {
            $sql = 'SELECT
                        a.bId,
                        a.bSales AS Sales,
                        (SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
                        b.bName,
                        b.bStore
                    FROM
                        tBranchSales AS a,
                        tBranch AS b
                    WHERE
                        bBranch=' . $_POST['realestate_branch3'] . ' AND
                        b.bId=a.bBranch

                    ORDER BY
                        bId
                    ASC';
        }
        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $contract->AddContract_Sales($_POST['scrivener_bankaccount'], $_POST['cFeedbackTarget3'], $rs->fields['Sales'], $_POST['realestate_branch3']);
            write_log('程式帶' . $_POST['scrivener_bankaccount'] . ':target' . $_POST['cFeedbackTarget3'] . ",sales" . $rs->fields['Sales'] . ",branch" . $_POST['realestate_branch3'], 'escrowSalse');

            $i++;
            $rs->MoveNext();
        }
    }
}
$store = $type = null;
unset($store, $type);
##

//契約書用印仲介店
if ($_POST['cAffixBranch']) {
    $checkAffix = $_POST['cAffixBranch'];

    $_POST['cAffixBranch']  = ($checkAffix == 'b') ? '1' : '0';
    $_POST['cAffixBranch1'] = ($checkAffix == 'b1') ? '1' : '0';

    $checkAffix = null;unset($checkAffix);
}
##

//20231030 轉換土地使用分區
$land_category_options = $contract->GetCategoryAreaMenuList();
if (isset($_POST['land_category']) && is_string($_POST['land_category'])) {
    foreach ($land_category_options as $v) {
        if (is_array($v) && isset($v['cName']) && $_POST['land_category'] == $v['cName']) {
            $_POST['land_category'] = $v['cId'];
        }
    }
}
$land_category_options = null;unset($land_category_options);

$contract->AddRealstate($_POST);
$contract->AddScrivener($_POST);
$contract->AddLand($_POST, 0);

//#2963
for ($i = 0; $i < count($_POST['land_movedate']); $i++) {
    $data['cCertifiedId'] = $certified_id;
    $data['cLandItem']    = 0;
    $data['cItem']        = $i;
    $data['cMoveDate']    = date_convert($_POST['land_movedate'][$i]) . "-00";
    $data['cLandPrice']   = str_replace(',', '', $_POST['land_landprice'][$i]);
    $data['cPower1']      = $_POST['land_power1'][$i];
    $data['cPower2']      = $_POST['land_power2'][$i];

    $contract->addLandPrice($data);
}

$contract->AddIncome($_POST);
$contract->AddExpenditure($_POST);
$contract->AddInvoice($_POST);
$contract->AddOwner($_POST);
$contract->AddBuyer($_POST);
$contract->AddContractFurniture($_POST);
$contract->AddContractAscription($_POST);
$contract->AddContractRent($_POST);
$contract->AddlandCategoryLand($_POST);

$cid = $contract->CutToCertifyId($_POST['scrivener_bankaccount']);

//取出地政士的預設紀錄
$scid = trim(addslashes($_POST['scrivener_id']));

$sql = 'SELECT sMobile,sDefault,sSend,sName FROM tScrivenerSms WHERE sScrivener="' . $scid . '" AND sDel = 0  ORDER BY sNID,sId ASC;';
$rs  = $conn->Execute($sql);

$smsTarget = [];
while (! $rs->EOF) {
    $tmp = $rs->fields;
    if ($tmp['sDefault'] == 1) {
        $smsTarget[] = $tmp['sMobile'];
        $name[]      = $tmp['sName'];
    }

    if ($tmp['sSend'] == 1) {
        $send[]  = $tmp['sMobile'];
        $name2[] = $tmp['sName'];
    }

    $tmp = null;unset($tmp);

    $i++;
    $rs->MoveNext();
}
##

//複製到案件的預設簡訊對象
if (count($smsTarget) > 0) {
    $_conn = new first1DB();

    $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . @implode(',', $smsTarget) . '",cSmsTargetName="' . @implode(',', $name) . '",cSend2 = "' . @implode(',', $send) . '",cSendName2="' . @implode(',', $name2) . '" WHERE cCertifiedId="' . $certified_id . '" AND cScrivener="' . $scid . '";';
    $_conn->exeSql($sql);

    $_conn = null;unset($_conn);
}
##

//仲介有代書回饋就不特殊回饋
##特殊回饋金計算

//土增稅
if ($_POST['changeLand'] == 1) {
    $cal = calCase($certified_id);

    $sql = "UPDATE tContractIncome SET cAddedTaxMoney = '" . $cal . "' WHERE cCertifiedId = '" . $certified_id . "'";
    $conn->Execute($sql);
}

##建物新增
$_POST['property_Item']            = 0;
$_POST['property_budmaterial']     = $_POST['property_budmaterial0'];
$_POST['property_builddate']       = date_convert($_POST['property_builddate0']);
$_POST['property_levelnow']        = $_POST['property_levelnow0'];
$_POST['property_levelhighter']    = $_POST['property_levelhighter0'];
$_POST['property_zip']             = $_POST['property_zip0'];
$_POST['property_addr']            = $_POST['property_addr0'];
$_POST['property_objkind']         = $_POST['property_objkind0'];
$_POST['property_cIsOther']        = $_POST['property_cIsOther0'];
$_POST['property_cOther']          = $_POST['property_cOther0'];
$_POST['property_buildage']        = $_POST['property_buildage0'];
$_POST['property_closingday']      = date_convert($_POST['property_closingday0']);
$_POST['property_room']            = $_POST['property_room0'];
$_POST['property_parlor']          = $_POST['property_parlor0'];
$_POST['property_toilet']          = $_POST['property_toilet0'];
$_POST['property_hascar']          = $_POST['property_hascar0'];
$_POST['property_measuretotal']    = $_POST['property_measuretotal0'];
$_POST['property_buildno']         = $_POST['property_buildno0'];
$_POST['property_housetown']       = $_POST['property_housetown0'];
$_POST['property_rentdate']        = date_convert($_POST['property_rentdate0']);
$_POST['property_rent']            = $_POST['property_rent0'];
$_POST['property_finish']          = $_POST['property_finish0'];
$_POST['property_cObjectOther']    = $_POST['property_cObjectOther0'];
$_POST['property_objuse']          = $_POST['property_objuse0'];
$_POST['property_cPropertyObject'] = $_POST['property_cPropertyObject0'];
$_POST['property_budmaterial']     = $_POST['property_budmaterial0'];

$contract->AddProperty($_POST);
$contract->AddProperty2($_POST);

//20220725 記錄呼叫一銀貸款成數API案件
$appraisal = new Appraisal;
$appraisal->registerCase($certified_id);
$appraisal = null;unset($appraisal);
##

//其他回饋金
$_POST['certifiedid'] = $certified_id;
##

//群義編號
$sql = "UPDATE tBankCode SET bNo72 = '" . $_POST['data_bankcode_No72'] . "' WHERE bAccount = '" . $data_case['cEscrowBankAccount'] . "'";
$conn->Execute($sql);
##

//埋log紀錄
write_log($_POST['certifiedid'] . '新增案件,' . $_POST['realestate_branch'] . '-' . $_POST['cCaseFeedback'] . ',' . $_POST['cCaseFeedBackMoney'] . ',' . $_POST['cFeedbackTarget'] . ';' . $_POST['realestate_branch1'] . '-' . $_POST['cCaseFeedback1'] . ',' . $_POST['cCaseFeedBackMoney1'] . ',' . $_POST['cFeedbackTarget1'] . ';' . $_POST['realestate_branch2'] . '-' . $_POST['cCaseFeedback2'] . ',' . $_POST['cCaseFeedBackMoney2'] . ',' . $_POST['cFeedbackTarget2'], 'escrowSave');
##

//20230323 判定通知業務是否審核
$paybycase = new First1\V1\PayByCase\PayByCase;

$paybycase->salesConfirmList($certified_id);
$paybycase = null;unset($paybycase);
##

//20250410 執行單案件總部回饋計算
$log = dirname(dirname(__DIR__)) . '/log/escrow/shell';
if (! is_dir($log)) {
    mkdir($log, 0777, true);
}
$log .= '/contractAdd_' . date('Ymd') . '.log';

$data = json_encode(['certifiedId' => $certified_id], JSON_UNESCAPED_UNICODE);
$data = base64_encode($data);

$cmd = '/usr/bin/php -f ' . FIRST198 . '/sales/setBranchHQFeedback.php ' . $data . ' > /dev/null 2>&1 &';
shell_exec($cmd);
file_put_contents($log, date('Y-m-d H:i:s') . ' ' . $cmd . PHP_EOL, FILE_APPEND);
##

echo "儲存完成";
