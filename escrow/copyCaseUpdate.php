<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/bank/report/calTax.php';
require_once dirname(__DIR__) . '/first1DB.php';

$_POST = escapeStr($_POST);

$contract  = new Contract();
$brand     = new brand();
$scrivener = new Scrivener();
$tlog      = new TraceLog();

$tlog->insertWrite($_SESSION['member_id'], json_encode($_POST), '案件複製儲存');

##
$oldCertifiedId = $_POST['oCertifiedId']; //要複製的保證號碼
$newCertifiedId = substr($_POST['scrivener_bankaccount'], -9); //保證號碼
$zip            = $_POST['zip'];
$addr           = $_POST['addr'];
$edit           = $_POST['edit'];

$sql = "SELECT bUsed,bId,bSID FROM tBankCode WHERE bAccount = '" . $_POST['scrivener_bankaccount'] . "'";
$rs  = $conn->Execute($sql);
$scrivenerId = $rs->fields['bSID'];

##標記使用前先檢查
if ($edit == 0) {

    if ($rs->fields['bUsed'] == '1') {
        die("已被使用過");
    }

    $fw = fopen('/home/httpd/html/first.twhg.com.tw/log2/copyCase.log', 'a+');
    fwrite($fw, $newCertifiedId . "複製" . $oldCertifiedId . "的案件\r\n");
    fclose($fw);

    ##保證號碼所屬銀行
    $bankcode = substr($_POST['scrivener_bankaccount'], 0, 5);

    $sql = "SELECT cBankCode FROM tContractBank WHERE cBankVR LIKE '" . $bankcode . "%'";
    $rs  = $conn->Execute($sql);

    $bankcode = $rs->fields['cBankCode'];

    ##
    $scr = $scrivener->GetScrivenerInfo($scrivenerId);
    ##
    //先新增
    $_POST['case_applydate']    = date('Y-m-d H:i:s');
    $_POST['case_bank']         = $bankcode;
    $_POST['scrivener_id']      = $scrivenerId;
    $_POST['sRecall']           = $scr['sRecall'];
    $_POST['case_status']       = 2;
    $_POST['cFeedbackTarget']   = 1;
    $_POST['cFeedbackTarget1']  = 1;
    $_POST['cFeedbackTarget2']  = 1;
    $_POST['cServiceTarget']    = 1;
    $_POST['cServiceTarget1']   = 1;
    $_POST['cServiceTarget2']   = 1;
    $_POST['case_undertakerid'] = $_SESSION['member_id'];

    $contract->AddContract($_POST);
    $contract->AddRealstate($_POST);
    $contract->AddScrivener($_POST);
    $contract->AddLand($_POST, 0);
    $contract->AddProperty($_POST);
    $contract->AddIncome($_POST);
    $contract->AddExpenditure($_POST);
    $contract->AddInvoice($_POST);
    $contract->AddOwner($_POST);
    $contract->AddBuyer($_POST);
    $contract->AddContractFurniture($_POST);
    $contract->AddContractAscription($_POST);
    $contract->AddContractRent($_POST);
    $contract->AddProperty($_POST);

    //地政士特殊回饋比率
    $sql = "UPDATE tContractCase SET cScrivenerSpRecall =" . $scr['sSpRecall'] . " WHERE cCertifiedId='" . $newCertifiedId . "'";
    $conn->Execute($sql);

    //地政士簡訊對象
    $sql = 'SELECT sMobile,sDefault,sSend,sName FROM tScrivenerSms WHERE sScrivener="' . $scrivenerId . '" AND sDel = 0  ORDER BY sNID,sId ASC;';
    $rs  = $conn->Execute($sql);

    $smsTarget = array();
    while (!$rs->EOF) {
        $tmp = $rs->fields;
        if ($tmp['sDefault'] == 1) {
            $smsTarget[] = $tmp['sMobile'];
            $name[]      = $tmp['sName'];
        }

        if ($tmp['sSend'] == 1) {
            $send[]  = $tmp['sMobile'];
            $name2[] = $tmp['sName'];
        }

        unset($tmp);

        $i++;
        $rs->MoveNext();
    }

    //複製到案件的預設簡訊對象
    if (count($smsTarget) > 0) {
        $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . @implode(',', $smsTarget) . '",cSmsTargetName="' . @implode(',', $name) . '",cSend2 = "' . @implode(',', $send) . '",cSendName2="' . @implode(',', $name2) . '" WHERE cCertifiedId="' . $certified_id . '" AND cScrivener="' . $scid . '";';

        $_conn = new first1DB();
        $_conn->exeSql($sql);
        $_conn = null;unset($_conn);
    }
    ##
}
##

$check  = 0; //檢查是否要算回饋
$check2 = 0; //檢查是否有正常複製

