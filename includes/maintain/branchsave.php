<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/brand.class.php';
require_once dirname(dirname(__DIR__)) . '/class/intolog.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

require_once dirname(__DIR__) . '/writelog.php';

require_once __DIR__ . '/feedBackData.php';

use First1\V1\PayByCase\PayByCase;

//取得地政士正在進行中的案件
function getWorkingCases(&$conn, $bId)
{
    $sql = 'SELECT a.cCertifiedId FROM tContractCase AS a JOIN tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId WHERE (b.cBranchNum = ' . $bId . ' OR b.cBranchNum1 = ' . $bId . ' OR b.cBranchNum2 = ' . $bId . ' OR b.cBranchNum3 = ' . $bId . ') AND a.cCaseStatus = 2;';
    $rs  = $conn->Execute($sql);

    $cIds = [];
    while (!$rs->EOF) {
        $cIds[] = $rs->fields['cCertifiedId'];
        $rs->MoveNext();
    }

    return $cIds;
}

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '編修特定仲介店明細');

//預載log物件
$logs = new Intolog();
##

$brand = new Brand();

//日期轉換
$_POST["bCashierOrderDate"] = preg_match("/0000-00-00/", $_POST["bCashierOrderDate"]) ? '' : date_convert($_POST["bCashierOrderDate"]);
$_POST["bCashierOrderSave"] = preg_match("/0000-00-00/", $_POST["bCashierOrderSave"]) ? '' : date_convert($_POST["bCashierOrderSave"]);
$_POST["bStatusDateStart"]  = preg_match("/000-00-00/", $_POST["bStatusDateStart"]) ? '' : date_convert($_POST["bStatusDateStart"]);
$_POST["bStatusDateEnd"]    = preg_match("/000-00-00/", $_POST["bStatusDateEnd"]) ? '' : date_convert($_POST["bStatusDateEnd"]);
$_POST["bSalesDate"]        = preg_match("/000-00-00/", $_POST["bSalesDate"]) ? '' : date_convert($_POST["bSalesDate"]);
##

$data = $brand->GetBranch($_POST["id"]);

if ($_SESSION['member_pFeedBackModify'] == 0) {
    $_POST['bRecall']    = $data[0]['bRecall'];
    $_POST['bScrRecall'] = $data[0]['bScrRecall'];
}

$str = $brand->SaveBranch($_POST);

if ($data[0]['bRecall'] != $_POST['bRecall'] || ($data[0]['bScrRecall'] != $_POST['bScrRecall']) || ($data[0]['bGroup'] != $_POST['bGroup'])) {
    $txt = getFeedMoney('b', $_POST["id"], '', $data[0]['bFeedDateCat']);

    echo '回饋金異動號碼:' . @implode(',', $txt) . "\r\n";
    $txt = null;unset($txt);
}

####合作契約書有調整，則回饋金要重算##
$bCooperationHas = (int)@implode(",", $_POST['bCooperationHas']);
$bCooperationHas = empty($bCooperationHas) ? '': $bCooperationHas;

$tlog->updateWrite($_SESSION['member_id'], json_encode(['bId'=> $_POST["id"], 'old' => $data[0]['bCooperationHas'], 'new' => $bCooperationHas]), '合作契約書參數');
if (($data[0]['bCooperationHas'] == 0 && $bCooperationHas === 1) || ($data[0]['bCooperationHas'] == 1 && $bCooperationHas == '')) {
    $txt = getFeedMoney('b', $_POST["id"]);

    echo '回饋金異動號碼:' . @implode(',', $txt) . "\r\n";
    $tlog->updateWrite($_SESSION['member_id'], json_encode($txt), '合作契約書調整回饋金重算');
    $txt = null;unset($txt);
}
#####

$OnlineCases = [];//進行中案件
//服務費動撥提醒

$sql = "SELECT
			cr.cCertifyId, cr.cBranchNum, cr.cBranchNum1, cr.cBranchNum2, cr.cBranchNum3
		FROM
			tContractRealestate AS cr
		LEFT JOIN
			tContractCase AS cc ON cc.cCErtifiedId = cr.cCertifyId
		WHERE  (cr.cBranchNum = '" . $_POST["id"] . "' OR cr.cBranchNum1 = '" . $_POST["id"] . "' OR cr.cBranchNum2 = '" . $_POST["id"] . "' OR cr.cBranchNum3 = '" . $_POST["id"] . "') AND cc.cCaseStatus = 2";
$rs  = $conn->Execute($sql);
$txt = array();
while (!$rs->EOF) {
    array_push($txt, $rs->fields['cCertifyId']);
    if($_POST["id"] == $rs->fields['cBranchNum']) {
        $OnlineCases[0] = $rs->fields['cCertifyId'];
    }
    for($i = 1; $i < 4; $i++) {
        if($_POST["id"] == $rs->fields['cBranchNum' . $i]) {
            $OnlineCases[$i] = $rs->fields['cCertifyId'];
        }
    }

    $rs->MoveNext();
}

