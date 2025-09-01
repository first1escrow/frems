<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../opendb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
include_once '../openadodb.php' ;
include_once '../report/getBranchType.php';
include_once 'includes/maintain/feedBackData.php';


$tlog = new TraceLog() ;

//預載log物件
$logs = new Intolog() ;

// $_POST = escapeStr($_POST) ;
$input = array();
$input = $_POST;
$tab = $_POST['tab'];// $menu_tab = array('sales'=>'業務','brand'=>'品牌','brancharea'=>'區域(仲介)','scrivenerarea'=>'區域(地政士)','branch'=>'店家');
$check = true; // 檢查查詢店家是否有資料

// echo "<br>";

///tab title
$title = array();
// echo $tab;

if ($tab == 'sales') { //業績統計表
	$sql = "SELECT pId,pName FROM tPeopleInfo WHERE  (pDep = 7 OR  pDep = 4) AND pJob =1"; //
	$rs = $conn->Execute($sql);		
	while (!$rs->EOF) {
		$title[$rs->fields['pId']]['id'] = $rs->fields['pId'];
		$title[$rs->fields['pId']]['name'] = $rs->fields['pName'];
		$rs->MoveNext();
	}		
}elseif ($tab == 'brand') {
	$sql = "SELECT bId,bName FROM tBrand";
	$rs = $conn->Execute($sql);		
	while (!$rs->EOF) {
		$title[$rs->fields['bId']]['id'] = $rs->fields['bId'];
		$title[$rs->fields['bId']]['name'] = $rs->fields['bName'];
		$rs->MoveNext();
	}	
}elseif ($tab == 'storearea' ) {
	$sql = "SELECT zZip,zCity FROM tZipArea GROUP BY zCity";
	$rs = $conn->Execute($sql);	
	
	while (!$rs->EOF) {
		
		$title[$rs->fields['zCity']]['id'] = $rs->fields['zCity'];
		$title[$rs->fields['zCity']]['name'] = $rs->fields['zCity'];
		$rs->MoveNext();
	}	


}elseif($tab == 'brandCategory'){

	$title[12]['id'] = '加盟台灣房屋';
	$title[12]['name'] = '加盟台灣房屋';

	$title[1]['id'] = '加盟其他品牌';
	$title[1]['name'] = '其他品牌';

	$title[13]['id'] = '加盟優美地產';
	$title[13]['name'] = '優美地產';
	
	$title[14]['id'] = '加盟永春不動產';
	$title[14]['name'] = '加盟永春不動產';


	$title[2]['id'] = '直營';
	$title[2]['name'] = '直營';

	$title[3]['id'] = '非仲介成交';
	$title[3]['name'] = '非仲介成交';
	

}elseif($tab == 'branchGroup'){
	$sql = "SELECT bId,bName FROM tBranchGroup";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$title[$rs->fields['bId']]['id']  = $rs->fields['bId'];
		$title[$rs->fields['bId']]['name'] = $rs->fields['bName'];

		$rs->MoveNext();
	}

}else{
	die;
}



//tab contant

$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus<>"8"';//AND cas.cCaseStatus IN (2,3,4,9,10) 
$FeedBackMoneyQueryStr = '';

//時間種類
$startYear = $input['startYear']+1911;
$endYear = $input['endYear']+1911;
$data = array();

if ($input['timeCategory'] == 'y') { //year
	$startDate = $startYear."-01-01 00:00:00";
	$endDate = $endYear."-12-31 23:59:59";


	for ($i=$startYear; $i <=$endYear ; $i++) { 
		$data[$i."年"] = array();
				
	}

	// die;
		
}elseif ($input['timeCategory'] == 's') { //season

	if ($input['s_season'] == 'S1') {
		$startDate = $startYear."-01-01 00:00:00";
		
	}elseif($input['s_season'] == 'S2'){
		$startDate = $startYear."-04-01 00:00:00";
		
	}elseif ($input['s_season'] == 'S3') {
		$startDate = $startYear."-07-01 00:00:00";
		
	}elseif ($input['s_season'] == 'S4') {
		$startDate = $startYear."-10-01 00:00:00";
		
	}

	if ($input['e_season'] == 'S1') {
		$endDate = $endYear."-03-31 23:59:59";
		
	}elseif ($input['e_season'] == 'S2') {
		$endDate = $endYear."-06-30 23:59:59";
		
	}elseif ($input['e_season'] == 'S3') {
		$endDate = $endYear."-09-31 23:59:59";
		
	}elseif ($input['e_season'] == 'S4') {
		$endDate = $endYear."-12-31 23:59:59";
		
	}

	for ($i=$startYear; $i <=$endYear ; $i++) { 
		$ss = 1;
		$se = 4;
		if ($i == $startYear) {
			$ss = (int)substr($input['s_season'], 1,1);
		}

		if ($i == $endYear) {
			$se = (int)substr($input['e_season'], 1,1);
		}

			// $data[$i."-s".$j] = array();
				
			// unset($tmp);

		for ($j=$ss; $j <=$se ; $j++) { 

			$data[$i."年第".$j."季"] = array();
		}
	}

	// print_r($data);

	unset($ss);unset($se);

}elseif ($input['timeCategory'] == 'm') {//month
		$startDate = $startYear."-".$input['s_month']."-01 00:00:00";
		$endDate = $endYear."-".$input['e_month']."-31 23:59:59";

		
		for ($i=$startYear; $i <=$endYear ; $i++) { 
			$m1 = 1;//開始月
			$m2 = 12;//結束月
			if ($i == $startYear) {
				$m1 = (int)$input['s_month'];
			}

			if ($i == $endYear) {
				$m2 = (int)$input['e_month'];
			}

			for ($j=$m1; $j <=$m2 ; $j++) { 

				$data[$i."年".str_pad($j, 2,0,STR_PAD_LEFT)."月"] = array();
				
				unset($tmp);
			}
		}


}

