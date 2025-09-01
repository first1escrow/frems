<?php

require_once __DIR__ . '/advance.class.php';

class Brand extends Advance
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetBranchList($brand, $category, $caseStatus)
    {
        $sql = " SELECT *,
                    CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ), LPAD(bId,5,'0'), year(Now()) ) bCode2
                 FROM
                    `tBranch` b
                 WHERE b.bBrand = '" . $brand . "' 
                    AND  bCategory = '" . $category . "' ";
                if($caseStatus == 2) {
                    $sql .= " AND bStatus = 1 ";
                }
                $sql .= "ORDER BY b.bStore ASC; ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetBrand($id)
    {
        $sql  = " SELECT * FROM  `tBrand` Where bId = '" . $id . "' ;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetSmsBranch()
    {

    }

    public function GetBranch($id)
    {
        $sql = " SELECT *,
                    CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ), LPAD(bId,5,'0'))  bCode2
                 FROM
                    `tBranch` b
                 WHERE b.bId = '" . $id . "'
                 Order by b.bid ; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetCategory($branch)
    {
        $sql  = "SELECT bCategory FROM `tBranch` Where bID = '" . $branch . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function CheckBrandExist($code)
    {

        $sql  = 'SELECT bId FROM tBrand WHERE bCode = "' . $code . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return empty($stmt->fetch(PDO::FETCH_ASSOC)) ? false : true;
    }

    public function AddBrand($data)
    {
        $bank      = '';
        $scrivener = '';

        if (!empty($data['bBank'])) {
            $bank = implode(",", $data['bBank']);
        }
        if (!empty($data['bScrivener'])) {
            $scrivener = implode(",", $data['bScrivener']);
        }

        $sql = " INSERT INTO `tBrand` (
            `bId`,
            `bCode`,
            `bName`,
            `bPassword`,
            `bSerialnum`,
            `bWholeName`,
            `bScrivener`,
            `bZip`,
            `bAddress`,
            `bTelArea`,
            `bTelMain`,
            `bEmail`,
            `bSeller`,
            `bRemark`,
            `bBank`,
            `bRecall`,
            `bCertified`,
            `bSignDate`) VALUES (
             null,
            '" . $data['code'] . "',
            '" . $data['name'] . "',
            '" . $data['password1'] . "',
            '" . $data['serialnum'] . "',
            '" . $data['wholename'] . "',
            '" . $scrivener . "',
            '" . $data['zip'] . "',
            '" . $data['address'] . "',
            '" . $data['bTelArea'] . "',
            '" . $data['bTelMain'] . "',
            '" . $data['bEmail'] . "',
            '" . $data['seller'] . "',
            '" . $data['remark'] . "',
            '" . $bank . "',
            '" . $data['recall'] . "',
            '" . $data['certified'] . "',
            '" . $data['signDate'] . "') ";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute();
    }

    public function SaveBrand($data)
    {
        $bank      = '';
        $scrivener = '';

        if (!empty($data['bBank'])) {
            $bank = implode(",", $data['bBank']);
        }
        if (!empty($data['bScrivener'])) {
            $scrivener = implode(",", $data['bScrivener']);
        }

        $sql = " UPDATE`tBrand` SET
                    `bCode` = '" . $data['code'] . "',
                    `bName` = '" . $data['name'] . "',
                    `bPassword` = '" . $data['password1'] . "',
                    `bSerialnum` = '" . $data['serialnum'] . "',
                    `bWholeName` = '" . $data['wholename'] . "',
                    `bScrivener` = '" . $scrivener . "',
                    `bZip` = '" . $data['zip'] . "',
                    `bAddress` = '" . $data['address'] . "',
                    `bTelArea` = '" . $data['bTelArea'] . "',
                    `bTelMain` = '" . $data['bTelMain'] . "',
                    `bEmail` = '" . $data['bEmail'] . "',
                    `bSeller` = '" . $data['seller'] . "',
                    `bRemark` = '" . $data['remark'] . "',
                    `bBank` = '" . $bank . "',
                    `bRecall` = '" . $data['recall'] . "',
                    `bCertified` = '" . $data['certified'] . "',
                    `bBranch` = '" . $data['TargetBranch'] . "',
                    `bSignDate` = '" . $data['signDate'] . "'

                    WHERE `bId` = '" . $data['id'] . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function AddBranch($data, $new = null)
    {
        $cashierorderhas = "";
        $message         = "";
        $emailreceive    = "";
        $system          = "";
        $newid           = "";

        if ($new == null) {
            $sql  = " SELECT (max(bId)+1) newid FROM `tBranch` ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $row   = $stmt->fetch(PDO::FETCH_ASSOC);
            $newid = $row['newid'];
        } else {
            $newid = $new;
        }

        if (!empty($data['bMessage'])) {
            $message = implode(",", $data['bMessage']);
        }
        if (!empty($data['bEmailReceive'])) {
            $emailreceive = implode(",", $data['bEmailReceive']);
        }
        if (!empty($data['bCashierOrderHas'])) {
            $cashierorderhas = implode(",", $data['bCashierOrderHas']);
        }
        if (!empty($data['bServiceOrderHas'])) {
            $bServiceOrderHas = implode(",", $data['bServiceOrderHas']);
        }

        if (!empty($data['bCooperationHas'])) {
            $bCooperationHas = implode(",", $data['bCooperationHas']);
        }

        if (!empty($data['bBackDocument'])) {
            $bBackDocument = implode(",", $data['bBackDocument']);
        }

        if (!empty($data['faxDefault'])) {
            $faxDefault = implode(',', $data['faxDefault']);
        }

        if (!empty($data['bSystem'])) {
            $system = implode(",", $data['bSystem']);
        }

        $data['bBot']  = (!empty($data['bBot'])) ? implode(",", $data['bBot']) : 0;
        $bAccDetail    = (!empty($data['bAccDetail'])) ? implode(",", $data['bAccDetail']) : 0;
        $bCaseDetail   = (!empty($data['bCaseDetail'])) ? implode(",", $data['bCaseDetail']) : 0;
        $bFeedbackCase = (!empty($data['bFeedbackCase'])) ? implode(",", $data['bFeedbackCase']) : 0;

        $data['bClassBranch']    = preg_replace('/;+$/', '', $data['bClassBranch']);
        $data['bIndividualRate'] = empty($data['bIndividualRate']) ? '' : $data['bIndividualRate'];

        if (is_array($data['bContractStatus'])) {
            $bContractStatus = @implode(',', $data['bContractStatus']);
        }
        if ($bContractStatus != 1) {
            $bContractStatus = 2;
        }
        if (is_array($data['feedbackmoney'])) {
            $feedbackmoney = @implode(',', $data['feedbackmoney']);
        }

        if (!empty($data['bAccountUnused'])) {
            $bAccountUnused = implode(",", $data['bAccountUnused']);
        }

        if (!empty($data['bAccountUnused1'])) {
            $bAccountUnused1 = implode(",", $data['bAccountUnused1']);
        }

        if (!empty($data['bAccountUnused2'])) {
            $bAccountUnused2 = implode(",", $data['bAccountUnused2']);
        }

        if (!empty($data['bAccountUnused3'])) {
            $bAccountUnused2 = implode(",", $data['bAccountUnused3']);
        }

        $data['bSalesWeightAdding'] = preg_match("/^\d+$/", $data['bSalesWeightAdding']) ? $data['bSalesWeightAdding'] : 1;
        $data['bSalesWeightMinus']  = preg_match("/^\d+$/", $data['bSalesWeightMinus']) ? $data['bSalesWeightMinus'] : 1;

        $sql = " INSERT INTO `tBranch` (
            `bId`,
            `bPassword`,
            `bBrand`,
            `bName`,
            `bStore`,
            `bCategory`,
            `bSerialnum`,
            `bManager`,
            `bStatus`,
            `bStatusDateStart`,
            `bStatusDateEnd`,
            `bStoreClass`,
            `bClassBranch`,
            `bAccDetail`,
            `bCaseDetail`,
            `bFeedbackCase`,
            `bZip`,
            `bAddress`,
            `bTelArea`,
            `bTelMain`,
            `bFaxArea`,
            `bFaxMain`,
            `bMobileNum`,
            `bEmail`,
            `bMessage`,
            `bEmailReceive`,
            `bCashierOrderHas`,
            `bServiceOrderHas`,
            `bCooperationHas`,
            `bCashierOrderNumber`,
            `bCashierOrderMoney`,
            `bInvoice1`,
            `bInvoice2`,
            `bCashierOrderDate`,
            `bCashierOrderSave`,
            `bCashierOrderRemark`,
            `bCashierOrderMemo`,
            `bCashierOrderPpl`,
            `bSystem`,
            `bRecall`,
            `bScrRecall`,
            `bIndividualRate`,
            `bCertified`,
            `bFeedBack`,
            `bRtitle`,
            `bTtitle`,
            `bIdentity`,
            `bIdentityNumber`,
            `bZip2`,
            `bZip3`,
            `bAddr2`,
            `bAddr3`,
            `bMobileNum2`,
            `bEmail2`,
            `bAccountingName`,
            `bAccountingMobileNum`,
            `bSecretaryMobileNum`,
            `bSecretaryName`,
            `bAccountNum1`,
            `bAccountNum11`,
            `bAccountNum12`,
            `bAccountNum13`,
            `bAccountNum2`,
            `bAccountNum21`,
            `bAccountNum22`,
            `bAccountNum23`,
            `bAccount3`,
            `bAccount31`,
            `bAccount32`,
            `bAccount33`,
            `bAccount4`,
            `bAccount41`,
            `bAccount42`,
            `bAccount43`,
            `bAccountUnused`,
            `bAccountUnused1`,
            `bAccountUnused2`,
            `bAccountUnused3`,
            `bAccountNum5`,
            `bAccountNum6`,
            `bAccount7`,
            `bAccount8`,
            `bLoginTime`,
            `bCreat_time`,
			`bModify_time`,
			`bStatusTime`,
            `bRenote`,
            `bGroup`,
            `bContractStatus`,
            `bFeedbackMoney`,
            `bfnote`,
            `bPrincipal`,
            `bReTicket`,
            `bFeedDateCat`,
            `bEditor`,
            `bRg`,
            `bRgMoney`,
            `bBackDocument`,
            `bBackDocumentNote`,
            `bSameStore`,
            `bFaxDefault`,
            `bBot`,
            `bFeedbackMark`,
            `bFeedbackMark2`,
            `bSmsText`,
            `bSmsTextStyle`,
            `bSalesLevel`,
            `bSalesWeightAdding`,
            `bSalesWeightMinus`
            ) VALUES (
            '" . $newid . "',
            '" . $data['password1'] . "',
            '" . $data['bBrand'] . "',
            '" . $data['bName'] . "',
            '" . $data['bStore'] . "',
            '" . $data['bCategory'] . "',
            '" . $data['bSerialnum'] . "',
            '" . $data['bManager'] . "',
            '" . $data['bStatus'] . "',
            '" . $data['bStatusDateStart'] . "',
            '" . $data['bStatusDateEnd'] . "',
            '" . $data['bStoreClass'] . "',
            '" . $data['bClassBranch'] . "',
            '" . $bAccDetail . "',
            '" . $bCaseDetail . "',
            '" . $bFeedbackCase . "',
            '" . $data['zip'] . "',
            '" . $data['addr'] . "',
            '" . $data['bTelArea'] . "',
            '" . $data['bTelMain'] . "',
            '" . $data['bFaxArea'] . "',
            '" . $data['bFaxMain'] . "',
            '" . $data['bMobileNum'] . "',
            '" . $data['bEmail'] . "',
            '" . $message . "',
            '" . $emailreceive . "',
            '" . $cashierorderhas . "',
            '" . $bServiceOrderHas . "',
            '" . $bCooperationHas . "' ,
            '" . $data['bCashierOrderNumber'] . "',
            '" . $data['bCashierOrderMoney'] . "',
            '" . $data['bInvoice1'] . "',
            '" . $data['bInvoice2'] . "',
            '" . $data['bCashierOrderDate'] . "',
            '" . $data['bCashierOrderSave'] . "',
            '" . $data['bCashierOrderRemark'] . "',
            '" . $data['bCashierOrderMemo'] . "',
            '" . $data['bCashierOrderPpl'] . "',
            '" . $system . "',
            '" . $data['bRecall'] . "',
            '" . $data['bScrRecall'] . "',
            '" . $data['bIndividualRate'] . "',
            '" . $data['bCertified'] . "',
            '" . $data['bFeedBack'] . "',
            '" . $data['bRtitle'] . "',
            '" . $data['bTtitle'] . "',
            '" . $data['bIdentity'] . "',
            '" . $data['bIdentityNumber'] . "',
            '" . $data['zip2'] . "',
            '" . $data['zip3'] . "',
            '" . $data['addr2'] . "',
            '" . $data['addr3'] . "',
            '" . $data['bMobileNum2'] . "',
            '" . $data['bEmail2'] . "',
            '" . $data['bAccountingName'] . "',
            '" . $data['bAccountingMobileNum'] . "',
            '" . $data['bSecretaryMobileNum'] . "',
            '" . $data['bSecretaryName'] . "',
            '" . $data['bAccountNum1'] . "',
            '" . $data['bAccountNum11'] . "',
            '" . $data['bAccountNum12'] . "',
            '" . $data['bAccountNum13'] . "',
            '" . $data['bAccountNum2'] . "',
            '" . $data['bAccountNum21'] . "',
            '" . $data['bAccountNum22'] . "',
            '" . $data['bAccountNum23'] . "',
            '" . $data['bAccount3'] . "',
            '" . $data['bAccount31'] . "',
            '" . $data['bAccount32'] . "',
            '" . $data['bAccount33'] . "',
            '" . $data['bAccount4'] . "',
            '" . $data['bAccount41'] . "',
            '" . $data['bAccount42'] . "',
            '" . $data['bAccount43'] . "',
            '" . $data['bAccountUnused'] . "',
            '" . $data['bAccountUnused1'] . "',
            '" . $data['bAccountUnused2'] . "',
            '" . $data['bAccountUnused3'] . "',
            '" . $data['bAccountNum5'] . "',
            '" . $data['bAccountNum6'] . "',
            '" . $data['bAccount7'] . "',
            '" . $data['bAccount8'] . "',
            '" . date('Y-m-d H:i:s', time()) . "',
            '" . date('Y-m-d H:i:s', time()) . "',
            '" . date('Y-m-d H:i:s', time()) . "',
            '" . date('Y-m-d H:i:s', time()) . "',
            '" . $data['bRenote'] . "',
            '" . $data['bGroup'] . "',
            '" . $bContractStatus . "',
            '" . $feedbackmoney . "',
            '" . $data['bfnote'] . "',
            '" . $data['bPrincipal'] . "',
            '" . $data['bReTicket'] . "',
            '" . $data['feedDateCat'] . "',
            '" . $_SESSION['member_name'] . "',
            '" . $data['bRg'] . "',
            '" . $data['bRgMoney'] . "',
            '" . $bBackDocument . "',
            '" . $data['bBackDocumentNote'] . "',
            '" . $data['sameStore'] . "',
            '" . $faxDefault . "',
            '" . $data['bBot'] . "',
            '" . $data['feedbackMark'] . "',
            '" . $data['feedbackMark2'] . "',
            '" . $data['smsText'] . "',
            '" . $data['smsStyle'] . "',
            '" . $data['bSalesLevel'] . "',
            '" . $data['bSalesWeightAdding'] . "',
            '" . $data['bSalesWeightMinus'] . "'
            );";

        // echo $sql;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        if ($bContractStatus == 1) {
            $tmp = explode('-', $data['bContractStatusTime']);

            $tmp[0]              = $tmp[0] + 1911;
            $bContractStatusTime = $tmp[0] . $tmp[1] . $tmp[2];
            unset($tmp);
            $sql  = "UPDATE tBranch SET bContractStatusTime = '" . $bContractStatusTime . "' WHERE bId ='" . $newid . "'";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

        return $newid;
    }

    private function checkBranchStatus($id, $status, $checkType = 'status')
    {
        $tf   = false;
        $list = array();

        $sql  = 'SELECT * FROM tBranch WHERE bId="' . $id . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $list = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($checkType == 'status') {
            if (($list['bStatus'] != $status) && ($status != '1')) {
                $tf = true;
            }
        }

        if ($checkType == 'pending') {
            if ($list['bPendingTime'] == '0000-00-00 00:00:00') {
                $tf = true;
            }
        }

        return $tf;
    }

    public function SaveBranch($data)
    {
        $cashierorderhas = "";
        $message         = "";
        $emailreceive    = "";
        $system          = "";

        if (stripos($data['bName'], "待停用") or stripos($data['bStore'], "待停用")) {
            if ($this->checkBranchStatus($data['id'], $data['bStatus'], 'pending')) {
                $bPendingTime = date('Y-m-d H:i:s');
                $str          = "`bPendingTime`= '" . $bPendingTime . "',";
            }
        } else {
            $bPendingTime = '0000-00-00 00:00:00';
            $str          = "`bPendingTime`= '" . $bPendingTime . "',";
        }

        if (!empty($data['bMessage'])) {
            $message = implode(",", $data['bMessage']);
        }
        if (!empty($data['bEmailReceive'])) {
            $emailreceive = implode(",", $data['bEmailReceive']);
        }
        if (!empty($data['bCashierOrderHas'])) {
            $cashierorderhas = implode(",", $data['bCashierOrderHas']);
        }
        if (!empty($data['bServiceOrderHas'])) {
            $bServiceOrderHas = implode(",", $data['bServiceOrderHas']);
        }
        if (!empty($data['bCooperationHas'])) {
            $bCooperationHas = implode(",", $data['bCooperationHas']);
        }

        if (!empty($data['bBackDocument'])) {
            $bBackDocument = implode(",", $data['bBackDocument']);
        }

        if (!empty($data['faxDefault'])) {
            $faxDefault = implode(",", $data['faxDefault']);
        }

        if (!empty($data['bSystem'])) {
            $system = implode(",", $data['bSystem']);
        }

        $data['bBot']  = (!empty($data['bBot'])) ? implode(",", $data['bBot']) : 0;
        $bAccDetail    = (!empty($data['bAccDetail'])) ? implode(",", $data['bAccDetail']) : 0;
        $bCaseDetail   = (!empty($data['bCaseDetail'])) ? implode(",", $data['bCaseDetail']) : 0;
        $bFeedbackCase = (!empty($data['bFeedbackCase'])) ? implode(",", $data['bFeedbackCase']) : 0;

        if (!empty($data['bAccountUnused'])) {
            $bAccountUnused = implode(",", $data['bAccountUnused']);
        }

        if (!empty($data['bAccountUnused1'])) {
            $bAccountUnused1 = implode(",", $data['bAccountUnused1']);
        }

        if (!empty($data['bAccountUnused2'])) {
            $bAccountUnused2 = implode(",", $data['bAccountUnused2']);
        }

        if (!empty($data['bAccountUnused3'])) {
            $bAccountUnused3 = implode(",", $data['bAccountUnused3']);
        }

        $bStatusTime = '';
        if (!empty($data['bStatus']) && !empty($data['id'])) {
            if ($this->checkBranchStatus($data['id'], $data['bStatus'])) {
                $bStatusTime = "`bStatusTime` = '" . date('Y-m-d H:i:s', time()) . "',";
            }
        }

        $data['bClassBranch']    = preg_replace('/;;+/', ';', $data['bClassBranch']);
        $data['bClassBranch']    = preg_replace('/;+$/', '', $data['bClassBranch']);
        $data['bIndividualRate'] = empty($data['bIndividualRate']) ? '' : $data['bIndividualRate'];

        if (is_array($data['bContractStatus'])) {
            $bContractStatus = @implode(',', $data['bContractStatus']);
        }
        if ($bContractStatus != 1) {
            $bContractStatus = 2;
        }

        if (is_array($data['feedbackmoney'])) {
            $feedbackmoney = @implode(',', $data['feedbackmoney']);
        }

        if ($data['feedDateCat'] != '') {
            $str .= "`bFeedDateCat` ='" . $data['feedDateCat'] . "',";
        }

        $sql = "
                    UPDATE `tBranch` SET
                    `bPassword` = '" . $data['password1'] . "',
                    `bBrand` = '" . $data['bBrand'] . "',
                    `bName` = '" . $data['bName'] . "',
                    `bStore` = '" . $data['bStore'] . "',
                    `bCategory` = '" . $data['bCategory'] . "',
                    `bSerialnum` = '" . $data['bSerialnum'] . "',
                    `bManager` = '" . $data['bManager'] . "',
                    `bStatus` = '" . $data['bStatus'] . "',
                    `bStatusDateStart` = '" . $data['bStatusDateStart'] . "',
                    `bStatusDateEnd` = '" . $data['bStatusDateEnd'] . "',
                    `bStoreClass` = '" . $data['bStoreClass'] . "',
                    `bClassBranch` = '" . $data['bClassBranch'] . "',
                    `bAccDetail` = '" . $bAccDetail . "',
                    `bCaseDetail` = '" . $bCaseDetail . "',
                    `bFeedbackCase` = '" . $bFeedbackCase . "',
                    `bZip` = '" . $data['zip'] . "',
                    `bAddress` = '" . $data['addr'] . "',
                    `bTelArea` = '" . $data['bTelArea'] . "',
                    `bTelMain` = '" . $data['bTelMain'] . "',
                    `bFaxArea` = '" . $data['bFaxArea'] . "',
                    `bFaxMain` = '" . $data['bFaxMain'] . "',
                    `bFaxDefault` ='" . $faxDefault . "',
                    `bMobileNum` = '" . $data['bMobileNum'] . "',
                    `bEmail` = '" . $data['bEmail'] . "',
                    `bMessage` = '" . $message . "',
                    `bEmailReceive` = '" . $emailreceive . "',
                    `bServiceOrderHas` = '" . $bServiceOrderHas . "',
                    `bCashierOrderHas` = '" . $cashierorderhas . "',
                    `bCooperationHas` = '" . $bCooperationHas . "',
                    `bCashierOrderNumber` = '" . $data['bCashierOrderNumber'] . "',
                    `bCashierOrderMoney` = '" . $data['bCashierOrderMoney'] . "',
                    `bInvoice1` = '" . $data['bInvoice1'] . "',
                    `bInvoice2` = '" . $data['bInvoice2'] . "',
                    `bCashierOrderDate` = '" . $data['bCashierOrderDate'] . "',
                    `bCashierOrderSave` = '" . $data['bCashierOrderSave'] . "',
                    `bCashierOrderRemark` = '" . $data['bCashierOrderRemark'] . "',
                    `bCashierOrderMemo` = '" . $data['bCashierOrderMemo'] . "',
                    `bCashierOrderPpl` = '" . $data['bCashierOrderPpl'] . "',
                    `bSystem` = '" . $system . "',
                    `bRecall` = '" . $data['bRecall'] . "',
                    `bScrRecall` = '" . $data['bScrRecall'] . "',
                    `bIndividualRate` = '" . $data['bIndividualRate'] . "',
                    `bCertified` = '" . $data['bCertified'] . "',
                    `bFeedBack` = '" . $data['bFeedBack'] . "',
                    `bRtitle` = '" . $data['bRtitle'] . "',
                    `bTtitle` = '" . $data['bTtitle'] . "',
                    `bIdentity` = '" . $data['bIdentity'] . "',
                    `bIdentityNumber` = '" . $data['bIdentityNumber'] . "',
                    `bZip2` = '" . $data['zip2'] . "',
                    `bZip3` = '" . $data['zip3'] . "',
                    `bAddr2` = '" . $data['addr2'] . "',
                    `bAddr3` = '" . $data['addr3'] . "',
                    `bMobileNum2` = '" . $data['bMobileNum2'] . "',
                    `bEmail2` = '" . $data['bEmail2'] . "',
                    `bAccountingName` = '" . $data['bAccountingName'] . "',
                    `bAccountingMobileNum` = '" . $data['bAccountingMobileNum'] . "',
                    `bSecretaryMobileNum` = '" . $data['bSecretaryMobileNum'] . "',
                    `bSecretaryName` = '" . $data['bSecretaryName'] . "',
                    `bAccountNum1` = '" . $data['bAccountNum1'] . "',
                    `bAccountNum11` = '" . $data['bAccountNum11'] . "',
                    `bAccountNum12` = '" . $data['bAccountNum12'] . "',
                    `bAccountNum13` = '" . $data['bAccountNum13'] . "',
                    `bAccountNum2` = '" . $data['bAccountNum2'] . "',
                    `bAccountNum21` = '" . $data['bAccountNum21'] . "',
                    `bAccountNum22` = '" . $data['bAccountNum22'] . "',
                    `bAccountNum23` = '" . $data['bAccountNum23'] . "',
                    `bAccount3` = '" . $data['bAccount3'] . "',
                    `bAccount31` = '" . $data['bAccount31'] . "',
                    `bAccount32` = '" . $data['bAccount32'] . "',
                    `bAccount33` = '" . $data['bAccount33'] . "',
                    `bAccount4` = '" . $data['bAccount4'] . "',
                    `bAccount41` = '" . $data['bAccount41'] . "',
                    `bAccount42` = '" . $data['bAccount42'] . "',
                    `bAccount43` = '" . $data['bAccount43'] . "',
                    `bAccountNum5` = '" . $data['bAccountNum5'] . "',
                    `bAccountNum6` = '" . $data['bAccountNum6'] . "',
                    `bAccount7` = '" . $data['bAccount7'] . "',
                    `bAccount8` = '" . $data['bAccount8'] . "',
					" . $bStatusTime . "
                    `bRenote` = '" . $data['bRenote'] . "',
                    `bGroup` = '" . $data['bGroup'] . "',
                    `bGroup2` = '" . $data['bGroup2'] . "',
                    `bAccountUnused` = '" . $bAccountUnused . "',
                    `bAccountUnused1` = '" . $bAccountUnused1 . "',
                    `bAccountUnused2` = '" . $bAccountUnused2 . "',
                    `bAccountUnused3` = '" . $bAccountUnused3 . "',
                    `bContractStatus` = '" . $bContractStatus . "',
                    `bFeedbackMoney` = '" . $feedbackmoney . "',
                    `bPrincipal` = '" . $data['bPrincipal'] . "',
                    `bReTicket` = '" . $data['bReTicket'] . "',
                    `bOldStoreID` = '" . substr($data['bOldStoreID'], 3) . "',
                    " . $str . "
                    `bfnote` = '" . $data['bfnote'] . "',
                    `bSales` = '" . $data['bSales'] . "',
                    `bSalesDate` = '" . $data['bSalesDate'] . "',
                    `bRg` = '" . $data['bRg'] . "',
                    `bRgMoney` = '" . $data['bRgMoney'] . "',
                    `bBackDocument` = '" . $bBackDocument . "',
                    `bBackDocumentNote` = '" . $data['bBackDocumentNote'] . "',
                    `bSameStore` = '" . $data['sameStore'] . "',
                    `bFeedbackMark` = '" . $data['feedbackMark'] . "',
                    `bFeedbackMark2` = '" . $data['feedbackMark2'] . "',
                    `bFeedbackAllCase` = '" . $data['bFeedbackAllCase'] . "',
                    `bBot` = '" . $data['bBot'] . "',
                    `bSmsText` = '" . $data['smsText'] . "',
                    `bSmsTextStyle` = '" . $data['smsStyle'] . "',
                    `bSalesLevel` = '" . $data['bSalesLevel'] . "',
                    `bSalesWeightAdding` = '" . $data['bSalesWeightAdding'] . "',
                    `bSalesWeightMinus` = '" . $data['bSalesWeightMinus'] . "'

                    WHERE `bId` = " . $data['id'] . ";
                ";

        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute();
    }

    ##群組
    public function AddGroup($data)
    {

        $sql = "INSERT INTO `tBranchGroup` SET
                    `bName` = '" . $data['name'] . "',
                    `bStore` = '" . $data['store'] . "',
                    `bRecall` = '" . $data['bRecall'] . "',
                    `bBranch` = '" . $data['TargetBranch'] . "',
                    `bSignDate`  = '" . $data['signDate'] . "',
                    `bAccount` = '" . $data['account'] . "',
                    `bPassword` = '" . $data['password'] . "'";

        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute();
    }

    public function SaveGroup($data)
    {

        $sql = " UPDATE `tBranchGroup` SET
                    `bName` = '" . $data['name'] . "',
                    `bStore` = '" . $data['store'] . "',
                    `bRecall` = '" . $data['bRecall'] . "',
                    `bBranch` = '" . $data['TargetBranch'] . "',
                    `bSignDate`  = '" . $data['signDate'] . "',
                    `bAccount` = '" . $data['account'] . "',
                    `bPassword` = '" . $data['password'] . "'
                    WHERE `bId` = '" . $data['id'] . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetGroup($id)
    {
        $sql  = " SELECT * FROM  `tBranchGroup` Where bId = '" . $id . "' ;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetGroupList()
    {
        $sql = " SELECT * FROM  `tBranchGroup` ORDER BY bId ASC; ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
