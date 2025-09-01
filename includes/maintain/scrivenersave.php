<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/scrivener.class.php';
require_once dirname(dirname(__DIR__)) . '/class/intolog.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCaseScrivener.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/opendb2.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

require_once dirname(__DIR__) . '/writelog.php';
require_once dirname(__DIR__) . '/lib.php';

require_once __DIR__ . '/checkCaseHasBeforeDate.php';
require_once __DIR__ . '/feedBackData.php';

use First1\V1\PayByCase\PayByCase;
use First1\V1\PayByCase\PayByCaseScrivener;

//取得地政士正在進行中的案件
function getWorkingCases(&$conn, $sId)
{
    $sql = 'SELECT a.cCertifiedId FROM tContractCase AS a JOIN tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId WHERE b.cScrivener = ' . $sId . ' AND a.cCaseStatus = 2;';
    $rs  = $conn->Execute($sql);

    $cIds = [];
    while (!$rs->EOF) {
        $cIds[] = $rs->fields['cCertifiedId'];
        $rs->MoveNext();
    }

    return $cIds;
}

//檢查新舊回饋金帳號資料是否有差異
function feedback_bank_diff($feedback_bank_before, $feedback_bank_after)
{
    //新舊帳戶數不同
    if (count($feedback_bank_before) != count($feedback_bank_after)) {
        return true;
    }

    //比對帳戶資料
    foreach ($feedback_bank_before as $before) {
        $founded = false;
        foreach ($feedback_bank_after as $after) {
            if ($before['fAccountNum'] == $after[$k]['fAccountNum']
                && $before['fAccountNumB'] == $after[$k]['fAccountNumB']
                && $before['fAccount'] == $after[$k]['fAccount']
                && $before['fAccountName'] == $after[$k]['fAccountName']) {
                $founded = true; //找到相同的帳戶
                break;
            }
        }

        if (!$founded) {
            return true;
        }
    }

    return false;
}

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '編修特定地政士資料明細');

//預載log物件
$logs = new Intolog();
##

$scrivener = new Scrivener();

//日期轉換
if ($_POST["sAppointDate"] != '0000-00-00') {
    $_POST["sAppointDate"] = date_convert($_POST["sAppointDate"]);
}

if ($_POST["sOpenDate"] != '0000-00-00') {
    $_POST["sOpenDate"] = date_convert($_POST["sOpenDate"]);
}

if ($_POST["sSaveDate"] != '0000-00-00') {
    $_POST["sSaveDate"] = date_convert($_POST["sSaveDate"]);
}

if ($_POST["sLicenseExpired"] != '0000-00-00' && $_POST["sLicenseExpired"] != '000-00-00' && $_POST["sLicenseExpired"] != '') {
    $_POST["sLicenseExpired"] = date_convert($_POST["sLicenseExpired"]);
}

$_POST["sStatusDateStart"] = preg_match("/000-00-00/", $_POST["sStatusDateStart"]) ? '' : date_convert($_POST["sStatusDateStart"]);
$_POST["sStatusDateEnd"]   = preg_match("/000-00-00/", $_POST["sStatusDateEnd"]) ? '' : date_convert($_POST["sStatusDateEnd"]);
$_POST["sSalesDate"]       = preg_match("/000-00-00/", $_POST["sSalesDate"]) ? '' : date_convert($_POST["sSalesDate"]);
$_POST["sBirthday"]        = preg_match("/000-00-00/", $_POST["sBirthday"]) ? '' : date_convert($_POST["sBirthday"]);
##

//取得原始資料
$data                 = $scrivener->GetScrivenerInfo($_POST["id"]);
$feedback_bank_before = $scrivener->GetScrivenerFeedbackBank($_POST["id"]);

//結算方式確認 (地政士結算切換每日排程切換 198)
if ($_POST['sFeedDateCatSwitch'] == 2) {
    if (!preg_match("/^[0-9]{4}\-[0-9]{2}$/", $_POST["sFeedDateCatSwitchDate"])) {
        http_response_code(400);
        exit('回饋金結算方式為隨案支付，請選擇結算切換日期');
    }

    if (checkCaseHasBeforeDate($_POST["id"], $_POST["sFeedDateCatSwitchDate"])) {
        http_response_code(400);
        exit('結算切換日期範圍內已有案件，請重新選擇');
    }
}

