<?php
if ($_SESSION['member_id'] == 6) {
    ini_set("display_errors", "On");
    error_reporting(E_ALL & ~E_NOTICE);
}

header("Content-Type:text/html; charset=utf-8");

//總部回饋
$branchSPData  = array();
$branchSPData2 = array();

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

//取得雲林縣所有郵遞區號
$TC_area_zips = [];
$sql          = 'SELECT zZip FROM tZipArea WHERE zCity = "雲林縣";';
$rs           = $conn->Execute($sql);
while (!$rs->EOF) {
    $TC_area_zips[] = $rs->fields['zZip'];
    $rs->MoveNext();
}

//取得台中所有店家(仲介與地政士、台中業務為38:立寰、72:富閔)
$TCStore = [];

//仲介
$sql = 'SELECT a.bId, b.bSales FROM tBranch AS a JOIN tBranchSales AS b ON a.bId = b.bBranch WHERE a.bZip IN ("' . implode('","', $TC_area_zips) . '") AND b.bSales IN (38, 72);';
$rs  = $conn->Execute($sql);
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
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $TCStore['scrivener'][] = $rs->fields['sId'];
    $rs->MoveNext();
}

if (!empty($TCStore['scrivener'])) {
    $TCStore['scrivener'] = array_unique($TCStore['scrivener']);
    sort($TCStore['scrivener']);
}

//業務資料
$PeopleInfo = array();
$sql        = "SELECT pName,pId FROM tPeopleInfo WHERE pDep IN (2, 4, 7)";
$rs         = $conn->Execute($sql);
while (!$rs->EOF) {
    $PeopleInfo[$rs->fields['pId']]['id']   = $rs->fields['pId'];
    $PeopleInfo[$rs->fields['pId']]['name'] = $rs->fields['pName'];

    $rs->MoveNext();
}

// $dafaultTwHouseSales = array('id'=>3,'name'=>'曾政耀'); //台屋預設業務(政耀)
// $dafaultTwHouseSales = ['id' => 66, 'name' => '公司']; //台屋預設業務(公司)
$dafaultTwHouseSales = ['id' => 2, 'name' => '雄哥']; //台屋預設業務(雄哥)

// $cCertifiedId = [['cCertifiedId' => '110048969']];
// $cCertifiedId = [['cCertifiedId' => '110919949']];
// $cCertifiedId = [['cCertifiedId' => '110150938']];
// $cCertifiedId = [['cCertifiedId' => '110126223']];
// $cCertifiedId = [['cCertifiedId' => '090298097']];

// $cCertifiedId = [];
// $cCertifiedId[] = ['cCertifiedId' => '090078802'];
// $cCertifiedId[] = ['cCertifiedId' => '110128937'];

// $cCertifiedId[] = ['cCertifiedId' => '100032107']; //應該要有兩筆
// $cCertifiedId[] = ['cCertifiedId' => '100036938'];
// $cCertifiedId[] = ['cCertifiedId' => '110132415'];

// $cCertifiedId[] = ['cCertifiedId' => '110150679']; //應該只有一筆
// $cCertifiedId[] = ['cCertifiedId' => '111809240'];
// $cCertifiedId[] = ['cCertifiedId' => '090649256'];

// $cCertifiedId[] = ['cCertifiedId' => '110060114']; //不回饋

// $cCertifiedId[] = ['cCertifiedId' => '070220450']; //非仲介成交

// $cCertifiedId[] = ['cCertifiedId' => '110134549'];

// $cCertifiedId[] = ['cCertifiedId' => '090079045'];
// $cCertifiedId[] = ['cCertifiedId' => '110035648'];

// $cCertifiedId[] = ['cCertifiedId' => '111540445'];
// $cCertifiedId[] = ['cCertifiedId' => '110129590'];

// $cCertifiedId[] = ['cCertifiedId' => '110085087'];
// $cCertifiedId[] = ['cCertifiedId' => '110127445'];
// $cCertifiedId[] = ['cCertifiedId' => '110127547'];
// $cCertifiedId[] = ['cCertifiedId' => '110127649'];
// $cCertifiedId[] = ['cCertifiedId' => '110127740'];
// $cCertifiedId[] = ['cCertifiedId' => '110127842'];
// $cCertifiedId[] = ['cCertifiedId' => '110127944'];
// $cCertifiedId[] = ['cCertifiedId' => '110128041'];
// $cCertifiedId[] = ['cCertifiedId' => '110175841'];
// $cCertifiedId[] = ['cCertifiedId' => '111918348'];

// $cCertifiedId[] = ['cCertifiedId' => '110936257'];
// $cCertifiedId[] = ['cCertifiedId' => '111158757'];

