<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/first1DB.php';

//預載log物件
$logs = new Intolog();
$tlog = new TraceLog();
##

$sn = trim(addslashes($_REQUEST['sn']));

$conn = new first1DB();

if ($_REQUEST['save_ok']) {
    $conditions = '';

    $sn                    = $_POST['cCertifiedId'];
    $caseStatus            = $_POST['caseStatus'];
    $signDate              = $_POST['signDate'];
    $owner                 = $_POST['owner'];
    $ownerID               = $_POST['ownerID'];
    $buyer                 = $_POST['buyer'];
    $buyerID               = $_POST['buyerID'];
    $zZip                  = $_POST['addrZip'];
    $cAddr                 = $_POST['cAddr'];
    $cSignMoney            = $_POST['cSignMoney'];
    $cAffixMoney           = $_POST['cAffixMoney'];
    $cDutyMoney            = $_POST['cDutyMoney'];
    $cEstimatedMoney       = $_POST['cEstimatedMoney'];
    $cTotalMoney           = $_POST['cTotalMoney'];
    $cCertifiedMoney       = $_POST['cCertifiedMoney'];
    $cRealestateMoney      = $_POST['cRealestateMoney'];
    $cAdvanceMoney         = $_POST['cAdvanceMoney'];
    $cDealMoney            = $_POST['cDealMoney'];
    $cScrivenerMoney       = $_POST['cScrivenerMoney'];
    $cRealestateMoneyBuyer = $_POST['cRealestateMoneyBuyer'];
    $cAdvanceMoneyBuyer    = $_POST['cAdvanceMoneyBuyer'];
    $cDealMoneyBuyer       = $_POST['cDealMoneyBuyer'];
    $cScrivenerMoneyBuyer  = $_POST['cScrivenerMoneyBuyer'];
    $cCaseProcessing       = $_POST['cCaseProcessing'];
    $owner_agent           = $_POST['owner_agent'];
    $buyer_agent           = $_POST['buyer_agent'];
    $branch                = $_POST['recall_branch'];
    $branch1               = $_POST['recall_branch1'];
    $branch2               = $_POST['recall_branch2'];
    $recall                = $_POST['recall'];
    $recall1               = $_POST['recall1'];
    $recall2               = $_POST['recall2'];
    $cCMChange             = $_POST['cCMChange'];
    $cFeedbackTarget       = $_POST['cFeedbackTarget'];
    $sRecall               = $_POST['sRecall'];
    $cAuthorized           = $_POST['buyer_authorized'];

    //檢核實收履保費是否大於應收履保費
    $sql        = 'SELECT cCaseFeedBackModifier FROM tContractCase WHERE cCertifiedId="' . $sn . '";';
    $fbModifier = $conn->one($sql);
    $modi       = $fbModifier['cCaseFeedBackModifier'];

    if (($modi == '') || preg_match("/i$/", $modi)) { //當回饋金無人為修改或由出款建檔前頁面修改時
        $title_cer = $cTotalMoney * 0.0006; //名義履保費 = 總價金 * 萬分之6
        if (($cCertifiedMoney + 10) > $title_cer) { //計算回饋金分配(+10元誤差)
            $caseFB = 0; //本案件須回饋

            if ($branch && $branch1 && $branch2) {
                $branchArr = array($branch, $branch1, $branch2); //轉成陣列(三家仲介)
                // echo "A<br>\n" ;
            } else if ($branch && $branch1 && ($branch2 == '')) {
                $branchArr = array($branch, $branch1); //轉成陣列(二家仲介)
                // echo "B<br>\n" ;
            } else {
                $branchArr = array($branch); //轉成陣列(一家仲介)
                // echo "C<br>\n" ;
            }
            ##

            if ($cTotalMoney <= 1000000) { //當總價金小於等於100萬時，回饋金為200
                //以最小比率做為回饋金計算比率

                $bFb = 200;
                // echo 'bFb='.$bFb ;                //總回饋金
            } else { //當總價金大於100萬時，回饋金需計算求得
                $val = 33.33;
                if ($cFeedbackTarget == '2') {
                    //回饋對象為代書
                    // $val = $sRecall / 10000 ;                //換算為萬分之x
                    if ($sRecall == '') {$sRecall = $val;} //為空預設33.33

                    $val = $sRecall / 100; //改為百分之X
                    // $bFb = round($cTotalMoney * $val) ;        //總回饋金

                    $bFb = round($cCertifiedMoney * $val); //總回饋金
                    ##
                } else {
                    if ($recall == '') {
                        $recall = $val;
                    }
                    //回饋對象為仲介身分
                    if ($branch && $branch1 && $branch2) { //三家仲介

                        if ($recall1 == '') {$recall1 = $val;}
                        if ($recall2 == '') {$recall2 = $val;}

                        $branchArr = array($recall, $recall1, $recall2); //轉成陣列並由小到大排列
                        sort($branchArr);
                        $val = $branchArr[0]; //取出最小值做為回饋金比率
                        // $val = 33 ;                                                //當配件案件時，回饋比率強制設定為2
                    } else if ($branch && $branch1 && ($branch2 == '')) { //兩家仲介
                        if ($recall1 == '') {$recall1 = $val;}

                        $branchArr = array($recall, $recall1); //轉成陣列並由小到大排列
                        sort($branchArr);
                        $val = $branchArr[0]; //取出最小值做為回饋金比率
                        // $val = 33 ;                                                //當配件案件時，回饋比率強制設定為2
                    } else { //只有一家仲介
                        $branchArr = array($recall);
                        $val       = $branchArr[0]; //當非配件時，回饋比率依據仲介店定義
                    }
                    ##

                    // $val /= 10000 ;                            //換算為萬分之x
                    $val = $val / 100; //20150121
                    // $bFb = round($cTotalMoney * $val) ;        //總回饋金
                    $bFb = round($cCertifiedMoney * $val); //總回饋金
                }
            }

            //完成重新計算回饋金鰾預備寫入資料庫
            if ($cFeedbackTarget == '2') { //回饋對象為代書
                $branch_sql .= ' cCaseFeedBackMoney="' . $bFb . '", ';
            } else { //回饋對象為仲介
                //計算各家仲介回饋金
                $bMax = count($branchArr); //仲介店總數
                $bq   = $bFb % $bMax; //餘數
                $br   = floor($bFb / $bMax); //商數

                $branch_sql = '';
                foreach ($branchArr as $k => $v) {
                    if ($k == 0) {
                        $branchArr[$k] = $br + $bq; //首家仲介 = 商數 + 餘數
                        $branch_sql .= ' cCaseFeedBackMoney="' . ($br + $bq) . '", ';
                    } else {
                        $branchArr[$k] = $br; //其餘仲介 = 商數
                        $branch_sql .= ' cCaseFeedBackMoney' . $k . '="' . $br . '", ';
                    }
                }
                ##
            }
            ##特殊回饋金計算
            $sql = "SELECT cr.cBrand,cr.cBrand1,cr.cBrand2,scrivener.sSpRecall FROM  `tContractCase` AS cc
				JOIN tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
				JOIN tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
				JOIN tScrivener AS scrivener ON scrivener.sId=cs.cScrivener
				WHERE cc.`cCertifiedId` =  '" . $sn . "'";

            $tmp   = $conn->one($sql);
            $check = 0;
            if ($tmp['cBrand'] != 1 && $tmp['cBrand'] != 49 && $tmp['cBrand'] != 2) { //不是優美跟台屋的//不為非仲介成交

                $check = 1;

            } elseif ($tmp['cBrand1'] != 1 && $tmp['cBrand1'] != 49 && $tmp['cBrand1'] != 0 && $tmp['cBrand1'] != 2) {
                $check = 1;
            } elseif ($tmp['cBrand2'] != 1 && $tmp['cBrand2'] != 49 && $tmp['cBrand2'] != 0 && $tmp['cBrand2'] != 2) {
                $check = 1;
            }

            if ($tmp['sSpRecall'] != 0 && $check == 1) {
                // $val = $tmp['sSpRecall'] / 10000 ;                //換算為萬分之x
                $val = $tmp['sSpRecall'] / 100; //百分之X
                // $spFb = round($cTotalMoney * $val) ;//總回饋金

                $spFb = round($cCertifiedMoney * $val); //總回饋金
                $branch_sql .= 'cSpCaseFeedBackMoney = ' . $spFb . ',';

            }

            ##
        } else {
            $caseFB     = 1; //本案件不回饋
            $branch_sql = ' cCaseFeedBackMoney="0", cCaseFeedBackMoney1="0", cCaseFeedBackMoney2="0", ';
        }

        //若重新分配過回饋金時，紀錄是誰更動履保費導致重新計算回饋金
        if ($cCMChange == 'ok') {
            $sql_cCMChange = ' cCaseFeedBackModifier="' . $_SESSION['member_id'] . 'i", ';
        }
        ##
    }

    // 買方姓名、ID 更新
    $sql = '
		UPDATE
			tContractBuyer
		SET
			cName="' . $buyer . '",
			cIdentifyId="' . $buyerID . '",
			cAuthorized="' . $cAuthorized . '",
			cContactName="' . $buyer_agent . '"
		WHERE
			cCertifiedId="' . $sn . '" ; ';

    $conn->exeSql($sql);
    $tlog->updateWrite($_SESSION['member_id'], $sql, '合約書修改 Inquire');
    ##

    // 賣方姓名、ID 更新
    $sql = '
		UPDATE
			tContractOwner
		SET
			cName="' . $owner . '",
			cIdentifyId="' . $ownerID . '",
			cContactName="' . $owner_agent . '"
		WHERE
			cCertifiedId="' . $sn . '" ; ';
    $conn->exeSql($sql);
    $tlog->updateWrite($_SESSION['member_id'], $sql, '合約書修改 Inquire');
    ##

    // 案件狀態
    if ($signDate) {
        $tmp      = explode('-', $signDate);
        $signDate = ($tmp[0] + 1911) . '-' . $tmp[1] . '-' . $tmp[2] . ' 00:00:00';
        unset($tmp);
    }

    $case_date = '';
    if (($caseStatus == '3') || ($caseStatus == '4') || ($caseStatus == '8')) {
        $case_date = 'cEndDate="' . date("Y-m-d H:i:s") . '",';
    }

    $sql = '
	UPDATE
		tContractCase
	SET
		cCaseStatus="' . $caseStatus . '",
		cCaseFeedback="' . $caseFB . '",
		' . $branch_sql . '
		' . $sql_cCMChange . '
		cLastEditor="' . $_SESSION['member_id'] . '",
		cLastTime="' . date("Y-m-d H:i:s") . '",
		cCaseProcessing="' . $cCaseProcessing . '",
		' . $case_date . '
		cSignDate="' . $signDate . '"
	WHERE
		cCertifiedId="' . $sn . '" ; ';
    $conn->exeSql($sql);
    $tlog->updateWrite($_SESSION['member_id'], $sql, '合約書修改 Inquire');
    ##

    // 標的物坐落
    for ($i = 0; $i < count($_POST['cItem']); $i++) {
        $sql = '
			UPDATE
				tContractProperty
			SET
				cZip="' . $_POST['addrZip'][$i] . '",
				cAddr="' . $_POST['cAddr'][$i] . '"
			WHERE
				 cItem ="' . $_POST['cItem'][$i] . '"
			AND	cCertifiedId="' . $sn . '" ; ';

        $conn->exeSql($sql);
        $tlog->updateWrite($_SESSION['member_id'], $sql, '合約書修改 Inquire');
    }
    $conditions_addr1 = implode(';', $_POST['addrZip']);
    $conditions_addr2 = implode(';', $_POST['cAddr']);
    // die();
    ##

    // 各期價款
    $sql = '
	UPDATE
		tContractIncome
	SET
		cSignMoney="' . $cSignMoney . '",
		cAffixMoney="' . $cAffixMoney . '",
		cDutyMoney="' . $cDutyMoney . '",
		cEstimatedMoney="' . $cEstimatedMoney . '",
		cTotalMoney="' . $cTotalMoney . '",
		cCertifiedMoney="' . $cCertifiedMoney . '"
	WHERE
		cCertifiedId="' . $sn . '" ; ';
    $conn->exeSql($sql);
    $tlog->updateWrite($_SESSION['member_id'], $sql, '合約書修改 Inquire');
    ##

    // 代收款項
    $sql = '
	UPDATE
		  tContractExpenditure
	SET
		cRealestateMoney="' . $cRealestateMoney . '",
		cAdvanceMoney="' . $cAdvanceMoney . '",
		cDealMoney="' . $cDealMoney . '",
		cScrivenerMoney="' . $cScrivenerMoney . '",
		cRealestateMoneyBuyer="' . $cRealestateMoneyBuyer . '",
		cAdvanceMoneyBuyer="' . $cAdvanceMoneyBuyer . '",
		cDealMoneyBuyer="' . $cDealMoneyBuyer . '",
		cScrivenerMoneyBuyer="' . $cScrivenerMoneyBuyer . '"
	WHERE
		cCertifiedId="' . $sn . '";
	';
    $conn->exeSql($sql);
    $tlog->updateWrite($_SESSION['member_id'], $sql, '合約書修改 Inquire');
    ##

    $ok = '1';

    //埋log紀錄
    //if ($conditions=='') $conditions = '無預設條件!!' ;
    // $logs->writelog('buyerowner_detailsave','編修案件('.$sn.')：'.$conditions) ;

    $conditions = $caseStatus . ',' . $signDate . ',' . $owner . ',' . $ownerID . ',' . $buyer . ',' . $buyerID . ',' . $conditions_addr1 . ',' . $conditions_addr2 . ',';
    $conditions .= $cSignMoney . ',' . $cAffixMoney . ',' . $cDutyMoney . ',' . $cEstimatedMoney . ',' . $cTotalMoney . ',' . $cCertifiedMoney . ',';
    $conditions .= $cRealestateMoney . ',' . $cAdvanceMoney . ',' . $cDealMoney . ',';
    $conditions .= $cRealestateMoneyBuyer . ',' . $cAdvanceMoneyBuyer . ',' . $cDealMoneyBuyer . ',';
    $conditions .= $cScrivenerMoney . ',' . $cCaseProcessing;

    write_log($sn . ',編修案件,' . $conditions, 'buyerowner_detailsave');

/*    $conditions = "案件狀態=$caseStatus;簽約日=$signDate;賣方=$owner;賣方ID=$ownerID;買方=$buyer;買方ID=$buyerID;標的物zip=$zZip;標的物adr=$cAddr;" ;
$conditions .= "簽約款=$cSignMoney;用印款=$cAffixMoney;完稅款=$cDutyMoney;尾款=$cEstimatedMoney;總價金=$cTotalMoney;保證費=$cCertifiedMoney;" ;
$conditions .= "賣方仲介費=$cRealestateMoney;賣方仲介先收=$cAdvanceMoney;賣方仲介餘額=$cDealMoney;" ;
$conditions .= "買方仲介費=$cRealestateMoneyBuyer;買方仲介先收=$cAdvanceMoneyBuyer;買方仲介餘額=$cDealMoneyBuyer;" ;
$conditions .= "賣方地政士=$cScrivenerMoney;買方地政士=$cScrivenerMoneyBuyer;案件進度=$cCaseProcessing;" ;*/
    ##
}

