<?php
/**
 * SmsTarget Trait
 */
trait SmsTarget
{

    //取出地政士簡訊接收對象
    public function getsScrivenerMobile($cId, $sId)
    {
        if (empty($this->conn)) {
            throw new Exception('DB Connection is empty.');
        }

        $cId = substr($cId, -9);

        //取得案件須發送簡訊對象
        $sql = 'SELECT
                    `cId`,
                    `cCertifiedId`,
                    `cScrivener`,
                    `cSmsTarget`,
                    `cSmsTargetName`,
                    `cAssistant`,
                    `cBankAccount`,
                    `cZip`,
                    `cAddress`
                FROM
                    tContractScrivener
                WHERE
                    cScrivener = :sId
                    AND cCertifiedId = :pid;';
        $data = $this->conn->one($sql, ['sId' => $sId, 'pid' => $cId]);

        $mobiles = explode(",", $data['cSmsTarget']);
        if ($data['cSmsTargetName']) {
            $names = explode(',', $data['cSmsTargetName']);
        }

        //取得地政士預設簡訊接收對象
        $sql = '';
        if (count($mobiles) > 0) {
            $sql = ' AND a.sMobile IN ("' . implode('","', $mobiles) . '") ';
        }

        $sql = 'SELECT
                    a.sName as mName,
                    a.sMobile as mMobile,
                    c.tTitle,
                    b.sSmsLocationMark AS boss
        	    FROM
        		    tScrivenerSms AS a
        	    INNER JOIN
        		    tScrivener AS b ON a.sScrivener = b.sId
        	    INNER JOIN
        		    tTitle_SMS AS c ON a.sNID = c.id
        	    WHERE
        		    (
                        (
                            a.sScrivener = "' . $sId . '"
                            AND a.sCheck_id = ""
                        )
                        OR a.sCheck_id = "' . $cId . '"
                    )
                    AND a.sDel = 0
                    AND a.sLock = 0
                    ' . $sql . ';';
        $rs = $this->conn->all($sql);

        if (empty($rs)) {
            return [];
        }

        $data = [];
        for ($i = 0; $i < count($rs); $i++) {
            for ($j = 0; $j < count($mobiles); $j++) {
                if ($rs[$i]['mMobile'] == $mobiles[$j]) {
                    $data[] = $rs[$i];
                }
            }

        }

        return $data;
    }

    /**
     * 取得案件相關的仲介店資訊
     * @param array &$data 案件資料
     */
    private function getBranchs(&$data)
    {
        //仲介店編號集合
        $realstates = array_column($data, 'branchNum');

        $sql = 'SELECT bId, bBrand, bName, bStore, (SELECT bName FROM tBrand WHERE a.bBrand = bId) as brand, (SELECT bCode FROM tBrand WHERE a.bBrand = bId) as code FROM tBranch AS a WHERE bId IN ("' . implode('","', $realstates) . '");';
        $rs  = $this->conn->all($sql);

        if (empty($rs)) {
            return $data;
        }

        //第一間仲介店
        if (! empty($data['0'])) {
            foreach ($rs as $v) {
                if ($v['bId'] == $data['0']['branchNum']) {
                    $data['0']['branchData'] = $v;
                }
            }
        }

        //第二間仲介店
        if (! empty($data['1'])) {
            foreach ($rs as $v) {
                if ($v['bId'] == $data['1']['branchNum']) {
                    $data['1']['branchData'] = $v;
                }
            }
        }

        //第三間仲介店
        if (! empty($data['2'])) {
            foreach ($rs as $v) {
                if ($v['bId'] == $data['2']['branchNum']) {
                    $data['2']['branchData'] = $v;
                }
            }
        }

        //第四間仲介店
        if (! empty($data['3'])) {
            foreach ($rs as $v) {
                if ($v['bId'] == $data['3']['branchNum']) {
                    $data['3']['branchData'] = $v;
                }
            }
        }

        return $data;
    }

