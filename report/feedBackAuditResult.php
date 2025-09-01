<?php
header("Content-Type:text/html; charset=utf-8");

$_POST = escapeStr($_POST);

$cat   = ($_POST['cat'] == 1) ? 0 : 1;
$sales = empty($_POST["sales"]) ? $_SESSION['member_id'] : $_POST["sales"];

$sEndDate = $_POST['sEndDate'];
$eEndDate = $_POST['eEndDate'];

if ($sEndDate) {
    $tmp      = explode('-', $sEndDate);
    $sEndDate = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}

if ($eEndDate) {
    $tmp      = explode('-', $eEndDate);
    $eEndDate = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
}

$cCertifiedId = $_POST['cCertifiedId'];

$str = '';
if ($cCertifiedId) {
    $str = " AND fCertifiedId = '" . $cCertifiedId . "'";
}

if ($_POST["sales"] || $_SESSION['memeber_pDep'] == 7) {
    $str = " AND fCreator = '" . $sales . "'";
}

if ($_POST['payByCase'] == 1) {
    $paybycase       = new First1\V1\PayByCase\PayByCase;
    $certifiedIdsStr = $paybycase->getCertifiedId();

    $str .= " AND FIND_IN_SET(`fCertifiedId`, '" . $certifiedIdsStr . "')";
} else if ($_POST['payByCase'] == 2) {
    $confirmFeedback       = new First1\V1\ConfirmFeedback\ConfirmFeedback;
    $certifiedIdsStr = $confirmFeedback->getCertifiedId();

    $str .= " AND FIND_IN_SET(`fCertifiedId`, '" . $certifiedIdsStr . "')";
}

$sql = "SELECT
			fCertifiedId,
			fId,
			(SELECT pName FROM tPeopleInfo WHERE pId = fCreator) AS fCreator,
			fApplyTime,
			(SELECT pName FROM tPeopleInfo WHERE pId = fAuditor) AS fAuditor,
			fAuditorTime,
			(SELECT cFeedBackClose FROM tContractCase WHERE cCertifiedId = fCertifiedId) AS close,
			fNote,
            fTotalMoney,
            fCertifiedMoney
		FROM
			tFeedBackMoneyReview WHERE fStatus = '" . $cat . "'  AND fFail = 0" . $str;
$rs = $conn->Execute($sql);

$i = 0;
while (!$rs->EOF) {
    if (getCaseEndDate($rs->fields['fCertifiedId'], $sEndDate, $eEndDate)) {
        $list[$i]                 = getCaseDetail($rs->fields['fCertifiedId']);
        $list[$i]['ReviewId']     = $rs->fields['fId'];
        $list[$i]['fCreator']     = $rs->fields['fCreator'];
        $list[$i]['fApplyTime']   = $rs->fields['fApplyTime'];
        $list[$i]['fAuditor']     = $rs->fields['fAuditor'];
        $list[$i]['fAuditorTime'] = $rs->fields['fAuditorTime'];
        $list[$i]['close']        = $rs->fields['close'];
        $list[$i]['fNote']        = $rs->fields['fNote'];
        $list[$i]['fTotalMoney']  = $rs->fields['fTotalMoney'];
        $list[$i]['fCertifiedMoney'] = $rs->fields['fCertifiedMoney'];
        $i++;
    }

    $rs->MoveNext();
}

