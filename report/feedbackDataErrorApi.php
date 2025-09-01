<?php
/**
 * 2025-01-23 API版本
 * 檢查1:回饋金回饋資料是否有異常(不檢查有審核通過的資料)
 * 檢查2:回饋金審核過的回饋是否遭到覆蓋
 * 輸入參數 cid 合約書編號
 */
header("Content-Type:text/html; charset=utf-8");

require_once dirname(__DIR__) . '/.env.php';
include_once dirname(__DIR__) . '/configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/openadodb.php';
include_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/lib/contractBank.php';
require_once dirname(__DIR__) . '/class/slack.class.php';

use First1\V1\Notify\Slack;

$type = 'c';
$id = trim($_GET['cid']);

$output['msg'] = '';
$output['cid'] = $id;

if (!empty($id)) {
    $errorDetail2 = checkFeedMoneyReview($id);
    $errorDetail = getFeedMoney($type, $id, $id2 = '', $FeedDateCat = '');

    if (count($errorDetail2) > 0) {
        foreach ($errorDetail2 as $k => $v) {
            $output['msg'] = '修改審核過後：' . implode('<br>', $v);
        }
    }

    if (count($errorDetail) > 0) {
        foreach ($errorDetail as $k => $v) {
            $output['msg'] = implode('<br>', $v);
        }
    }
}

//echo json_encode($output, JSON_UNESCAPED_UNICODE);
if(!empty($output['msg'])) {
    Slack::channelSend($id.'('.$output['msg'].')','https://hooks.slack.com/services/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', '回饋金異常通知');
}
exit;