for ($i = 0; $i < count($_POST['cat']); $i++) {
    switch ($_POST['cat'][$i]) {
        case '1': //案件明細表(只複製客服內容)[作廢]
            break;
        case '2': //合約書
            ContractCase($oldCertifiedId, $newCertifiedId);
            ContractIncome($oldCertifiedId, $newCertifiedId);
            $check++;
            break;
        case '3': //土地
            ContractLand($oldCertifiedId, $newCertifiedId);
            break;
        case '4': //建物
            ContractProperty($oldCertifiedId, $newCertifiedId, $zip, $addr);
            break;
        case '5': //地政士 [作廢]
            # code...
            break;
        case '6': //仲介
            ContractRealestate($oldCertifiedId, $newCertifiedId, $scrivenerId);

            $check++;
            break;
        case '7': //買方
            ContractBuyer($oldCertifiedId, $newCertifiedId);
            ContractPhone($oldCertifiedId, $newCertifiedId, 'b');
            ContractOther($oldCertifiedId, $newCertifiedId, 'b');
            break;
        case '8': //賣方
            ContractOwner($oldCertifiedId, $newCertifiedId);
            ContractPhone($oldCertifiedId, $newCertifiedId, 'o');
            ContractOther($oldCertifiedId, $newCertifiedId, 'o');
            break;
        default:
            break;
    }
}

//回饋金
getFeedMoney('c', $newCertifiedId);

//20230323 判定通知業務是否審核
$paybycase = new First1\V1\PayByCase\PayByCase;

$paybycase->salesConfirmList($newCertifiedId);
$paybycase = null;unset($paybycase);
##

echo $newCertifiedId;
exit;

function ContractCase($old, $new)
{
    global $conn;

    $sql = "SELECT * FROM tContractCase WHERE cCertifiedId = '" . $old . "'";
    $rs  = $conn->Execute($sql);

    $sql = "UPDATE
                tContractCase
            SET
                cSignDate = '" . $rs->fields['cSignDate'] . "',
                cFinishDate = '" . $rs->fields['cFinishDate'] . "',
                cFinishDate2 = '" . $rs->fields['cFinishDate2'] . "',
                cFirstDate = '" . $rs->fields['cFirstDate'] . "'
            WHERE
                cCertifiedId = '" . $new . "'";
    $conn->Execute($sql);
}

function ContractIncome($old, $new)
{
    global $conn;

    $sql = "SELECT * FROM tContractIncome WHERE cCertifiedId = '" . $old . "'";
    $rs  = $conn->Execute($sql);

    $data = $rs->fields;

    if ($data['cCertifiedId']) {
        $sql = "UPDATE
                    tContractIncome
                SET
                    cBankLoan = '" . $data['cBankLoan'] . "',
                    cPayCash = '" . $data['cPayCash'] . "',
                    cPayTicket = '" . $data['cPayTicket'] . "',
                    cPayCommercialPaper = '" . $data['cPayCommercialPaper'] . "',
                    cLoanMoney = '" . $data['cLoanMoney'] . "',
                    cSignMoney = '" . $data['cSignMoney'] . "',
                    cAffixMoney = '" . $data['cAffixMoney'] . "',
                    cDutyMoney = '" . $data['cDutyMoney'] . "',
                    cEstimatedMoney = '" . $data['cEstimatedMoney'] . "',
                    cTotalMoney = '" . $data['cTotalMoney'] . "',
                    cCertifiedMoney = '" . $data['cCertifiedMoney'] . "',
                    cFirstMoney = '" . $data['cFirstMoney'] . "',
                    cDepositMoney = '" . $data['cDepositMoney'] . "',
                    cParking = '" . $data['cParking'] . "',
                    cSrivenerMoney = '" . $data['cSrivenerMoney'] . "',
                    cNotIntoMoney = '" . $data['cNotIntoMoney'] . "'
                WHERE
                    cCertifiedId = '" . $new . "'
                ";
        $conn->Execute($sql);
    }
}