$tbl     = '';
$balance = 0;
$total   = 0;
$c_index = 0;
$color   = '#FFFFFF';
$j       = 0;
$arr     = array();
$realty2 = '';

# 取得所有資料
$query = '
SELECT
	(SELECT pName FROM tPeopleInfo WHERE pId=scr.sUndertaker1) as undertaker,
	scr.sName as scrivener,
	scr.sRecall as sRecall,
	cas.cCaseStatus as status,
	cas.cSignDate as signdate,
	cas.cEscrowBankAccount as account,
	cas.cCaseFeedback as cCaseFeedback,
	cas.cCaseProcessing as cCaseProcessing,
	cas.cFeedbackTarget as cFeedbackTarget,
	own.cName as owner,
	own.cIdentifyId as owner_id,
	own.cContactName as owner_agent,
	buy.cName as buyer,
	buy.cIdentifyId as buyer_id,
	buy.cContactName as buyer_agent,
	buy.cAuthorized as buyer_authorized,
	(SELECT bName FROM tBrand WHERE bId=rea.cBrand) as brand,
	(SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum AND bBrand=rea.cBrand) as branch,
	(SELECT bRecall FROM tBranch WHERE bId=rea.cBranchNum AND bBrand=rea.cBrand AND bId<>0) as recall,
	(SELECT bName FROM tBrand WHERE bId=rea.cBrand1) as brand1,
	(SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum1 AND bBrand=rea.cBrand1) as branch1,
	(SELECT bRecall FROM tBranch WHERE bId=rea.cBranchNum1 AND bBrand=rea.cBrand1 AND bId<>0) as recall1,
	(SELECT bName FROM tBrand WHERE bId=rea.cBrand2) as brand2,
	(SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum2 AND bBrand=rea.cBrand2) as branch2,
	(SELECT bRecall FROM tBranch WHERE bId=rea.cBranchNum2 AND bBrand=rea.cBrand2 AND bId<>0) as recall2,
	pro.cZip as cZip,
	pro.cAddr as address,
	inc.cSignMoney as signmoney,
	inc.cAffixMoney as affixmoney,
	inc.cDutyMoney as dutymoney,
	inc.cEstimatedMoney as estimatedmoney,
	inc.cTotalMoney as totalmoney,
	exp.cRealestateMoney as realestatemoney,
	exp.cAdvanceMoney as advancemoney,
	exp.cDealMoney as dealmoney,
	exp.cScrivenerMoney as scrivenermoney,
	exp.cRealestateMoneyBuyer as realestatemoneyBuyer,
	exp.cAdvanceMoneyBuyer as advancemoneyBuyer,
	exp.cDealMoneyBuyer as dealmoneyBuyer,
	exp.cScrivenerMoneyBuyer as scrivenermoneyBuyer,
	inc.cCertifiedMoney as cerifiedmoney