echo '服務費動撥提醒案件號碼:' . @implode(',', $txt) . "\r\n";
$tlog->updateWrite($_SESSION['member_id'], json_encode($txt), '服務費動撥提醒案件號碼');
$txt = null;unset($txt);
#####

//儲存紀錄
//恢復啟用
if ($data[0]['bStatus'] == 2 && $_POST['bStatus'] == 1) {
    $sql = "UPDATE tBranch SET bReStart = '" . date('Y-m-d H:i:s') . "' WHERE bId = '" . $_POST["id"] . "'";
    $conn->Execute($sql);
}
##

$bContractStatus = @implode(',', $_POST['bContractStatus']);

if ($_POST['bContractStatusTime'] != '' && $_POST['bContractStatusTime'] != '000-00-00') {
    $tmp = explode('-', $_POST['bContractStatusTime']);

    $tmp[0]              = $tmp[0] + 1911;
    $bContractStatusTime = $tmp[0] . "-" . $tmp[1] . "-" . $tmp[2];
    $tmp                 = null;unset($tmp);
} else {
    $bContractStatusTime = "0000-00-00";
}

if ($bContractStatus == 1) {
    $signSalseID      = array(); //簽約業務
    $signSalseCheckID = array(); //檢查是否刪除

    $sql = "UPDATE tBranch SET bContractStatusTime = '" . $bContractStatusTime . "' WHERE bId ='" . $_POST['id'] . "'";
    $conn->Execute($sql);

    $sql   = "SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = '" . $_POST['id'] . "';";
    $rs    = $conn->Execute($sql);
    $sales = $rs->fields['bSales'];

    $sql = "SELECT sId,sSales,sStopChange,sCreatTime FROM tSalesSign WHERE sType = '2' AND sStore='" . $_POST['id'] . "' ORDER BY sCreatTime DESC";
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
                        '2',
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
            $sql = "UPDATE tSalesSign SET sSignDate = '" . $bContractStatusTime . "' WHERE sStore='" . $_POST['id'] . "'  AND sType = 2";
            $conn->Execute($sql);
        }
    }

    //刪除業務
    foreach ($signSalseCheckID as $k => $v) {
        if (!in_array($v, $signSalseID)) {
            $sql = "DELETE FROM tSalesSign WHERE sStore='" . $_POST['id'] . "' AND sSales = '" . $v . "' AND sType = 2";
            $conn->Execute($sql);
        }
    }

} elseif ($bContractStatus != 1) {
    $sql = "UPDATE tBranch SET bContractStatusTime = '0000-00-00' WHERE bId ='" . $_POST['id'] . "'";
    $conn->Execute($sql);

    $sql = "UPDATE tSalesSign SET sSignDate = '0000-00-00',sSales ='0' WHERE sStore ='" . $_POST['id'] . "' AND sType =2";
    $conn->Execute($sql);
}
##

$brand = $brand->GetBrand($_POST['bBrand']);

// $sql    = "SELECT * FROM tBranch WHERE bId = '" . $_POST['id'] . "'";
// $rs     = $conn->Execute($sql);
// $data[] = $rs->fields;

//關店 LINE跟APP帳號要關
if ($_POST['bStatus'] == 2) {
    $sql = "UPDATE tLineAccount SET lStatus = 'N' WHERE lTargetCode = '" . $brand['bCode'] . str_pad($_POST['id'], 5, '0', STR_PAD_LEFT) . "'";
    $conn->Execute($sql);

    $sql = "UPDATE tAppAccount SET aStatus = 2 WHERE aParentId = '" . $brand['bCode'] . str_pad($_POST['id'], 5, '0', STR_PAD_LEFT) . "'";
    $conn->Execute($sql);
}
##

$sql = "SELECT * FROM tCategoryRealty WHERE bId='" . $_POST['id'] . "'";
$rs  = $conn->Execute($sql);

if ($rs->fields['cBrandId'] != $_POST['bBrand'] || $rs->fields['cBranch'] != $_POST['bStore'] || $rs->fields['cCompany'] != $_POST['bName']) {
    $sql2 = "UPDATE tCategoryRealty SET cBrandId='" . $_POST['bBrand'] . "',cBranch='" . $_POST['bStore'] . "',cCompany='" . $_POST['bName'] . "',cBrand ='" . $brand['bName'] . "' WHERE bId='" . $_POST['id'] . "'";
    $conn->Execute($sql2);
}