###############重新計算回饋金###################
function getFeedMoney($type, $id, $id2 = '', $FeedDateCat = '')
{
    global $conn;

    $errorData = [];
    $cCertifiedId = array();

    $nowMonth = date('m');
    if ($FeedDateCat == 1) { //FeedDateCat 0:季1:月
        $sDate = date('Y-m') . "-01";
        $eDate = date('Y-m') . "-31";
    } else {
        if ($nowMonth >= 1 && $nowMonth <= 3) {
            $sDate = date('Y') . "-01-01";
            $eDate = date('Y') . "-03-31";
        } elseif ($nowMonth >= 4 && $nowMonth <= 6) {
            $sDate = date('Y') . "-04-01";
            $eDate = date('Y') . "-06-30";
        } elseif ($nowMonth >= 7 && $nowMonth <= 9) {
            $sDate = date('Y') . "-07-01";
            $eDate = date('Y') . "-09-30";
        } else {
            $sDate = date('Y') . "-10-01";
            $eDate = date('Y') . "-12-31";
        }
    }

    if ($type == 's') {
        $str = "AND cc.cFeedBackScrivenerClose != 1  AND cs.cScrivener='" . $id . "'";
    } elseif ($type == 'b') {
        $str = "AND (cr.cBranchNum = '" . $id . "' OR cr.cBranchNum1 = '" . $id . "' OR cr.cBranchNum2 = '" . $id . "')";
    } elseif ($type == 'c') {
        $str = "AND cc.cCertifiedId ='" . $id . "'";
    } elseif ($type == 'bs') { //品牌回饋代書
        $str = " AND (cr.cBrand = '" . $id . "' OR cr.cBrand1 = '" . $id . "' OR cr.cBrand2 = '" . $id . "') AND cs.cScrivener = '" . $id2 . "'";
    }
//    $str .= " AND (cc.cCaseStatus = 2 OR cc.cEndDate >= '" . $sDate . "' AND cc.cEndDate <= '" . $eDate . "')";
    $str .= " AND (cc.cCaseStatus <> 8)";

    $sql = "SELECT
            cc.cCertifiedId AS cCertifiedId,
            ci.cTotalMoney AS cTotalMoney,
            ci.cCertifiedMoney as cerifiedmoney,
            ci.cFirstMoney as cFirstMoney,
            cr.cBranchNum AS branch,
            cr.cBranchNum1 AS branch1,
            cr.cBranchNum2 AS branch2,
            cr.cBranchNum3 AS branch3,
            cr.cBrand AS brand,
            cr.cBrand1 AS brand1,
            cr.cBrand2 AS brand2,
            cr.cBrand3 AS brand3,
            cr.cServiceTarget AS cServiceTarget,
            cr.cServiceTarget1 AS cServiceTarget1,
            cr.cServiceTarget2 AS cServiceTarget2,
            cr.cServiceTarget3 AS cServiceTarget3,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS bRecall,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS bRecall1,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS bRecall2,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS bRecall3,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS scrRecall,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS scrRecall1,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS scrRecall2,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS scrRecall3,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum)  AS bFeedbackMoney,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum1)  AS bFeedbackMoney1,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum2)  AS bFeedbackMoney2,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum3)  AS bFeedbackMoney3,
            (SELECT sRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall,
            (SELECT sSpRecall2 FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall2,
            (SELECT sFeedbackMoney FROM tScrivener WHERE sId=cs.cScrivener) AS sFeedbackMoney,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandScrRecall,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandScrRecall1,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandScrRecall2,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandRecall,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandRecall1,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandRecall2,
            cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
            cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
            cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
            cc.cCaseFeedBackMoney3 AS cCaseFeedBackMoney3,
            cc.cCaseFeedback AS cCaseFeedback,
            cc.cCaseFeedback1 AS cCaseFeedback1,
            cc.cCaseFeedback2 AS cCaseFeedback2,
            cc.cCaseFeedback3 AS cCaseFeedback3,
            cc.cFeedbackTarget AS cFeedbackTarget,
            cc.cFeedbackTarget1 AS cFeedbackTarget1,
            cc.cFeedbackTarget2 AS cFeedbackTarget2,
            cc.cFeedbackTarget3 AS cFeedbackTarget3,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum)  AS branchbook,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum1)  AS branchbook1,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum2)  AS branchbook2,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum3)  AS branchbook3,
            cr.cAffixBranch,
            cr.cAffixBranch1,
            cr.cAffixBranch2,
            cr.cAffixBranch3,
            cc.cSpCaseFeedBackMoney
        FROM
            tContractCase AS cc
        JOIN tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
        JOIN tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
        JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
        WHERE 
             ci.cTotalMoney !=0 AND cc.cCaseFeedBackModifier ='' AND ci.cCertifiedMoney !=0  " . $str;
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $list[] = $rs->fields;
        $rs->MoveNext();
    }

    if (is_array($list)) {
        for ($i = 0; $i < count($list); $i++) {
            //資料庫內資料
            $dbData['cCertifiedId'] = $list[$i]['cCertifiedId'];
            $dbData['cCaseFeedBackMoney'] = $list[$i]['cCaseFeedBackMoney'];
            $dbData['cCaseFeedBackMoney1'] = $list[$i]['cCaseFeedBackMoney1'];
            $dbData['cCaseFeedBackMoney2'] = $list[$i]['cCaseFeedBackMoney2'];
            $dbData['cCaseFeedBackMoney3'] = $list[$i]['cCaseFeedBackMoney3'];
            $dbData['cSpCaseFeedBackMoney'] = $list[$i]['cSpCaseFeedBackMoney'];

            $cerifiedMoney = ($list[$i]['cTotalMoney'] - $list[$i]['cFirstMoney']) * 0.0006; //應收保證費

            $uSql = array(
                'cBranchRecall' => '',
                'cBranchScrRecall' => '',
                'cScrivenerRecall' => '',
                'cScrivenerSpRecall' => '',
                'cBranchRecall1' => '',
                'cCaseFeedback' => 0,
                'cCaseFeedback1' => 0,
                'cCaseFeedback2' => 0,
                'cCaseFeedback3' => 0,
                'cCaseFeedBackMoney' => 0,
                'cCaseFeedBackMoney1' => 0,
                'cCaseFeedBackMoney2' => 0,
                'cCaseFeedBackMoney3' => 0,
                'cFeedbackTarget' => 1,
                'cFeedbackTarget1' => 1,
                'cFeedbackTarget2' => 1,
                'cFeedbackTarget3' => 1,
                'cBranchRecall2' => '',
                'cBranchRecall3' => '',
                'cBrandRecall' => '',
                'cBrandRecall1' => '',
                'cBrandRecall2' => '',
                'cBrandRecall3' => '',
                'cSpCaseFeedBackMoney' => 0);
            $brecall = array();
            $scrrecall = array();
            $scrpartsp = array();
            $bcount = 0;
            $scrpart = '';
            $cScrivenerSpRecallFlag = true;

            //確認店家數及地政回饋比率casecheck
            if ($list[$i]['branch'] > 0) {
                if ($list[$i]['cFeedbackTarget'] == 2) { //scrivener
                    $brecall[0] = $list[$i]['sRecall'] / 100; //計算用
                    if ($brecall[0] > 0) {
                        $cScrivenerSpRecallFlag = false;
                    }
                } else {
                    $brecall[0] = $list[$i]['bRecall'] / 100; //計算用
                    if ($cScrivenerSpRecallFlag && in_array($list[$i]['brand'], array(1, 2, 49))) {
                        $cScrivenerSpRecallFlag = false;
                    }
                }
                $uSql['cBranchRecall'] = $list[$i]['bRecall'];
                if ($list[$i]['scrRecall'] != '' && $list[$i]['scrRecall'] != '0') {
                    $scrrecall[0] = $list[$i]['scrRecall'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall'] != '') {
                    $brecall[0] = $list[$i]['brandRecall'] / 100;
                    $scrpartsp[0] = $list[$i]['brandScrRecall'] / 100; //地政士部

                    $uSql['cBrandRecall'] = $list[$i]['brandRecall'];
                }

                $bcount++;
            }

            if ($list[$i]['branch1'] > 0) {
                if ($list[$i]['cFeedbackTarget1'] == 2) { //scrivener
                    $brecall[1] = $list[$i]['sRecall'] / 100; //計算用
                    if ($brecall[1] > 0) {
                        $cScrivenerSpRecallFlag = false;
                    }
                } else {
                    $brecall[1] = $list[$i]['bRecall1'] / 100; //計算用
                    if ($cScrivenerSpRecallFlag && in_array($list[$i]['brand1'], array(1, 2, 49))) {
                        $cScrivenerSpRecallFlag = false;
                    }
                }

                $uSql['cBranchRecall1'] = $list[$i]['bRecall1'];

                if ($list[$i]['scrRecall1'] != '' && $list[$i]['scrRecall1'] != '0') {
                    $scrrecall[1] = $list[$i]['scrRecall1'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall1'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall1'] != '') {
                    $brecall[1] = $list[$i]['brandRecall1'] / 100;
                    $scrpartsp[1] = $list[$i]['brandScrRecall1'] / 100; //地政士部
                    $uSql['cBrandRecall1'] = $list[$i]['brandRecall1'];
                }

                $bcount++;
            }

            if ($list[$i]['branch2'] > 0) {
                if ($list[$i]['cFeedbackTarget2'] == 2) { //scrivener
                    $brecall[2] = $list[$i]['sRecall'] / 100; //計算用
                    if ($brecall[2] > 0) {
                        $cScrivenerSpRecallFlag = false;
                    }
                } else {
                    $brecall[2] = $list[$i]['bRecall2'] / 100; //計算用
                    if ($cScrivenerSpRecallFlag && in_array($list[$i]['brand2'], array(1, 2, 49))) {
                        $cScrivenerSpRecallFlag = false;
                    }
                }

                $uSql['cBranchRecall2'] = $list[$i]['bRecall2'];

                if ($list[$i]['scrRecall2'] != '' && $list[$i]['scrRecall2'] != '0') {
                    $scrrecall[2] = $list[$i]['scrRecall2'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall2'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall2'] != '') {
                    $brecall[2] = $list[$i]['brandRecall2'] / 100;
                    $scrpartsp[2] = $list[$i]['brandScrRecall2'] / 100; //地政士部
                    $uSql['cBrandRecall2'] = $list[$i]['brandRecall2'];
                }

                $bcount++;
            }

            if ($list[$i]['branch3'] > 0) {

                if ($list[$i]['cFeedbackTarget3'] == 2) { //scrivener
                    $brecall[3] = $list[$i]['sRecall'] / 100; //計算用
                    if ($brecall[3] > 0) {
                        $cScrivenerSpRecallFlag = false;
                    }
                } else {
                    $brecall[3] = $list[$i]['bRecall3'] / 100; //計算用
                    if ($cScrivenerSpRecallFlag && in_array($list[$i]['brand3'], array(1, 2, 49))) {
                        $cScrivenerSpRecallFlag = false;
                    }
                }

                $uSql['cBranchRecall3'] = $list[$i]['bRecall3'];

                if ($list[$i]['scrRecall2'] != '' && $list[$i]['scrRecall3'] != '0') {
                    $scrrecall[3] = $list[$i]['scrRecall3'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall3'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall3'] != '') {
                    $brecall[3] = $list[$i]['brandRecall3'] / 100;
                    $scrpartsp[3] = $list[$i]['brandScrRecall3'] / 100; //地政士部
                    $uSql['cBrandRecall3'] = $list[$i]['scrRecall3'];
                }

                $bcount++;
            }

            //地政士特殊回饋(有台屋、非仲一律不回饋)
            if (count($scrrecall) > 0) {
                rsort($scrrecall); //取一個就好
                $scrpart = $scrrecall[0];
            }

            if (count($scrpartsp) > 0) {
                rsort($scrpartsp); //取一個就好
                $scrpart = $scrpartsp[0];
            }

            if (empty($scrpart) && $cScrivenerSpRecallFlag && $list[$i]['sSpRecall'] > 0) {
                $scrpart = $list[$i]['sSpRecall'] / 100;
            }

            unset($scrrecall);
            unset($scrpartsp);

            $uSql['cScrivenerRecall'] = $list[$i]['sRecall'];
            $uSql['cScrivenerSpRecall'] = $list[$i]['sSpRecall'];

            if (($list[$i]['cerifiedmoney'] + 10) < $cerifiedMoney) {
                $uSql['cCaseFeedback'] = 0;
                $uSql['cCaseFeedback1'] = 0;
                $uSql['cCaseFeedback2'] = 0;
                $uSql['cCaseFeedback3'] = 0;

                if ($bcount == 1) {
                    //第一間無合作契約書給代書
                    if (($list[$i]['branchbook'] == '' || $list[$i]['branchbook'] == 0) && $list[$i]['branch'] > 0 && $list[$i]['brand'] != 1 && $list[$i]['brand'] != 69) {
                        $uSql['cFeedbackTarget'] = 2;
                        if ($list[$i]['sFeedbackMoney'] == 1) { //地政士未收足也要回饋
                            $uSql['cCaseFeedback'] = 0;
                            $uSql['cCaseFeedBackMoney'] = round(($brecall[0] * $list[$i]['cerifiedmoney']));
                        }
                    } else { //
                        if ($list[$i]['bFeedbackMoney'] == 1) {
                            $uSql['cCaseFeedback'] = 0;
                            $uSql['cCaseFeedBackMoney'] = round(($brecall[0] * $list[$i]['cerifiedmoney']));
                        }
                    }
                } else {
                    if ($list[$i]['bFeedbackMoney'] == 1) {
                        //有合契
                        if (($list[$i]['branchbook'] == '1') || ($list[$i]['brand'] == 1 || $list[$i]['brand'] == 69)) {
                            $uSql['cCaseFeedback'] = 0;
                            $uSql['cCaseFeedBackMoney'] = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback'] = 1;
                            $uSql['cCaseFeedBackMoney'] = 0;
                        }
                    } else {
                        //有合契
                        if (($list[$i]['branchbook'] == '1') || ($list[$i]['brand'] == 1 || $list[$i]['brand'] == 69)) {
                            $uSql['cCaseFeedback'] = 0;
                        } else {
                            $uSql['cCaseFeedback'] = 1;
                        }
                    }

                    if ($list[$i]['bFeedbackMoney1'] == 1) {
                        //有合契
                        if (($list[$i]['branchbook1'] == '1') || ($list[$i]['brand1'] == 1 || $list[$i]['brand1'] == 69)) {
                            $uSql['cCaseFeedback1'] = 0;
                            $uSql['cCaseFeedBackMoney1'] = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback1'] = 1;
                            $uSql['cCaseFeedBackMoney1'] = 0;
                        }
                    } else {
                        //有合契
                        if (($list[$i]['branchbook1'] == '1') || ($list[$i]['brand1'] == 1 || $list[$i]['brand1'] == 69)) {
                            $uSql['cCaseFeedback1'] = 0;
                        } else {
                            $uSql['cCaseFeedback1'] = 1;
                        }
                    }

                    if ($list[$i]['bFeedbackMoney2'] == 1) {
                        //有合契
                        if (($list[$i]['branchbook2'] == '1') || ($list[$i]['brand2'] == 1 || $list[$i]['brand2'] == 69)) {
                            $uSql['cCaseFeedback2'] = 0;
                            $uSql['cCaseFeedBackMoney2'] = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback2'] = 1;
                            $uSql['cCaseFeedBackMoney2'] = 0;
                        }

                    } else {
                        //有合契
                        if (($list[$i]['branchbook2'] == '1') || ($list[$i]['brand2'] == 1 || $list[$i]['brand2'] == 69)) {
                            $uSql['cCaseFeedback2'] = 0;
                        } else {
                            $uSql['cCaseFeedback2'] = 1;
                        }
                    }

                    if ($list[$i]['bFeedbackMoney3'] == 1) {
                        //有合契
                        if (($list[$i]['branchbook3'] == '1') || ($list[$i]['brand3'] == 1 || $list[$i]['brand3'] == 69)) {
                            $uSql['cCaseFeedback3'] = 0;
                            $uSql['cCaseFeedBackMoney3'] = round(($brecall[3] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback3'] = 0;
                            $uSql['cCaseFeedBackMoney3'] = 0;
                        }
                    } else {
                        //有合契
                        if (($list[$i]['branchbook3'] == '1') || ($list[$i]['brand3'] == 1 || $list[$i]['brand3'] == 69)) {
                            $uSql['cCaseFeedback3'] = 0;
                        } else {
                            $uSql['cCaseFeedback3'] = 1;
                        }
                    }
                }

                $str = array();
                foreach ($uSql as $key => $value) {
                    $str[] = $key . "='" . $value . "'";
                }

//                $sql = "UPDATE tContractCase SET " . @implode(',', $str) . " WHERE cCertifiedId ='" . $list[$i]['cCertifiedId'] . "'";
//                $conn->Execute($sql);

                continue;
            }

            $brand69 = 0;
            if ($brand > 0 && $brand == 69) {
                $brand69++;
            }
            if ($brand1 > 0 && $brand1 == 69) {
                $brand69++;
            }
            if ($brand2 > 0 && $brand2 == 69) {
                $brand69++;
            }
            if ($brand3 > 0 && $brand3 == 69) {
                $brand69++;
            }

            //幸福家
            if ($bcount > 1 && $brand69 == $bcount) {
                //配件只有幸福家
                $o = 0;
                if ($list[$i]['cAffixBranch'] == 1) {
                    $ownerbrand = $list[$i]['brand'];
                    $ownercol = 'cCaseFeedBackMoney';
                    $ownerRecall = $brecall[0];
                    $ownercheck = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['bFeedbackMoney'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $ownerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                    $o++;
                } else {
                    $buyerbrand = $list[$i]['brand'];
                    $buyercol = 'cCaseFeedBackMoney';
                    $buyerRecall = $brecall[0];
                    $buyercheck = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $buyerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                }

                if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                    if ($list[$i]['cAffixBranch1'] == 1) {
                        $ownerbrand = $list[$i]['brand1'];
                        $ownercol = 'cCaseFeedBackMoney1';
                        $ownerRecall = $brecall[1];
                        $ownercheck = $list[$i]['branchbook1'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand1'];
                        $buyercol = 'cCaseFeedBackMoney1';
                        $buyerRecall = $brecall[1];
                        $buyercheck = $list[$i]['branchbook1'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                    if ($list[$i]['cAffixBranch2'] == 1) {
                        $ownerbrand = $list[$i]['brand2'];
                        $ownercol = 'cCaseFeedBackMoney2';
                        $ownerRecall = $brecall[2];
                        $ownercheck = $list[$i]['branchbook2'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand2'];
                        $buyercol = 'cCaseFeedBackMoney2';
                        $buyerRecall = $brecall[2];
                        $buyercheck = $list[$i]['branchbook2'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                    if ($list[$i]['cAffixBranch3'] == 1) {
                        $ownerbrand = $list[$i]['brand3'];
                        $ownercol = 'cCaseFeedBackMoney3';
                        $ownerRecall = $brecall[3];
                        $ownercheck = $list[$i]['branchbook3'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand3'];
                        $buyercol = 'cCaseFeedBackMoney3';
                        $buyerRecall = $brecall[3];
                        $buyercheck = $list[$i]['branchbook3'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                    }
                }

                //以防沒選到契約書用印店(用舊的方法 只回饋給賣方)
                if ($o == 0) {
                    if ($list[$i]['cFeedbackTarget'] == 2) {
                        $ownerbrand = $list[$i]['brand'];
                        $ownercol = 'cCaseFeedBackMoney';
                        $ownerRecall = $brecall[0];
                        $ownercheck = $list[$i]['branchbook'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand'];
                        $buyercol = 'cCaseFeedBackMoney';
                        $buyerRecall = $brecall[0];
                        $buyercheck = $list[$i]['branchbook'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyrfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                    }

                    if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                        if ($list[$i]['cFeedbackTarget1'] == 2) {
                            $ownerbrand = $list[$i]['brand1'];
                            $ownercol = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck = $list[$i]['branchbook1'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand = $list[$i]['brand1'];
                            $buyercol = 'cCaseFeedBackMoney1';
                            $buyerRecall = $brecall[1];
                            $buyercheck = $list[$i]['branchbook1'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                        if ($list[$i]['cFeedbackTarget2'] == 2) {
                            $ownerbrand = $list[$i]['brand2'];
                            $ownercol = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck = $list[$i]['branchbook2'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand = $list[$i]['brand2'];
                            $buyercol = 'cCaseFeedBackMoney2';
                            $buyerRecall = $brecall[2];
                            $buyercheck = $list[$i]['branchbook2'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                        if ($list[$i]['cFeedbackTarget3'] == 2) {
                            $ownerbrand = $list[$i]['brand3'];
                            $ownercol = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck = $list[$i]['branchbook3'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand = $list[$i]['brand3'];
                            $buyercol = 'cCaseFeedBackMoney3';
                            $buyerRecall = $brecall[3];
                            $buyercheck = $list[$i]['branchbook3'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($o == 0) { //沒有選定賣方則從買賣方選一個
                        if ($list[$i]['cFeedbackTarget'] == 1) {
                            $ownerbrand = $list[$i]['brand'];
                            $ownercol = 'cCaseFeedBackMoney';
                            $ownerRecall = $brecall[0];
                            $ownercheck = $list[$i]['branchbook'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['brand1'] > 0) {
                            $ownerbrand = $list[$i]['brand1'];
                            $ownercol = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['branchbook1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['brand2'] > 0) {
                            $ownerbrand = $list[$i]['brand2'];
                            $ownercol = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck = $list[$i]['branchbook2'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['branchbook2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['brand3'] > 0) {
                            $ownerbrand = $list[$i]['brand3'];
                            $ownercol = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck = $list[$i]['branchbook3'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['branchbook3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }
                }

                if ($ownerbrand == 69) {
                    if ($ownerfeed == '') {
                        $_feedbackMoney = round($ownerRecall * $list[$i]['cerifiedmoney']);
                        $uSql[$ownercol] = $_feedbackMoney;
                        $uSql[$buyercol] = 0;
                    } else {
                        $uSql[$ownercol] = 0;
                        $uSql[$buyercol] = 0;
                        $uSql[$ownercol] = 1;
                        $uSql[$buyercol] = 1;
                    }
                } else if ($ownerbrand != 69) {
                    if ($ownercheck > 0) { //他牌是契約用印店且有合作契約書 各店:保證費*回饋趴/回饋數
                        if ($feed == 1) { //  只有一間有勾選未收足，只算給那一間店
                            $bcount = 0;
                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch2'] == 1 || $list[$i]['brand2'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                //是契約書用印店才回饋 &&
                                $_feedbackMoney = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney'] = 0;
                                $uSql['cCaseFeedback'] = 1;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $_feedbackMoney = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney1'] = 0;
                                $uSql['cCaseFeedback1'] = 1;
                            }

                            if ($bcount == 3) {
                                if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand2'] == 69)) { //是契約書用印店才回饋 && $("[name='cAffixBranch']:checked").val() == 'b2'
                                    $_feedbackMoney = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                    $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney;
                                } else {
                                    $uSql['cCaseFeedBackMoney2'] = 0;
                                    $uSql['cCaseFeedback2'] = 1;
                                }
                            }
                        } else {
                            $_feedbackMoney = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;

                            $_feedbackMoney = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;

                            if ($bcount == 3) {
                                $_feedbackMoney = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            }
                        }
                    } else {
                        //沒合作契約書回饋給幸福家(買)
                        if ($buyerfeed == '') {
                            $_feedbackMoney = round($ownerRecall * $list[$i]['cerifiedmoney']);
                            $uSql[$buyercol] = $_feedbackMoney;
                            $uSql[$ownercol] = 0;
                        } else {
                            $uSql[$ownercol] = 0;
                            $uSql[$buyercol] = 0;
                            $uSql[$ownerfeed] = 1;
                            $uSql[$buyerfeed] = 1;
                        }
                    }
                }
            } else if ($bcount > 1 && ($list[$i]['brand'] == 69 || $list[$i]['brand1'] == 69 || $list[$i]['brand2'] == 69)) {
                continue;
                //幸福他排配(含台屋)
                $o = 0;
                if ($list[$i]['cServiceTarget'] == 2) {
                    $ownerbrand = $list[$i]['brand'];
                    $ownercol = 'cCaseFeedBackMoney';
                    $ownerRecall = $brecall[0];
                    $ownercheck = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $ownerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                    $o++;
                } else {
                    $buyerbrand = $list[$i]['brand'];
                    $buyercol = 'cCaseFeedBackMoney';
                    $buyerRecall = $brecall[0];
                    $buyercheck = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $buyerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                }

                if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                    if ($list[$i]['cServiceTarget1'] == 2) {
                        $ownerbrand = $list[$i]['brand1'];
                        $ownercol = 'cCaseFeedBackMoney1';
                        $ownerRecall = $brecall[1];
                        $ownercheck = $list[$i]['branchbook1'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand1'];
                        $buyercol = 'cCaseFeedBackMoney1';
                        $buyerRecall = $brecall[1];
                        $buyercheck = $list[$i]['branchbook1'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                    if ($list[$i]['cServiceTarget2'] == 2) {
                        $ownerbrand = $list[$i]['brand2'];
                        $ownercol = 'cCaseFeedBackMoney2';
                        $ownerRecall = $brecall[2];
                        $ownercheck = $list[$i]['branchbook2'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand2'];
                        $buyercol = 'cCaseFeedBackMoney2';
                        $buyerRecall = $brecall[2];
                        $buyercheck = $list[$i]['branchbook2'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                    if ($list[$i]['cServiceTarget3'] == 2) {
                        $ownerbrand = $list[$i]['brand3'];
                        $ownercol = 'cCaseFeedBackMoney3';
                        $ownerRecall = $brecall[3];
                        $ownercheck = $list[$i]['branchbook3'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand3'];
                        $buyercol = 'cCaseFeedBackMoney3';
                        $buyerRecall = $brecall[3];
                        $buyercheck = $list[$i]['branchbook3'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                    }
                }

                //以防沒選到契約書用印店(用舊的方法 只回饋給賣方)
                if ($o == 0) {
                    if ($list[$i]['cFeedbackTarget'] == 2) {
                        $ownerbrand = $list[$i]['brand'];
                        $ownercol = 'cCaseFeedBackMoney';
                        $ownerRecall = $brecall[0];
                        $ownercheck = $list[$i]['branchbook'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand = $list[$i]['brand'];
                        $buyercol = 'cCaseFeedBackMoney';
                        $buyerRecall = $brecall[0];
                        $buyercheck = $list[$i]['branchbook'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyrfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                    }

                    if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                        if ($list[$i]['cFeedbackTarget1'] == 2) {
                            $ownerbrand = $list[$i]['brand1'];
                            $ownercol = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand = $list[$i]['brand1'];
                            $buyercol = 'cCaseFeedBackMoney1';
                            $buyerRecall = $brecall[1];
                            $buyercheck = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                        if ($list[$i]['cFeedbackTarget2'] == 2) {
                            $ownerbrand = $list[$i]['brand2'];
                            $ownercol = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck = $list[$i]['branchbook2'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand = $list[$i]['brand2'];
                            $buyercol = 'cCaseFeedBackMoney2';
                            $buyerRecall = $brecall[2];
                            $buyercheck = $list[$i]['branchbook2'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                        if ($list[$i]['cFeedbackTarget3'] == 2) {
                            $ownerbrand = $list[$i]['brand3'];
                            $ownercol = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck = $list[$i]['branchbook3'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand = $list[$i]['brand3'];
                            $buyercol = 'cCaseFeedBackMoney3';
                            $buyerRecall = $brecall[3];
                            $buyercheck = $list[$i]['branchbook3'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($o == 0) { //沒有選定賣方則從買賣方選一個
                        if ($list[$i]['cFeedbackTarget'] == 1) {
                            $ownerbrand = $list[$i]['brand'];
                            $ownercol = 'cCaseFeedBackMoney';
                            $ownerRecall = $brecall[0];
                            $ownercheck = $list[$i]['branchbook'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['brand1'] > 0) {
                            $ownerbrand = $list[$i]['brand1'];
                            $ownercol = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['branchbook1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['brand2'] > 0) {
                            $ownerbrand = $list[$i]['brand2'];
                            $ownercol = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck = $list[$i]['branchbook2'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['branchbook2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['brand3'] > 0) {
                            $ownerbrand = $list[$i]['brand3'];
                            $ownercol = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck = $list[$i]['branchbook3'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['branchbook3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }
                }

                if ($ownerbrand == 69) {
                    if ($ownerfeed == '') {
                        $_feedbackMoney = round($ownerRecall * $list[$i]['cerifiedmoney']);
                        $uSql[$ownercol] = $_feedbackMoney;
                        $uSql[$buyercol] = 0;
                    } else {
                        $uSql[$ownercol] = 0;
                        $uSql[$buyercol] = 0;
                        $uSql[$ownerfeed] = 1;
                        $uSql[$buyerfeed] = 1;
                    }
                } else if ($ownerbrand != 69) {

                    if ($ownercheck > 0) { //他牌是契約用印店且有合作契約書 各店:保證費*回饋趴/回饋數
                        if ($feed == 1) { //  只有一間有勾選未收足，只算給那一間店
                            $bcount = 0;
                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch2'] == 1 || $list[$i]['brand2'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                //是契約書用印店才回饋 &&
                                $_feedbackMoney = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney'] = 0;
                                $uSql['cCaseFeedback'] = 1;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $_feedbackMoney = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney1'] = 0;
                                $uSql['cCaseFeedback1'] = 1;
                            }

                            if ($bcount == 3) {
                                if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand2'] == 69)) { //是契約書用印店才回饋 && $("[name='cAffixBranch']:checked").val() == 'b2'
                                    $_feedbackMoney = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                    $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney;
                                } else {
                                    $uSql['cCaseFeedBackMoney2'] = 0;
                                    $uSql['cCaseFeedback2'] = 1;
                                }
                            }
                        } else {
                            $_feedbackMoney = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            $_feedbackMoney = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;

                            if ($bcount == 3) {
                                $_feedbackMoney = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            }
                        }
                    } else {
                        //沒合作契約書回饋給幸福家(買)
                        if ($buyerfeed == '') {
                            $_feedbackMoney = round($buyerRecall * $list[$i]['cerifiedmoney']);
                            $uSql[$buyercol] = $_feedbackMoney;
                            $uSql[$ownercol] = 0;
                        } else {
                            $uSql[$ownercol] = 0;
                            $uSql[$buyercol] = 0;
                            $uSql[$ownerfeed] = 0;
                            $uSql[$buyerfeed] = 0;
                        }
                    }
                }
            } else {
                if ($bcount == 1) { //只有一間店
                    $_feedbackMoney = round($brecall[0] * $list[$i]['cerifiedmoney']);

                    $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                    $uSql['cCaseFeedBackMoney1'] = 0;
                    $uSql['cCaseFeedBackMoney2'] = 0;
                    $uSql['cCaseFeedBackMoney3'] = 0;

                    //無合作契約書給代書
                    if ($list[$i]['branchbook'] != 1 && $list[$i]['branch'] > 0 && $list[$i]['brand'] != 1 && $list[$i]['brand'] != 69) {
                        $uSql['cFeedbackTarget'] = 2;
                    }

                    //如有回饋給地政士另有地政士特殊回饋
                    if (($list[$i]['cFeedbackTarget'] == 2 || $list[$i]['cFeedbackTarget1'] == 2 || $list[$i]['cFeedbackTarget2'] == 2) && ($list[$i]['brand'] != 69 || $list[$i]['brand'] != 1 || $list[$i]['brand'] != 49) && ($list[$i]['sSpRecall'] != '' || $list[$i]['sSpRecall'] != 0)) {
                        $list[$i]['sSpRecall'] = $list[$i]['sSpRecall'] / 100;

                        if ($list[$i]['sSpRecall'] > $brecall[0]) {
                            $_feedbackMoney = round($list[$i]['sSpRecall'] * $list[$i]['cerifiedmoney']);
                        } else {
                            $_feedbackMoney = round($brecall[0] * $list[$i]['cerifiedmoney']);
                        }

                        $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                        $uSql['cCaseFeedBackMoney1'] = 0;
                        $uSql['cCaseFeedBackMoney2'] = 0;
                        $uSql['cCaseFeedBackMoney3'] = 0;
                    }
                } else if ($bcount > 1) {
                    $tmp_c = 0;

                    //計算回饋
                    if ($list[$i]['branch'] > 0) {
                        $_feedbackMoney = round($brecall[0] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                    }

                    if ($list[$i]['branch1'] > 0) {
                        $_feedbackMoney1 = round($brecall[1] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney1;
                    }

                    if ($list[$i]['branch2'] > 0) {
                        $_feedbackMoney2 = round($brecall[2] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney2;
                    }

                    if ($list[$i]['branch3'] > 0) {
                        $_feedbackMoney3 = round($brecall[3] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney3'] = $_feedbackMoney3;
                    }

                    //是否為台屋優美或有合作契約書
                    if (($list[$i]['brand'] == 1 || $list[$i]['brand'] == 49 || $list[$i]['branchbook'] > 0)) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback'] = 1;
                        $uSql['cCaseFeedBackMoney'] = 0;
                    }

                    if (($list[$i]['brand1'] == 1 || $list[$i]['brand1'] == 49 || $list[$i]['branchbook1'] > 0) && $list[$i]['branch1'] > 0) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback1'] = 1;
                        $uSql['cCaseFeedBackMoney1'] = 0;
                    }

                    if (($list[$i]['brand2'] == 1 || $list[$i]['brand2'] == 49 || $list[$i]['branchbook2'] > 0) && $list[$i]['branch2'] > 0) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback2'] = 1;
                        $uSql['cCaseFeedBackMoney2'] = 0;
                    }

                    if (($list[$i]['brand3'] == 1 || $list[$i]['brand3'] == 49 || $list[$i]['branchbook3'] > 0) && $list[$i]['branch3'] > 0) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback3'] = 1;
                        $uSql['cCaseFeedBackMoney3'] = 0;
                    }
                    //配件都沒有合作契約書，回饋給代書
                    if ($tmp_c == 0) {
                        if ($list[$i]['branch'] > 0) {
                            $uSql['cCaseFeedback'] = 0;
                            $uSql['cFeedbackTarget'] = 2;
                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                        }

                        if ($list[$i]['branch1'] > 0) {
                            $uSql['cCaseFeedback1'] = 0;
                            $uSql['cFeedbackTarget1'] = 2;
                            $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney1;
                        }

                        if ($list[$i]['branch2'] > 0) {
                            $uSql['cCaseFeedback2'] = 0;
                            $uSql['cFeedbackTarget2'] = 2;
                            $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney2;
                        }

                        if ($list[$i]['branch3'] > 0) {
                            $uSql['cCaseFeedback3'] = 0;
                            $uSql['cFeedbackTarget3'] = 2;
                            $uSql['cCaseFeedBackMoney3'] = $_feedbackMoney3;
                        }
                    }
                }
            }

            if ($scrpart != 0 && $scrpart != '') {
                $scrFeedMoney = round($scrpart * $list[$i]['cerifiedmoney']);
                $uSql['cSpCaseFeedBackMoney'] = $scrFeedMoney;
            } else {
                $uSql['cSpCaseFeedBackMoney'] = 0;
            }

            $str = array();
            foreach ($uSql as $key => $value) {
                $str[] = $key . "='" . $value . "'";
            }

            //比對是否資料不一致
            if (
                (abs($dbData['cCaseFeedBackMoney'] - $uSql['cCaseFeedBackMoney']) > 1) ||
                (abs($dbData['cCaseFeedBackMoney1'] - $uSql['cCaseFeedBackMoney1']) > 1) ||
                (abs($dbData['cCaseFeedBackMoney2'] - $uSql['cCaseFeedBackMoney2']) > 1) ||
                (abs($dbData['cCaseFeedBackMoney3'] - $uSql['cCaseFeedBackMoney3']) > 1) ||
                (abs($dbData['cSpCaseFeedBackMoney'] - $uSql['cSpCaseFeedBackMoney']) > 1)
            ) {
                $errorDetail = [];
                if ((abs($dbData['cCaseFeedBackMoney'] - $uSql['cCaseFeedBackMoney']) > 1)) {
                    $errorDetail[] = '回饋金(1)不一致' . $dbData['cCaseFeedBackMoney'] . ':' . $uSql['cCaseFeedBackMoney'];
                }
                if ((abs($dbData['cCaseFeedBackMoney1'] - $uSql['cCaseFeedBackMoney1']) > 1)) {
                    $errorDetail[] = '回饋金(2)不一致' . $dbData['cCaseFeedBackMoney1'] . ':' . $uSql['cCaseFeedBackMoney1'];
                }
                if ((abs($dbData['cCaseFeedBackMoney2'] - $uSql['cCaseFeedBackMoney2']) > 1)) {
                    $errorDetail[] = '回饋金(3)不一致' . $dbData['cCaseFeedBackMoney2'] . ':' . $uSql['cCaseFeedBackMoney2'];
                }
                if ((abs($dbData['cCaseFeedBackMoney3'] - $uSql['cCaseFeedBackMoney3']) > 1)) {
                    $errorDetail[] = '回饋金(4)不一致' . $dbData['cCaseFeedBackMoney3'] . ':' . $uSql['cCaseFeedBackMoney3'];
                }
                if ((abs($dbData['cSpCaseFeedBackMoney'] - $uSql['cSpCaseFeedBackMoney']) > 1)) {
                    $errorDetail[] = '特殊回饋金不一致' . $dbData['cSpCaseFeedBackMoney'] . ':' . $uSql['cSpCaseFeedBackMoney'];
                }
                $errorData[$dbData['cCertifiedId']] = $errorDetail;
//                if($dbData['cCertifiedId']=='120016620'){
//                    var_dump($dbData);
//                    var_dump($str);exit;
//                }
            }

//            $sql = "UPDATE tContractCase SET " . @implode(',', $str) . " WHERE cCertifiedId ='" . $list[$i]['cCertifiedId'] . "'";
//            $conn->Execute($sql);
//
//            //如果有回饋給地政士 特殊回饋不回饋
//            if (($scrpart == 0 || $scrpart == '') && ($uSql['cFeedbackTarget'] != 2 && $uSql['cFeedbackTarget1'] != 2 && $uSql['cFeedbackTarget2'] != 2 && $uSql['cFeedbackTarget3'] != 2)) { //如果仲介品牌有回饋給地政士 特殊回饋不回饋
//                if ($feed == 1) {
//                    if ($list[$i]['sFeedbackMoney'] == 1) {
//                        SpRecall($list[$i]);
//                    }
//                } else {
//                    SpRecall($list[$i]);
//                }
//            }
//
//            write_log($id . ":" . $sql . "\r\n", 'checkFeedPart');

//            $cCertifiedId[] = $list[$i]['cCertifiedId'];
        }
    }

    return $errorData;
}

function checkFeedMoneyReview($id)
{
    global $conn;

    $sql = "SELECT a.fId,a.fCertifiedId,c.fRId,
            b.cCaseFeedback,b.cCaseFeedback1,b.cCaseFeedback2,b.cCaseFeedback3,
            b.cFeedbackTarget,b.cFeedbackTarget1,b.cFeedbackTarget2,b.cFeedbackTarget3,
            b.cCaseFeedBackMoney,b.cCaseFeedBackMoney1,b.cCaseFeedBackMoney2,b.cCaseFeedBackMoney3,b.cSpCaseFeedBackMoney,
            c.fCategory,c.fCaseFeedback,c.fFeedbackTarget,c.fCaseFeedBackMoney,c.fCaseFeedBackMark,c.fFeedbackStoreId, 
            d.cBranchNum,d.cBranchNum1,d.cBranchNum2,d.cBranchNum3 
            FROM tFeedBackMoneyReview AS a 
            LEFT JOIN tContractCase AS b ON a.fCertifiedId=b.cCertifiedId 
            LEFT JOIN tFeedBackMoneyReviewList AS c ON a.fId=c.fRId 
            LEFT JOIN tContractRealestate AS d ON d.cCertifyId=a.fCertifiedId
            where a.fCertifiedId = '" . $id . "' AND c.fDelete = 0 AND a.fStatus = 1 AND a.fFail = 0";
    $rs = $conn->Execute($sql);

    $checkList = [];
    $errorReviews = [];
    $errorMemo = [];
    $tFeedBackMoney = [];
    $fCategoryFlag4 = [];
    $cSpCaseFeedBackMoneyCheck = [];

    if ($rs) {
        while (!$rs->EOF) {
            if (isset($checkList[$rs->fields['fCertifiedId']]) && $checkList[$rs->fields['fCertifiedId']] != $rs->fields['fId']) {
                $rs->MoveNext();

                $errorReviews = [];
                $errorMemo = [];
                $tFeedBackMoney = [];
                $fCategoryFlag4 = [];
                $cSpCaseFeedBackMoneyCheck = [];
                
                continue;
            }

            if (!empty($rs->fields['cSpCaseFeedBackMoney']) && $rs->fields['cSpCaseFeedBackMoney'] > 0) {
                if (!isset($fCategoryFlag4[$rs->fields['fCertifiedId']])) {
                    $fCategoryFlag4[$rs->fields['fCertifiedId']] = $rs->fields['fCategory'];
                    $cSpCaseFeedBackMoneyCheck[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                }
            }

            $tmpCategory = ["1" => "", "2" => "1", "3" => "2", "6" => "3"];
            if ($rs->fields['fCategory'] == "1" || $rs->fields['fCategory'] == "2" || $rs->fields['fCategory'] == "3" || $rs->fields['fCategory'] == "6") {
                $tmp = $tmpCategory[$rs->fields['fCategory']];

                if ($rs->fields['cBranchNum' . $tmp] > 0 && $rs->fields['fCaseFeedback'] != $rs->fields['cCaseFeedback' . $tmp]) {
                    $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                    $errorMemo[$rs->fields['fCertifiedId']][] = "是否回饋不一致";
                }
                if ($rs->fields['cBranchNum' . $tmp] > 0 && $rs->fields['fFeedbackTarget'] != $rs->fields['cFeedbackTarget' . $tmp]) {
                    $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                    $errorMemo[$rs->fields['fCertifiedId']][] = "回饋對象不一致";
                }
                if ($rs->fields['cBranchNum' . $tmp] > 0 && $rs->fields['fCaseFeedBackMoney'] != $rs->fields['cCaseFeedBackMoney' . $tmp]) {
                    $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                    $errorMemo[$rs->fields['fCertifiedId']][] = "回饋金額不一致";
                }
            } else if ($rs->fields['fCategory'] == "4") {
                unset($cSpCaseFeedBackMoneyCheck[$rs->fields['fCertifiedId']]);

                if ($rs->fields['fCaseFeedBackMoney'] != $rs->fields['cSpCaseFeedBackMoney']) {
                    $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                    $errorMemo[$rs->fields['fCertifiedId']][] = "特殊回饋金額不一致";
                }
            } else if ($rs->fields['fCategory'] == "5") {
                if (!empty($rs->fields['fCaseFeedBackMark'])) {
                    if (!isset($tFeedBackMoney[$rs->fields['fCaseFeedBackMark']])) {
                        $sql_tFeedBackMoney = "SELECT fId,fCertifiedId,fStoreId,fMoney FROM tFeedBackMoney WHERE fCertifiedId = '" . $rs->fields['fCertifiedId'] . "' AND fDelete = 0";
                        $rs_tFeedBackMoney = $conn->Execute($sql_tFeedBackMoney);
                        if ($rs_tFeedBackMoney) {
                            while (!$rs_tFeedBackMoney->EOF) {
                                $tFeedBackMoney[$rs_tFeedBackMoney->fields['fCertifiedId']][$rs_tFeedBackMoney->fields['fStoreId']] = $rs_tFeedBackMoney->fields['fMoney'];
                                $rs_tFeedBackMoney->MoveNext();
                            }
                            $rs_tFeedBackMoney->Close();
                        }
                    }

                    if ($tFeedBackMoney[$rs->fields['fCertifiedId']][$rs->fields['fFeedbackStoreId']] != $rs->fields['fCaseFeedBackMoney']) {
                        $errorMemo[$rs->fields['fCertifiedId']][] = "其他回饋資料不一致";
                        $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                    }
                }
            }

            $checkList[$rs->fields['fCertifiedId']] = $rs->fields['fId'];

            $rs->MoveNext();
        }

        if (count($cSpCaseFeedBackMoneyCheck) > 0) {
            $cSpCaseFeedBackMoneyCheck = array_unique($cSpCaseFeedBackMoneyCheck);
            foreach ($cSpCaseFeedBackMoneyCheck as $k => $v) {
                $errorReviews[$v] = $v;
                $errorMemo[$v][] = "特殊回饋資料不一致";
            }
        }

        $rs->Close();
    }

    return $errorMemo;
}

?>