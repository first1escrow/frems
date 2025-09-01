<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/first1DB.php';

use First1\V1\PayByCase\PayByCase;

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '查看/編修保證號碼轉換功能');

$chg_scr = $_POST['chg_scr'];
$more_Ok = $_POST['moreOk'];

$s = '';

//更新保證號碼代書
if ($chg_scr == 'ok') {
    $new_scr = $_POST['scr_option_replace'];
    $cid     = $_POST['cid'];

    $sql = "SELECT cCertifiedId FROM tContractCase WHERE cCertifiedId = '" . $cid . "' AND cCaseStatus = 3";
    $rs  = $conn->Execute($sql);

    if ($rs->fields['cCertifiedId']) {
        echo "<script>alert('案件已結案，禁止更改');</script>";
    } else {
        if ($new_scr && $cid) {
            $sql = 'UPDATE tBankCode SET bSID="' . $new_scr . '", bEditDate="' . date("Y-m-d G:i:s") . '", bEditPerson="' . $_SESSION['member_id'] . '" WHERE bAccount LIKE "%' . $cid . '";';
            $conn->Execute($sql);

            $sql = 'UPDATE tContractScrivener SET cScrivener="' . $new_scr . '" WHERE cCertifiedId="' . $cid . '";';
            $conn->Execute($sql);

            $sql = 'SELECT sMobile,sDefault,sSend,sName FROM tScrivenerSms WHERE sScrivener="' . $new_scr . '" AND sDel = 0 AND sLock = 0 ORDER BY sNID,sId ASC;';
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

                $tmp = null;unset($tmp);

                $i++;
                $rs->MoveNext();
            }
            ##

            setSales($cid);

            //複製到案件的預設簡訊對象
            if (count($smsTarget) > 0) {
                $_conn = new first1DB();

                $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . @implode(',', $smsTarget) . '",cSmsTargetName="' . @implode(',', $name) . '",cSend2 = "' . @implode(',', $send) . '",cSendName2="' . @implode(',', $name2) . '" WHERE cCertifiedId="' . $cid . '" AND cScrivener="' . $new_scr . '";';
                $_conn->exeSql($sql);

                $_conn = null;unset($_conn);
            }
            ###

            $s = '1';

            payByCaseSave([$cid]);
        }
    }
}

if ($more_Ok == 1) {
    $new_scr = $_POST['scr_option_replace2'];

    for ($i = 0; $i < count($_POST['CertifiedId']); $i++) {
        $sql = "SELECT cCertifiedId FROM tContractCase WHERE cCertifiedId = '" . $_POST['CertifiedId'][$i] . "' AND cCaseStatus = 3";
        $rs  = $conn->Execute($sql);

        if ($rs->fields['cCertifiedId']) {
            $CaseEnd[] = $_POST['CertifiedId'][$i];
        } else {
            $sql = 'UPDATE tBankCode SET bSID="' . $new_scr . '", bEditDate="' . date("Y-m-d G:i:s") . '", bEditPerson="' . $_SESSION['member_id'] . '" WHERE bAccount LIKE "%' . $_POST['CertifiedId'][$i] . '";';
            $conn->Execute($sql);

            $sql   = "SELECT * FROM tContractCase WHERE cCertifiedId = '" . $_POST['CertifiedId'][$i] . "'";
            $rs    = $conn->Execute($sql);
            $total = $rs->RecordCount();

            $sql = 'UPDATE tContractScrivener SET cScrivener="' . $new_scr . '" WHERE cCertifiedId="' . $_POST['CertifiedId'][$i] . '";';
            $conn->Execute($sql);

            $sql = 'SELECT sMobile,sDefault,sSend,sName FROM tScrivenerSms WHERE sScrivener="' . $new_scr . '" AND sDel = 0 AND sLock = 0 ORDER BY sNID,sId ASC;';
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

                $tmp = null;unset($tmp);

                $rs->MoveNext();
            }
            ##

            //複製到案件的預設簡訊對象
            if (count($smsTarget) > 0) {
                $_conn = new first1DB();

                $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . @implode(',', $smsTarget) . '",cSmsTargetName="' . @implode(',', $name) . '",cSend2 = "' . @implode(',', $send) . '",cSendName2="' . @implode(',', $name2) . '" WHERE cCertifiedId="' . $_POST['CertifiedId'][$i] . '" AND cScrivener="' . $new_scr . '";';
                $_conn->exeSql($sql);

                $_conn = null;unset($_conn);
            }

            setSales($_POST['CertifiedId'][$i]);

            $s = '1';
        }
    }

    payByCaseSave($_POST['CertifiedId']);

    if (is_array($CaseEnd)) {
        echo "<script>alert('" . @implode(',', $CaseEnd) . "案件已結案，禁止更改');</script>";
    }
}