$caseSalesCount = array();
$dataCaseFeed   = array();
if (is_array($cCertifiedId)) {
    foreach ($cCertifiedId as $k => $v) {
        $sql = '
			SELECT
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
				rea.cZip AS cZip,
				rea.cZip1 AS cZip1,
				rea.cZip2 AS cZip2,
				rea.cZip3 AS cZip3,
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
				csales.cCreator,
			    scr.sZip1
			FROM
				tContractCase AS cas
			LEFT JOIN tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
			LEFT JOIN tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
			LEFT JOIN tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
			LEFT JOIN tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
			LEFT JOIN tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
			LEFT JOIN tContractSales AS csales  ON  csales.cCertifiedId =cas.cCertifiedId
			LEFT JOIN tBranch AS b ON b.bId=csales.cBranch
			LEFT JOIN tScrivener AS scr ON scr.sId = csc.cScrivener
			WHERE
			' . $query . '
			AND cas.cCertifiedId = "' . $v['cCertifiedId'] . '"';
        $rs = $conn->Execute($sql);
//         exit($sql);
        //正常回饋
        while (!$rs->EOF) {
            $noFeedBackStore       = array(); //不回饋店家，判斷總部回饋用[不回饋要算公司包含總部回饋]
            $Arr                   = $rs->fields;
            $Arr['tBankLoansDate'] = $v['tBankLoansDate'];

            //收入(保證費) //保證費/合約店家數/該店業務數後分算收入
            $Arr['branchCount'] = 0; //店家數
            if ($Arr['cBranchNum'] > 0) {$Arr['branchCount']++;}
            if ($Arr['cBranchNum1'] > 0) {$Arr['branchCount']++;}
            if ($Arr['cBranchNum2'] > 0) {$Arr['branchCount']++;}
            if ($Arr['cBranchNum3'] > 0) {$Arr['branchCount']++;}
            ##

            //不回饋案件給公司2022-03-22
            if ($Arr['cCaseFeedback'] == 1) {
                array_push($noFeedBackStore, $Arr['cBranchNum']);
            }

            if ($Arr['cCaseFeedback1'] == 1) {
                array_push($noFeedBackStore, $Arr['cBranchNum1']);
            }

            if ($Arr['cCaseFeedback2'] == 1) {
                array_push($noFeedBackStore, $Arr['cBranchNum2']);
            }

            if ($Arr['cCaseFeedback3'] == 1) {
                array_push($noFeedBackStore, $Arr['cBranchNum3']);
            }
            ##

            if ($Arr['sameCount'] > 1) { //確認是否有同店業務
                $Arr['sameStore'] = 1;
            }

            ##案件總回饋計算##
            if (($Arr['bid'] == $Arr['cBranchNum'])) {
                $areaZip = ($Arr['cTarget'] == 2) ? $Arr['sZip1'] : $Arr['cZip'];
                //不回饋案件給公司2022-03-22
                setData($Arr, $Arr['bid'], $Arr['cBrand'], $Arr['brand'], $Arr['cFeedbackTarget'], $Arr['bCode'], $Arr['store'], $Arr['branch'], $Arr['cCaseFeedback'], $Arr['cCaseFeedBackMoney'], $Arr['bCategory'],'',$areaZip);
            } else if (($Arr['bid'] == $Arr['cBranchNum1']) && $Arr['cBranchNum1'] > 0) {
                $areaZip = ($Arr['cTarget'] == 2) ? $Arr['sZip1'] : $Arr['cZip1'];
                //不回饋案件給公司2022-03-22
                setData($Arr, $Arr['bid'], $Arr['cBrand1'], $Arr['brand1'], $Arr['cFeedbackTarget1'], $Arr['bCode'], $Arr['store1'], $Arr['branch1'], $Arr['cCaseFeedback1'], $Arr['cCaseFeedBackMoney1'], $Arr['bCategory1'],'',$areaZip);
            } else if (($Arr['bid'] == $Arr['cBranchNum2']) && $Arr['cBranchNum2'] > 0) {
                $areaZip = ($Arr['cTarget'] == 2) ? $Arr['sZip1'] : $Arr['cZip2'];
                //不回饋案件給公司2022-03-22
                setData($Arr, $Arr['bid'], $Arr['cBrand2'], $Arr['brand2'], $Arr['cFeedbackTarget2'], $Arr['bCode'], $Arr['store2'], $Arr['branch2'], $Arr['cCaseFeedback2'], $Arr['cCaseFeedBackMoney2'], $Arr['bCategory2'],'',$areaZip);
            } else if (($Arr['bid'] == $Arr['cBranchNum3']) && $Arr['cBranchNum3'] > 0) {
                $areaZip = ($Arr['cTarget'] == 2) ? $Arr['sZip1'] : $Arr['cZip3'];
                //不回饋案件給公司2022-03-22
                setData($Arr, $Arr['bid'], $Arr['cBrand3'], $Arr['brand3'], $Arr['cFeedbackTarget3'], $Arr['bCode'], $Arr['store3'], $Arr['branch3'], $Arr['cCaseFeedback3'], $Arr['cCaseFeedBackMoney3'], $Arr['bCategory3'],'',$areaZip);
            } else if (($Arr['cTarget'] == 3 || $Arr['cSpCaseFeedBackMoney'] > 0) && empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkscrivenerSp'])) { //地政士特殊回饋
                setData($Arr, $Arr['cScrivener'], 2, $Arr['scrivener'], 3, 'SC' . str_pad($Arr['cScrivener'], "4", "0", STR_PAD_LEFT), $Arr['scrivener'], '', 0, $Arr['cSpCaseFeedBackMoney'], 'sp',$Arr['sZip1']);
            }
            // echo '<pre>';
            // print_r($dataCaseFeed);exit;

            //其他回饋
            if (empty($dataCaseFeed[$Arr['cCertifiedId']]['otherFeedCheck'])) {
                getOtherFeedForReport2($Arr, $v['cCertifiedId'], $noFeedBackStore);
            }
            // echo '<pre>';
            // print_r($dataCaseFeed);exit;

            $rs->MoveNext();
        }
        ##案件總回饋計算END##
    }
}