    /**
     * 取得案件仲介店資訊
     * @param string $cId 案件編號
     * @return array 仲介店資訊
     */
    private function getRealestate($cId, $titleTarget = [])
    {
        $cId = substr($cId, -9);

        $sql = 'SELECT
                    `cId`,
                    `cCertifyId`,
                    `cBranchNum`,
                    `cBranchNum1`,
                    `cBranchNum2`,
                    `cBranchNum3`,
                    `cServiceTarget`,
                    `cServiceTarget1`,
                    `cServiceTarget2`,
                    `cServiceTarget3`,
                    `cSmsTarget`,
                    `cSmsTarget1`,
                    `cSmsTarget2`,
                    `cSmsTarget3`
                FROM
                    tContractRealestate
                WHERE
                    cCertifyId = :pid;';
        $rs = $this->conn->one($sql, ['pid' => $cId]);

        if (empty($rs)) {
            return [];
        }

        $data = [];
        if (! empty($rs['cBranchNum'])) {
            //對象為賣方:2，顯示為owner，否則為buyer(買賣方:1、買方:3)
            $target = ($rs['cServiceTarget'] == 2) ? 'owner' : 'buyer';

            $data['0'] = [
                'branchNum' => $rs['cBranchNum'],
                'target'    => $rs['cServiceTarget'],
                'targetTo'  => $target,
                'smsTarget' => empty($rs['cSmsTarget']) ? [] : explode(',', $rs['cSmsTarget']),
            ];
        }

        if (! empty($rs['cBranchNum1'])) {
            //對象為賣方:2，顯示為owner，否則為buyer(買賣方:1、買方:3)
            $target = ($rs['cServiceTarget1'] == 2) ? 'owner' : 'buyer';

            $data['1'] = [
                'branchNum' => $rs['cBranchNum1'],
                'target'    => $rs['cServiceTarget1'],
                'targetTo'  => $target,
                'smsTarget' => empty($rs['cSmsTarget1']) ? [] : explode(',', $rs['cSmsTarget1']),
            ];
        }

        if (! empty($rs['cBranchNum2'])) {
            //對象為賣方:2，顯示為owner，否則為buyer(買賣方:1、買方:3)
            $target = ($rs['cServiceTarget2'] == 2) ? 'owner' : 'buyer';

            $data['2'] = [
                'branchNum' => $rs['cBranchNum2'],
                'target'    => $rs['cServiceTarget2'],
                'targetTo'  => $target,
                'smsTarget' => empty($rs['cSmsTarget2']) ? [] : explode(',', $rs['cSmsTarget2']),
            ];
        }

        if (! empty($rs['cBranchNum3'])) {
            //對象為賣方:2，顯示為owner，否則為buyer(買賣方:1、買方:3)
            $target = ($rs['cServiceTarget3'] == 2) ? 'owner' : 'buyer';

            $data['3'] = [
                'branchNum' => $rs['cBranchNum3'],
                'target'    => $rs['cServiceTarget3'],
                'targetTo'  => $target,
                'smsTarget' => empty($rs['cSmsTarget3']) ? [] : explode(',', $rs['cSmsTarget3']),
            ];
        }

        // $this->getBranchs($data);
        $this->getBranchMobileDetail($data, $titleTarget);

        //店長要再額外取得仲介簡訊發送對象(判斷是否為該案額外新增的簡訊對象(CertifyId))
        if (! empty($titleTarget) && in_array('店長', $titleTarget)) {
            $this->getExtraBranchMobile($data, $cId);
        }

        return $this->divideBranch($data);
    }

    /**
     * 分類案件的仲介店
     * @param array $data 案件資料
     * @return array 分類案件的仲介店
     */
    private function divideBranch($data)
    {
        $branchs = [];
        foreach ($data as $k => $v) {
            if ($v['targetTo'] == 'owner') {
                $branchs['owner'][] = $v;
            } else {
                $branchs['buyer'][] = $v;
            }
        }

        return $branchs;
    }

    /**
     * 取得案件額外定義的簡訊通知對象明細
     * @param array $data 案件資料
     * @param string $cId 案件編號
     * @return array 額外定義的簡訊通知對象明細
     */
    private function getExtraBranchMobile(&$data, $cId)
    {
        foreach ($data as $k => $v) {
            foreach ($v['smsTarget'] as $mobile => $target) {
                $extra = [];
                if ($target['tTitle'] == '店長') {
                    $extra = $this->getExtraBranchMobileDetail($target['bId'], $cId, $mobile);
                }

                if (! empty($extra)) {
                    $data[$k]['smsTarget'][$extra['mMobile']] = $extra;
                }
            }
        }

        return $data;
    }

