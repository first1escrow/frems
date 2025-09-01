<?php
namespace First1\V1\PayByCase;

require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once __DIR__ . '/payByCase.class.php';

use \first1DB;

class PayByCaseScrivener
{
    private $conn;

    public function __construct(first1DB $conn)
    {
        $this->conn = $conn;
    }

    //依據保證號碼取得地政士維護回饋金資訊
    public function getScrivenerFeedBackBankByCertifiedId($certifiedId)
    {
        $type = 1;

        $sql = 'SELECT
                    a.cCertifiedId,
                    a.cScrivener,
                    b.fIdentity,
                    b.fIdentityNumber,
                    b.fAccountNum,
                    b.fAccountNumB,
                    b.fAccount,
                    b.fAccountName,
                    (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fAccountNum AND bBank4 = "") as bankMain,
                    (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fAccountNum AND bBank4 = b.fAccountNumB) as bankBranch
                FROM
                    tContractScrivener AS a
                LEFT JOIN
                    tFeedBackData AS b ON (a.cScrivener = b.fStoreId AND b.fType = :type AND b.fStatus = 0 AND b.fStop = 0)
                WHERE
                    a.cCertifiedId = :certifiedId;';
        $rs = $this->conn->all($sql, ['type' => $type, 'certifiedId' => $certifiedId]);

        if (empty($rs)) {
            return [];
        }

        foreach ($rs as $k => $v) {
            $rs[$k]['bank'] = $v['fIdentity'] . '_' . $v['fAccountNum'] . '_' . $v['fAccountNumB'] . '_' . $v['fAccount'] . '_' . $v['fAccountName'] . '_' . $v['fIdentityNumber'];
        }

        return $rs;

    }
    ##

    //取得地政士維護回饋金帳戶資訊
    public function getFeedBackBank($sId, $type = 1, $fId = 0)
    {
        $sql = 'SELECT
                    a.fId,
                    a.fIdentity,
                    a.fIdentityNumber,
                    a.fAccountNum,
                    a.fAccountNumB,
                    a.fAccount,
                    a.fAccountName,
                    (SELECT bBank4_name FROM tBank WHERE bBank3 = a.fAccountNum AND bBank4 = "") as bankMain,
                    (SELECT bBank4_name FROM tBank WHERE bBank3 = a.fAccountNum AND bBank4 = a.fAccountNumB) as bankBranch
                FROM
                    tFeedBackData AS a
                WHERE
                    a.fType = :type
                    AND a.fStoreId = :sId
                    AND a.fStatus = 0
                    AND a.fStop = 0 ';
        if (0 != $fId) {
            $sql .= ' AND a.fId = ' . $fId;
        }

        $rs = $this->conn->all($sql, ['type' => $type, 'sId' => $sId]);

        if (empty($rs)) {
            return [];
        }

        foreach ($rs as $k => $v) {
            $rs[$k]['bank'] = $v['fIdentity'] . '_' . $v['fAccountNum'] . '_' . $v['fAccountNumB'] . '_' . $v['fAccount'] . '_' . $v['fAccountName'] . '_' . $v['fIdentityNumber'] . '_' . $v['fId'];
        }

        return $rs;
    }
    ##

    //依據代書代碼取得可能會受影響的案件
    private function getAffectCaseByScrivener($scrivener, $target = "S")
    {
        $sql = 'SELECT
                    a.fCertifiedId AS certifiedId,
                    a.fId AS caseId,
                    b.fId AS bankId
                FROM
                    tFeedBackMoneyPayByCase AS a
                JOIN
                    tFeedBackMoneyPayByCaseAccount AS b ON a.fCertifiedId = b.fCertifiedId AND b.fTarget = :target AND a.fId = b.fPayByCaseId
                WHERE
                    a.fTarget = :target AND a.fTargetId = :scrivener;';
        return $this->conn->all($sql, ['target' => $target, 'scrivener' => $scrivener]);
    }
    ##

    private function getAffectCase($certifiedId, $target = "S")
    {
        $sql = 'SELECT
                    a.fCertifiedId AS certifiedId,
                    a.fId AS caseId,
                    b.fId AS bankId
                FROM
                    tFeedBackMoneyPayByCase AS a
                JOIN
                    tFeedBackMoneyPayByCaseAccount AS b ON a.fCertifiedId = b.fCertifiedId AND b.fTarget = :target AND a.fId = b.fPayByCaseId
                WHERE
                    a.fTarget = :target AND a.fCertifiedId = :certifiedId;';
        return $this->conn->all($sql, ['target' => $target, 'certifiedId' => $certifiedId]);
    }

