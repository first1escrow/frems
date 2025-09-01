<?php
/**
 * 2025-03-03 class 版本
 * 標記仲介案件須進確認名單
 * 輸入參數 cid 合約書編號
 */

namespace First1\V1\ConfirmFeedback;

require_once dirname(__DIR__) . '/first1DB.php';

//use First1\V1\Notify\Slack;

class ConfirmFeedback
{
    private $conn;

    public function __construct()
    {
        $this->conn = new \first1DB;
    }

    //取得標記仲介
    public function getFeedBackMarkBranch()
    {
        $sql = 'SELECT
                    a.bId,
                    a.bBrand,
                    a.bStore,
                    a.bName
                FROM
                    tBranch AS a,tBranchSalesForPerformance AS b
                WHERE
                    a.bId=b.bBranch AND a.bFeedbackMark = 1;';
        $rs = $this->conn->all($sql);

        if (empty($rs)) {
            return [];
        }

        $list = [];
        foreach ($rs as $v) {
            $list[$v['bId']] = $v['bName'];
        }

        return $list;
    }

    //取得標記地政士
    public function getFeedBackMarkScrivener()
    {
        $sql = 'SELECT
                    a.sId,
                    a.sOffice,
                    a.sName
                FROM
                    tScrivener AS a
                WHERE
                    a.sFeedbackMark = 1;';
        $rs = $this->conn->all($sql);

        if (empty($rs)) {
            return [];
        }

        $list = [];
        foreach ($rs as $v) {
            $list[$v['sId']] = $v['sName'];
        }

        return $list;
    }

    public function salesConfirmList($cId, $branch, $branch1, $branch2, $branch3, $scrivener_id = null)
    {
        //取得標記仲介
        $markBranch = $this->getFeedBackMarkBranch();
        //取得標記代書
        $markScrivener = $this->getFeedBackMarkScrivener();

        //判斷代書是否有被標記
        if($scrivener_id && isset($markScrivener[$scrivener_id])){
            //判斷是否有在隨案
            $isPayByCase = $this->isPayByCase($cId);
            if($isPayByCase){
                //刪除標記代書
                $this->deleteSalesConfirmRecord($cId, 'S');
            } else {
                //判斷tFeedBackMoneyReview 業務最新一筆有沒有未核可的回饋金
                $rs = $this->getFeedBackMoneyReview($cId);
                if (empty($rs)) {
                    $feedback_case = $this->getRealtyFeedbackMoney($cId, null);
                } else {
                    $feedback_case = $this->getReviewListFeedbackMoney($rs['fId'], null);
                }
                $scrivener_sales = $this->getScrivenerSales($scrivener_id);

                $record = $this->getSalesConfirmRecord($cId, $scrivener_sales, 'S');

                if (empty($record)) {
                    //沒有業務通知列表紀錄(新增)
                    $this->addSalesConfirmRecord($cId, $scrivener_sales, $feedback_case, 'S');
                } else {
                    $_compare1 = json_encode($feedback_case, JSON_UNESCAPED_UNICODE); //更新後的紀錄
                    $_compare2 = $record['fDetail']; //更新前的紀錄

                    if ($_compare1 != $_compare2) { //前後記錄不一致(跟新紀錄並清除確認紀錄、重新確認)
                        $this->updateSalesConfirmRecord($record['fId'], $cId, $scrivener_sales, $feedback_case, 'S');
                    }

                    $_compare1 = $_compare2 = null;
                    unset($_compare1, $_compare2);
                }
            }
        }

        //判斷仲介是否有被標記
        if (!isset($markBranch[$branch]) && !isset($markBranch[$branch1]) && !isset($markBranch[$branch2]) && !isset($markBranch[$branch3])) {
            return false;
        }

        //判斷tFeedBackMoneyReview 業務最新一筆有沒有未核可的回饋金
        $rs = $this->getFeedBackMoneyReview($cId);
        if (empty($rs)) {
            $feedback_case = $this->getRealtyFeedbackMoney($cId, $markBranch);
        } else {
            $feedback_case = $this->getReviewListFeedbackMoney($rs['fId'], $markBranch);
        }

        foreach ($feedback_case['case'] as $case_v) {
            if (isset($markBranch[$case_v['cBranchNum']])) {
                $record = $this->getSalesConfirmRecord($cId, $case_v['sales']);

                if (empty($record)) {
                    //沒有業務通知列表紀錄(新增)
                    $this->addSalesConfirmRecord($cId, $case_v['sales'], $feedback_case);
                } else {
                    $scrSales = $feedback_case['sales'];
                    $_compare1 = json_encode($feedback_case, JSON_UNESCAPED_UNICODE); //更新後的紀錄
                    $_compare2 = $record['fDetail']; //更新前的紀錄

                    if ($_compare1 != $_compare2) { //前後記錄不一致(跟新紀錄並清除確認紀錄、重新確認)
                        $this->updateSalesConfirmRecord($record['fId'], $cId, $case_v['sales'], $feedback_case);
                    }

                    $_compare1 = $_compare2 = null;
                    unset($_compare1, $_compare2);
                }
            }
        }
    }

