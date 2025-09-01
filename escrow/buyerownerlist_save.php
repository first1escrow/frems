<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

$_POST    = escapeStr($_POST);
$_REQUEST = escapeStr($_REQUEST);

// safe input reads
$_iden        = isset($_REQUEST['iden']) ? $_REQUEST['iden'] : '';
$save         = isset($_REQUEST['save']) ? $_REQUEST['save'] : '';
$del          = isset($_POST['del']) ? $_POST['del'] : '';
$cCertifiedId = isset($_REQUEST['cCertifyId']) ? $_REQUEST['cCertifyId'] : '';

if ($_iden == 'o') { // 賣：2
    $BankIden = 52;
}

if ($_iden == 'b') { // 買：1
    $BankIden = 53;
}

if ($_POST) {
    // print_r($_POST);
    // file_put_contents('/var/www/html/first.twhg.com.tw/log2/QQ.log', json_encode($_POST));

    // 修改
    $countOld = isset($_POST['oldId']) && is_array($_POST['oldId']) ? count($_POST['oldId']) : 0;
    for ($i = 0; $i < $countOld; $i++) {
        $birthday = '';
        if (isset($_POST['oldBirthdayDay_' . $i]) && $_POST['oldBirthdayDay_' . $i]) {
            $tmp = explode('-', $_POST['oldBirthdayDay_' . $i]);
            if (isset($tmp[0], $tmp[1], $tmp[2])) {
                $birthday = ((int) $tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
            }
            $tmp = null;unset($tmp);
        }

        if (isset($_POST['oldId'][$i]) && $_POST['oldId'][$i]) {
            $sql = "UPDATE
                        tContractOthers
                    SET
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
                    WHERE
                        cId = '" . (isset($_POST['oldId'][$i]) ? $_POST['oldId'][$i] : '') . "'";
            $conn->Execute($sql);
        }
        // tContractCustomerBank 後來增加所以只能切開做
        $countOther = isset($_POST['otherBankId_' . $i]) && is_array($_POST['otherBankId_' . $i]) ? count($_POST['otherBankId_' . $i]) : 0;
        for ($j = 0; $j < $countOther; $j++) {
            $oldBankMain_j   = (isset($_POST['oldBankMain_' . $i]) && is_array($_POST['oldBankMain_' . $i]) && isset($_POST['oldBankMain_' . $i][$j])) ? $_POST['oldBankMain_' . $i][$j] : '';
            $oldBankBranch_j = (isset($_POST['oldcBankBranch_' . $i]) && is_array($_POST['oldcBankBranch_' . $i]) && isset($_POST['oldcBankBranch_' . $i][$j])) ? $_POST['oldcBankBranch_' . $i][$j] : '';
            if (($j > 0) && $oldBankMain_j != '0' && $oldBankBranch_j != 0) {
                $sql = "INSERT INTO
                            tContractCustomerBank
                        SET
                            cCertifiedId = '" . (isset($_POST['cCertifiedId']) ? $_POST['cCertifiedId'] : '') . "',
                            cIdentity = '" . $BankIden . "',
                            cBankMain = '" . $oldBankMain_j . "',
                            cBankBranch = '" . $oldBankBranch_j . "',
                            cBankAccountName = '" . ((isset($_POST['oldBankAccName_' . $i]) && is_array($_POST['oldBankAccName_' . $i]) && isset($_POST['oldBankAccName_' . $i][$j])) ? $_POST['oldBankAccName_' . $i][$j] : '') . "',
                            cBankAccountNo = '" . ((isset($_POST['oldBankAccNum_' . $i]) && is_array($_POST['oldBankAccNum_' . $i]) && isset($_POST['oldBankAccNum_' . $i][$j])) ? $_POST['oldBankAccNum_' . $i][$j] : '') . "',
                            cBankMoney = '" . ((isset($_POST['oldBankAccMoney_' . $i]) && is_array($_POST['oldBankAccMoney_' . $i]) && isset($_POST['oldBankAccMoney_' . $i][$j])) ? $_POST['oldBankAccMoney_' . $i][$j] : '') . "',
                            cChecklistBank  = '" . (isset($_POST['oldChecklistBank_' . $i . '_' . $j]) ? $_POST['oldChecklistBank_' . $i . '_' . $j] : '') . "',
                            cOtherId = '" . (isset($_POST['oldId'][$i]) ? $_POST['oldId'][$i] : '') . "';";

                if (isset($_POST['otherBankId_' . $i][$j]) && $_POST['otherBankId_' . $i][$j] != '') {
                    $sql = "UPDATE
                                tContractCustomerBank
                            SET
                                cBankMain = '" . $oldBankMain_j . "',
                                cBankBranch = '" . $oldBankBranch_j . "',
                                cBankAccountName = '" . ((isset($_POST['oldBankAccName_' . $i]) && is_array($_POST['oldBankAccName_' . $i]) && isset($_POST['oldBankAccName_' . $i][$j])) ? $_POST['oldBankAccName_' . $i][$j] : '') . "',
                                cBankAccountNo = '" . ((isset($_POST['oldBankAccNum_' . $i]) && is_array($_POST['oldBankAccNum_' . $i]) && isset($_POST['oldBankAccNum_' . $i][$j])) ? $_POST['oldBankAccNum_' . $i][$j] : '') . "',
                                cBankMoney = '" . ((isset($_POST['oldBankAccMoney_' . $i]) && is_array($_POST['oldBankAccMoney_' . $i]) && isset($_POST['oldBankAccMoney_' . $i][$j])) ? $_POST['oldBankAccMoney_' . $i][$j] : '') . "',
                                cChecklistBank  = '" . (isset($_POST['oldChecklistBank_' . $i . '_' . $j]) ? $_POST['oldChecklistBank_' . $i . '_' . $j] : '') . "'
                            WHERE
                                cId = '" . ((isset($_POST['otherBankId_' . $i][$j]) && $_POST['otherBankId_' . $i][$j]) ? $_POST['otherBankId_' . $i][$j] : '') . "';";
                }

                $conn->Execute($sql);
            }
        }
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
                $tmp = null;unset($tmp);
            }

            $sql = "INSERT INTO
                        tContractOthers
                    SET
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
                        cChecklistBank = '" . (isset($_POST['newChecklistBank_' . $i]) ? $_POST['newChecklistBank_' . $i] : '') . "',
                        cEmail = '" . (isset($_POST['newEmail_' . $i]) ? $_POST['newEmail_' . $i] : '') . "',
                        cOtherName = '" . (isset($_POST['newOtherName_' . $i]) ? $_POST['newOtherName_' . $i] : '') . "';";
            $conn->Execute($sql);
            $id = $conn->Insert_ID();

            $newIndex = isset($_POST['newIndex_' . $i]) ? (int) $_POST['newIndex_' . $i] : 0;
            for ($j = 0; $j <= $newIndex; $j++) { // newIndex_0
                $newBankMain_j   = (isset($_POST['newBankMain_' . $i]) && is_array($_POST['newBankMain_' . $i]) && isset($_POST['newBankMain_' . $i][$j])) ? $_POST['newBankMain_' . $i][$j] : '';
                $newBankBranch_j = (isset($_POST['newcBankBranch_' . $i]) && is_array($_POST['newcBankBranch_' . $i]) && isset($_POST['newcBankBranch_' . $i][$j])) ? $_POST['newcBankBranch_' . $i][$j] : '';
                if (($j > 0) && $newBankMain_j != '0' && $newBankBranch_j != 0) {
                    $sql = "INSERT INTO
                                tContractCustomerBank
                            SET
                                cCertifiedId = '" . (isset($_POST['cCertifiedId']) ? $_POST['cCertifiedId'] : '') . "',
                                cIdentity = '" . $BankIden . "',
                                cBankMain = '" . $newBankMain_j . "',
                                cBankBranch = '" . $newBankBranch_j . "',
                                cBankAccountName = '" . ((isset($_POST['newBankAccName_' . $i]) && is_array($_POST['newBankAccName_' . $i]) && isset($_POST['newBankAccName_' . $i][$j])) ? $_POST['newBankAccName_' . $i][$j] : '') . "',
                                cBankAccountNo = '" . ((isset($_POST['newBankAccNum_' . $i]) && is_array($_POST['newBankAccNum_' . $i]) && isset($_POST['newBankAccNum_' . $i][$j])) ? $_POST['newBankAccNum_' . $i][$j] : '') . "',
                                cBankMoney = '" . ((isset($_POST['newBankAccMoney_' . $i]) && is_array($_POST['newBankAccMoney_' . $i]) && isset($_POST['newBankAccMoney_' . $i][$j])) ? $_POST['newBankAccMoney_' . $i][$j] : '') . "',
                                cChecklistBank  = '" . (isset($_POST['oldChecklistBank_' . $i . '_' . $j]) ? $_POST['oldChecklistBank_' . $i . '_' . $j] : '') . "',
                                cOtherId = '" . $id . "';";
                    $conn->Execute($sql);
                }
            }
        }
    }
    //

    //刪除
    if ($del == 'ok') {
        $del_no = $_POST['del_no'];

        if ($del_no) {
            $sql = 'DELETE FROM tContractOthers WHERE cId="' . $del_no . '";';
            $conn->Execute($sql);

            $sql = "DELETE FROM tContractCustomerBank WHERE cOtherId = '" . $del_no . "'";
            $conn->Execute($sql);

            $tlog = new TraceLog();
            $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多筆買賣案件刪除');
        }
    }
    ##
}

exit('OK');