//找出所有狀態正常的地政士
$sql = 'SELECT *,CONCAT("SC",LPAD(sId,4,"0")) as Code FROM tScrivener ORDER BY sId ASC;'; //WHERE sStatus="1"
$rs  = $conn->Execute($sql);

$scr_option_total        = '';
$scr_option_total2       = '';
$menuSearchShipScrivener = '';
while (!$rs->EOF) {
    //出貨進度-地政士
    $selected = ($rs->fields['sId'] == $_POST['searchShipScrivener']) ? "selected=selected" : "";
    $menuSearchShipScrivener .= '<option value="' . $rs->fields['sId'] . '" ' . $selected . '>' . $rs->fields['Code'] . $rs->fields['sName'] . '</option>' . "\n";

    $scr_option_total .= '<option value="' . $rs->fields['sId'] . '">' . $rs->fields['Code'] . $rs->fields['sName'] . '</option>' . "\n";

    if ($rs->fields['sStatus'] == 1) {
        $scr_option_total2 .= '<option value="' . $rs->fields['sId'] . '">' . $rs->fields['Code'] . $rs->fields['sName'] . '</option>' . "\n";
    }

    $rs->MoveNext();
}
##

//找出所有銀行別
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;';
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $branch = '';
    if ($rs->fields['cBankMain'] == '807') {
        $branch = '(' . $rs->fields['cBranchName'] . ')';
    }

    $bank_option_total .= '<option value="' . $rs->fields['cBankVR'] . '">' . $rs->fields['cBankFullName'] . $branch . '</option>' . "\n";
    $rs->MoveNext();
}
##

//找出所有合約版本
$sql = 'SELECT * FROM tBrand WHERE bContract = 1 ORDER BY bId ASC;';
$rs  = $conn->Execute($sql);

$ver_option_total = '';
while (!$rs->EOF) {
    $ver_option_total .= '<option value="' . $rs->fields['bId'] . '">' . $rs->fields['bName'] . '</option>' . "\n";
    $rs->MoveNext();
}

$year = (int) (date('Y') - 1911);
for ($i = 102; $i <= $year; $i++) {
    $menuYear[$i] = $i;
}

$month = (int) date('m');
for ($i = 1; $i <= 12; $i++) {
    $menuMonth[$i] = $i;
}

//出貨進度 申請人
$sql = "SELECT pId,pName FROM  tPeopleInfo WHERE pDep NOT IN(2,9,10,1) AND pJob = 1 ";
$rs  = $conn->Execute($sql);

$menuApplicant    = array();
$menuApplicant[0] = '全部';
while (!$rs->EOF) {
    $menuApplicant[$rs->fields['pId']] = $rs->fields['pName'];
    $rs->MoveNext();
}
##