    //取得業務確認列表紀錄
    public function getSalesConfirmRecord($cId, $sId, $target = 'R')
    {
        $sql = 'SELECT fId, fDetail FROM tFeedBackConfirm WHERE fCertifiedId = :cId AND fSales = :sId AND fTarget = :target AND fHidden = 0;';
        return $this->conn->one($sql, ['cId' => $cId, 'sId' => $sId, 'target' => $target]);
    }

    //取得未核可
    private function getFeedBackMoneyReview($certifiedId)
    {
        $sql = 'SELECT
                    `fId`, `fCertifiedId`, `fStatus`
                FROM
                    `tFeedBackMoneyReview` AS re
                WHERE
                   fCertifiedId = :fCertifiedId AND fFail = 0
                ORDER BY
                    fId DESC
                LIMIT 1
        ';

        $rs = $this->conn->one($sql, ['fCertifiedId' => $certifiedId]);

        if (empty($rs) or $rs['fStatus'] == 1) {
            return [];
        }
        return $rs;
    }

    //取得回饋金隨案支付案件
    public function getRealtyFeedbackMoney($cId, $markBranch)
    {
        $sales = '';
        $scrivener = '';
        $cScrivener = '';

        $review_case = $this->getOtherFeedbackCase($cId); //取得其他回饋金資訊
        $feedback_case = $this->getFeedbackCase($cId); //取得案件回饋金資訊

        $cases = array_merge($review_case, $feedback_case);

        if (empty($cases)) { //無回饋給代書
            return [];
        }
        $detail = [];
        foreach ($cases as $case) {
            $detail[] = $case;
            $scrivener = $case['scrivener'];
            $cScrivener = $case['cScrivener'];

            if(!$markBranch || isset($markBranch[$case['cBranchNum']])) {
                $sales = $case['sales'];
            }
        }

        $case = [
            'case' => $detail,
            'scrivener' => $scrivener,
            'cScrivener' => $cScrivener,
            'total' => $this->calculateFeedbackMoney($feedback_case, $review_case),
            'sales' => $sales,
        ];

        return $case;
    }

    private function getReviewListFeedbackMoney($fId, $markBranch, $fFeedbackTarget = 1)
    {
        $sql = 'SELECT
                    `fCertifiedId`, `fCategory`, `fFeedbackTarget`, `fFeedbackStoreId`, `fCaseFeedBackMoney`
                FROM
                     `tFeedBackMoneyReviewList` AS l
                WHERE
                    l.fRId = :fRId
                  AND
                    fFeedbackTarget = :fFeedbackTarget
                  AND
                    fDelete = 0
        ';

        $rs = $this->conn->all($sql, ['fRId' => $fId, 'fFeedbackTarget' => $fFeedbackTarget]);
        if (empty($rs)) {
            return [];
        }

        $cases = [];
        $sales = '';

        $scrivener = $this->getScrivener($rs[0]['fCertifiedId']);

        $total = 0;
        foreach ($rs as $k => $v) {
            if (in_array($v['fCategory'], [1, 2, 3, 6])) {
                $cases[$k]['cBranchNum'] = $this->getBranchNum($v['fCertifiedId'], $v['fCategory']);
                $cases[$k]['cFeedbackTarget'] = $v['fFeedbackTarget'];

                if(!$markBranch || isset($markBranch[$cases[$k]['cBranchNum']])) {
                    $sales = $this->getBranchSales($v['fFeedbackStoreId']);
                }
            }
            if ($v['fCategory'] == 4) {
                $cases[$k]['cSpFeedBack'] = 1;
            }
            if ($v['fCategory'] == 5) {
                $cases[$k]['cOtherFeedBack'] = 1;
            }

            $cases[$k]['cCaseFeedBackMoney'] = $v['fCaseFeedBackMoney'];
            $cases[$k]['cScrivener'] = $scrivener['cScrivener'];
            $cases[$k]['scrivener'] = $scrivener['sOffice'];
            $cases[$k]['sales'] = $sales;
            $total = $v['fCaseFeedBackMoney'] + $total;
        }

        $detail = [
            'case' => $cases,
            'scrivener' => $scrivener['sOffice'], //連子瑩地政士事務所
            'cScrivener' => $scrivener['cScrivener'], //2164
            'total' => $total, //0
            'sales' => $sales, //68
        ];
        return $detail;
    }