FROM
	tContractCase AS cas
JOIN
	tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
JOIN
	tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
JOIN
	tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
LEFT JOIN
	tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
LEFT JOIN
	tScrivener AS scr ON scr.sId=csc.cScrivener
LEFT JOIN
	tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
JOIN
	tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
JOIN
	tContractExpenditure AS exp ON exp.cCertifiedId=cas.cCertifiedId
JOIN
	tContractInvoice AS inv ON inv.cCertifiedId=cas.cCertifiedId
WHERE
	cas.cCertifiedId="' . $sn . '" ;
';

$list = $conn->one($query);
$tlog->selectWrite($_SESSION['member_id'], $query, '合約書修改 Inquire');

##其他買賣方及其他買賣代理人
$countbuyer = $countowner = $countbuyer_agent = $countowner_agent = 0; //人數

$sql = "SELECT * FROM  `tContractOthers` WHERE  `cCertifiedId` =  '" . $sn . "' ";
$rs  = $conn->all($sql);

foreach ($rs as $tmp) {
    if ($tmp['cIdentity'] == 6) { //買代

        if ($countbuyer_agent == 0) {
            $list['buyer_agent'] = $tmp['cName'];
        }

        $buyer_agent .= $tmp['cName'] . ",";
        $buyer_agentid .= $tmp['cIdentifyId'] . ",";

    } elseif ($tmp['cIdentity'] == 7) { //賣代

        if ($countowner_agent == 0) {
            $list['owner_agent'] = $tmp['cName'];
        }
        $owner_agent .= $tmp['cName'] . ",";
        $owner_agentid .= $tmp['cIdentifyId'] . ",";
    }

}

