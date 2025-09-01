<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/class/lineMessage.php';
require_once dirname(dirname(__DIR__)) . '/class/slack.class.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

use First1\V1\Notify\Slack;

define('MANAGER', 3);                                         //副總
define('MANAGER_TOKEN', 'Ue3a988aae4cc2d611cd4b4ed56420d85'); //副總LineId

$_REQUEST = escapeStr($_REQUEST);

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_REQUEST), '發送未收足通知');

$cId = $_REQUEST['cId'];
$cat = $_REQUEST['cat'];

// $cId = '121601430'; //測試用
// $cId = '090099227'; //測試用
// $cId = '130056364'; //測試用
// $cId = '121601430'; //測試用
// $cat = 1;           //測試用

if (empty($cId) || empty($cat)) {
    echo '參數錯誤';
    exit;
}

if (! preg_match('/^[0-9]{9}$/', $cId)) {
    echo '參數錯誤(CID)';
    exit;
}

if (! in_array($cat, [1, 2])) {
    echo '參數錯誤(CAT)';
    exit;
}

//檢查業務是否已確認
if (confirmSalesChecked($cId)) {
    $cat = 3; //已確認、直發主管確認
}

$data = [];
$line = new LineMsg();

$pushDetail = [];
switch ($cat) {
    case 1:
        //實習業務
        $TraineeZip = [];

        $sql = 'SELECT
            a.zZip, a.zTrainee, pId AS sales
        FROM
            tPeopleInfo AS i LEFT JOIN tZipArea AS a ON i.pTest = a.zTrainee
        WHERE
            pDep = 7 and pJob = 1 AND pTest != 0 AND pId != 38 AND pId != 72';
        $rs = $conn->Execute($sql);
        while (! $rs->EOF) {
            $TraineeZip[$rs->fields['sales']][] = $rs->fields['zZip'];
            $rs->MoveNext();
        }

        $sql = 'SELECT
            cc.cCertifiedId,
            cc.cCaseFeedback,
            cc.cCaseFeedback1,
            cc.cCaseFeedback2,
            cc.cCaseFeedback3,
            cc.cFeedbackTarget,
            cc.cFeedbackTarget1,
            cc.cFeedbackTarget2,
            cc.cFeedbackTarget3,
            cc.cScrivenerSpRecall,
            cs.cScrivener,
            cr.cBranchNum,
            cr.cBranchNum1,
            cr.cBranchNum2,
            cr.cBranchNum3
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
		WHERE
			cc.cCertifiedId = "' . $cId . '";';
        $rs       = $conn->Execute($sql);
        $caseData = $rs->fields;

        //蒐集所有台屋直營店家以備後續比對用
        $store = [];

        $sql = 'SELECT bId fROM tBranch WHERE bCategory = 2 AND bBrand = 1;';
        $rs  = $conn->Execute($sql);
        while (! $rs->EOF) {
            array_push($store, $rs->fields['bId']);
            $rs->MoveNext();
        }

        $checkTwStore = 0; //1:直營，直營給政耀

        $sales = [];
        if ($caseData['cCaseFeedback'] == 0) {
            $sales = getSales($sales, $caseData['cFeedbackTarget'], $caseData['cScrivener'], $caseData['cBranchNum']);
        }

        if ($caseData['cCaseFeedback1'] == 0) {
            $sales = getSales($sales, $caseData['cFeedbackTarget1'], $caseData['cScrivener'], $caseData['cBranchNum1']);
        }

        if ($caseData['cCaseFeedback2'] == 0) {
            $sales = getSales($sales, $caseData['cFeedbackTarget2'], $caseData['cScrivener'], $caseData['cBranchNum2']);
        }

        if ($caseData['cCaseFeedback3'] == 0) {
            $sales = getSales($sales, $caseData['cFeedbackTarget3'], $caseData['cScrivener'], $caseData['cBranchNum3']);
        }

        if (! empty($caseData['cScrivenerSpRecall'])) {
            $sales = array_merge($sales, getScrivenerSales($caseData['cScrivener']));

        }

        //通知業務
        if (empty($sales)) {
            $pushDetail[] = 'Line帳號:無業務發送;';
            slackSend(implode(',', $pushDetail) . '(未收足通知: ' . $cId . ')');
        }

        if (! empty($sales)) {
            $sales = array_unique($sales);

            //取得業務姓名
            $sql       = 'SELECT pId, pName FROM tPeopleInfo WHERE pId IN(' . implode(',', $sales) . ') AND pJob = 1;';
            $rs        = $conn->Execute($sql);
            $salesName = [];
            while (! $rs->EOF) {
                $salesName[$rs->fields['pId']] = $rs->fields['pName'];
                $rs->MoveNext();
            }

            $sql = 'SELECT lLineId,lTargetCode, lNickName FROM tLineAccount WHERE lpId IN(' . implode(',', $sales) . ') AND lStatus = "Y";';
            $rs  = $conn->Execute($sql);

            $total = $rs->RecordCount();
            while (! $rs->EOF) {
                $v = enCrypt('lineId=' . $rs->fields['lLineId'] . '&s=' . $rs->fields['lTargetCode'] . '&c=' . $rs->fields['lIdentity'] . '&cId=' . $cId);

                $_salesName = empty($salesName) ? '' : '(' . implode('、', $salesName) . ')';

                $data['lineId']    = $rs->fields['lLineId'];
                $data['btn_url']   = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v=' . $v;
                $data['title']     = '履保費未收足通知';
                $data['text']      = '保證號碼:' . $cId . '，請填寫原因並審核' . $_salesName;
                $data['btn_label'] = '點我填寫';

                $pushResult = $line->sendFlexTemplateMsg($data);

                if ($pushResult->http_code == 200) {
                    $pushDetail[] = 'Line帳號:' . $rs->fields['lNickName'] . '發送成功;';
                    #slackSend(implode(',', $pushDetail) . '(未收足通知: ' . $cId . ')');
                } else {
                    $pushDetail[] = 'Line帳號:' . $rs->fields['lNickName'] . '發送失敗;';
                    slackSend(implode(',', $pushDetail) . '(未收足通知: ' . $cId . ')');
                }

                $rs->MoveNext();
            }
        }

        $store = null;unset($store);

        if (empty($pushDetail)) {
            $pushDetail[] = 'Line帳號:無業務發送;';
        }

        echo implode(',', $pushDetail);
        break;

    case 2:
        supervisorConfirm($cId);
        break;

    case 3:
        supervisorConfirm($cId);
        echo 'Line帳號:主管審核通知發送成功';
        break;
    default:
        # code...
        break;
}