function ContractProperty($old, $new, $zip, $addr)
{
    global $conn;

    $sql = "DELETE FROM tContractProperty WHERE cCertifiedId = '" . $new . "' AND cItem != 0"; //
    $conn->Execute($sql);

    $sql = "DELETE FROM tContractPropertyObject WHERE cCertifiedId = '" . $new . "'";
    $conn->Execute($sql);

    $sql = "SELECT * FROM tContractProperty WHERE cCertifiedId = '" . $old . "' ORDER bY cItem ASC";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        if ($addr) {
            if ($addr == $rs->fields['cAddr']) {
                $tmp[] = $rs->fields;
            } else {
                $tmp2[] = $rs->fields;
            }
        } else {
            $data[] = $rs->fields;
        }

        $rs->MoveNext();
    }
    if ($addr) {
        $data = (is_array($tmp2)) ? array_merge($tmp, $tmp2) : $tmp;
        unset($tmp);unset($tmp2);
    }

    for ($i = 0; $i < count($data); $i++) {
        $sql = '';

        if ($i == 0) {
            $sql = "UPDATE
                        tContractProperty
                    SET
                        cBudMaterial = '" . $data[$i]['cBudMaterial'] . "',
                        cPropertyObject = '" . $data[$i]['cPropertyObject'] . "',
                        cObjectOther = '" . $data[$i]['cObjectOther'] . "',
                        cBudMaterial = '" . $data[$i]['cBudMaterial'] . "',
                        cBuildDate = '" . $data[$i]['cBuildDate'] . "',
                        cLevelNow = '" . $data[$i]['cLevelNow'] . "',
                        cLevelHighter = '" . $data[$i]['cLevelHighter'] . "',
                        cLevelUse = '" . $data[$i]['cLevelUse'] . "',
                        cZip = '" . $data[$i]['cZip'] . "',
                        cAddr = '" . $data[$i]['cAddr'] . "',
                        cBuildNo = '" . $data[$i]['cBuildNo'] . "',
                        cTownHouse = '" . $data[$i]['cTownHouse'] . "',
                        cObjKind = '" . $data[$i]['cObjKind'] . "',
                        cObjUse = '" . $data[$i]['cObjUse'] . "',
                        cIsOther = '" . $data[$i]['cIsOther'] . "',
                        cOther = '" . $data[$i]['cOther'] . "',
                        cBuildAge = '" . $data[$i]['cBuildAge'] . "',
                        cClosingDay = '" . $data[$i]['cClosingDay'] . "',
                        cRoom = '" . $data[$i]['cRoom'] . "',
                        cParlor = '" . $data[$i]['cParlor'] . "',
                        cToilet = '" . $data[$i]['cToilet'] . "',
                        cHasCar = '" . $data[$i]['cHasCar'] . "',
                        cMeasureMain = '" . $data[$i]['cMeasureMain'] . "',
                        cMeasureExt = '" . $data[$i]['cMeasureExt'] . "',
                        cMeasureCommon = '" . $data[$i]['cMeasureCommon'] . "',
                        cMeasureTotal = '" . $data[$i]['cMeasureTotal'] . "',
                        cCategory = '" . $data[$i]['cCategory'] . "',
                        cPower1 = '" . $data[$i]['cPower1'] . "',
                        cPower2 = '" . $data[$i]['cPower2'] . "',
                        cPublicMeasureTotal = '" . $data[$i]['cPublicMeasureTotal'] . "',
                        cPublicMeasureMain = '" . $data[$i]['cPublicMeasureMain'] . "',
                        cPublicMeasureMain2 = '" . $data[$i]['cPublicMeasureMain2'] . "'
                    WHERE
                        cItem = '" . $i . "' AND cCertifiedId = '" . $new . "'";
        } else {
            $sql = "INSERT INTO
                 tContractProperty
                (
                        cId,
                        cBudMaterial,
                        cCertifiedId,
                        cItem,
                        cPropertyId,
                        cContractNumber,
                        cBuildDate,
                        cLevelNow,
                        cLevelHighter,
                        cLevelUse,
                        cZip,
                        cAddr,
                        cBuildNo,
                        cTownHouse,
                        cObjKind,
                        cObjUse,
                        cIsOther,
                        cOther,
                        cBuildAge,
                        cClosingDay,
                        cRoom,
                        cParlor,
                        cToilet,
                        cHasCar,
                        cMeasureMain,
                        cMeasureExt,
                        cMeasureCommon,
                        cMeasureTotal,
                        cCategory,
                        cPower1,
                        cPower2,
                        cPublicMeasureTotal,
                        cPublicMeasureMain,
                        cPublicMeasureMain2,
                        cCooperationBrand,
                        cCooperationBranch,
                        cRentDate,
                        cRent,
                        cFinish,
                        cPropertyObject,
                        cObjectOther
                )
            SELECT (
                    SELECT max(cId) FROM tContractProperty)+1,
                        cBudMaterial,
                        '" . $new . "',
                        '" . $i . "',
                        cPropertyId,
                        cContractNumber,
                        cBuildDate,
                        cLevelNow,
                        cLevelHighter,
                        cLevelUse,
                        cZip,
                        cAddr,
                        cBuildNo,
                        cTownHouse,
                        cObjKind,
                        cObjUse,
                        cIsOther,
                        cOther,
                        cBuildAge,
                        cClosingDay,
                        cRoom,
                        cParlor,
                        cToilet,
                        cHasCar,
                        cMeasureMain,
                        cMeasureExt,
                        cMeasureCommon,
                        cMeasureTotal,
                        cCategory,
                        cPower1,
                        cPower2,
                        cPublicMeasureTotal,
                        cPublicMeasureMain,
                        cPublicMeasureMain2,
                        cCooperationBrand,
                        cCooperationBranch,
                        cRentDate,
                        cRent,
                        cFinish,
                        cPropertyObject,
                        cObjectOther
                    FROM
                        tContractProperty WHERE cCertifiedId='" . $data[$i]['cCertifiedId'] . "' AND cItem = '" . $data[$i]['cItem'] . "'";
        }
        $conn->Execute($sql);

        ContractPropertyObject($old, $new, $data[$i]['cItem'], $i);
    }
}

function ContractPropertyObject($old, $new, $oldItem, $newItem)
{
    global $conn;

    $sql = "SELECT * FROM tContractPropertyObject WHERE cCertifiedId = '" . $old . "' AND cBuildItem = '" . $oldItem . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[] = $rs->fields;
        $rs->MoveNext();
    }

    for ($i = 0; $i < count($data); $i++) {
        $sql = "INSERT INTO
                    tContractPropertyObject
                    (
                        cId,
                        cCertifiedId,
                        cBuildItem,
                        cBuildNo,
                        cItem,
                        cCategory,
                        cLevelUse,
                        cMeasureMain,
                        cMeasureTotal,
                        cPower1,
                        cPower2
                    )
                SELECT
                    (SELECT max(cId) FROM tContractPropertyObject)+1,
                    '" . $new . "',
                    '" . $newItem . "',
                    cBuildNo,
                    cItem,
                    cCategory,
                    cLevelUse,
                    cMeasureMain,
                    cMeasureTotal,
                    cPower1,
                    cPower2
                FROM
                    tContractPropertyObject WHERE cId='" . $data[$i]['cId'] . "'";
        $conn->Execute($sql);
    }
}