unset($tmp);unset($rel);
$list['owner_agent_name'] = $owner_agent;
$list['buyer_agent_name'] = $buyer_agent;

##

$cindex = 0;
//確認是否有第一家仲介
if (!$list['branch']) { //無
    $list['recall'] = '';
} else {
    $cindex++;
}
##

//確認是否有第二家仲介
if (!$list['branch1']) { //無
    $list['recall1'] = '';
} else { //有
    $realty2 = '
	<tr style="">
		<td style="background-color:#E4BEB1;width:110px;">仲介品牌</td>
		<td>' . $list['brand1'] . '</td>
		<td style="background-color:#E4BEB1;width:110px;">仲介店名</td>
		<td>' . $list['branch1'] . '</td>
	</tr>
	';
    $cindex++;
}
##

//確認是否有第三家仲介
if (!$list['branch2']) { //無
    $list['recall2'] = '';
} else { //有
    $realty3 = '
	<tr style="background-color:#F8ECE9;">
		<td style="background-color:#E4BEB1;width:110px;">仲介品牌</td>
		<td>' . $list['brand2'] . '</td>
		<td style="background-color:#E4BEB1;width:110px;">仲介店名</td>
		<td>' . $list['branch2'] . '</td>
	</tr>
	';
    $cindex++;
}
##

