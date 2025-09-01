<?php
namespace First1\V1\PayByCase;

use First1\V1\Notify\Slack;

require_once dirname(dirname(__DIR__)) . '/.env.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
// require_once dirname(__DIR__) . '/traits/LineNotify.traits.php';
require_once dirname(__DIR__) . '/traits/PayTax.traits.php';
require_once dirname(__DIR__) . '/traits/slackIncomingWebhook.trait.php';
require_once dirname(__DIR__) . '/LineBotRequest.php';
require_once dirname(__DIR__) . '/slack.class.php';

class PayByCase
{
    // use \First1\V1\Util\LineNotify;
    use \First1\V1\Util\PayTax;
    use \SlackIncomingWebhook;

    private $conn;

    public function __construct()
    {
        $this->conn = new \first1DB;
    }

    //取得回饋金隨案支付案件
    public function getRealtyFeedbackMoney($cId)
    {
        $sales = '';

        $review_case   = $this->getOtherFeedbackCase($cId); //取得其他回饋金資訊
        $feedback_case = $this->getFeedbackCase($cId);      //取得案件回饋金資訊

        $cases = array_merge($review_case, $feedback_case);

        if (empty($cases)) { //無回饋給代書
            return [];
        }
        $detail = [];
        foreach ($cases as $k => $case) {
            unset($case['sales']);
            $detail[] = $case;

            if ($case['cCaseFeedBackMoney'] > 0) {
                $feedbackScrivener  = $case['scrivener'];
                $feedbackcScrivener = $case['cScrivener'];
                $feedbackSales      = $case['sales'];
            }
        }

        $case = [
            'case'       => $detail,
            'scrivener'  => isset($feedbackScrivener) ? $feedbackScrivener : $cases[0]['scrivener'],
            'cScrivener' => isset($feedbackcScrivener) ? $feedbackcScrivener : $cases[0]['cScrivener'],
            'total'      => $this->calculateFeedbackMoney($feedback_case, $review_case),
            'sales'      => isset($feedbackSales) ? $feedbackSales : $cases[0]['sales'],
        ];

        return $case;
    }
    ##

