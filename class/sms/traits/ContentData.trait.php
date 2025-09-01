<?php
/**
 * contentData Trait
 */
trait ContentData
{

    /**
     * 取得合約資料
     *
     * @param string $cId 合約編號
     * @return array
     */
    public function getContractData($cId)
    {
        if (empty($this->conn)) {
            throw new Exception('DB Connection is empty.');
        }

        $cId = substr($cId, -9);

        $sql = 'SELECT
                    cs.cScrivener,
                    cr.cBranchNum,
                    cr.cBranchNum1,
                    cr.cBranchNum2,
                    cr.cBranchNum3,
                    cr.cServiceTarget,
                    cr.cServiceTarget1,
                    cr.cServiceTarget2,
                    cr.cServiceTarget3,
                    a.cName AS b_name,
                    a.cMobileNum AS b_mobile,
                    a.sAgentName1 AS b_agent_name,
                    a.sAgentMobile1 AS b_agent_mobile,
                    a.sAgentName2 AS b_agent_name2,
                    a.sAgentMobile2 AS b_agent_mobile2,
                    a.sAgentName3 AS b_agent_name3,
                    a.sAgentMobile3 AS b_agent_mobile3,
                    a.sAgentName4 AS b_agent_name4,
                    a.sAgentMobile4 AS b_agent_mobile4,
                    b.cName AS o_name,
                    b.cMobileNum AS o_mobile,
                    b.sAgentName1 AS o_agent_name,
                    b.sAgentMobile1 AS o_agent_mobile,
                    b.sAgentName2 AS o_agent_name2,
                    b.sAgentMobile2 AS o_agent_mobile2,
                    b.sAgentName3 AS o_agent_name3,
                    b.sAgentMobile3 AS o_agent_mobile3,
                    b.sAgentName4 AS o_agent_name4,
                    b.sAgentMobile4 AS o_agent_mobile4
                FROM
                    tContractBuyer AS a
                INNER JOIN
                    tContractOwner AS b ON a.cCertifiedId = b.cCertifiedId
                LEFT JOIN
                    tContractScrivener AS cs ON cs.cCertifiedId=a.cCertifiedId
                LEFT JOIN
                    tContractRealestate AS cr ON cr.cCertifyId =a.cCertifiedId
                WHERE
                    a.cCertifiedId = :cId;';

        return $this->conn->one($sql, ['cId' => $cId]);
    }

    /**
     * 銷帳檔資料
     * @param string $expenseId 銷帳紀錄id
     * @return array 銷帳檔資料
     */
    public function getExpenseData($expenseId)
    {
        if (empty($this->conn)) {
            throw new Exception('DB Connection is empty.');
        }

        $sql = 'SELECT
                    a.eLender,
                    a.eDebit,
                    a.eBuyerMoney,
                    a.eExtraMoney,
                    a.eDepAccount,
                    a.eTradeStatus,
                    b.sName AS _title,
                    a.eRemarkContent,
                    a.eTradeDate,
                    a.eChangeMoney
                FROM
                    tExpense AS a
                INNER JOIN
                    tCategoryIncome AS b ON a.eStatusRemark = b.sId
                WHERE
                    a.eTradeStatus = 0
                    AND a.id = :expenseId;';
        return $this->conn->one($sql, ['expenseId' => $expenseId]);
    }

    /**
     * 取得簡訊內容
     * @param string $target 簡訊目標
     * @param string $expenseId 銷帳紀錄id
     * @param array $contract_data 合約資料
     * @return string 簡訊內容
     */
    public function getContentData($target, $expenseId, $users)
    {
        if ($target == 'income') {
            return $this->incomeContent($expenseId, $users);
        }
        if ($target == 'income2') {
            return $this->incomeContent2($expenseId, $users);
        }
    }