//表格顏色區分
if ((($cindex + 1) % 2) == 1) {
    $colorIndex  = 'background-color:#F8ECE9;';
    $colorIndex1 = '';
} else {
    $colorIndex  = '';
    $colorIndex1 = 'background-color:#F8ECE9;';
}
##

// 收入部分
$sql_exp = '
SELECT
	id,
	eTradeDate,
	eLender,
	eDebit,
	eTradeStatus,
	eStatusRemark,
	eRemarkContent,
	ePayTitle,
	eLastTime,
	(SELECT eId FROM tExpenseDetail WHERE eExpenseId=exp.id AND eCertifiedId="' . $sn . '" LIMIT 1) as eId,
	(SELECT sName FROM tCategoryIncome WHERE sId=exp.eStatusRemark) object
FROM
	tExpense AS exp
WHERE
	eDepAccount="00' . $list['account'] . '"
	AND ePayTitle <> "網路整批"
ORDER BY
	eLastTime
ASC ;
';

$rel_exp = $conn->all($sql_exp);
$max_exp = count($rel_exp);

$arr_exp[] = '';
for ($i = 0; $i < $max_exp; $i++) {
    $arr_exp[$i] = $rel_exp[$i];

    $arr_exp[$i]['eLender'] = substr($arr_exp[$i]['eLender'], 0, -2) + 1 - 1;
    $arr_exp[$i]['eDebit']  = substr($arr_exp[$i]['eDebit'], 0, -2) + 1 - 1;

    //$arr[$j]['date'] = $arr_exp[$i]['eLastTime'] ;
    $arr[$j]['date'] = (substr($arr_exp[$i]['eTradeDate'], 0, 3) + 1911) . '-' . substr($arr_exp[$i]['eTradeDate'], 3, 2) . '-' . substr($arr_exp[$i]['eTradeDate'], 5);

    if ($arr_exp[$i]['eStatusRemark'] == '0') {
        $arr[$j]['detail'] = $arr_exp[$i]['ePayTitle'];
    } else {
        $arr[$j]['detail'] = $arr_exp[$i]['object'];
    }
    $arr[$j]['income'] = $arr_exp[$i]['eLender'];
    //$arr[$j]['outgoing'] = '' ;
    $arr[$j]['outgoing']     = $arr_exp[$i]['eDebit'];
    $arr[$j]['remark']       = $arr_exp[$i]['eRemarkContent'];
    $arr[$j]['obj']          = '1'; // 1 表示為收入
    $arr[$j]['expId']        = $arr_exp[$i]['id'];
    $arr[$j]['eId']          = $arr_exp[$i]['eId'];
    $arr[$j]['eTradeStatus'] = $arr_exp[$i]['eTradeStatus'];

    $j++;
}
unset($arr_exp);
unset($max_exp);
unset($rel_exp);

