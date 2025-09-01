<?php
if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}
header("Content-Type:text/html; charset=utf-8"); 

// unset($cCertifiedId);
// $cCertifiedId['100025895']['tBankLoansDate'] = '2021-11-12' ;
// $cCertifiedId['100025895']['cCertifiedId']= '100025895';

//總部回饋
$branchSPData = array();
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
//
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$branchSPData2[$rs->fields['bBranch']][] = $rs->fields['bId'];

	$rs->MoveNext();
}


##

//業務資料
$PeopleInfo = array();
$sql = "SELECT pName,pId FROM tPeopleInfo WHERE pDep = 7";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$PeopleInfo[$rs->fields['pId']]['id'] = $rs->fields['pId'];
	$PeopleInfo[$rs->fields['pId']]['name'] = $rs->fields['pName'];
	$rs->MoveNext();
}

$dafaultTwHouseSales = array('id'=>3,'name'=>'曾政耀'); //台屋預設業務(政耀)


$caseSalesCount = array();
$dataCaseFeed = array();
if (is_array($cCertifiedId)) {
	foreach ($cCertifiedId as $k => $v) {
		$sql ='
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
			WHERE 
			'.$query.'
			AND cas.cCertifiedId = "'.$v['cCertifiedId'].'"' ;

		//正常回饋

		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$Arr = $rs->fields;
			$Arr['tBankLoansDate'] = $v['tBankLoansDate'];
			
			//收入(保證費) //保證費/合約店家數/該店業務數後分算收入
			$Arr['branchCount'] = 0;//店家數
			if ($Arr['cBranchNum'] > 0) { $Arr['branchCount']++; }
			if ($Arr['cBranchNum1'] >0) { $Arr['branchCount']++; }
			if ($Arr['cBranchNum2'] >0) { $Arr['branchCount']++; }
			if ($Arr['cBranchNum3'] >0) { $Arr['branchCount']++; }

			##
			if ($Arr['sameCount'] > 1) {$Arr['sameStore'] = 1;} //確認是否有同店業務
				##案件總回饋計算##
				// echo $Arr['bid']."_".$Arr['cBranchNum']."_".$Arr['cBranchNum1'];
				

				if (($Arr['bid'] == $Arr['cBranchNum'])) {	

					if ($Arr['cCaseFeedback'] == 0) {
						setData($Arr,$Arr['bid'],$Arr['cBrand'],$Arr['brand'],$Arr['cFeedbackTarget'],$Arr['bCode'],$Arr['store'],$Arr['branch'],$Arr['cCaseFeedback'],$Arr['cCaseFeedBackMoney'],$Arr['bCategory']);

					}
					// echo 'A'.$Arr['store'];
					
				}elseif (($Arr['bid'] == $Arr['cBranchNum1']) && $Arr['cBranchNum1'] > 0) {
					// echo 'B'.$Arr['store1'];
					if ($Arr['cCaseFeedback1'] == 0) {
						setData($Arr,$Arr['bid'],$Arr['cBrand1'],$Arr['brand1'],$Arr['cFeedbackTarget1'],$Arr['bCode'],$Arr['store1'],$Arr['branch1'],$Arr['cCaseFeedback1'],$Arr['cCaseFeedBackMoney1'],$Arr['bCategory1']);
					}
				}elseif (($Arr['bid'] == $Arr['cBranchNum2']) && $Arr['cBranchNum2'] > 0) {
					// echo 'C'.$Arr['store2'];
					if ($Arr['cCaseFeedback2'] == 0) {
						setData($Arr,$Arr['bid'],$Arr['cBrand2'],$Arr['brand2'],$Arr['cFeedbackTarget2'],$Arr['bCode'],$Arr['store2'],$Arr['branch2'],$Arr['cCaseFeedback2'],$Arr['cCaseFeedBackMoney2'],$Arr['bCategory2']);
					}
				}elseif (($Arr['bid'] == $Arr['cBranchNum3']) && $Arr['cBranchNum3'] > 0) {
					// echo 'D'.$Arr['store3'];
					if ($Arr['cCaseFeedback2'] == 0) {
						setData($Arr,$Arr['bid'],$Arr['cBrand3'],$Arr['brand3'],$Arr['cFeedbackTarget3'],$Arr['bCode'],$Arr['store3'],$Arr['branch3'],$Arr['cCaseFeedback3'],$Arr['cCaseFeedBackMoney3'],$Arr['bCategory3']);
					}
				}elseif (($Arr['cTarget'] == 3 || $Arr['cSpCaseFeedBackMoney'] > 0) && empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkscrivenerSp'])) { //地政士特殊回饋	
					//12
					//setData($Arr,$bId,$brandId,$brandName,$feedbackTarget,$branchCode,$branchstore,$branch,$feedback,$feedBackMoney,$category,$cost ='')
					setData($Arr,$Arr['cScrivener'],2,$Arr['scrivener'],3,'SC'.str_pad($Arr['cScrivener'], "4","0",STR_PAD_LEFT),$Arr['scrivener'],'',0,$Arr['cSpCaseFeedBackMoney'],'sp');

				}
				//其他回饋
				if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['otherFeedCheck'])) {
					
						getOtherFeedForReport2($Arr,$v['cCertifiedId']);
					
				}
				


			$rs->MoveNext();
		}


		##案件總回饋計算END##
		

		
				
	}

	
}