function getCustomerBank($old, $new, $iden)
{
    global $conn;

    if ($iden != 1 && $iden != 2) {
        $str = " AND cId = '" . $iden . "'";
    } else {
        $str = " AND cIdentity = '" . $iden . "'";
    }

    $sql = "DELETE FROM tContractCustomerBank WHERE cCertifiedId = '" . $new . "'" . $str;
    $conn->Execute($sql);

    $sql = "SELECT
                *
            FROM
                tContractCustomerBank
            WHERE
                cCertifiedId = '" . $old . "'" . $str;
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[] = $rs->fields;
        $rs->MoveNext();
    }

    for ($i = 0; $i < count($data); $i++) {
        # code...
        $sql = "INSERT INTO
                    tContractCustomerBank
                    (
                        cId,
                        cCertifiedId,
                        cIdentity,
                        cBankMain,
                        cBankBranch,
                        cBankAccountNo,
                        cBankAccountName,
                        cOtherId,
                        cModify_time
                    )
                SELECT
                        (SELECT max(cId) FROM tContractCustomerBank)+1,
                        '" . $new . "',
                        cIdentity,
                        cBankMain,
                        cBankBranch,
                        cBankAccountNo,
                        cBankAccountName,
                        cOtherId,
                        '" . date('Y-m-d H:i:s') . "'
                    FROM
                        tContractCustomerBank WHERE cId='" . $data[$i]['cId'] . "'";
        $conn->Execute($sql);
    }
}

function ContractPhone($old, $new, $iden)
{
    global $conn;

    $cat = ($iden == 'b') ? 1 : 2;

    $sql = "DELETE FROM tContractPhone WHERE cIdentity = '" . $iden . "' AND cCertifiedId = '" . $new . "'";
    $conn->Execute($sql);

    $sql = "SELECT * FROM tContractPhone WHERE cIdentity = '" . $iden . "' AND cCertifiedId = '" . $old . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[] = $rs->fields;
        $rs->MoveNext();
    }

    for ($i = 0; $i < count($data); $i++) {
        # code...
        $sql = "INSERT INTO
                    tContractPhone
                    (
                        cId,
                        cCertifiedId,
                        cIdentity,
                        cMobileNum
                    )
                SELECT
                        (SELECT max(cId) FROM tContractPhone)+1,
                        '" . $new . "',
                        cIdentity,
                        cMobileNum
                    FROM
                        tContractPhone WHERE cId='" . $data[$i]['cId'] . "'";
        $conn->Execute($sql);
    }
}

function ContractOwner($old, $new)
{
    global $conn;

    $sql  = "SELECT * FROM tContractOwner WHERE cCertifiedId = '" . $old . "'";
    $rs   = $conn->Execute($sql);
    $data = $rs->fields;

    $sql = "UPDATE
                tContractOwner
            SET
                cIdentifyId = '" . $data['cIdentifyId'] . "',
                cCategoryIdentify = '" . $data['cCategoryIdentify'] . "',
                cName = '" . $data['cName'] . "',
                cCountryCode = '" . $data['cCountryCode'] . "',
                cPassport = '" . $data['cPassport'] . "',
                cTaxtreatyCode = '" . $data['cTaxtreatyCode'] . "',
                cResidentLimit = '" . $data['cResidentLimit'] . "',
                cPaymentDate = '" . $data['cPaymentDate'] . "',
                cNHITax = '" . $data['cNHITax'] . "',
                sAgentName1 = '" . $data['sAgentName1'] . "',
                sAgentName2 = '" . $data['sAgentName2'] . "',
                sAgentName3 = '" . $data['sAgentName3'] . "',
                sAgentName4 = '" . $data['sAgentName4'] . "',
                sAgentMobile1 = '" . $data['sAgentMobile1'] . "',
                sAgentMobile2 = '" . $data['sAgentMobile2'] . "',
                sAgentMobile3 = '" . $data['sAgentMobile3'] . "',
                sAgentMobile4 = '" . $data['sAgentMobile4'] . "',
                cBirthdayDay = '" . $data['cBirthdayDay'] . "',
                cContactName = '" . $data['cContactName'] . "',
                cMobileNum = '" . $data['cMobileNum'] . "',
                cTelArea1 = '" . $data['cTelArea1'] . "',
                cTelMain1 = '" . $data['cTelMain1'] . "',
                cTelExt1 = '" . $data['cTelExt1'] . "',
                cTelArea2 = '" . $data['cTelArea2'] . "',
                cTelMain2 = '" . $data['cTelMain2'] . "',
                cTelExt2 = '" . $data['cTelExt2'] . "',
                cRegistZip = '" . $data['cRegistZip'] . "',
                cRegistAddr = '" . $data['cRegistAddr'] . "',
                cBaseZip = '" . $data['cBaseZip'] . "',
                cBaseAddr = '" . $data['cBaseAddr'] . "',
                cBankKey = '" . $data['cBankKey'] . "',
                cBankKey2 = '" . $data['cBankKey2'] . "',
                cBankBranch = '" . $data['cBankBranch'] . "',
                cBankBranch2 = '" . $data['cBankBranch2'] . "',
                cBankAccName = '" . $data['cBankAccName'] . "',
                cBankAccNumber = '" . $data['cBankAccNumber'] . "',
                cOtherName = '" . $data['cOtherName'] . "'
            WHERE
                cCertifiedId = '" . $new . "'";
    $conn->Execute($sql);

    getCustomerBank($old, $new, 2);
}

