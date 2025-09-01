<?php

require_once __DIR__ . '/advance.class.php';

class Scrivener extends Advance
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetListScrivener()
    {
        $sql  = "SELECT * FROM `tScrivener` WHERE sStatus='1' Order by sId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function GetScrivenerInfo($id)
    {
        $sql  = "SELECT * FROM `tScrivener` Where sId = '" . $id . "' Order by sId;   ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getBC($bc)
    {
        $sql  = 'SELECT cBankCode,cBankVR FROM tContractBank WHERE cBankCode="' . $bc . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetBankCode($id, $bc = '8')
    {
        $bankcode = array();
        $conBank  = array();

        $conBank = $this->getBC($bc);
        $bc      = $conBank[0]['cBankVR'] . '%';

        $sql  = "SELECT * FROM `tBankCode` where bUsed = '0' AND bDel = 'n' AND bSID = '" . $id . "' AND bAccount LIKE '" . $bc . "' ORDER BY bVersion DESC";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $k => $v) {
            $bankcode[$v['bAccount']]['bAccount'] = $v['bAccount'];
            $bankcode[$v['bAccount']]['bVersion'] = $v['bVersion'];

            //99986
            if (preg_match("/^99986/", $v['bAccount'])) {
                $bankcode[$v['bAccount']]['branch'] = 6; // 城中
            }
        }
        return $bankcode;
    }

    public function GetScrivenerFeedbackBank($id)
    {
        $sql  = "SELECT * FROM `tFeedBackData` WHERE fType = 1 AND fStoreId = '" . $id . "' AND fStatus = 0 AND fStop = 0;";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function AddScrivener($data)
    {
        $brand       = '';
        $invoicecase = '';

        if (!empty($data['sBrand'])) {
            $brand = implode(",", $data['sBrand']);
        }
        if (!empty($data['sBackDocument'])) {
            $sBackDocument = implode(",", $data['sBackDocument']);
        }
        if (is_array($data['sContractStatus'])) {
            $sContractStatus = @implode(',', $data['sContractStatus']);
        }

        if ($sContractStatus != 1) {
            $sContractStatus = 2;
        }
        if (is_array($data['feedbackmoney'])) {
            $feedbackmoney = @implode(',', $data['feedbackmoney']);
        }

        if (!empty($data['sBank'])) {
            $bank = is_array($data['sBank']) ? implode(",", $data['sBank']) : $data['sBank'];
        }

        if (empty($data['sScrivenerBranch'])) {
            $data['sScrivenerBranch'] = null;
        }

        if(empty($data['feedDateCat'])) {
            $data['feedDateCat'] = 2;
        }

        $sql = " INSERT INTO  `tScrivener` (
                    `sId`,
                    `sName`,
                    `sIdentifyId`,
                    `sSerialnum`,
                    `sPassword`,
                    `sLicenseExpired`,
                    `sBrand`,
                    `sBank`,
                    `sCategory`,
                    `sOffice`,
                    `sGuild`,
                    `sTelArea`,
                    `sTelMain`,
                    `sTelExt`,
                    `sTelArea2`,
                    `sTelMain2`,
                    `sTelExt2`,
                    `sFaxArea`,
                    `sFaxMain`,
                    `sZip1`,
                    `sAddress`,
                    `sCpZip1`,
                    `sCpAddress`,
                    `sMobileNum`,
                    `sInputDate`,
                    `sAppointDate`,
                    `sOpenDate`,
                    `sSaveDate`,
                    `sTicketNumber`,
                    `sTicketMoney`,
                    `sEmail`,
                    `sStatus`,
                    `sStatusDateStart`,
                    `sStatusDateEnd`,
                    `sInvoiceCase`,
                    `sRecall`,
                    `sSpRecall`,
                    `sUndertaker1`,
                    `sUndertaker2`,
                    `sDrawer`,
                    `sTicketRemark`,
                    `sRemark1`,
                    `sRemark2`,
                    `sRemark3`,
                    `sRemark4`,
                    `sRemark5`,
                    `sAccountNum1`,
                    `sAccountNum2`,
                    `sAccount3`,
                    `sAccount4`,
                    `sAccountNum11`,
                    `sAccountNum21`,
                    `sAccount31`,
                    `sAccount41`,
                    `sAccountNum12`,
                    `sAccountNum22`,
                    `sAccount32`,
                    `sAccount42`,
                    `sMessage1`,
                    `sMessage2`,
                    `sMessage3`,
                    `sMessage4`,
                    `sMessage5`,
                    `sMesName1`,
                    `sMesName2`,
                    `sMesName3`,
                    `sMesName4`,
                    `sMesName5`,
                    `sLoginTime`,
                    `sCreat_time`,
                    `sModify_time`,
                    `sStatusTime`,
                    `sRenote`,
                    `sContractStatus`,
                    `sFeedBack`,
                    `sTtitle`,
                    `sMobileNum2`,
                    `sIdentity`,
                    `sIdentityNumber`,
                    `sRtitle`,
                    `sZip3`,
                    `sAddr3`,
                    `sZip2f`,
                    `sAddr2f`,
                    `sEmail2`,
                    `sAccountNum5`,
                    `sAccountNum6`,
                    `sAccount7`,
                    `sAccount8`,
                    `sfnote`,
                    `sFeedDateCat`,
                    `sFeedDateCatSwitch`,
                    `sFeedDateCatSwitchDate`,
                    `sEditor`,
                    `sRg`,
                    `sRgMoney`,
                    `sBirthday`,
                    `sBackDocument`,
                    `sFeedbackMoney`,
                    `sBackDocumentNote`,
                    `sFeedbackMark`,
                    `sScrivenerBranch`,
                    `sScrivenerSystem`,
                    `sScrivenerSystemOther`
                    ) VALUES (
                    NULL,
                    '" . $data['sName'] . "',
                    '" . $data['sIdentifyId'] . "',
                    '" . $data['sSerialnum'] . "',
                    '" . $data['sPassword'] . "',
                    '" . $data['sLicenseExpired'] . "',
                    '" . $brand . "',
                    '" . $bank . "',
                    '" . $data['sCategory'] . "',
                    '" . $data['sOffice'] . "',
                    '" . $data['sGuild'] . "',
                    '" . $data['sTelArea'] . "',
                    '" . $data['sTelMain'] . "',
                    '" . $data['sTelExt'] . "',
                    '" . $data['sTelArea2'] . "',
                    '" . $data['sTelMain2'] . "',
                    '" . $data['sTelExt2'] . "',
                    '" . $data['sFaxArea'] . "',
                    '" . $data['sFaxMain'] . "',
                    '" . $data['zip2'] . "',
                    '" . $data['addr2'] . "',
                    '" . $data['zip2'] . "',
                    '" . $data['addr2'] . "',
                    '" . $data['sMobileNum'] . "',
                    '" . $data['sInputDate'] . "',
                    '" . $data['sAppointDate'] . "',
                    '" . $data['sOpenDate'] . "',
                    '" . $data['sSaveDate'] . "',
                    '" . $data['sTicketNumber'] . "',
                    '" . $data['sTicketMoney'] . "',
                    '" . $data['sEmail'] . "',
                    '" . $data['sStatus'] . "',
                    '" . $data['sStatusDateStart'] . "',
                    '" . $data['sStatusDateEnd'] . "',
                    '" . $data['sInvoiceCase'] . "',
                    '" . $data['sRecall'] . "',
                    '" . $data['sSpRecall'] . "',
                    '" . $data['sUndertaker1'] . "',
                    '" . $data['sUndertaker2'] . "',
                    '" . $data['sDrawer'] . "',
                    '" . $data['sTicketRemark'] . "',
                    '" . $data['sRemark1'] . "',
                    '" . $data['sRemark2'] . "',
                    '" . $data['sRemark3'] . "',
                    '" . $data['sRemark4'] . "',
                    '" . $data['sRemark5'] . "',
                    '" . $data['sAccountNum1'] . "',
                    '" . $data['sAccountNum2'] . "',
                    '" . $data['sAccount3'] . "',
                    '" . $data['sAccount4'] . "',
                    '" . $data['sAccountNum11'] . "',
                    '" . $data['sAccountNum21'] . "',
                    '" . $data['sAccount31'] . "',
                    '" . $data['sAccount41'] . "',
                    '" . $data['sAccountNum12'] . "',
                    '" . $data['sAccountNum22'] . "',
                    '" . $data['sAccount32'] . "',
                    '" . $data['sAccount42'] . "',
                    '" . $data['sMessage1'] . "',
                    '" . $data['sMessage2'] . "',
                    '" . $data['sMessage3'] . "',
                    '" . $data['sMessage4'] . "',
                    '" . $data['sMessage5'] . "',
                    '" . $data['sMesName1'] . "',
                    '" . $data['sMesName2'] . "',
                    '" . $data['sMesName3'] . "',
                    '" . $data['sMesName4'] . "',
                    '" . $data['sMesName5'] . "',
                    '" . date('Y-m-d H:i:s', time()) . "',
                    '" . date('Y-m-d H:i:s', time()) . "',
                    '" . date('Y-m-d H:i:s', time()) . "',
                    '" . date('Y-m-d H:i:s', time()) . "',
                    '" . $data['sRenote'] . "',
                    '" . $sContractStatus . "',
                    '" . $data['sFeedBack'] . "',
                    '" . $data['sTtitle'] . "',
                    '" . $data['sMobileNum2'] . "',
                    '" . $data['sIdentity'] . "',
                    '" . $data['sIdentityNumber'] . "',
                    '" . $data['sRtitle'] . "',
                    '" . $data['zip3'] . "',
                    '" . $data['addr3'] . "',
                    '" . $data['zip2f'] . "',
                    '" . $data['addr2f'] . "',
                    '" . $data['sEmail2'] . "',
                    '" . $data['sAccountNum5'] . "',
                    '" . $data['sAccountNum6'] . "',
                    '" . $data['sAccount7'] . "',
                    '" . $data['sAccount8'] . "',
                    '" . $data['sfnote'] . "',
                    '" . $data['feedDateCat'] . "',
                    '" . $data['sFeedDateCatSwitch'] . "',
                    '" . $data['sFeedDateCatSwitchDate'] . "',
                    '" . $data['member_name'] . "',
                    '" . $data['sRg'] . "',
                    '" . $data['sRgMoney'] . "',
                    '" . $data['sBirthday'] . "',
                    '" . $sBackDocument . "',
                    '" . $feedbackmoney . "',
                    '" . $data['sBackDocumentNote'] . "',
                    '" . $data['feedbackMark'] . "',
                    '" . $data['sScrivenerBranch'] . "',
                    '" . $data['sScrivenerSystem'] . "',
                    '" . addslashes($data['sScrivenerSystemOther']) . "'
                    ); ";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $newid = $this->dbh->lastInsertId();

        if ($sContractStatus == 1) {
            $tmp = explode('-', $data['sContractStatusTime']);

            $tmp[0]              = $tmp[0] + 1911;
            $sContractStatusTime = $tmp[0] . $tmp[1] . $tmp[2];
            unset($tmp);
            $sql  = "UPDATE tScrivener SET sContractStatusTime = '" . $sContractStatusTime . "' WHERE sId ='" . $newid . "'";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

        ##

        return $newid;
    }

    private function checkScrivenerStatus($id, $status, $checkType = 'status')
    {
        $tf   = false;
        $list = array();

        $sql  = 'SELECT * FROM tScrivener WHERE sId="' . $id . '";';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $list = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($checkType == 'status') {
            if (($list['sStatus'] != $status) && ($status != '1')) {
                $tf = true;
            }
        }
        if ($checkType == 'pending') {
            if ($list['sPendingTime'] == '0000-00-00 00:00:00') {
                $tf = true;
            }
        }

        return $tf;
    }

    public function SaveScrivener($data)
    {
        $brand       = '';
        $invoicecase = '';

        if (stripos($data['sName'], "待停用") or stripos($data['sOffice'], "待停用")) {
            if ($this->checkScrivenerStatus($data['id'], $data['sStatus'], 'pending')) {
                $sPendingTime = date('Y-m-d H:i:s');
                $str          = "`sPendingTime`= '" . $sPendingTime . "',";
            }
        } else {
            $sPendingTime = '0000-00-00 00:00:00';
            $str          = "`sPendingTime`= '" . $sPendingTime . "',";
        }

        if (!empty($data['sBrand'])) {
            $brand = implode(",", $data['sBrand']);
        }
        if (!empty($data['sBank'])) {
            $bank = is_array($data['sBank']) ? implode(",", $data['sBank']) : $data['sBank'];
        }
        if (!empty($data['sBackDocument'])) {
            $sBackDocument = implode(",", $data['sBackDocument']);
        }

        if (!empty($data['sAccountUnused'])) {
            $sAccountUnused = implode(",", $data['sAccountUnused']);
        }

        if (!empty($data['sAccountUnused1'])) {
            $sAccountUnused1 = implode(",", $data['sAccountUnused1']);
        }

        if (!empty($data['sAccountUnused2'])) {
            $sAccountUnused2 = implode(",", $data['sAccountUnused2']);
        }

        $sStatusTime = '';
        if (!empty($data['sStatus']) && !empty($data['id'])) {
            if ($this->checkScrivenerStatus($data['id'], $data['sStatus'])) {
                $sStatusTime = "`sStatusTime` = '" . date('Y-m-d H:i:s', time()) . "',";
            }

        }

        if (is_array($data['sContractStatus'])) {
            $sContractStatus = @implode(',', $data['sContractStatus']);
        }

        if ($sContractStatus != 1) {
            $sContractStatus = 2;
        }

        if ($data['feedDateCat'] != '') {
            $str .= "`sFeedDateCat`= '" . $data['feedDateCat'] . "',";
        }

        if (is_array($data['feedbackmoney'])) {
            $feedbackmoney = @implode(',', $data['feedbackmoney']);
        }

        if (empty($data['sScrivenerBranch'])) {
            $data['sScrivenerBranch'] = null;
        }

        $sql = " UPDATE
                    `tScrivener`
                 SET
                    `sName` = '" . $data['sName'] . "',
                    `sIdentifyId` = '" . $data['sIdentifyId'] . "',
                    `sSerialnum` = '" . $data['sSerialnum'] . "',
                    `sPassword` = '" . $data['sPassword'] . "',
                    `sLicenseExpired` = '" . $data['sLicenseExpired'] . "',
                    `sBrand` = '" . $brand . "',
                    `sBank` = '" . $bank . "',
                    `sCategory` = '" . $data['sCategory'] . "',
                    `sOffice` = '" . $data['sOffice'] . "',
                    `sGuild` = '" . $data['sGuild'] . "',
                    `sTelArea` = '" . $data['sTelArea'] . "',
                    `sTelMain` = '" . $data['sTelMain'] . "',
                    `sTelExt` = '" . $data['sTelExt'] . "',
                    `sTelArea2` = '" . $data['sTelArea2'] . "',
                    `sTelMain2` = '" . $data['sTelMain2'] . "',
                    `sTelExt2` = '" . $data['sTelExt2'] . "',
                    `sFaxArea` = '" . $data['sFaxArea'] . "',
                    `sFaxMain` = '" . $data['sFaxMain'] . "',
                    `sZip1` = '" . $data['zip2'] . "',
                    `sAddress` = '" . $data['addr2'] . "',
                    `sCpZip1` = '" . $data['zip2'] . "',
                    `sCpAddress` = '" . $data['addr2'] . "',
                    `sMobileNum` = '" . $data['sMobileNum'] . "',
                    `sInputDate` = '" . $data['sInputDate'] . "',
                    `sAppointDate` = '" . $data['sAppointDate'] . "',
                    `sOpenDate` = '" . $data['sOpenDate'] . "',
                    `sSaveDate` = '" . $data['sSaveDate'] . "',
                    `sTicketNumber` = '" . $data['sTicketNumber'] . "',
                    `sTicketMoney` = '" . $data['sTicketMoney'] . "',
                    `sEmail` = '" . $data['sEmail'] . "',
                    `sStatus` = '" . $data['sStatus'] . "',
                    `sStatusDateStart` = '" . $data['sStatusDateStart'] . "',
                    `sStatusDateEnd` = '" . $data['sStatusDateEnd'] . "',
                    `sInvoiceCase` = '" . $data['sInvoiceCase'] . "',
                    `sRecall` = '" . $data['sRecall'] . "',
                    `sSpRecall` = '" . $data['sSpRecall'] . "',
                    `sUndertaker1` = '" . $data['sUndertaker1'] . "',
                    `sUndertaker2` = '" . $data['sUndertaker2'] . "',
                    `sDrawer` = '" . $data['sDrawer'] . "',
                    `sTicketRemark` = '" . $data['sTicketRemark'] . "',
                    `sRemark1` = '" . $data['sRemark1'] . "',
                    `sRemark2` = '" . $data['sRemark2'] . "',
                    `sRemark3` = '" . $data['sRemark3'] . "',
                    `sRemark4` = '" . $data['sRemark4'] . "',
                    `sRemark5` = '" . $data['sRemark5'] . "',
                    `sAccountNum1` = '" . $data['sAccountNum1'] . "',
                    `sAccountNum2` = '" . $data['sAccountNum2'] . "',
                    `sAccount3` = '" . $data['sAccount3'] . "',
                    `sAccount4` = '" . $data['sAccount4'] . "',
                    `sAccountNum11` = '" . $data['sAccountNum11'] . "',
                    `sAccountNum21` = '" . $data['sAccountNum21'] . "',
                    `sAccount31` = '" . $data['sAccount31'] . "',
                    `sAccount41` = '" . $data['sAccount41'] . "',
                    `sAccountNum12` = '" . $data['sAccountNum12'] . "',
                    `sAccountNum22` = '" . $data['sAccountNum22'] . "',
                    `sAccount32` = '" . $data['sAccount32'] . "',
                    `sAccount42` = '" . $data['sAccount42'] . "',
                    `sMessage1` = '" . $data['sMessage1'] . "',
                    `sMessage2` = '" . $data['sMessage2'] . "',
                    `sMessage3` = '" . $data['sMessage3'] . "',
                    `sMessage4` = '" . $data['sMessage4'] . "',
                    `sMessage5` = '" . $data['sMessage5'] . "',
                    `sMesName1` = '" . $data['sMesName1'] . "',
                    `sMesName2` = '" . $data['sMesName2'] . "',
                    `sMesName3` = '" . $data['sMesName3'] . "',
                    `sMesName4` = '" . $data['sMesName4'] . "',
                    `sMesName5` = '" . $data['sMesName5'] . "',
					" . $sStatusTime . "
                    `sRenote` = '" . $data['sRenote'] . "',
                    `sAccountUnused` = '" . $sAccountUnused . "',
                    `sAccountUnused1` = '" . $sAccountUnused1 . "',
                    `sAccountUnused2` = '" . $sAccountUnused2 . "',
                    `sFeedBack` = '" . $data['sFeedBack'] . "',
                    `sTtitle` = '" . $data['sTtitle'] . "',
                    `sMobileNum2` = '" . $data['sMobileNum2'] . "',
                    `sIdentity` = '" . $data['sIdentity'] . "',
                    `sIdentityNumber` = '" . $data['sIdentityNumber'] . "',
                    `sRtitle` = '" . $data['sRtitle'] . "',
                    `sZip3` = '" . $data['zip3'] . "',
                    `sAddr3` = '" . $data['addr3'] . "',
                    `sZip2f` = '" . $data['zip2f'] . "',
                    `sAddr2f` = '" . $data['addr2f'] . "',
                    `sEmail2` = '" . $data['sEmail2'] . "',
                    `sAccountNum5` = '" . $data['sAccountNum5'] . "',
                    `sAccountNum6` = '" . $data['sAccountNum6'] . "',
                    `sAccount7` = '" . $data['sAccount7'] . "',
                    `sAccount8` = '" . $data['sAccount8'] . "',
                    `sfnote` = '" . $data['sfnote'] . "',
                    " . $str . "
                    `sFeedDateCatSwitch` = '" . $data['sFeedDateCatSwitch'] . "',
                    `sFeedDateCatSwitchDate` = '" . $data['sFeedDateCatSwitchDate'] . "',
                    `sContractStatus` ='" . $sContractStatus . "',
                    `sRg` = '" . $data['sRg'] . "',
                    `sRgMoney` = '" . $data['sRgMoney'] . "',
                    `sSales` = '" . $data['sSales'] . "',
                    `sSalesDate` = '" . $data['sSalesDate'] . "',
                    `sBirthday` = '" . $data['sBirthday'] . "',
                    `sBackDocument` = '" . $sBackDocument . "',
                    `sFeedbackMoney` = '" . $feedbackmoney . "',
                    `sBackDocumentNote` = '" . $data['sBackDocumentNote'] . "',
                    `sFeedbackMark` = '" . $data['feedbackMark'] . "',
                    `sScrivenerBranch` = '" . $data['sScrivenerBranch'] . "',
                    `sScrivenerSystem` = '" . $data['sScrivenerSystem'] . "',
                    `sScrivenerSystemOther` = '" . addslashes($data['sScrivenerSystemOther']) . "'
                    WHERE `sId` = '" . $data['id'] . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

}