if (!empty($query)) {
	$query .= " AND ";
}
if ($input['dateCategory'] == 1) { // 進案
	// $query .= '(cas.cApplyDate >= "'.$startDate.'" AND cas.cApplyDate<="'.$endDate.'")';
	$query .= "cas.cApplyDate BETWEEN  '".$startDate."' AND '".$endDate."'";
}elseif ($input['dateCategory'] == 2) { // 簽約
	// $query .= '(cas.cSignDate >= "'.$startDate.'" AND cas.cSignDate <= "'.$endDate.'")';
	$query .= "cas.cSignDate BETWEEN  '".$startDate."' AND '".$endDate."'";

}elseif($input['dateCategory'] == 3){ // 結案
	// $query .= '(cas.cEndDate >= "'.$startDate.'" AND cas.cEndDate <= "'.$endDate.'")';
	$query .= "cas.cEndDate BETWEEN  '".$startDate."' AND '".$endDate."'";

}
unset($m);unset($startYear);unset($endYear);
##
//品牌

if ($input['brand']) {
	if (!empty($query)) { 	$query .= " AND ";}
	$query .= " (rea.cBrand = '".$input['brand']."' OR rea.cBrand1 = '".$input['brand']."' OR rea.cBrand2 = '".$input['brand']."' OR rea.cBrand3 = '".$input['brand']."')";

}

//地區
//只要業務負責的店跟地政士有就算

if (!empty($input['zip']) || $input['city'] != '0') {
	
	if (!empty($input['zip'])) {//區
		$zip_str = "zZip = '".$input['zip']."'";
	}else {//縣市
		$zip_str = "zCity = '".$input['city']."'";
	}

	$zipArray = array();
	$sql = "SELECT zZip FROM tZipArea WHERE ".$zip_str;
	
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($zipArray, $rs->fields['zZip']);
		
		$rs->MoveNext();
	}
	unset($zip_str);
	
	//仲介
	$store_b = array();
	$store_str = '';
	$sql = "SELECT bId FROM tBranch WHERE bZip IN (".@implode(',', $zipArray).")";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($store_b, $rs->fields['bId']);
		
		$rs->MoveNext();
	}
	// print_r($store_b);


	if (!empty($store_b)) { //檢查是否有資料
		$store_str .= "rea.cBranchNum IN(".@implode(',', $store_b).") OR rea.cBranchNum1 IN(".@implode(',', $store_b).") OR rea.cBranchNum2 IN(".@implode(',', $store_b).") OR rea.cBranchNum3 IN(".@implode(',', $store_b).")";
	}
	
	//地政士
	$store_s = array();
	$sql = "SELECT sId FROM tScrivener WHERE sCpZip1 IN (".@implode(',', $zipArray).")";
	// print_r($input['scrivener']);
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($store_s, $rs->fields['sId']);

		$rs->MoveNext();
	}

	// print_r(expression)

	if (!empty($store_s)) {
		
		if (!empty($store_str)) { $store_str .= ' OR ';}
		
		
		$store_str .= " cs.cScrivener IN(".@implode(',', $store_s).")";

	}
	
	if (!is_array($input['branch']) && !is_array($input['scrivener'])) { //沒有搜尋店家跟地政式
		if (!empty($query)) {$query .= " AND ";}

		$query .= "(".$store_str.")";
	}
	

	

	unset($store_b);unset($store_s);unset($store_str);
}

//銀行
if ($input['bank']) {
	if (!empty($query)) {$query .= " AND ";}
	$query .= " cas.cBank = '".$input['bank']."'";

}


//搜尋店家
if (is_array($input['branch'])) {

	//有搜尋地區
	

	if (is_array($zipArray)) {
		$str = " AND bZip IN(".@implode(',', $zipArray).")";
	}

	$store_b = array();

	$sql = "SELECT bId FROM tBranch WHERE bId IN(".@implode(',', $input['branch']).")".$str;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($store_b, $rs->fields['bId']);
		$rs->MoveNext();
	}

	if (!empty($store_b)) {
		if (!empty($query)) {$query .= " AND ";}
		$query .= "(rea.cBranchNum IN(".@implode(',', $store_b).") OR rea.cBranchNum1 IN(".@implode(',', $store_b).") OR rea.cBranchNum2 IN(".@implode(',', $store_b).") OR rea.cBranchNum3 IN(".@implode(',', $store_b)."))";

		
		$FeedBackMoneyQueryStr .= " AND (fType = 2 AND fStoreId IN ('".@implode(',', $store_b)."'))"; //類型1地政2仲介

		$check = true;

	}else{
		//查無店家 rea.cBranchNum不會有英文
		// $query .= "(rea.cBranchNum IN('A') OR rea.cBranchNum1 IN('A') OR rea.cBranchNum2 IN('A') OR rea.cBranchNum3 IN('A'))"; // 
		$check = false;
	}

	
	unset($store_b);unset($str);
}

//搜尋地政士

if (is_array($input['scrivener']) ) {

	$store_s = array();

	if (is_array($zipArray)) {
		$str = " AND sCpZip1 IN(".@implode(',', $zipArray).")";
	}

	$sql = "SELECT sId FROM tScrivener WHERE sId IN(".@implode(',', $input['scrivener']).")".$str;

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($store_s, $rs->fields['sId']);
		$rs->MoveNext();
	}

	if (!empty($store_s)) {
		if (!empty($query)) {$query .= " AND ";}
		$query .= "cs.cScrivener IN(".@implode(',', $store_s).")";
		
		$FeedBackMoneyQueryStr .= " AND (fType = 1 AND fStoreId = '".@implode(',', $store_s)."')"; //類型1地政2仲介

	}else{
		//查無店家 rea.cBranchNum不會有英文
		// $query .= "cs.cScrivener IN('A')"; // 
		$check = false;
	}
	

	unset($store_s);unset($str);

}
unset($zipArray);