function ContractOther($old, $new, $iden)
{
    global $conn;

    if ($iden == 'b') { //1買2賣3仲介4代書5買方登記名義人6買方代理人7賣方代理人
        $str = '1,5,6';
    } elseif ($iden == 'o') {
        $str = '2,7';
    } else {
        die('ErrorContractOther');
    }

    $sql = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $old . "' AND cIdentity IN (" . $str . ")";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[] = $rs->fields;
        $rs->MoveNext();
    }

    //20230206 新增刪除覆蓋既有的賣方資料
    // if (!empty($data)) {
    $sql = "DELETE FROM tContractOthers WHERE cCertifiedId = '" . $new . "' AND cIdentity IN (" . $str . ")";
    $conn->Execute($sql);
    // }
    ##

    for ($i = 0; $i < count($data); $i++) {
        $sql = "INSERT INTO
                    tContractOthers
                    (
                        cId,
                        cCertifiedId,
                        cIdentity,
                        cIdentifyId,
                        cName,
                        cBirthdayDay,
                        cTarget,
                        cCountryCode,
                        cPassport,
                        cTaxTreatyCode,
                        cResidentLimit,
                        cPaymentDate,
                        cNHITax,
                        cMobileNum,
                        cRegistZip,
                        cRegistAddr,
                        cBaseZip,
                        cBaseAddr,
                        cBankMain,
                        cBankBranch,
                        cBankAccName,
                        cBankAccNum,
                        cInvoiceMoney,
                        cInvoiceDonate,
                        cInterestMoney,
                        cLoginTime,
                        cLastModify,
                        cIPSource,
                        cTax1,
                        cTax2,
                        cOtherName,
                        cInvoicePrint
                    )
                SELECT
                        (SELECT max(cId) FROM tContractOthers)+1,
                        '" . $new . "',
                        cIdentity,
                        cIdentifyId,
                        cName,
                        cBirthdayDay,
                        cTarget,
                        cCountryCode,
                        cPassport,
                        cTaxTreatyCode,
                        cResidentLimit,
                        cPaymentDate,
                        cNHITax,
                        cMobileNum,
                        cRegistZip,
                        cRegistAddr,
                        cBaseZip,
                        cBaseAddr,
                        cBankMain,
                        cBankBranch,
                        cBankAccName,
                        cBankAccNum,
                        cInvoiceMoney,
                        cInvoiceDonate,
                        cInterestMoney,
                        '0000-00-00 00:00:00',
                        cLastModify,
                        cIPSource,
                        cTax1,
                        cTax2,
                        cOtherName,
                        cInvoicePrint
                    FROM
                        tContractOthers
                    WHERE
                        cId='" . $data[$i]['cId'] . "'";
        $conn->Execute($sql);

        $sql = "SELECT cId FROM tContractOthers WHERE cCertifiedId = '" . $new . "' ORDER bY cId DESC LIMIT 1";
        $rs  = $conn->Execute($sql);

        getCustomerBank($old, $new, $rs->fields['cId']);
    }
}

