<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/class/getBank.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseScrivener.class.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/class/confirmFeedback.class.php';

$brand         = '';
$status        = '';
$category      = '';
$contract_bank = '';

$_POST = escapeStr($_POST);
$cat   = $_POST['cat'];
$payByCase   = $_POST['payByCase'];

if ($cat == 1 || $cat == '') {
    $checked1 = 'checked=checked';
    $checked2 = '';
} else if ($cat == 2) {
    $checked1 = '';
    $checked2 = 'checked=checked';
}

if ($_POST['ok'] == 'ok') {
    if (is_array($_POST['Case'])) {
        for ($i = 0; $i < count($_POST['Case']); $i++) {
            $sql = "SELECT fCreator,fCertifiedId  FROM tFeedBackMoneyReview WHERE fId = '" . $_POST['Case'][$i] . "'";
            $rs  = $conn->Execute($sql);

            $CertifiedId = $rs->fields['fCertifiedId'];
            $fCreator    = $rs->fields['fCreator'];

            $sql = "SELECT cFeedBackClose FROM tContractCase WHERE cCertifiedId = '" . $CertifiedId . "'";
            $rs  = $conn->Execute($sql);

            $close = $rs->fields['cFeedBackClose'];

            if ($close == 0 || $close == 2) {
                $sql = "UPDATE tFeedBackMoneyReview SET fStatus = 1,fAuditor = '" . $_SESSION['member_id'] . "',fAuditorTime = '" . date('Y-m-d H:i:s') . "' WHERE fId = '" . $_POST['Case'][$i] . "'";
                $conn->Execute($sql);

                $fId = updateCasefeedBackMoney($_POST['Case'][$i], $CertifiedId, $fCreator);

                $paybycase = new First1\V1\PayByCase\PayByCase;
                $paybycaseScrivener = new First1\V1\PayByCase\PayByCaseScrivener(new first1DB);

                $paybycase->salesConfirmList($CertifiedId);
                $payByCaseJoinAccount = $paybycase->getPayByCase($CertifiedId);
                if(!empty($payByCaseJoinAccount)) {
                    $scrivenerId = $payByCaseJoinAccount['fTargetId'];
                    $banks = $paybycaseScrivener->getFeedBackBank($scrivenerId, 1, $fId);

                    $paybycase->savePayByCaseAccount(
                        $CertifiedId,
                        [
                            'identity'    => $banks[0]['fIdentity'],
                            'main'        => $banks[0]['fAccountNum'],
                            'branch'      => $banks[0]['fAccountNumB'],
                            'account'     => $banks[0]['fAccount'],
                            'accountName' => $banks[0]['fAccountName'],
                            'idNumber'    => $banks[0]['fIdentityNumber'],
                            'bankId'      => $banks[0]['fId'],
                        ],
                        'S', $payByCaseJoinAccount['fId']
                    );

                    salesConfirm($CertifiedId);
                    //通知會計
                    $paybycase->needAccountingConfirm($CertifiedId);
                    //20250818 標記仲介案件回饋
                    $confirmFeedback = new First1\V1\ConfirmFeedback\ConfirmFeedback;
                    $confirmFeedback->salesConfirmAudit($CertifiedId);
                    $confirmFeedback = null;unset($confirmFeedback);
                    ##

                }


            } else {
                $msg .= $CertifiedId . "回饋金已鎖定，核可失敗；";
            }
        }
    }

    require_once __DIR__ . '/feedBackAuditResult.php';
}