function setSales($cid)
{
    global $conn;

    $contract = new Contract();

    $sql   = "SELECT * FROM tContractSales  WHERE cCertifiedId = '" . $cid . "'";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total > 0) {
        $sql = "SELECT
                    cr.cBranchNum,
                    cr.cBranchNum1,
                    cr.cBranchNum2,
                    cc.cFeedbackTarget,
                    cc.cFeedbackTarget1,
                    cc.cFeedbackTarget2,
                    cc.cSpCaseFeedBackMoney,
                    cc.cApplyDate,
                    (SELECT cScrivener FROM tContractScrivener AS cs WHERE cs.cCertifiedId=cr.cCertifyId) AS cScrivener
                FROM
                tContractRealestate AS cr
                LEFT JOIN
                tContractCase AS cc ON cc.cCertifiedId=cr.cCertifyId
                WHERE
                cr.cCertifyId='" . $cid . "'
                ";
        $rs = $conn->Execute($sql);

        $list = $rs->fields;

        $sql = "DELETE FROM tContractSales WHERE cCertifiedId = '" . $cid . "' AND cTarget != 3";
        $conn->Execute($sql);

        if ($list['cBranchNum'] > 0) {
            $sales[] = Sales($cid, $list['cBranchNum'], $list['cFeedbackTarget'], $list['cScrivener']);
        }

        if ($list['cBranchNum1'] > 0) {
            $sales[] = Sales($cid, $list['cBranchNum1'], $list['cFeedbackTarget1'], $list['cScrivener']);
        }

        if ($list['cBranchNum2'] > 0) {
            $sales[] = Sales($cid, $list['cBranchNum2'], $list['cFeedbackTarget2'], $list['cScrivener']);
        }

        for ($i = 0; $i < count($sales); $i++) {
            if (is_array($sales[$i])) {
                foreach ($sales[$i] as $k => $v) {
                    if ($v['Sales'] == 46) {
                        if ($list['cApplyDate'] <= '2018-07-26 00:00:00') {
                            $v['Sales'] = getOldSaled($cat, $v['branch'], '2018-07-26');
                        }
                    }

                    $contract->AddContract_Sales($cid, $v['cFeedbackTarget'], $v['Sales'], $v['branch']);
                    write_log('變更店家(保號更改地政士)' . $cid . ':target' . $v['cFeedbackTarget'] . ",sales" . $v['Sales'] . ",OLDbranch" . $list['cBranchNum'] . "_" . $list['cBranchNum1'] . "_" . $list['cBranchNum2'], 'escrowSalse');
                }
            }
        }

        require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';

        getFeedMoney('c', $cid);
    }
}

function Sales($id, $branch, $cFeedbackTarget, $scrivener)
{
    global $conn;

    if ($branch == 505 || $cFeedbackTarget == 2) {
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
					a.sScrivener=' . $scrivener . ' AND
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
						bBranch=' . $branch . ' AND
						b.bId=a.bBranch

					ORDER BY
						bId
					ASC';
    }

    $rs = $conn->Execute($sql);

    $i = 0;
    while (!$rs->EOF) {
        $list[$i]['Sales']           = $rs->fields['Sales'];
        $list[$i]['cFeedbackTarget'] = $cFeedbackTarget;
        $list[$i]['branch']          = $branch;

        $i++;

        $rs->MoveNext();
    }

    return $list;
}

function payByCaseSave($cIds)
{
    global $conn;

    if (empty($cIds)) {
        return;
    }

    $paybycase = new PayByCase(new first1DB);
    foreach ($cIds as $cId) {
        $sql = "SELECT bUsed FROM tBankCode WHERE bDel = 'n' AND bUsed = 1 AND bAccount LIKE '%". $cId . "';";
        $rs  = $conn->Execute($sql);

        while (!$rs->EOF) {
            $paybycase->salesConfirmList($cId);
            $rs->MoveNext();
        }
    }
}

$smarty->assign('menuYear', $menuYear);
$smarty->assign('year', $year);
$smarty->assign('menuMonth', $menuMonth);
$smarty->assign('month', $month);
$smarty->assign('scr_option_total', $scr_option_total);
$smarty->assign('scr_option_total2', $scr_option_total2);
$smarty->assign('bank_option_total', $bank_option_total);
$smarty->assign('ver_option_total', $ver_option_total);
$smarty->assign('s', $s);
$smarty->assign('web_addr', $web_addr);
$smarty->assign('menuSearchShipScrivener', $menuSearchShipScrivener);
$smarty->assign('menuApplicant', $menuApplicant);
$smarty->assign('applicant', $applicant);
$smarty->display('searchscrivener.inc.tpl', '', 'escrow');