function ContractBuyer($old, $new)
{
    global $conn;

    $sql  = "SELECT * FROM tContractBuyer WHERE cCertifiedId = '" . $old . "'";
    $rs   = $conn->Execute($sql);
    $data = $rs->fields;

    $sql = "UPDATE
                tContractBuyer
            SET
                cIdentifyId = '" . $data['cIdentifyId'] . "',
                cCategoryIdentify = '" . $data['cCategoryIdentify'] . "',
                cName = '" . $data['cName'] . "',
                cCountryCode = '" . $data['cCountryCode'] . "',
                cPassport = '" . $data['cPassport'] . "',
                cTaxtreatyCode = '" . $data['cTaxtreatyCode'] . "',
                cResidentLimit = '" . $data['cResidentLimit'] . "',
                cPaymentDate = '" . $data['cPaymentDate'] . "',
                cNHITax = '" . $data['cNHITax'] . "',
                sAgentName1 = '" . $data['sAgentName1'] . "',
                sAgentName2 = '" . $data['sAgentName2'] . "',
                sAgentName3 = '" . $data['sAgentName3'] . "',
                sAgentName4 = '" . $data['sAgentName4'] . "',
                sAgentMobile1 = '" . $data['sAgentMobile1'] . "',
                sAgentMobile2 = '" . $data['sAgentMobile2'] . "',
                sAgentMobile3 = '" . $data['sAgentMobile3'] . "',
                sAgentMobile4 = '" . $data['sAgentMobile4'] . "',
                cBirthdayDay = '" . $data['cBirthdayDay'] . "',
                cContactName = '" . $data['cContactName'] . "',
                cMobileNum = '" . $data['cMobileNum'] . "',
                cTelArea1 = '" . $data['cTelArea1'] . "',
                cTelMain1 = '" . $data['cTelMain1'] . "',
                cTelExt1 = '" . $data['cTelExt1'] . "',
                cTelArea2 = '" . $data['cTelArea2'] . "',
                cTelMain2 = '" . $data['cTelMain2'] . "',
                cTelExt2 = '" . $data['cTelExt2'] . "',
                cRegistZip = '" . $data['cRegistZip'] . "',
                cRegistAddr = '" . $data['cRegistAddr'] . "',
                cBaseZip = '" . $data['cBaseZip'] . "',
                cBaseAddr = '" . $data['cBaseAddr'] . "',
                cBankKey = '" . $data['cBankKey'] . "',
                cBankKey2 = '" . $data['cBankKey2'] . "',
                cBankBranch = '" . $data['cBankBranch'] . "',
                cBankBranch2 = '" . $data['cBankBranch2'] . "',
                cBankAccName = '" . $data['cBankAccName'] . "',
                cBankAccNumber = '" . $data['cBankAccNumber'] . "',
                cAuthorized = '" . $data['cAuthorized'] . "',
                cOtherName = '" . $data['cOtherName'] . "'
            WHERE
                cCertifiedId = '" . $new . "'";
    $conn->Execute($sql);

    getCustomerBank($old, $new, 1);
}

function ContractRealestate($old, $new, $scrivenerId)
{
    global $conn, $brand;

    $sql = "SELECT * FROM tContractRealestate WHERE cCertifyId = '" . $old . "'";
    $rs  = $conn->Execute($sql);

    $data = $rs->fields;

    $branch  = $brand->GetBranch($rs->fields['cBranchNum']);
    $branch1 = $brand->GetBranch($rs->fields['cBranchNum1']);
    $branch2 = $brand->GetBranch($rs->fields['cBranchNum2']);
    $tmpb    = getScrivenerBrandRecall($scrivenerId, $branch[0]['bBrand']);
    $tmpb1   = getScrivenerBrandRecall($scrivenerId, $branch[0]['bBrand1']);
    $tmpb2   = getScrivenerBrandRecall($scrivenerId, $branch[0]['bBrand1']);

    $SmsTarget  = ($rs->fields['cBranchNum']) ? addSmsDefaultB($rs->fields['cBranchNum']) : '';
    $SmsTarget1 = ($rs->fields['cBranchNum1']) ? addSmsDefaultB($rs->fields['cBranchNum1']) : '';
    $SmsTarget2 = ($rs->fields['cBranchNum2']) ? addSmsDefaultB($rs->fields['cBranchNum2']) : '';

    $sql = "UPDATE
                `tContractRealestate`
            SET
                `cBrand` = '" . $branch[0]['bBrand'] . "',
                `cBrand1` = '" . $branch1[0]['bBrand'] . "',
                `cBrand2` = '" . $branch2[0]['bBrand'] . "',
                `cName` = '" . $branch[0]['bName'] . "',
                `cName1` = '" . $branch1[0]['bName'] . "',
                `cName2` = '" . $branch2[0]['bName'] . "',
                `cBranchNum` = '" . $rs->fields['cBranchNum'] . "',
                `cBranchNum1` = '" . $rs->fields['cBranchNum1'] . "',
                `cBranchNum2` = '" . $rs->fields['cBranchNum2'] . "',
                `cServiceTarget` = '" . $rs->fields['cServiceTarget'] . "',
                `cServiceTarget1` = '" . $rs->fields['cServiceTarget1'] . "',
                `cServiceTarget2` = '" . $rs->fields['cServiceTarget2'] . "',
                `cSerialNumber` = '" . $branch[0]['bSerialnum'] . "',
                `cSerialNumber1` = '" . $branch1[0]['bSerialnum'] . "',
                `cSerialNumber2` = '" . $branch2[0]['bSerialnum'] . "',
                `cSmsTarget` = '" . $SmsTarget . "',
                `cSmsTarget1` = '" . $SmsTarget1 . "',
                `cSmsTarget2` = '" . $SmsTarget2 . "',
                `cTelArea` = '" . $branch[0]['bTelArea'] . "',
                `cTelArea1` = '" . $branch1[0]['bTelArea'] . "',
                `cTelArea2` = '" . $branch2[0]['bTelArea'] . "',
                `cTelMain` = '" . $branch[0]['bTelMain'] . "',
                `cTelMain1` = '" . $branch1[0]['bTelMain'] . "',
                `cTelMain2` = '" . $branch2[0]['bTelMain'] . "',
                `cFaxArea` = '" . $branch[0]['bFaxArea'] . "',
                `cFaxArea1` = '" . $branch1[0]['bFaxArea'] . "',
                `cFaxArea2` = '" . $branch2[0]['bFaxArea'] . "',
                `cFaxMain` = '" . $branch[0]['bFaxMain'] . "',
                `cFaxMain1` = '" . $branch1[0]['bFaxMain'] . "',
                `cFaxMain2` = '" . $branch2[0]['bFaxMain'] . "',
                `cZip` = '" . $branch[0]['bZip'] . "',
                `cZip1` = '" . $branch1[0]['bZip'] . "',
                `cZip2` = '" . $branch2[0]['bZip'] . "',
                `cAddress` = '" . $branch[0]['bAddress'] . "',
                `cAddress1` = '" . $branch1[0]['bAddress'] . "',
                `cAddress2` = '" . $branch2[0]['bAddress'] . "'
            WHERE
                `cCertifyId` = '" . $new . "'";
    $conn->Execute($sql);

    //回饋比率
    $sql = "UPDATE
                    tContractCase
                SET
                    cBranchRecall = '" . $branch[0]['bRecall'] . "',
                    cBranchRecall1 = '" . $branch1[0]['bRecall'] . "',
                    cBranchRecall2 = '" . $branch2[0]['bRecall'] . "',
                    cBranchScrRecall = '" . $branch[0]['bScrRecall'] . "',
                    cBranchScrRecall1 = '" . $branch1[0]['bScrRecall'] . "',
                    cBranchScrRecall2 = '" . $branch2[0]['bScrRecall'] . "',
                    cBrandScrRecall = '" . $tmpb['sRecall'] . "',
                    cBrandScrRecall1 = '" . $tmpb1['sRecall1'] . "',
                    cBrandScrRecall2 = '" . $tmpb2['sRecall2'] . "',
                    cBrandRecall = '" . $tmpb['sReacllBrand'] . "',
                    cBrandRecall1 = '" . $tmpb['sReacllBrand1'] . "',
                    cBrandRecall2 = '" . $tmpb['sReacllBrand2'] . "'
                WHERE
                    `cCertifiedId` = '" . $new . "'";
    $conn->Execute($sql);

    //業務
    ContractSales($old, $new, $scrivenerId);
}