function updateCasefeedBackMoney($id, $CertifiedId, $fCreator)
{
    global $conn;

    //其他
    $sql = "UPDATE tFeedBackMoney SET fDelete = '1' WHERE fCertifiedId = '" . $CertifiedId . "'";
    $conn->Execute($sql);
    ##

    $sql = "SELECT * FROM tFeedBackMoneyReviewList WHERE fRId = '" . $id . "' AND fDelete = 0";
    $rs  = $conn->Execute($sql);

    $str = "cCaseFeedBackModifier = '" . $fCreator . "',cCaseFeedBackModifyTime = '" . date('Y-m-d H:i:s') . "'";
    $fId = 0; //指定代書回饋帳戶
    while (!$rs->EOF) {
        if ($rs->fields['fCategory'] == 1) {
            $str .= empty($str) ? '' : ',';
            $str .= 'cCaseFeedBackMoney = "' . $rs->fields['fCaseFeedBackMoney'] . '" ,
					cCaseFeedback = "' . $rs->fields['fCaseFeedback'] . '",
					cFeedbackTarget = "' . $rs->fields['fFeedbackTarget'] . '"';
        } elseif ($rs->fields['fCategory'] == 2) {
            $str .= empty($str) ? '' : ',';
            $str .= 'cCaseFeedBackMoney1 = "' . $rs->fields['fCaseFeedBackMoney'] . '" ,
					cCaseFeedback1 = "' . $rs->fields['fCaseFeedback'] . '",
					cFeedbackTarget1 = "' . $rs->fields['fFeedbackTarget'] . '"';
        } elseif ($rs->fields['fCategory'] == 3) {
            $str .= empty($str) ? '' : ',';
            $str .= 'cCaseFeedBackMoney2 = "' . $rs->fields['fCaseFeedBackMoney'] . '" ,
					cCaseFeedback2 = "' . $rs->fields['fCaseFeedback'] . '",
					cFeedbackTarget2 = "' . $rs->fields['fFeedbackTarget'] . '"';
        } elseif ($rs->fields['fCategory'] == 4) {
            $str .= empty($str) ? '' : ',';
            $str .= 'cSpCaseFeedBackMoney = "' . $rs->fields['fCaseFeedBackMoney'] . '"';
        } elseif ($rs->fields['fCategory'] == 5) {
            if (empty($rs->fields['fDelete'])) {
                $target = ($rs->fields['fFeedbackTarget'] == 1) ? '2' : '1';
                $sales  = getFeedBackOtherSales($target, $rs->fields['fFeedbackStoreId']);

                $sql = "INSERT INTO tFeedBackMoney
                            (fCertifiedId,fType,fStoreId,fMoney,fSales)
                        VALUES
                            ('" . $CertifiedId . "','" . $target . "','" . $rs->fields['fFeedbackStoreId'] . "','" . $rs->fields['fCaseFeedBackMoney'] . "','" . $sales . "')";
                $conn->Execute($sql);
            }
        } elseif ($rs->fields['fCategory'] == 6) {
            $str .= empty($str) ? '' : ',';
            $str .= 'cCaseFeedBackMoney3 = "' . $rs->fields['fCaseFeedBackMoney'] . '" ,
					cCaseFeedback3 = "' . $rs->fields['fCaseFeedback'] . '",
					cFeedbackTarget3 = "' . $rs->fields['fFeedbackTarget'] . '"';
        }  elseif ($rs->fields['fCategory'] == 7) {
            if (empty($rs->fields['fDelete'])) {
                $target = '2';
                $sales  = getFeedBackOtherSales($target, $rs->fields['fFeedbackStoreId']);
                $fType = 3;

                $sql = "INSERT INTO tFeedBackMoney
                            (fCertifiedId,fType,fStoreId,fIndividualId,fMoney,fSales)
                        VALUES
                            ('" . $CertifiedId . "','" . $fType . "','" . $rs->fields['fFeedbackStoreId'] . "','" . $rs->fields['fIndividualId'] . "','" . $rs->fields['fCaseFeedBackMoney'] . "','" . $sales . "')";
                $conn->Execute($sql);
            }
        }
        if($rs->fields['fFeedbackDataId'] != 0 and  $rs->fields['fCaseFeedBackMoney'] > 0) {
            $fId = $rs->fields['fFeedbackDataId'];
        }
        $rs->MoveNext();
    }

    $sql = "UPDATE tContractCase SET " . $str . " WHERE cCertifiedId = '" . $CertifiedId . "'";
    $conn->Execute($sql);

    //20230413 判定通知業務是否審核
    $paybycase = new First1\V1\PayByCase\PayByCase;

    $paybycase->salesConfirmList($CertifiedId);
    $paybycase = null;unset($paybycase);
    ##
    return $fId;
}

function salesConfirm($certifiedId) {
    global $conn;

    $sql = 'UPDATE
                tFeedBackMoneyPayByCase
            SET
                fSalesConfirmDate = NOW(),
                fSalesConfirmId = '. $_SESSION['member_id'] .'
            WHERE
                fCertifiedId = ' . $certifiedId . '
            ';
    return $conn->Execute($sql);
}

$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(4,7) AND pJob = 1 ";
$rs  = $conn->Execute($sql);

$salesList = '';
while (!$rs->EOF) {
    $selected = '';
    if ($rs->fields['pId'] == $_POST["sales"]) {
        $selected = "selected=selected";
    }
    $salesList .= "<option value='" . $rs->fields['pId'] . "' " . $selected . ">" . $rs->fields['pName'] . "</option>\n";

    $rs->MoveNext();
}
##

if ($record_limit == 10) {$records_limit .= '<option value="10" selected="selected">10</option>' . "\n";} else { $records_limit .= '<option value="10">10</option>' . "\n";}
if ($record_limit == 50) {$records_limit .= '<option value="50" selected="selected">50</option>' . "\n";} else { $records_limit .= '<option value="50">50</option>' . "\n";}
if ($record_limit == 100) {$records_limit .= '<option value="100" selected="selected">100</option>' . "\n";} else { $records_limit .= '<option value="100">100</option>' . "\n";}
if ($record_limit == 150) {$records_limit .= '<option value="150" selected="selected">150</option>' . "\n";} else { $records_limit .= '<option value="150">150</option>' . "\n";}
if ($record_limit == 200) {$records_limit .= '<option value="200" selected="selected">200</option>' . "\n";} else { $records_limit .= '<option value="200">200</option>' . "\n";}
##

if ($sEndDate) {
    $tmp      = explode('-', $sEndDate);
    $sEndDate = ($tmp[0] - 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}

if ($eEndDate) {
    $tmp      = explode('-', $eEndDate);
    $eEndDate = ($tmp[0] - 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}
##

$smarty->assign('msg', $msg);
$smarty->assign('i_begin', $i_begin);
$smarty->assign('i_end', $i_end);
$smarty->assign('current_page', $current_page);
$smarty->assign('total_page', $total_page);
$smarty->assign('record_limit', $records_limit);
$smarty->assign('max', number_format($max));
$smarty->assign('cat', $cat);
$smarty->assign('list', $list);
$smarty->assign('salesList', $salesList);
$smarty->assign('sEndDate', $sEndDate);
$smarty->assign('eEndDate', $eEndDate);
$smarty->assign('checked1', $checked1);
$smarty->assign('checked2', $checked2);
$smarty->assign('payByCase', $payByCase);
$smarty->display('feedBackAudit.inc.tpl', '', 'report');