$PeopleInfo = $dafaultTwHouseSales = null;
unset($PeopleInfo, $dafaultTwHouseSales);

// echo '<pre>';
// print_r($dataCaseFeed);exit;

$sortArray = array();
foreach ($dataCaseFeed as $k => $v) {
    foreach ($v as $key => $value) {
//        if (in_array($value['salesId'], $sales_arr)) { //是查尋的業務且狀態是回饋
//        if(isset($value['feedMoney'])){
        if (!empty($value['salesPerformance']) && !empty($value['certifiedId'])) {
            $value['feedMoney']          = round($value['feedMoney']);
            $sortArray[$value['sort']][] = $value;
        }

//        }
    }
}

// echo '<pre>';
// print_r($sortArray);exit;

$dataCaseFeed = null;unset($dataCaseFeed);

ksort($sortArray);

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("績效業績統計");
$objPHPExcel->getProperties()->setDescription("第一建績效經業績統計");

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('C1:E1');

//基本資料開始頁數
$row = 8; //列
$c   = 65; //欄

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', '範圍');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '時間');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '民國' . $start_y . '年' . $start_m . '月 ~ 民國' . $end_y . '年' . $end_m . '月');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '仲介品牌');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '仲介店名');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '公司名');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '平均保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '業績分配');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '業績');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '業績業務人員');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '績效業務人員');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '簽約日期');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '建檔日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '仲介類別');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, '結案日期');

$no  = 1;
$row = 9;
foreach ($sortArray as $value) {
    foreach ($value as $v) {
        $salesMoney = $v['avgCertifiedMoney'] - $v['feedMoney'];
        $storeData  = storeSort($v['brand'], $v['code'], $v['store'], $v['branch'], $v['branchCategory'], $v['certifiedId']);

        $brand          = @implode('_', $storeData['brand']);
        $code           = @implode('_', $storeData['code']);
        $store          = @implode('_', $storeData['store']);
        $branch         = @implode('_', $storeData['branch']);
        $branchCategory = @implode('_', $storeData['branchCategory']);
        $salesCount     = (empty($caseSalesCount[$v['certifiedId']])) ? 0 : count($caseSalesCount[$v['certifiedId']]);

        if ($v['certifiedId'] == '101018516') {
            $salesCount = 1;
        }

        $storeData = null;unset($storeData);

        $c = 65; //欄
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, ($no));
        $objPHPExcel->getActiveSheet()->getCell(chr($c++) . $row)->setValueExplicit($v['certifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);

        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $brand); ///C
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $code); //D
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $store); //E
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $branch); //F
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['owner']); //G
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['buyer']); //H
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['totalMoney']); //I
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['certifiedMoney']); //J
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['avgCertifiedMoney']); //K
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['feedMoney']); //L
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $salesCount); //M
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $salesMoney); //N
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['salesName']); //O
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['salesPerformanceName']); //O
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['statusDate']); //P
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['signDate']); //Q
        // $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['applyDate']); //Q
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['finishDate']); //R
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['scrivener']); //S
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['address']); //T
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['status']); //U
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $branchCategory); //V
        $objPHPExcel->getActiveSheet()->setCellValue(chr($c++) . $row, $v['endDate']); //W

        //總業績