//確認結算狀態切換（不允許隨案結改成季、月結）
if (($_POST['sFeedDateCatSwitch'] == '0') || ($_POST['sFeedDateCatSwitch'] == '1')) {
    if (workingCaseHas($_POST["id"])) {
        http_response_code(400);
        exit('目前有進行中案件，無法切換回月結或季結');
    }

    $_POST['feedDateCat']            = $_POST['sFeedDateCatSwitch'];
    $_POST['sFeedDateCatSwitch']     = '';
    $_POST["sFeedDateCatSwitchDate"] = '';
}

if ($_SESSION['member_pFeedBackModify'] == 0) {
    $_POST['sRecall']   = $data['sRecall'];
    $_POST['sSpRecall'] = $data['sSpRecall'];
}

if ($data['sStatus'] == 2 && $_POST['sStatus'] == 1) {
    $sql = "UPDATE tScrivener SET sReStart = '" . date('Y-m-d H:i:s') . "' WHERE sId = '" . $_POST["id"] . "'";
    $conn->Execute($sql);
}

//關店 LINE跟APP帳號要關
if ($_POST['sStatus'] == 2) {
    $sql = "UPDATE tLineAccount SET lStatus = 'N' WHERE lTargetCode = 'SC" . str_pad($_POST["id"], '4', '0', STR_PAD_LEFT) . "'";
    $conn->Execute($sql);

    $sql = "UPDATE tAppAccount SET aStatus = 2 WHERE aParentId = 'SC" . str_pad($_POST["id"], '4', '0', STR_PAD_LEFT) . "'";
    $conn->Execute($sql);
}

$scrivener->SaveScrivener($_POST);

//20240507 回饋金隨案支付方式切換立即執行
if ($_POST['sFeedDateCatSwitch'] == 2) {
    $cmd = '/home/first198/firstschedule/maintain/scrivenerPayByCaseSwitch.sh';
    shell_exec($cmd);
    $cmd = null;unset($cmd);
}

if ($data['sRecall'] != $_POST['sRecall'] || $data['sSpRecall'] != $_POST['sSpRecall']) {
    $txt = getFeedMoney('s', $_POST["id"], '', $data['sFeedDateCat']);
    echo '回饋金更動的保證號碼:' . @implode(',', $txt) . "\r\n";
    $tlog->updateWrite($_SESSION['member_id'], @implode(',', $txt), '回饋金更動的保證號碼');
}
##

//儲存紀錄
$sContractStatus = @implode(',', $_POST['sContractStatus']);

$tmp = explode('-', $_POST['sContractStatusTime']);

$tmp[0]              = $tmp[0] + 1911;
$bContractStatusTime = $tmp[0] . "-" . $tmp[1] . "-" . $tmp[2];
$tmp                 = null;unset($tmp);

if ($_SESSION['pBusinessView'] == 1 && $_POST['sContractStatusTime'] != '000-00-00') { //有更改權限
    if ($sContractStatus == 1) {
        $signSalseID      = array(); //簽約業務
        $signSalseCheckID = array(); //檢查是否刪除
        $sql              = "UPDATE tScrivener SET sContractStatusTime = '" . $bContractStatusTime . "' WHERE sId ='" . $_POST['id'] . "'";
        $conn->Execute($sql);

        $sql = "SELECT sSales FROM tScrivenerSalesForPerformance WHERE sScrivener = '" . $_POST['id'] . "'";
        $rs  = $conn->Execute($sql);

        $sales = $rs->fields['sSales'];

        $sql = "SELECT sId,sSales,sStopChange,sCreatTime FROM tSalesSign WHERE sType = 1 AND sStore='" . $_POST['id'] . "' ORDER BY sCreatTime DESC";
        $rs  = $conn->Execute($sql);
        while (!$rs->EOF) {
            $signSalseCheckID[] = $rs->fields['sSales'];
            $rs->MoveNext();
        }

        $signSalseID = empty($_POST['signSalseID']) ? [$sales] : $_POST['signSalseID'];

        //新增業務跟更改簽約時間
        foreach ($signSalseID as $k => $v) {
            if (!in_array($v, $signSalseCheckID)) {
                //增加至 tSalesSign
                $sql = "INSERT INTO
                            tSalesSign
                        (
                            sType,
                            sStore,
                            sName,
                            sOffice,
                            sBrand,
                            sArea,
                            sSignDate,
                            sSales
                        ) VALUES
                        (
                            '1',
                            '" . $_POST['id'] . "',
                            '" . $_POST['bStore'] . "',
                            '" . $_POST['bName'] . "',
                            '" . $_POST['bBrand'] . "',
                            '" . $_POST['zip'] . "',
                            '" . $bContractStatusTime . "',
                            '" . $v . "'
                        )";
                $conn->Execute($sql);
            } else {
                $sql = "UPDATE
                            tSalesSign
                        SET
                            sSignDate = '" . $bContractStatusTime . "'
                        WHERE
                            sStore='" . $_POST['id'] . "'  AND sType = 1";
                $conn->Execute($sql);
            }
        }

        //刪除業務
        foreach ($signSalseCheckID as $k => $v) {
            if (!in_array($v, $signSalseID)) {
                $sql = "DELETE FROM
                            tSalesSign
                        WHERE
                            sStore='" . $_POST['id'] . "' AND sSales = '" . $v . "' AND sType = 1";
                $conn->Execute($sql);
            }
        }
    } else if ($sContractStatus != 1) {
        $sql = "UPDATE tScrivener SET sContractStatusTime = '000-00-00' WHERE sId ='" . $_POST['id'] . "'";
        $conn->Execute($sql);

        $sql = "UPDATE tSalesSign SET sSignDate = '0000-00-00',sSales ='0' WHERE sStore ='" . $_POST['id'] . "' AND sType = 1";
        $conn->Execute($sql);
    }
    ##
} else if ($_SESSION['pBusinessView'] == 1 && $_POST['sContractStatusTime'] == '000-00-00') {
    $sql = "UPDATE tScrivener SET sContractStatusTime = '000-00-00' WHERE sId ='" . $_POST['id'] . "'";
    $conn->Execute($sql);

    $sql = "UPDATE tSalesSign SET sSignDate = '0000-00-00',sSales ='0' WHERE sStore ='" . $_POST['id'] . "' AND sType = 1";
    $conn->Execute($sql);
}
##