//分辨會計跟其他人儲存
if (($_SESSION['member_pDep'] == 9 || $_SESSION['member_pDep'] == 10 || $_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 48) && $_POST['change_feedbackData'] == 1) {
    $sql = "UPDATE tBranch SET bEditor_Accounting='" . $_SESSION['member_name'] . "',bModify_time_Accounting ='" . date('Y-m-d H:i:s', time()) . "' WHERE bId='" . $_POST['id'] . "'";
    $conn->Execute($sql);

    if ($_SESSION['member_id'] == 6) {
        $sql = "UPDATE tBranch SET  bEditor='" . $_SESSION['member_name'] . "',bModify_time ='" . date('Y-m-d H:i:s', time()) . "' WHERE bId='" . $_POST['id'] . "'";
        $conn->Execute($sql);
    }

    //回饋資料儲存
    $ck = 0;

    for ($i = 0; $i < count($_POST['fId']); $i++) {
        $feedBakcDataStr = "fStop = '0',";
        if (is_array($_POST['feedBackStop']) && in_array($_POST['fId'][$i], $_POST['feedBackStop'])) {
            $feedBakcDataStr = "fStop = '1',";
        }

        #永晟不動產經紀業有限公司
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
					fAccountNum ='" . $_POST['fAccountNum'][$i] . "',
					fAccountNumB ='" . $_POST['fAccountNumB'][$i] . "',
					fAccount ='" . $_POST['fAccount'][$i] . "',
					fAccountName ='" . $_POST['fAccountName'][$i] . "',
					" . $feedBakcDataStr . "
					fNote ='" . $_POST['fNote'][$i] . "'
				WHERE
					fId = '" . $_POST['fId'][$i] . "';
				";
        $conn->Execute($sql);

        //合作契約書
        if (!empty($_POST['fAccountName'][$i])) {
            $ck = 1;
        }
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
						fNote
					)VALUES(
						'2',
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
						'" . $_POST['newNote'][$i] . "'
					)";
            $conn->Execute($sql);

            if (!empty($_POST['newAccountName'][$i])) {
                $sql = "UPDATE tBranch SET bCooperationHas = 1 WHERE bId ='" . $_POST['id'] . "'";
                $conn->Execute($sql);
                //新增回饋金帳戶也會自動改成有合契 所以 回饋金也需要重算 20250102
                if ($data[0]['bCooperationHas'] == 0) {
                    $bCooperationHas = 1;
                    $txt = getFeedMoney('b', $_POST["id"]);

                    echo '回饋金異動號碼:' . @implode(',', $txt) . "\r\n";
                    $tlog->updateWrite($_SESSION['member_id'], json_encode($txt), '合作契約書調整回饋金重算');
                    $txt = null;unset($txt);
                }
            }
        }
    }
    ##
} else {
    $sql = "UPDATE tBranch SET  bEditor='" . $_SESSION['member_name'] . "',bModify_time ='" . date('Y-m-d H:i:s', time()) . "' WHERE bId='" . $_POST['id'] . "'";
    $conn->Execute($sql);
}
##

if ($_POST['bRgfirst'] == 1) {
    $sql   = "SELECT * FROM tRgMoney WHERE rAccount ='" . $brand['bCode'] . str_pad($_POST['id'], 5, '0', STR_PAD_LEFT) . "' AND rDate >='" . date('Y-m') . "-01' AND rDate <='" . date('Y-m') . "-31'";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total == 0) {
        $sql = "INSERT INTO
                    tRgMoney
                (
                    rAccount,
                    rIdentity,
                    rRgMoney,
                    rRgBalance,
                    rDate
                ) VALUES (
                    '" . $brand['bCode'] . str_pad($_POST['id'], 5, '0', STR_PAD_LEFT) . "',
                    'R',
                    '" . $_POST['bRgMoney'] . "',
                    '" . $_POST['bRgMoney'] . "',
                    '" . date('Y-m-d') . "'
                )";
        $conn->Execute($sql);
    }
}
###

//編修55紀錄(台屋加盟店寫到台屋資料庫!!!)
//20230221 拿掉此功能
##

//埋log紀錄
$logs->writelog('branchSave', '編修仲介店(' . $_POST['bStore'] . ' ' . $_POST['id'] . ')');
##

if (!empty($_POST['bAccountId14'])) {
    for ($i = 0; $i < count($_POST['bAccountId14']); $i++) {
        $sql = "UPDATE tBranchBank SET bBankMain ='" . $_POST['bAccountNum14'][$i] . "',bBankBranch ='" . $_POST['bAccountNum24'][$i] . "',bBankAccountNo = '" . $_POST['bAccount34'][$i] . "',bBankAccountName = '" . $_POST['bAccount44'][$i] . "' WHERE bId ='" . $_POST['bAccountId14'][$i] . "'";
        $conn->Execute($sql);
    }
}

