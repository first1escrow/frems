<?php
namespace First1\V1\Bank;

require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(__DIR__) . '/util.class.php';

use First1\V1\Util\Util;

class AdjustAccount
{
    private $conn;

    public function __construct()
    {
        $this->conn = new \first1DB;
    }

    //取得案件相關欄位
    public function getCaseDetail($certifiedId)
    {
        return $this->getContractCase($certifiedId);
    }

    //合約相關資訊
    private function getContractCase($certifiedId)
    {
        $sql = 'SELECT
                    a.cCertifiedId,
                    SUBSTRING(a.cSignDate, 1, 10) as signDate,
                    (SELECT sName FROM tStatusCase WHERE sId = a.cCaseStatus) as status,
                    (SELECT pName FROM tPeopleInfo WHERE pId = a.cUndertakerId) as undertaker,
                    (SELECT sName FROM tScrivener WHERE sId = b.cScrivener) as scrivener,
                    (SELECT cName FROM tContractBuyer WHERE cCertifiedId = a.cCertifiedId) as buyer,
                    (SELECT cName FROM tContractOwner WHERE cCertifiedId = a.cCertifiedId) as owner,
                    CONCAT(e.cBankName, e.cBranchName) as bank,
                    a.cBankList
                FROM
                    tContractCase AS a
                JOIN
                    tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId
                JOIN
                    tContractBuyer AS c ON a.cCertifiedId = c.cCertifiedId
                JOIN
                    tContractOwner AS d ON a.cCertifiedId = d.cCertifiedId
                JOIN
                    tContractBank AS e ON a.cBank = e.cBankCode
                WHERE
                    a.cCertifiedId = :cId;';
        $rs = $this->conn->one($sql, ['cId' => $certifiedId]);

        $cCertifyDate       = $this->certifyCaseExportTime($certifiedId, $rs['cBankList']);
        $rs['cCertifyDate'] = empty($cCertifyDate) ? '' : $cCertifyDate;

        $rs['cBankList'] = empty($rs['cBankList']) ? '' : '有';
        $rs['buyer'] .= $this->getOtherBuyer($certifiedId);
        $rs['owner'] .= $this->getOtherOwner($certifiedId);

        return $rs;
    }

    //取得其他買方
    private function getOtherBuyer($certifiedId)
    {
        $rs = $this->getOtherData($certifiedId, 1);
        return empty($rs) ? '' : '等' . (count($rs) + 1) . '人';
    }

    //取得其他賣方
    private function getOtherOwner($certifiedId)
    {
        $rs = $this->getOtherData($certifiedId, 2);
        return empty($rs) ? '' : '等' . (count($rs) + 1) . '人';
    }

    //取得相應身分人士的數量
    private function getOtherData($certifiedId, $identity)
    {
        $sql = 'SELECT cId FROM tContractOthers WHERE cCertifiedId = :cId AND cIdentity = :identity;';
        return $this->conn->all($sql, ['cId' => $certifiedId, 'identity' => $identity]);
    }

    //取得履保費出款日
    private function certifyCaseExportTime($certifiedId, $cBankList = null)
    {
        $sql = 'SELECT SUBSTRING(tExport_time, 1, 10) as exportDate FROM tBankTrans WHERE tKind = "保證費" AND tMemo = :cId;';
        $rs  = $this->conn->one($sql, ['cId' => $certifiedId]);

        return empty($rs) ? $cBankList : $rs['exportDate'];
    }

    //取得開始日期帳戶餘額
    public function accountBalance($account, $date)
    {
        $date = date("Y-m-d", strtotime('-1 day', strtotime($date)));

        $sql = 'SELECT tMoney FROM tBankInterest WHERE tAccount = :account AND tTime = :date;';
        $rs  = $this->conn->one($sql, ['account' => $account, 'date' => $date]);

        return empty($rs) ? 0 : $rs['tMoney'];
    }
    ##

    //取得出款資訊
    public function getPayData($account, $from_date, $to_date)
    {
        $sql = 'SELECT tObjKind, tMoney, tBankLoansDate, tTxt FROM tBankTrans WHERE tVR_Code = :account AND tBankLoansDate >= :start AND tBankLoansDate <= :end ORDER BY tBankLoansDate ASC;';
        return $this->conn->all($sql, ['account' => $account, 'start' => $from_date, 'end' => $to_date]);
    }

    //取得入款資訊
    public function getDepositData($account, $from_date, $to_date)
    {
        $from_date = Util::convertDateToEast($from_date, '-', '');
        $to_date   = Util::convertDateToEast($to_date, '-', '');

        $sql = 'SELECT eTradeDate, eLender FROM tExpense WHERE eDepAccount = :account AND eTradeDate >= :start AND eTradeDate <= :end ORDER BY eTradeDate, eTradeNum ASC;';
        return $this->conn->all($sql, ['account' => '00' . $account, 'start' => $from_date, 'end' => $to_date]);
    }

    //取得入出款資訊
    public function getData($account, $from_date, $to_date)
    {
        $util = new Util;

        $detail = [];

        //出款
        $pay = $this->getPayData($account, $from_date, $to_date);

        foreach ($pay as $v) {
            $matches = [];
            preg_match("/^轉入(\d+)$/iu", $util->Full2Half($v['tTxt']), $matches);

            $case = $this->getCaseDetail(substr($matches[1], 5));

            $detail[] = [
                'date'    => $v['tBankLoansDate'],
                'money'   => $v['tMoney'],
                'income'  => 0,
                'kind'    => '代墊利息',
                'account' => empty($matches) ? '' : $matches[1],
                'case'    => $case,
            ];

            $matches = null;unset($matches);
        }
        ##

        //入款
        $deposit = $this->getDepositData($account, $from_date, $to_date);

        foreach ($deposit as $v) {
            $detail[] = [
                'date'    => implode('-', [(substr($v['eTradeDate'], 0, 3) + 1911), substr($v['eTradeDate'], 3, 2), substr($v['eTradeDate'], 5, 2)]),
                'money'   => 0,
                'income'  => (int) substr($v['eLender'], 0, 13),
                'kind'    => '',
                'account' => '',
                'case'    => '',
            ];
        }
        ##

        //依日期排序
        usort($detail, function ($a, $b) {
            if ($a['date'] == $b['date']) {
                return 0;
            }

            return ($a['date'] < $b['date']) ? -1 : 1;
        });
        ##

        return $detail;
    }
}