// $data = $scrivener->GetScrivenerInfo($_POST["id"]);

//分辨會計跟其他人儲存
if (($_SESSION['member_pDep'] == 9 || $_SESSION['member_pDep'] == 10 || $_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 48) && $_POST['change_feedbackData'] == 1) {
    $sql = "UPDATE tScrivener SET  sEditor_Accounting='" . $_SESSION['member_name'] . "',sModify_time_Accounting ='" . date('Y-m-d H:i:s', time()) . "' WHERE sId='" . $_POST['id'] . "'";
    $conn->Execute($sql);

    if ($_SESSION['member_id'] == 6) {
        $sql = "UPDATE tScrivener SET  sEditor='" . $_SESSION['member_name'] . "',sModify_time ='" . date('Y-m-d H:i:s', time()) . "' WHERE sId='" . $_POST['id'] . "'";
        $conn->Execute($sql);
    }

    //回饋資料儲存
    for ($i = 0; $i < count($_POST['fId']); $i++) {
        $feedBakcDataStr = '';
        if (is_array($_POST['feedBackStop'])) {
            if (in_array($_POST['fId'][$i], $_POST['feedBackStop'])) {
                $feedBakcDataStr = "fStop = '1',";
            } else {
                $feedBakcDataStr = "fStop = '0',";
            }
        } else {
            $feedBakcDataStr = "fStop = '0',";
        }

        $sql = "UPDATE
					tFeedBackData
				SET
					fFeedBack ='" . $_POST['fFeedBack'][$i] . "',
					fRtitle ='" . $_POST['fRtitle'][$i] . "',
					fTitle ='" . $_POST['fTitle'][$i] . "',
					fIdentity ='" . $_POST['fIdentity'][$i] . "',
					fIdentityNumber ='" . $_POST['fIdentityNumber'][$i] . "',
					fZipC ='" . $_POST['fZipC'][$i] . "',
					fAddrC ='" . $_POST['fAddrC'][$i] . "',
					fZipR ='" . $_POST['fZipR'][$i] . "',
					fAddrR ='" . $_POST['fAddrR'][$i] . "',
					fMobileNum ='" . $_POST['fMobileNum'][$i] . "',
					fEmail ='" . $_POST['fEmail'][$i] . "',
					" . $feedBakcDataStr . "
					fAccountNum ='" . $_POST['fAccountNum'][$i] . "',
					fAccountNumB ='" . $_POST['fAccountNumB'][$i] . "',
					fAccount ='" . $_POST['fAccount'][$i] . "',
					fAccountName ='" . $_POST['fAccountName'][$i] . "',
					fNote ='" . $_POST['fNote'][$i] . "',
					fIncomeCategory ='" . $_POST['fIncomeCategory'][$i] . "'
				WHERE
					fId = '" . $_POST['fId'][$i] . "';
				";
        mysqli_query($link, $sql);
    }

    for ($i = 0; $i < count($_POST['newTtitle']); $i++) {
        if ($_POST['newTtitle'][$i] != '') {
            $sql = "INSERT INTO tFeedBackData(
						fType,
						fStoreId,
						fFeedBack,
						fRtitle,
						fTitle,
						fIdentity,
						fIdentityNumber,
						fZipC,
						fAddrC,
						fZipR,
						fAddrR,
						fMobileNum,
						fEmail,
						fAccountNum,
						fAccountNumB,
						fAccount,
						fAccountName,
						fNote,
                        fIncomeCategory
					)VALUES(
						'1',
						'" . $_POST['id'] . "',
						'" . $_POST['newFeedBack'][$i] . "',
						'" . $_POST['newRtitle'][$i] . "',
						'" . $_POST['newTtitle'][$i] . "',
						'" . $_POST['newIdentity'][$i] . "',
						'" . $_POST['newIdentityNumber'][$i] . "',
						'" . $_POST['newzipC'][$i] . "',
						'" . $_POST['newaddrC'][$i] . "',
						'" . $_POST['newzipR'][$i] . "',
						'" . $_POST['newaddrR'][$i] . "',
						'" . $_POST['newMobileNum'][$i] . "',
						'" . $_POST['newEmail'][$i] . "',
						'" . $_POST['newAccountNum'][$i] . "',
						'" . $_POST['newAccountNumB'][$i] . "',
						'" . $_POST['newAccount'][$i] . "',
						'" . $_POST['newAccountName'][$i] . "',
						'" . $_POST['newNote'][$i] . "',
						'" . $_POST['newIncomeCategory'][$i] . "'
					)";
            mysqli_query($link, $sql);
        }
    }
} else {
    $sql = "UPDATE tScrivener SET sEditor='" . $_SESSION['member_name'] . "',sModify_time ='" . date('Y-m-d H:i:s', time()) . "' WHERE sId='" . $_POST['id'] . "'";
    $conn->Execute($sql);
}