// 支出部分
$sql_tra = '
SELECT
	tBankLoansDate as tExport_time,
	tObjKind,
	tMoney,
	tTxt
FROM
	tBankTrans
WHERE
	tVR_Code="' . $list['account'] . '"
ORDER BY
	tExport_time
ASC ;
';

$rel_tra = $conn->all($sql_tra);
$max_tra = count($rel_tra);

$arr_tra[] = '';
for ($i = 0; $i < $max_tra; $i++) {
    $arr_tra[$i] = $rel_tra[$i];

    $arr[$j]['date']     = substr($arr_tra[$i]['tExport_time'], 0, 10);
    $arr[$j]['detail']   = $arr_tra[$i]['tObjKind'];
    $arr[$j]['income']   = '';
    $arr[$j]['outgoing'] = $arr_tra[$i]['tMoney'];
    $arr[$j]['remark']   = $arr_tra[$i]['tTxt'];
    $arr[$j]['obj']      = '2'; // 2 表示為支出
    $j++;
}
unset($arr_tra);
unset($max_tra);
unset($rel_tra);

// 氣泡排序
$max = count($arr);
for ($i = 0; $i < $max; $i++) {
    for ($j = 0; $j < $max - 1; $j++) {
        if ($arr[$j]['date'] > $arr[$j + 1]['date']) {
            $tmp         = $arr[$j];
            $arr[$j]     = $arr[$j + 1];
            $arr[$j + 1] = $tmp;
            unset($tmp);
        } else if ($arr[$j]['date'] == $arr[$j + 1]['date']) {
            if (($arr[$j]['obj'] == '2') && ($arr[$j + 1]['obj'] == '1')) {
                $tmp         = $arr[$j];
                $arr[$j]     = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
                unset($tmp);
            }
        }
    }
}

##

# 資料處理區

// 簽約日期
$list['signdate'] = preg_replace("/ [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/", "", $list['signdate']);
if (preg_match("/0000-00-00/", $list['signdate'])) {
    $list['signdate'] = '';
} else {
    $tmp              = explode('-', $list['signdate']);
    $list['signdate'] = ($tmp[0] - 1911) . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);
}

// 取案件進度
$list['cCaseProcessing'] += 1 - 1;
$processing = '';
for ($j = 1; $j < 7; $j++) {
    if ($list['status'] == '3') {$_index = "";} else { $index = ' onclick="processing(' . $j . ')"';}
    $processing .= '<td id="ps' . $j . '"' . $index;
    if (($j <= $list['cCaseProcessing']) || ($list['status'] == '3')) {
        $processing .= ' class="step_class"';
    }
    $processing .= '>　</td>' . "\n";
}

