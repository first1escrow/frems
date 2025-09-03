<?php

require_once __DIR__ . '/advance.class.php';

class Income extends Advance
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetIncomeInfo($id)
    {
        $sql = "
            SELECT
                id, eAccount, eTradeDate,eTradeNum, eBuyerMoney, eExtraMoney, eBankTransId, eCreditTime, eChangeMoney, eTradeCode,
                case eTradeStatus when 1 then CONCAT('-', convert(LEFT( eDebit, 13 ), SIGNED)) else convert(LEFT( eLender, 13 ), SIGNED) end eLender, eDepAccount,
                SUBSTRING(eDepAccount, -9) CertifiedId, ePayTitle,
                (Select sName From tStatusIncome b Where a.eStatusIncome = b.sId) StatusIncome,
                eStatusRemark,  eStatusIncome, eRemarkContent, eRemarkContentSp,
                (SELECT pName FROM  `tPeopleInfo` c Where c.pId = a.eLastEditer) eLastEditer, eLastTime, eExplain,
                case eTradeStatus when 0 then '正常交易' when 1 then '沖正交易' when 9 then '被沖正交易' end eTradeStatusName
            FROM `tExpense` a
            WHERE id = '" . $id . "'  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function SaveIncome($data)
    {
        if ($data['eStatusIncome'] == '3' || $data['eStatusIncome'] == '2') {
            // $money_b = $this->GetBankTransMoney($data['eBankTransId']);
            // $money_e = $this->GetExpenseLender($data['id']);
            // $data['eChangeMoney'] = $money_e - $money_b;
            $sql_ext = " `eBankTransId` = '" . $data['eBankTransId'] . "',
                         `eCreditTime` = now(),
                         `eChangeMoney` = '" . $data['eChangeMoney'] . "', ";
        } else {
            $sql_ext = " `eBankTransId` = '0',
                        `eChangeMoney` = '0',  ";
        }
        $data['eBuyerMoney'] = str_replace(',', '', $data['eBuyerMoney']);
        $data['eExtraMoney'] = str_replace(',', '', $data['eExtraMoney']);
        $sql                 = "UPDATE  `tExpense` SET
                        `eStatusIncome` = '" . $data['eStatusIncome'] . "',
                        " . $sql_ext . "
                        `eStatusRemark` =  '" . $data['eStatusRemark'] . "',
                        `eRemarkContentSp` = '" . $data['eRemarkContentSp'] . "',
                        `eRemarkContent` =  '" . $data['eRemarkContent'] . "',
                        `eExplain` =  '" . $data['eExplain'] . "',
                        `eLastEditer` =  '" . $_SESSION['member_id'] . "',
                        `eBuyerMoney` =  '" . $data['eBuyerMoney'] . "',
                        `eExtraMoney` =  '" . $data['eExtraMoney'] . "',
                        `eLastTime` =  now()
               WHERE  `id` = '" . $data['id'] . "' ;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function AddIncomeSms($data)
    {
        //$_POST["cCertifiedId"]
        $data['SignMoney']         = str_replace(',', '', $data['SignMoney']);
        $data['AffixMoney']        = str_replace(',', '', $data['AffixMoney']);
        $data['DutyMoney']         = str_replace(',', '', $data['DutyMoney']);
        $data['EstimatedMoney']    = str_replace(',', '', $data['EstimatedMoney']);
        $data['EstimatedMoney2']   = str_replace(',', '', $data['EstimatedMoney2']);
        $data['CompensationMoney'] = str_replace(',', '', $data['CompensationMoney']);
        $data['OtherMoney']        = str_replace(',', '', $data['OtherMoney']);

        $sql = "INSERT INTO
                    tExpenseDetailSms
                    (
                        eCertifiedId,
                        eExpenseId,
                        eSignMoney,
                        eAffixMoney,
                        eDutyMoney,
                        eEstimatedMoney,
                        eEstimatedMoney2,
                        eCompensationMoney,
                        eServiceFee,
                        eExtraMoney,
                        eExchangeMoney
                    )
                    VALUES
                    (
                        '" . $data['cCertifiedId'] . "',
                        '" . $data['id'] . "',
                        '" . $data['SignMoney'] . "',
                        '" . $data['AffixMoney'] . "',
                        '" . $data['DutyMoney'] . "',
                        '" . $data['EstimatedMoney'] . "',
                        '" . $data['EstimatedMoney2'] . "',
                        '" . $data['CompensationMoney'] . "',
                        '" . $data['ServiceFee'] . "',
                        '" . $data['ExtraMoney'] . "',
                        '" . $data['ExchangeMoney'] . "'
                    )";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function SaveIncomeSms($data)
    {
        $data['SignMoney']         = str_replace(',', '', $data['SignMoney']);
        $data['AffixMoney']        = str_replace(',', '', $data['AffixMoney']);
        $data['DutyMoney']         = str_replace(',', '', $data['DutyMoney']);
        $data['EstimatedMoney']    = str_replace(',', '', $data['EstimatedMoney']);
        $data['EstimatedMoney2']   = str_replace(',', '', $data['EstimatedMoney2']);
        $data['CompensationMoney'] = str_replace(',', '', $data['CompensationMoney']);
        $data['OtherMoney']        = str_replace(',', '', $data['OtherMoney']);

        $sql = "UPDATE
                    tExpenseDetailSms
                SET
                    eSignMoney = '" . $data['SignMoney'] . "',
                    eAffixMoney = '" . $data['AffixMoney'] . "',
                    eDutyMoney = '" . $data['DutyMoney'] . "',
                    eEstimatedMoney = '" . $data['EstimatedMoney'] . "',
                    eEstimatedMoney2 = '" . $data['EstimatedMoney2'] . "',
                    eCompensationMoney = '" . $data['CompensationMoney'] . "',
                    eServiceFee = '" . $data['ServiceFee'] . "',
                    eExtraMoney = '" . $data['ExtraMoney'] . "',
                    eExchangeMoney = '" . $data['ExchangeMoney'] . "'

                WHERE

                    eId = '" . $data['edsId'] . "'
                ";

        // echo $sql;
        //     eOtherTitle = '".$data['OtherTitle']."',
        //     eOtherMoney = '".$data['OtherMoney']."'
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
//        if ($data['ExtraMoney'] != 0) {
//            $this->SaveExpenseDetail($data, 8, $data['ExtraMoney']);
//        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //
    //檢查是否有出款過
    public function checkExpenseDetailALL($data)
    {
        $sql = "SELECT * FROM tExpenseDetail WHERE eExpenseId = '" . $data['id'] . "' AND eOK !=''";
        // echo $sql."\r\n";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $dataED = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataED['eId']) { //有出過款
            return true;
        } else {
            return false;
        }
    }

    public function SaveExpenseDetail($data, $cat, $money, $id = '')
    {
        //tExpenseDetail 6

        //溢入款 6
        // print_r($data);
        // $data['id'] = '66120';
        if (! $this->checkExpenseDetailALL($data)) {
            $sql = "SELECT * FROM tExpenseDetail WHERE eExpenseId = '" . $data['id'] . "' AND eItem = '" . $cat . "' AND eTarget = 3";
            // echo $sql."\r\n";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $dataED = $stmt->fetch(PDO::FETCH_ASSOC);
            // print_r($data);
            // die;

            if ($dataED['eId']) { //已寫入過
                $sql = 'UPDATE tExpenseDetail SET  eMoney="' . $money . '" WHERE eOtherId = "' . $id . '" AND eOK = "" AND eId ="' . $dataED['eId'] . '" AND eItem = "' . $cat . '" AND eTarget = 3 AND eCertifiedId="' . $data['cCertifiedId'] . '";';
                // echo $sql."\r\n";
            } else {
                $code = ($data['cBank'] == 68) ? '03' : '';

                $sql = 'INSERT INTO tExpenseDetail (eCertifiedId, eExpenseId, eTarget, eItem, eMoney,eOtherId,eObjKind2 ) VALUES ("' . $data['cCertifiedId'] . '", "' . $data['id'] . '", "3", "' . $cat . '", "' . $money . '","' . $id . '","' . $code . '");';
                // echo $sql."\r\n";
            }
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

    }

    public function checkExpenseDetail($id)
    {
        $sql = "SELECT * FROM tExpenseDetail WHERE eOtherId = '" . $data['id'] . "'";
        // echo $sql."\r\n";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $dataED = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataED['eId']) { //有寫入過
            return true;
        } else {
            return false;
        }
    }

    public function SaveExpenseDetail_v2($data)
    {
        //tExpenseDetail 6

        //溢入款 6
        // print_r($data);
        // $data['id'] = '66120';
        if (! $this->checkExpenseDetailALL($data)) {
            $sql = "SELECT * FROM tExpenseDetailSmsOther WHERE eExpenseId = '" . $data['id'] . "' AND eDel = 0";
            // echo $sql."\r\n";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $dataEDALL = $stmt->fetchALL(PDO::FETCH_ASSOC);

            for ($i = 0; $i < count($dataEDALL); $i++) {
                if ($dataEDALL[$i]['eTitle'] == '買方履保費') { // 9
                    $cat = 9;
                } elseif ($dataEDALL[$i]['eTitle'] == '買方預收款項') {
                    $cat = 6;
                } elseif ($dataEDALL[$i]['eTitle'] == '契稅') {
                    $cat = 4;
                } elseif ($dataEDALL[$i]['eTitle'] == '印花稅') {
                    $cat = 5;
                }

                if (! $this->checkExpenseDetail($dataEDALL[$i]['eId'])) {

                    $sql = 'UPDATE tExpenseDetail SET eMoney="' . $dataEDALL[$i]['eMoney'] . '" WHERE eOtherId = "' . $dataEDALL[$i]['eId'] . '" AND eOK = "";';
                    // echo $sql."\r\n";

                } else {
                    $code = ($data['cBank'] == 68) ? '03' : '';
                    $sql  = 'INSERT INTO tExpenseDetail (eCertifiedId, eExpenseId, eTarget, eItem, eMoney,eOtherId) VALUES ("' . $data['cCertifiedId'] . '", "' . $data['id'] . '", "3", "' . $cat . '", "' . $dataEDALL[$i]['eMoney'] . '","' . $dataEDALL[$i]['eId'] . '");';
                    // echo $sql."\r\n";
                }
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
            }

        }

    }

    public function AddIncomeSmsOther($data)
    {
        // print_r($data['otherTitle']);
        for ($i = 0; $i < count($data['otherTitle']); $i++) {
            $cat = 0;
            $sql = "INSERT INTO
                        tExpenseDetailSmsOther
                        (
                            eExpenseId,
                            eTitle,
                            eMoney
                        )VALUES(
                            '" . $data['id'] . "',
                            '" . $data['otherTitle'][$i] . "',
                            '" . $data['otherMoney'][$i] . "'
                        ) ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $data['otherId'][$i] = $this->dbh->lastInsertId();

            // $this->SaveExpenseDetail($data,$cat,$data['otherMoney'][$i],$data['otherId'][$i]);

        }
        $this->SaveExpenseDetail_v2($data);
    }

    public function SaveIncomeSmsOther($data)
    {

        for ($i = 0; $i < count($data['otherId']); $i++) {
            $sql = "UPDATE
                    tExpenseDetailSmsOther
                SET
                    eTitle ='" . $data['otherT'][$i] . "',
                    eMoney ='" . $data['otherM'][$i] . "'

                      WHERE
                        eId = '" . $data['otherId'][$i] . "'
                    ";
            // echo $sql."\r\n";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            if ($data['otherT'][$i] == '買方履保費') { // 9
                $cat = 9;
            } elseif ($data['otherT'][$i] == '買方預收款項') {
                $cat = 6;
            } elseif ($data['otherT'][$i] == '契稅') {
                $cat = 4;
            } elseif ($data['otherT'][$i] == '印花稅') {
                $cat = 5;
            }

            $this->SaveExpenseDetail($data, $cat, $data['otherM'][$i]);
        }

    }

    public function GetIncomeSmsOther($id)
    {
        $sql = "SELECT
                    *
                FROM
                    tExpenseDetailSmsOther
                WHERE
                    eDel = 0 AND
                    eExpenseId = '" . $id . "'";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $arr   = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $title = $this->GetIncomeTitle();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i]['menu'] = $title;
            if (! in_array($arr[$i]['eTitle'], $title)) {
                // echo $arr[$i]['eTitle'];
                $arr[$i]['menu'][$arr[$i]['eTitle']] = $arr[$i]['eTitle'];
                // print_r($arr[$i]['menu']);
            }

        }

        return $arr;
    }

    public function GetIncomeSms($cid, $id)
    {
        $sql = "SELECT
                    *
                FROM
                    tExpenseDetailSms
                WHERE
                    eCertifiedId = '" . $cid . "' AND
                    eExpenseId = '" . $id . "'";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function SaveContract($data)
    {
        $sql = "Update `tContractCase` SET
                cCaseProcessing = '" . $data['cCaseProcessing'] . "'
               WHERE  `cCertifiedId` = '" . $data['cCertifiedId'] . "' ;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function GetChangeCreditList($id, $limit)
    {
        $sql = " SELECT tId, concat(right(tVR_Code, 9), '/', tMoney, '元') dshow, tVR_Code, tMoney
                 FROM `tBankTrans`
                 WHERE  right(tVr_Code, 9) = '" . $id . "' AND  tObjKind = '調帳' AND tPayOk = '1' AND tPayTxt = ''  And convert(tMoney, SIGNED) <= '" . $limit . "'
                 ORDER BY tDate; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetBankTransMoney($id)
    {
        $sql  = " SELECT tId, tMoney FROM `tBankTrans` Where tId = '" . $id . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            return null; // 如果查詢結果為空，返回 null
        }
        return $row['tMoney'];
    }

    public function GetExpenseLender($id)
    {
        $sql  = " SELECT id, convert(LEFT( eLender, 13 ), SIGNED) eLender FROM `tExpense` Where id = '" . $id . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            return null; // 如果查詢結果為空，返回 null
        }
        return $row['eLender'];
    }

    public function GetIncomeTitle()
    {

        return [
            ''       => '',
            '買方履保費'  => '買方履保費',
            '買方預收款項' => '買方預收款項',
            '契稅'     => '契稅',
            '印花稅'    => '印花稅',
        ];
    }

}