    private function _calculateScrFeedback($cId)
    {
        $sql = 'SELECT
                    (SUM(a.cScrivenerSpRecall) + SUM(a.cBranchScrRecall) + SUM(a.cBranchScrRecall1) + SUM(a.cBranchScrRecall2) + SUM(a.cBranchScrRecall3)) AS total
                FROM
                    tContractCase AS a
                WHERE
                    a.cCertifiedId = :cId;';
        $rs = $this->conn->one($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }
        return $rs['total'];
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
    ##

    //確認對象是否為地政士
    private function checkFeedbackMoney($case)
    {
        $list = [];

        if (! empty($this->checkFeedbackTarget($case['cBranchNum'], $case['cCaseFeedback'], $case['cFeedbackTarget'], $case['cCaseFeedBackMoney']))) {
            $list[] = [
                'cBranchNum'         => $case['cBranchNum'],
                'cFeedbackTarget'    => $case['cFeedbackTarget'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
            ];
        }

        if (! empty($this->checkFeedbackTarget($case['cBranchNum1'], $case['cCaseFeedback1'], $case['cFeedbackTarget1'], $case['cCaseFeedBackMoney1']))) {
            $list[] = [
                'cBranchNum'         => $case['cBranchNum1'],
                'cFeedbackTarget'    => $case['cFeedbackTarget1'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney1'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
            ];
        }

        if (! empty($this->checkFeedbackTarget($case['cBranchNum2'], $case['cCaseFeedback2'], $case['cFeedbackTarget2'], $case['cCaseFeedBackMoney2']))) {
            $list[] = [
                'cBranchNum'         => $case['cBranchNum2'],
                'cFeedbackTarget'    => $case['cFeedbackTarget2'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney2'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
            ];
        }

        if (! empty($this->checkFeedbackTarget($case['cBranchNum3'], $case['cCaseFeedback3'], $case['cFeedbackTarget3'], $case['cCaseFeedBackMoney3']))) {
            $list[] = [
                'cBranchNum'         => $case['cBranchNum3'],
                'cFeedbackTarget'    => $case['cFeedbackTarget3'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney3'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
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
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
            ];

        }

        return $list;
    }

    private function checkFeedbackTarget($cBranchNum, $cCaseFeedback, $cFeedbackTarget, $cCaseFeedBackMoney)
    {
        //設定不回饋或回饋對象為仲介時為 false
        // return (($cCaseFeedback == 1) || ($cFeedbackTarget == 1) || ($cBranchNum == 0) || ($cCaseFeedBackMoney == 0)) ? false : true;
        return (($cCaseFeedback == 0) && ($cFeedbackTarget == 2) && ($cBranchNum > 0)) ? true : false;
    }
    ##

    //取得已(未)審核案件
    public function getReviewCase($cId)
    {
        $sql = 'SELECT
                    a.fCertifiedId,
                    a.fId,
                    a.fStatus,
                    (SELECT cFeedBackClose FROM tContractCase WHERE cCertifiedId = a.fCertifiedId) AS close,
                    b.fCategory,
                    b.fCaseFeedback,
                    b.fFeedbackTarget,
                    b.fFeedbackStoreId,
                    b.fCaseFeedBackMoney,
                    (SELECT sOffice FROM tScrivener WHERE sId = b.fFeedbackStoreId) as scrivener
                FROM
                    tFeedBackMoneyReview AS a
                JOIN
                    tFeedBackMoneyReviewList AS b ON a.fId = b.fRId
                WHERE
                    a.fFail = 0
                    AND a.fCertifiedId = :cId
                    AND b.fFeedbackTarget = 2
                    AND b.fDelete = 0;';
        $rs = $this->conn->all($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        $list = [];
        foreach ($rs as $v) {
            $list[] = [
                'fStatus'            => $v['fStatus'],
                'cCaseFeedBackMoney' => $v['fCaseFeedBackMoney'],
                'cScrivener'         => $v['fFeedbackStoreId'],
                'scrivener'          => $v['scrivener'],
            ];
        }

        return $list;
    }
    ##

    //取得已審核其他回饋金案件
    public function getOtherFeedbackCase($cId)
    {
        $sql = 'SELECT
                    a.fCertifiedId,
                    a.fStoreId,
                    a.fMoney,
                    (SELECT sOffice FROM tScrivener WHERE sId = a.fStoreId) as scrivener
                FROM
                    tFeedBackMoney AS a
                WHERE
                    a.fDelete = 0
                    AND a.fCertifiedId = :cId
                    AND a.fType = 1
                    AND a.fDelete = 0;';
        $rs = $this->conn->all($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        $list = [];
        foreach ($rs as $v) {
            $list[] = [
                'cOtherFeedBack'     => 1,
                'cCaseFeedBackMoney' => $v['fMoney'],
                'cScrivener'         => $v['fStoreId'],
                'scrivener'          => $v['scrivener'],
                'sales'              => $this->getScrivenerSales($v['fStoreId']),
            ];
        }

        return $list;
    }
    ##

    //計算回饋金總金額
    private function calculateFeedbackMoney($feedback, $review)
    {
        $total = 0;

        if (! empty($feedback)) {
            foreach ($feedback as $v) {
                $total += $v['cCaseFeedBackMoney'];
            }
        }

        if (! empty($review)) {
            foreach ($review as $v) {
                $total += $v['cCaseFeedBackMoney'];
            }
        }

        return $total;
    }
    ##

    //取得代書所屬績效業務
    public function getScrivenerSales($scrivener)
    {
        $sql = 'SELECT sSales as sales FROM tScrivenerSalesForPerformance WHERE sScrivener = :sId;';
        $rs  = $this->conn->one($sql, ['sId' => $scrivener]);

        return empty($rs['sales']) ? '' : $rs['sales'];
    }
    ##

    //刪除業務確認列表紀錄
    public function removeSalesConfirmRecord($cId, $target = "S")
    {
        $sql = 'DELETE FROM tFeedBackMoneyPayByCase WHERE fCertifiedId = :cId AND fTarget = :target;';

        $this->_writeLogCase($cId, json_encode(['cId' => $cId, 'target' => $target]), '刪除業務確認列表紀錄');

        return $this->conn->exeSql($sql, ['cId' => $cId, 'target' => $target]);
    }
    ##

    //刪除案件的銀行帳戶資料
    public function deletePayByCaseAccount($certifiedId, $target = "S")
    {
        $sql = 'DELETE FROM tFeedBackMoneyPayByCaseAccount WHERE fCertifiedId = :cId AND fTarget = :target;';

        $this->_writeLogCase($certifiedId, json_encode(['cId' => $certifiedId, 'target' => $target]), '刪除案件的銀行帳戶資料');

        return $this->conn->exeSql($sql, ['cId' => $certifiedId, 'target' => $target]);
    }
    ##

    //取得業務確認列表紀錄
    public function getSalesConfirmRecord($cId, $target = 'S')
    {
        $sql = 'SELECT fDetail FROM tFeedBackMoneyPayByCase WHERE fCertifiedId = :cId AND fTarget = :target;';
        return $this->conn->one($sql, ['cId' => $cId, 'target' => $target]);
    }
    ##

    //回饋金額大於2萬&&自然人身份，會計必須覆核(直接加上日期)
    private function accountConfirmLimit($money, $feedBackIdentity)
    {
        if ($money >= 20000 and $feedBackIdentity == 2) {
            return 'NULL';
        } else {
            return 'NOW()';
        }
    }
    ##

    //回饋金隨案付款業務通知列表
    public function salesConfirmList($cId)
    {
        $this->_writeLogConfirmList($cId, '', '隨案列表開始');

        #判斷中繼帳戶資料是否存在
        if ($this->_checkBankTransRelay($cId)) {
            $this->_writeLogConfirmList($cId, '', 'tBankTransRelay資料已存在');
            return false;
        }
        #確認履保費是否出款建檔
        if ($this->checkBankLoansDate($cId)) {
            $this->_writeLogConfirmList($cId, '', '履保費已出款建檔');
            return false;
        }
        #判斷(回饋金修改申請) 是否有未核可回饋金
        $rs = $this->getFeedBackMoneyReview($cId);
        if (empty($rs)) {
            $feedback_case = $this->getRealtyFeedbackMoney($cId);
        } else {
            $feedback_case = $this->getReviewListFeedbackMoney($rs['fId']);
        }
        //確認代書是否選擇隨案
        if (! empty($feedback_case)) {
            $res = $this->checkScrivenerFeedDateCat($feedback_case['cScrivener']);
        }

        //不回饋給代書、刪除列表通知
        if (empty($feedback_case) or ($res['sFeedDateCat'] != 2)) {

            $this->payByCaseLog($cId, $feedback_case, '2', '1'); //增加異動再確認
            $this->updatePayByCaseLog($cId);                     //更新異動再確認

            $this->removeSalesConfirmRecord($cId);
            $this->deletePayByCaseAccount($cId);

            $this->_writeLogConfirmList($cId, '', '不回饋給代書或不是隨案');

            if (empty($this->getRealtyFeedbackMoney($cId)) and $this->_calculateScrFeedback($cId) > 0) {
                Slack::channelSend('保證號碼：' . $cId . '沒有回饋代書，也沒有特殊回饋，刪除此隨案');
            }

            return true;
        }
        ##

        $feedBackIdentity = $this->getFeedBackIdentity($feedback_case['cScrivener']);

        //有回饋給代書、新增更新業務列表
        $record = $this->getSalesConfirmRecord($cId);

        if (empty($record)) {
            //沒有業務通知列表紀錄(新增)
            $res = $this->addSalesConfirmRecord($cId, $feedback_case['sales'], $feedBackIdentity['fIdentity'], $feedback_case);
            $this->_writeLogConfirmList($cId, json_encode(['res' => $res, 'lastInsertId' => $this->conn->lastInsertId()]), '隨案列表新增');
            //異動資料調整成不顯示(軟刪除)
            $this->deletePayByCaseLog($cId, '2');
        } else {
            $scrSales = $feedback_case['sales'];
            unset($feedback_case['sales']);
            $_compare1 = json_encode($feedback_case); //更新後的紀錄
            $_compare2 = $record['fDetail'];          //更新前的紀錄

            if ($_compare1 != $_compare2) { //前後記錄不一致(跟新紀錄並清除確認紀錄、重新確認)
                $this->_writeLog($cId, $_compare2, $_compare1);

                $this->payByCaseLog($cId, $feedback_case, '1', '0'); //更新前存log
                $this->updateSalesConfirmRecord($cId, $scrSales, $feedBackIdentity['fIdentity'], $feedback_case);
                $this->deletePayByCaseAccount($cId);
            }

            $_compare1 = $_compare2 = null;
            unset($_compare1, $_compare2);
        }
        ##
        $this->_writeLogConfirmList($cId, '', '隨案列表結束');
    }
    ##

    //人工產生隨案付款資料(會計用)
    public function manualAddSalesConfirmRecord($cId)
    {
        $record = $this->getSalesConfirmRecord($cId);
        if (! empty($record)) {
            $this->removeSalesConfirmRecord($cId);
            $this->deletePayByCaseAccount($cId);
        }
        $feedback_case    = $this->getRealtyFeedbackMoney($cId);
        $feedBackIdentity = $this->getFeedBackIdentity($feedback_case['cScrivener']);
        //新增隨案付款
        return $this->addSalesConfirmRecord($cId, $feedback_case['sales'], $feedBackIdentity['fIdentity'], $feedback_case, 'S', $_SESSION['member_id']);
    }

    //新增業務確認列表紀錄
    public function addSalesConfirmRecord($cId, $sales, $feedBackIdentity, $detail = [], $target = "S", $creator = null)
    {
        unset($detail['sales']);
        $sql = 'INSERT INTO
                    tFeedBackMoneyPayByCase
                    (
                        fCertifiedId,
                        fTarget,
                        fTargetId,
                        fSales,
                        fAccountantConfirmDate,
                        fTax,
                        fNHI,
                        fDetail,
                        fCreator,
                        fCreated_at
                    )
                    VALUES
                    (
                        "' . $cId . '",
                        "' . $target . '",
                        "' . $detail['cScrivener'] . '",
                        "' . $sales . '",
                        ' . $this->accountConfirmLimit($detail['total'], $feedBackIdentity) . ',
                        "' . $this->feedbackIncomeTax($detail['total'], $feedBackIdentity) . '",
                        "' . $this->feedbackNHITax($detail['total'], $feedBackIdentity) . '",
                        "' . addslashes(json_encode($detail)) . '",
                        "' . $creator . '",
                        NOW()
                    );';

        return $this->conn->exeSql($sql);
    }
    ##

    //更新業務確認列表紀錄
    public function updateSalesConfirmRecord($cId, $sales, $feedBackIdentity, $detail = [], $target = "S")
    {
        $sql = 'UPDATE
                    tFeedBackMoneyPayByCase
                SET
                    fTargetId = "' . $detail['cScrivener'] . '",
                    fSales = "' . $sales . '",
                    fSalesConfirmDate = NULL,
                    fSalesConfirmId = NULL,
                    fAccountant = 0,
                    fAccountantConfirmDate = ' . $this->accountConfirmLimit($detail['total'], $feedBackIdentity) . ',
                    fTax = ' . $this->feedbackIncomeTax($detail['total'], $feedBackIdentity) . ',
                    fNHI = ' . $this->feedbackNHITax($detail['total'], $feedBackIdentity) . ',
                    fReceipt = "N",
                    fDetail = "' . addslashes(json_encode($detail)) . '"
                WHERE
                    fCertifiedId = "' . $cId . '"
                    AND fTarget = "' . $target . '";';
        return $this->conn->exeSql($sql);
    }
    ##

    //紀錄回饋金帳戶
    public function savePayByCaseAccount($certifiedId, $bank, $fId, $target = "S")
    {
        //補上戶籍、聯絡地址
        $addr = $this->_getFeedBackAddr($bank['bankId']);

        $sql = 'INSERT INTO
                    tFeedBackMoneyPayByCaseAccount
                (
                    fCertifiedId,
                    fTarget,
                    fType,
                    fIdentityIdNumber,
                    fBankMain,
                    fBankBranch,
                    fBankAccount,
                    fBankAccountName,
                    fBankId,
                    fZipC,
                    fAddrC,
                    fZipR,
                    fAddrR,
                    fPayByCaseId,
                    fCreated_at
                )
                VALUES
                (
                    :cId,
                    :target,
                    :type,
                    :id_no,
                    :main,
                    :branch,
                    :account,
                    :account_name,
                    :bank_id,
                    :zip_c,
                    :addr_c,
                    :zip_r,
                    :addr_r,
                    :fPayByCaseId,
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                    fType = :type,
                    fIdentityIdNumber = :id_no,
                    fBankMain = :main,
                    fBankBranch = :branch,
                    fBankAccount = :account,
                    fBankAccountName = :account_name,
                    fZipC = :zip_c,
                    fAddrC = :addr_c,
                    fZipR = :zip_r,
                    fAddrR = :addr_r,
                    fPayByCaseId = :fPayByCaseId
                    ;';
        return $this->conn->exeSql($sql, [
            'cId'          => $certifiedId,
            'target'       => $target,
            'type'         => $bank['identity'],
            'id_no'        => $bank['idNumber'],
            'main'         => $bank['main'],
            'branch'       => $bank['branch'],
            'account'      => $bank['account'],
            'account_name' => $bank['accountName'],
            'bank_id'      => $bank['bankId'],
            'zip_c'        => $addr['fZipC'],
            'addr_c'       => $addr['fAddrC'],
            'zip_r'        => $addr['fZipR'],
            'addr_r'       => $addr['fAddrR'],
            'fPayByCaseId' => $fId,
        ]);
    }
    ##

    //更新業務審核時間
    public function updateSalesConfirmTime($certifiedId, $target = "S")
    {
        $case = $this->getPayByCase($certifiedId, $target);

        $sql = 'UPDATE
                    tFeedBackMoneyPayByCase
                SET
                    fSalesConfirmDate = NOW(),
                    fSalesConfirmId = :salesConfirmId,
                    fTax = :tax,
                    fNHI = :nhi
                WHERE
                    fCertifiedId = :cId
                    AND fTarget = :target;';

        $pattern = [
            'cId'            => $certifiedId,
            'target'         => $target,
            'salesConfirmId' => $_SESSION['member_id'],
            'tax'            => $this->feedbackIncomeTax($case['detail']['total'], $case['fType']),
            'nhi'            => $this->feedbackNHITax($case['detail']['total'], $case['fType']),
        ];
        $res = $this->conn->exeSql($sql, $pattern);

        if (! empty($case['fCreator'])) {
            if ($this->_checkBankTransRelay($certifiedId)) {
                return false;
            }
            if ($case['detail']['total'] >= 20000) {
                return false;
            }
            $this->_saveRelayBank($certifiedId, $case);
        }
        $this->_writeLogCase($certifiedId, json_encode($pattern), '隨案回饋金確認');
        return $res;
    }
    ##

    //更新代扣稅額與補充保費
    public function updatePayTax($certifiedId, $identityNumberChange, $target = "S")
    {
        $case = $this->getPayByCase($certifiedId, $target);

        $str = '';
        if (1 == $identityNumberChange and $case['detail']['total'] > 20000) {
            $str = ', fAccountantConfirmDate = null';
        }

        $sql = 'UPDATE
                    tFeedBackMoneyPayByCase
                SET
                    fTax = :tax, fNHI = :nhi ' . $str . '
                WHERE
                    fCertifiedId = :cId
                  AND
                    fTarget = :target;
                ';

        return $this->conn->exeSql($sql, [
            'cId'    => $certifiedId,
            'target' => $target,
            'tax'    => $this->feedbackIncomeTax($case['detail']['total'], $case['fType']),
            'nhi'    => $this->feedbackNHITax($case['detail']['total'], $case['fType']),
        ]);
    }
    ##

    //取得隨案紀錄
    public function getPayByCase($certifiedId, $target = "S")
    {
        $sql = 'SELECT
                    a.fId,
                    a.fCertifiedId,
                    a.fSales,
                    a.fSalesConfirmDate,
                    a.fAccountant,
                    a.fAccountantConfirmDate,
                    a.fTax,
                    a.fNHI,
                    a.fReceipt,
                    a.fAccountantClose,
                    a.fNHIpay,
                    a.fDetail,
                    a.fCreator,
                    a.fMultipleFeedback,
                    b.fBankMain,
                    b.fBankBranch,
                    b.fBankAccount,
                    b.fBankAccountName,
                    b.fType,
                    b.fIdentityIdNumber,
                    a.fTargetId
                FROM
                    tFeedBackMoneyPayByCase AS a
                LEFT JOIN
                    tFeedBackMoneyPayByCaseAccount AS b ON (a.fCertifiedId = b.fCertifiedId AND b.fTarget = :target AND a.fId = b.fPayByCaseId)
                WHERE
                    a.fCertifiedId = :cId
                    AND a.fTarget = :target;';
        $case = $this->conn->one($sql, ['cId' => $certifiedId, 'target' => $target]);
        if (empty($case)) {
            return [];
        }

        $case['detail'] = json_decode($case['fDetail'], true);
        return $case;
    }
    ##

    public function getPayByCaseWithTargetId($certifiedId, $targetId, $target = "S")
    {
        $sql = 'SELECT
                    a.fCertifiedId,
                    a.fSales,
                    a.fSalesConfirmDate,
                    a.fAccountant,
                    a.fAccountantConfirmDate,
                    a.fTax,
                    a.fNHI,
                    a.fReceipt,
                    a.fAccountantClose,
                    a.fNHIpay,
                    a.fDetail,
                    a.fCreator,
                    b.fBankMain,
                    b.fBankBranch,
                    b.fBankAccount,
                    b.fBankAccountName,
                    b.fType,
                    b.fIdentityIdNumber
                FROM
                    tFeedBackMoneyPayByCase AS a
                LEFT JOIN
                    tFeedBackMoneyPayByCaseAccount AS b ON (a.fId = b.fPayByCaseId )
                WHERE
                    a.fCertifiedId = :cId
                    AND a.fTarget = :target
                    AND a.fTargetId = :targetId;';

        $case = $this->conn->one($sql, ['cId' => $certifiedId, 'target' => $target, 'targetId' => $targetId]);
        if (empty($case)) {
            return [];
        }

        $case['detail'] = json_decode($case['fDetail'], true);
        return $case;
    }

    //確認案件是否需要會計確認
    public function needAccountingConfirm($certifiedId, $target = "S")
    {
        global $env;
        $message = '';

        $case = $this->getPayByCase($certifiedId, $target);
        if ($case['fMultipleFeedback'] == 1) {
            $this->incomingWebhook($certifiedId . '雙代書隨案列表資料異動');
            $message .= '[雙代書]';
        }

        $sql = 'SELECT fId FROM tFeedBackMoneyPayByCase WHERE fCertifiedId  = :cId AND fTarget = :target AND fAccountantConfirmDate IS NULL;';
        $res = empty($this->conn->one($sql, ['cId' => $certifiedId, 'target' => $target])) ? false : true;

        if ($res) {
            $message .= '保證號碼：' . $certifiedId . "\r\n" . '回饋金額大於2萬！' . "\r\n" . '請至"回饋金隨案出款確認清單"中確認 ...' . "\r\n\r\n" . 'http://' . $_SERVER['SERVER_NAME'] . '/accounting/payByCaseAccountingConfirm.php';

            // $key = 'qbYfrbDEwuopFvvenBdVAJEhVeoV0AsFGtLtu90CqEW'; //財務部提醒通知
            // $this->notify($key, $msg);

            //line通知
            $bot = new \LineBotRequest($env['line']['channel_id'], $env['line']['channel_secret'], $env['line']['channel_access_token'], dirname(dirname(__DIR__)) . '/log/line');

            $groupId = 'C02a322e77d1fc021e95e2b1c77189ca9';
            $request = [];

            $request['userId']     = $groupId;
            $request['messages'][] = [
                'actionType' => 'text',
                'text'       => $message,
            ];

            $response = $bot->send($request);

            //slack通知
            $this->incomingWebhook($message);
        }

        return $res;
    }
    ##

    //更新會計審核時間
    public function updateAccountingConfirmTime($certifiedId, $accountant, $fNHIpay, $tax, $target = "S", $NHI = 0)
    {
        $sql = '
            UPDATE
                tFeedBackMoneyPayByCase
            SET
                fAccountant = :accountant,
                fAccountantConfirmDate = NOW(),
                fNHIpay = :fNHIpay,
                fTax = :fTax,
                fNHI = :fNHI
            WHERE
                fCertifiedId = :cId
              AND fTarget = :target;
            ';

        $res = $this->conn->exeSql($sql, [
            'cId'        => $certifiedId,
            'accountant' => $accountant,
            'fNHIpay'    => $fNHIpay,
            'target'     => $target,
            'fTax'       => $tax,
            'fNHI'       => $NHI,
        ]);

        $case = $this->getPayByCase($certifiedId, $target);
        if (! empty($case['fCreator'])) {
            if ($this->_checkBankTransRelay($certifiedId)) {
                return false;
            }

            $this->_saveRelayBank($certifiedId, $case);
        }
        return $res;
    }
    ##

    //取得審核時間
    public function getConfirmDate($certifiedId, $target = "S")
    {
        $sql = 'SELECT
                    a.fSalesConfirmDate,
                    a.fAccountantConfirmDate
                FROM
                    tFeedBackMoneyPayByCase AS a
                WHERE
                    a.fCertifiedId = :cId
                    AND a.fTarget = :target;';
        return $this->conn->one($sql, ['cId' => $certifiedId, 'target' => $target]);
    }
    ##

    //取得所有保證號碼
    public function getCertifiedId()
    {
        $sql = 'SELECT
                    fCertifiedId
                FROM
                    tFeedBackMoneyPayByCase AS a
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

    public function checkScrivenerFeedDateCat($sId)
    {
        $sql = 'SELECT
                    sFeedDateCat
                FROM
                    `tScrivener`
                WHERE
                    sId = :sId;';

        return $this->conn->one($sql, ['sId' => $sId]);
    }

    //取得地政士維護回饋金帳戶身份
    public function getFeedBackIdentity($sId, $type = 1)
    {
        $sql = 'SELECT
                    a.fIdentity
                FROM
                    tFeedBackData AS a
                WHERE
                    a.fType = :type
                    AND a.fStoreId = :sId
                    AND a.fStatus = 0
                    AND a.fStop = 0;';

        $rs = $this->conn->one($sql, ['type' => $type, 'sId' => $sId]);

        return empty($rs) ? [] : $rs;
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

    private function getReviewListFeedbackMoney($fId, $fFeedbackTarget = 2)
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
                    (fCaseFeedback = 0 or (fCategory = 4))
                  AND
                    fDelete = 0
        ';

        $rs = $this->conn->all($sql, ['fRId' => $fId, 'fFeedbackTarget' => $fFeedbackTarget]);
        if (empty($rs)) {
            return [];
        }

        $cases = [];

        $scrivener = $this->getScrivener($rs[0]['fCertifiedId']);

        $scrivenerOffice = $scrivener['sOffice'];
        $scrivenerId     = $scrivener['cScrivener'];
        $total           = 0;
        foreach ($rs as $k => $v) {
            if (in_array($v['fCategory'], [1, 2, 3, 6])) {
                $cases[$k]['cBranchNum']      = $this->getBranchNum($v['fCertifiedId'], $v['fCategory']);
                $cases[$k]['cFeedbackTarget'] = $v['fFeedbackTarget'];
            }
            if ($v['fCategory'] == 4) {
                $cases[$k]['cSpFeedBack'] = 1;
            }
            if ($v['fCategory'] == 5) {
                $cases[$k]['cOtherFeedBack'] = 1;
            }

            $cases[$k]['cCaseFeedBackMoney'] = $v['fCaseFeedBackMoney'];
            $cases[$k]['cScrivener']         = $scrivener['cScrivener'];
            $cases[$k]['scrivener']          = $scrivener['sOffice'];
            $total                           = $v['fCaseFeedBackMoney'] + $total;

            //其他回饋的對象不是此案件代書
            if ($v['fCategory'] == 5 && $v['fFeedbackTarget'] == 2 && $v['fFeedbackStoreId'] != $scrivener['cScrivener']) {
                $otherScrivener          = $this->getScrivenerInfo($v['fFeedbackStoreId']);
                $cases[$k]['cScrivener'] = $otherScrivener['sId'];
                $cases[$k]['scrivener']  = $otherScrivener['sOffice'];
                if ($v['fCaseFeedBackMoney'] > 0) {
                    $scrivenerOffice = $otherScrivener['sOffice'];
                    $scrivenerId     = $otherScrivener['sId'];
                }
            }
        }

        $detail = [
            'case'       => $cases,
            'scrivener'  => $scrivenerOffice,                       //連子瑩地政士事務所
            'cScrivener' => $scrivenerId,                           //2164
            'total'      => $total,                                 //0
            'sales'      => $this->getScrivenerSales($scrivenerId), //68
        ];
        return $detail;
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

    private function getScrivenerInfo($sId)
    {
        $sql = 'SELECT
                    s.`sId`, s.`sOffice`
                FROM
                    tScrivener AS s
                WHERE
                    s.sId = :sId;
                ';

        $rs = $this->conn->one($sql, ['sId' => $sId]);
        return empty($rs) ? [] : $rs;
    }

    private function _checkBankTransRelay($certifiedId)
    {
        $sql = '
            SELECT
                *
            FROM
                tBankTransRelay
            WHERE
                bCertifiedId = :bCertifiedId
                ';
        $res = $this->conn->one($sql, ['bCertifiedId' => $certifiedId]);

        return $res == false ? false : true;
    }

    private function _getFeedBackAddr($fId)
    {
        $sql = 'SELECT
                    fZipC, fAddrC, fZipR, fAddrR
                FROM
                    tFeedBackData as f
                WHERE
                    f.fId = :fId;
                ';

        $rs = $this->conn->one($sql, ['fId' => $fId]);
        return empty($rs) ? [] : $rs;
    }

    //是否出款建檔保證費
    public function checkBankLoansDate($certifiedId)
    {
        $sql = '
                SELECT
                    tMemo as cId, tBankLoansDate as eDate
                FROM
                    `tBankTrans`
                WHERE
                    tMemo = :certifiedId
                  AND
                    tKind = "保證費"
                  AND
                    tObjKind != "履保費先收(結案回饋)";
                ';

        $res = $this->conn->one($sql, ['certifiedId' => $certifiedId]);

        if ($res == false) {
            $sql = '
                SELECT
                    tMemo as cId, tInvoice as eDate
                FROM
                    `tBankTrans`
                WHERE
                    tMemo =:certifiedId
                  AND
                    tInvoice IS NOT NULL
                    ;';
            $res = $this->conn->one($sql, ['certifiedId' => $certifiedId]);
            if ($res == false) {
                return false;
            }
        }
        return true;
    }

    private function _saveRelayBank($certifiedId, $payByCase)
    {
        $_money = empty($payByCase['detail']['total']) ? 0 : $payByCase['detail']['total']; //金額
        $_money -= (empty($payByCase)) ? 0 : $payByCase['fTax'];                            //代扣稅款
        $_money -= (empty($payByCase)) ? 0 : $payByCase['fNHI'];                            //代扣二代健保

        //相減後回饋金額仍大於 0，則記錄至中繼帳號出款
        if ($_money > 0) {
            $_bank_code = empty($payByCase['fBankMain']) ? '' : $payByCase['fBankMain'];      //總行代碼
            $_bank_code .= empty($payByCase['fBankBranch']) ? '' : $payByCase['fBankBranch']; //分行代碼

            $_bank_account      = empty($payByCase['fBankAccount']) ? '' : $payByCase['fBankAccount'];         //帳號
            $_bank_account_name = empty($payByCase['fBankAccountName']) ? '' : $payByCase['fBankAccountName']; //帳戶

            $_txt = $certifiedId . '地政士回饋金';

            $sql = '
                SELECT
                    cEscrowBankAccount
                FROM
                    `tContractCase`
                WHERE
                    cCertifiedId =:certifiedId;';
            $res = $this->conn->one($sql, ['certifiedId' => $certifiedId]);

            $sql = 'INSERT INTO
                        tBankTransRelay
                    (
                        bUid,
                        bCertifiedId,
                        bVR_Code,
                        bDate,
                        bKind,
                        bBankCode,
                        bAccount,
                        bAccountName,
                        bMoney,
                        bIncomingMoney,
                        bTxt,
                        bConfirmOk,
                        bCreated_at
                    ) VALUES (
                        UUID(),
                        "' . $certifiedId . '",
                        "' . $res['cEscrowBankAccount'] . '",
                        "' . date("Y-m-d") . '",
                        "' . '地政士回饋金' . '",
                        "' . $_bank_code . '",
                        "' . $_bank_account . '",
                        "' . $_bank_account_name . '",
                        "' . $_money . '",
                        "' . 0 . '",
                        "' . $_txt . '",
                        1,
                        NOW()
                    );';
            return $this->conn->exeSql($sql);
        }
    }

    //取得業務確認列表紀錄資料
    public function getPayByCaseData($cId, $target = 'S')
    {
        $sql = 'SELECT * FROM tFeedBackMoneyPayByCase WHERE fCertifiedId = :cId AND fTarget = :target;';
        return $this->conn->one($sql, ['cId' => $cId, 'target' => $target]);
    }

    /*
     * (1)回饋對象從代書⇒仲介 (2)地政士特殊回饋從有⇒無 (3)其他回饋代書⇒仲介或不回饋代書。
     * 寫入一筆資料到回饋對象異動列表並記錄原因，異動列表需要有業務確認按鈕，並記錄確認的人員與確認時間，業務確認後，此案件才可出款建檔。
     * 一旦有代書回饋對象異動，業務就須重新確認。
     * fStatus 狀態：0=不顯示、1=顯示、2=刪除
     */
    public function payByCaseLog($cId, $feedback_case, $fKind, $fStatus)
    {
        $record = $this->getPayByCaseData($cId);

        if (! empty($record)) {

            //將原有異動資料設為列表不顯示(只顯示最新一筆)
            if ($fKind == '2') {
                //刪除前轉log
                $this->addPayByCaseLog($record, $feedback_case, '1', '2');

                $sql_update = '
                    UPDATE
                        tFeedBackMoneyPayByCaseLog
                    SET
                        fStatus = 0
                    WHERE
                        fCertifiedId = :cId AND fTarget = :target AND fKind = :kind;
                    ';

                $this->conn->exeSql($sql_update, [
                    'cId'    => $record['fCertifiedId'],
                    'target' => $record['fTarget'],
                    'kind'   => 2,
                ]);
            }

            $this->addPayByCaseLog($record, $feedback_case, $fKind, $fStatus); //增加待業務審核
        }

        return true;
    }

    //增加新資料到回饋金隨案付款Log
    public function addPayByCaseLog($record, $feedback_case, $fKind, $fStatus)
    {
        $fDetail = json_decode($record['fDetail'], true);

        $insert_data = [];
        if ($fKind == '1') {
            $insert_data = $record;
            unset($insert_data['fId']);
        } else if ($fKind == '2') {
            $feedback_case = $this->getFeedbackCaseLog($record['fCertifiedId']);
            $insert_data   = [
                'fCertifiedId' => $record['fCertifiedId'],
                'fTarget'      => $record['fTarget'],
                'fTargetId'    => $record['fTargetId'],
                'fSales'       => $record['fSales'],
                'fDetail'      => json_encode($fDetail),
            ];
        }

        //異動說明
        $fMemo = $this->getPayByCaseMemo($record, $feedback_case, $fKind, $fStatus);

        $insert_data = array_merge($insert_data, [
            'fKind'    => $fKind,
            'fStatus'  => $fStatus,
            'fDetail2' => json_encode($feedback_case),
            'fMemo'    => implode('、', $fMemo),
        ]);

        $sql = "INSERT INTO tFeedBackMoneyPayByCaseLog " .
        "(" . implode(',', array_keys($insert_data)) . ",fLogCreated_at) VALUES (:" . implode(',:', array_keys($insert_data)) . ",NOW())";
        return $this->conn->exeSql($sql, $insert_data);
    }

    //更新業務再確認資料(回饋金隨案付款Log)
    public function updatePayByCaseLog($fCertifiedId)
    {
        $sql_check = 'SELECT fId FROM tFeedBackMoneyPayByCaseLog WHERE fCertifiedId = :cId AND fStatus = :status AND fKind = :kind AND fSalesConfirmId is null ;';
        $rs_check  = $this->conn->one($sql_check, [
            'cId'    => $fCertifiedId,
            'status' => 1,
            'kind'   => 2,
        ]);

        if (! empty($rs_check) && isset($rs_check['fId'])) {
            $feedback_case = $this->getFeedbackCaseLog($fCertifiedId);

            $sql_update = '
                    UPDATE
                        tFeedBackMoneyPayByCaseLog
                    SET
                        fDetail2 = :fDetail2
                    WHERE
                        fId = :fId ;
                    ';

            $this->conn->exeSql($sql_update, [
                'fDetail2' => json_encode($feedback_case, JSON_UNESCAPED_UNICODE),
                'fId'      => $rs_check['fId'],
            ]);
        }

        return true;
    }

    public function getPayByCaseMemo($record, $feedback_case, $fKind, $fStatus)
    {
        //異動說明
        $fMemo             = [];
        $oldTarget         = '';
        $newTarget         = '';
        $oldOtherScrivener = '';
        $newOtherScrivener = '';
        $fDetail           = json_decode($record['fDetail'], true);

        foreach ($fDetail['case'] as $v) {
            if ($v['cOtherFeedBack'] == '1') {
                $oldOtherScrivener .= $v['cScrivener'] . ',';
            }
            $oldTarget .= $v['cFeedbackTarget'];
        }

        $branchNum = [];
        foreach ($feedback_case['case'] as $v) {
            if ($v['cBranchNum'] > 0) {$branchNum[] = $v['cBranchName'];}
            if ($v['cOtherFeedBack'] == '1') {
                $newOtherScrivener .= $v['cScrivener'] . ',';
            }
            $newTarget .= $v['cFeedbackTarget'];
        }

        if ($oldTarget != $newTarget) {
            $scrivenerData = [];
            $sql           = 'SELECT
                    a.sId, a.sName
                FROM
                    tScrivener AS a
                WHERE
                    a.sId = ' . $fDetail['cScrivener'] . ' ;';
            $rs = $this->conn->all($sql);
            foreach ($rs as $k => $v) {
                $scrivenerData[$v['sId']] = $v['sName'];
            }

            $fMemo[] = '回饋對象異動(' . $scrivenerData[$fDetail['cScrivener']] . '=>' . implode(',', $branchNum) . ')';
        }

        if ($fDetail['total'] != $feedback_case['total']) {
            $fMemo[] = '回饋金額異動(' . $fDetail['total'] . '=>' . (int) $feedback_case['total'] . ')';
        }

        if ($oldOtherScrivener != $newOtherScrivener) {
            $fMemo[] = '其他回饋對象異動(' . $oldOtherScrivener . '=>' . $newOtherScrivener . ')';
        }

        return $fMemo;
    }

    //更新業務再審核確認時間
    public function updateSalesConfirmTimeLog($certifiedId, $fId, $target = "S")
    {
        $sql = 'UPDATE
                    tFeedBackMoneyPayByCaseLog
                SET
                    fSalesConfirmDate = NOW(),
                    fSalesConfirmId = :salesConfirmId
                WHERE
                    fId = :fId
                    AND fCertifiedId = :fCertifiedId
                    AND fTarget = :target;';

        $pattern = [
            'fId'            => $fId,
            'fCertifiedId'   => $certifiedId,
            'target'         => $target,
            'salesConfirmId' => $_SESSION['member_id'],
        ];
        $res = $this->conn->exeSql($sql, $pattern);

        $this->_writeLogCase($certifiedId, json_encode($pattern), '隨案回饋金再確認(Log)');
        return $res;
    }

    //刪除業務異動資料
    public function deletePayByCaseLog($certifiedId, $fKind)
    {
        $sql = 'UPDATE
                    tFeedBackMoneyPayByCaseLog
                SET
                    fStatus = 0
                WHERE
                    fCertifiedId = :fCertifiedId AND fKind = :fKind;';

        $pattern = [
            'fCertifiedId' => $certifiedId,
            'fKind'        => $fKind,
        ];
        $res = $this->conn->exeSql($sql, $pattern);

        return $res;
    }

    //取得隨案異動需再確認之紀錄
    public function getPayByCaseLogNotConfirm($certifiedId, $target = "S")
    {
        $sql = 'SELECT
                    a.fCertifiedId,
                    a.fSales,
                    a.fSalesConfirmDate,
                    a.fAccountant,
                    a.fAccountantConfirmDate,
                    a.fTax,
                    a.fNHI,
                    a.fReceipt,
                    a.fAccountantClose,
                    a.fNHIpay,
                    a.fDetail,
                    a.fCreator
                FROM
                    tFeedBackMoneyPayByCaseLog AS a
                WHERE
                    a.fCertifiedId = :cId
                    AND a.fTarget = :target
                    AND a.fStatus = :status
                    AND a.fKind = :kind;';
        $case = $this->conn->one($sql, ['cId' => $certifiedId, 'target' => $target, 'status' => 1, 'kind' => 2]);
        if (empty($case)) {
            return [];
        }

        $case['detail'] = json_decode($case['fDetail'], true);
        return $case;
    }

    //取得隨案異動再確認的回饋內容
    public function getFeedbackCaseLog($cId)
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

        $case = $rs;
        $list = [];

        if (! empty($case['cBranchNum']) && $case['cCaseFeedback'] == "1") {
            $cBranchName = $this->getBranchName($case['cBranchNum']);
            $list[]      = [
                'cBranchNum'         => $case['cBranchNum'],
                'cFeedbackTarget'    => $case['cFeedbackTarget'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
                'cBranchName'        => $cBranchName,
            ];
        }

        if (! empty($case['cBranchNum1']) && $case['cCaseFeedback1'] == "1") {
            $cBranchName = $this->getBranchName($case['cBranchNum1']);
            $list[]      = [
                'cBranchNum'         => $case['cBranchNum1'],
                'cFeedbackTarget'    => $case['cFeedbackTarget1'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney1'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
                'cBranchName'        => $cBranchName,
            ];
        }

        if (! empty($case['cBranchNum2']) && $case['cCaseFeedback2'] == "1") {
            $cBranchName = $this->getBranchName($case['cBranchNum2']);
            $list[]      = [
                'cBranchNum'         => $case['cBranchNum2'],
                'cFeedbackTarget'    => $case['cFeedbackTarget2'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney2'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
                'cBranchName'        => $cBranchName,
            ];
        }

        if (! empty($case['cBranchNum3']) && $case['cCaseFeedback3'] == "1") {
            $cBranchName = $this->getBranchName($case['cBranchNum3']);
            $list[]      = [
                'cBranchNum'         => $case['cBranchNum3'],
                'cFeedbackTarget'    => $case['cFeedbackTarget3'],
                'cCaseFeedBackMoney' => $case['cCaseFeedBackMoney3'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
                'cBranchName'        => $cBranchName,
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
                'cFeedbackTarget'    => '1', //回饋對象已異動成 代書變仲介
                'cCaseFeedBackMoney' => $case['cSpCaseFeedBackMoney'],
                'cScrivener'         => $case['cScrivener'],
                'scrivener'          => $case['scrivener'],
                'sales'              => $this->getScrivenerSales($case['cScrivener']),
            ];

        }

        $review_case   = $this->getOtherFeedbackCase($cId); //取得其他回饋金資訊
        $feedback_case = $list;                             //取得案件回饋金資訊

        $cases = array_merge($review_case, $feedback_case);

        $detail = [];
        foreach ($cases as $case) {
            unset($case['sales']);
            $detail[] = $case;
        }

        $case = [
            'case'       => $detail,
            'scrivener'  => $cases[0]['scrivener'],
            'cScrivener' => $cases[0]['cScrivener'],
            'total'      => $this->calculateFeedbackMoney($feedback_case, $review_case),
            'sales'      => $cases[0]['sales'],
        ];

        return $case;
    }

    //取得仲介店名
    private function getBranchName($cBranchNum)
    {
        $sql = 'SELECT bId,bStore FROM tBranch WHERE bId = :cBranchNum';

        $rs = $this->conn->one($sql, ['cBranchNum' => $cBranchNum]);
        if (empty($rs)) {
            return '';
        } else {
            return $rs['bStore'];
        }
    }
    ##

    private function _writeLog($cId, $old, $new)
    {
        $txt = "===========================\r\n";
        $txt .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
        $txt .= "Cid: " . $cId . "\r\n";
        $txt .= "Old: " . $old . "\r\n";
        $txt .= "New: " . $new . "\r\n";
        $txt .= "===========================\r\n";

        $path = dirname(dirname(__DIR__)) . '/log/paybycase/compare';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fw = fopen($path . '/' . date("Ymd") . '.log', 'a+');
        fwrite($fw, $txt . "\r\n");
        fclose($fw);
    }

    private function _writeLogCase($cId, $pattern, $reason)
    {
        $txt = "===========================\r\n";
        $txt .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
        $txt .= "Cid: " . $cId . "\r\n";
        $txt .= "Reason: " . $reason . "\r\n";

        $txt .= "Pattern: " . $pattern . "\r\n";
        $txt .= "===========================\r\n";

        $path = dirname(dirname(__DIR__)) . '/log/paybycase';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fw = fopen($path . '/' . date("Ymd") . '.log', 'a+');
        fwrite($fw, $txt . "\r\n");
        fclose($fw);
    }

    public function writeLog($id, $pattern, $reason)
    {
        $txt = "===========================\r\n";
        $txt .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
        $txt .= "Id: " . $id . "\r\n";
        $txt .= "Reason: " . $reason . "\r\n";

        $txt .= "Pattern: " . $pattern . "\r\n";
        $txt .= "===========================\r\n";

        $path = dirname(dirname(__DIR__)) . '/log/paybycase';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fw = fopen($path . '/' . date("Ymd") . '.log', 'a+');
        fwrite($fw, $txt . "\r\n");
        fclose($fw);
    }

    private function _writeLogConfirmList($cId, $pattern, $reason)
    {
        $txt = "===========================\r\n";
        $txt .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
        $txt .= "Cid: " . $cId . "\r\n";
        $txt .= "Reason: " . $reason . "\r\n";
        $txt .= "Pattern: " . $pattern . "\r\n";
        $txt .= "===========================\r\n";

        $path = dirname(dirname(__DIR__)) . '/log/paybycase/confirmList';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fw = fopen($path . '/' . date("Ymd") . '.log', 'a+');
        fwrite($fw, $txt . "\r\n");
        fclose($fw);
    }
}