function ContractSales($old, $new, $scrivenerId)
{

    global $conn;
    global $contract;

    $data     = $contract->GetRealstate($new);
    $dataCase = $contract->GetContract($new);

    $sql = "DELETE FROM tContractSales WHERE cCertifiedId = '" . $new . "'";
    $conn->Execute($sql);

    if (!empty($data['cBranchNum'])) {
        if ($data['cBranchNum'] == 505 || $dataCase['cFeedbackTarget'] == 2) {
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
                    a.sScrivener=' . $scrivenerId . ' AND
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
                        bBranch=' . $data['cBranchNum'] . ' AND
                        b.bId=a.bBranch
                    ORDER BY
                        bId
                    ASC';
        }
        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $contract->AddContract_Sales($dataCase['cEscrowBankAccount'], $dataCase['cFeedbackTarget'], $rs->fields['Sales'], $data['cBranchNum']);
            write_log('程式帶' . $dataCase['cEscrowBankAccount'] . ':target' . $dataCase['cFeedbackTarget'] . ",sales" . $rs->fields['Sales'] . ",branch" . $data['cBranchNum'], 'escrowSalse');

            $rs->MoveNext();
        }
    }

    if ($data['cBranchNum1'] > 0) {
        if ($data['cBranchNum1'] == 505 || $dataCase['cFeedbackTarget1'] == 2) {
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
                        a.sScrivener=' . $scrivenerId . ' AND
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
                            bBranch=' . $data['cBranchNum1'] . ' AND
                            b.bId=a.bBranch
                        ORDER BY
                            bId
                        ASC';
        }
        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $contract->AddContract_Sales($dataCase['cEscrowBankAccount'], $dataCase['cFeedbackTarget1'], $rs->fields['Sales'], $data['cBranchNum1']);
            write_log('程式帶' . $dataCase['cEscrowBankAccount'] . ':target' . $dataCase['cFeedbackTarget1'] . ",sales" . $rs->fields['Sales'] . ",branch" . $data['cBranchNum1'], 'escrowSalse');

            $rs->MoveNext();
        }
    }

    if ($data['cBranchNum2'] > 0) {
        if ($data['cBranchNum2'] == 505 || $dataCase['cFeedbackTarget2'] == 2) {
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
                        a.sScrivener=' . $scrivenerId . ' AND
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
                            bBranch=' . $data['cBranchNum2'] . ' AND
                            b.bId=a.bBranch
                        ORDER BY
                            bId
                        ASC';
        }
        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $contract->AddContract_Sales($dataCase['cEscrowBankAccount'], $dataCase['cFeedbackTarget2'], $rs->fields['Sales'], $data['cBranchNum2']);
            write_log('程式帶' . $dataCase['cEscrowBankAccount'] . ':target' . $dataCase['cFeedbackTarget2'] . ",sales" . $rs->fields['Sales'] . ",branch" . $data['cBranchNum2'], 'escrowSalse');

            $rs->MoveNext();
        }
    }
}

