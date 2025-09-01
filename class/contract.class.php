 <?php
require_once __DIR__ . '/advance.class.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

class Contract extends Advance
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetContract($id)
    {
        $sql  = " SELECT * FROM  `tContractCase` Where `cCertifiedId` = '" . $id . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetFurniture($id)
    {
        $sql  = " SELECT * FROM  `tContractFurniture` Where `cCertifyId` = '" . $id . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetRent($id)
    {
        $sql  = " SELECT * FROM  `tContractRent` Where `cCertifiedId` = '" . $id . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetAscription($id)
    {
        $sql  = " SELECT * FROM  `tContractAscription` Where `cCertifiedId` = '" . $id . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetContractLandCategory($id)
    {
        $sql  = "SELECT * FROM tContractLandCategory WHERE `cCertifiedId` = '" . $id . "'";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetRealstate($id)
    {
        $sql = 'SELECT
                    *,
                    (SELECT bStore FROM tBranch WHERE rea.cBranchNum=bId) AS cStore,
                    (SELECT bCategory FROM tBranch WHERE rea.cBranchNum=bId) AS bCategory,
                    (SELECT bRecall FROM tBranch WHERE rea.cBranchNum=bId) AS bRecall,
                    (SELECT bScrRecall FROM tBranch WHERE rea.cBranchNum=bId) AS bScrRecall,
                    (SELECT bCashierOrderHas FROM tBranch WHERE rea.cBranchNum=bId) AS bCashierOrderHas1,
                    (SELECT bServiceOrderHas FROM tBranch WHERE rea.cBranchNum=bId) AS bServiceOrderHas1,
                    (SELECT bStore FROM tBranch WHERE rea.cBranchNum1=bId) AS cStore1,
                    (SELECT bCategory FROM tBranch WHERE rea.cBranchNum1=bId) AS bCategory1,
                    (SELECT bRecall FROM tBranch WHERE rea.cBranchNum1=bId) AS bRecall1,
                    (SELECT bScrRecall FROM tBranch WHERE rea.cBranchNum1=bId) AS bScrRecall1,
                    (SELECT bCashierOrderHas FROM tBranch WHERE rea.cBranchNum1=bId) AS bCashierOrderHas2,
                    (SELECT bServiceOrderHas FROM tBranch WHERE rea.cBranchNum1=bId) AS bServiceOrderHas2,
                    (SELECT bStore FROM tBranch WHERE rea.cBranchNum2=bId) AS cStore2,
                    (SELECT bCategory FROM tBranch WHERE rea.cBranchNum2=bId) AS bCategory2,
                    (SELECT bRecall FROM tBranch WHERE rea.cBranchNum2=bId) AS bRecall2,
                    (SELECT bScrRecall FROM tBranch WHERE rea.cBranchNum2=bId) AS bScrRecall2,
                    (SELECT bCashierOrderHas FROM tBranch WHERE rea.cBranchNum2=bId) AS bCashierOrderHas3,
                    (SELECT bServiceOrderHas FROM tBranch WHERE rea.cBranchNum2=bId) AS bServiceOrderHas3,
                    (SELECT bStore FROM tBranch WHERE rea.cBranchNum3=bId) AS cStore3,
                    (SELECT bCategory FROM tBranch WHERE rea.cBranchNum3=bId) AS bCategory3,
                    (SELECT bRecall FROM tBranch WHERE rea.cBranchNum3=bId) AS bRecall3,
                    (SELECT bScrRecall FROM tBranch WHERE rea.cBranchNum3=bId) AS bScrRecall3,
                    (SELECT bCashierOrderHas FROM tBranch WHERE rea.cBranchNum3=bId) AS bCashierOrderHas4,
                    (SELECT bServiceOrderHas FROM tBranch WHERE rea.cBranchNum3=bId) AS bServiceOrderHas4
                FROM
                    tContractRealestate AS rea
                WHERE
                    cCertifyId="' . $id . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetScrivener($id)
    {
        $sql  = "SELECT * FROM  `tContractScrivener` Where `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetProperty($id, $item = 0)
    {
        $sql  = "SELECT * FROM  `tContractProperty` Where `cCertifiedId` = '" . $id . "' And cItem = '" . $item . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetPropertyOther($id)
    {
        $sql  = "SELECT * FROM  `tContractProperty` Where `cCertifiedId` = '" . $id . "' And cItem <> '0';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetParking($id)
    {
        $sql  = "SELECT * FROM  `tContractParking` Where `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetIncome($id)
    {
        $sql  = "SELECT * FROM  `tContractIncome` Where `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetExpenditure($id)
    {
        $sql  = "SELECT * FROM  `tContractExpenditure` Where `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetBuyer($id)
    {
        $sql  = "SELECT * FROM  `tContractBuyer` Where `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetOwner($id)
    {
        $sql  = "SELECT * FROM  `tContractOwner` Where `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetOthers($id, $ide)
    {
        $sql  = "SELECT * FROM  `tContractOthers` Where `cCertifiedId` = '" . $id . "' AND cIdentity='" . $ide . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetInvoice($id)
    {
        $sql  = "SELECT * FROM  `tContractInvoice` Where `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetLandFirst($id, $item)
    {
        $sql  = "SELECT * FROM  `tContractLand` Where cItem = '" . $item . "' And `cCertifiedId` = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetLandList($id)
    {
        $sql  = "SELECT * FROM  `tContractLand` Where cItem <> '0' And `cCertifiedId` = '" . $id . "' Order by cItem;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function CheckLandExist($id, $item)
    {
        $sql  = "SELECT count(*) cnt FROM  `tContractLand` Where cItem = '" . $item . "' And `cCertifiedId` = '" . $id . "' ;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['cnt'] != '0';
    }

    public function GetBuildMax($id)
    {
        $sql  = "SELECT max(cItem) max FROM `tContractProperty` Where cCertifiedId = '" . $id . "';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $row['max']++;
        return $row['max'];
    }

    public function AddContract($data)
    {

        if ($data['realestate_branch'] == '505') {
            $data['cFeedbackTarget'] = '2';
        }

        if ($data['realestate_branch1'] == '505') {
            $data['cFeedbackTarget1'] = '2';
        }

        if ($data['realestate_branch2'] == '505') {
            $data['cFeedbackTarget2'] = '2';
        }

        if ($data['realestate_branch3'] == '505') {
            $data['cFeedbackTarget3'] = '2';
        }

        if ($data['realestate_bRecall1'] == '') {
            $data['realestate_bRecall1'] = 0;
        }

        if ($data['realestate_bRecall2'] == '') {
            $data['realestate_bRecall2'] = 0;
        }

        if ($data['realestate_bRecall3'] == '') {
            $data['realestate_bRecall3'] = 0;
        }

        if ($data['realestate_bScrRecall'] == '') {
            $data['realestate_bScrRecall'] = 0;
        }

        if ($data['realestate_bScrRecall1'] == '') {
            $data['realestate_bScrRecall1'] = 0;
        }

        if ($data['realestate_bScrRecall2'] == '') {
            $data['realestate_bScrRecall2'] = 0;
        }

        if ($data['realestate_bScrRecall3'] == '') {
            $data['realestate_bScrRecall3'] = 0;
        }

        $data['case_cancellingClause'] = (empty($data['case_cancellingClause'])) ? 0 : $data['case_cancellingClause'];

        $sql = "INSERT INTO
                    `tContractCase`
                (
                    `cId`,
                    `cCertifiedId`,
                    `cEscrowBankAccount`,
                    `cDealId`,
                    `cApplyDate`,
                    `cSignDate`,
                    `cFinishDate`,
                    `cFinishDate2`,
                    `cUndertakerId`,
                    `cExceptionStatus`,
                    `cExceptionReason`,
                    `cBank`,
                    `cLastEditor`,
                    `cLastTime`,
                    `cCaseFeedback`,
                    `cCaseFeedback1`,
                    `cCaseFeedback2`,
                    `cCaseFeedback3`,
                    `cFeedbackTarget`,
                    `cFeedbackTarget1`,
                    `cFeedbackTarget2`,
                    `cFeedbackTarget3`,
                    `cCaseFeedBackMoney`,
                    `cCaseFeedBackMoney1`,
                    `cCaseFeedBackMoney2`,
                    `cCaseFeedBackMoney3`,
                    `cSpCaseFeedBackMoney`,
                    `cCaseFeedBackModifier`,
                    `cSpCaseFeedBackMoneyMark`,
                    `cBranchRecall`,
                    `cBranchRecall1`,
                    `cBranchRecall2`,
                    `cBranchRecall3`,
                    `cBranchScrRecall`,
                    `cBranchScrRecall1`,
                    `cBranchScrRecall2`,
                    `cBranchScrRecall3`,
                    `cScrivenerRecall`,
                    `cBrandScrRecall`,
                    `cBrandScrRecall1`,
                    `cBrandScrRecall2`,
                    `cBrandScrRecall3`,
                    `cBrandRecall`,
                    `cBrandRecall1`,
                    `cBrandRecall2`,
                    `cBrandRecall3`,
                    `cAffixDate`,
                    `cFirstDate`,
                    `cProperty`,
                    `cOnSales`,
                    `cCaseStatus`,
                    `cScrivenerSpRecall`,
                    `cScrivenerSpRecall2`,
                    `cRelatedCase`,
                    `cCancellingClause`,
                    `cCancellingClauseNote`,
                    `cCaseReport`
                ) VALUES (
                    null,
                    '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "',
                    '" . $data['scrivener_bankaccount'] . "',
                    '" . $data['case_dealid'] . "',
                    '" . $data['case_applydate'] . "',
                    '" . $data['case_signdate'] . "',
                    '" . $data['case_finishdate'] . "',
                    '" . $data['case_finishdate2'] . "',
                    '" . $data['case_undertakerid'] . "',
                    '" . $data['case_exception'] . "',
                    '" . $data['case_exceptionreason'] . "',
                    '" . $data['case_bank'] . "',
                    '" . $_SESSION['member_id'] . "',
                    NOW(),
                    '" . $data['cCaseFeedback'] . "',
                    '" . $data['cCaseFeedback1'] . "',
                    '" . $data['cCaseFeedback2'] . "',
                    '" . $data['cCaseFeedback3'] . "',
                    '" . $data['cFeedbackTarget'] . "',
                    '" . $data['cFeedbackTarget1'] . "',
                    '" . $data['cFeedbackTarget2'] . "',
                    '" . $data['cFeedbackTarget3'] . "',
                    '" . $data['cCaseFeedBackMoney'] . "',
                    '" . $data['cCaseFeedBackMoney1'] . "',
                    '" . $data['cCaseFeedBackMoney2'] . "',
                    '" . $data['cCaseFeedBackMoney3'] . "',
                    '" . $data['cSpCaseFeedBackMoney'] . "',
                    '" . $data['cCaseFeedBackModifier'] . "',
                    '" . $data['cSpCaseFeedBackMoneyMark'] . "',
                    '" . $data['realestate_bRecall'] . "',
                    '" . $data['realestate_bRecall1'] . "',
                    '" . $data['realestate_bRecall2'] . "',
                    '" . $data['realestate_bRecall3'] . "',
                    '" . $data['realestate_bScrRecall'] . "',
                    '" . $data['realestate_bScrRecall1'] . "',
                    '" . $data['realestate_bScrRecall2'] . "',
                    '" . $data['realestate_bScrRecall3'] . "',
                    '" . $data['sRecall'] . "',
                    '" . $data['scrivener_BrandScrRecall'] . "',
                    '" . $data['scrivener_BrandScrRecall1'] . "',
                    '" . $data['scrivener_BrandScrRecall2'] . "',
                    '" . $data['scrivener_BrandScrRecall3'] . "',
                    '" . $data['scrivener_BrandRecall'] . "',
                    '" . $data['scrivener_BrandRecall1'] . "',
                    '" . $data['scrivener_BrandRecall2'] . "',
                    '" . $data['scrivener_BrandRecall3'] . "',
                    '" . $data['case_affixdate'] . "',
                    '" . $data['case_firstdate'] . "',
                    '" . $data['case_property'] . "',
                    '" . $data['contract_sale'] . "',
                    '" . $data['case_status'] . "',
                    '" . $data['scrivener_sSpRecall'] . "',
                    '" . $data['cScrivenerSpRecall2'] . "',
                    '" . $data['relatedCase'] . "',
                    '" . $data['case_cancellingClause'] . "',
                    '" . $data['case_cancellingClauseNote'] . "',
                    '" . $data['case_reportupload'] . "'
                );";
        $stmt    = $this->dbh->prepare($sql);
        $is_pass = $stmt->execute();
        if ($is_pass) {
            $sql  = "UPDATE  `tBankCode` SET  `bUsed` =  '1' WHERE `bAccount` = " . $data['scrivener_bankaccount'] . ";";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            //20231016 建檔後計算已存在銷帳檔內的帳戶金額
            $this->updateNewContractCaseMoney($data['scrivener_bankaccount']);
        }
    }

    //建檔後計算已存在銷帳檔內的帳戶金額
    private function updateNewContractCaseMoney($cId)
    {
        $sql  = 'SELECT SUM(eDebit) as D, SUM(eLender) as L FROM tExpense WHERE eDepAccount = "00' . $cId . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($rs)) {
            return true;
        }

        $_balance_money = ($rs['L'] - $rs['D']) / 100;
        if (empty($_balance_money)) {
            return true;
        }

        $sql  = 'UPDATE tContractCase SET cCaseMoney = :money WHERE cEscrowBankAccount = :cId;';
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindParam('cId', $cId, PDO::PARAM_STR);
        $stmt->bindParam('money', $_balance_money, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function AddlandCategoryLand($data)
    {

        $land  = empty($data['landCategoryLand']) ? '' : implode(',', $data['landCategoryLand']);
        $build = empty($data['landCategoryBuild']) ? '' : implode(',', $data['landCategoryBuild']);

        $sql = "INSERT INTO
                    `tContractLandCategory`
                (
                    `cId`,
                    `cCertifiedId`,
                    `cLand`,
                    `cBuild`,
                    `cLandFee`
                ) VALUES (
                    null,
                    '" . $data['certifiedid'] . "',
                    '" . $land . "',
                    '" . $build . "',
                    '" . $data['LandFee'] . "'
                );";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddContract_Sales($cid, $target, $sid, $branch)
    {
        $sql = "INSERT INTO
                    `tContractSales`
                (
                    `cId`,
                    `cCertifiedId`,
                    `cTarget`,
                    `cSalesId`,
                    `cBranch`
                ) VALUES (
                    null,
                    '" . $this->CutToCertifyId($cid) . "',
                    '" . $target . "',
                    '" . $sid . "',
                    '" . $branch . "'
                );";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddContractAscription($data)
    {
        $cOwner = '';
        if ($data['ascription_owner']) {
            $cOwner = implode(',', $data['ascription_owner']);
        }

        $cBuyer = '';
        if ($data['ascription_buy']) {
            $cBuyer = implode(',', $data['ascription_buy']);
        }

        if ($data['certifiedid']) {
            $certifyid = $data['certifiedid'];
        } else {
            $certifyid = $this->CutToCertifyId($data['scrivener_bankaccount']);
        }

        $sql = " INSERT INTO `tContractAscription`
            (`cId`,
             `cCertifiedId`,
             `cContribute`,
             `cBuyer`,
             `cBuyerOther`,
             `cOwner`,
             `cOwnerOther`
             ) VALUES (
             null,
             '" . $certifyid . "',
            '" . $data['ascription_contribute'] . "',
             '" . $cBuyer . "',
             '" . $data['ascription_buyerother'] . "',
             '" . $cOwner . "',
             '" . $data['ascription_ownerother'] . "'
              );";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

    }

    public function AddContractFurniture($data)
    {

        if ($data['certifiedid']) {
            $certifyid = $data['certifiedid'];
        } else {
            $certifyid = $this->CutToCertifyId($data['scrivener_bankaccount']);
        }

        $sql = " INSERT INTO `tContractFurniture`
            (`cId`,
             `cCertifyId`,
             `cLamp`,
             `cBed`,
             `cDresser`,
             `cGeyser`,
             `cTelephone`,
             `cWasher`,
             `cGasStove`,
             `cSofa`,
             `cAir`,
             `cMachine`,
             `cTv`,
             `cOther`,
             `cRefrigerator`,
             `cSink`,
             `cGas`
             ) VALUES (
             null,
             '" . $certifyid . "',
             '" . $data['furniture_lamp'] . "',
             '" . $data['furniture_bed'] . "',
             '" . $data['furniture_dresser'] . "',
             '" . $data['furniture_geyser'] . "',
             '" . $data['furniture_telephone'] . "',
             '" . $data['furniture_washer'] . "',
             '" . $data['furniture_gasStove'] . "',
             '" . $data['furniture_sofa'] . "',
             '" . $data['furniture_air'] . "',
             '" . $data['furniture_machine'] . "',
             '" . $data['furniture_tv'] . "',
             '" . $data['furniture_other'] . "',
             '" . $data['furniture_refrigerator'] . "',
             '" . $data['furniture_sink'] . "',
             '" . $data['furniture_gas'] . "'
              );";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

    }

    public function AddContractRent($data)
    {

        if ($data['certifiedid']) {
            $certifyid = $data['certifiedid'];
        } else {
            $certifyid = $this->CutToCertifyId($data['scrivener_bankaccount']);
        }
        $sql = " INSERT INTO `tContractRent`
            (`cId`,
             `cCertifiedId`,
             `cRentDate`,
             `cRent`,
             `cOther`
             ) VALUES (
             null,
             '" . $certifyid . "',
             '" . $data['rent_rentdate'] . "',
             '" . $data['rent_rent'] . "',
             '" . $data['rent_cOther'] . "'
              );";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

    }

    public function AddRealstate($data)
    {

        if (($data['realestate_branchnum1'] == 0) || ($data['realestate_branchnum1'] == '')) {
            $data['realestate_brand1'] = 0;
        }

        //·s¼W²Ä¤@²Õ¥ò¤¶ªº¹w³]Â²°T¹ï¶H
        if ($data['realestate_branchnum']) {
            $data['cSmsTarget'] = $this->addSmsDefault($data['realestate_branchnum']);
            write_log($this->CutToCertifyId($data['scrivener_bankaccount']) . ',帶入簡訊對象' . $data['realestate_branchnum'] . ':' . $data['cSmsTarget'], 'contract_branchsms');
        }
        ##

        //·s¼W²Ä¤G²Õ¹w³]ªºÂ²°T¹ï¶H
        if ($data['realestate_branchnum1']) {
            $data['cSmsTarget1'] = $this->addSmsDefault($data['realestate_branchnum1']);
            write_log($this->CutToCertifyId($data['scrivener_bankaccount']) . ',帶入簡訊對象' . $data['realestate_branchnum1'] . ':' . $data['cSmsTarget1'], 'contract_branchsms');
        }
        ##
        if ($data['realestate_branchnum2']) {
            $data['cSmsTarget1'] = $this->addSmsDefault($data['realestate_branchnum2']);
            write_log($this->CutToCertifyId($data['scrivener_bankaccount']) . ',帶入簡訊對象' . $data['realestate_branchnum2'] . ':' . $data['cSmsTarget2'], 'contract_branchsms');
        }
        ##

        $sql = " INSERT INTO `tContractRealestate` (
            `cId`,
            `cCertifyId`,
            `cBrand`,
            `cName`,
            `cBranchNum`,
            `cServiceTarget`,
            `cSerialNumber`,
            `cSmsTarget`,
            `cTelArea`,
            `cTelMain`,
            `cFaxArea`,
            `cFaxMain`,
            `cZip`,
            `cAddress`,
            `cBrand1`,
            `cName1`,
            `cBranchNum1`,
            `cServiceTarget1`,
            `cSerialNumber1`,
            `cSmsTarget1`,
            `cTelArea1`,
            `cTelMain1`,
            `cFaxArea1`,
            `cFaxMain1`,
            `cZip1`,
            `cAddress1`,
            `cAffixBranch`,
            `cAffixBranch1`
            ) VALUES (
            null,
            '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "',
            '" . $data['realestate_brand'] . "',
            '" . $data['realestate_name'] . "',
            '" . $data['realestate_branchnum'] . "',
            '" . $data['cServiceTarget'] . "',
            '" . $data['realestate_serialnumber'] . "',
            '" . $data['cSmsTarget'] . "',
            '" . $data['realestate_telarea'] . "',
            '" . $data['realestate_telmain'] . "',
            '" . $data['realestate_faxarea'] . "',
            '" . $data['realestate_faxmain'] . "',
            '" . $data['realestate_zip'] . "',
            '" . $data['realestate_addr'] . "',
            '" . $data['realestate_brand1'] . "',
            '" . $data['realestate_name1'] . "',
            '" . $data['realestate_branchnum1'] . "',
            '" . $data['cServiceTarget1'] . "',
            '" . $data['realestate_serialnumber1'] . "',
            '" . $data['cSmsTarget1'] . "',
            '" . $data['realestate_telarea1'] . "',
            '" . $data['realestate_telmain1'] . "',
            '" . $data['realestate_faxarea1'] . "',
            '" . $data['realestate_faxmain1'] . "',
            '" . $data['realestate_zip1'] . "',
            '" . $data['realestate_addr1'] . "',
            '" . $data['cAffixBranch'] . "',
            '" . $data['cAffixBranch1'] . "'
            );";
        // echo $sql;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddScrivener($data)
    {

        if ($data['scrivener_print'] == '') {
            $data['scrivener_print'] = 'N';
        }

        $sql = " INSERT INTO `tContractScrivener` (
                    `cId`,
                    `cCertifiedId`,
                    `cScrivener`,
                    `cAssistant`) VALUES (
                    null,
                    '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "',
                    '" . $data['scrivener_id'] . "',
                    '" . $data['scrivener_assistant'] . "')";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function addLandPrice($data)
    {
        $sql = "INSERT INTO
                        tContractLandPrice
                    SET
                        cCertifiedId = '" . $data['cCertifiedId'] . "',
                        cLandItem = '" . $data['cLandItem'] . "',
                        cItem = '" . $data['cItem'] . "',
                        cMoveDate = '" . $data['cMoveDate'] . "',
                        cLandPrice = '" . $data['cLandPrice'] . "',
                        cPower1 = '" . $data['cPower1'] . "',
                        cPower2 = '" . $data['cPower2'] . "'";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function saveLandPrice($data)
    {
        $sql = "UPDATE
                    tContractLandPrice
                SET
                    cMoveDate = '" . $data['cMoveDate'] . "',
                    cLandPrice = '" . $data['cLandPrice'] . "',
                    cPower1 = '" . $data['cPower1'] . "',
                    cPower2 = '" . $data['cPower2'] . "'
                 WHERE
                    cId = '" . $data['cId'] . "'";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddLand($data, $item)
    {
        $data['land_landprice'] = str_replace(',', '', $data['land_landprice']);
        $data['land_money']     = str_replace(',', '', $data['land_money']);
        if (!empty($data['land_movedate'])) {
            $data['land_movedate'] = $data['land_movedate'] . "-00";
        }
        $cId = empty($this->CutToCertifyId($data['scrivener_bankaccount'])) ? $this->CutToCertifyId($data['scrivener_bankaccount2']) : $this->CutToCertifyId($data['scrivener_bankaccount']);

        $sql = " INSERT INTO `tContractLand`
                    (`cId`,
                    `cCertifiedId`,
                    `cItem`,
                    `cZip`,
                    `cAddr`,
                    `cLand1`,
                    `cLand2`,
                    `cLand3`,
                    `cLand4`,
                    `cMeasure`,
                    `cCategory`,
                    `cMoney`,
                    `cPower1`,
                    `cPower2`,
                    `cMoveDate`,
                    `cFarmLand`,
                    `cLandPrice`) VALUES (
                    NULL,
                    '" . $cId . "',
                    '" . $item . "',
                    '" . $data['land_zip'] . "',
                    '" . $data['land_addr'] . "',
                    '" . $data['land_land1'] . "',
                    '" . $data['land_land2'] . "',
                    '" . $data['land_land3'] . "',
                    '" . $data['land_land4'] . "',
                    '" . $data['land_measure'] . "',
                    '" . $data['land_category'] . "',
                    '" . $data['land_money'] . "',
                    '" . $data['lpower1'] . "',
                    '" . $data['lpower2'] . "',
                    '" . $data['land_movedate'] . "',
                    '" . $data['land_farmland'] . "',
                    '" . $data['land_landprice'] . "');";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddLand2($data, $item)
    {
        if (!empty($data['land_movedate'])) {
            $data['land_movedate'] = $data['land_movedate'] . "-00";
        }
        $sql = " INSERT INTO `tContractLand`
                    (`cId`,
                    `cCertifiedId`,
                    `cItem`,
                    `cZip`,
                    `cAddr`,
                    `cLand1`,
                    `cLand2`,
                    `cLand3`,
                    `cLand4`,
                    `cMeasure`,
                    `cCategory`,
                    `cMoney`,
                    `cPower1`,
                    `cPower2`,
                    `cMoveDate`,
                    `cLandPrice`) VALUES (
                    NULL,
                    '" . $data['certifiedid'] . "',
                    '" . $item . "',
                    '" . $data['land_zip'] . "',
                    '" . $data['land_addr'] . "',
                    '" . $data['land_land1'] . "',
                    '" . $data['land_land2'] . "',
                    '" . $data['land_land3'] . "',
                    '" . $data['land_land4'] . "',
                    '" . $data['land_measure'] . "',
                    '" . $data['land_category'] . "',
                    '" . $data['land_money'] . "',
                    '" . $data['lpower1'] . "',
                    '" . $data['lpower2'] . "',
                    '" . $data['land_movedate'] . "',
                    '" . $data['land_landprice'] . "');";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddLand3($id, $item)
    {

        $sql  = 'SELECT * FROM tContractLand WHERE cCertifiedId="' . $id . '" AND cItem = "0";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = " INSERT INTO `tContractLand`  (`cId`,
                    `cCertifiedId`,
                    `cItem`,
                    `cZip`,
                    `cLand1`,
                    `cLand2` ) VALUES (
                    NULL,
                    '$id',
                    '" . $item . "',
                    '" . $rs['cZip'] . "',
                    '" . $rs['cLand1'] . "',
                    '" . $rs['cLand2'] . "' ); ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddLand4($data, $count)
    {

        $cnt = (is_array($data['new_land_item'])) ? count($data['new_land_item']) : 0;

        for ($i = 0; $i < $cnt; $i++) {
            $count++;

            if ($data['new_land_measure'][$i] != '') {
                $data['new_land_landprice'][$i] = str_replace(',', '', $data['new_land_landprice'][$i]);
                $data['new_land_money'][$i]     = str_replace(',', '', $data['new_land_money'][$i]);
                if (!empty($data['new_land_movedate'][$i])) {
                    $data['new_land_movedate'][$i] = $data['new_land_movedate'][$i] . "-00";
                }

                $sql = " INSERT INTO `tContractLand`
                            (`cId`,
                            `cCertifiedId`,
                            `cItem`,
                            `cZip`,
                            `cAddr`,
                            `cLand1`,
                            `cLand2`,
                            `cLand3`,
                            `cLand4`,
                            `cMeasure`,
                            `cCategory`,
                            `cMoney`,
                            `cPower1`,
                            `cPower2`,
                            `cMoveDate`,
                            `cFarmLand`,
                            `cLandPrice`) VALUES (
                            NULL,
                            '" . $data['certifiedid'] . "',
                            '" . $count . "',
                            '" . $data['new_land_zip'][$i] . "',
                            '" . $data['new_land_addr'][$i] . "',
                            '" . $data['new_land_land1'][$i] . "',
                            '" . $data['new_land_land2'][$i] . "',
                            '" . $data['new_land_land3'][$i] . "',
                            '" . $data['new_land_land4'][$i] . "',
                            '" . $data['new_land_measure'][$i] . "',
                            '" . $data['new_land_category'][$i] . "',
                            '" . $data['new_land_money'][$i] . "',
                            '" . $data['new_land_power1'][$i] . "',
                            '" . $data['new_land_power2'][$i] . "',
                            '" . $data['new_land_movedate'][$i] . "',
                            '" . $data['new_land_farmland'][$i] . "',
                            '" . $data['new_land_landprice'][$i] . "');";
                // echo $sql;
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();

                for ($j = 0; $j <= 1; $j++) {
                    $dataPrice                 = array();
                    $dataPrice['cCertifiedId'] = $data['certifiedid'];
                    $dataPrice['cLandItem']    = $count;
                    $dataPrice['cItem']        = $j;

                    $date = explode('-', $data['new_land_movedate'][$j]);

                    $dataPrice['cMoveDate']  = (empty($data['new_land_movedate'][$j])) ? '0000-00-00' : ($date[0] + 1911) . "-" . $date[1] . "-00";
                    $dataPrice['cLandPrice'] = str_replace(',', '', $data['new_land_landprice'][$j]);
                    $dataPrice['cPower1']    = $data['new_land_landprice_power1'][$j];
                    $dataPrice['cPower2']    = $data['new_land_landprice_power2'][$j];

                    $this->addLandPrice($dataPrice);
                    unset($dataPrice);unset($date);
                }

            }

        }

    }

    // save new_property_item
    public function AddProperty2($data)
    {

        if (is_array($data['new_property_Item'])) {

            for ($i = 0; $i <= count($data['new_property_Item']); $i++) {
                // echo 'C'.$i;
                $objuse = '';
                $object = '';
                $item   = ($i == 0) ? (count($data['property_Item']) + 1) : ($item + 1);

                if ($data['new_property_zip' . $data['new_property_Item'][$i]]) {
                    // echo 'D'.$i;
                    $data['new_property_builddate' . $data['new_property_Item'][$i]]  = date_convert($data['new_property_builddate' . $data['new_property_Item'][$i]]);
                    $data['new_property_closingday' . $data['new_property_Item'][$i]] = date_convert($data['new_property_closingday' . $data['new_property_Item'][$i]]);

                    if ($data['new_property_objuse' . $data['new_property_Item']]) { //new_property_Item
                        $objuse = implode(',', $data['new_property_objuse' . $data['new_property_Item'][$i]]);
                    }

                    if ($data['new_property_cPropertyObject' . $data['new_property_Item' . $data['new_property_Item'][$i]]]) {
                        $object = implode(',', $data['new_property_cPropertyObject' . $data['new_property_Item'][$i]]);
                    }

                    if ($data['certifiedid'] == '') {
                        $data['certifiedid'] = $this->CutToCertifyId($data['scrivener_bankaccount']);
                    }

                    if ($data['new_property_actualArea' . $data['new_property_Item'][$i]] == 0) {
                        if ($data['new_property_power2' . $data['new_property_Item'][$i]] > 0) {
                            $data['new_property_actualArea' . $data['new_property_Item'][$i]] = $data['new_property_measuretotal' . $data['new_property_Item'][$i]] * ($data['new_property_power1' . $data['new_property_Item'][$i]] / $data['new_property_power2' . $data['new_property_Item'][$i]]);
                        }
                    }

                    $sql = " INSERT INTO `tContractProperty` (
                                `cId`,
                                `cCertifiedId`,
                                `cItem`,
                                `cBudMaterial`,
                                `cBuildDate`,
                                `cLevelNow`,
                                `cLevelHighter`,
                                `cZip`,
                                `cAddr`,
                                `cObjKind`,
                                `cObjUse`,
                                `cIsOther`,
                                `cOther`,
                                `cBuildAge`,
                                `cClosingDay`,
                                `cRoom`,
                                `cParlor`,
                                `cToilet`,
                                `cHasCar`,
                                `cMeasureTotal`,
                                `cActualArea`,
                                `cBuildNo`,
                                `cTownHouse`,
                                `cRentDate`,
                                `cRent`,
                                `cFinish`,
                                `cPropertyObject`,
                                `cObjectOther`,
                                `cPower1`,
                                `cPower2`,
                                `cPublicMeasureTotal`,
                                `cPublicMeasureMain`
                                ) VALUES (
                                null,
                                '" . $data['certifiedid'] . "',
                                '" . $item . "',
                                '" . $data['new_property_budmaterial' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_builddate' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_levelnow' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_levelhighter' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_zip' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_addr' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_objkind' . $data['new_property_Item'][$i]] . "',
                                '" . $objuse . "',
                                '" . $data['new_property_cIsOther' . $data['new_property_Item']] . "',
                                '" . $data['new_property_cOther' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_buildage' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_closingday' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_room' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_parlor' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_toilet' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_hascar' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_measuretotal' . $data['new_property_Item'][$i]] . "',
                                '" . $data['property_actualArea' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_buildno' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_housetown' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_rentdate' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_rent' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_finish' . $data['new_property_Item'][$i]] . "',
                                '" . $object . "',
                                '" . $data['new_property_cObjectOther' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_power1' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_power2' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_publicmeasuretotal' . $data['new_property_Item'][$i]] . "',
                                '" . $data['new_property_publicmeasuremain' . $data['new_property_Item'][$i]] . "'
                                );";

                    // echo $sql;

                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();

                    $this->AddProperty2BuildingLanNo($data['new_property_Item'][$i], $item, $data);

                }
            }
        }

    }

    public function AddProperty2BuildingLanNo($index, $item, $data)
    {
        $this->dbh->beginTransaction();

        try {
            $this->RemovePropertyBuildingLanNo($item, $data['certifiedid']);

            //插入紀錄
            if (!empty($data['new_buildingLandNo_' . $index])) {
                $row = [];
                foreach ($data['new_buildingLandSession_' . $index] as $k => $v) {
                    if (!empty($data['new_buildingLandNo_' . $index][$k])) {
                        $row[] = '(UUID(), "' . $data['certifiedid'] . '", "' . $item . '", "' . $data['new_buildingLandSession_' . $index][$k] . '", "' . $data['new_buildingLandSessionExt_' . $index][$k] . '", "' . $data['new_buildingLandNo_' . $index][$k] . '")';
                    }
                }

                $this->InsertBuildingLandNo($row);
                $row = null;unset($row);
            }

            $this->dbh->commit();
        } catch (Exception $e) {
            $this->dbh->rollBack();
            echo $e->getMessage();
        }
    }

    // save property_item
    public function AddProperty($data)
    {
        $objuse = '';
        $object = '';
        $itemNo = isset($data['item']) ? $data['item'] : 0;

        if ($data['property_objuse']) {
            $objuse = implode(',', $data['property_objuse']);
        }

        if ($data['property_cPropertyObject']) {
            $object = implode(',', $data['property_cPropertyObject']);
        }

        if ($data['certifiedid'] == '') {
            $data['certifiedid'] = $this->CutToCertifyId($data['scrivener_bankaccount']);
        }

        $sql = " INSERT INTO `tContractProperty` (
            `cId`,
            `cCertifiedId`,
            `cItem`,
            `cBudMaterial`,
            `cBuildDate`,
            `cLevelNow`,
            `cLevelHighter`,
            `cZip`,
            `cAddr`,
            `cObjKind`,
            `cObjUse`,
            `cIsOther`,
            `cOther`,
            `cBuildAge`,
            `cClosingDay`,
            `cRoom`,
            `cParlor`,
            `cToilet`,
            `cHasCar`,
            `cMeasureTotal`,
            `cBuildNo`,
            `cTownHouse`,
            `cRentDate`,
            `cRent`,
            `cFinish`,
            `cPropertyObject`,
            `cObjectOther`,
            `cPower1`,
            `cPower2`
            ) VALUES (
            null,
            '" . $data['certifiedid'] . "',
            '" . $itemNo . "',
            '" . $data['property_budmaterial'] . "',
            '" . $data['property_builddate'] . "',
            '" . $data['property_levelnow'] . "',
            '" . $data['property_levelhighter'] . "',
            '" . $data['property_zip'] . "',
            '" . $data['property_addr'] . "',
            '" . $data['property_objkind'] . "',
            '" . $objuse . "',
            '" . $data['property_cIsOther'] . "',
            '" . $data['property_cOther'] . "',
            '" . $data['property_buildage'] . "',
            '" . $data['property_closingday'] . "',
            '" . $data['property_room'] . "',
            '" . $data['property_parlor'] . "',
            '" . $data['property_toilet'] . "',
            '" . $data['property_hascar'] . "',
            '" . $data['property_measuretotal'] . "',
            '" . $data['property_buildno'] . "',
             '" . $data['property_housetown'] . "',
            '" . $data['property_rentdate'] . "',
            '" . $data['property_rent'] . "',
            '" . $data['property_finish'] . "',
            '" . $object . "',
            '" . $data['property_cObjectOther'] . "',
            '" . $data['property_power10'] . "',
            '" . $data['property_power20'] . "'
            );";
        // echo $sql;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $this->AddPropertyBuildingLanNo($itemNo, $data);
    }

    public function AddPropertyBuildingLanNo($item, $data)
    {
        $this->dbh->beginTransaction();

        try {
            $this->RemovePropertyBuildingLanNo($item, $data['certifiedid']);

            //插入紀錄
            if (!empty($data['new_buildingLandNo_' . $item])) {
                $row = [];
                foreach ($data['new_buildingLandSession_' . $item] as $k => $v) {
                    if (!empty($data['buildingLandNo_' . $item][$k])) {
                        $row[] = '(UUID(), "' . $data['certifiedid'] . '", "' . $item . '", "' . $data['new_buildingLandSession_' . $item][$k] . '", "' . $data['new_buildingLandSessionExt_' . $item][$k] . '", "' . $data['new_buildingLandNo_' . $item][$k] . '")';
                    }
                }

                $this->InsertBuildingLandNo($row);
                $row = null;unset($row);
            }

            $this->dbh->commit();
        } catch (Exception $e) {
            $this->dbh->rollBack();
            // echo $e->getMessage();InsertBuildingLandNo
        }
    }

    private function RemovePropertyBuildingLanNo($item, $cId)
    {
        $sql  = 'DELETE FROM tContractPropertyBuildingLandNo WHERE cCertifiedId = "' . $cId . '" AND cItem = "' . $item . '";';
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute();
    }

    private function InsertBuildingLandNo($values)
    {
        $sql = 'INSERT INTO
                    tContractPropertyBuildingLandNo
                (
                    cUUID, cCertifiedId, cItem, cBuildingSession, cBuildingSessionExt, cBuildingLandNo
                )
                VALUES
                ' . implode(',', $values) . ';';
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute();
    }

    public function AddPropertyItem($data)
    {
        $count = (is_array($data['property_item'])) ? count($data['property_item']) : 0;
        $sql   = " Delete from tContractProperty where cCertifiedId = '" . $data['certifiedid'] . "'; ";
        $stmt  = $this->dbh->prepare($sql);
        $stmt->execute();
        for ($i = 0; $i < $count; $i++) {
            $sql .= " INSERT INTO
                        `tContractProperty` (`cId`,
                                            `cCertifiedId`,
                                            `cItem`,
                                            `cLevelUse`,
                                            `cMeasureMain`,
                                            `cMeasure`,
                                            `cCategory`,
                                            `cPower1`,
                                            `cPower2`) VALUES (
                                            NULL,
                                            '" . $data['certifiedid'] . "',
                                            '" . ($i + 1) . "',
                                            '" . $data['property_use'][$i] . "',
                                            '" . $data['property_measuremain'][$i] . "',
                                            '" . $data['property_measure'][$i] . "',
                                            '" . $data['property_item'][$i] . "',
                                            '" . $data['property_power1'][$i] . "',
                                            '" . $data['property_power2'][$i] . "');";

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
    }

    public function AddIncome($data)
    {
        $data['income_loanmoney']            = str_replace(',', '', $data['income_loanmoney']);
        $data['income_signmoney']            = str_replace(',', '', $data['income_signmoney']);
        $data['income_affixmoney']           = str_replace(',', '', $data['income_affixmoney']);
        $data['income_dutymoney']            = str_replace(',', '', $data['income_dutymoney']);
        $data['income_estimatedmoney']       = str_replace(',', '', $data['income_estimatedmoney']);
        $data['income_totalmoney']           = str_replace(',', '', $data['income_totalmoney']);
        $data['income_certifiedmoney']       = str_replace(',', '', $data['income_certifiedmoney']);
        $data['income_addedtaxmoney']        = str_replace(',', '', $data['income_addedtaxmoney']);
        $data['income_paycash']              = str_replace(',', '', $data['income_paycash']);
        $data['income_ticket']               = str_replace(',', '', $data['income_ticket']);
        $data['income_paycommercialpaper']   = str_replace(',', '', $data['income_paycommercialpaper']);
        $data['income_firstmoney']           = str_replace(',', '', $data['income_firstmoney']);
        $data['income_nointomoney']          = str_replace(',', '', $data['income_nointomoney']);
        $data['income_commitmentmoney']      = str_replace(',', '', $data['income_commitmentmoney']);
        $data['income_depositMoney']         = str_replace(',', '', $data['income_depositMoney']);
        $data['income_businessTax']          = str_replace(',', '', $data['income_businessTax']);
        $data['income_certifiedMoneyPower1'] = str_replace(',', '', $data['income_certifiedMoneyPower1']);
        $data['income_certifiedMoneyPower2'] = str_replace(',', '', $data['income_certifiedMoneyPower2']);
        $data['income_land']                 = str_replace(',', '', $data['income_land']);
        $data['income_building']             = str_replace(',', '', $data['income_building']);

        $sql = " INSERT INTO `tContractIncome`
                    (`cId`,
                     `cCertifiedId`,
                     `cFirstMoney`,
                     `cBankLoan`,
                     `cLoanMoney`,
                     `cSignMoney`,
                     `cAffixMoney`,
                     `cDutyMoney`,
                     `cEstimatedMoney`,
                     `cTotalMoney`,
                     `cCertifiedMoney`,
                     `cParking`,
                     `cPayCash`,
                     `cPayTicket`,
                     `cPayCommercialPaper`,
                     `cSrivenerMoney`,
                     `cNotIntoMoney`,
                     `cAddedTaxMoney`,
                     `cCommitmentMoney`,
                     `cDepositMoney`,
                     `cBusinessTax`,
                     `cLand`,
                     `cBuilding`,
                     `cCertifiedMoneyPower1`,
                     `cCertifiedMoneyPower2`,
                     `cReasonCategory`
                     ) VALUES (
                     NULL,
                     '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "',
                     '" . $data['income_firstmoney'] . "',
                     '" . $data['income_bankloan'] . "',
                     '" . $data['income_loanmoney'] . "',
                     '" . $data['income_signmoney'] . "',
                     '" . $data['income_affixmoney'] . "',
                     '" . $data['income_dutymoney'] . "',
                     '" . $data['income_estimatedmoney'] . "',
                     '" . $data['income_totalmoney'] . "',
                     '" . $data['income_certifiedmoney'] . "',
                     '" . $data['income_parking'] . "',
                     '" . $data['income_paycash'] . "',
                     '" . $data['income_ticket'] . "',
                     '" . $data['income_paycommercialpaper'] . "',
                     '" . $data['income_scrivenermoney'] . "',
                     '" . $data['income_nointomoney'] . "',
                     '" . $data['income_addedtaxmoney'] . "',
                     '" . $data['income_commitmentmoney'] . "',
                     '" . $data['income_depositMoney'] . "',
                     '" . $data['income_businessTax'] . "',
                     '" . $data['income_land'] . "',
                     '" . $data['income_building'] . "',
                     '" . $data['income_certifiedMoneyPower1'] . "',
                     '" . $data['income_certifiedMoneyPower2'] . "',
                     '" . $data['income_reason_cat'] . "');";

        // echo $sql;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddExpenditure($data)
    {
        $data['expenditure_scrivenermoney']        = str_replace(",", "", $data['expenditure_scrivenermoney']);
        $data['expenditure_realestatemoney']       = str_replace(",", "", $data['expenditure_realestatemoney']);
        $data['expenditure_advancemoney']          = str_replace(",", "", $data['expenditure_advancemoney']);
        $data['expenditure_dealmoney']             = str_replace(",", "", $data['expenditure_dealmoney']);
        $data['expenditure_scrivenermoney_buyer']  = str_replace(",", "", $data['expenditure_scrivenermoney_buyer']);
        $data['expenditure_realestatemoney_buyer'] = str_replace(",", "", $data['expenditure_realestatemoney_buyer']);
        $data['expenditure_advancemoney_buyer']    = str_replace(",", "", $data['expenditure_advancemoney_buyer']);
        $data['expenditure_dealmoney_buyer']       = str_replace(",", "", $data['expenditure_dealmoney_buyer_buyer']);

        $sql = " INSERT INTO `tContractExpenditure`
            (`cId`,
             `cCertifiedId`,
             `cScrivenerMoney`,
             `cScrivenerMoneyBuyer`,
             `cRealestateMoney`,
             `cRealestateMoneyBuyer`,
             `cAdvanceMoney`,
             `cAdvanceMoneyBuyer`,
             `cDealMoney`,
             `cDealMoneyBuyer`,
             `cReason`,
             `cReasonBuyer`
             ) VALUES (
             null,
             '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "',
             '" . $data['expenditure_scrivenermoney'] . "',
             '" . $data['expenditure_scrivenermoney_buyer'] . "',
             '" . $data['expenditure_realestatemoney'] . "',
             '" . $data['expenditure_realestatemoney_buyer'] . "',
             '" . $data['expenditure_advancemoney'] . "',
             '" . $data['expenditure_advancemoney_buyer'] . "',
             '" . $data['expenditure_dealmoney'] . "',
             '" . $data['expenditure_dealmoney_buyer'] . "',
             '" . $data['expenditure_reason'] . "',
             '" . $data['expenditure_reason_buyer'] . "'); ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddInvoice($data)
    {
        $data['income_loanmoney']      = str_replace(',', '', $data['income_loanmoney']);
        $data['income_signmoney']      = str_replace(',', '', $data['income_signmoney']);
        $data['income_affixmoney']     = str_replace(',', '', $data['income_affixmoney']);
        $data['income_dutymoney']      = str_replace(',', '', $data['income_dutymoney']);
        $data['income_estimatedmoney'] = str_replace(',', '', $data['income_estimatedmoney']);
        $data['income_totalmoney']     = str_replace(',', '', $data['income_totalmoney']);
        $data['income_certifiedmoney'] = str_replace(',', '', $data['income_certifiedmoney']);
        $data['income_addedtaxmoney']  = str_replace(',', '', $data['income_addedtaxmoney']);
        $sql                           = " INSERT INTO `tContractInvoice`
            (`cId`,
            `cCertifiedId`,
            `cSplitBuyer`,
            `cInvoiceBuyer`,
            `cSplitOwner`,
            `cInvoiceOwner`,
            `cSplitRealestate`,
            `cInvoiceRealestate`,
            `cSplitScrivener`,
            `cInvoiceScrivener`,
            `cSplitOther`,
            `cInvoiceOther`,
            `cCertifiedBankAcc`,
            `cTaxReceiptTarget`,
            `cRemark`) VALUES (
            NULL,
            '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "',
            '" . $data['invoice_splitbuyer'] . "',
            '" . $data['invoice_invoicebuyer'] . "',
            '" . $data['invoice_splitowner'] . "',
            '" . $data['invoice_invoiceowner'] . "',
            '" . $data['invoice_splitrealestate'] . "',
            '" . $data['invoice_invoicerealestate'] . "',
            '" . $data['invoice_splitscrivener'] . "',
            '" . $data['invoice_invoicescrivener'] . "',
            '" . $data['invoice_splitother'] . "',
            '" . $data['invoice_invoiceother'] . "',
            '" . $data['invoice_certifiedbankacc'] . "',
            '" . $data['cTaxReceiptTarget'] . "',
            '" . $data['invoice_remark'] . "');";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddBuyer($data)
    {
        $sql = " INSERT INTO `tContractBuyer`
                    (`cId`,
                    `cCertifiedId`
                    ) VALUES (
                    NULL,
                    '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "');";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function AddOwner($data)
    {
        $sql = " INSERT INTO `tContractOwner`
            (`cId`,
            `cCertifiedId`
            ) VALUES (
            NULL,
            '" . $this->CutToCertifyId($data['scrivener_bankaccount']) . "'); ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveContract($data)
    {
        $sql_ext = " `cEndDate`='" . $data["case_cEndDate"] . "', ";

        ##防止空值導致回饋金變0
        if ($data['cCaseFeedBackMoney'] != '') {
            $str .= "`cCaseFeedBackMoney` = '" . $data['cCaseFeedBackMoney'] . "',";
        }

        if ($data['cCaseFeedBackMoney1'] != '') {
            $str .= "`cCaseFeedBackMoney1` = '" . $data['cCaseFeedBackMoney1'] . "',";
        }

        if ($data['cCaseFeedBackMoney2'] != '') {
            $str .= "`cCaseFeedBackMoney2` = '" . $data['cCaseFeedBackMoney2'] . "',";
        }

        if ($data['cCaseFeedBackMoney3'] != '') {
            if ($data['realestate_branchnum'] == '') {
                $data['cCaseFeedBackMoney3'] = 0;
            }
            $str .= "`cCaseFeedBackMoney3` = '" . $data['cCaseFeedBackMoney3'] . "',";
        }

        if ($data['cSpCaseFeedBackMoney'] != '' || $data['cSpCaseFeedBackMoneyMark'] == 'x') {
            $str .= "`cSpCaseFeedBackMoney` = '" . $data['cSpCaseFeedBackMoney'] . "',";
        }

        if ($data['cFeedbackTarget'] != '') {
            $str .= "`cFeedbackTarget` = '" . $data['cFeedbackTarget'] . "',";
        }

        if ($data['cFeedbackTarget1'] != '') {
            $str .= "`cFeedbackTarget1` = '" . $data['cFeedbackTarget1'] . "',";
        }

        if ($data['cFeedbackTarget2'] != '') {
            $str .= " `cFeedbackTarget2` = '" . $data['cFeedbackTarget2'] . "',";
        }

        if ($data['cFeedbackTarget3'] != '') {
            $str .= " `cFeedbackTarget3` = '" . $data['cFeedbackTarget3'] . "',";
        }

        if ($data['cCaseFeedBackModifyTime'] == 'time') {
            $str .= " `cCaseFeedBackModifyTime` = '" . date('Y-m-d H:i:s') . "',";
        }

        if(in_array($data['case_status'], [3, 10])) {
            $data['cCaseProcessing'] = 6;
        }
        ##
        $sql = "UPDATE  `tContractCase` SET
                    `cDealId` =  '" . $data['case_dealid'] . "',
                    `cSignDate` =  '" . $data['case_signdate'] . "',
                    `cFinishDate` = '" . $data['case_finishdate'] . "',
                    `cFinishDate2` = '" . $data['case_finishdate2'] . "',
                    `cCaseFeedback` = '" . $data['cCaseFeedback'] . "',
                    `cCaseFeedback1` = '" . $data['cCaseFeedback1'] . "',
                    `cCaseFeedback2` = '" . $data['cCaseFeedback2'] . "',
                    `cCaseFeedback3` = '" . $data['cCaseFeedback3'] . "',
                    " . $sql_ext . "
                    `cExceptionStatus` =  '" . $data['case_exception'] . "',
                    `cExceptionReason` =  '" . $data['case_exceptionreason'] . "' ,
                    `cBank` =  '" . $data['case_bank'] . "' ,
                    `cLastEditor` =  '" . $_SESSION['member_id'] . "',
                    `cLastTime` =  now(),
                    `cCaseStatus` =  '" . $data['case_status'] . "',
                    `cAffixDate` = '" . $data['case_affixdate'] . "',
                        `cFirstDate` = '" . $data['case_firstdate'] . "',
                        `cProperty` = '" . $data['case_property'] . "',
                    `cCaseProcessing` ='" . $data['cCaseProcessing'] . "',
                    `cCaseFeedBackModifier` = '" . $data['cCaseFeedBackModifier'] . "',
                    `cSpCaseFeedBackMoneyMark` = '" . $data['cSpCaseFeedBackMoneyMark'] . "',
                    `cNoIncome` = '" . $data['cNoIncome'] . "',
                    " . $str . "
                    `cNoClosing` = '" . $data['cNoClosing'] . "',
                    `cOnSales` = '" . $data['contract_sale'] . "',
                    `cRelatedCase` = '" . $data['relatedCase'] . "',
                    `cShow` = '" . $data['case_show'] . "',
                    `cCancellingClause` = '" . $data['case_cancellingClause'] . "',
                    `cCancellingClauseNote` = '" . $data['case_cancellingClauseNote'] . "',
                    `cCaseReport` = '" . $data['case_reportupload'] . "'
                WHERE `cCertifiedId` =  '" . $data['certifiedid'] . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        //2023-08-21 設定更新未出履保、有墊付利息的紀錄 2023-08-23 佩琦通知暫時取消
        // $this->setContractBankList($data['certifiedid'], $data['case_status']);
    }

    public function setContractBankList($cId, $case_status)
    {
        $bank_loans_date = '';

        //2023-08-21 案件狀態：已結案(3)、解約/終止履保(4)、作廢(8)、發函終止(9)、已結案有保留款(10) 時判斷是否有出過履保費
        if (in_array($case_status, [3, 4, 8, 9, 10])) {
            //2023-08-22 未付款履保費(1)，並且有墊付利息出款(2)時，押上日期
            if (empty($this->verifyCertifyMoneyPaid($cId)) && $this->verifyInterestLoan($cId)) {
                $bank_loans_date = $this->getBankLoansDate($cId);
            }
        }

        $this->setBankListValue($cId, $bank_loans_date);
    }

    //2023-08-22 取得銀行放款日
    private function getBankLoansDate($cId)
    {
        $item = ['點交(結案)', '解除契約', '保留款撥付', '建經發函終止', '預售屋'];

        $sql  = 'SELECT tBankLoansDate FROM tBankTrans WHERE tMemo = :cId AND tObjKind IN ("' . implode('","', $item) . '") AND tPayOk = 1 ORDER BY tBankLoansDate DESC LIMIT 1;';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam('cId', $cId, PDO::PARAM_STR);
        $stmt->execute();
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);

        return empty($rs['tBankLoansDate']) ? '' : $rs['tBankLoansDate'];
    }

    //2023-08-22 押上 cBankList 日期
    private function setBankListValue($cId, $value)
    {
        $sql  = 'UPDATE tContractCase SET cBankList = :value WHERE cCertifiedId = :cId;';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam('value', $value, PDO::PARAM_STR);
        $stmt->bindParam('cId', $cId, PDO::PARAM_STR);
        $stmt->execute();
    }

    //2023-08-21 是否有出款過履保費
    private function verifyCertifyMoneyPaid($cId)
    {
        $sql  = 'SELECT tId FROM tBankTrans WHERE tMemo = :cId AND tKind = "保證費" AND tPayOk = 1;';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam('cId', $cId, PDO::PARAM_STR);
        $stmt->execute();
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);

        return empty($rs['tId']) ? false : true;
    }

    //2023-08-21 是否有墊付利息出款
    private function verifyInterestLoan($cId)
    {
        $sql  = 'SELECT tId FROM tBankTrans WHERE tObjKind = "代墊利息" AND SUBSTRING(tAccount, 6) = :cId AND tPayOk = 1;';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam('cId', $cId, PDO::PARAM_STR);
        $stmt->execute();
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);

        return empty($rs['tId']) ? false : true;
    }

    private function getRealtySmsTarget($cid, $index = '')
    {
        $sql  = 'SELECT cBranchNum' . $index . ' as cBranchNum FROM tContractRealestate WHERE cCertifyId="' . $cid . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rs[0]['cBranchNum'] > 0) {
            return false; //¥ò¤¶¤w¦s¦b
        } else {
            return true; //¥ò¤¶¤£¦s¦b
        }
    }

    private function checkRealtyChange($bid, $cid, $index = '')
    {
        $sql  = 'SELECT cBranchNum' . $index . ' as cBranchNum FROM tContractRealestate WHERE cCertifyId="' . $cid . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($bid == $rs[0]['cBranchNum']) {
            return false;
        }
        //ÅÜ§ó«áªº©±®a»P¤§«e¬Û¦P
        else {
            return true;
        }
        //ÅÜ§ó«áªº©±®a»P¤§«e¬Û²§
    }

    private function checkRealtyChange2($bid, $cid, $index = '')
    { //仲介店改變-動態新增刪除

        $sql  = 'SELECT cBranchNum' . $index . ' as cBranchNum FROM tContractRealestate WHERE cCertifyId="' . $cid . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql_del = "DELETE FROM tBranchSms WHERE bCheck_id ='" . $cid . "' AND bBranch ='" . $rs[0]['cBranchNum'] . "'";

        $stmt = $this->dbh->prepare($sql_del);
        $stmt->execute();

    }

    public function SaveRealstate($data)
    {
        //½T»{¬O§_¬°·s¼W¥ò¤¶
        $newSms = '';

        //$chg = $this->getRealtySmsTarget($data['certifiedid']) ;
        //if ($chg && ($data['realestate_branchnum'] > 0)) {
        if ($this->checkRealtyChange($data['realestate_branchnum'], $data['certifiedid'])) {

            $sms = $this->addSmsDefault($data['realestate_branchnum']);

            $newSms .= ' cSmsTarget = "' . $sms . '", ';

            $this->checkRealtyChange2($data['realestate_branchnum'], $data['certifiedid']);

            write_log($data['certifiedid'] . ',更改簡訊對象' . $data['realestate_branchnum'] . ':' . $sms, 'contract_branchsms');
        }
        //$chg = $this->getRealtySmsTarget($data['certifiedid'],'1') ;
        //if ($chg && ($data['realestate_branchnum1'] > 0)) {
        if ($this->checkRealtyChange($data['realestate_branchnum1'], $data['certifiedid'], '1')) {

            $sms1 = $this->addSmsDefault($data['realestate_branchnum1']);

            $newSms .= ' cSmsTarget1 = "' . $sms1 . '", ';

            $this->checkRealtyChange2($data['realestate_branchnum'], $data['certifiedid'], '1');

            write_log($data['certifiedid'] . ',更改簡訊對象' . $data['realestate_branchnum1'] . ':' . $sms1, 'contract_branchsms');

        }
        //$chg = $this->getRealtySmsTarget($data['certifiedid'],'2') ;
        //if ($chg && ($data['realestate_branchnum2'] > 0)) {
        if ($this->checkRealtyChange($data['realestate_branchnum2'], $data['certifiedid'], '2')) {

            $sms2 = $this->addSmsDefault($data['realestate_branchnum2']);

            $newSms .= ' cSmsTarget2 = "' . $sms2 . '", ';

            $this->checkRealtyChange2($data['realestate_branchnum'], $data['certifiedid'], '2');

            write_log($data['certifiedid'] . ',更改簡訊對象' . $data['realestate_branchnum2'] . ':' . $sms2, 'contract_branchsms');
        }
        //
        if ($this->checkRealtyChange($data['realestate_branchnum3'], $data['certifiedid'], '2')) {

            $sms2 = $this->addSmsDefault($data['realestate_branchnum3']);

            $newSms .= ' cSmsTarget3 = "' . $sms2 . '", ';

            $this->checkRealtyChange2($data['realestate_branchnum'], $data['certifiedid'], '3');

            write_log($data['certifiedid'] . ',更改簡訊對象' . $data['realestate_branchnum3'] . ':' . $sms3, 'contract_branchsms');
        }
        ##

        if ($data['realestate_branchnum'] > 0) {
            $sql = "
                UPDATE  `tContractRealestate` SET
                    `cBrand` =  '" . $data['realestate_brand'] . "',
                    `cName` =  '" . $data['realestate_name'] . "',
                    `cBranchNum` =  '" . $data['realestate_branchnum'] . "',
                    `cServiceTarget` =  '" . $data['cServiceTarget'] . "',
                    `cSerialNumber` =  '" . $data['realestate_serialnumber'] . "',
                    `cTelArea` =  '" . $data['realestate_telarea'] . "',
                    `cTelMain` =  '" . $data['realestate_telmain'] . "',
                    `cFaxArea` =  '" . $data['realestate_faxarea'] . "',
                    `cFaxMain` =  '" . $data['realestate_faxmain'] . "',
                    `cZip` =  '" . $data['realestate_zip'] . "',
                    `cAddress` =  '" . $data['realestate_addr'] . "',
                    `cBrand1` =  '" . $data['realestate_brand1'] . "',
                    `cName1` =  '" . $data['realestate_name1'] . "',
                    `cBranchNum1` =  '" . $data['realestate_branchnum1'] . "',
                    `cServiceTarget1` =  '" . $data['cServiceTarget1'] . "',
                    `cSerialNumber1` =  '" . $data['realestate_serialnumber1'] . "',
                    `cTelArea1` =  '" . $data['realestate_telarea1'] . "',
                    `cTelMain1` =  '" . $data['realestate_telmain1'] . "',
                    `cFaxArea1` =  '" . $data['realestate_faxarea1'] . "',
                    `cFaxMain1` =  '" . $data['realestate_faxmain1'] . "',
                    `cZip1` =  '" . $data['realestate_zip1'] . "',
                    `cAddress1` =  '" . $data['realestate_addr1'] . "',
                    `cBrand2` =  '" . $data['realestate_brand2'] . "',
                    `cName2` =  '" . $data['realestate_name2'] . "',
                    `cBranchNum2` =  '" . $data['realestate_branchnum2'] . "',
                    `cServiceTarget2` =  '" . $data['cServiceTarget2'] . "',
                    `cSerialNumber2` =  '" . $data['realestate_serialnumber2'] . "',
                    `cTelArea2` =  '" . $data['realestate_telarea2'] . "',
                    `cTelMain2` =  '" . $data['realestate_telmain2'] . "',
                    `cFaxArea2` =  '" . $data['realestate_faxarea2'] . "',
                    `cFaxMain2` =  '" . $data['realestate_faxmain2'] . "',
                    `cZip2` =  '" . $data['realestate_zip2'] . "',
                    `cBrand3` =  '" . $data['realestate_brand3'] . "',
                    `cName3` =  '" . $data['realestate_name3'] . "',
                    `cBranchNum3` =  '" . $data['realestate_branchnum3'] . "',
                    `cServiceTarget3` =  '" . $data['cServiceTarget3'] . "',
                    `cSerialNumber3` =  '" . $data['realestate_serialnumber3'] . "',
                    `cTelArea3` =  '" . $data['realestate_telarea3'] . "',
                    `cTelMain3` =  '" . $data['realestate_telmain3'] . "',
                    `cFaxArea3` =  '" . $data['realestate_faxarea3'] . "',
                    `cFaxMain3` =  '" . $data['realestate_faxmain3'] . "',
                    `cZip3` =  '" . $data['realestate_zip3'] . "',
                    `cAddress3` =  '" . $data['realestate_addr3'] . "',
                    " . $newSms . "
                    `cAddress2` =  '" . $data['realestate_addr2'] . "',
                    `cAffixBranch` = '" . $data['cAffixBranch'] . "',
                    `cAffixBranch1` = '" . $data['cAffixBranch1'] . "',
                    `cAffixBranch2` = '" . $data['cAffixBranch2'] . "',
                    `cAffixBranch3` = '" . $data['cAffixBranch3'] . "'


                WHERE `cCertifyId` =  '" . $data['certifiedid'] . "' ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

    }

    public function SaveScrivener($data)
    {

        if ($data['scrivener_id']) {
            $data['ku_download'] = empty($data['ku_download']) ? 'N' : strtoupper($data['ku_download']);

            $sql = 'UPDATE
                        `tContractScrivener`
                    SET
                        `cAssistant` =  "' . $data['scrivener_assistant'] . '",
                        `cKuDownload` =  "' . $data['ku_download'] . '"
                    WHERE
                        `cCertifiedId` = "' . $data['certifiedid'] . '";';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
    }

    public function SavelandCategoryLand($data)
    {

        $land  = @implode(',', $data['landCategoryLand']);
        $build = @implode(',', $data['landCategoryBuild']);

        $sql = "UPDATE
                    `tContractLandCategory`
                SET
                    cLand = '" . $land . "',
                    cBuild = '" . $build . "',
                    cLandFee = '" . $data['LandFee'] . "'
                WHERE
                  `cCertifiedId` = '" . $data['certifiedid'] . "'
                ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

    }

    public function SaveAscription($data)
    {

        $cOwner = '';
        if ($data['ascription_owner']) {
            $cOwner = implode(',', $data['ascription_owner']);
        }

        $cBuyer = '';
        if ($data['ascription_buy']) {
            $cBuyer = implode(',', $data['ascription_buy']);
        }

        $sql = "
            UPDATE  `tContractAscription` SET
                `cContribute` =  '" . $data['ascription_contribute'] . "',
                `cBuyer` =  '" . $cBuyer . "',
                `cBuyerOther` =  '" . $data['ascription_buyerother'] . "',
                `cOwner` =  '" . $cOwner . "',
                `cOwnerOther` =  '" . $data['ascription_ownerother'] . "'

            WHERE  `cCertifiedId` = '" . $data['certifiedid'] . "' ; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveFurniture($data)
    {

        $sql = "
            UPDATE  `tContractFurniture` SET
                `cLamp` =  '" . $data['furniture_lamp'] . "',
                `cBed` =  '" . $data['furniture_bed'] . "',
                `cDresser` =  '" . $data['furniture_dresser'] . "',
                `cGeyser` =  '" . $data['furniture_geyser'] . "',
                `cTelephone` =  '" . $data['furniture_telephone'] . "',
                `cWasher` =  '" . $data['furniture_washer'] . "',
                `cGasStove` =  '" . $data['furniture_gasStove'] . "',
                `cSofa` =  '" . $data['furniture_sofa'] . "',
                `cAir` =  '" . $data['furniture_air'] . "',
                `cMachine` =  '" . $data['furniture_machine'] . "',
                `cTv` =  '" . $data['furniture_tv'] . "',
                `cOther` =  '" . $data['furniture_other'] . "',
                `cRefrigerator`=  '" . $data['furniture_refrigerator'] . "',
                `cSink`=  '" . $data['furniture_sink'] . "',
                `cGas`=  '" . $data['furniture_gas'] . "'
            WHERE  `cCertifyId` = '" . $data['certifiedid'] . "' ; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveContractRent($data)
    {

        $sql = "
            UPDATE  `tContractRent` SET
                `cRentDate` =  '" . $data['rent_rentdate'] . "',
                `cRent` =  '" . $data['rent_rent'] . "',
                `cFinish` =  '" . $data['rent_finish'] . "',
                `cOther` =  '" . $data['rent_cOther'] . "'

            WHERE  `cCertifiedId` = '" . $data['certifiedid'] . "' ; ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveProperty($data, $item = 0)
    {
        $objuse = '';
        if ($data['property_objuse']) {
            $objuse = implode(',', $data['property_objuse']);
        }

        if ($data['property_cPropertyObject']) {
            $object = implode(',', $data['property_cPropertyObject']);
        }

        $sql = "
            UPDATE `tContractProperty` SET
                    `cBuildDate` = '" . $data['property_builddate'] . "',
                    `cLevelNow` = '" . $data['property_levelnow'] . "',
                    `cLevelHighter` = '" . $data['property_levelhighter'] . "',
                    `cBudMaterial` = '" . $data['property_budmaterial'] . "',
                    `cZip` = '" . $data['property_zip'] . "',
                    `cAddr` = '" . $data['property_addr'] . "',
                    `cObjKind` = '" . $data['property_objkind'] . "',
                    `cObjUse` = '" . $objuse . "',
                    `cIsOther` = '" . $data['property_cIsOther'] . "',
                    `cOther` = '" . $data['property_cOther'] . "',
                    `cBuildAge` = '" . $data['property_buildage'] . "',
                    `cClosingDay` = '" . $data['property_closingday'] . "',
                    `cRoom` = '" . $data['property_room'] . "',
                    `cParlor` = '" . $data['property_parlor'] . "',
                    `cToilet` = '" . $data['property_toilet'] . "',
                    `cHasCar` = '" . $data['property_hascar'] . "',
                    `cBuildNo` = '" . $data['property_buildno'] . "',
                    `cTownHouse` = '" . $data['property_housetown'] . "',
                    `cRent` = '" . $data['property_rent'] . "',
                    `cRentDate` = '" . $data['property_rentdate'] . "',
                    `cFinish` = '" . $data['property_finish'] . "',
                    `cMeasureTotal` = '" . $data['property_measuretotal'] . "',
                    `cPropertyObject` = '" . $object . "',
                    `cObjectOther` = '" . $data['property_cObjectOther'] . "'

                    WHERE cCertifiedId = '" . $data['certifiedid'] . "' AND cItem = '" . $item . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveProperty2($data, $item = 0)
    {
        $objuse = '';

        if ($data['property_objuse' . $item]) {
            $objuse = implode(',', $data['property_objuse' . $item]);
        }

        if ($data['property_cPropertyObject' . $item]) {
            $object = implode(',', $data['property_cPropertyObject' . $item]);
        }

        if ($data['property_power2' . $item] > 0) {
            $data['property_actualArea' . $item] = $data['property_measuretotal' . $item] * ($data['property_power1' . $item] / $data['property_power2' . $item]);
        }

        $sql = "
            UPDATE `tContractProperty` SET
                    `cBuildDate` = '" . $data['property_builddate' . $item] . "',
                    `cLevelNow` = '" . $data['property_levelnow' . $item] . "',
                    `cLevelHighter` = '" . $data['property_levelhighter' . $item] . "',
                    `cBudMaterial` = '" . $data['property_budmaterial' . $item] . "',
                    `cZip` = '" . $data['property_zip' . $item] . "',
                    `cAddr` = '" . $data['property_addr' . $item] . "',
                    `cObjKind` = '" . $data['property_objkind' . $item] . "',
                    `cObjUse` = '" . $objuse . "',
                    `cIsOther` = '" . $data['property_cIsOther' . $item] . "',
                    `cOther` = '" . $data['property_cOther' . $item] . "',
                    `cBuildAge` = '" . $data['property_buildage' . $item] . "',
                    `cClosingDay` = '" . $data['property_closingday' . $item] . "',
                    `cRoom` = '" . $data['property_room' . $item] . "',
                    `cParlor` = '" . $data['property_parlor' . $item] . "',
                    `cToilet` = '" . $data['property_toilet' . $item] . "',
                    `cHasCar` = '" . $data['property_hascar' . $item] . "',
                    `cBuildNo` = '" . $data['property_buildno' . $item] . "',
                    `cTownHouse` = '" . $data['property_housetown' . $item] . "',
                    `cRent` = '" . $data['property_rent' . $item] . "',
                    `cRentDate` = '" . $data['property_rentdate' . $item] . "',
                    `cFinish` = '" . $data['property_finish' . $item] . "',
                    `cMeasureTotal` = '" . $data['property_measuretotal' . $item] . "',
                    `cActualArea` = '" . $data['property_actualArea' . $item] . "',
                    `cPropertyObject` = '" . $object . "',
                    `cObjectOther` = '" . $data['property_cObjectOther' . $item] . "',
                    `cPower1` = '" . $data['property_power1' . $item] . "',
                    `cPower2` = '" . $data['property_power2' . $item] . "',
                    `cPublicMeasureTotal` = '" . $data['property_publicmeasuretotal' . $item] . "',
                    `cPublicMeasureMain` = '" . $data['property_publicmeasuremain' . $item] . "'
                    WHERE cCertifiedId = '" . $data['certifiedid'] . "' AND cItem = '" . $item . "' ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        // $this->SaveProperty2BuildingLanNo($item, $data);
    }

    public function SaveProperty2BuildingLanNo($item, $data)
    {
        $this->dbh->beginTransaction();

        try {
            $this->RemovePropertyBuildingLanNo($item, $data['certifiedid']);

            //插入紀錄
            if (!empty($data['buildingLandNo_' . $item])) {
                $row = [];
                foreach ($data['buildingLandSession_' . $item] as $k => $v) {
                    if (!empty($data['buildingLandNo_' . $item][$k])) {
                        $row[] = '(UUID(), "' . $data['certifiedid'] . '", "' . $item . '", "' . $data['buildingLandSession_' . $item][$k] . '", "' . $data['buildingLandSessionExt_' . $item][$k] . '", "' . $data['buildingLandNo_' . $item][$k] . '")';
                    }
                }

                $this->InsertBuildingLandNo($row);
                $row = null;unset($row);
            }

            $this->dbh->commit();
        } catch (Exception $e) {
            $this->dbh->rollBack();
            // echo $e->getMessage();
        }
    }

    public function SaveLand($data, $item)
    {
        $data['land_landprice'] = str_replace(',', '', $data['land_landprice']);
        $data['land_money']     = str_replace(',', '', $data['land_money']);
        $data['land_movedate']  = $data['land_movedate'] . "-00";
        $sql                    = " UPDATE `tContractLand` SET
                    `cZip` = '" . $data['land_zip'] . "',
                    `cAddr` = '" . $data['land_addr'] . "',
                    `cLand1` = '" . $data['land_land1'] . "',
                    `cLand2` = '" . $data['land_land2'] . "',
                    `cLand3` = '" . $data['land_land3'] . "',
                    `cLand4` = '" . $data['land_land4'] . "',
                    `cMeasure` = '" . $data['land_measure'] . "',
                    `cCategory` = '" . $data['land_category'] . "',
                    `cMoney` = '" . $data['land_money'] . "',
                    `cPower1` = '" . $data['lpower1'] . "',
                    `cPower2` = '" . $data['lpower2'] . "',
                    `cMoveDate` = '" . $data['land_movedate'] . "',
                    `cFarmLand` = '" . $data['land_farmland'] . "',
                    `cLandPrice` = '" . $data['land_landprice'] . "'
                    WHERE `cCertifiedId` = '" . $data['certifiedid'] . "' AND `cItem` = '" . $item . "' ;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveLand2($data)
    {
        $sql = " UPDATE `tContractLand` SET
                    `cZip` = '" . $data['land_zip'] . "',
                    `cAddr` = '" . $data['land_addr'] . "',
                    `cLand1` = '" . $data['land_land1'] . "',
                    `cLand2` = '" . $data['land_land2'] . "',
                    `cLand3` = '" . $data['land_land3'] . "',
                    `cLand4` = '" . $data['land_land4'] . "',
                    `cMeasure` = '" . $data['land_measure'] . "',
                    `cCategory` = '" . $data['land_category'] . "',
                    `cMoney` = '" . $data['land_money'] . "',
                    `cPower1` = '" . $data['lpower1'] . "',
                    `cPower2` = '" . $data['lpower2'] . "',
                    `cMoveDate` = '" . $data['land_movedate'] . "',
                    `cLandPrice` = '" . $data['land_landprice'] . "'
                    WHERE `cCertifiedId` = '" . $data['certifiedid'] . "' AND `cItem` = '1' ;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveLand3($data)
    {
        $cnt = (is_array($data['land_item'])) ? count($data['land_item']) : 0;
        for ($i = 0; $i < $cnt; $i++) {
            $data['land_money'][$i]           = str_replace(',', '', $data['land_money'][$i]);
            $data['land_landprice'][$i]       = str_replace(',', '', $data['land_landprice'][$i]);
            $data['land_movedate' . ($i + 1)] = $data['land_movedate' . ($i + 1)] . "-00";
            $sql                              = " UPDATE `tContractLand` SET
                    `cZip` = '" . $data['land_zip'][$i] . "',
                    `cAddr` = '" . $data['land_addr'][$i] . "',
                    `cLand1` = '" . $data['land_land1'][$i] . "',
                    `cLand2` = '" . $data['land_land2'][$i] . "',
                    `cLand3` = '" . $data['land_land3'][$i] . "',
                    `cLand4` = '" . $data['land_land4'][$i] . "',
                    `cMeasure` = '" . $data['land_measure'][$i] . "',
                    `cCategory` = '" . $data['land_category'][$i] . "',
                    `cMoney` = '" . $data['land_money'][$i] . "',
                    `cPower1` = '" . $data['land_power1'][$i] . "',
                    `cPower2` = '" . $data['land_power2'][$i] . "',
                    `cMoveDate` = '" . $data['land_movedate' . ($i + 1)] . "',
                    `cLandPrice` = '" . $data['land_landprice'][$i] . "'
                    WHERE `cCertifiedId` = '" . $data['certifiedid'] . "' AND `cItem` = '" . $data['land_item'][$i] . "' ;";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
    }

    public function SaveIncome($data)
    {
        $data['income_loanmoney']          = str_replace(',', '', $data['income_loanmoney']);
        $data['income_signmoney']          = str_replace(',', '', $data['income_signmoney']);
        $data['income_affixmoney']         = str_replace(',', '', $data['income_affixmoney']);
        $data['income_dutymoney']          = str_replace(',', '', $data['income_dutymoney']);
        $data['income_estimatedmoney']     = str_replace(',', '', $data['income_estimatedmoney']);
        $data['income_totalmoney']         = str_replace(',', '', $data['income_totalmoney']);
        $data['income_certifiedmoney']     = str_replace(',', '', $data['income_certifiedmoney']);
        $data['income_addedtaxmoney']      = str_replace(',', '', $data['income_addedtaxmoney']);
        $data['income_firstmoney']         = str_replace(',', '', $data['income_firstmoney']);
        $data['income_nointomoney']        = str_replace(',', '', $data['income_nointomoney']);
        $data['income_paycash']            = str_replace(',', '', $data['income_paycash']);
        $data['income_ticket']             = str_replace(',', '', $data['income_ticket']);
        $data['income_paycommercialpaper'] = str_replace(',', '', $data['income_paycommercialpaper']);
        $data['income_commitmentmoney']    = str_replace(',', '', $data['income_commitmentmoney']);
        $data['income_depositMoney']       = str_replace(',', '', $data['income_depositMoney']);
        $data['income_businessTax']        = str_replace(',', '', $data['income_businessTax']);
        $data['income_land']               = str_replace(',', '', $data['income_land']);
        $data['income_building']           = str_replace(',', '', $data['income_building']);

        $sql = "UPDATE  `tContractIncome` SET
            `cBankLoan` =  '" . $data['income_bankloan'] . "',
            `cFirstMoney` =  '" . $data['income_firstmoney'] . "',
            `cLoanMoney` =  '" . $data['income_loanmoney'] . "',
            `cSignMoney` =  '" . $data['income_signmoney'] . "',
            `cAffixMoney` =  '" . $data['income_affixmoney'] . "',
            `cDutyMoney` =  '" . $data['income_dutymoney'] . "',
            `cEstimatedMoney` =  '" . $data['income_estimatedmoney'] . "',
            `cTotalMoney` =  '" . $data['income_totalmoney'] . "',
            `cCertifiedMoney` =  '" . $data['income_certifiedmoney'] . "',
            `cParking` =  '" . $data['income_parking'] . "',
            `cPayCash` =  '" . $data['income_paycash'] . "',
            `cPayTicket` = '" . $data['income_ticket'] . "',
            `cPayCommercialPaper` = '" . $data['income_paycommercialpaper'] . "',
            `cAddedTaxMoney` =  '" . $data['income_addedtaxmoney'] . "',
            `cSrivenerMoney` = '" . $data['income_scrivenermoney'] . "',
            `cNotIntoMoney` = '" . $data['income_nointomoney'] . "',
            `cCommitmentMoney` = '" . $data['income_commitmentmoney'] . "',
            `cDepositMoney` = '" . $data['income_depositMoney'] . "',
            `cBusinessTax` = '" . $data['income_businessTax'] . "',
            `cLand` = '" . $data['income_land'] . "',
            `cBuilding` = '" . $data['income_building'] . "',
            `cReasonCategory` = '" . $data['income_reason_cat'] . "'
            Where cCertifiedId = '" . $data['certifiedid'] . "'";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveExpenditure($data)
    {
        $data['expenditure_scrivenermoney']        = str_replace(",", "", $data['expenditure_scrivenermoney']);
        $data['expenditure_realestatemoney']       = str_replace(",", "", $data['expenditure_realestatemoney']);
        $data['expenditure_advancemoney']          = str_replace(",", "", $data['expenditure_advancemoney']);
        $data['expenditure_dealmoney']             = str_replace(",", "", $data['expenditure_dealmoney']);
        $data['expenditure_scrivenermoney_buyer']  = str_replace(",", "", $data['expenditure_scrivenermoney_buyer']);
        $data['expenditure_realestatemoney_buyer'] = str_replace(",", "", $data['expenditure_realestatemoney_buyer']);
        $data['expenditure_advancemoney_buyer']    = str_replace(",", "", $data['expenditure_advancemoney_buyer']);
        $data['expenditure_dealmoney_buyer']       = str_replace(",", "", $data['expenditure_dealmoney_buyer']);
        $sql                                       = "
            UPDATE  `tContractExpenditure`
            SET
                    `cScrivenerMoney` =  '" . $data['expenditure_scrivenermoney'] . "',
                    `cScrivenerMoneyBuyer` =  '" . $data['expenditure_scrivenermoney_buyer'] . "',
                    `cRealestateMoney` =  '" . $data['expenditure_realestatemoney'] . "',
                    `cRealestateMoneyBuyer` =  '" . $data['expenditure_realestatemoney_buyer'] . "',
                    `cAdvanceMoney` =  '" . $data['expenditure_advancemoney'] . "',
                    `cAdvanceMoneyBuyer` =  '" . $data['expenditure_advancemoney_buyer'] . "',
                    `cDealMoney` =  '" . $data['expenditure_dealmoney'] . "',
                    `cDealMoneyBuyer` =  '" . $data['expenditure_dealmoney_buyer'] . "',
                    `cReason` =  '" . $data['expenditure_reason'] . "',
                    `cReasonBuyer` =  '" . $data['expenditure_reason_buyer'] . "'
            Where cCertifiedId = '" . $data['certifiedid'] . "'";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveInvoice($data)
    {
        $data['invoice_invoicebuyer']      = str_replace(",", "", $data['invoice_invoicebuyer']);
        $data['invoice_invoiceowner']      = str_replace(",", "", $data['invoice_invoiceowner']);
        $data['invoice_invoicerealestate'] = str_replace(",", "", $data['invoice_invoicerealestate']);
        $data['invoice_invoicescrivener']  = str_replace(",", "", $data['invoice_invoicescrivener']);
        $data['invoice_invoiceother']      = str_replace(",", "", $data['invoice_invoiceother']);

        $sql = "
            UPDATE  `tContractInvoice` SET
                    `cCertifiedBankAcc` =  '" . $data['invoice_certifiedbankacc'] . "',
                    `cTaxReceiptTarget` =  '" . $data['cTaxReceiptTarget'] . "',
                    `cRemark` =  '" . $data['invoice_remark'] . "'
           Where cCertifiedId = '" . $data['certifiedid'] . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveOwner($data)
    {
        $data['owner_money1'] = str_replace(",", "", $data['owner_money1']);
        $data['owner_money2'] = str_replace(",", "", $data['owner_money2']);
        $data['owner_money3'] = str_replace(",", "", $data['owner_money3']);
        $data['owner_money4'] = str_replace(",", "", $data['owner_money4']);
        $data['owner_money5'] = str_replace(",", "", $data['owner_money5']);

        if ($data['owner_name']) {
            $sql = " UPDATE `tContractOwner` SET
                `cIdentifyId` = '" . $data['owner_identifyid'] . "',
                `cCategoryIdentify` = '" . $data['owner_categoryidentify'] . "',
                `cName` = '" . $data['owner_name'] . "',
                `cCountryCode` = '" . strtoupper($data['owner_country']) . "',
                `cTaxtreatyCode` = '" . strtoupper($data['owner_taxtreaty']) . "',
                `cResidentLimit` = '" . $data['owner_resident_limit'] . "',
                `cPaymentDate` = '" . $data['owner_payment_date'] . "',
                `cNHITax` = '" . $data['owner_NHITax'] . "',
                `cOther` = '" . $data['owner_other'] . "',
                `cBirthdayDay` = '" . $data['owner_birthdayday'] . "',
                `cContactName` = '" . $data['owner_contactname'] . "',
                `cMobileNum` = '" . $data['owner_mobilenum'] . "',
                `sAgentName1` = '" . $data['owner_agentname1'] . "',
                `sAgentName2` = '" . $data['owner_agentname2'] . "',
                `sAgentName3` = '" . $data['owner_agentname3'] . "',
                `sAgentName4` = '" . $data['owner_agentname4'] . "',
                `sAgentMobile1` = '" . $data['owner_agentmobile1'] . "',
                `sAgentMobile2` = '" . $data['owner_agentmobile2'] . "',
                `sAgentMobile3` = '" . $data['owner_agentmobile3'] . "',
                `sAgentMobile4` = '" . $data['owner_agentmobile4'] . "',
                `cTelArea1` = '" . $data['owner_telarea1'] . "',
                `cTelMain1` = '" . $data['owner_telmain1'] . "',
                `cTelArea2` = '" . $data['owner_telarea2'] . "',
                `cTelMain2` = '" . $data['owner_telmain2'] . "',
                `cRegistZip` = '" . $data['owner_registzip'] . "',
                `cRegistAddr` = '" . $data['owner_registaddr'] . "',
                `cBaseZip` = '" . $data['owner_basezip'] . "',
                `cBaseAddr` = '" . $data['owner_baseaddr'] . "',
                `cMoney1` = '" . $data['owner_money1'] . "',
                `cMoney2` = '" . $data['owner_money2'] . "',
                `cMoney3` = '" . $data['owner_money3'] . "',
                `cMoney4` = '" . $data['owner_money4'] . "',
                `cMoney5` = '" . $data['owner_money5'] . "',
                `cBankKey2` = '" . $data['owner_bankkey'] . "',
                `cBankBranch2` = '" . $data['owner_bankbranch'] . "',
                `cBankAccName` = '" . $data['owner_bankaccname'] . "',
                `cBankAccNumber` = '" . $data['owner_bankaccnumber'] . "',
                `cOtherName` = '" . $data['owner_othername'] . "',
                `cChecklistBank` = '" . $data['owner_cklist'] . "',
                `cShow` = '" . $data['owner_show'] . "',
                `cPassport` = '" . $data['owner_passport'] . "',
                `cBankMoney` = '" . $data['owner_bankMoney'] . "',
                `cEmail` = '" . $data['owner_mail'] . "'
                WHERE  `cCertifiedId` = '" . $data['certifiedid'] . "'";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
    }

    public function SaveBuyer($data)
    {

        if ($data['buy_name']) {
            $sql = "UPDATE `tContractBuyer` SET
                `cIdentifyId` = '" . $data['buy_identifyid'] . "',
                `cCategoryIdentify` = '" . $data['buy_categoryidentify'] . "',
                `cName` = '" . $data['buy_name'] . "',
                `cCountryCode` = '" . strtoupper($data['buyer_country']) . "',
                `cTaxtreatyCode` = '" . strtoupper($data['buyer_taxtreaty']) . "',
                `cResidentLimit` = '" . $data['buyer_resident_limit'] . "',
                `cPaymentDate` = '" . $data['buyer_payment_date'] . "',
                `cNHITax` = '" . $data['buyer_NHITax'] . "',
                `cOther` = '" . $data['buyer_other'] . "',
                `cBirthdayDay` = '" . $data['buy_birthdayday'] . "',
                `cContactName` = '" . $data['buy_contactname'] . "',
                `cMobileNum` = '" . $data['buy_mobilenum'] . "',
                `sAgentName1` = '" . $data['buyer_agentname1'] . "',
                `sAgentName2` = '" . $data['buyer_agentname2'] . "',
                `sAgentName3` = '" . $data['buyer_agentname3'] . "',
                `sAgentName4` = '" . $data['buyer_agentname4'] . "',
                `sAgentMobile1` = '" . $data['buyer_agentmobile1'] . "',
                `sAgentMobile2` = '" . $data['buyer_agentmobile2'] . "',
                `sAgentMobile3` = '" . $data['buyer_agentmobile3'] . "',
                `sAgentMobile4` = '" . $data['buyer_agentmobile4'] . "',
                `cTelArea1` = '" . $data['buy_telarea1'] . "',
                `cTelMain1` = '" . $data['buy_telmain1'] . "',
                `cTelArea2` = '" . $data['buy_telarea2'] . "',
                `cTelMain2` = '" . $data['buy_telmain2'] . "',
                `cRegistZip` = '" . $data['buyer_registzip'] . "',
                `cRegistAddr` = '" . $data['buyer_registaddr'] . "',
                `cBaseZip` = '" . $data['buyer_basezip'] . "',
                `cBaseAddr` = '" . $data['buyer_baseaddr'] . "',
                `cBankKey2` = '" . $data['buyer_bankkey'] . "',
                `cBankBranch2` = '" . $data['buyer_bankbranch'] . "',
                `cBankAccName` = '" . $data['buyer_bankaccname'] . "',
                `cBankAccNumber` = '" . $data['buyer_bankaccnumber'] . "',
                `cOtherName` = '" . $data['buyer_othername'] . "',
                `cAuthorized` = '" . $data['buyer_authorized'] . "',
                `cChecklistBank` = '" . $data['buyer_cklist'] . "',
                `cShow` = '" . $data['buyer_show'] . "',
                `cPassport` = '" . $data['buyer_passport'] . "',
                `cBankMoney` = '" . $data['buyer_bankMoney'] . "',
                `cEmail` = '" . $data['buyer_mail'] . "'
                WHERE `cCertifiedId` = '" . $data['certifiedid'] . "' ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
    }

    public function saveOwnerSales($data)
    {
        if (is_array($data['owner_agentmobile'])) {
            for ($i = 0; $i < count($data['owner_agentmobile']); $i++) {

                if ($data['owner_agenId'][$i]) {
                    $sql = "UPDATE
                                tContractPhone
                            SET
                                cMobileNum = '" . $data['owner_agentmobile'][$i] . "',
                                cName = '" . $data['owner_agentname'][$i] . "'
                            WHERE
                                cId = '" . $data['owner_agenId'][$i] . "'";
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                } else {
                    if ($data['owner_agentmobile'][$i] || $data['owner_agentname'][$i]) {
                        $sql = "INSERT INTO
                                    tContractPhone
                                SET
                                    cCertifiedId = '" . $data['certifiedid'] . "',
                                    cIdentity = 4,
                                    cMobileNum = '" . $data['owner_agentmobile'][$i] . "',
                                    cName = '" . $data['owner_agentname'][$i] . "'";
                        $stmt = $this->dbh->prepare($sql);
                        $stmt->execute();
                    }

                }
            }
        }

    }

    public function saveBuyerSales($data)
    {
        if (is_array($data['buyer_agentname'])) {
            for ($i = 0; $i < count($data['buyer_agentname']); $i++) {

                if ($data['buyer_agenId'][$i]) {
                    $sql = "UPDATE
                                tContractPhone
                            SET
                                cMobileNum = '" . $data['buyer_agentmobile'][$i] . "',
                                cName = '" . $data['buyer_agentname'][$i] . "'
                            WHERE
                                cId = '" . $data['buyer_agenId'][$i] . "'";
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                } else {
                    if ($data['buyer_agentmobile'][$i] || $data['buyer_agentname'][$i]) {
                        $sql = "INSERT INTO
                                    tContractPhone
                                SET
                                    cCertifiedId = '" . $data['certifiedid'] . "',
                                    cIdentity = 3,
                                    cMobileNum = '" . $data['buyer_agentmobile'][$i] . "',
                                    cName = '" . $data['buyer_agentname'][$i] . "'";
                        $stmt = $this->dbh->prepare($sql);
                        $stmt->execute();
                    }

                }
            }
        }

    }

    public function CheckLand($id, $item = 0)
    {
        $sql  = "SELECT count(*) cnt FROM  `tContractLand` Where cCertifiedId = '" . $id . "' AND cItem = '" . $item . "';   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['cnt'] > 0);
    }

    public function CheckProperty($id, $item = 0)
    {
        $sql  = "SELECT count(*) cnt FROM  `tContractProperty` Where cCertifiedId = '" . $id . "' AND cItem = '" . $item . "';   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['cnt'] > 0);
    }

    public function CheckContractProperty($id, $bitem)
    {
        $sql  = "SELECT count(*) cnt FROM  `tContractPropertyObject` Where cCertifiedId = '" . $id . "' AND cBuildItem = '" . $bitem . "';  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['cnt'] <= 1);
    }

    public function AddContractProperty($id, $item, $bitem)
    {
        $sql  = " Insert Into tContractPropertyObject (`cCertifiedId`, `cItem`, `cBuildItem`) value ('" . $id . "', '" . $item . "' ,'" . $bitem . "'); ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function GetContractProperty($id, $bitem)
    {
        $sql  = " Select * From tContractPropertyObject Where cCertifiedId = '" . $id . "' AND cBuildItem = '" . $bitem . "' Order by cItem ; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function SavePropertyObject($data)
    {
        $len   = (is_array($data['cCategory'])) ? count($data['cCategory']) : 0;
        $total = 0;
        $sum   = 0;
        for ($i = 0; $i < $len; $i++) {
            if ($data['cPower1'][$i] != 0 && $data['cPower2'][$i] != 0) {
                $total = $data['cMeasureTotal'][$i] * ($data['cPower1'][$i] / $data['cPower2'][$i]);
            }

            $sql = " Update tContractPropertyObject Set
                        `cCategory` = '" . $data['cCategory'][$i] . "',
                        `cLevelUse` = '" . $data['cLevelUse'][$i] . "',
                        `cMeasureTotal` = '" . $data['cMeasureTotal'][$i] . "',
                        `cPower1` = '" . $data['cPower1'][$i] . "',
                        `cPower2` = '" . $data['cPower2'][$i] . "',
                        `cCategory` = '" . $data['cCategory'][$i] . "',
                        `cMeasureMain` = '" . $total . "'
                      WHERE cId = '" . $data['cId'][$i] . "'
                    ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $sum += $total;
            $total = 0;
        }
        $sql  = "Select sum(cMeasureMain) mm From tContractPropertyObject Where cCertifiedId  = '" . $data['cCertifiedId'] . "' AND cBuildItem = '" . $data['bitem'] . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = " Update `tContractProperty` SET  `cMeasureTotal` =  '" . number_format($row['mm'], 2, '.', '') . "' WHERE  cCertifiedId = '" . $data['cCertifiedId'] . "' AND cItem = '" . $data['bitem'] . "';";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    private function addSmsDefault($bid)
    {
        $sql  = 'SELECT bMobile FROM tBranchSms WHERE bBranch="' . $bid . '" AND bDefault="1" AND bNID NOT IN ("14","15") AND bDel = 0 ORDER BY bNID,bId ASC;';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $tmp = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $smsTarget = array();
        foreach ($tmp as $k => $v) {
            $smsTarget[] = $v['bMobile'];
        }

        return implode(",", $smsTarget);
    }

}

?>