//取案件狀態
if ($list['status'] != '3') {
    $_status = "<option>請選擇</option>\n";

    $sql = 'SELECT * FROM tStatusCase ORDER BY sId ASC;';
    $rel = $conn->all($sql);

    foreach ($rel as $tmp) {
        $_status .= "<option value='" . $tmp['sId'] . "'";
        if ($list['status'] == $tmp['sId']) {
            $_status .= " selected='selected'";
        }
        $_status .= ">" . $tmp['sName'] . "</option>\n";
        unset($tmp);
    }
} else {
    $_status = '<option value="' . $list['status'] . '" selected="selected">已結案</option>' . "\n";
}

// 建立帳務明細表格
for ($i = 0; $i < $max; $i++) {
    if ($i % 2 == 0) {$color = $colorIndex;} else { $color = $colorIndex1;}

    $total += $arr[$i]['income'] + 1 - 1;
    $total -= $arr[$i]['outgoing'] + 1 - 1;
    $income   = $arr[$i]['income'] + 1 - 1;
    $outgoing = $arr[$i]['outgoing'] + 1 - 1;
    $expId    = $arr[$i]['expId'];

    $tbl .= '
	<tr style="' . $color . ';">
		<td>' . $arr[$i]['date'] . '&nbsp;</td>
	';

    if ($arr[$i]['obj'] == '1') {
        $aa = '';
        $bb = '';
        if ($arr[$i]['eId']) {
            $aa = 'class="incomeDetail" ';
            $bb = '<span style="width:100%;color:red;font-weight:bold;">&nbsp;*</span>';
        }

        $correct = '';
        if ($arr[$i]['eTradeStatus'] == '9') {
            $correct = '<span style="font-size:9pt;color:red;">(被沖正)</span>';
        }

        $tbl .= '<td>
					<span style="float:left;">
					' . $arr[$i]['detail'] . $correct . '&nbsp;
					</span>
					<span style="font-size:9pt;float:right;">
						<a href="expenseDetail.php?cid=' . $sn . '&eid=' . $expId . '" class="iframe">(編輯)</a>
					</span>
				</td>';
        $tbl .= '<td ' . $aa . 'id="' . $expId . '" style="text-align:right;">' . $bb . number_format($income) . '&nbsp;</td>';
    } else {
        $tbl .= '<td>' . $arr[$i]['detail'] . '&nbsp;</td>';
        $tbl .= '<td style="text-align:right;">' . number_format($income) . '&nbsp;</td>';
    }

    $tbl .= '
		<td style="text-align:right;">' . number_format($outgoing) . '&nbsp;</td>
		<td style="text-align:right;">' . number_format($total) . '&nbsp;</td>
		<td>' . $arr[$i]['remark'] . '&nbsp;</td>
	</tr>
	';
}

if ($tbl == '') {
    $tbl = '
	<tr style="background-color:' . $colorIndex . ';">
		<td colspan="6">尚無出入款紀錄!!</td>
	</tr>
	';
}

##

// 開發票對象資訊
$sql     = 'SELECT * FROM tContractInvoice WHERE cCertifiedId="' . $sn . '" ;';
$invoice = $conn->one($sql);
##

//取得總利息
$sql       = 'SELECT * FROM tChecklist WHERE cCertifiedId="' . $sn . '";';
$rel       = $conn->one($sql);
$int_total = -1;
if (count($rel) > 0) {
    $tmp       = $rel;
    $int_total = $tmp['cInterest'] + 1 - 1;
    $int_total += $tmp['bInterest'] + 1 - 1;
    unset($tmp);
}
##

$int_money = 0; //取得實際分配利息

//取得買方利息金額
$sql = 'SELECT cInterestMoney FROM tContractBuyer WHERE cCertifiedId="' . $sn . '";';
$tmp = $conn->one($sql);
$int_money += $tmp['cInterestMoney'] + 1 - 1;
unset($tmp);
##

//取得賣方利息金額
$sql = 'SELECT cInterestMoney FROM tContractOwner WHERE cCertifiedId="' . $sn . '";';
$tmp = $conn->one($sql);
$int_money += $tmp['cInterestMoney'] + 1 - 1;
unset($tmp);
##

//取得其他買賣方利息金額
$sql = 'SELECT cInterestMoney FROM tContractOthers WHERE cCertifiedId="' . $sn . '";';
$rel = $conn->all($sql);

foreach ($rel as $tmp) {
    $int_money += $tmp['cInterestMoney'] + 1 - 1;
    unset($tmp);
}
##

//取得仲介利息金額
$sql = 'SELECT cInterestMoney,cInterestMoney1,cInterestMoney2 FROM tContractRealestate WHERE cCertifyId="' . $sn . '";';
$tmp = $conn->one($sql);