// print_r($input);

if ($check == true) {



	$OtherFeedBackData = array();
	//其他回饋(仲介)
	$sql = "SELECT 
				fbm.fCertifiedId,
				fbm.fType,
				fbm.fStoreId,
				fbm.fMoney,
				b.bCategory AS category,
				b.bBrand AS brand,
				(SELECT zCity FROM tZipArea WHERE zZip = bZip) AS city
			FROM
				tFeedBackMoney AS fbm
			LEFT JOIN
				tBranch AS b ON b.bId = fbm.fStoreId
			WHERE
				fbm.fDelete = 0 AND fbm.fType = 2 ".$FeedBackMoneyQueryStr;
	// echo $sql;

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$OtherFeedBackData[$rs->fields['fCertifiedId']][] = $rs->fields;

		$rs->MoveNext();
	}
	//其他回饋(地政)
	$sql = "SELECT 
				fCertifiedId,
				fType,
				fStoreId,
				fMoney,
				fSales,
				(SELECT zCity FROM tZipArea WHERE zZip = sCpZip1) AS city
			FROM
				tFeedBackMoney AS fbm
			LEFT JOIN
				tScrivener AS s ON s.sId = fbm.fStoreId
			WHERE
				fbm.fDelete = 0 AND fbm.fType = 1 ".$FeedBackMoneyQueryStr;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$OtherFeedBackData[$rs->fields['fCertifiedId']][] = $rs->fields;

		$rs->MoveNext();
	}

	$query = ' WHERE '.$query ; 

	//查詢符合的案件
	$sql ='
		SELECT 
			cas.cCertifiedId
		FROM 
			tContractCase AS cas 
		LEFT JOIN 
			tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId = cas.cCertifiedId
		'.$query.'
		' ;
		// echo $sql."<br>";

		unset($query);

		$list = array();
		$rs = $conn->Execute($sql);
		$totalCount = $rs->RecordCount();
		// echo '總數'.$totalCount."<br>";
		while (!$rs->EOF) {
			array_push($list, $rs->fields['cCertifiedId']);
			// echo $rs->fields['cCertifiedId']."<br>";
			$rs->MoveNext();
		}


		if ($tab == '!!') {
			foreach ($list as $k => $v) {
				$sql ='
					SELECT 
						cas.cCertifiedId,
						cas.cApplyDate,
						cas.cSignDate,
						cas.cEndDate,
						rea.cBrand as brand,
						rea.cBrand1 as brand1,
						rea.cBrand2 as brand2,
						rea.cBrand2 as brand3,
						rea.cBranchNum as branch,
						rea.cBranchNum1 as branch1,
						rea.cBranchNum2 as branch2,
						rea.cBranchNum3 as branch3,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum) AS branchCity,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum1) AS branchCity1,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum2) AS branchCity2,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum3) AS branchCity3,
						cas.cCaseFeedBackMoney,
						cas.cCaseFeedBackMoney1,
						cas.cCaseFeedBackMoney2,
						cas.cCaseFeedBackMoney3,
						cas.cSpCaseFeedBackMoney,
						cas.cCaseFeedback,
						cas.cCaseFeedback1,
						cas.cCaseFeedback2,
						cas.cCaseFeedback3,
						cas.cFeedbackTarget,
						cas.cFeedbackTarget1,
						cas.cFeedbackTarget2,
						cas.cFeedbackTarget3,
						cs.cScrivener,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip =sCpZip1) FROM tScrivener WHERE sId = cs.cScrivener) AS scrivenerCity,
						ci.cTotalMoney,
						ci.cCertifiedMoney
					FROM 
						tContractCase AS cas 
					LEFT JOIN 
						tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
					LEFT JOIN
						tContractScrivener AS cs ON cs.cCertifiedId = cas.cCertifiedId
					LEFT JOIN
						tContractIncome AS ci ON ci.cCertifiedId=cas.cCertifiedId
					WHERE
					cas.cCertifiedId= "'.$v.'"' ; 
				$rs = $conn->Execute($sql);


					
			}
		}else{
			//查詢明細
			foreach ($list as $k => $v) {
				// $totalData['TT']++;
				
				$sql ='
					SELECT 
						cas.cCertifiedId,
						cas.cApplyDate,
						cas.cSignDate,
						cas.cEndDate,
						rea.cBrand as brand,
						rea.cBrand1 as brand1,
						rea.cBrand2 as brand2,
						rea.cBrand2 as brand3,
						rea.cBranchNum as branch,
						rea.cBranchNum1 as branch1,
						rea.cBranchNum2 as branch2,
						rea.cBranchNum3 as branch3,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum) AS branchCity,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum1) AS branchCity1,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum2) AS branchCity2,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) AS city FROM tBranch WHERE bId= rea.cBranchNum3) AS branchCity3,
						(SELECT bGroup FROM tBranch WHERE bId = rea.cBranchNum) AS branchGroup,
						(SELECT bGroup FROM tBranch WHERE bId = rea.cBranchNum1) AS branchGroup1,
						(SELECT bGroup FROM tBranch WHERE bId = rea.cBranchNum2) AS branchGroup2,
						(SELECT bGroup FROM tBranch WHERE bId = rea.cBranchNum3) AS branchGroup3,
						(SELECT bGroup2 FROM tBranch WHERE bId = rea.cBranchNum) AS branchGroup2,
						(SELECT bGroup2 FROM tBranch WHERE bId = rea.cBranchNum1) AS branchGroup21,
						(SELECT bGroup2 FROM tBranch WHERE bId = rea.cBranchNum2) AS branchGroup22,
						(SELECT bGroup2 FROM tBranch WHERE bId = rea.cBranchNum3) AS branchGroup23,
						cas.cCaseFeedBackMoney,
						cas.cCaseFeedBackMoney1,
						cas.cCaseFeedBackMoney2,
						cas.cCaseFeedBackMoney3,
						cas.cSpCaseFeedBackMoney,
						cas.cCaseFeedback,
						cas.cCaseFeedback1,
						cas.cCaseFeedback2,
						cas.cCaseFeedback3,
						cas.cFeedbackTarget,
						cas.cFeedbackTarget1,
						cas.cFeedbackTarget2,
						cas.cFeedbackTarget3,
						cs.cScrivener,
						(SELECT (SELECT zCity FROM tZipArea WHERE zZip =sCpZip1) FROM tScrivener WHERE sId = cs.cScrivener) AS scrivenerCity,
						ci.cTotalMoney,
						ci.cCertifiedMoney
					FROM 
						tContractCase AS cas 
					LEFT JOIN 
						tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
					LEFT JOIN
						tContractScrivener AS cs ON cs.cCertifiedId = cas.cCertifiedId
					LEFT JOIN
						tContractIncome AS ci ON ci.cCertifiedId=cas.cCertifiedId
					WHERE
					cas.cCertifiedId= "'.$v.'"
					' ; 
				$rs = $conn->Execute($sql);
				
				$dataDetail = $rs->fields;

				if ($input['dateCategory'] == 1) {
					$date = $dataDetail['cApplyDate'];
				}elseif ($input['dateCategory'] == 2) {
					$date = $dataDetail['cSignDate'];
				}elseif ($input['dateCategory'] == 3) {
					$date = $dataDetail['cEndDate'];
				}

				if ($input['timeCategory'] == 'y') {
					$date = substr($date, 0,4)."年";
				}elseif ($input['timeCategory'] == 's') {
					$m = (int)substr($date, 5,2);
					if ($m >=1 && $m <=3) {
						$date = substr($date, 0,4)."年第1季";
					}elseif ($m >=4 && $m <=6) {
						$date = substr($date, 0,4)."年第2季";
						
					}elseif ($m >=7 && $m <=9) {
						$date = substr($date, 0,4)."年第3季";
					}elseif ($m >=10 && $m <=12) {
						$date = substr($date, 0,4)."年第4季";
					}
					
					unset($m);
				}elseif ($input['timeCategory'] == 'm') {
					$m = (int)substr($date, 5,2);
					
					// die;
					// echo $m."_";
					if ($m >=1 && $m <=3) {
						$date = substr($date, 0,4)."年".substr($date, 5,2)."月";
					}elseif ($m >=4 && $m <=6) {
						$date = substr($date, 0,4)."年".substr($date, 5,2)."月";
					}elseif ($m >=7 && $m <=9) {
						$date = substr($date, 0,4)."年".substr($date, 5,2)."月";
					}elseif ($m >=10 && $m <=12) {
						$date = substr($date, 0,4)."年".substr($date, 5,2)."月";
					}
					
					unset($m);
				}

				if ($tab == 'sales') {
					$salesList = array();
					$sales = array();

					if ($dataDetail['branch'] > 0) {
						//回饋金對象(1:仲介、2:代書)
						$checkDate = ($dataDetail['cSignDate'] == '0000-00-00 00:00:00')? $dataDetail['cApplyDate']:$dataDetail['cSignDate'];
						if ($dataDetail['cFeedbackTarget'] == 1 && $dataDetail['branch'] != 505) {

							
							$sales = getSales($dataDetail['branchCity'],$checkDate);
						}else{
							$sales = getSales($dataDetail['scrivenerCity'],$checkDate);
						}
						
						
						
						
						$salesCount = count($sales);
						foreach ($sales as $key => $value) {
							if ($dataDetail['cCaseFeedback'] == 0) {
								$salesList[$value]['feedbackMoney'] += round($dataDetail['cCaseFeedBackMoney']/$salesCount);
							}
							$salesList[$value]['count']++;
							// $salesList[$value]['certifiedMoney'] += round($dataDetail['cCertifiedMoney']/$salesCount);
							// $salesList[$value]['totalMoney'] += round($dataDetail['cTotalMoney']/$salesCount);
						}
						
						// unset($sales);
					}


					if ($dataDetail['branch1'] > 0) {
						$sales = array();
						//回饋金對象(1:仲介、2:代書)
						$checkDate = ($dataDetail['cSignDate'] == '0000-00-00 00:00:00')? $dataDetail['cApplyDate']:$dataDetail['cSignDate'];
						
						if ($dataDetail['cFeedbackTarget1'] == 1 && $dataDetail['branch1'] != 505) {
							$sales = getSales($dataDetail['branchCity1'],$checkDate);
						}else{
							$sales = getSales($dataDetail['scrivenerCity'],$checkDate);
						}
						
						
						$salesCount = count($sales);
						foreach ($sales as $key => $value) {
							if ($dataDetail['cCaseFeedback1'] == 0) {
								$salesList[$value]['feedbackMoney'] += round($dataDetail['cCaseFeedBackMoney1']/$salesCount);
							}
							$salesList[$value]['count']++;
							// $salesList[$value]['certifiedMoney'] += round($dataDetail['cCertifiedMoney']/$salesCount);
							// $salesList[$value]['totalMoney'] += round($dataDetail['cTotalMoney']/$salesCount);
						}
						
					}

					if ($dataDetail['branch2'] > 0) {
						$sales = array();
						//回饋金對象(1:仲介、2:代書)
						$checkDate = ($dataDetail['cSignDate'] == '0000-00-00 00:00:00')? $dataDetail['cApplyDate']:$dataDetail['cSignDate'];
						
						if ($dataDetail['cFeedbackTarget2'] == 1 && $dataDetail['branch2'] != 505) {
							$sales = getSales($dataDetail['branchCity2'],$checkDate);
						}else{
							$sales = getSales($dataDetail['scrivenerCity2'],$checkDate);
						}
						
						
						$salesCount = count($sales);
						foreach ($sales as $key => $value) {
							if ($dataDetail['cCaseFeedback2'] == 0) {
								$salesList[$value]['feedbackMoney'] += round($dataDetail['cCaseFeedBackMoney2']/$salesCount);
							}
							$salesList[$value]['count']++;
							// $salesList[$value]['certifiedMoney'] += round($dataDetail['cCertifiedMoney']/$salesCount);
							// $salesList[$value]['totalMoney'] += round($dataDetail['cTotalMoney']/$salesCount);
						}
						
					}

					if ($dataDetail['branch3'] > 0) {
						$sales = array();
						//回饋金對象(1:仲介、2:代書)
						$checkDate = ($dataDetail['cSignDate'] == '0000-00-00 00:00:00')? $dataDetail['cApplyDate']:$dataDetail['cSignDate'];
						
						if ($dataDetail['cFeedbackTarget3'] == 1 && $dataDetail['branch3'] != 505) {
							$sales = getSales($dataDetail['branchCity3'],$checkDate);
						}else{
							$sales = getSales($dataDetail['scrivenerCity3'],$checkDate);
						}
						
						
						$salesCount = count($sales);
						foreach ($sales as $key => $value) {
							if ($dataDetail['cCaseFeedback3'] == 0) {
								$salesList[$value]['feedbackMoney'] += round($dataDetail['cCaseFeedBackMoney3']/$salesCount);
							}
							$salesList[$value]['count']++;
							// $salesList[$value]['certifiedMoney'] += round($dataDetail['cCertifiedMoney']/$salesCount);
							// $salesList[$value]['totalMoney'] += round($dataDetail['cTotalMoney']/$salesCount);
						}
						
					}

					if ($dataDetail['cSpCaseFeedBackMoney'] > 0) {
						$checkDate = ($dataDetail['cSignDate'] == '0000-00-00 00:00:00')? $dataDetail['cApplyDate']:$dataDetail['cSignDate'];
						
						$sales = getSales($dataDetail['scrivenerCity3'],$checkDate);

						$salesCount = count($sales);
						foreach ($sales as $key => $value) {
							if ($dataDetail['cCaseFeedback3'] == 0) {
								$salesList[$value]['feedbackMoney'] += round($dataDetail['cCaseFeedBackMoney3']/$salesCount);
							}
							$salesList[$value]['count']++;
							// $salesList[$value]['certifiedMoney'] += round($dataDetail['cCertifiedMoney']/$salesCount);
							// $salesList[$value]['totalMoney'] += round($dataDetail['cTotalMoney']/$salesCount);
						}

					}

					if (is_array($OtherFeedBackData[$dataDetail['cCertifiedId']])) {
						foreach ($OtherFeedBackData[$dataDetail['cCertifiedId']] as $key => $value) {
							$checkDate = ($dataDetail['cSignDate'] == '0000-00-00 00:00:00')? $dataDetail['cApplyDate']:$dataDetail['cSignDate'];
						
							$sales = getSales($value['city'],$checkDate);
							$salesCount = count($sales);
							foreach ($sales as $key2 => $value2) {

								// print_r($value2);
								
								$salesList[$value2]['feedbackMoney'] += round($value['fMoney']/$salesCount);

								$salesList[$value2]['count']++;
								// $salesList[$value]['certifiedMoney'] += round($dataDetail['cCertifiedMoney']/$salesCount);
								// $salesList[$value]['totalMoney'] += round($dataDetail['cTotalMoney']/$salesCount);
							}
							
								
						}
					}

					unset($checkDate);

					// if ($dataDetail['cCertifiedId'] == '090445827') {
					// 	echo $dataDetail['cCertifiedId'];
					// 	print_r($salesList);
					// 	echo "<br>";
					// 	die;
					// }
					
					// die;

					if (!empty($salesList)) {
						$count = count($salesList);
						foreach ($salesList as $key => $value) {
							// if ($dataDetail['cCertifiedId']== '070351011') {
							// 	echo $key."_";print_r($value);
							// }
							// if ($key == '26') {
							// 	echo $dataDetail['cCertifiedId'];
							// 	echo "<br>";
							// 	// die;
							// }
							$data[$date][$key]['feedbackMoney'] += $value['feedbackMoney'];
							$data[$date][$key]['count'] += round((1/$count),2);
							$data[$date][$key]['certifiedMoney'] += round(($dataDetail['cCertifiedMoney']/$count),2);
							$data[$date][$key]['totalMoney'] += round(($dataDetail['cTotalMoney']/$count),2);

						}
					}else{
						// 	echo $dataDetail['cCertifiedId'];
						// print_r($salesList);
						// echo "<br>";
					}

					// if ($dataDetail['cCertifiedId']== '070351011') {
					// 	die;
					// }
					

					
				
				}else if ($tab == 'brand' || $tab == 'storearea' || $tab == 'brandCategory' ) {
					// echo 'brand';
					// $count = 0;
					//比對用
					// $ff[0] = $dataDetail['cCaseFeedBackMoney'];
					// $ff[1] = $dataDetail['cCaseFeedBackMoney1'];
					// $ff[2] = $dataDetail['cCaseFeedBackMoney2'];
					// $ff[3] = $dataDetail['cCaseFeedBackMoney3'];
					// $ff['sp'] = $dataDetail['cSpCaseFeedBackMoney'];
					// $ff['total'] = $dataDetail['cCaseFeedBackMoney']+$dataDetail['cCaseFeedBackMoney1']+$dataDetail['cCaseFeedBackMoney2']+$dataDetail['cCaseFeedBackMoney3']+$dataDetail['cSpCaseFeedBackMoney'];
					$checkVal = '';$checkVal1 = '';$checkVal2 = '';$checkVal3 = '';$checkScrivener = '';

					$list = array();
					//設定對應查詢的關鍵職
					if ($tab == 'brand') {
						
						if ($dataDetail['branch'] > 0) {
							$checkVal = $dataDetail['brand']; //
							
						}

						if ($dataDetail['branch1'] > 0) {
							$checkVal1 = $dataDetail['brand1'];
						}

						if ($dataDetail['branch2'] > 0) {
							$checkVal2 = $dataDetail['brand2'];
						}

						if ($dataDetail['branch3'] > 0) {
							$checkVal3 = $dataDetail['brand3'];
						}

					}elseif ($tab =='storearea') {
						if ($dataDetail['branch'] > 0) {
							if ($dataDetail['cFeedbackTarget'] == 1) { //回饋金對象(1:仲介、2:代書)
								$checkVal = $dataDetail['branchCity']; //
							}else{
								$checkVal = $dataDetail['scrivenerCity'];
							}						
						}

						if ($dataDetail['branch1'] > 0) {
							if ($dataDetail['cFeedbackTarget1'] == 1) {
								$checkVal1 = $dataDetail['branchCity1'];
							}else{
								$checkVal1 = $dataDetail['scrivenerCity'];
							}
							
						}

						if ($dataDetail['branch2'] > 0) {
							if ($dataDetail['cFeedbackTarget2'] == 1) {
								$checkVal2 = $dataDetail['branchCity2'];
							}else{
								$checkVal2 = $dataDetail['scrivenerCity'];
							}
						}

						if ($dataDetail['branch3'] > 0) {
							if ($dataDetail['cFeedbackTarget3'] == 1) {
								$checkVal3 = $dataDetail['branchCity3'];
							}else{
								$checkVal3 = $dataDetail['scrivenerCity'];
							}
						}

						if ($dataDetail['cSpCaseFeedBackMoney'] > 0) {
							$checkScrivener = $dataDetail['scrivenerCity'];
						}
					}elseif ($tab == 'brandCategory') {
						if ($dataDetail['branch'] > 0) {
							$checkVal =  checkCat($conn,$dataDetail['branch'],$dataDetail['brand']);  //
							
						}

						if ($dataDetail['branch1'] > 0) {
							$checkVal1 =  checkCat($conn,$dataDetail['branch1'],$dataDetail['brand1']);  //
						}

						if ($dataDetail['branch2'] > 0) {
							$checkVal2 =  checkCat($conn,$dataDetail['branch2'],$dataDetail['brand2']);  //
						}
						if ($dataDetail['branch3'] > 0) {
							$checkVal3 =  checkCat($conn,$dataDetail['branch3'],$dataDetail['brand3']);
						}
					}
					
					//計算
					if ($dataDetail['branch'] > 0  ) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
									
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
								// if ($dataDetail['cCertifiedId'] == '007097458') {
								// 		echo $checkVal."_". $dataDetail['cCaseFeedBackMoney']."<br>";
								// 	}
							}	
						}
											
						$list[$checkVal]['count']++;
					}

					if ($dataDetail['branch1'] > 0 ) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand1']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal1]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch1'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal1]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal1]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
							}	

							// if ($dataDetail['cCertifiedId'] == '007097458') {
							// 	echo $checkVal."_". $dataDetail['cCaseFeedBackMoney1']."<br>";
							// }
						}
						$list[$checkVal1]['count']++;

						
					}

					if ($dataDetail['branch2'] > 0) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand2']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];

									
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch2'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];

								// if ($dataDetail['cCertifiedId'] == '007097458') {
								// 		echo $checkVal2."_".$dataDetail['cCaseFeedBackMoney2']."<br>";
								// 	}
							}	
						}

						$list[$checkVal2]['count']++;
						
						
					}

					if ($dataDetail['branch3'] > 0) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand3']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal3]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch3'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal3]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal3]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];

								// if ($dataDetail['cCertifiedId'] == '007097458') {
								// 		echo $checkVal3."_".$dataDetail['cCaseFeedBackMoney3']."<br>";
								// 	}
							}	
						}

						$list[$checkVal3]['count']++;

						
						
					}

					
					if ($dataDetail['cSpCaseFeedBackMoney'] > 0) {
						// if ($dataDetail['cCertifiedId'] == '090050741') {
						// 	echo $checkVal."_";
						// 	echo $dataDetail['cSpCaseFeedBackMoney'];

						// }
						$list[$checkVal]['feedbackMoney'] += $dataDetail['cSpCaseFeedBackMoney'];
						// $list[$checkScrivener]['count']++;

						// if ($dataDetail['cCertifiedId'] == '007097458') {
						// 				echo $checkScrivener.'_'.$dataDetail['cSpCaseFeedBackMoney']."<br>";
						// 			}

						
					}

					//涉及到回饋所以要算其他回饋的品牌
					// 

					if (is_array($OtherFeedBackData[$dataDetail['cCertifiedId']])) {
						
						foreach ($OtherFeedBackData[$dataDetail['cCertifiedId']] as $key => $value) {
							if ($value['fType'] == 1) { // 類型1地政2仲介
								if ($tab == 'brand') {
									$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
									// $list[$checkScrivener]['count']++;
									
								}elseif($tab == 'storearea'){
									$list[$value['city']]['feedbackMoney'] += $value['fMoney'];
									// $list[$value['city']]['count']++;

									
								}
								
								
							}else{
								if (!empty($input['brand'])) {
									
									if ($input['brand'] == $value['brand']) { //查詢品牌
										if ($tab == 'brand') {
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['brand']]['count']++;
											
										}elseif($tab == 'storearea'){
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['city']]['count']++;

											
										}
									}elseif ($input['branch'] == $value['fStoreId']) {  //查詢仲介
										if ($tab == 'brand') {
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['brand']]['count']++;
											
										}elseif($tab == 'storearea'){
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['city']]['count']++;
											
										}
									}

								}elseif(!empty($input['branch'])){

									if ($input['branch'] == $value['branch']) { //查詢品牌
										if ($tab == 'branch') {
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['branch']]['count']++;
										}elseif($tab == 'storearea'){
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['city']]['count']++;
										}
									}elseif ($input['branch'] == $value['fStoreId']) {  //查詢仲介
										if ($tab == 'branch') {
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['branch']]['count']++;
										}elseif($tab == 'storearea'){
											$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
											// $list[$value['city']]['count']++;
										}
									}

								}else{
									if ($tab == 'brand') {
										$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
										// $list[$checkVal]['count']++;

										
									}elseif($tab == 'storearea'){
										$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
										// $list[$value['city']]['count']++;

									}
								}
								
								


							}

						
						}


					}



					$count = count($list);

					if ($_SESSION['member_id'] == 6) {
						// if ($count > 1) {
						// 	print_r($list);
						// 	echo $dataDetail['cCertifiedId']."_";
						// 	die;
						// }
						// echo "<pre>";
						// print_r($list);
						
					}

					// if ($dataDetail['cCertifiedId'] == '007097458') {
									
					// 				print_r($list);
					// 				echo "<br>";
					// 			}
					foreach ($list as $key => $value) {

						if ($_SESSION['member_id'] == 6) {
							if ($key == '75') {

								// if ($dataDetail['cCertifiedId'] == '007097458') {
									// echo $dataDetail['cCertifiedId']."_".round((1/$count),2)."_".$value['feedbackMoney'];
								// 	print_r($value);
									// echo "<br>";
								// }

								// echo $dataDetail['cCertifiedId']."_".$count."_".$value['feedbackMoney']."=".round((1/$count),2);
								// 	print_r($value);

								// 		echo "<br>";
								
							}
					
						}

						
						
						// $RR = $value['feedbackMoney'];
						$data[$date][$key]['count'] += round((1/$count),2);

						
						$data[$date][$key]['feedbackMoney'] += $value['feedbackMoney'];
						$data[$date][$key]['certifiedMoney'] += round(($dataDetail['cCertifiedMoney']/$count),2);
						$data[$date][$key]['totalMoney'] += round(($dataDetail['cTotalMoney']/$count),2);

						
						//比對用
						// $totalData['count'] += round((1/$count),4);
						// $totalData['feedbackMoney'] += round(($value['feedbackMoney']/$count),2);
						// $totalData['certifiedMoney'] += round(($dataDetail['cCertifiedMoney']/$count),2);
						// $totalData['totalMoney'] += round(($dataDetail['cTotalMoney']/$count),2);
						

					}
					unset($list);unset($dataDetail);
					// echo "<br>".$dataDetail['cCertifiedId']."_".$RR."_";
						
						
					// print_r($ff);
					// unset($ff);
					// unset($RR);
					// unset($brandList);
					// die;

				}elseif ($tab == 'branchGroup') {
					$checkVal = '';$checkVal1 = '';$checkVal2 = '';$checkVal3 = '';$checkScrivener = '';
					$checkValPart2 = ''; $checkValPart21 = '';$checkValPart22 = ''; $checkValPart23 = '';
					$list = array();

					if ($tab == 'branchGroup') {
						if ($dataDetail['branch'] > 0) {
							$checkVal =  $dataDetail['branchGroup'];  //
							$checkValPart2 = $dataDetail['branchGroup2'];
							
						}

						if ($dataDetail['branch1'] > 0) {
							$checkVal1 =  $dataDetail['branchGroup1'];  //
							$checkValPart21 = $dataDetail['branchGroup21'];
						}

						if ($dataDetail['branch2'] > 0) {
							$checkVal2 =  $dataDetail['branchGroup2'];  //
							$checkValPart22 = $dataDetail['branchGroup22'];
						}
						if ($dataDetail['branch3'] > 0) {
							$checkVal3 =  $dataDetail['branchGroup3'];
							$checkValPart23 = $dataDetail['branchGroup23'];
						}
					}

										//計算
					if ($dataDetail['branch'] > 0  ) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
									if (!empty($checkValPart2)) {
										$list[$checkValPart2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
									}
									
									
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
									if (!empty($checkValPart2)) {
										$list[$checkValPart2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
									}
									
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
								if (!empty($checkValPart2)) {
									$list[$checkValPart2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney'];
								}

							}	
						}
											
						$list[$checkVal]['count']++;
					}

					if ($dataDetail['branch1'] > 0 ) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand1']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal1]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
									if (!empty($checkValPart21)) {
										$list[$checkValPart21]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
									}
									
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch1'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal1]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
									if (!empty($checkValPart21)) {
										$list[$checkValPart21]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
									}
									
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal1]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
								if (!empty($checkValPart21)) {
									$list[$checkValPart21]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney1'];
								}
								
							}	

							// if ($dataDetail['cCertifiedId'] == '007097458') {
							// 	echo $checkVal."_". $dataDetail['cCaseFeedBackMoney1']."<br>";
							// }
						}
						$list[$checkVal1]['count']++;

						
					}

					if ($dataDetail['branch2'] > 0) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand2']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];
									if (!empty($checkValPart22)) {
										$list[$checkValPart22]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];
									}
									
									
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch2'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];
									if (!empty($checkValPart22)) {
										$list[$checkValPart22]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];
									}
									
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal2]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];
								if (!empty($checkValPart22)) {
									$list[$checkValPart22]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney2'];
								}
								

								// if ($dataDetail['cCertifiedId'] == '007097458') {
								// 		echo $checkVal2."_".$dataDetail['cCaseFeedBackMoney2']."<br>";
								// 	}
							}	
						}

						$list[$checkVal2]['count']++;
						
						
					}

					if ($dataDetail['branch3'] > 0) {
						if (!empty($input['brand'])) { //查詢品牌
							if ($input['brand'] == $dataDetail['brand3']) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal3]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];
									$list[$checkVal23]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];
								}
							}
								
						}elseif(!empty($input['branch'])){
							if (in_array($dataDetail['branch3'],$input['branch'])) {
								if ($dataDetail['cCaseFeedback'] == 0) {
									$list[$checkVal3]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];
									$list[$checkVal23]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];
								}
							}
						}else{
							if ($dataDetail['cCaseFeedback'] == 0) {
								$list[$checkVal3]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];
								$list[$checkVal23]['feedbackMoney'] += $dataDetail['cCaseFeedBackMoney3'];

								// if ($dataDetail['cCertifiedId'] == '007097458') {
								// 		echo $checkVal3."_".$dataDetail['cCaseFeedBackMoney3']."<br>";
								// 	}
							}	
						}

						$list[$checkVal3]['count']++;

						
						
					}

					
					if ($dataDetail['cSpCaseFeedBackMoney'] > 0) {
						// if ($dataDetail['cCertifiedId'] == '090050741') {
						// 	echo $checkVal."_";
						// 	echo $dataDetail['cSpCaseFeedBackMoney'];

						// }
						$list[$checkVal]['feedbackMoney'] += $dataDetail['cSpCaseFeedBackMoney'];
						$list[$checkVal2]['feedbackMoney'] += $dataDetail['cSpCaseFeedBackMoney'];
						// $list[$checkScrivener]['count']++;

						// if ($dataDetail['cCertifiedId'] == '007097458') {
						// 				echo $checkScrivener.'_'.$dataDetail['cSpCaseFeedBackMoney']."<br>";
						// 			}

						
					}

					//涉及到回饋所以要算其他回饋的品牌
					// 

					if (is_array($OtherFeedBackData[$dataDetail['cCertifiedId']])) {
						
						foreach ($OtherFeedBackData[$dataDetail['cCertifiedId']] as $key => $value) {
							if ($value['fType'] == 1) { // 類型1地政2仲介
								
									$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
									$list[$checkVal2]['feedbackMoney'] += $value['fMoney'];
									// $list[$checkScrivener]['count']++;
								
								
							}else{
								if (!empty($input['brand'])) {
									
									if ($input['brand'] == $value['brand']) { //查詢品牌
										$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
										$list[$checkVal2]['feedbackMoney'] += $value['fMoney'];
									}elseif ($input['branch'] == $value['fStoreId']) {  //查詢仲介
										$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
										$list[$checkVal2]['feedbackMoney'] += $value['fMoney'];
									}

								}elseif(!empty($input['branch'])){

									if ($input['branch'] == $value['branch']) { //查詢品牌
										$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
										$list[$checkVal2]['feedbackMoney'] += $value['fMoney'];
									}elseif ($input['branch'] == $value['fStoreId']) {  //查詢仲介
										$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
										$list[$checkVal2]['feedbackMoney'] += $value['fMoney'];
									}

								}else{
									$list[$checkVal]['feedbackMoney'] += $value['fMoney'];
									$list[$checkVal2]['feedbackMoney'] += $value['fMoney'];
								}
								
								


							}

						
						}


					}



					$count = count($list);

					foreach ($list as $key => $value) {
						if ($key == 5) {
								echo $dataDetail['cCertifiedId']."_";
						}
					
						
						if ($tab == 'branchGroup' &&$key == '') { //不是每間店都有群組
							continue;
						}
						
						// $RR = $value['feedbackMoney'];
						$data[$date][$key]['count'] += 1;

						
						$data[$date][$key]['feedbackMoney'] += $value['feedbackMoney'];
						$data[$date][$key]['certifiedMoney'] += $dataDetail['cCertifiedMoney'];
						$data[$date][$key]['totalMoney'] += $dataDetail['cTotalMoney'];

						
						//比對用
						// $totalData['count'] += round((1/$count),4);
						// $totalData['feedbackMoney'] += round(($value['feedbackMoney']/$count),2);
						// $totalData['certifiedMoney'] += round(($dataDetail['cCertifiedMoney']/$count),2);
						// $totalData['totalMoney'] += round(($dataDetail['cTotalMoney']/$count),2);
						

					}
					unset($list);unset($dataDetail);
				}
				
			}
		}

		

		// echo "<pre>";
		// print_r($ff);
		// die;

}

 // 	echo "<pre>";
	// 	print_r($title);
	// // 	die;
	
	// echo "<pre>";
	// print_r($data);
	// die;
	$totalCaseCount = 0;
	$totalCaseMoney = 0;
	$totalCertifiedMoney = 0;
	$totalFeedBackMoney = 0;