###停用APP帳號也要停用##
if ($_POST['sStatus'] == 3) {
    $sql = "UPDATE tAppAccount SET aStatus = 2 WHERE aParentId = 'SC" . str_pad($_POST['id'], 4, '0', STR_PAD_LEFT) . "'";
    $conn->Execute($sql);
}
##

//埋log紀錄
$logs->writelog('scrivenerSave', '編修地政士(' . $_POST['sName'] . ' SC' . str_pad($_POST['id'], 4, '0', STR_PAD_LEFT) . ')');
##

##銀行儲存
for ($i = 0; $i < count($_POST['sAccountId14']); $i++) {
    $sql = "UPDATE tScrivenerBank SET sBankMain ='" . $_POST['sAccountNum14'][$i] . "',sBankBranch ='" . $_POST['sAccountNum24'][$i] . "',sBankAccountNo = '" . $_POST['sAccount34'][$i] . "',sBankAccountName = '" . $_POST['sAccount44'][$i] . "' WHERE sId ='" . $_POST['sAccountId14'][$i] . "'";
    $conn->Execute($sql);
}

$sql = "UPDATE tScrivenerBank SET sUnUsed = 0 WHERE sScrivener = '" . $_POST['id'] . "'";
$conn->Execute($sql);
for ($i = 0; $i < count($_POST['sAccountUnused4']); $i++) {
    $sql = "UPDATE tScrivenerBank SET sUnUsed ='1' WHERE sId ='" . $_POST['sAccountUnused4'][$i] . "'";
    $conn->Execute($sql);
}

for ($i = 0; $i < count($_POST['NewBankMain']); $i++) {
    if ($_POST['NewBankAccountNo'] != '' && $_POST['NewBankAccountName'] != '') {
        $sql = "INSERT INTO
					tScrivenerBank
				(
					sBankMain,
					sBankBranch,
					sBankAccountNo,
					sBankAccountName,
					sUnUsed,
					sScrivener
				) VALUES (
					'" . $_POST['NewBankMain'][$i] . "',
					'" . $_POST['NewBankBranch'][$i] . "',
					'" . $_POST['NewBankAccountNo'][$i] . "',
					'" . $_POST['NewBankAccountName'][$i] . "',
					'" . $_POST['NewUnUsed'][$i] . "',
					'" . $_POST['id'] . "'
				)";
        $conn->Execute($sql);
        unset($sql);
    }
}
##