$sql = "UPDATE tBranchBank SET bUnUsed = 0 WHERE bBranch = '" . $_POST['id'] . "'";
$conn->Execute($sql);

if (!empty($_POST['bAccountUnused4'])) {
    for ($i = 0; $i < count($_POST['bAccountUnused4']); $i++) {
        $sql = "UPDATE tBranchBank SET bUnUsed ='1' WHERE bId ='" . $_POST['bAccountUnused4'][$i] . "'";
        $conn->Execute($sql);
    }
}

if (!empty($_POST['NewBankMain'])) {
    for ($i = 0; $i < count($_POST['NewBankMain']); $i++) {
        if ($_POST['NewBankAccountNo'] != '' && $_POST['NewBankAccountName'] != '') {
            $sql = "INSERT INTO
						tBranchBank
					(
						bBankMain,
						bBankBranch,
						bBankAccountNo,
						bBankAccountName,
						bUnUsed,
						bBranch
					) VALUES (
						'" . $_POST['NewBankMain'][$i] . "',
						'" . $_POST['NewBankBranch'][$i] . "',
						'" . $_POST['NewBankAccountNo'][$i] . "',
						'" . $_POST['NewBankAccountName'][$i] . "',
						'" . $_POST['NewUnUsed'][$i] . "',
						'" . $_POST['id'] . "'
						)";
            $conn->Execute($sql);
            $sql = null;unset($sql);
        }
    }
}

//活動
if ($_SESSION['member_act_report'] == 1) {
    $sql = "UPDATE
				tBranch
			SET
				`bAct_2020` = '" . $_POST['act2020'] . "',
				`bAct_2020_gift` = '" . $_POST['gift2020'] . "',
				`bAct_2021` = '" . $_POST['act2021'] . "',
				`bAct_2021_gift` = '" . $_POST['gift2021'] . "'
			WHERE
				bId = '" . $_POST['id'] . "'";
    $conn->Execute($sql);

    //
    if (!empty($_POST['activities'])) {
        foreach ($_POST['activities'] as $v) {
            if (empty($_POST['activity_' . $v . '_rule'])) {
                $sql = 'DELETE FROM
                            tActivityRecords
                        WHERE
                            aActivityId = "' . $v . '"
                            AND aIdentity = "R"
                            AND aStoreId = "' . $_POST['bId'] . '";';
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
                            "R",
                            "' . $_POST['bId'] . '",
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
                            AND aIdentity = "R"
                            AND aStoreId = "' . $_POST['bId'] . '";';
                $conn->Execute($sql);
            } else {
                if ($v == 2) {
                    require dirname(__DIR__) . '/activities/2/branch_save.php';
                }
            }
        }
    }
    ##
}

/**
 * 檢查是否有切換回饋方式與回饋比率
 * $data['sFeedDateCat'] = 原始設定
 * $_POST['feedDateCat'] = 更新設定
 */

//變更回饋方式
$paybycase = new PayByCase(new first1DB);
if (($data[0]['bScrRecall'] != $_POST['bScrRecall'])||($data[0]['bCooperationHas'] == 0 && $bCooperationHas === 1)) { //代書回饋比率(特殊回饋) 合作契約書改成有
    $cases = getWorkingCases($conn, $_POST["id"]); //取得進行中案件

    if (!empty($cases)) {
        foreach ($cases as $cId) {
            $paybycase->salesConfirmList($cId);
        }
    }
}

//更新 進行中案件 店資料 2024-12-03
if(count($OnlineCases) > 0){
    foreach ($OnlineCases as $k => $v) {
        if($k == 0) {
            $sql_realestate = "UPDATE tContractRealestate SET 
                               cZip = '".$_POST['zip']."',
                               cAddress = '".$_POST['addr']."',
                               cFaxArea = '".$_POST['bFaxArea']."',
                               cFaxMain = '".$_POST['bFaxMain']."',
                               cTelArea = '".$_POST['bTelArea']."',
                               cTelMain = '".$_POST['bTelMain']."' 
                            WHERE cCertifyId = '".$v."'";
        } else {
            $sql_realestate = "UPDATE tContractRealestate SET 
                               cZip".$k." = '".$_POST['zip']."',
                               cAddress".$k." = '".$_POST['addr']."',
                               cFaxArea".$k." = '".$_POST['bFaxArea']."',
                               cFaxMain".$k." = '".$_POST['bFaxMain']."',
                               cTelArea".$k." = '".$_POST['bTelArea']."',
                               cTelMain".$k." = '".$_POST['bTelMain']."' 
                            WHERE cCertifyId = '".$v."'";
        }

        $rs_realestate  = $conn->Execute($sql_realestate);
    }
}

echo "儲存完成";