    /**
     * 取得入帳與案件合約等資訊
     * @param string $expenseId 銷帳紀錄id
     * @param array $users 買賣方人數
     * @return array
     */
    public function getExpense($expenseId, $users)
    {
        //取得銷帳檔資料
        $expenseData = $this->getExpenseData($expenseId);

        //取得案件資料
        $cId          = substr($expenseData["eDepAccount"], -9);
        $contractData = $this->getContractData($cId);

        //取得相關資訊
        $buyer  = $contractData["b_name"] . $this->appendUserCount($users['buyer']['count']);
        $seller = $contractData["o_name"] . $this->appendUserCount($users['owner']['count']);

        $memo = $expenseData["_title"] . $expenseData["eRemarkContent"];

        $M = substr($expenseData["eTradeDate"], 3, 2);
        $M = preg_replace("/^0/", "", $M);
        $D = substr($expenseData["eTradeDate"], 5, 2);
        $D = preg_replace("/^0/", "", $D);

        //匯入款總金額
        $money = (int) substr($expenseData['eLender'], 0, -2);

        //調帳後餘額
        $changeMoney = 0;
        if (($money != $expenseData["eChangeMoney"]) && ($expenseData["eChangeMoney"] > 0)) {
            $changeMoney = $expenseData["eChangeMoney"];
        }

        return [
            'cId'          => $cId,
            'expenseData'  => $expenseData,
            'contractData' => $contractData,
            'buyer'        => $buyer,
            'seller'       => $seller,
            'memo'         => $memo,
            'M'            => $M,
            'D'            => $D,
            'money'        => $money,
            'changeMoney'  => $changeMoney,
        ];
    }

    /**
     * 入帳簡訊內容 (income 版)
     * @param int $expenseId 銷帳紀錄id
     * @param array $users 買賣方資訊
     * @return string 簡訊內容
     */
    private function incomeContent($expenseId, $users)
    {
        $expense = $this->getExpense($expenseId, $users);

        $buyerContent = '第一建經信託履約保證專戶已於' . $expense['M'] . '月' . $expense['D'] . '日收到保證編號' . $expense['cId'] . '（買方' . $expense['buyer'] . '賣方' . $expense['seller'] . '）存入' . $expense['memo'];
        $buyerContent .= ($expense['changeMoney'] > 0) ? $expense['changeMoney'] : $expense['money'];
        $buyerContent .= '元';

        $ownerContent = $buyerContent;

        //入帳金額中若有仲介服務費且匯入金額大於仲介服務費時,因要只有賣方簡訊中要扣除服務費,所以把賣方單獨拉出來發送簡訊
        if (((($expense['money'] - $expense['expenseData']['eBuyerMoney']) > 0) && ($expense['expenseData']['eBuyerMoney'] > 0)) || $expense['expenseData']['eExtraMoney'] > 0) {
            $expense['money'] -= $expense['expenseData']['eBuyerMoney'] - $expense['expenseData']['eExtraMoney'];

            //判斷是否有溢入款的文字
            $tmp  = explode('+', $expense['memo']);
            $tmp2 = [];
            for ($i = 0; $i < count($tmp); $i++) {
                $check = true;
                if (preg_match("/^.*溢入款/", $tmp[$i]) || preg_match("/^買方仲介服務費/", $tmp[$i]) || preg_match("/^買方服務費/", $tmp[$i]) || preg_match("/^買方履保費/", $tmp[$i]) || preg_match("/^契稅/", $tmp[$i]) || preg_match("/^印花稅/", $tmp[$i])) {
                    $check = false;
                }

                if ($check) {
                    array_push($tmp2, $tmp[$i]);
                }
            }

            $memo = implode('+', $tmp2);
            unset($tmp, $tmp2);

            $ownerContent = '第一建經信託履約保證專戶已於' . $expense['M'] . '月' . $expense['D'] . '日收到保證編號' . $expense['cId'] . '（買方' . $expense['buyer'] . '賣方' . $expense['seller'] . '）存入' . $expense['memo'];
            if ($expense['changeMoney'] > 0) {
                $expense['changeMoney'] = $expense['changeMoney'] - $expense['expenseData']['eBuyerMoney'] - $expense['expenseData']['eExtraMoney'];
                $ownerContent .= $expense['changeMoney'] . '元';
            } else {
                $ownerContent .= $expense['money'] . '元';
            }
        }

        //入帳金額中若有仲介服務費且匯入金額等於仲介服務費時,則賣方不發送
        if ((($expense['money'] - $expense['expenseData']['eBuyerMoney']) == 0) && ($expense['expenseData']['eBuyerMoney'] > 0)) {
            $ownerContent = '';
        }

        //主管顯示地址部分
        $address = $this->getProperty($expense['cId'], 'all');

        return [
            'buyer'     => [$buyerContent],
            'owner'     => [$ownerContent],
            'address'   => $address,
            'scrivener' => [$buyerContent],
        ];
    }

