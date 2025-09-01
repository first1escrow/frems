<?php
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/lib.php';
require_once dirname(__DIR__) . '/includes/IDCheck.php';
$advance = new Advance();

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多買賣');

$_iden = isset($_REQUEST['iden']) && is_string($_REQUEST['iden']) ? trim($_REQUEST['iden']) : '';
$save  = isset($_REQUEST['save']) && is_string($_REQUEST['save']) ? trim($_REQUEST['save']) : '';
$del   = isset($_POST['del']) && is_string($_POST['del']) ? trim($_POST['del']) : '';

$cCertifiedId = isset($_REQUEST['cCertifyId']) && is_string($_REQUEST['cCertifyId']) ? trim($_REQUEST['cCertifyId']) : '';

$sign = isset($_REQUEST['cSingCategory']) && is_string($_REQUEST['cSingCategory']) ? trim($_REQUEST['cSingCategory']) : '';

if ($_iden == 'o') { // 6買方代理人7賣方代理人
    $_ide      = '賣方代理人';
    $cIdentity = 7;
} else if ($_iden == 'b') { // 6買方代理人7賣方代理人
    $_ide      = '買方代理人';
    $cIdentity = 6;
} else {
    echo "資料錯誤!!";
    exit;
}

// 刪除/儲存資料
if ($_POST) {
    // 更新舊資料
    $countOld = isset($_POST['oldId']) && is_array($_POST['oldId']) ? count($_POST['oldId']) : 0;
    for ($i = 0; $i < $countOld; $i++) {
        $birthday = '';
        if (isset($_POST['oldBirthdayDay_' . $i]) && $_POST['oldBirthdayDay_' . $i]) {
            $tmp = explode('-', $_POST['oldBirthdayDay_' . $i]);
            if (isset($tmp[0], $tmp[1], $tmp[2])) {
                $birthday = ((int) $tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
            }
            unset($tmp);
        }

        $sql = "UPDATE tContractOthers SET
            cIdentifyId = '" . (isset($_POST['oldIdentifyId_' . $i]) ? $_POST['oldIdentifyId_' . $i] : '') . "',
            cName = '" . (isset($_POST['oldName_' . $i]) ? $_POST['oldName_' . $i] : '') . "',
            cBirthdayDay = '" . $birthday . "',
            cCountryCode = '" . (isset($_POST['oldCountryCode_' . $i]) ? $_POST['oldCountryCode_' . $i] : '') . "',
            cPassport = '" . (isset($_POST['oldPassport_' . $i]) ? $_POST['oldPassport_' . $i] : '') . "',
            cTaxTreatyCode = '" . (isset($_POST['oldTaxTreatyCode_' . $i]) ? $_POST['oldTaxTreatyCode_' . $i] : '') . "',
            cResidentLimit = '" . (isset($_POST['oldResidentLimit_' . $i]) ? $_POST['oldResidentLimit_' . $i] : '') . "',
            cPaymentDate = '" . (isset($_POST['oldPaymentDate_' . $i]) ? $_POST['oldPaymentDate_' . $i] : '') . "',
            cNHITax = '" . (isset($_POST['oldcNHITax_' . $i]) ? $_POST['oldcNHITax_' . $i] : '') . "',
            cMobileNum = '" . (isset($_POST['oldMobileNum_' . $i]) ? $_POST['oldMobileNum_' . $i] : '') . "',
            cRegistZip = '" . (isset($_POST['oldRegistZip_' . $i]) ? $_POST['oldRegistZip_' . $i] : '') . "',
            cRegistAddr = '" . (isset($_POST['oldRegistAddr_' . $i]) ? $_POST['oldRegistAddr_' . $i] : '') . "',
            cBaseZip = '" . (isset($_POST['oldBaseZip_' . $i]) ? $_POST['oldBaseZip_' . $i] : '') . "',
            cBaseAddr = '" . (isset($_POST['oldBaseAddr_' . $i]) ? $_POST['oldBaseAddr_' . $i] : '') . "',
            cBankMain = '" . (isset($_POST['oldBankMain_' . $i]) && is_array($_POST['oldBankMain_' . $i]) ? $_POST['oldBankMain_' . $i][0] : '') . "',
            cBankBranch = '" . (isset($_POST['oldcBankBranch_' . $i]) && is_array($_POST['oldcBankBranch_' . $i]) ? $_POST['oldcBankBranch_' . $i][0] : '') . "',
            cBankAccName = '" . (isset($_POST['oldBankAccName_' . $i]) && is_array($_POST['oldBankAccName_' . $i]) ? $_POST['oldBankAccName_' . $i][0] : '') . "',
            cBankAccNum = '" . (isset($_POST['oldBankAccNum_' . $i]) && is_array($_POST['oldBankAccNum_' . $i]) ? $_POST['oldBankAccNum_' . $i][0] : '') . "',
            cBankMoney = '" . (isset($_POST['oldBankAccMoney_' . $i]) && is_array($_POST['oldBankAccMoney_' . $i]) ? $_POST['oldBankAccMoney_' . $i][0] : '') . "',
            cChecklistBank = '" . (isset($_POST['oldChecklistBank_' . $i]) && is_array($_POST['oldChecklistBank_' . $i]) ? $_POST['oldChecklistBank_' . $i][0] : '') . "',
            cOtherName = '" . (isset($_POST['oldOtherName_' . $i]) ? $_POST['oldOtherName_' . $i] : '') . "',
            cEmail = '" . (isset($_POST['oldEmail_' . $i]) ? $_POST['oldEmail_' . $i] : '') . "'
        WHERE cId = '" . (isset($_POST['oldId'][$i]) ? $_POST['oldId'][$i] : '') . "'";

        $conn->Execute($sql);
    }

    // 新增
    $newRowCount = isset($_POST['newRowCount']) ? (int) $_POST['newRowCount'] : -1;
    for ($i = 0; $i <= $newRowCount; $i++) {
        if (isset($_POST['newName_' . $i]) && $_POST['newName_' . $i] && isset($_POST['newIdentifyId_' . $i]) && $_POST['newIdentifyId_' . $i]) {
            $birthday = '';
            if (isset($_POST['newBirthdayDay_' . $i]) && $_POST['newBirthdayDay_' . $i]) {
                $tmp = explode('-', $_POST['newBirthdayDay_' . $i]);
                if (isset($tmp[0], $tmp[1], $tmp[2])) {
                    $birthday = ((int) $tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
                }
                unset($tmp);
            }

            $birthday = isset($_POST['newBirthday_' . $i]) ? $_POST['newBirthday_' . $i] : $birthday;
            $sql      = "INSERT INTO tContractOthers SET
                cCertifiedId = '" . (isset($_POST['cCertifiedId']) ? $_POST['cCertifiedId'] : '') . "',
                cIdentity = '" . (isset($_POST['cIdentity']) ? $_POST['cIdentity'] : '') . "',
                cIdentifyId = '" . (isset($_POST['newIdentifyId_' . $i]) ? $_POST['newIdentifyId_' . $i] : '') . "',
                cName = '" . (isset($_POST['newName_' . $i]) ? $_POST['newName_' . $i] : '') . "',
                cBirthdayDay = '" . $birthday . "',
                cCountryCode = '" . (isset($_POST['newCountryCode_' . $i]) ? $_POST['newCountryCode_' . $i] : '') . "',
                cPassport = '" . (isset($_POST['newPassport_' . $i]) ? $_POST['newPassport_' . $i] : '') . "',
                cTaxTreatyCode = '" . (isset($_POST['newTaxTreatyCode_' . $i]) ? $_POST['newTaxTreatyCode_' . $i] : '') . "',
                cResidentLimit = '" . (isset($_POST['newResidentLimit_' . $i]) ? $_POST['newResidentLimit_' . $i] : '') . "',
                cPaymentDate = '" . (isset($_POST['newPaymentDate_' . $i]) ? $_POST['newPaymentDate_' . $i] : '') . "',
                cNHITax = '" . (isset($_POST['newcNHITax_' . $i]) ? $_POST['newcNHITax_' . $i] : '') . "',
                cMobileNum = '" . (isset($_POST['newMobileNum_' . $i]) ? $_POST['newMobileNum_' . $i] : '') . "',
                cRegistZip = '" . (isset($_POST['newRegistZip_' . $i]) ? $_POST['newRegistZip_' . $i] : '') . "',
                cRegistAddr = '" . (isset($_POST['newRegistAddr_' . $i]) ? $_POST['newRegistAddr_' . $i] : '') . "',
                cBaseZip = '" . (isset($_POST['newBaseZip_' . $i]) ? $_POST['newBaseZip_' . $i] : '') . "',
                cBaseAddr = '" . (isset($_POST['newBaseAddr_' . $i]) ? $_POST['newBaseAddr_' . $i] : '') . "',
                cBankMain = '" . (isset($_POST['newBankMain_' . $i]) && is_array($_POST['newBankMain_' . $i]) ? $_POST['newBankMain_' . $i][0] : '') . "',
                cBankBranch = '" . (isset($_POST['newcBankBranch_' . $i]) && is_array($_POST['newcBankBranch_' . $i]) ? $_POST['newcBankBranch_' . $i][0] : '') . "',
                cBankAccName = '" . (isset($_POST['newBankAccName_' . $i]) && is_array($_POST['newBankAccName_' . $i]) ? $_POST['newBankAccName_' . $i][0] : '') . "',
                cBankAccNum = '" . (isset($_POST['newBankAccNum_' . $i]) && is_array($_POST['newBankAccNum_' . $i]) ? $_POST['newBankAccNum_' . $i][0] : '') . "',
                cBankMoney = '" . (isset($_POST['newBankAccMoney_' . $i]) && is_array($_POST['newBankAccMoney_' . $i]) ? $_POST['newBankAccMoney_' . $i][0] : '') . "',
                cChecklistBank = '" . (isset($_POST['newChecklistBank_' . $i]) && is_array($_POST['newChecklistBank_' . $i]) ? $_POST['newChecklistBank_' . $i] : '') . "',
                cEmail = '" . (isset($_POST['newEmail_' . $i]) ? $_POST['newEmail_' . $i] : '') . "',
                cOtherName = '" . (isset($_POST['newOtherName_' . $i]) ? $_POST['newOtherName_' . $i] : '') . "'";

            $conn->Execute($sql);
            $id = $conn->Insert_ID();
        }
    }

    // 刪除
    if ($del == 'ok') {
        $del_no = isset($_POST['del_no']) ? $_POST['del_no'] : '';
        if ($del_no) {
            $sql = "DELETE FROM tContractOthers WHERE cId = '" . $del_no . "'";
            $conn->Execute($sql);

            $sql = "DELETE FROM tContractCustomerBank WHERE cOtherId = '" . $del_no . "'";
            $conn->Execute($sql);

            $tlog = new TraceLog();
            $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多筆買賣案件刪除');
        }
    }
}

//顯示相關資料
$sql = '
	SELECT
		a.*,
		b.zCity as cRegistCity,
		b.zArea as cRegistArea,
		c.zCity as cBaseCity,
		c.zArea as cBaseArea
	FROM
		tContractOthers AS a
	LEFT JOIN
		tZipArea AS b ON a.cRegistZip=b.zZip
	LEFT JOIN
		tZipArea AS c ON a.cBaseZip=c.zZip
	WHERE
		a.cCertifiedId="' . $cCertifiedId . '"
		AND a.cIdentity="' . $cIdentity . '"
	ORDER BY
		a.cIdentity
	ASC ;
';
// echo "SQL=".$sql ;
$rs   = $conn->Execute($sql);
$list = [];
$i    = 0;
while (! $rs->EOF) {
    $arr = [];
    $arr = $rs->fields;
    switch ($arr['cIdentity']) {
        case '6':
            $arr['_ide'] = '買'; //6買方代理人7賣方代理人
            break;
        case '7':
            $arr['_ide'] = '賣'; //6買方代理人7賣方代理人
            break;
        default:
            $arr['_ide'] = '其他';
            break;
    }

    $arr['cRegistAreaMenu'] = getArea($arr['cRegistCity']);
    $arr['cBaseAreaMenu']   = getArea($arr['cBaseCity']);
    if ($arr['cBaseZip'] == $arr['cRegistZip'] && $arr['cBaseAddr'] == $arr['cRegistAddr']) {
        $arr['sameAddr'] = "checked=checked";
    } else {
        $arr['sameAddr'] = "";
    }
    $arr['no']           = $i + 1;
    $arr['cBirthdayDay'] = $advance->ConvertDateToRoc($arr['cBirthdayDay'], base::DATE_FORMAT_NUM_DATE);
    $arr['checkIDImg']   = (checkUID($arr['cIdentifyId'])) ? '<img src="/images/ok.png">' : '<img src="/images/ng.png">';
    array_push($list, $arr);
    unset($arr);
    $i++;
    $rs->MoveNext();
}
##
##檢查是否會計發票關掉
$sql           = "SELECT cInvoiceClose,cSignCategory FROM tContractCase WHERE cCertifiedId ='" . $cCertifiedId . "'";
$rs            = $conn->Execute($sql);
$cInvoiceClose = $rs->fields['cInvoiceClose'];
$cSignCategory = $rs->fields['cSignCategory'];
$checkSave     = 1;
if ($cInvoiceClose == 'Y' && ($_SESSION['member_pDep'] != 9 && $_SESSION['member_pDep'] != 10 && $_SESSION['member_pDep'] != 1)) {
    $checkSave = 0;
}
##
##縣市
$sql         = 'SELECT DISTINCT zCity FROM tZipArea ORDER BY nid ASC';
$rs          = $conn->CacheExecute($sql);
$menuCity[0] = "縣市";
while (! $rs->EOF) {
    $menuCity[$rs->fields['zCity']] = $rs->fields['zCity'];

    $rs->MoveNext();
}
##
function getArea($city)
{
    global $conn;
    $sql = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="' . $city . '";';
    $rs  = $conn->CacheExecute($sql);

    $arr = [];
    while (! $rs->EOF) {
        $arr[$rs->fields['zZip']] = $rs->fields['zArea'];
        $rs->moveNext();
    }
    return $arr;
}

##
$smarty->assign('meunCity', $menuCity);
$smarty->assign('_ide', $_ide);
$smarty->assign('save', $save);
$smarty->assign('del', $del);

$sameAddr = isset($sameAddr) ? $sameAddr : '';
$smarty->assign('sameAddr', $sameAddr);
$smarty->assign('cInvoiceClose', $cInvoiceClose);
$smarty->assign('cSignCategory', $cSignCategory);
$smarty->assign('checkSave', $checkSave);
$smarty->assign('cCertifiedId', $cCertifiedId);
$smarty->assign('cIdentity', $cIdentity);
$smarty->assign('list', $list);
$smarty->display('buycontractlist.inc.tpl', '', 'escrow');