unset($PeopleInfo);unset($dafaultTwHouseSales);
//排序
// b.bCategory AS order1,
// b.bBrand AS order2,
// cas.cCertifiedId as order3,
// csales.cSalesId as order4,


$sortArray = array();
// echo "<pre>";
foreach ($dataCaseFeed as $k => $v) {
	// echo $k;
	// $sort1 = $v['']
	foreach ($v as $key => $value) {
		
		
		if (in_array($value['salesId'], $sales_arr)) { //是查尋的業務且狀態是回饋

			$sortArray[$value['sort']][] = $value;
		}

	}
	
}
unset($dataCaseFeed);
ksort($sortArray);
// echo "<pre>";
// print_r($sortArray);
// die;
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("業績統計");
$objPHPExcel->getProperties()->setDescription("第一建經業績統計");

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('C1:E1');
//基本資料開始頁數
$row=8;//列
$c=65;//欄
//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1','範圍');
$objPHPExcel->getActiveSheet()->setCellValue('B1','時間');
$objPHPExcel->getActiveSheet()->setCellValue('C1','民國'.$start_y.'年'.$start_m.'月 ~ 民國'.$end_y.'年'.$end_m.'月');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'仲介品牌');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'仲介店名');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'公司名');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'平均保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'業績分配');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'業績');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'業務人員');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'簽約日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'仲介類別');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'結案日期');

$no = 1;
$row = 9;
foreach ($sortArray as $value) {
	// echo "<pre>";
	// print_r($v);
	// die;
	foreach ($value as $v) {
		$salesMoney = $v['avgCertifiedMoney']-$v['feedMoney'];

		// print_r($v['brand']);
		// ksort($v['brand']);
		// print_r($v['brand']);
		$storeData = storeSort($v['brand'],$v['code'],$v['store'],$v['branch'],$v['branchCategory'],$v['certifiedId']);

		

		$brand = @implode('_', $storeData['brand']);
		$code = @implode('_', $storeData['code']);
		$store = @implode('_', $storeData['store']);
		$branch = @implode('_', $storeData['branch']);
		$branchCategory = @implode('_', $storeData['branchCategory']);
		$salesCount = (empty($caseSalesCount[$v['certifiedId']]))?0:count($caseSalesCount[$v['certifiedId']]);

		if ($v['certifiedId'] == '101018516') {
			$salesCount = 1;
		}

		unset($storeData);

		$c=65;//欄
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,($no));
		$objPHPExcel->getActiveSheet()->getCell(chr($c++).$row)->setValueExplicit($v['certifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);


		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$brand);///C
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$code);//D
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$store);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$branch);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['owner']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['buyer']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['totalMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['certifiedMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['avgCertifiedMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['feedMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$salesCount);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$salesMoney);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['salesName']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['statusDate']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['signDate']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['finishDate']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['scrivener']);//
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['address']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['status']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$branchCategory);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['endDate']);


		//總業績
		$sales_total[$v['salesId']] +=$salesMoney;


		//月業績
		##
		if ($report_type==1) {
			if($date_type==1){	

				// preg_match_all("/(.*)-/U",$v['showSignDate'] , $tmp);
				$month = substr($v['signDate'], 0,6) ;
								
			}elseif ($date_type==2) {
								// cEndDate
				// preg_match_all("/(.*)-/U",$export_date[$v['cCertifiedId']] , $tmp);
				$month = substr($v['bankLoansDate'], 0,7) ;
				
			
			}

			// $month=$tmp[1][0].'-'.$tmp[1][1];
			$sales_month[$month] = $sales_month[$month]+$salesMoney;
		}

		$row++;
		$no++;
	}
	

}

