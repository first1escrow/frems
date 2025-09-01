<?php
namespace First1\V1\PayByCase;

require_once dirname(dirname(__DIR__)) . '/first1DB.php';

use \first1DB;

class PayByCaseInfo
{
    private $conn;

    public function __construct(first1DB $conn)
    {
        $this->conn = $conn;
    }

    //取得案件相關資訊
    public function getCaseOtherInfo($cId, $feedback_money = 0)
    {
        $sql = 'SELECT
                (SELECT CONCAT((SELECT bName FROM tBrand WHERE bId = a.cBrand),bStore) FROM tBranch WHERE bId = a.cBranchNum)  AS realty1,
                (SELECT CONCAT((SELECT bName FROM tBrand WHERE bId = a.cBrand1),bStore) FROM tBranch WHERE bId = a.cBranchNum1) AS realty2,
                (SELECT CONCAT((SELECT bName FROM tBrand WHERE bId = a.cBrand2),bStore) FROM tBranch WHERE bId = a.cBranchNum2) AS realty3,
                (SELECT CONCAT((SELECT bName FROM tBrand WHERE bId = a.cBrand3),bStore) FROM tBranch WHERE bId = a.cBranchNum3) AS realty4,
                b.cTotalMoney,
                b.cCertifiedMoney
            FROM
                tContractRealestate AS a
            JOIN
                tContractIncome AS b ON a.cCertifyId = b.cCertifiedId
            WHERE
                a.cCertifyId = :cId;';
        $rs = $this->conn->one($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return [];
        }

        $cTotalMoney     = empty($rs['cTotalMoney']) ? 0 : $rs['cTotalMoney'];
        $cCertifiedMoney = empty($rs['cCertifiedMoney']) ? 0 : $rs['cCertifiedMoney'];

        $official_certified_money = $cTotalMoney * 0.0006; //萬分之六履保費
        $deficiency               = (($cCertifiedMoney + 10) < $official_certified_money) ? 'Y' : 'N'; //Y = 不足、N = 有收足

        $case = [
            'cTotalMoney'     => $cTotalMoney,
            'cCertifiedMoney' => $cCertifiedMoney,
            'ratio'           => round(($feedback_money / $cCertifiedMoney * 100), 1),
            'deficiency'      => $deficiency,
        ];

        $this->verifyRealty($case, $rs['realty1']);
        $this->verifyRealty($case, $rs['realty2']);
        $this->verifyRealty($case, $rs['realty3']);
        $this->verifyRealty($case, $rs['realty4']);

        return $case;
    }
    ##

    //確認仲介店名是否正確
    private function verifyRealty(&$case, $realty)
    {
        if (!empty($realty)) {
            $realty           = preg_match("/非仲介成交/iu", $realty) ? '非仲介成交' : $realty;
            $case['realty'][] = $realty;
        }

        $realty = null;unset($realty);
    }
    ##

    public function getFeedbackTotal($cId)
    {
        $sql = 'SELECT
                cCaseFeedback,
                cCaseFeedback1,
                cCaseFeedback2,
                cCaseFeedback3,
                cCaseFeedBackMoney,
                cCaseFeedBackMoney1,
                cCaseFeedBackMoney2,
                cCaseFeedBackMoney3,
                cSpCaseFeedBackMoney
            FROM
                tContractCase AS c
            WHERE
                c.cCertifiedId = :cId;';
        $rs = $this->conn->one($sql, ['cId' => $cId]);

        if (empty($rs)) {
            return false;
        }
        $total = 0; //總回饋金額
        if($rs['cCaseFeedback'] == 0) $total = $total + $rs['cCaseFeedBackMoney']; //仲介1
        if($rs['cCaseFeedback1'] == 0) $total = $total + $rs['cCaseFeedBackMoney1']; //仲介2
        if($rs['cCaseFeedback2'] == 0) $total = $total + $rs['cCaseFeedBackMoney2']; //仲介3
        if($rs['cCaseFeedback3'] == 0) $total = $total + $rs['cCaseFeedBackMoney3']; //仲介4
        $total = $total + $rs['cSpCaseFeedBackMoney']; //地政士特殊回饋
        //其他回饋對象
        $sql = 'SELECT SUM(fMoney) AS totalMoney FROM `tFeedBackMoney` WHERE fCertifiedId = :cId AND fDelete = 0';
        $feedBackMoney = $this->conn->one($sql, ['cId' => $cId]);
        $total = $total + $feedBackMoney['totalMoney'];

        return $total;
    }

}