//        $sales_total[$v['salesId']] += $salesMoney;
        $sales_total[$v['salesPerformance']] += $salesMoney;

        //月業績
        if ($report_type == 1) {
            if ($date_type == 1) {
                // $month = substr($v['signDate'], 0, 6);
                $month = substr($v['applyDate'], 0, 6);
            } else if ($date_type == 2) {
                $month = substr($v['bankLoansDate'], 0, 7);
            }

            $sales_month[$month] = $sales_month[$month] + $salesMoney;
        }

        $row++;
        $no++;
    }
}
##

function storeSort($brand, $code, $store, $branch, $branchCategory, $cId = '')
{
    $scrivener      = array();
    $dataSort       = array();
    $scrivenerCount = 0;
    $dataCount      = 0;

    //地政士排後面
    for ($i = (count($code) - 1); $i >= 0; $i--) {
        if (substr($code[$i], 0, 2) == 'SC') {
            $scrivener[$scrivenerCount]['brand']          = $brand[$i];
            $scrivener[$scrivenerCount]['code']           = $code[$i];
            $scrivener[$scrivenerCount]['store']          = $store[$i];
            $scrivener[$scrivenerCount]['branch']         = $branch[$i];
            $scrivener[$scrivenerCount]['branchCategory'] = $branchCategory[$i];

            $scrivenerCount++;
        } else {
            $dataSort[$dataCount]['brand']          = $brand[$i];
            $dataSort[$dataCount]['code']           = $code[$i];
            $dataSort[$dataCount]['store']          = $store[$i];
            $dataSort[$dataCount]['branch']         = $branch[$i];
            $dataSort[$dataCount]['branchCategory'] = $branchCategory[$i];

            $dataCount++;
        }
    }

    for ($i = 0; $i < $dataCount; $i++) {
        for ($j = 0; $j < $dataCount - 1; $j++) {
            if ($dataSort[$j]['brand'] > $dataSort[$j + 1]['brand']) {
                $tmp              = $dataSort[$j];
                $dataSort[$j]     = $dataSort[$j + 1];
                $dataSort[$j + 1] = $tmp;
                $tmp              = null;unset($tmp);
            }
        }
    }

    $dataSort  = array_merge($dataSort, $scrivener);
    $scrivener = null;unset($scrivener);

    $data = array();
    for ($i = 0; $i < count($dataSort); $i++) {
        $data['brand'][]          = $dataSort[$i]['brand'];
        $data['code'][]           = $dataSort[$i]['code'];
        $data['store'][]          = $dataSort[$i]['store'];
        $data['branch'][]         = $dataSort[$i]['branch'];
        $data['branchCategory'][] = $dataSort[$i]['branchCategory'];
    }

    return $data;
}