$int_money += $tmp['cInterestMoney'] + 1 - 1;
$int_money += $tmp['cInterestMoney1'] + 1 - 1;
$int_money += $tmp['cInterestMoney2'] + 1 - 1;
unset($tmp);
##

//取得代書利息金額
$sql = 'SELECT cInterestMoney FROM tContractScrivener WHERE cCertifiedId="' . $sn . '";';
$tmp = $conn->one($sql);

$int_money += $tmp['cInterestMoney'] + 1 - 1;
unset($tmp);
##

//標的物坐落
$sql = 'SELECT cItem,cZip,cAddr FROM tContractProperty WHERE cCertifiedId="' . $sn . '";';
$rel = $conn->all($sql);
$max = count($rel);

for ($i = 0; $i < $max; $i++) {
    $tmp[$i] = $rel[$i];

    if ($i == 0) {
        $property[$i]['title'] = '標的物坐落';
    }
    // zip 轉合併為地址
    if ($tmp[$i]['cZip']) {
        $sql  = 'SELECT * FROM tZipArea WHERE zZip="' . substr($tmp[$i]['cZip'], 0, 3) . '" ;';
        $tmp2 = $conn->one($sql);

        $property[$i]['city'] = $tmp2['zCity'];
        $property[$i]['area'] = $tmp2['zArea'];
        unset($tmp2);
    }

    $property[$i]['cItem'] = $tmp[$i]['cItem'];
    $property[$i]['cCity'] = property_city($property[$i]['city']);
    $property[$i]['cZip']  = $tmp[$i]['cZip'];
    $property[$i]['cArea'] = property_area($property[$i]['city'], $property[$i]['area']);
    $property[$i]['cAddr'] = str_replace($property[$i]['city'], '', str_replace($property[$i]['area'], '', $tmp[$i]['cAddr']));

}

unset($tmp);

function property_city($city)
{
    global $conn;

    $addr_city = "<option>請選擇</option>\n";
    $sql       = 'SELECT DISTINCT zCity FROM tZipArea';
    $rel       = $conn->all($sql);

    foreach ($rel as $tmp) {
        $addr_city .= "<option value='" . $tmp['zCity'] . "'";
        if ($tmp['zCity'] == $city) {
            $addr_city .= " selected='selected'";
            unset($cc);
        }
        $addr_city .= ">" . $tmp['zCity'] . "</option>\n";
        unset($tmp);
    }
    return $addr_city;
}

function property_area($city, $area)
{
    global $conn;

    $addr_area = "<option>請選擇</option>\n";
    $sql       = 'SELECT zArea,zZip FROM tZipArea WHERE zCity="' . $city . '"';
    $rel       = $conn->all($sql);

    foreach ($rel as $tmp) {
        $addr_area .= "<option value='" . $tmp['zZip'] . "'";
        if ($tmp['zArea'] == $area) {
            $addr_area .= " selected='selected'";

            unset($cc);
        }
        $addr_area .= ">" . $tmp['zArea'] . "</option>\n";
        unset($tmp);
    }

    return $addr_area;
}

$sql = "SELECT cSignCategory FROM tContractCase WHERE cCertifiedId ='" . $sn . "'";
$tmp = $conn->one($sql);

$cSignCategory = $tmp['cSignCategory'];
unset($tmp);

##
//埋log紀錄
$logs->writelog('buyerowner_detail', '查詢(' . $sn . ')的資料');
##

require_once dirname(__DIR__) . '/closedb.php';

# 搜尋資訊
$smarty->assign('cCertifiedId', $sn);
$smarty->assign('processing', $processing);
$smarty->assign('status', $_status);

$smarty->assign('list', $list);

$smarty->assign('tbl', $tbl);
$smarty->assign('total', number_format($total));
$smarty->assign('vr', $vr);

$smarty->assign('property', $property);

$smarty->assign('save_ok', $ok);
$smarty->assign('cmChange', $cCMChange);

$smarty->assign('invoice', $invoice);
$smarty->assign('realty2', $realty2);
$smarty->assign('realty3', $realty3);
$smarty->assign('int_total', $int_total);
$smarty->assign('int_money', $int_money);

# 其他
$smarty->assign('functions', $functions);
$smarty->assign('colorIndex', $colorIndex);
$smarty->assign('colorIndex1', $colorIndex1);
$smarty->assign('cSignCategory', $cSignCategory); //判斷內部

$smarty->display('buyerowner_detail.inc.tpl', '', 'report');