    //新增業務確認列表紀錄
    public function addSalesConfirmRecord($cId, $sales, $detail = [], $target = "R", $creator = null)
    {
        $sql = 'INSERT INTO
                    tFeedBackConfirm
                    (
                        fCertifiedId,
                        fTarget,
                        fTargetId,
                        fSales,
                        fDetail,
                        fCreated_at
                    )
                    VALUES
                    (
                        "' . $cId . '",
                        "' . $target . '",
                        "' . $detail['cScrivener'] . '",
                        "' . $sales . '",
                        "' . addslashes(json_encode($detail, JSON_UNESCAPED_UNICODE)) . '",
                        NOW()
                    );';

        return $this->conn->exeSql($sql);
    }

    //更新業務確認列表紀錄
    public function updateSalesConfirmRecord($fId, $cId, $sales, $detail = [], $target = "R")
    {
        $sql = 'UPDATE
                    tFeedBackConfirm
                SET
                    fTargetId = "' . $detail['cScrivener'] . '",
                    fSales = "' . $sales . '",
                    fSalesConfirmDate = NULL,
                    fSalesConfirmId = NULL,
                    fDetail = "' . addslashes(json_encode($detail, JSON_UNESCAPED_UNICODE)) . '"
                WHERE
                    fId = "' . $fId . '"
                    AND fCertifiedId = "' . $cId . '"
                    AND fTarget = "' . $target . '";';
        return $this->conn->exeSql($sql);
    }