for ($i = 0; $i < count($list); $i++) {
    $j   = 0;
    $sql = "SELECT *,
                (SELECT bStore FROM `tBranch` WHERE `tBranch`.bId = `tFeedBackMoneyReviewList`.fIndividualId) AS fIndividualName, 
                (SELECT bIndividualRate FROM `tBranch` WHERE `tBranch`.bId = `tFeedBackMoneyReviewList`.fIndividualId) AS fIndividuaRecall 
            FROM tFeedBackMoneyReviewList WHERE fCertifiedId = '" . $list[$i]['cCertifiedId'] . "' AND fRId = '" . $list[$i]['ReviewId'] . "'  ORDER BY fCategory ASC"; //AND fDelete = 0
    $rs  = $conn->Execute($sql);

    $_certifiedMoney = (int) $list[$i]['cCertifiedMoney']; //合約保證費、計算各店家回饋比率用
    while (!$rs->EOF) {
        if ($rs->fields['fCategory'] == 1) {
            $list[$i]['aFeedBackMoney']  = $rs->fields['fCaseFeedBackMoney'];
            $list[$i]['aCaseFeedback']   = $rs->fields['fCaseFeedback'];
            $list[$i]['aFeedbackTarget'] = $rs->fields['fFeedbackTarget'];
            $list[$i]['feedbackRate']    = round(((int) $list[$i]['aFeedBackMoney'] / $_certifiedMoney) * 100, 2);

            //如果回饋金有異動就標記紅色
            if ($list[$i]['aFeedBackMoney'] != $list[$i]['cCaseFeedBackMoney'] || $list[$i]['aCaseFeedback'] != $list[$i]['cCaseFeedback'] || $list[$i]['aFeedbackTarget'] != $list[$i]['cFeedbackTarget']) {
                $list[$i]['change'] = "color:red";
            }

            //仲介回饋但沒有合契 1:符合條件  (bCooperationHas = 1 有合契)
            $list[$i]['BranchFeedBackStatus']      = ($list[$i]['BranchCooperationHas'] == 0 && $list[$i]['aCaseFeedback'] == 0 && $list[$i]['cBranchNum'] != 505 && $list[$i]['aFeedbackTarget'] == 1 && $list[$i]['cBrand'] != 1) ? 1 : 0;
            $list[$i]['BranchFeedBackStatusColor'] = ($list[$i]['BranchFeedBackStatus'] == 1) ? 'yellow' : '';

        } elseif ($rs->fields['fCategory'] == 2) {
            $list[$i]['aFeedBackMoney1']  = $rs->fields['fCaseFeedBackMoney'];
            $list[$i]['aCaseFeedback1']   = $rs->fields['fCaseFeedback'];
            $list[$i]['aFeedbackTarget1'] = $rs->fields['fFeedbackTarget'];
            $list[$i]['feedbackRate1']    = round(((int) $list[$i]['aFeedBackMoney1'] / $_certifiedMoney) * 100, 2);

            if ($list[$i]['aFeedBackMoney1'] != $list[$i]['cCaseFeedBackMoney1'] || $list[$i]['aCaseFeedback1'] != $list[$i]['cCaseFeedback1'] || $list[$i]['aFeedbackTarget1'] != $list[$i]['cFeedbackTarget1']) {
                $list[$i]['change1'] = "color:red";
            }

            //仲介回饋但沒有合契 1:符合條件  (bCooperationHas = 1 有合契)
            $list[$i]['BranchFeedBackStatus1']      = ($list[$i]['BranchCooperationHas1'] == 0 && $list[$i]['aCaseFeedback1'] == 0 && $list[$i]['cBranchNum1'] != 505 && $list[$i]['aFeedbackTarget1'] == 1 && $list[$i]['cBrand1'] != 1) ? 1 : 0;
            $list[$i]['BranchFeedBackStatusColor1'] = ($list[$i]['BranchFeedBackStatus1'] == 1) ? 'yellow' : '';

        } elseif ($rs->fields['fCategory'] == 3) {
            $list[$i]['aFeedBackMoney2']  = $rs->fields['fCaseFeedBackMoney'];
            $list[$i]['aCaseFeedback2']   = $rs->fields['fCaseFeedback'];
            $list[$i]['aFeedbackTarget2'] = $rs->fields['fFeedbackTarget'];
            $list[$i]['feedbackRate2']    = round(((int) $list[$i]['aFeedBackMoney2'] / $_certifiedMoney) * 100, 2);

            if ($list[$i]['aFeedBackMoney2'] != $list[$i]['cCaseFeedBackMoney2'] || $list[$i]['aCaseFeedback2'] != $list[$i]['cCaseFeedback2'] || $list[$i]['aFeedbackTarget2'] != $list[$i]['cFeedbackTarget2']) {
                $list[$i]['change2'] = "color:red";
            }

            //仲介回饋但沒有合契 1:符合條件  (bCooperationHas = 1 有合契)
            $list[$i]['BranchFeedBackStatus2']      = ($list[$i]['BranchCooperationHas2'] == 0 && $list[$i]['aCaseFeedback2'] == 0 && $list[$i]['cBranchNum2'] != 505 && $list[$i]['aFeedbackTarget2'] == 1 && $list[$i]['cBrand2'] != 1) ? 1 : 0;
            $list[$i]['BranchFeedBackStatusColor2'] = ($list[$i]['BranchFeedBackStatus2'] == 1) ? 'yellow' : '';
        } elseif ($rs->fields['fCategory'] == 6) {
            $list[$i]['aFeedBackMoney3']  = $rs->fields['fCaseFeedBackMoney'];
            $list[$i]['aCaseFeedback3']   = $rs->fields['fCaseFeedback'];
            $list[$i]['aFeedbackTarget3'] = $rs->fields['fFeedbackTarget'];
            $list[$i]['feedbackRate3']    = round(((int) $list[$i]['aFeedBackMoney3'] / $_certifiedMoney) * 100, 2);

            if ($list[$i]['aFeedBackMoney3'] != $list[$i]['cCaseFeedBackMoney3'] || $list[$i]['aCaseFeedback3'] != $list[$i]['cCaseFeedback3'] || $list[$i]['aFeedbackTarget3'] != $list[$i]['cFeedbackTarget3']) {
                $list[$i]['change3'] = "color:red";
            }

            //仲介回饋但沒有合契 1:符合條件  (bCooperationHas = 1 有合契)
            $list[$i]['BranchFeedBackStatus3']      = ($list[$i]['BranchCooperationHas3'] == 0 && $list[$i]['aCaseFeedback3'] == 0 && $list[$i]['cBranchNum3'] != 505 && $list[$i]['aFeedbackTarget3'] == 1) ? 1 : 0;
            $list[$i]['BranchFeedBackStatusColor3'] = ($list[$i]['BranchFeedBackStatus3'] == 1) ? 'yellow' : '';
        } elseif ($rs->fields['fCategory'] == 4) {
            $list[$i]['aScrivnerSpFeedBackMoney'] = $rs->fields['fCaseFeedBackMoney'];
            $list[$i]['feedbackRateSP']           = round(((int) $list[$i]['aScrivnerSpFeedBackMoney'] / $_certifiedMoney) * 100, 2);

            if ($list[$i]['aScrivnerSpFeedBackMoney'] != $list[$i]['ScrivenerSPFeedMoney']) {
                $list[$i]['changesp'] = "color:red";
            }
        } elseif ($rs->fields['fCategory'] == 5) {
            if ($rs->fields['fDelete'] == 0) {
                $target = ($rs->fields['fFeedbackTarget'] == 1) ? '2' : '1';

                $list[$i]['otherFeed'][$j]                       = getStoreData($target, $rs->fields['fFeedbackStoreId']);
                $list[$i]['otherFeed'][$j]['fCaseFeedBackMoney'] = $rs->fields['fCaseFeedBackMoney'];
                $list[$i]['otherFeed'][$j]['fFeedbackTarget']    = ($rs->fields['fFeedbackTarget'] == 1) ? '2' : '1';
                $list[$i]['otherFeed'][$j]['feedbackRateOther']  = round(((int) $list[$i]['otherFeed'][$j]['fCaseFeedBackMoney'] / $_certifiedMoney) * 100, 2);
                $list[$i]['otherFeed'][$j]['fCaseFeedBackNote']  = $rs->fields['fCaseFeedBackNote'];

                $j++;

                $target = null;unset($target);
            } else {
                $target = ($rs->fields['fFeedbackTarget'] == 1) ? '2' : '1';

                if ($rs->fields['fCaseFeedBackMark'] != '') {
                    if (empty($list[$i]['otherFeedDel'])) {
                        $list[$i]['otherFeedDel'] = array();
                    }

                    $delData                       = getStoreData($target, $rs->fields['fFeedbackStoreId']);
                    $delData['fCaseFeedBackMoney'] = $rs->fields['fCaseFeedBackMoney'];
                    $delData['fFeedbackTarget']    = ($rs->fields['fFeedbackTarget'] == 1) ? '2' : '1';
                    $delData['feedbackRateOther']  = round(((int) $delData['fCaseFeedBackMoney'] / $_certifiedMoney) * 100, 2);

                    $delData['fCaseFeedBackNote'] = $rs->fields['fCaseFeedBackNote'];

                    array_push($list[$i]['otherFeedDel'], $delData);

                    $delData = null;unset($delData);
                }
            }
        } elseif ($rs->fields['fCategory'] == 7) {

            $no = 'N';
            if($rs->fields['fFeedbackStoreId'] == $list[$i]['cBranchNum']) $no = '';
            if($rs->fields['fFeedbackStoreId'] == $list[$i]['cBranchNum1']) $no = 1;
            if($rs->fields['fFeedbackStoreId'] == $list[$i]['cBranchNum2']) $no = 2;
            if($rs->fields['fFeedbackStoreId'] == $list[$i]['cBranchNum3']) $no = 3;


            if($no != 'N' ) {
                if($no == 0) $no = '';
                $list[$i]['individualName'.$no][] = $rs->fields['fIndividualName'];
                $list[$i]['individualRecall'.$no][] = $rs->fields['fIndividuaRecall'];
                $list[$i]['individualMoney'.$no][] = $rs->fields['fCaseFeedBackMoney'];
                $list[$i]['individualRate'.$no][] = round(($rs->fields['fCaseFeedBackMoney'] / $_certifiedMoney) * 100, 2);
            }
        }

        $rs->MoveNext();
    }

    $list[$i]['otherFeedCount'] = $j;

    $_certifiedMoney = null;unset($_certifiedMoney);
}
$max = count($list);