    /**
     * 入帳簡訊內容 (income2 版)
     * @param int $expenseId 銷帳紀錄id
     * @param array $users 買賣方資訊
     * @return string 簡訊內容
     */
    private function incomeContent2($expenseId, $users)
    {
        $expense = $this->getExpense($expenseId, $users);

        //1110106 不用看調帳以入帳編輯的明細為主 (賣方)
        $memo    = $this->getMemo($expenseId, $expense['expenseData']['eBuyerMoney'], $expense['expenseData']['eExtraMoney'], $expense['money'], $expense['changeMoney']);
        $sms_txt = '第一建經信託履約保證專戶已於' . $expense['M'] . '月' . $expense['D'] . '日收到保證編號' . $expense['cId'] . '（買方' . $expense['buyer'] . '賣方' . $expense['seller'] . '）存入';

        //地政士版
        $memo_scrivener    = empty($memo['normal']) ? [] : $memo['normal'];
        $memo['scrivener'] = [];
        if (! empty($memo_scrivener)) {
            foreach ($memo_scrivener as $v) {
                $memo['scrivener'][] = $sms_txt . $v . '元';
            }
        }

        //正常版
        if (! empty($memo['normal'])) {
            $memoTxt        = implode('+', $memo['normal']);
            $memo['normal'] = [$sms_txt . $expense['money'] . '元（認列:' . $memoTxt . '元)'];
        }

        //賣方版
        if (! empty($memo['status']) && ($memo['status'] == 1)) {
            $memo['owner'] = [];
        } else if (! empty($memo['owner'])) {
            $memoTxt       = implode('+', $memo['owner']);
            $memo['owner'] = [$sms_txt . $memoTxt . '元'];
        }

        //主管顯示地址部分
        $address = $this->getProperty($expense['cId'], 'all');

        return [
            'buyer'     => $memo['normal'],
            'owner'     => $memo['owner'],
            'address'   => $address,
            'scrivener' => $memo['scrivener'],
        ];
    }

    /**
     * 多買賣方人數、修正內容文字
     * @param int $count 買賣方人數
     * @return string 修正後簡訊內容
     */
    private function appendUserCount($count)
    {
        if ($count > 1) {
            return '等' . $count . '人';
        }

        return '';
    }