//如果勾選黑名單，判斷是否黑名單沒有資料，沒資料就寫入
if ($_POST['blacklist'][0] == 1 && ($data['sBlackListId'] == '' || $data['sBlackListId'] == 0)) {
    $sql = "INSERT INTO
                tScrivenerBlackList
            SET
                sName = '" . $_POST['sName'] . "',
                sIdentifyId = '" . $_POST['sIdentifyId'] . "',
                sOffice = '" . $_POST['sOffice'] . "',
                sZip = '" . $_POST['zip2'] . "',
                sAddress = '" . $_POST['addr2'] . "',
                sCreator = '" . $_SESSION['member_id'] . "',
                sCreatTime = '" . date('Y-m-d H:i:s') . "',
                sEditor = '" . $_SESSION['member_id'] . "'";
    $conn->Execute($sql);
    $id = $conn->Insert_ID();

    $sql = "UPDATE tScrivener SET sBlackListId = '" . $id . "' WHERE sId = '" . $_POST['id'] . "'";
    $conn->Execute($sql);
}

if ($_POST['blacklist'][0] != 1) {
    $sql = "SELECT sBlackListId FROM tScrivener WHERE sId = '" . $_POST['id'] . "' AND sBlackListId != '' AND sBlackListId != 0";
    $rs  = $conn->Execute($sql);

    if (!$rs->EOF) {
        $sql = "UPDATE tScrivener SET sBlackListId = '' WHERE sId = '" . $_POST['id'] . "'";
        $conn->Execute($sql);

        $sql = "UPDATE tScrivenerBlackList SET sDelete = '1' WHERE sId = '" . $rs->fields['sBlackListId'] . "'";
        $conn->Execute($sql);
    }
}

//活動
if ($_SESSION['member_act_report'] == 1) {
    if (!empty($_POST['activities'])) {
        foreach ($_POST['activities'] as $v) {
            if (empty($_POST['activity_' . $v . '_rule'])) {
                $sql = 'DELETE FROM
                            tActivityRecords
                        WHERE
                            aActivityId = "' . $v . '"
                            AND aIdentity = "S"
                            AND aStoreId = "' . $_POST['id'] . '";';
            } else {
                $priority = (strtoupper($_POST['activity_' . $v . '_priority'][0]) == 'Y') ? 'Y' : 'N';

                $sql = 'INSERT INTO
                            tActivityRecords
                        (
                            aActivityId,
                            aIdentity,
                            aStoreId,
                            aRule,
                            aGift,
                            aPriority
                        ) VALUES (
                            "' . $v . '",
                            "S",
                            "' . $_POST['id'] . '",
                            "' . $_POST['activity_' . $v . '_rule'] . '",
                            "' . $_POST['activity_' . $v . '_gift'] . '",
                            "' . $priority . '"
                        ) ON DUPLICATE KEY UPDATE
                            aRule = "' . $_POST['activity_' . $v . '_rule'] . '",
                            aGift = "' . $_POST['activity_' . $v . '_gift'] . '",
                            aPriority = "' . $priority . '";';
                $priority = null;unset($priority);
            }
            $conn->Execute($sql);

            if (empty($_POST['activity_' . $v . '_rule'])) {
                $sql = 'DELETE FROM
                            tActivityRecordsExt
                        WHERE
                            aActivityId = "' . $v . '"
                            AND aIdentity = "S"
                            AND aStoreId = "' . $_POST['id'] . '";';
                $conn->Execute($sql);
            } else {
                if ($v == 2) {
                    require dirname(__DIR__) . '/activities/2/scrivener_save.php';
                }
            }
        }
    }
}
##

//20230421 回饋金隨案支付
// $feedback_bank_before; //原始回饋金銀行資料(在未更新時取得)
$feedback_bank_after = $scrivener->GetScrivenerFeedbackBank($_POST["id"]); //新回饋金銀行資料

if (feedback_bank_diff($feedback_bank_before, $feedback_bank_after)) {
    $pay_by_case_scrivener = new PayByCaseScrivener(new first1DB);
    $pay_by_case_scrivener->modifyAffectCaseBankAccountByScrivener($_POST["id"]);
}

/**
 * 檢查是否有切換回饋方式與回饋比率
 * $data['sFeedDateCat'] = 原始設定
 * $_POST['feedDateCat'] = 更新設定
 */

//變更回饋方式
$paybycase = new PayByCase(new first1DB);
if (($data['sFeedDateCat'] != $_POST['feedDateCat']) //變更回饋金方式
     || ($data['sRecall'] != $_POST['sRecall']) //變更回饋比率
     || ($data['sSpRecall'] != $_POST['sSpRecall'])) { //變更特殊回饋比率
    $cases = getWorkingCases($conn, $_POST["id"]); //取得進行中案件

    if (!empty($cases)) {
        foreach ($cases as $cId) {
            $paybycase->salesConfirmList($cId);
        }
    }
}

echo "儲存完成";