    //取得所有已出"地政士回饋金"的案件
    private function getTransCaseByScrivener($certifiedIds, $kind = "地政士回饋金")
    {
        $sql = 'SELECT bCertifiedId FROM tBankTransRelay WHERE bCertifiedId IN ("' . implode('","', $certifiedIds) . '") AND bKind = :kind ;';
        return $this->conn->all($sql, ['kind' => $kind]);
    }
    ##

    //比對出未出款的案件
    public function affectCases($scrivener)
    {
        $cases = $this->getAffectCaseByScrivener($scrivener);

        $certifiedIds = array_column($cases, 'certifiedId'); //取出保證號碼
        $trans_cases  = $this->getTransCaseByScrivener($certifiedIds);

        return array_values(                                    //重新排序鍵值
            array_filter($cases, function ($v) use ($trans_cases) { //將不存在tBankTrans的案件挑出來
                $remove_cids = array_column($trans_cases, 'bCertifiedId');

                if (! in_array($v['certifiedId'], $remove_cids)) {
                    return $v;
                }
            })
        );
    }
    ##

    //確認案件是否出款
    public function affectCase($certifiedId)
    {
        $cases          = $this->getAffectCase($certifiedId);
        $certifiedIds[] = $certifiedId;
        $transCases     = $this->getTransCaseByScrivener($certifiedIds);

        return empty($transCases) ? $cases : [];
    }

    //取得PayByCase銀行帳戶資訊
    public function getPayByCaseBankAccount($certifiedId, $type = 'S')
    {
        $sql = 'SELECT
                    fType as identity,
                    fIdentityIdNumber as idNumber,
                    fBankMain as bankMain,
                    fBankBranch as bankBranch,
                    fBankAccount as account,
                    fBankAccountName as accountName
                FROM
                    tFeedBackMoneyPayByCaseAccount
                WHERE
                    fCertifiedId = :certifiedId
                    AND fType = :type;';
        return $this->conn->one($sql, ['certifiedId' => $certifiedId, 'type' => $type]);
    }
    ##

    //修改PayByCase銀行帳號資訊
    public function updateBankAccount($id, $bank)
    {
        $sql = 'UPDATE
                    tFeedBackMoneyPayByCaseAccount
                SET
                    fType = "' . $bank['identity'] . '",
                    fIdentityIdNumber = "' . $bank['idNumber'] . '",
                    fBankMain = "' . $bank['bankMain'] . '",
                    fBankBranch = "' . $bank['bankBranch'] . '",
                    fBankAccount = "' . $bank['account'] . '",
                    fBankAccountName = "' . $bank['accountName'] . '"
                WHERE
                    fId = ' . $id . ';';
        return $this->conn->exeSql($sql);
    }
    ##

    //依據保證號碼修改案件銀行帳號資訊
    public function modifyAffectCaseBankAccountByCase($certifiedId)
    {
        //依據保證號碼取得地政士維護回饋金資訊
        $banks = $this->getScrivenerFeedBackBankByCertifiedId($certifiedId);
        if (empty($banks)) {
            throw new \Exception('Can not find scrivener relative data(cId: ' . $certifiedId . ').');
        }
        ##

        //確認未出款
        $cases = $this->affectCase($certifiedId);
        ##

        //更新銀行資訊
        $this->modifyBankAccount($banks, $cases);
        ##

        //記log
        $certifiedIds = array_column($cases, 'certifiedId');
        $this->_writeLog($banks[0]['cScrivener'], $certifiedIds, $banks, '刪除待修改資料');
    }
    ##

    //依據地政士修改案件銀行帳號資訊
    public function modifyAffectCaseBankAccountByScrivener($sId)
    {
        //亞洲健康城代書不做隨案回饋
        if ($sId == 1182) {
            return false;
        }

        //依據保證號碼取得地政士維護回饋金資訊
        $banks = $this->getFeedBackBank($sId, 1);
        if (empty($banks)) {
            // throw new \Exception('Can not find scrivener relative data(sId: ' . $sId . ').');
            // 戴戴說有可能新建地政士尚未提供回饋金帳號，所以有可能暫時無回饋金資料
            return;
        }
        ##

        //比對出未出款的案件
        $cases = $this->affectCases($sId);
        ##

        //更新銀行資訊
        $this->modifyBankAccount($banks, $cases);
        ##

        $certifiedIds = array_column($cases, 'certifiedId');
        $this->_writeLog($sId, $certifiedIds, $banks, '地政士修改案件銀行帳號資訊');
    }
    ##