function ContractInvoice($old, $new)
{
    global $conn;

    $sql = "SELECT * FROM tContractInvoice WHERE cCertifiedId = '" . $old . "'";
    $rs  = $conn->Execute($sql);

    $data = $rs->fields;

    $sql = "UPDATE
                tContractInvoice
            SET
                cSplitBuyer = '" . $data['cSplitBuyer'] . "',
                cInvoiceBuyer = '" . $data['cInvoiceBuyer'] . "',
                cSplitOwner = '" . $data['cSplitOwner'] . "',
                cInvoiceOwner = '" . $data['cInvoiceOwner'] . "',
                cSplitRealestate = '" . $data['cSplitRealestate'] . "',
                cInvoiceRealestate = '" . $data['cInvoiceRealestate'] . "',
                cSplitScrivener = '" . $data['cSplitScrivener'] . "',
                cInvoiceScrivener = '" . $data['cInvoiceScrivener'] . "',
                cSplitOther = '" . $data['cSplitOther'] . "',
                cInvoiceOther = '" . $data['cInvoiceOther'] . "',
                cCertifiedBankAcc = '" . $data['cCertifiedBankAcc'] . "',
                cTaxReceiptTarget = '" . $data['cTaxReceiptTarget'] . "',
                cRemark = '" . $data['cRemark'] . "'
            WHERE
                cCertifiedId = '" . $new . "'";
    $conn->Execute($sql);
}

function ContractLand($old, $new)
{
    global $conn;

    $sql = "SELECT * FROM tContractLand WHERE cCertifiedId = '" . $old . "' ORDER bY cItem ASC";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[] = $rs->fields;
        $rs->MoveNext();
    }

    for ($i = 0; $i < count($data); $i++) {
        $sql = '';

        if ($data[$i]['cItem'] == 0) {
            $sql = "UPDATE
                        tContractLand
                    SET
                        cZip = '" . $data[$i]['cZip'] . "',
                        cAddr = '" . $data[$i]['cAddr'] . "',
                        cLand1 = '" . $data[$i]['cLand1'] . "',
                        cLand2 = '" . $data[$i]['cLand2'] . "',
                        cLand3 = '" . $data[$i]['cLand3'] . "',
                        cLand4 = '" . $data[$i]['cLand4'] . "',
                        cMeasure = '" . $data[$i]['cMeasure'] . "',
                        cCategory = '" . $data[$i]['cCategory'] . "',
                        cMoney = '" . $data[$i]['cMoney'] . "',
                        cPower1 = '" . $data[$i]['cPower1'] . "',
                        cPower2 = '" . $data[$i]['cPower2'] . "',
                        cMoveCategory = '" . $data[$i]['cMoveCategory'] . "',
                        cMoveDate = '" . $data[$i]['cMoveDate'] . "',
                        cLandPrice = '" . $data[$i]['cLandPrice'] . "',
                        cFarmLand = '" . $data[$i]['cFarmLand'] . "'
                    WHERE
                        cItem = '" . $data[$i]['cItem'] . "' AND cCertifiedId = '" . $new . "'";
        } else {
            $sql = "INSERT INTO
                 tContractLand
                (
                        cId,
                        cCertifiedId,
                        cItem,
                        cZip,
                        cAddr,
                        cLand1,
                        cLand2,
                        cLand3,
                        cLand4,
                        cMeasure,
                        cCategory,
                        cMoney,
                        cPower1,
                        cPower2,
                        cMoveCategory,
                        cMoveDate,
                        cLandPrice,
                        cFarmLand
                )
            SELECT
                (SELECT max(cId) FROM tContractLand)+1,
                '" . $new . "',
                cItem,
                cZip,
                cAddr,
                cLand1,
                cLand2,
                cLand3,
                cLand4,
                cMeasure,
                cCategory,
                cMoney,
                cPower1,
                cPower2,
                cMoveCategory,
                cMoveDate,
                cLandPrice,
                cFarmLand
            FROM
                tContractLand WHERE cCertifiedId='" . $data[$i]['cCertifiedId'] . "' AND cItem = '" . $data[$i]['cItem'] . "'";
        }
        $conn->Execute($sql);
    }

    $cal = calCase($newCertifiedId);
    $sql = "UPDATE tContractIncome SET cAddedTaxMoney = '" . $cal . "' WHERE cCertifiedId = '" . $newCertifiedId . "'";
    $conn->Execute($sql);
    unset($cal);
}

function getScrivenerBrandRecall($sId, $brandId)
{
    global $conn;

    $sql                 = "SELECT sRecall,sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener='" . $sId . "' AND sBrand ='" . $brandId . "' AND sDel = 0";
    $rs                  = $conn->Execute($sql);
    $tmp['sRecall']      = $rs->fields['sRecall'];
    $tmp['sReacllBrand'] = $rs->fields['sReacllBrand'];

    return $tmp; //cBrandScrRecall
}

function addSmsDefaultB($bid)
{
    global $conn;

    $sql = 'SELECT bMobile FROM tBranchSms WHERE bBranch="' . $bid . '" AND bDefault="1" AND bNID NOT IN ("14","15") AND bDel = 0 ORDER BY bNID,bId ASC;';
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $smsTarget[] = $rs->fields['bMobile'];
        $rs->MoveNext();
    }

    return implode(",", $smsTarget);
}