foreach ($data as $key => $value) {
	
	foreach ($value as $k => $v) {
		$totalCaseCount += round($v['count']);
		$totalCaseMoney +=  round($v['totalMoney']);
		$totalCertifiedMoney += round($v['certifiedMoney']);
		$totalFeedBackMoney += round($v['feedbackMoney']);
	}
	
}




##
function getSales($city,$date){
	global $conn;
	$sales = array();
	//先取當時最近一筆的區域時間
	$sql = "SELECT zTime,zCity FROM tZipArea_log WHERE zTime <= '".$date."' AND zCity = '".$city."' ORDER BY zTime DESC LIMIT 1";
	$rs = $conn->Execute($sql);

	//
	$sql = "SELECT zTime,zCity,zSales FROM tZipArea_log WHERE zTime = '".$rs->fields['zTime']."' AND zCity = '".$city."' GROUP BY zSales";
	
	$rs = $conn->Execute($sql);



	while (!$rs->EOF) {
		array_push($sales, $rs->fields['zSales']);
		$rs->MoveNext();
	}

	

	return $sales;
}





##
$smarty->assign('totalCaseCount',$totalCaseCount);
$smarty->assign('totalCaseMoney',$totalCaseMoney);
$smarty->assign('totalCertifiedMoney',$totalCertifiedMoney);
$smarty->assign('totalFeedBackMoney',$totalFeedBackMoney);
$smarty->assign('title',$title);
$smarty->assign('data',$data);
$smarty->display('analysiscase_result.inc.tpl', '', 'report') ;
?>