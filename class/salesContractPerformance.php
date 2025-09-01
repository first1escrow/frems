<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once dirname(__DIR__) . '/openadodb.php';

if(empty($_GET['id'])){
    exit('參數錯誤');
}

$certifiedId = $_GET['id'];

$feedbackStore = [];
$feedbackSalesId = [];
$detail = [];

// $dafaultTwHouseSales = array('id'=>3,'name'=>'曾政耀'); //台屋預設業務(政耀)
// $dafaultTwHouseSales = ['id' => 66, 'name' => '公司']; //台屋預設業務(公司)
$dafaultTwHouseSales = ['id' => 2, 'name' => '雄哥']; //台屋優美。預設業務(雄哥)
$noFeedbackTwHouseSales = ['id' => 66, 'name' => '公司']; //不回饋時，預設業務(公司)
$caseNoFeedbackSales = []; //原始不回饋的業務
$TCSales = [38 => 72, 72 => 38]; //中區業務

$sql = 'SELECT
            cas.cCertifiedId as cCertifiedId,
            cas.cApplyDate as cApplyDate,
            cas.cSignDate as cSignDate,
            cas.cFinishDate as cFinishDate,
            cas.cEndDate as cEndDate,
            buy.cName as buyer,
            own.cName as owner,
            inc.cTotalMoney as cTotalMoney,
            inc.cCertifiedMoney as cCertifiedMoney,
            csc.cScrivener as cScrivener,
            (SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener,
            (SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
            cas.cCaseStatus as caseStatus,
            (SELECT sSales FROM tScrivenerSales AS b WHERE b.sScrivener = csc.cScrivener  LIMIT 1) AS scrivenerSales,
            (SELECT bName FROM tBrand AS brand WHERE brand.bId=rea.cBrand) as brand,
            (SELECT bName FROM tBrand AS brand WHERE brand.bId=rea.cBrand1) as brand1,
            (SELECT bName FROM tBrand AS brand WHERE brand.bId=rea.cBrand2) as brand2,
            (SELECT bName FROM tBrand AS brand WHERE brand.bId=rea.cBrand3) as brand3,
            (SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum) as store,
            (SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) as store1,
            (SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) as store2,
            (SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum3) as store3,
            (SELECT bCategory FROM tBranch AS b WHERE b.bId=rea.cBranchNum) as bCategory,
            (SELECT bCategory FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) as bCategory1,
            (SELECT bCategory FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) as bCategory2,
            (SELECT bCategory FROM tBranch AS b WHERE b.bId=rea.cBranchNum3) as bCategory3,
            (SELECT bName FROM tBranch AS b WHERE b.bId=rea.cBranchNum) as branch,
            (SELECT bName FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) as branch1,
            (SELECT bName FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) as branch2,
            (SELECT bName FROM tBranch AS b WHERE b.bId=rea.cBranchNum3) as branch3,
            rea.cBranchNum AS cBranchNum,
            rea.cBranchNum1 AS cBranchNum1,
            rea.cBranchNum2 AS  cBranchNum2,
            rea.cBranchNum3 AS  cBranchNum3,
            rea.cBrand AS cBrand,
            rea.cBrand1 AS cBrand1,
            rea.cBrand2 AS  cBrand2,
            rea.cBrand3 AS  cBrand3,
            cas.cCaseFeedback,
            cas.cCaseFeedback1,
            cas.cCaseFeedback2,
            cas.cCaseFeedback3,
            cas.cFeedbackTarget,
            cas.cFeedbackTarget1,
            cas.cFeedbackTarget2,
            cas.cFeedbackTarget3,
            cas.cCaseFeedBackMoney,
            cas.cCaseFeedBackMoney1,
            cas.cCaseFeedBackMoney2,
            cas.cCaseFeedBackMoney3,
            cas.cSpCaseFeedBackMoney,
            csales.cSalesId AS caseSalesID,
            (SELECT pName FROM tPeopleInfo WHERE pId=csales.cSalesId) as SalesName,
            csales.cBranch AS bid,
            csales.cTarget,
            b.bCategory AS order1,
            b.bBrand AS order2,
            CONCAT((SELECT bCode FROM tBrand WHERE bId = bBrand),LPAD(b.bId,5,"0")) as bCode,
            cas.cCertifiedId as order3,
            csales.cSalesId as order4,
            (SELECT COUNT(c.`cBranch`) FROM tContractSales AS c WHERE c.`cCertifiedId`=csales.`cCertifiedId` AND c.cBranch=csales.`cBranch`) AS sameCount,
            csales.cCreator
        FROM
            tContractCase AS cas
        LEFT JOIN tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
        LEFT JOIN tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
        LEFT JOIN tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
        LEFT JOIN tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
        LEFT JOIN tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
        LEFT JOIN tContractSales AS csales  ON  csales.cCertifiedId =cas.cCertifiedId
        LEFT JOIN tBranch AS b ON b.bId=csales.cBranch
        WHERE  cas.cCertifiedId = "' . $certifiedId . '"';
$rs = $conn->Execute($sql);

if ($rs) {
    $allCaseSales= []; //有業績的業務
    $TCStore = getTCStore();
    $PeopleInfo = getSalesData();
    $branchSPData = getBranchSPData();//群組仲介
    $branchSPData2 = getBranchSPData2();//品牌仲介
//    $scrivenerSalesId = $rs->fields['scrivenerSales'];//地政士負責業務(只抓第一位，不採用)
    $scrivenerSalesId = getScrivenerSales($rs->fields['cScrivener']);//地政士負責業務
    $feedBackSalesList = [];//計算保證費要被多少業務均分
    $moneySalesList = [];//計算保證費要被多少業務均分
    $cSpCaseFeedBackMoney = []; //特殊回饋統計

    //判斷台中拆分情況
    $tcAreaFlag = false; //是不是雲林仲介或代書
    $tcFeedbackFlag = false; //正常回饋是否要拆分
    $tcFeedbackOtherFlag = false; //其他回饋是否要拆分
    $tcFeedbackFlag2 = false;

    while (!$rs->EOF) {
        $caseSalesID = $rs->fields['caseSalesID'];

        if ($rs->fields['bid'] == $rs->fields['cBranchNum']) {
            $cCaseFeedback = $rs->fields['cCaseFeedback'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney'];
            $cBrand = $rs->fields['cBrand'];
            if (count($TCStore) > 0 && in_array($rs->fields['cBranchNum'], $TCStore['realty'])) {
                $tcAreaFlag = true;
            }
        } else if (($rs->fields['bid'] == $rs->fields['cBranchNum1']) && $rs->fields['cBranchNum1'] > 0) {
            $cCaseFeedback = $rs->fields['cCaseFeedback1'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney1'];
            $cBrand = $rs->fields['cBrand1'];
            if (count($TCStore) > 0 && in_array($rs->fields['cBranchNum1'], $TCStore['realty'])) {
                $tcAreaFlag = true;
            }
        } else if (($rs->fields['bid'] == $rs->fields['cBranchNum2']) && $rs->fields['cBranchNum2'] > 0) {
            $cCaseFeedback = $rs->fields['cCaseFeedback2'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney2'];
            $cBrand = $rs->fields['cBrand2'];
            if (count($TCStore) > 0 && in_array($rs->fields['cBranchNum2'], $TCStore['realty'])) {
                $tcAreaFlag = true;
            }
        } else if (($rs->fields['bid'] == $rs->fields['cBranchNum3']) && $rs->fields['cBranchNum3'] > 0) {
            $cCaseFeedback = $rs->fields['cCaseFeedback3'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney3'];
            $cBrand = $rs->fields['cBrand3'];
            if (count($TCStore) > 0 && in_array($rs->fields['cBranchNum3'], $TCStore['realty'])) {
                $tcAreaFlag = true;
            }
        } else if (($rs->fields['cTarget'] == 3 || $rs->fields['cSpCaseFeedBackMoney'] > 0) && empty($checkScrivenerSp[$certifiedId][$caseSalesID])) {
            $caseSalesID = '';//特殊回饋不加入台中計算
            $cCaseFeedback = 0;
//            $cCaseFeedbackMoney = $rs->fields['cSpCaseFeedBackMoney'];
            $cBrand = 2;
        } else {
            $cCaseFeedback = 0;
            $cBrand = 0;
        }

        //台屋跟優美算雄哥
        if ($cBrand == 1 || $cBrand == 49) {
            $caseSalesID = $dafaultTwHouseSales['id'];
        } else {
            $caseOriginSales[$rs->fields['scrivenerSales']] = $rs->fields['scrivenerSales'];
            if (!empty($caseSalesID)) {
                $caseOriginSales[$caseSalesID] = $caseSalesID;
            }
        }

        //不回饋給公司2022-03-22
        if ($cCaseFeedback == 1) {
            $caseNoFeedbackSales[] = $caseSalesID;
            $caseSalesID = $noFeedbackTwHouseSales['id'];
        }

        if (!empty($caseSalesID)) {
            $allCaseSales[$caseSalesID] = $caseSalesID;
        }

        $rs->MoveNext();
    }

    //過濾剩下台中業務 是否要拆分
    $tcFeedbackFlag = false;
    $allSalesTC = array_column(array_intersect($allCaseSales, $TCSales), null);
    if (count($allSalesTC) == 1) {
        if (isset($TCSales[$allSalesTC[0]])) {
            if(!$tcAreaFlag && count($allCaseSales) > 0 && filterSpecialCase($certifiedId)){
                $tcFeedbackFlag = true;
            }
        }
    }

    //其他回饋是否要均分
    $tcFeedbackOtherFlag = false;
    if (count($allSalesTC) == 2) {
        $tcFeedbackOtherFlag = true;
    }

    $rs->MoveFirst();

    $caseSalesFeedbackMoney = [];
    $salesStore = [];
    while (!$rs->EOF) {
        $noFeedBackStore = array(); //不回饋店家，判斷總部回饋用[不回饋要算公司包含總部回饋]
        $Arr = $rs->fields;
        $cCertifiedMoney = $Arr['cCertifiedMoney'];

        //收入(保證費) //保證費/合約店家數/該店業務數後分算收入
        $caseBranchCount = 0; //店家數
        $caseBranchs = [];

        for ($x = 0; $x < 4; $x++) {
            $tmpSort = ($x == 0) ? '' : $x;

            if (!empty($rs->fields['branch' . $tmpSort]) && $rs->fields['branch' . $tmpSort] != 505) {
                $caseBranchs[] = $rs->fields['cBranchNum' . $tmpSort];
            }

            if ($Arr['cBranchNum' . $tmpSort] > 0) {
                $caseBranchCount++;
            }

            //不回饋
            if ($Arr['cCaseFeedback' . $tmpSort] == 1) {
                if ($Arr['cBranchNum' . $tmpSort] > 0) {
                    $noFeedBackStore[] = $Arr['cBranchNum' . $tmpSort];
                    $feedBackSalesList[$noFeedbackTwHouseSales['id']]++;
                    $moneySalesList[$Arr['cBranchNum' . $tmpSort]][$noFeedbackTwHouseSales['id']]++;
                }
            }
        }

//        $branchSales = getBranchSales($caseBranchs);

        $caseSalesID = $rs->fields['caseSalesID'];
        if(empty($caseSalesID)){ $rs->MoveNext(); continue;}
        $applyDate = getDataDate($rs->fields['cApplyDate']);
        $bid = $rs->fields['bid'];
        $allBranch[$caseSalesID][] = $bid;

        $cCaseFeedbackMoney = 0;

        if ($rs->fields['bid'] == $rs->fields['cBranchNum']) {
            $feedbackTarget = $rs->fields['cFeedbackTarget'];
            $cCaseFeedback = $rs->fields['cCaseFeedback'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney'];
            $cBrand = $rs->fields['cBrand'];
        } else if (($rs->fields['bid'] == $rs->fields['cBranchNum1']) && $rs->fields['cBranchNum1'] > 0) {
            $feedbackTarget = $rs->fields['cFeedbackTarget1'];
            $cCaseFeedback = $rs->fields['cCaseFeedback1'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney1'];
            $cBrand = $rs->fields['cBrand1'];
        } else if (($rs->fields['bid'] == $rs->fields['cBranchNum2']) && $rs->fields['cBranchNum2'] > 0) {
            $feedbackTarget = $rs->fields['cFeedbackTarget2'];
            $cCaseFeedback = $rs->fields['cCaseFeedback2'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney2'];
            $cBrand = $rs->fields['cBrand2'];
        } else if (($rs->fields['bid'] == $rs->fields['cBranchNum3']) && $rs->fields['cBranchNum3'] > 0) {
            $feedbackTarget = $rs->fields['cFeedbackTarget3'];
            $cCaseFeedback = $rs->fields['cCaseFeedback3'];
            $cCaseFeedbackMoney = $rs->fields['cCaseFeedBackMoney3'];
            $cBrand = $rs->fields['cBrand3'];
        } else if (($rs->fields['cTarget'] == 3 || $rs->fields['cSpCaseFeedBackMoney'] > 0) && empty($checkScrivenerSp[$certifiedId][$caseSalesID])) {
            $feedbackTarget = 3;
            $cCaseFeedback = 0;
//            $cCaseFeedbackMoney = $rs->fields['cSpCaseFeedBackMoney'];
            $cSpCaseFeedBackMoney[$caseSalesID] = $rs->fields['cSpCaseFeedBackMoney'];
            $cBrand = 2;
            $tmpFeedStore = getFeedStoreData( $conn, 1, $Arr['cScrivener'] );
            $salesStore[$tmpFeedStore['code']][] = $caseSalesID;
        } else {
            $feedbackTarget = 0;
            $cCaseFeedback = 0;
            $cBrand = 0;
        }

        //代書特殊回饋只算一次
        if ($rs->fields['cTarget'] == 3) {
            $checkScrivenerSp[$certifiedId][$caseSalesID] = 1;
        }

        //不回饋給公司2022-03-22
        if ($cCaseFeedback == 1) {
            $caseSalesID = $noFeedbackTwHouseSales['id'];
        }

        //台屋跟優美算雄哥
        if ($cBrand == 1 || $cBrand == 49) {
            $denominator = 1;
            $denominator = $Arr['sameCount'];
            $caseSalesID = $dafaultTwHouseSales['id'];
        } else {
            $denominator = $Arr['sameCount'];
        }

        if($cBrand > 0){
            if(!isset($salesStore[$Arr['bCode']]) || !in_array($caseSalesID, $salesStore[$Arr['bCode']])){
                $salesStore[$Arr['bCode']][] = $caseSalesID;
            }
        }

        for ($x = 0; $x < 4; $x++) {
            $tmpSort = ($x == 0) ? '' : $x;

            if ($Arr['cTarget'] != '3') {
//            if ($Arr['cCaseFeedback' . $tmpSort] == 0) {
                if ($Arr['cBranchNum' . $tmpSort] > 0 && $rs->fields['bid'] == $Arr['cBranchNum' . $tmpSort]) {
                    $feedBackSalesList[$caseSalesID]++;
                    $moneySalesList[$rs->fields['bid']][$caseSalesID]++;
                } else if($Arr['cBranchNum' . $tmpSort] > 0 && $Arr['cCaseFeedback' . $tmpSort] == 1){
                    //有不回饋資料，但tContractSales卻沒有
//                    $feedBackSalesList[$noFeedbackTwHouseSales['id']]++;
//                    $moneySalesList[$rs->fields['bid']][$noFeedbackTwHouseSales['id']]++;
                }
//            }
            }
        }

        if ($Arr['cTarget'] == '2') {
//            $feedBackSalesList[$caseSalesID]++;
        }

        //判斷中區業務業績拆分機制
        //20230104 針對中區立寰富閔調整業績計算 20240304 限定非雲林
        if ($tcFeedbackFlag && $feedbackTarget != 3 && $bid != 505 && in_array($caseSalesID, $TCSales)) {
            if ($rs->fields['cTarget'] == 1) {
                $tcFeedbackFlagTmp = (in_array($TCSales[$caseSalesID], $scrivenerSalesId)) ? true : false;
                if(!$tcFeedbackFlagTmp){
                    //被歸於不回饋的業務
                    $tcFeedbackFlagTmp = (in_array($TCSales[$caseSalesID], $caseNoFeedbackSales)) ? true : false;
                }
            } else if ($rs->fields['cTarget'] == 2) {
                //回饋為地政士時，確認仲介是否有合契
                $sql_bCooperationHas = "SELECT bCooperationHas FROM tBranch WHERE bid = " . $rs->fields['bid'];
                $rs_bCooperationHas = $conn->Execute($sql_bCooperationHas);
                if (!$rs_bCooperationHas->EOF) {
                    if ($rs_bCooperationHas->fields['bCooperationHas'] == '1') {
                        $tcFeedbackFlagTmp = true;
                    } else {
                        $tcFeedbackFlagTmp = false;
                    }
                }
            }
        } else {
            $tcFeedbackFlagTmp = false;
        }

        //計算每一筆回饋均分
        if( $cSpCaseFeedBackMoney[$caseSalesID] > 0 && $feedbackTarget == 3){
            //特殊回饋
            $cSpCaseFeedBackMoney[$caseSalesID] = $cSpCaseFeedBackMoney[$caseSalesID] / $denominator;
        } else if (!empty($caseSalesID) && $feedbackTarget > 0) {
            if(in_array($caseSalesID, $TCSales) && $tcFeedbackFlagTmp){
                $tcFeedbackFlag2 = true; //確定台中業務要拆分
                $caseSalesFeedbackMoney[$caseSalesID] += ((int)$cCaseFeedbackMoney / (int)$denominator) / 2;
                $caseSalesFeedbackMoney[$TCSales[$caseSalesID]] += ((int)$cCaseFeedbackMoney / (int)$denominator) / 2;
            } else {
                $caseSalesFeedbackMoney[$caseSalesID] += ((int)$cCaseFeedbackMoney / (int)$denominator);
            }
        }
        $rs->MoveNext();
    }
    $rs->Close();
}

//其他回饋
$caseSalesFeedbackMoneyOther = [];
$sql_tFeedBackMoney = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='" . $certifiedId . "' AND fDelete = 0 ";
$rs_tFeedBackMoney = $conn->Execute($sql_tFeedBackMoney);
if($rs_tFeedBackMoney && !$rs_tFeedBackMoney->EOF) {
    $TCOtherFlag = false;
    foreach($TCSales as $v){
        //台中業務都有在正常回饋中，但其他回饋店家沒有
        if(isset($allBranch[$v]) && isset($allBranch[$TCSales[$v]]) && !in_array($v,$allBranch)){$TCOtherFlag = true;}
        else {$TCOtherFlag = false;}
    }

    while (!$rs_tFeedBackMoney->EOF) {
        if ($rs_tFeedBackMoney->fields['fType'] == 3) {
            //個案回饋
            $tmpSalesArr = explode(',', $rs_tFeedBackMoney->fields['fSales']);
            if (!empty($tmpSalesArr)) {
                $tmpFeedStore = getFeedStoreData(
                    $conn,
                    $rs_tFeedBackMoney->fields['fType'],
                    ($rs_tFeedBackMoney->fields['fType'] == 3) ? $rs_tFeedBackMoney->fields['fIndividualId'] : $rs_tFeedBackMoney->fields['fStoreId']
                );

                $money = round($rs_tFeedBackMoney->fields['fMoney'] / count($tmpSalesArr));
                echo $performanceDenominator = count($tmpSalesArr);
                foreach ($tmpSalesArr as $v) {
                    if ($money) {
                        $caseSalesFeedbackMoneyOther[$v]['feedback'] += round($rs_tFeedBackMoney->fields['fMoney'] / $performanceDenominator);
                        $salesStore[$tmpFeedStore['code']][] = $v;
                    }
                }
            }
        } else if ($rs_tFeedBackMoney->fields['fType'] == 2 && !empty($branchSPData[$rs_tFeedBackMoney->fields['fStoreId']])) {
            //群組回饋
            $branchCount = [];

            foreach ($allBranch as $k => $v) {
                foreach ($v as $v2) {
                    if (in_array($v2, $branchSPData[$rs_tFeedBackMoney->fields['fStoreId']])) {
                        $branchCount[$k] = (in_array($v2, $noFeedBackStore)) ? $noFeedbackTwHouseSales['id'] : $k;
                    }
                }
            }

            $performanceDenominator = count($branchCount);

            foreach ($branchCount as $v) {
                $caseSalesFeedbackMoneyOther[$v]['feedback'] += ($rs_tFeedBackMoney->fields['fMoney'] / $performanceDenominator);
            }

            $tmpFeedStore = getFeedStoreData($conn, $rs_tFeedBackMoney->fields['fType'], $rs_tFeedBackMoney->fields['fStoreId']);
            $tmpFeedStore['salesId'] = implode(',', $branchCount);
            $fSales_p = explode(',', $rs_tFeedBackMoney->fields['fSales']);
            foreach ($fSales_p as $v_fSales) {
                $salesStore[$tmpFeedStore['code']][] = $v_fSales;
            }
        } else if ($rs_tFeedBackMoney->fields['fType'] == 2 && !empty($branchSPData2[$rs_tFeedBackMoney->fields['fStoreId']])) {
            //品牌回饋
            $branchCount = [];
            foreach ($allBranch as $k => $v) {
                foreach ($v as $v2) {
                    if (in_array($v2, $branchSPData2[$rs_tFeedBackMoney->fields['fStoreId']])) {
                        $branchCount[$k] = (in_array($v2, $noFeedBackStore)) ? $noFeedbackTwHouseSales['id'] : $k;
                    }
                }
            }

            $performanceDenominator = count($branchCount);
            foreach ($branchCount as $v) {
                $caseSalesFeedbackMoneyOther[$v]['feedback'] += ($rs_tFeedBackMoney->fields['fMoney'] / $performanceDenominator);
            }

            $tmpFeedStore = getFeedStoreData($conn, $rs_tFeedBackMoney->fields['fType'], $rs_tFeedBackMoney->fields['fStoreId']);
            $tmpFeedStore['salesId'] = implode(',', $branchCount);
            $fSales_p = explode(',', $rs_tFeedBackMoney->fields['fSales']);
            foreach ($fSales_p as $v_fSales) {
                $salesStore[$tmpFeedStore['code']][] = $v_fSales;
            }
        } else {
            //可能會有一個以上的業務
            $tmpSalesArr = explode(',', $rs_tFeedBackMoney->fields['fSales']);
            if (!empty($tmpSalesArr)) {
                $tmpFeedStore = getFeedStoreData(
                    $conn,
                    $rs_tFeedBackMoney->fields['fType'],
                    ($rs_tFeedBackMoney->fields['fType'] == 3) ? $rs_tFeedBackMoney->fields['fIndividualId'] : $rs_tFeedBackMoney->fields['fStoreId']
                );

                $money = round($rs_tFeedBackMoney->fields['fMoney'] / count($tmpSalesArr));
                $performanceDenominator = count($tmpSalesArr);

                foreach ($tmpSalesArr as $v) {
                    if ($money) {
                        $caseSalesFeedbackMoneyOther[$v]['feedback'] += ($rs_tFeedBackMoney->fields['fMoney'] / $performanceDenominator);
                        $salesStore[$tmpFeedStore['code']][] = $v;
                    }
                }
            }
        }
        $rs_tFeedBackMoney->MoveNext();
    }

    //其他回饋 台中拆分
    $tcFeedbackMoneyOther = 0;
    foreach($caseSalesFeedbackMoneyOther as $k => $v ){
        if (($tcFeedbackFlag2 || $TCOtherFlag) && in_array($k, $TCSales)) {
            $tcFeedbackMoneyOther += $v['feedback'];
        }
    }

    if ($tcFeedbackFlag2 || $TCOtherFlag){
        foreach($TCSales as $v_sales){
            $caseSalesFeedbackMoneyOther[$v_sales]['feedback'] = ($tcFeedbackMoneyOther / 2);
        }
    }
}

//計算平均分攤的保證費用
$certifiedMoneyAvg = [];
$all_denominator = [];
$sales_numerator = [];
if($tcFeedbackFlag2){
    foreach ($moneySalesList as $k => $v) {
        foreach ($v as $k_slaes => $v_denominator) {
            if (in_array($k_slaes, $TCSales)) {
                $moneySalesList[$k] = [$k_slaes => $v_denominator / 2,$TCSales[$k_slaes] => $v_denominator / 2];
            }
        }
    }
}

foreach ($moneySalesList as $k => $v) {
    foreach ($v as $k_slaes => $v_denominator) {
        $sales_numerator[$k][$k_slaes] += $v_denominator;
        $all_denominator[$k] += $v_denominator;
    }
}

foreach ($moneySalesList as $k => $v) {
    foreach ($v as $k_slaes => $v_denominator) {
//        $certifiedMoneyAvg[$k_slaes] += ($cCertifiedMoney / count($moneySalesList)) * $sales_numerator[$k][$k_slaes] / $all_denominator[$k];
        $certifiedMoneyAvg[$k_slaes] += (($cCertifiedMoney / $caseBranchCount) * $sales_numerator[$k][$k_slaes]) / $all_denominator[$k];
    }
}

foreach ($cSpCaseFeedBackMoney as $k_sales => $v_feedback) {
    if(!isset($caseSalesFeedbackMoney[$k_sales])){
        $caseSalesFeedbackMoney[$k_sales] = 0;
    }
    if(!isset($certifiedMoneyAvg[$k_sales])){
        $caseSalesFeedbackMoney[$k_sales] = 0;
    }
}

foreach ($caseSalesFeedbackMoneyOther as $k_sales => $v_feedback){
    if(!isset($caseSalesFeedbackMoney[$k_sales])){
        $caseSalesFeedbackMoney[$k_sales] = 0;
    }
    if(!isset($certifiedMoneyAvg[$k_sales])){
        $caseSalesFeedbackMoney[$k_sales] = 0;
    }
}

//保證費 - 其他回饋 - 正常回饋 - 特殊回饋
foreach ($caseSalesFeedbackMoney as $k_sales => $v_feedback) {
    if(isset($cSpCaseFeedBackMoney[$k_sales])){
        $performance = round($certifiedMoneyAvg[$k_sales] - $caseSalesFeedbackMoneyOther[$k_sales]['feedback'] - $v_feedback - $cSpCaseFeedBackMoney[$k_sales]);
    } else {
        $performance = round($certifiedMoneyAvg[$k_sales] - $caseSalesFeedbackMoneyOther[$k_sales]['feedback'] - $v_feedback);
    }
    $detail[] = array('id' => (string)$k_sales, 'name' => $PeopleInfo[$k_sales]['name'], 'performance' => $performance);
    $feedbackSalesId[] = $k_sales;
}

$output['store'] = $salesStore;
$output['sales'] = implode(',', $feedbackSalesId);
$output['detail'] = $detail;

echo json_encode($output, JSON_UNESCAPED_UNICODE);
exit;

function getTCStore()
{
    global $conn;

    //取得雲林縣所有郵遞區號
    $TC_area_zips = [];
    $sql = 'SELECT zZip FROM tZipArea WHERE zCity = "雲林縣";';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $TC_area_zips[] = $rs->fields['zZip'];
        $rs->MoveNext();
    }

    //取得台中所有店家(仲介與地政士、台中業務為38:立寰、72:富閔)
    $TCStore = [];

    //仲介
    $sql = 'SELECT a.bId, b.bSales FROM tBranch AS a JOIN tBranchSales AS b ON a.bId = b.bBranch WHERE a.bZip IN ("' . implode('","', $TC_area_zips) . '") AND b.bSales IN (38, 72);';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $TCStore['realty'][] = $rs->fields['bId'];
        $rs->MoveNext();
    }
    if (!empty($TCStore['realty'])) {
        $TCStore['realty'] = array_unique($TCStore['realty']);
        sort($TCStore['realty']);
    }

    //地政士
    $sql = 'SELECT a.sId, b.sSales FROM tScrivener AS a JOIN tScrivenerSales AS b ON a.sId = b.sScrivener WHERE a.sCpZip1 IN ("' . implode('","', $TC_area_zips) . '") AND b.sSales IN (38, 72);';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $TCStore['scrivener'][] = $rs->fields['sId'];
        $rs->MoveNext();
    }

    if (!empty($TCStore['scrivener'])) {
        $TCStore['scrivener'] = array_unique($TCStore['scrivener']);
        sort($TCStore['scrivener']);
    }

    if ($rs) {
        $rs->Close();
    }

    return $TCStore;
}

function getSalesData()
{
    global $conn;

    //業務資料
    $PeopleInfo = array();
    $sql = "SELECT pName,pId FROM tPeopleInfo WHERE pDep IN (2, 4, 7)";
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $PeopleInfo[$rs->fields['pId']]['id'] = $rs->fields['pId'];
        $PeopleInfo[$rs->fields['pId']]['name'] = ($rs->fields['pId'] == 2) ? '雄哥' : $rs->fields['pName'];

        $rs->MoveNext();
    }

    if ($rs) {
        $rs->Close();
    }

    return $PeopleInfo;
}

function getDataDate($input)
{
    $output = 'null';

    if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", substr($input, 0, 10))) {
        $output = substr($input, 0, 10);
    }

    if ($output == '0000-00-00') {
        $output = 'null';
    }
    return $output;
}

//取得地政士業務
function getScrivenerSales($cScrivener)
{
    global $conn;

    $output = [];

    $sql = 'SELECT pId,pName FROM tPeopleInfo WHERE pId IN (
    SELECT
		a.sSales
	FROM
	    tScrivener AS b,
		tScrivenerSales AS a
	WHERE
		a.sScrivener=' . $cScrivener . ' AND
		b.sId=a.sScrivener )
	ORDER BY pId ASC';

    $rs = $conn->Execute($sql);
    if($rs){
        while (!$rs->EOF) {
            $output[$rs->fields['pId']] = $rs->fields['pId'];
            $rs->MoveNext();
        }
    }

    return $output;
}

function getBranchSPData()
{
    global $conn;
    $branchSPData = [];

    //群組
    $sql = "SELECT
			b.bId,
			b.bGroup,
			bg.bBranch
		FROM
			tBranch AS b
		LEFT JOIN
			tBranchGroup AS bg ON bg.bId = b.bGroup
		WHERE
			b.bGroup != '0' AND bg.bBranch != 0 AND bg.bRecall !=''";
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $branchSPData[$rs->fields['bBranch']][] = $rs->fields['bId'];
        $rs->MoveNext();
    }

    return $branchSPData;
}

function getBranchSPData2()
{
    global $conn;
    $branchSPData2 = [];

    //品牌
    $sql = "SELECT
			b.bId,
		  	bd.bRecall,
		  	bd.bBranch
		FROM
			tBrand AS bd
		LEFT JOIN
			tBranch AS b ON b.bBrand = bd.bId
		WHERE
			bd.bRecall != ''";
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $branchSPData2[$rs->fields['bBranch']][] = $rs->fields['bId'];
        $rs->MoveNext();
    }

    return $branchSPData2;
}

//排除特定案件(特殊案件, eg.地政士是回饋給業務)
function filterSpecialCase($cId)
{
    return in_array($cId, [
        '110127241',
        '110127445',
        '110127547',
        '110127649',
        '110127740',
        '110127842',
        '110127944',
        '110128041',
        '110175841',
        '111918348',
    ]) ? false : true;
}

//回饋給地政士時，取得該仲介的業務
function getRealtySales($bId)
{
    global $conn;

    $sql = 'SELECT a.bSales as sales, b.pName FROM tBranchSales AS a JOIN tPeopleInfo AS b ON a.bSales = b.pId WHERE a.bBranch = ' . $bId;
    $rs = $conn->Execute($sql);

    return ['id' => $rs->fields['sales'], 'name' => $rs->fields['pName']];
}

function getFeedStoreData($conn, $fType, $id)
{
    //2:仲介,1:代書,3:個案
    $otherFeedStore = [];

    if ($id == '505') {
        $otherFeedStore['code'] = 'NG00505';
        $otherFeedStore['store'] = '非仲介成交';
        $otherFeedStore['name'] = '非仲介成交';
        return $otherFeedStore;
    }

    if ($fType == 2 || $fType == 3) {
        $sql = "SELECT
					a.bStore AS Store,
					a.bName AS Name,
					a.bCategory,
					b.bCode AS brandCode,
					b.bName AS brand,
					CONCAT(b.bCode,LPAD(a.bId,5,'0')) as Code
				FROM
					tBranch AS a
				LEFT JOIN tBrand AS b ON a.bBrand = b.bId
				WHERE
					a.bId ='" . $id . "'";
    } elseif ($fType == 1) {
        $sql = "SELECT
					s.sName AS Name,
					CONCAT('SC',LPAD(s.sId,4,'0')) as Code,
					s.sOffice AS Store
				FROM
					tScrivener AS s
				WHERE s.sId ='" . $id . "'";
    }

    $rs = $conn->Execute($sql);

    if ($rs && !$rs->EOF) {
        if ($fType != 1) {
            $otherFeedStore['brandCode'] = $rs->fields['brandCode'];
            $otherFeedStore['brand'] = $rs->fields['brand'];
        }
        $otherFeedStore['code'] = $rs->fields['Code'];
        $otherFeedStore['store'] = $rs->fields['Store'];
        $otherFeedStore['name'] = $rs->fields['Name'];
    }

    return $otherFeedStore;
}

//取得仲介業務
function getBranchSales($caseBranchs)
{
    global $conn;

    $output = [];

    if (count($caseBranchs) > 0) {
        $sql = 'SELECT
                a.bBranch,c.pId,c.pName
            FROM
                tBranchSales AS a,
                tBranch AS b,
                tPeopleInfo AS c
            WHERE
                a.bBranch IN (' . implode(",", $caseBranchs) . ') AND
                b.bId = a.bBranch AND
                a.bSales = c.pId
            ORDER BY c.pId ASC';

        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $output[$rs->fields['bBranch']][] = $rs->fields['pId'];
            $output['id'][] = $rs->fields['pId'];
            $output['name'][] = $rs->fields['pName'];
            $rs->MoveNext();
        }
    }

    return $output;
}