##
function storeSort($brand,$code,$store,$branch,$branchCategory,$cId=''){

	// $code
	// $branchCategory
	$scrivener = array();
	$dataSort = array();
	$scrivenerCount = 0;
	$dataCount = 0;
	//地政士排後面
	// $max = count($code);
	for ($i=(count($code)-1); $i >= 0; $i--) { 
		
		if (substr($code[$i], 0,2) == 'SC') {
			$scrivener[$scrivenerCount]['brand'] = $brand[$i];
			$scrivener[$scrivenerCount]['code'] = $code[$i];
			$scrivener[$scrivenerCount]['store'] = $store[$i];
			$scrivener[$scrivenerCount]['branch'] = $branch[$i];
			$scrivener[$scrivenerCount]['branchCategory'] = $branchCategory[$i];

			$scrivenerCount++;
		}else{
			$dataSort[$dataCount]['brand'] = $brand[$i];
			$dataSort[$dataCount]['code'] = $code[$i];
			$dataSort[$dataCount]['store'] = $store[$i];
			$dataSort[$dataCount]['branch'] = $branch[$i];
			$dataSort[$dataCount]['branchCategory'] = $branchCategory[$i];

			$dataCount++;
		}
	}

	for ($i = 0 ; $i < $dataCount ; $i ++) {
		for ($j = 0 ; $j < $dataCount - 1 ; $j ++) {
			if ($dataSort[$j]['brand'] > $dataSort[$j+1]['brand']) {
				$tmp = $dataSort[$j] ;
				$dataSort[$j] = $dataSort[$j+1] ;
				$dataSort[$j+1] = $tmp ;
				unset($tmp) ;
			}
		}
	}


	

	$dataSort = array_merge($dataSort,$scrivener);
	unset($scrivener);
	$data = array();
	for ($i=0; $i < count($dataSort); $i++) { 
		$data['brand'][] = $dataSort[$i]['brand'];
		$data['code'][] = $dataSort[$i]['code'];
		$data['store'][] = $dataSort[$i]['store'];
		$data['branch'][] = $dataSort[$i]['branch'];
		$data['branchCategory'][] = $dataSort[$i]['branchCategory'];
	}
	
	
	return $data;

}	
function setData($Arr,$bId,$brandId,$brandName,$feedbackTarget,$branchCode,$branchstore,$branch,$feedback,$feedBackMoney,$category,$cost =''){
	global $dataCaseFeed;
	global $PeopleInfo;
	global $dafaultTwHouseSales;
	global $caseSalesCount;

	
					
		$Arr['SalesName'] = $PeopleInfo[$Arr['caseSalesID']]['name'];
		$Arr['caseSalesID'] = ($brandId == 1 || $brandId == 49)?$dafaultTwHouseSales['id']:$Arr['caseSalesID'];//台屋跟優美算政耀
		$Arr['SalesName'] = ($brandId == 1 || $brandId == 49)?$dafaultTwHouseSales['name']:$Arr['SalesName'];//台屋跟優美算政耀
		$Arr['cCaseFeedBackMoney'] = ($feedback == 1)? 0:$Arr['cCaseFeedBackMoney'];//不回饋0元
		// $Arr['sameCount'] = ($brandId == 1 || $brandId == 49)?1:$Arr['sameCount']; 
		//單店台屋但有區域士N個業務，所以只算一次
		if ($Arr['sameCount'] > 1 && $Arr['branchCount'] == 1 && $Arr['caseSalesID'] == 3) {
			$Arr['sameCount'] = 1;
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkTwOne'] = 1;
		}
		// 仲介類別
					

		//統計資料
		if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['brand'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['brand'] = array();}
		if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'] = array();}
		if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['store'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['store'] = array();}
		if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branch'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branch'] = array();}
		if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branchCategory'])) {$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branchCategory'] = array();}
		if (empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'])) {
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'] = 0;
		}
		
		//相同的店只算一次
		
		if (!in_array($branchCode,$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'])) {
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['code'][] = $branchCode;//店編
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['brand'][] = $brandName;//品牌
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['store'][] = $branchstore;//仲介店名
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branch'][] = $branch;//公司名
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['branchCategory'][] =  getCategory($category,$brandName);// 仲介類別

		}

		
		
		$caseSalesCount[$Arr['cCertifiedId']][$Arr['caseSalesID']]++;
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['certifiedId'] = $Arr['cCertifiedId'];//保證號碼
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['owner'] = $Arr['owner'];//賣
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['buyer'] = $Arr['buyer'];//買
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['totalMoney'] = $Arr['cTotalMoney'];//總價金
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['certifiedMoney'] = $Arr['cCertifiedMoney'];//合約保證費總額


		if ($feedback == 0 && $cost == '' && $feedbackTarget != 3) { //有選回饋才算收入(保證費) [他回饋保證費不能計算]
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'] += round($Arr['cCertifiedMoney']/$Arr['branchCount']/$Arr['sameCount']); //回饋同家店有兩人所以要除
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['showCount2'] += round(1/$Arr['branchCount']/$Arr['sameCount'],2); //新制
		}elseif ($Arr['cCertifiedId'] == '101018516') { //特殊案件要算給偉哲
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['avgCertifiedMoney'] += round($Arr['cCertifiedMoney']/$Arr['branchCount']/$Arr['sameCount']); //回饋同家店有兩人所以要除
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['showCount2'] = 1;
		}

		if ($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkTwOne'] == 1) {
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedMoney'] = $feedBackMoney;

		}else{
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedMoney'] += round($feedBackMoney/$Arr['sameCount']); //回饋金(同家店有兩人所以要除)
			// if ($cost == '1') {
			// 	echo '總:'.$Arr['caseSalesID']."_".$feedBackMoney."_".$Arr['sameCount']."_".$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedMoney']."<br>";	
			// }else{
			// 	echo ''.$Arr['caseSalesID']."_".$feedBackMoney."_".$Arr['sameCount']."_".$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedMoney']."<br>";

			// }
		}
		
	
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesName'] = $Arr['SalesName'];//業務人員
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['statusDate'] = ($Arr['caseStatus'] != 2)?dateformate($Arr['cEndDate']):dateformate($Arr['cSignDate']);//案件狀態日期

		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['signDate'] = dateformate($Arr['cSignDate']);//簽約日期
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['finishDate'] = dateformate($Arr['cFinishDate']);//實際點交日期
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['scrivener'] = $Arr['scrivener'];//地政士姓名
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['address'] = addr($Arr['cCertifiedId']);//標的物坐落
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['status'] = $Arr['status'];//狀態
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['endDate'] = dateformate($Arr['cEndDate']);
					 	
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['bankLoansDate']= $Arr['tBankLoansDate'];//保證費出款時間
		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['salesId'] = $Arr['caseSalesID'];//業務代碼


		$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['feedback'] = $feedback;//是否回饋

		//代書特殊回饋只算一次
		if ($feedbackTarget == 3) {
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['checkscrivenerSp'] = 1;
			$Arr['order1'] = 2;//排在仲介後

		}

		//其他回饋只算一次
		if ($cost) {
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['otherFeedCheck'] = 1;
		}


		//排序
		// b.bCategory AS order1,
		// b.bBrand AS order2,
		// cas.cCertifiedId as order3,
		// csales.cSalesId as order4,



		
		

		if ($brandId != 1 && empty($dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sort'] =$Arr['order1']."_".$brandId."_".$Arr['order3']."_".$Arr['order4'])) { //台屋不排
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sort'] =$Arr['order1']."_".$brandId."_".$Arr['order3']."_".$Arr['order4'];
		}
		
		
		if ($Arr['sameStore'] == 1) {
			$dataCaseFeed[$Arr['cCertifiedId']][$Arr['caseSalesID']]['sameStore'] = $Arr['sameStore'];
		}

		
}
function getCategory($category,$brand){
	global $conn;

	$categoryName = '';
	if($category==1 && $brand !='台灣房屋' && $brand !='非仲介成交' && $brand != '優美地產'){//加盟(其他品牌)
		$category ='加盟(其他品牌)';
						
		}elseif ($category == 1 && $brand == '台灣房屋') {//加盟(台灣房屋)
			$category ='加盟(台灣房屋)';
						
		}elseif ($category == 1 && $brand == '優美地產') {//加盟(優美地產)
			$category ='加盟(優美地產)';
						
		}elseif ($category == 1) {//加盟
			$category ='加盟';
						 
		}elseif ($category == 2) {//直營
			$category ='直營';
						 
		}elseif ($category == 3) {//非仲介成交
			$category ='非仲介成交';
						 
		}elseif($category == 'sp'){
			$category ='特殊回饋地政士';
						 
		}else{
			$category ='';
		}

	return $category;
}

function getOtherFeedForReport2($Arr,$certifiedId) {
	global $conn;
	// global $dataCaseFeed;
	// global $PeopleInfo;
	// global $dafaultTwHouseSales;
	// global $caseSalesCount;
	global $branchSPData;
	global $branchSPData2;




	$sql = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='".$certifiedId."' AND fDelete = 0 AND (fType = 1 OR fType = 2)";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);
	$total=$rs->RecordCount();
	$i = 0;
	// echo $total."<br>";
	
	if ($total ==0) {
		return false;
	}else{
		
		while (!$rs->EOF) {
			//1地政2仲介

			// print_r($branchSPData2[$rs->fields['fStoreId']]);



			if ($rs->fields['fType'] == 2 && !empty($branchSPData[$rs->fields['fStoreId']])) { //群組回饋

				$branchCount = array();
				

				$sql = "SELECT cSalesId,cBranch FROM tContractSales WHERE cCertifiedId = '".$certifiedId."' AND cBranch IN (".@implode(',', $branchSPData[$rs->fields['fStoreId']]).")";
				$rs2 = $conn->Execute($sql);
				while (!$rs2->EOF) {
					

					$branchCount[$rs2->fields['cBranch']]['sales'][$rs2->fields['cSalesId']] = $rs2->fields['cSalesId'];
					$branchCount[$rs2->fields['cBranch']]['store']++;
					$rs2->MoveNext();
				}

				foreach ($branchCount as $key => $value) {

					$Arr['sameCount'] = count($value['sales']);

					foreach ($value['sales'] as $k => $v) {
						$Arr['caseSalesID'] = $v;

						$tmp = getOtherFeed($rs->fields['fType'],$rs->fields['fStoreId']);
						$money = round($rs->fields['fMoney']/count($branchCount)); //先算仲介部分

						// echo $money;


						setData($Arr,$rs->fields['fStoreId'],$tmp['brandCode'],$tmp['brand'],1,$tmp['Code'],$tmp['Store'],$tmp['Name'],0,$money,$tmp['bCategory'],1);
						unset($tmp);
						// echo "<br>";
					}
					
					
				}

			}elseif($rs->fields['fType'] == 2 && !empty($branchSPData2[$rs->fields['fStoreId']])){ //品牌回饋
				$branchCount = array();
				

				$sql = "SELECT cSalesId,cBranch FROM tContractSales WHERE cCertifiedId = '".$certifiedId."' AND cBranch IN (".@implode(',', $branchSPData2[$rs->fields['fStoreId']]).")";
				$rs2 = $conn->Execute($sql);
				while (!$rs2->EOF) {
					

					$branchCount[$rs2->fields['cBranch']]['sales'][$rs2->fields['cSalesId']] = $rs2->fields['cSalesId'];
					$branchCount[$rs2->fields['cBranch']]['store']++;
					$rs2->MoveNext();
				}

				foreach ($branchCount as $key => $value) {

					// echo $rs->fields['fStoreId']."_";
					// print_r($value);


					$Arr['sameCount'] = count($value['sales']);

					foreach ($value['sales'] as $k => $v) {
						$Arr['caseSalesID'] = $v;

						$tmp = getOtherFeed($rs->fields['fType'],$rs->fields['fStoreId']);
						$money = round($rs->fields['fMoney']/count($branchCount)); //先算仲介部分

						// echo $money;


						setData($Arr,$rs->fields['fStoreId'],$tmp['brandCode'],$tmp['brand'],1,$tmp['Code'],$tmp['Store'],$tmp['Name'],0,$money,$tmp['bCategory'],1);
						unset($tmp);
						// echo "<br>";
					}
					
					
				}

				
				
				
			}else{
				//可能會有一個以上的業務
				$sales = explode(',', $rs->fields['fSales']);
				
				
					// $money = round($rs->fields['fMoney']/count($sales));
				$money = $rs->fields['fMoney'];
				$Arr['sameCount'] = count($sales);
						
				$tmp = getOtherFeed($rs->fields['fType'],$rs->fields['fStoreId']);
				//類型1地政2仲介

						foreach ($sales as $v) {
							$Arr['caseSalesID'] = $v;

							if ($rs->fields['fType'] == 2) {
								//回饋金對象(1:仲介、2:代書) //round($rs->fields['fMoney']/$total2)
								

								setData($Arr,$rs->fields['fStoreId'],$tmp['brandCode'],$tmp['brand'],1,$tmp['Code'],$tmp['Store'],$tmp['Name'],0,$money,$tmp['bCategory'],1);

							}elseif ($rs->fields['fType'] == 1) {
								setData($Arr,$rs->fields['fStoreId'],'','',1,$tmp['Code'],$tmp['Store'],$tmp['Name'],0,$money,'',1);
								 
							

							}
						}

					unset($tmp);
	
			}
			
			


			

			
			$rs->MoveNext();
		}
	}

	// die;
	
}


?>