    /**
     * 取得備註內容
     * @param string $tid 銷帳紀錄id
     * @param int $buyerMoney 買方金額
     * @param int $extraMoney 買方溢入款
     * @param int $total 匯入金額
     * @param int $changeMoney 調帳後餘額
     * @return array 備註內容
     */
    private function getMemo($tid, $buyerMoney, $extraMoney, $total, $changeMoney)
    {
        if ($changeMoney > 0) {
            $total = $changeMoney;
        }

        $msg = ['owner' => [], 'normal' => []];
        $txt = [];

        $sql  = 'SELECT * FROM tExpenseDetailSms WHERE eExpenseId = :tid ORDER BY eId DESC LIMIT 1;';
        $data = $this->conn->one($sql, ['tid' => $tid]);

        /** 賣方簡訊內容 */
        if ($data['eSignMoney'] > 0) {
            $txt[] = "簽約款" . $data['eSignMoney'];
        }

        if ($data['eAffixMoney'] > 0) {
            $txt[] = "用印款" . $data['eAffixMoney'];
        }

        if ($data['eDutyMoney'] > 0) {
            $txt[] = "完稅款" . $data['eDutyMoney'];
        }

        if ($data['eEstimatedMoney'] > 0) {
            $txt[] = "尾款" . $data['eEstimatedMoney'];
        }

        if ($data['eEstimatedMoney2'] > 0) {
            $txt[] = "尾款差額" . $data['eEstimatedMoney2'];
        }

        if ($data['eCompensationMoney'] > 0) {
            $txt[] = "代償後餘額" . $data['eCompensationMoney'];
        }

        if ($data['eExchangeMoney'] > 0) {
            $txt[] = "換約款" . $data['eExchangeMoney'];
        }

        if (! empty($txt) && is_array($txt)) {
            $msg['owner'] = $txt;
        }

        /** 買方簡訊內容 $txt = 賣方項目 + 其他部分*/
        //入帳明細(簡訊)其他項部分
        $sql   = 'SELECT eTitle, eMoney FROM tExpenseDetailSmsOther WHERE eExpenseId = :tid AND eDel = 0;';
        $data2 = $this->conn->all($sql, ['tid' => $tid]);

        $extraIncome = 0;
        foreach ($data2 as $k => $v) {
            $txt[]  = $v['eTitle'] . $v['eMoney']; //normal 顯示項目
            $txtb[] = $v['eTitle'] . $v['eMoney']; //檢視是否有入帳明細項目
            $extraIncome += $v['eMoney'];
        }

        //入帳金額明細(簡訊)賣方顯示項目 + normal 顯示項目
        if ($data['eServiceFee'] > 0) {
            $txt[] = "買方仲介服務費" . $data['eServiceFee'];
        }

        if ($data['eExtraMoney'] > 0) {
            $txt[] = "買方溢入款" . $data['eExtraMoney'];
        }

        // if ($data['eServiceFee'] == 0 && $data['eExtraMoney'] == 0 && ! is_array($txtb)) { //沒有買方仲介服務費跟買方溢入款 賣方不用單獨發送
        //沒有買方仲介服務費跟買方溢入款 賣方不用單獨發送 (看不懂???) 20250526經與佩琦討論後決定拿
        // if ($data['eServiceFee'] == 0 && $data['eExtraMoney'] == 0 && empty($txtb)) {
        //     unset($msg['owner']);
        // }

        $msg['normal'] = '';
        if (! empty($txt) && is_array($txt)) {
            $msg['normal'] = $txt;
        }

        //賣方不發送情況 只匯入買方服務費或溢入款
        $money = $total - $buyerMoney - $extraMoney;
        if ($money == 0 || $extraIncome == $money) {
            $msg['status'] = 1;
        }

        if (is_array($msg)) {
            return $msg;
        }

        return false;
    }

    /**
     * 取得財產地址資訊
     * @param string $cId 合約編號
     * @return array 地政士資料
     */
    private function getProperty($cId, $cat = '')
    {
        //建物地址
        $sql = 'SELECT
                    (SELECT zCity FROM tZipArea AS z WHERE z.zZip=p.cZip) AS bCity,
                    (SELECT zArea FROM tZipArea AS z WHERE z.zZip=p.cZip) AS bArea,
                    p.cAddr AS bAddr,
                    (SELECT zCity FROM tZipArea AS z WHERE z.zZip=l.cZip) AS lCity,
                    (SELECT zArea FROM tZipArea AS z WHERE z.zZip=l.cZip) AS lArea,
                    l.cLand1 AS lAddr
                FROM
                    tContractProperty AS p
                LEFT JOIN
                    tContractLand AS l ON l.cCertifiedId = p.cCertifiedId
                WHERE
                    p.cCertifiedId = "' . $cId . '" ;';
        $rs = $this->conn->one($sql);

        $addr = $rs['lCity'] . $rs['lArea'] . $rs['lAddr'] . '段';
        if ($rs['bAddr'] != '') {
            $addr = $rs['bCity'] . $rs['bArea'] . $rs['bAddr'];

            if ($cat != 'all') {
                $match = [];
                preg_match("/(\D(.*)[路|街|段]{1})?(.*)?/isu", $rs['bAddr'], $match);

                $addr = $rs['bCity'] . $rs['bArea'] . $match[1];
            }
        }

        return $addr;
    }
}