    /**
     * 取得指定店家的案件額外定義的簡訊通知對象明細
     * @param string $bId 仲介店編號
     * @param string $cId 案件編號
     * @param string $mobile 簡訊號碼
     * @return array 額外定義的簡訊通知對象明細
     */
    private function getExtraBranchMobileDetail($bId, $cId, $mobile)
    {
        $sql = 'SELECT
                    a.bName as mName,
                    a.bMobile as mMobile,
                    b.tTitle,
                    (SELECT CONCAT((Select bName From `tBrand` c Where c.bId = bBrand ),bStore) FROM tBranch WHERE bId = a.bBranch) AS storeName,
                    (SELECT bSmsText FROM tBranch WHERE bId = a.bBranch) AS smsText,
                    (SELECT bSmsTextStyle FROM tBranch WHERE bId = a.bBranch) AS smsTextStyle
                FROM
                    tBranchSms AS a
                JOIN
                    tTitle_SMS AS b ON b.id=a.bNID
                WHERE
                    a.bDel = 0
                    AND a.bBranch="' . $bId . '"
                    AND b.tCheck = 1
                    AND a.bCheck_id ="' . $cId . '";';
        $rs = $this->conn->all($sql);

        if (empty($rs)) {
            return [];
        }

        $data = [];
        foreach ($rs as $v) {
            if ($v['mMobile'] == $mobile) {
                $data = [
                    'bId'          => $bId,
                    'mName'        => $v['mName'],
                    'mMobile'      => $v['mMobile'],
                    'tTitle'       => $v['tTitle'],
                    'storeName'    => $v['storeName'],
                    'smsText'      => $v['smsText'],
                    'smsTextStyle' => $v['smsTextStyle'],
                ];

                if (in_array($v['tTitle'], ['店長', '店東'])) {
                    $data['boss'] = 1;
                }
            }
        }

        return $data;
    }
    /**
     * 取得仲介店電話對應職稱與簡訊樣式
     * @param array $data 案件資料
     * @return array 包含仲介店電話對應職稱與簡訊樣式的資訊
     */
    private function getBranchMobileDetail(&$data, $titleTarget = [])
    {
        foreach ($data as $k => $v) {
            $smsTarget = $v['smsTarget'];
            if (empty($smsTarget)) {
                continue;
            }

            //指定職稱對象
            $sql = empty($titleTarget) ? '' : ' AND b.tTitle IN ("' . implode('","', $titleTarget) . '") ';

            $sql = 'SELECT
                        a.bBranch as bId,
                        a.bName as mName,
                        a.bMobile as mMobile,
                        a.bDefault as mDefault,
                        b.tTitle,
                        (SELECT CONCAT((Select bName From `tBrand` c Where c.bId = bBrand ),bStore) FROM tBranch WHERE bId = a.bBranch) AS storeName,
                        (SELECT bSmsText FROM tBranch WHERE bId = a.bBranch) AS smsText,
                        (SELECT bSmsTextStyle FROM tBranch WHERE bId = a.bBranch) AS smsTextStyle
                    FROM
                        tBranchSms AS a
                    JOIN
                        tTitle_SMS AS b ON b.id = a.bNID
                    WHERE
                        a.bBranch = "' . $v['branchNum'] . '"
                        AND b.tKind = "0"
                        AND a.bDel = 0
                        AND a.bCheck_id = 0
                        AND a.bMobile IN ("' . implode('","', $smsTarget) . '")
                        ' . $sql . ';';
            $rs = $this->conn->all($sql);

            if (empty($rs)) {
                continue;
            }

            $_targets = [];
            foreach ($rs as $d) {
                if (in_array($d['tTitle'], ['店長', '店東'])) {
                    $d['boss'] = 1;
                }

                $_targets[$d['mMobile']] = $d;
            }

            $data[$k]['smsTarget'] = $_targets;
        }

        return $data;
    }

    /**
     * 取出仲介店簡訊接收對象
     * @param array $data 案件資料
     * @param array $titleTarget 職稱對象
     * @return array 仲介店簡訊接收對象
     */
    public function getBranchMobile($cId, $titleTarget = [])
    {
        if (empty($this->conn)) {
            throw new Exception('DB Connection is empty.');
        }

        $cId = substr($cId, -9);

        $realtates = $this->getRealestate($cId, $titleTarget);
        if (empty($realtates)) {
            throw new Exception('tContract realestate data is empty.');
        }

        return $realtates;
    }

    /**
     * 取得合約書的仲介簡訊號碼
     * @param array $data 合約書資料
     * @return array 仲介簡訊號碼
     */
    private function getSmsTarget($data)
    {
        print_r($data);exit;
        for ($i = 0; $i < 4; $i++) {
            $index = '';

            if ($i > 0) {
                $index = $i;
            }

            if (empty($data['cBranchNum' . $index])) {
                continue;
            }

            $smsTarget[$i] = explode(',', $data['cSmsTarget' . $index]); //取出合約書的仲介簡訊號碼
        }

        return $smsTarget;
    }

    /**
     * 取得買賣方電話號碼
     * @param string $cId 合約編號
     * @return array 買賣方電話號碼
     */
    public function getBuyerOwnerMobile($cId)
    {
        $cId = substr($cId, -9);

        list($buyer, $buyerCount) = $this->getBuyerOwner($cId, 'buyer');
        list($owner, $ownerCount) = $this->getBuyerOwner($cId, 'owner');

        return [
            'buyer' => [
                'data'  => $buyer,
                'count' => $buyerCount,
            ],
            'owner' => [
                'data'  => $owner,
                'count' => $ownerCount,
            ],
        ];
    }