function getScrivenerSales($id)
{
    global $conn, $TraineeZip;

    $sales = [];

    //實習業務
    if (is_array($TraineeZip)) {
        foreach ($TraineeZip as $key => $value) {
            if (empty($value) || ! is_array($value)) {
                continue;
            }

            $sql = 'SELECT sId FROM tScrivener WHERE sCpZip1 IN ("' . implode('","', $value) . '") AND sId = "' . $id . '";';
            $rs  = $conn->Execute($sql);

            if ($rs->RecordCount() > 0) {
                array_push($sales, $key);
            }
        }
    }

    //地政士業務
    $sql = 'SELECT sSales FROM tScrivenerSales WHERE sScrivener = "' . $id . '";';
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        array_push($sales, $rs->fields['sSales']);
        $rs->MoveNext();
    }

    return $sales;
}

function getBranchSales($id)
{
    global $conn, $TraineeZip;

    $sales = [];
    if (is_array($TraineeZip)) {
        foreach ($TraineeZip as $key => $value) {
            $sql = 'SELECT bId FROM tBranch WHERE bZip IN ("' . @implode('","', $value) . '") AND bId = "' . $id . '";';
            $rs  = $conn->Execute($sql);

            if ($rs->RecordCount() > 0) {
                array_push($sales, $key);
            }
        }

    }

    //仲介業務
    $sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '" . $id . "'";
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        array_push($sales, $rs->fields['bSales']);
        $rs->MoveNext();
    }

    return $sales;
}

//依據回饋對象決定業務身分
function getSales($sales, $target, $scrivener, $branchNum)
{
    global $store;

    $salesId = [];

    //回饋給仲介
    if (($target == 1) && ($branchNum > 0)) {
        if (in_array($branchNum, $store)) {
            $salesId[] = MANAGER;
        } else {
            $salesId = getBranchSales($branchNum);
        }
    }

    //回饋給地政士
    if ($target == 2) {
        $salesId = getScrivenerSales($scrivener);
    }

    if (empty($salesId)) {
        return $sales;
    }

    return array_merge($sales, $salesId);
}

//slack發送
function slackSend($message)
{
    global $env;

    if (empty($message)) {
        return false;
    }

    $slack = Slack::getInstance($env['slack']['token'], $env['slack']['channelToken']);
    return $slack->chatPostMessage($message, $env['slack']['defaultChannel']);
}

//發送主管審核通知
function supervisorConfirm($cId)
{
    global $line;

    $v = enCrypt('lineId=' . MANAGER_TOKEN . '&s=SC0224&c=O&cId=' . $cId);

    $data['lineId']    = MANAGER_TOKEN;
    $data['btn_url']   = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v=' . $v;
    $data['title']     = '履保費未收足通知';
    $data['text']      = '保證號碼:' . $cId . '，請審核';
    $data['btn_label'] = '點我審核';

    return $line->sendFlexTemplateMsg($data);
}

//檢查業務是否已確認
function confirmSalesChecked($cId)
{
    global $conn;

    $sql = 'SELECT cId FROM tContractIncome WHERE cCertifiedId = "' . $cId . '" AND cInspetor > 0;';
    $rs  = $conn->Execute($sql);

    if ($rs->RecordCount() > 0) {
        return true;
    }

    return false;
}
