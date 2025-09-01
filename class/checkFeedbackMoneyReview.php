<?php
/**
 * 2024-09-24
 * 檢查回饋金審核過的回饋是否遭到覆蓋
 * 之後要移去 checkFeedbackMoney.class.php
 */

function checkFeedbackMoneyReview($_conn, $certifiedId)
{
    if (!$_conn || empty($certifiedId)) {
        return false;
    }

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
            where a.fCertifiedId = '" . $certifiedId . "' AND a.fFail = 0 AND c.fDelete = 0 ORDER BY c.fId DESC ";
    $rs = $_conn->Execute($sql);

    $checkList = [];
    $errorReviews = [];
    $errorMemo = [];
    $tFeedBackMoney = [];

    if ($rs) {
        while (!$rs->EOF) {
            if (isset($checkList[$rs->fields['fCertifiedId']]) && $checkList[$rs->fields['fCertifiedId']] != $rs->fields['fId']) {
                $rs->MoveNext();
                continue;
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
                if ($rs->fields['fCaseFeedBackMoney'] != $rs->fields['cSpCaseFeedBackMoney']) {
                    $errorReviews[$rs->fields['fCertifiedId']] = $rs->fields['fCertifiedId'];
                    $errorMemo[$rs->fields['fCertifiedId']][] = "特殊回饋金額不一致";
                }
            } else if ($rs->fields['fCategory'] == "5") {
                if (!empty($rs->fields['fCaseFeedBackMark'])) {
                    if (!isset($tFeedBackMoney[$rs->fields['fCaseFeedBackMark']])) {
                        $sql_tFeedBackMoney = "SELECT fId,fCertifiedId,fStoreId,fMoney FROM tFeedBackMoney WHERE fCertifiedId = '" . $rs->fields['fCertifiedId'] . "' AND fDelete = 0";
                        $rs_tFeedBackMoney = $_conn->Execute($sql_tFeedBackMoney);
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
        $rs->Close();
    }

    return $errorReviews;
}