function getCaseDetail($cId, $sDate = '', $eDate = '')
{
    global $conn;

    $query = ' cc.cCertifiedId<>"" AND cc.cCaseStatus<>"8" AND cc.cCertifiedId !="005030342" AND cc.cCertifiedId = "' . $cId . '"';

    // 搜尋條件-結案日期
    if ($sDate) {
        $tmp   = explode('-', $sDate);
        $sDate = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
        $tmp   = null;unset($tmp);

        $query .= empty($query) ? '' : ' AND ';
        $query .= ' cc.cEndDate>="' . $sDate . ' 00:00:00" ';
    }
    if ($eDate) {
        $tmp   = explode('-', $eDate);
        $eDate = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2];
        $tmp   = null;unset($tmp);

        $query .= empty($query) ? '' : ' AND ';
        $query .= ' cc.cEndDate<="' . $eDate . ' 23:59:59" ';
    }

    if ($query) {
        $query = ' WHERE ' . $query;
    }

    $query = 'SELECT
                cc.cCertifiedId AS cCertifiedId,
                inc.cTotalMoney AS cTotalMoney,
                inc.cCertifiedMoney as cCertifiedMoney,
                inc.cFirstMoney as cFirstMoney,
                (SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS BranchName,
                (SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS BranchName1,
                (SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS BranchName2,
                (SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum3) AS BranchName3,
                (SELECT bRecall FROM tBranch WHERE bId = cr.cBranchNum) AS bRecall,
                (SELECT bRecall FROM tBranch WHERE bId = cr.cBranchNum1) AS bRecall1,
                (SELECT bRecall FROM tBranch WHERE bId = cr.cBranchNum2) AS bRecall2,
                (SELECT bRecall FROM tBranch WHERE bId = cr.cBranchNum3) AS bRecall3,
                (SELECT bScrRecall FROM tBranch WHERE bId = cr.cBranchNum) AS bScrRecall,
                (SELECT bScrRecall FROM tBranch WHERE bId = cr.cBranchNum1) AS bScrRecall1,
                (SELECT bScrRecall FROM tBranch WHERE bId = cr.cBranchNum2) AS bScrRecall2,
                (SELECT bScrRecall FROM tBranch WHERE bId = cr.cBranchNum3) AS bScrRecall3,
                (SELECT bCooperationHas FROM tBranch WHERE bId = cr.cBranchNum) AS BranchCooperationHas,
                (SELECT bCooperationHas FROM tBranch WHERE bId = cr.cBranchNum1) AS BranchCooperationHas1,
                (SELECT bCooperationHas FROM tBranch WHERE bId = cr.cBranchNum2) AS BranchCooperationHas2,
                (SELECT bCooperationHas FROM tBranch WHERE bId = cr.cBranchNum3) AS BranchCooperationHas3,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS BrandName,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS BrandName1,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS BrandName2,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand3) AS BrandName3,
                (SELECT bCode FROM tBrand WHERE bId = cr.cBrand) AS bCode,
                (SELECT bCode FROM tBrand WHERE bId = cr.cBrand1) AS bCode1,
                (SELECT bCode FROM tBrand WHERE bId = cr.cBrand2) AS bCode2,
                (SELECT bCode FROM tBrand WHERE bId = cr.cBrand3) AS bCode3,
                (SELECT (SELECT zCity FROM tZipArea WHERE zZip = bZip) FROM tBranch WHERE bId = cr.cBranchNum) AS branchCity,
                (SELECT (SELECT zCity FROM tZipArea WHERE zZip = bZip) FROM tBranch WHERE bId = cr.cBranchNum1) AS branchCity1,
                (SELECT (SELECT zCity FROM tZipArea WHERE zZip = bZip) FROM tBranch WHERE bId = cr.cBranchNum2) AS branchCity2,
                (SELECT (SELECT zCity FROM tZipArea WHERE zZip = bZip) FROM tBranch WHERE bId = cr.cBranchNum3) AS branchCity3,
                cr.cBrand,
                cr.cBrand1,
                cr.cBrand2,
                cr.cBrand3,
                cr.cBranchNum,
                cr.cBranchNum1,
                cr.cBranchNum2,
                cr.cBranchNum3,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS BrandName,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS BrandName1,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS BrandName2,
                (SELECT bName FROM tBrand WHERE bId = cr.cBrand3) AS BrandName3,
                cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
                cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
                cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
                cc.cCaseFeedBackMoney3 AS cCaseFeedBackMoney3,
                cc.cSpCaseFeedBackMoney AS ScrivenerSPFeedMoney,
                cc.cSpCaseFeedBackMoneyMark AS cSpCaseFeedBackMoneyMark,
                cc.cCaseFeedback AS cCaseFeedback,
                cc.cCaseFeedback1 AS cCaseFeedback1,
                cc.cCaseFeedback2 AS cCaseFeedback2,
                cc.cCaseFeedback3 AS cCaseFeedback3,
                cc.cFeedbackTarget AS cFeedbackTarget,
                cc.cFeedbackTarget1 AS cFeedbackTarget1,
                cc.cFeedbackTarget2 AS cFeedbackTarget2,
                cc.cFeedbackTarget3 AS cFeedbackTarget3,
                cc.cScrivenerSpRecall,
                cs.cScrivener,
                (SELECT sName FROM tScrivener WHERE sId = cs.cScrivener) AS sName,
                (SELECT sOffice FROM tScrivener WHERE sId = cs.cScrivener) AS sOffice,
                (SELECT (SELECT zCity FROM tZipArea WHERE zZip = sCpZip1) FROM tScrivener WHERE sId = cs.cScrivener) AS scrivenerCity,
                cc.cCaseFeedBackModifier,
                buy.cName AS buyer,
                own.cName AS owner,
                inc.cTotalMoney,
                (SELECT sName FROM tStatusCase AS sc WHERE sc.sId=cc.cCaseStatus) AS status,
                scr.sRecall,
                scr.sSpRecall AS sSpRecall1
            FROM
                tContractCase AS cc
            LEFT JOIN
                tContractBuyer AS buy ON buy.cCertifiedId=cc.cCertifiedId
            LEFT JOIN
                tContractOwner AS own ON own.cCertifiedId=cc.cCertifiedId
            LEFT JOIN
                tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
            LEFT JOIN
                tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
            LEFT JOIN
                tContractProperty AS pro ON pro.cCertifiedId=cc.cCertifiedId
            LEFT JOIN
                tContractIncome AS inc ON inc.cCertifiedId=cc.cCertifiedId
            LEFT JOIN
                tZipArea AS zip ON zip.zZip=pro.cZip
            LEFT JOIN
                tScrivener AS scr ON scr.sId = cs.cScrivener
            ' . $query . '
            GROUP BY
                cc.cCertifiedId
            ORDER BY
                cc.cApplyDate,cc.cId,cc.cSignDate ASC;';
    $rs = $conn->Execute($query);

    $detail = $rs->fields;

    $salesCheck = array();
    $storeCount = 0;
    $check      = '';

    if ($detail['cBranchNum'] > 0) {
        if ($detail['cFeedbackTarget'] == 2 || $detail['cBranchNum'] == 505) { //scrivener
            $check = getAreaSales($detail['scrivenerCity']);

            $salesCheck[$check]++;
        } else {
            $check = getAreaSales($detail['branchCity']);

            $salesCheck[$check]++;
        }

        $storeCount++;
    }

    if ($detail['cBranchNum1'] > 0) {
        if ($detail['cFeedbackTarget1'] == 2) { //scrivener
            $check = getAreaSales($detail['scrivenerCity']);
            $salesCheck[$check]++;
        } else {
            $check = getAreaSales($detail['branchCity1']);
            $salesCheck[$check]++;
        }

        $storeCount++;
    }

    if ($detail['cBranchNum2'] > 0) {
        if ($detail['cFeedbackTarget2'] == 2) { //scrivener
            $check = getAreaSales($detail['scrivenerCity']);
            $salesCheck[$check]++;
        } else {
            $check = getAreaSales($detail['branchCity2']);
            $salesCheck[$check]++;
        }

        $storeCount++;
    }

    if ($detail['cBranchNum3'] > 0) {
        if ($detail['cFeedbackTarget3'] == 2) { //scrivener
            $check = getAreaSales($detail['scrivenerCity']);
            $salesCheck[$check]++;
        } else {
            $check = getAreaSales($detail['branchCity3']);
            $salesCheck[$check]++;
        }

        $storeCount++;
    }

    $detail['checkSalesArea'] = 1;
    if ($storeCount > 1 && count($salesCheck) > 1) {
        $detail['checkSalesArea'] = 0;
    }

    if ($detail['ScrivenerSPFeedMoney'] > 0) {
        $detail['sSpRecall'] = '';
    } else {
        $detail['sSpRecall'] = 'none';
    }

    return $detail;
}

function getAreaSales($city)
{
    global $conn;

    $sql = "SELECT zSales FROM tZipArea WHERE zCity = '" . $city . "'";
    $rs  = $conn->Execute($sql);

    $exp = explode(',', $rs->fields['zSales']);

    $sales = array();
    foreach ($exp as $v) {
        array_push($sales, $v);
    }

    sort($sales);

    return implode(',', $sales);
}

function getCertifyDate($id, $date, $date2)
{
    global $conn;

    if (empty($date) || empty($date2)) {
        return true;
    }

    $sql = 'SELECT
                tBankLoansDate
            FROM
                tBankTrans
            WHERE
                tMemo="' . $id . '"
                AND tPayOk="1"
                AND tKind = "保證費"
                AND tBankLoansDate>="' . $date . '" AND tBankLoansDate<="' . $date2 . '"
                AND tMemo != "000000000"
            ORDER BY
                tExport_time
            ASC;';
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total > 0) {
        return true;
    }

    $_sql  = 'SELECT cCertifiedId FROM tContractCase WHERE cBankList >= "' . $date . '" AND cBankList <= "' . $date2 . '";';
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    return ($total > 0) ? true : false;
}

function getCaseEndDate($id, $date, $date2)
{
    global $conn;

    if (empty($date) || empty($date2)) {
        return true;
    }

    //取得合約銀行帳號
    $_sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" GROUP BY cBankAccount ORDER BY cId ASC;';
    $rs   = $conn->Execute($_sql);

    while (!$rs->EOF) {
        $conBank[] = $rs->fields['cBankAccount'];
        $rs->MoveNext();
    }

    $conBank_sql = implode('","', $conBank);

    $sql   = 'SELECT tra.tMemo as cCertifiedId FROM tBankTrans AS tra WHERE tra.tAccount IN ("' . $conBank_sql . '") AND tra.tKind = "保證費" AND tMemo = "' . $id . '" AND (tra.tBankLoansDate>="' . $date . '" AND tra.tBankLoansDate<="' . $date2 . '");';
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total == 0) {
        $sql = 'SELECT cBankList FROM tContractCase WHERE cCertifiedId = "' . $id . '" AND (cBankList >="' . $date . '" AND cBankList <="' . $date2 . '");';
        $rs  = $conn->Execute($sql);

        $total = $rs->RecordCount();
    }

    return ($total > 0) ? true : false;
}