    //取得已審核其他回饋金案件
    public function getOtherFeedbackCase($cId)
    {
        $sql = 'SELECT
                    a.fCertifiedId,
                    a.fStoreId,
                    a.fMoney,
                    a.fType,
                    a.fStoreId,
                    a.fIndividualId,
                    (SELECT sOffice FROM tScrivener WHERE sId = a.fStoreId) as scrivener
                FROM
                    tFeedBackMoney AS a
                WHERE
                    a.fDelete = 0
                    AND a.fCertifiedId = :cId
                    AND a.fType in (2,3)
                    AND a.fDelete = 0;';
        $rs = $this->conn->all($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        $list = [];
        foreach ($rs as $v) {
            $branchNum = ($v['fType'] == 3) ? $v['fIndividualId'] : $v['fStoreId'];
            $list[] = [
                'cBranchNum' => $branchNum,
                'cBranchStore' => $this->getBranchData($branchNum)['bStore'],
                // 'cFeedbackTarget'    => $case['cFeedbackTarget3'],
                'cOtherFeedBack' => 1,
                'cCaseFeedBackMoney' => $v['fMoney'],
                'cScrivener' => $v['fStoreId'],
                'scrivener' => $v['scrivener'],
                'sales' => $this->getBranchData($branchNum)['bSales'],
            ];
        }

        return $list;
    }

    //取得案件
    public function getFeedbackCase($cId)
    {
        $sql = 'SELECT
                    a.cCaseFeedback,
                    a.cCaseFeedback1,
                    a.cCaseFeedback2,
                    a.cCaseFeedback3,
                    a.cFeedbackTarget,
                    a.cFeedbackTarget1,
                    a.cFeedbackTarget2,
                    a.cFeedbackTarget3,
                    a.cCaseFeedBackMoney,
                    a.cCaseFeedBackMoney1,
                    a.cCaseFeedBackMoney2,
                    a.cCaseFeedBackMoney3,
                    a.cFeedBackClose,
                    a.cSpCaseFeedBackMoney,
                    a.cScrivenerSpRecall,
                    a.cBranchScrRecall,
                    a.cBranchScrRecall1,
                    a.cBranchScrRecall2,
                    a.cBranchScrRecall3,
                    b.cBranchNum,
                    b.cBranchNum1,
                    b.cBranchNum2,
                    b.cBranchNum3,
                    c.cScrivener,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum) AS store,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum1) AS store1,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum2) AS store2,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum3) AS store3,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum) AS bsales,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum1) AS bsales1,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum2) AS bsales2,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum3) AS bsales3,
                    (SELECT sOffice FROM tScrivener WHERE c.cScrivener = sId) as scrivener
                FROM
                    tContractCase AS a
                JOIN
                    tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId
                JOIN
                    tContractScrivener AS c ON a.cCertifiedId = c.cCertifiedId
                WHERE
                    a.cCertifiedId = :cId;';
        $rs = $this->conn->one($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        return $this->checkFeedbackMoney($rs);
    }

    //計算回饋金總金額
    private function calculateFeedbackMoney($feedback, $review)
    {
        $total = 0;

        if (!empty($feedback)) {
            foreach ($feedback as $v) {
                $total += $v['cCaseFeedBackMoney'];
            }
        }

        if (!empty($review)) {
            foreach ($review as $v) {
                $total += $v['cCaseFeedBackMoney'];
            }
        }

        return $total;
    }

    private function checkFeedbackTarget($cBranchNum, $cCaseFeedback, $cFeedbackTarget, $cCaseFeedBackMoney)
    {
        //設定不回饋或回饋對象為仲介時為 false
        // return (($cCaseFeedback == 1) || ($cFeedbackTarget == 1) || ($cBranchNum == 0) || ($cCaseFeedBackMoney == 0)) ? false : true;
//        return (($cCaseFeedback == 0) && ($cFeedbackTarget == 2) && ($cBranchNum > 0)) ? true : false;
        return true;
    }

    //取得仲介店所屬績效業務
    public function getBranchSales($branch)
    {
        $sql = 'SELECT bSales as sales FROM tBranchSalesForPerformance WHERE bBranch = :sId;';
        $rs = $this->conn->one($sql, ['sId' => $branch]);

        return empty($rs['sales']) ? '' : $rs['sales'];
    }

    //取得代書所屬績效業務
    public function getScrivenerSales($scrivener)
    {
        $sql = 'SELECT sSales as sales FROM tScrivenerSalesForPerformance WHERE sScrivener = :sId;';
        $rs = $this->conn->one($sql, ['sId' => $scrivener]);

        return empty($rs['sales']) ? '' : $rs['sales'];
    }

    //確認對象是否為地政士
    private function checkFeedbackMoney($case)
    {
        $list = [];

        if (!empty($case['cBranchNum']) && !empty($this->checkFeedbackTarget($case['cBranchNum'], $case['cCaseFeedback'], $case['cFeedbackTarget'], $case['cCaseFeedBackMoney']))) {
            $list[] = [
                'cBranchNum' => $case['cBranchNum'],
                'cBranchStore' => $case['store'],
                'cFeedbackTarget' => $case['cFeedbackTarget'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney'],
                'cScrivener' => $case['cScrivener'],
                'scrivener' => $case['scrivener'],
                'sales' => $case['bsales'],
            ];
        }

        if (!empty($case['cBranchNum1']) && !empty($this->checkFeedbackTarget($case['cBranchNum1'], $case['cCaseFeedback1'], $case['cFeedbackTarget1'], $case['cCaseFeedBackMoney1']))) {
            $list[] = [
                'cBranchNum' => $case['cBranchNum1'],
                'cBranchStore' => $case['store1'],
                'cFeedbackTarget' => $case['cFeedbackTarget1'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney1'],
                'cScrivener' => $case['cScrivener'],
                'scrivener' => $case['scrivener'],
                'sales' => $case['bsales1'],
            ];
        }

        if (!empty($case['cBranchNum2']) && !empty($this->checkFeedbackTarget($case['cBranchNum2'], $case['cCaseFeedback2'], $case['cFeedbackTarget2'], $case['cCaseFeedBackMoney2']))) {
            $list[] = [
                'cBranchNum' => $case['cBranchNum2'],
                'cBranchStore' => $case['store2'],
                'cFeedbackTarget' => $case['cFeedbackTarget2'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney2'],
                'cScrivener' => $case['cScrivener'],
                'scrivener' => $case['scrivener'],
                'sales' => $case['bsales2'],
            ];
        }

        if (!empty($case['cBranchNum3']) && !empty($this->checkFeedbackTarget($case['cBranchNum3'], $case['cCaseFeedback3'], $case['cFeedbackTarget3'], $case['cCaseFeedBackMoney3']))) {
            $list[] = [
                'cBranchNum' => $case['cBranchNum3'],
                'cBranchStore' => $case['store3'],
                'cFeedbackTarget' => $case['cFeedbackTarget3'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney3'],
                'cScrivener' => $case['cScrivener'],
                'scrivener' => $case['scrivener'],
                'sales' => $case['bsales3'],
            ];
        }

        if (($case['cSpCaseFeedBackMoney'] > 0)
            || ($case['cScrivenerSpRecall'] > 0 and $case['cBranchNum'] != 505)
            || ($case['cBranchScrRecall'] > 0 and $case['cBranchNum'] != 505)
            || ($case['cBranchScrRecall1'] > 0 and $case['cBranchNum'] != 505)
            || ($case['cBranchScrRecall2'] > 0 and $case['cBranchNum'] != 505)
            || ($case['cBranchScrRecall3'] > 0 and $case['cBranchNum'] != 505)
        ) {
            $list[] = [
                // 'cBranchNum'         => $case['cBranchNum3'],
                // 'cFeedbackTarget'    => $case['cFeedbackTarget3'],
                'cCaseFeedBackMoney' => $case['cSpCaseFeedBackMoney'],
                'cScrivener' => $case['cScrivener'],
                'scrivener' => $case['scrivener'],
                'scrivenerSales' => $this->getScrivenerSales($case['cScrivener']),
            ];

        }

        return $list;
    }

    private function getScrivener($certifiedId)
    {
        $sql = 'SELECT
                    c.`cScrivener`, s.`sOffice`
                FROM
                    tContractScrivener AS c
                  LEFT JOIN
                    tScrivener AS s
                  ON c.cScrivener = s.sId
                WHERE
                    c.cCertifiedId = :cCertifiedId;
                ';

        $rs = $this->conn->one($sql, ['cCertifiedId' => $certifiedId]);
        return empty($rs) ? [] : $rs;
    }

    private function getBranchData($branch)
    {
        $sql = 'SELECT
                    a.bStore, b.bSales 
                FROM
                    tBranch AS a LEFT JOIN tBranchSalesForPerformance AS b ON a.bId = b.bBranch
                WHERE
                    a.bId = :bId;
                ';

        $rs = $this->conn->one($sql, ['bId' => $branch]);
        return empty($rs) ? [] : $rs;
    }

    //更新業務確認時間
    public function updateSalesConfirmTime($fId, $certifiedId, $target = "R")
    {
        $sql = 'UPDATE
                    tFeedBackConfirm
                SET
                    fSalesConfirmDate = NOW(),
                    fSalesConfirmId = :salesConfirmId 
                WHERE fId = :fId
                    AND fCertifiedId = :cId';

        $pattern = [
            'fId' => $fId,
            'cId' => $certifiedId,
            'salesConfirmId' => $_SESSION['member_id'],
        ];
        $res = $this->conn->exeSql($sql, $pattern);

        return $res;
    }

    private function getBranchNum($certifiedId, $category)
    {
        $sql = 'SELECT
                    r.cBranchNum, r.cBranchNum1, r.cBranchNum2, r.cBranchNum3
                FROM
                    tContractRealestate AS r
                WHERE
                    r.cCertifyId = :cCertifyId;
                ';

        $rs = $this->conn->one($sql, ['cCertifyId' => $certifiedId]);
        if (empty($rs)) {
            return [];
        }

        $branchNum = [
            '1' => $rs['cBranchNum'],
            '2' => $rs['cBranchNum1'],
            '3' => $rs['cBranchNum2'],
            '6' => $rs['cBranchNum3'],
        ];
        return $branchNum[$category];
    }

    //取得所有保證號碼
    public function getCertifiedId()
    {
        $sql = 'SELECT
                    fCertifiedId
                FROM
                    tFeedBackConfirm AS a
                WHERE fHidden = 0
                ;';
        $rs = $this->conn->all($sql);

        if (empty($rs)) {
            return [];
        }

        $certifiedIds = '';
        foreach ($rs as $v) {
            $certifiedIds .= $v['fCertifiedId'];
            $certifiedIds .= ',';
        }

        return $certifiedIds;
    }

    //判斷案件是否有在隨案
    public function isPayByCase($certifiedId)
    {
        $sql = 'SELECT
                    a.fId
                FROM
                    tFeedBackMoneyPayByCase AS a
                WHERE
                    a.fCertifiedId = "'.$certifiedId.'";';
        $rs = $this->conn->all($sql);

        if (empty($rs)) {
            return false;
        } else {
            return true;
        }
    }

    function deleteSalesConfirmRecord($certifiedId, $target = "R"){
        $sql = 'UPDATE
                    tFeedBackConfirm
                SET
                    fHidden = 1
                WHERE fCertifiedId = :cId
                    AND fTarget = :target
                    AND fHidden = :fHidden
                    AND fSalesConfirmId IS NULL;';
        $pattern = [
            'cId' => $certifiedId,
            'target' => $target,
            'fHidden' => 0,
        ];
        $res = $this->conn->exeSql($sql, $pattern);

        return $res;
    }

    // 審核過更新標記資料
    function salesConfirmAudit($certifiedId){
        $branchScrivener = $this->getBranchScrivener($certifiedId);

        if(!empty($branchScrivener)){
            $this->salesConfirmList($certifiedId,
                $branchScrivener['cBranchNum'],
                $branchScrivener['cBranchNum1'],
                $branchScrivener['cBranchNum2'],
                $branchScrivener['cBranchNum3'],
                $branchScrivener['cScrivener']);
        } else {
            echo $certifiedId.' 找無資料。';
        }
    }

    private function getBranchScrivener($cId){
        $sql = 'SELECT
                    a.cCaseFeedback,
                    a.cCaseFeedback1,
                    a.cCaseFeedback2,
                    a.cCaseFeedback3,
                    a.cFeedbackTarget,
                    a.cFeedbackTarget1,
                    a.cFeedbackTarget2,
                    a.cFeedbackTarget3,
                    a.cCaseFeedBackMoney,
                    a.cCaseFeedBackMoney1,
                    a.cCaseFeedBackMoney2,
                    a.cCaseFeedBackMoney3,
                    a.cFeedBackClose,
                    a.cSpCaseFeedBackMoney,
                    a.cScrivenerSpRecall,
                    a.cBranchScrRecall,
                    a.cBranchScrRecall1,
                    a.cBranchScrRecall2,
                    a.cBranchScrRecall3,
                    b.cBranchNum,
                    b.cBranchNum1,
                    b.cBranchNum2,
                    b.cBranchNum3,
                    c.cScrivener,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum) AS store,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum1) AS store1,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum2) AS store2,
                    (SELECT bStore FROM tBranch WHERE bId = b.cBranchNum3) AS store3,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum) AS bsales,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum1) AS bsales1,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum2) AS bsales2,
                    (SELECT bSales FROM tBranchSalesForPerformance WHERE bBranch = b.cBranchNum3) AS bsales3,
                    (SELECT sOffice FROM tScrivener WHERE c.cScrivener = sId) as scrivener
                FROM
                    tContractCase AS a
                JOIN
                    tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId
                JOIN
                    tContractScrivener AS c ON a.cCertifiedId = c.cCertifiedId
                WHERE
                    a.cCertifiedId = :cId;';
        $rs = $this->conn->one($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        return $rs;
    }

    public function check($cId)
    {
//        return json_encode($output, JSON_UNESCAPED_UNICODE);
//        if (!empty($output['msg'])) {
//            Slack::channelSend($id . '(' . $output['msg'] . ')', 'https://hooks.slack.com/services/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', '回饋金異常通知');
//        }
    }
}

?>