    /**
     * 取得買賣方代理人電話號碼
     * @param string $cId 合約編號
     * @return array 買賣方代理人電話號碼
     */
    public function getBuyerOwnerAgentMobile($cId)
    {
        $cId = substr($cId, -9);

        $buyerAgent = $this->getOthersData($cId, 6);
        $ownerAgent = $this->getOthersData($cId, 7);

        return ['buyer' => $buyerAgent, 'owner' => $ownerAgent];
    }

    /**
     * 取得買賣方經紀人電話號碼
     * @param string $cId 合約編號
     * @return array 買賣方經紀人電話號碼
     */
    public function getBuyerOwnerBrokerMobile($cId)
    {
        $cId = substr($cId, -9);

        $buyerBroker = $this->getOtherPhoneNumber($cId, 3);
        $ownerBroker = $this->getOtherPhoneNumber($cId, 4);

        return ['buyer' => $buyerBroker, 'owner' => $ownerBroker];
    }

    /**
     * 取得買賣方電話號碼
     * @param string $cId 合約編號
     * @param string $target 買賣方 (buyer: 買方, owner: 賣方)
     * @return array 買賣方電話號碼
     */
    public function getBuyerOwner($cId, $target)
    {
        $count = 0;

        $table = ($target == 'buyer') ? 'tContractBuyer' : 'tContractOwner';
        $title = ($target == 'buyer') ? '買方' : '賣方';

        $sql   = 'SELECT cName as mName, cMobileNum as mMobile, "' . $target . '" as iden, "' . $title . '" as tTitle FROM ' . $table . ' WHERE cCertifiedId = "' . $cId . '";';
        $users = $this->conn->all($sql);

        if (empty($users)) {
            return [];
        }

        $count++;

        //主買賣方其他電話
        $otherMobiles = $this->getOtherPhoneNumber($cId, ($target == 'buyer') ? 1 : 2);
        if (! empty($otherMobiles)) {
            foreach ($otherMobiles as $v) {
                $users[] = [
                    'mName'   => empty($v['mName']) ? $users[0]['mName'] : $v['mName'],
                    'mMobile' => $v['mMobile'],
                    'iden'    => $target,
                    'tTitle'  => $title,
                ];
            }
        }
        $otherMobiles = null;

        //其他買賣方電話
        $others = $this->getOthersData($cId, ($target == 'buyer') ? 1 : 2);
        if (! empty($others)) {
            foreach ($others as $v) {
                $users[] = [
                    'mName'   => $v['cName'],
                    'mMobile' => $v['cMobileNum'],
                    'iden'    => $target,
                    'tTitle'  => $title,
                ];

                $count++;
            }
        }

        return [$users, $count];
    }

    /**
     * 取得其他買賣方或經紀人資料
     * @param string $cId 合約編號
     * @param string $identity 身分 (1: 其他買方、2: 其他賣方、6: 買方代理人、7: 賣方代理人)
     */
    public function getOthersData($cId, $identity)
    {
        if (empty($cId) || ! preg_match("/^[0-9]{9}$/", $cId)) {
            throw new Exception('CertifiedId is invalid.');
        }

        if (empty($identity) || ! in_array($identity, [1, 2, 6, 7])) {
            throw new Exception('Identity is invalid.');
        }

        $sql = 'SELECT cMobileNum as mMobile, cName as mName FROM tContractOthers WHERE cCertifiedId = :cId AND cIdentity = :identity;';
        return $this->conn->all($sql, ['cId' => $cId, 'identity' => $identity]);
    }

    /**
     * 其他電話號碼
     * @param string $cId 合約編號
     * @param string $identity 身分 (1: 買方、2: 賣方、3: 買方經紀人、4: 賣方經紀人)
     */
    public function getOtherPhoneNumber($cId, $identity)
    {
        if (empty($cId) || ! preg_match("/^[0-9]{9}$/", $cId)) {
            throw new Exception('CertifiedId is invalid.');
        }

        if (empty($identity) || ! in_array($identity, [1, 2, 3, 4])) {
            throw new Exception('Identity is invalid.');
        }

        $sql = 'SELECT cMobileNum as mMobile, cName as mName FROM tContractPhone WHERE cCertifiedId = :cId AND cIdentity = :identity;';
        return $this->conn->all($sql, ['cId' => $cId, 'identity' => $identity]);
    }
}