function setData($Arr, $bId, $brandId, $brandName, $feedbackTarget, $branchCode, $branchstore, $branch, $feedback, $feedBackMoney, $category, $cost = '',$areaZip = '')
{
    global $dataCaseFeed;
    global $PeopleInfo;
    global $dafaultTwHouseSales;
    global $caseSalesCount;
    global $TCStore;
    global $salesArr;
//var_dump($Arr);

    //中區業務
    $TC_sales        = [38, 72];
    $TC_invert_sales = [ //台中互為業務
        38 => $PeopleInfo[72],
        72 => $PeopleInfo[38],
    ];
    ##

    //20230105 中區互為業務紀錄
    $_shadow = [];
    ##

    //不回饋給公司2022-03-22
    if ($feedback == 1) {
        $Arr['caseSalesID'] = 66;
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformance'] = '';
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformanceName'] = '';
    } else {
        //判斷績效業務區域
        if($Arr['cTarget'] == 2){
            if(isset($salesArr[2][$areaZip])){
                $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformance'] = $salesArr[2][$areaZip];
                $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformanceName'] = $PeopleInfo[$salesArr[2][$areaZip]]['name'];
            }
        } else if($Arr['cTarget'] == 3 || $Arr['cSpCaseFeedBackMoney'] > 0){
            if(isset($salesArr[1][$areaZip])) {
                $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformance'] = $salesArr[1][$areaZip];
                $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformanceName'] = $PeopleInfo[$salesArr[1][$areaZip]]['name'];
            }
        } else {
            if(isset($salesArr[1][$areaZip])) {
                $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformance'] = $salesArr[1][$areaZip];
                $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesPerformanceName'] = $PeopleInfo[$salesArr[1][$areaZip]]['name'];
            }
        }
    }

    $Arr['SalesName']   = $PeopleInfo[$Arr['caseSalesID']]['name'];
    $Arr['caseSalesID'] = ($brandId == 1 || $brandId == 49) ? $dafaultTwHouseSales['id'] : $Arr['caseSalesID']; //台屋跟優美算雄哥
    $Arr['SalesName']   = ($brandId == 1 || $brandId == 49) ? $dafaultTwHouseSales['name'] : $Arr['SalesName']; //台屋跟優美算雄哥
    $feedBackMoney      = ($feedback == 1) ? 0 : $feedBackMoney; //不回饋0元

    //單店台屋但有區域士N個業務，所以只算一次
    if ($Arr['sameCount'] > 1 && $Arr['branchCount'] == 1 && $Arr['caseSalesID'] == 3) {
        $Arr['sameCount']                                                      = 1;
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkTwOne'] = 1;
    }

    //統計資料-設定預設
    if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['brand'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['brand'] = array();}
    if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'] = array();}
    if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['store'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['store'] = array();}
    if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branch'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branch'] = array();}
    if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branchCategory'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branchCategory'] = array();}
    if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'])) {
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'] = 0;
    }

    //相同的店只算一次
    if (!in_array($branchCode, $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'])) {
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'][]           = $branchCode; //店編
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['brand'][]          = $brandName; //品牌
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['store'][]          = $branchstore; //仲介店名
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branch'][]         = $branch; //公司名
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branchCategory'][] = getCategory($category, $brandName); // 仲介類別
    }

    $caseSalesCount[$Arr['cCertifiedId']][$Arr['caseSalesID']]++;
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['certifiedId']    = $Arr['cCertifiedId']; //保證號碼
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['owner']          = $Arr['owner']; //賣
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['buyer']          = $Arr['buyer']; //買
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['totalMoney']     = $Arr['cTotalMoney']; //總價金
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['certifiedMoney'] = $Arr['cCertifiedMoney']; //合約保證費總額

    $_scrivener_sales = $PeopleInfo[$Arr['scrivenerSales']]; //地政士負責業務

    $_sales = ($bId == 505) ? getSalesBy($Arr['scrivenerSales']) : ['id' => $Arr['caseSalesID']];
    if ($cost == '' && $feedbackTarget != 3) { //有選回饋才算收入(保證費) [他回饋保證費不能計算]  $feedback == 0 && //不回饋給公司2022-03-22
        //for 中區業務
        /* 區域績效業績 不拆分
        $_scrivener_sales_count = 0; //是否地政士業務與仲介店為同一業務，若是、則為 0
        if (in_array($Arr['caseSalesID'], $TC_sales) && !targetAreaCheck($TCStore, $Arr['cTarget'], $Arr['cScrivener'], $Arr['bid'])) { //20230104 針對中區立寰富閔調整業績計算 20240304 限定非雲林
            if (in_array($_scrivener_sales['id'], $TC_sales)) { //若為範圍內業務，且非同一位業務時
                if (allStoreNotTCSales($TC_sales, $Arr['cCertifiedId'], $Arr, $_scrivener_sales) && ($bId != 505)) {
                    if ($Arr['cApplyDate'] > '2022-12-31') {
                        $_scrivener_sales_count += 1;
                    }

                    //20230105新增中區互為業務紀錄
                    $_shadow = [
                        'cCertifiedId'      => $Arr['cCertifiedId'],
                        'sales'             => $TC_invert_sales[$_sales['id']]['id'],
                        'salesName'         => $TC_invert_sales[$_sales['id']]['name'],
                        'avgCertifiedMoney' => round($Arr['cCertifiedMoney'] / $Arr['branchCount'] / ($Arr['sameCount'] + $_scrivener_sales_count)),
                    ];
                    ##
                }
            }
        }*/
        ##

        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'] += round($Arr['cCertifiedMoney'] / $Arr['branchCount'] / ($Arr['sameCount'] + $_scrivener_sales_count)); //回饋同家店有兩人所以要除
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['showCount2'] += round(1 / $Arr['branchCount'] / $Arr['sameCount'], 2); //新制

    } else if ($Arr['cCertifiedId'] == '101018516') { //特殊案件要算給偉哲
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'] += round($Arr['cCertifiedMoney'] / $Arr['branchCount'] / $Arr['sameCount']); //回饋同家店有兩人所以要除
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['showCount2'] = 1;
    }

    //計算回饋金
    if ($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkTwOne'] == 1) {
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedMoney'] = $feedBackMoney;
    } else {
        //for 中區業務
        /* 區域績效業績 不拆分
        $_scrivener_sales_count = 0; //是否地政士業務與仲介店為同一業務，若是、則為 0

        if (in_array($Arr['caseSalesID'], $TC_sales) && !targetAreaCheck($TCStore, $Arr['cTarget'], $Arr['cScrivener'], $Arr['bid'])) { //20230131 針對中區立寰富閔調整業績計算 20240304 限定非雲林
            if (in_array($_scrivener_sales['id'], $TC_sales)) { //若為範圍內業務，且非同一位業務時
                if (allStoreNotTCSales($TC_sales, $Arr['cCertifiedId'], $Arr, $_scrivener_sales) && ($bId != 505)) {
                    if ($Arr['cApplyDate'] > '2022-12-31') {
                        $_scrivener_sales_count += 1;
                    }

                    //20230105新增中區互為業務紀錄
                    if (empty($_shadow)) {
                        $_shadow = [
                            'cCertifiedId'      => $Arr['cCertifiedId'],
                            'sales'             => $TC_invert_sales[$_sales['id']]['id'],
                            'salesName'         => $TC_invert_sales[$_sales['id']]['name'],
                            'avgCertifiedMoney' => 0,
                        ];
                    }
                    ##
                    if ($feedBackMoney == 0) {
                        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['feedMoney'] += 0;
                    } else {
                        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['feedMoney'] += $feedBackMoney / ($Arr['sameCount'] + $_scrivener_sales_count);
                    }
                }
            }
        }*/
        ##

        if ($feedBackMoney == 0) {
            $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedMoney'] += 0;
        } else {
            $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedMoney'] += $feedBackMoney / ($Arr['sameCount'] + $_scrivener_sales_count); //回饋金(同家店有兩人所以要除)
        }

    }

    $_sales = null;unset($_sales);
    ##

    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesName'] = $Arr['SalesName']; //業務人員
    // $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['statusDate'] = ($Arr['caseStatus'] != 2) ? dateformate($Arr['cEndDate']) : dateformate($Arr['cSignDate']); //案件狀態日期
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['statusDate'] = ($Arr['caseStatus'] != 2) ? dateformate($Arr['cEndDate']) : dateformate($Arr['applyDate']); //案件狀態日期

    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['applyDate']  = dateformate($Arr['cApplyDate']); //建檔日期
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['signDate']   = dateformate($Arr['cSignDate']); //簽約日期
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['finishDate'] = dateformate($Arr['cFinishDate']); //實際點交日期
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['scrivener']  = $Arr['scrivener']; //地政士姓名
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['address']    = addr($Arr['cCertifiedId']); //標的物坐落
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['status']     = $Arr['status']; //狀態
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['endDate']    = dateformate($Arr['cEndDate']);

    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['bankLoansDate'] = $Arr['tBankLoansDate']; //保證費出款時間
    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesId']       = $Arr['caseSalesID']; //業務代碼

    $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedback'] = $feedback; //是否回饋

    //代書特殊回饋只算一次
    if ($feedbackTarget == 3) {
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkscrivenerSp'] = 1;
        $Arr['order1']                                                               = 2; //排在仲介後
    }

    //其他回饋只算一次
    if ($cost) {
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['otherFeedCheck'] = 1;
        $dataCaseFeed[$Arr['cCertifiedId']]['otherFeedCheck']                      = 1;
    }

    if ($brandId != 1 && empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sort'] = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['statusDate'] . "_" . $Arr['order3'] . "_" . $Arr['order4'])) { //台屋不排
//        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sort'] = $Arr['order1'] . "_" . $brandId . "_" . $Arr['order3'] . "_" . $Arr['order4'];
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sort'] = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['statusDate'] . "_" . $Arr['order3'] . "_" . $Arr['order4'];
    }

    if ($Arr['sameStore'] == 1) {
        $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sameStore'] = $Arr['sameStore'];
    }

    //20230105 中區互為業務存在時
    if (!empty($_shadow)) {
        //相同的店只算一次
        if (empty($dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['code'])) {
            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['code'] = [];
        }

        if (!in_array($branchCode, $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['code'])) {
            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['code'][]           = $branchCode; //店編
            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['brand'][]          = $brandName; //品牌
            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['store'][]          = $branchstore; //仲介店名
            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['branch'][]         = $branch; //公司名
            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['branchCategory'][] = getCategory($category, $brandName); // 仲介類別
        }

        //計算互為業務的中區業務平均回饋金額
        $_default_avg_money = empty($dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['avgCertifiedMoney']) ? 0 : $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['avgCertifiedMoney'];
        if (empty($cost)) {
            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['avgCertifiedMoney'] = $_default_avg_money + $_shadow['avgCertifiedMoney'];
        }
        ##

        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['salesName'] = $_shadow['salesName']; //業務人員

        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['certifiedId']    = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['certifiedId'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['owner']          = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['owner'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['buyer']          = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['buyer'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['totalMoney']     = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['totalMoney'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['certifiedMoney'] = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['certifiedMoney'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['showCount2']     = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['showCount2'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['statusDate']     = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['statusDate'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['applyDate']      = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['applyDate'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['signDate']       = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['signDate'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['finishDate']     = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['finishDate'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['scrivener']      = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['scrivener'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['address']        = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['address'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['status']         = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['status'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['endDate']        = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['endDate'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['bankLoansDate']  = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['bankLoansDate'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['salesId']        = $_shadow['sales'];
        $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['feedback']       = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedback'];

        if ($brandId != 1) { //台屋不排
//            $dataCaseFeed[$_shadow['cCertifiedId']][$_shadow['sales']]['sort'] = $Arr['order1'] . "_" . $brandId . "_" . $Arr['order3'] . "_" . $_shadow['sales'];
            $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sort'] = $dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['statusDate'] . "_" . $Arr['order3'] . "_" . $_shadow['sales'];
        }
    }

    $_shadow          = null;unset($_shadow);
    $_scrivener_sales = $_scrivener_sales_count = null;
    unset($_scrivener_sales, $_scrivener_sales_count);
    ##
}

function getCategory($category, $brand)
{
    if ($category == 1 && !in_array($brand, ['台灣房屋', '非仲介成交', '優美地產'])) { //加盟(其他品牌)
        return '加盟(其他品牌)';
    }

    if ($category == 1 && $brand == '台灣房屋') { //加盟(台灣房屋)
        return '加盟(台灣房屋)';
    }

    if ($category == 1 && $brand == '優美地產') { //加盟(優美地產)
        return '加盟(優美地產)';
    }

    if ($category == 1) { //加盟
        return '加盟';
    }

    if ($category == 2) { //直營
        return '直營';
    }

    if ($category == 3) { //非仲介成交
        return '非仲介成交';
    }

    if ($category == 'sp') {
        return '特殊回饋地政士';
    }

    return '';
}

function getOtherFeedForReport2($Arr, $certifiedId, $noFeedBackStore)
{
    global $conn;
    global $branchSPData;
    global $branchSPData2;

    $sql   = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='" . $certifiedId . "' AND fDelete = 0 ";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    $i = 0;
    if ($total == 0) {
        return false;
    }

    while (!$rs->EOF) {
        //1地政2仲介
        if ($rs->fields['fType'] == 2 && !empty($branchSPData[$rs->fields['fStoreId']])) { //群組回饋
            $branchCount = array();
            $sql         = "SELECT cSalesId,cBranch FROM tContractSales WHERE cCertifiedId = '" . $certifiedId . "' AND cBranch IN (" . @implode(',', $branchSPData[$rs->fields['fStoreId']]) . ")";
            $rs2         = $conn->Execute($sql);

            while (!$rs2->EOF) {
                if (in_array($rs2->fields['cBranch'], $noFeedBackStore)) {
                    $rs2->fields['cSalesId'] = 66;
                }

                $branchCount[$rs2->fields['cBranch']]['sales'][$rs2->fields['cSalesId']] = $rs2->fields['cSalesId'];
                $branchCount[$rs2->fields['cBranch']]['store']++;
                $rs2->MoveNext();
            }

            foreach ($branchCount as $key => $value) {
                $Arr['sameCount'] = count($value['sales']);
                foreach ($value['sales'] as $k => $v) {
                    $Arr['caseSalesID'] = $v;

                    $tmp   = getOtherFeed($rs->fields['fType'], $rs->fields['fStoreId']);
                    $money = $rs->fields['fMoney'] / count($branchCount); //先算仲介部分

                    setData($Arr, $rs->fields['fStoreId'], $tmp['brandCode'], $tmp['brand'], 1, $tmp['Code'], $tmp['Store'], $tmp['Name'], 0, $money, $tmp['bCategory'], 1);
                    $tmp = null;unset($tmp);
                }
            }
        } else if ($rs->fields['fType'] == 2 && !empty($branchSPData2[$rs->fields['fStoreId']])) { //品牌回饋
            $branchCount = array();

            $sql = "SELECT cSalesId,cBranch FROM tContractSales WHERE cCertifiedId = '" . $certifiedId . "' AND cBranch IN (" . @implode(',', $branchSPData2[$rs->fields['fStoreId']]) . ")";
            $rs2 = $conn->Execute($sql);

            while (!$rs2->EOF) {
                if (in_array($rs2->fields['cBranch'], $noFeedBackStore)) {
                    $rs2->fields['cSalesId'] = 66;
                }

                $branchCount[$rs2->fields['cBranch']]['sales'][$rs2->fields['cSalesId']] = $rs2->fields['cSalesId'];
                $branchCount[$rs2->fields['cBranch']]['store']++;
                $rs2->MoveNext();
            }

            foreach ($branchCount as $key => $value) {
                $Arr['sameCount'] = count($value['sales']);

                foreach ($value['sales'] as $k => $v) {
                    $Arr['caseSalesID'] = $v;

                    $tmp   = getOtherFeed($rs->fields['fType'], $rs->fields['fStoreId']);
                    $money = $rs->fields['fMoney'] / count($branchCount); //先算仲介部分

                    setData($Arr, $rs->fields['fStoreId'], $tmp['brandCode'], $tmp['brand'], 1, $tmp['Code'], $tmp['Store'], $tmp['Name'], 0, $money, $tmp['bCategory'], 1);
                    $tmp = null;unset($tmp);
                }
            }
        } else {
            //可能會有一個以上的業務
            $sales            = explode(',', $rs->fields['fSales']);
            $money            = $rs->fields['fMoney'];
            $Arr['sameCount'] = count($sales);

            $tmp = getOtherFeed($rs->fields['fType'], $rs->fields['fStoreId'], $rs->fields['fIndividualId']);
            //類型1地政2仲介

            foreach ($sales as $v) {
                $Arr['caseSalesID'] = $v;
                if ($rs->fields['fType'] == 2) {
                    //回饋金對象(1:仲介、2:代書) //round($rs->fields['fMoney']/$total2)
                    setData($Arr, $rs->fields['fStoreId'], $tmp['brandCode'], $tmp['brand'], 1, $tmp['Code'], $tmp['Store'], $tmp['Name'], 0, $money, $tmp['bCategory'], 1);
                } else if ($rs->fields['fType'] == 1) {
                    setData($Arr, $rs->fields['fStoreId'], '', '', 1, $tmp['Code'], $tmp['Store'], $tmp['Name'], 0, $money, '', 1);
                } else if ($rs->fields['fType'] == 3) { #個案回饋
                    setData($Arr, $rs->fields['fIndividualId'], $tmp['brandCode'], $tmp['brand'], 1, $tmp['Code'], $tmp['Store'], $tmp['Name'], 0, $money, $tmp['bCategory'], 1);
                }
            }

            $tmp = null;unset($tmp);
        }

        $rs->MoveNext();
    }
}

//案件為配件
function allStoreNotTCSales($TC_sales, $cCertifiedId, $Arr, $_scrivener_sales)
{
    global $conn;

    //找出案件的所業務
    $sql = 'SELECT a.cSalesId as sales, a.cTarget as target, a.cBranch, b.bBrand as brand FROM tContractSales AS a left join tBranch AS b ON a.cBranch = b.bId WHERE a.cCertifiedId = "' . $cCertifiedId . '";';
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        if ($rs->fields['brand'] != 1) { //品牌非台屋時
            $tmp = ['sales' => $rs->fields['sales'], 'target' => $rs->fields['target']]; //target = 1 為仲介, 2 為代書;

            //當回饋給代書時，業務身分會被改寫為代書的負責業務，所以需要增加以下代碼找回原本仲介的負責業務
            if (($tmp['target'] == 2) && ($rs->fields['cBranch'] != 505) && filterSpecialCase($cCertifiedId)) {
                $_origin_sales = getRealtySales($rs->fields['cBranch']);
                $tmp['sales']  = $_origin_sales['id'];
                $_origin_sales = null;unset($_origin_sales);
            }

            $sales[] = $tmp;

            $tmp = null;unset($tmp);
        }
        $rs->MoveNext();
    }
    ##

    //取得所有業務比對
    $_sales   = $sales;
    $_sales[] = ['sales' => $_scrivener_sales['id']]; //加入地政士業務一起比較
    $_sales   = array_column($_sales, 'sales');
    ##

    //所有業務均為同一人時，不拆分(false)
    if (is_array_values_same($_sales)) {
        return false;
    }
    ##

    //如果所有業務都是台中業務時，須拆分計算(true)；若有其他非台中業務時，則不要拆分(false)
    return empty(array_diff($_sales, $TC_sales)) ? true : false;
    ##
}

function is_array_values_same($array)
{
    $count_values = array_count_values($array);
    return (count($count_values) == 1) ? true : false;
}
##

//回饋給地政士時，取得該仲介的業務
function getRealtySales($bId)
{
    global $conn;

    $sql = 'SELECT a.bSales as sales, b.pName FROM tBranchSales AS a JOIN tPeopleInfo AS b ON a.bSales = b.pId WHERE a.bBranch = ' . $bId;
    $rs  = $conn->Execute($sql);

    return ['id' => $rs->fields['sales'], 'name' => $rs->fields['pName']];
}
##

//回饋給地政士時，取得該仲介的業務
function getSalesBy($pId)
{
    global $PeopleInfo;

    return ['id' => $pId, 'name' => $PeopleInfo[$pId]['pName']];
}
##

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
##

//20240304 限定雲林
function targetAreaCheck(&$TCStore, $cTarget, $sId, $bId)
{
    if (empty($TCStore)) {
        return false;
    }

    //cTarget = 1:仲介、2:地政士、3特殊回饋地政士
    $storeId   = ($cTarget == 1) ? $bId : $sId;
    $storeType = ($cTarget == 1) ? 'realty' : 'scrivener';

    return in_array($storeId, $TCStore[$storeType]) ? true : false;
}