    //修改案件銀行帳號資訊
    private function modifyBankAccount($banks, $cases)
    {
        $pay_by_case = new PayByCase;

        //更新銀行資訊
        $bankCount = count($banks);
        if ($bankCount == 1) { //地政士維護裡的回饋金帳戶只有一筆，直接覆蓋
            array_map(function ($v) use ($banks, $pay_by_case) {
                $banks = array_pop($banks); //取出一筆(為防key不對，所以用pop方式取出)
                                            //證件號碼有異動
                $identityNumberChange = 0;
                $bankAccount          = $this->getPayByCaseBankAccount($v['certifiedId']);

                if ($banks['fIdentity'] == 2 and $bankAccount['idNumber'] != $banks['fIdentityNumber']) {
                    $identityNumberChange = 1;
                }

                $this->updateBankAccount($v['bankId'], [
                    'identity'    => $banks['fIdentity'],
                    'bankMain'    => $banks['fAccountNum'],
                    'bankBranch'  => $banks['fAccountNumB'],
                    'account'     => $banks['fAccount'],
                    'accountName' => $banks['fAccountName'],
                    'idNumber'    => $banks['fIdentityNumber'],
                ]);

                $pay_by_case->updatePayTax($v['certifiedId'], $identityNumberChange, 'S'); //更新代扣稅額與補充保費
                                                                                           //通知會計
                $pay_by_case->needAccountingConfirm($v['certifiedId']);
                ##
            }, $cases);
        }

        if ($bankCount > 1) { //地政士維護裡的回饋金帳戶超過一筆以上、刪除帳戶資訊並恢復待確認
            array_map(function ($v) use ($banks, $pay_by_case) {
                if (! $pay_by_case->checkBankLoansDate($v['certifiedId'])) {
                    $pay_by_case->deletePayByCaseAccount($v['certifiedId'], 'S');   //刪除銀行帳戶資料
                    $pay_by_case->removeSalesConfirmRecord($v['certifiedId'], 'S'); //刪除payByCase紀錄
                    $pay_by_case->salesConfirmList($v['certifiedId']);              //新增payByCase紀錄
                }
            }, $cases);
        }
        ##
    }
    ##

    //取得業務已同意PayByCase銀行帳戶資訊
    public function getPayByCaseBankAccountSalesConfirm($certifiedId, $target = "S")
    {
        $sql = 'SELECT
                    CASE b.fType WHEN 1 THEN "未知" WHEN 2 THEN "身份證編號" WHEN 3 THEN "統一編號" WHEN 4 THEN "居留證號碼" END AS identity,
                    b.fIdentityIdNumber AS idNumber,
                    (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fBankMain AND bBank4 = "") AS bankMain,
                    (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fBankMain AND bBank4 = b.fBankBranch) AS bankBranch,
                    b.fBankAccount AS account,
                    b.fBankAccountName AS accountName
                FROM
                    tFeedBackMoneyPayByCase AS a
                JOIN
                    tFeedBackMoneyPayByCaseAccount AS b ON a.fCertifiedId = b.fCertifiedId AND b.fTarget = :target AND a.fId = b.fPayByCaseId
                WHERE
                    a.fCertifiedId = :certifiedId AND a.fSalesConfirmDate IS NOT NULL
                ;';
        return $this->conn->one($sql, ['target' => $target, 'certifiedId' => $certifiedId]);
    }
    ##

    private function _writeLog($storeId, $case, $banks, $reason)
    {
        $txt = "===========================\r\n";
        $txt .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
        $txt .= "StoreId: " . $storeId . "\r\n";
        $txt .= "Case: " . json_encode($case) . "\r\n";
        $txt .= "Banks: " . json_encode($banks) . "\r\n";
        $txt .= "Reason: " . $reason . "\r\n";
        $txt .= "===========================\r\n";

        $path = dirname(dirname(__DIR__)) . '/log/paybycase';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fw = fopen($path . '/' . date("Ymd") . '.log', 'a+');
        fwrite($fw, $txt . "\r\n");
        fclose($fw);
    }
}
