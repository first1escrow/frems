<?php
if (is_array($cCertifiedId)) {
	// $defaultTwHouseSales = array('id'=>3,'name'=>'曾政耀'); //台屋預設業務(政耀)
	$defaultTwHouseSales = ['id' => 66, 'name' => '公司']; //台屋預設業務(公司)
	$defaultTwHouseSales = ['id' => 2,  'name' => '雄哥']; //台屋預設業務(雄哥)

		foreach ($cCertifiedId as $k => $v) {
			// $v['cCertifiedId'] = '080051693';

			if (in_array($v['cCertifiedId'], $failCase)) {
				// echo 'D';
				// die;
				continue;
			}

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
				(SELECT sSales FROM tScrivenerSales AS b WHERE b.sScrivener = csc.cScrivener  LIMIT 1) AS scrivenerSales,
				(SELECT bName FROM tBrand AS brand WHERE brand.bId=rea.cBrand) as brand,
				(SELECT bName FROM tBrand AS brand WHERE brand.bId=rea.cBrand1) as brand1,
				(SELECT bName FROM tBrand AS brand WHERE brand.bId=rea.cBrand2) as brand2,
				(SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum) as store,
				(SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) as store1,
				(SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) as store2,
				(SELECT bCategory FROM tBranch AS b WHERE b.bId=rea.cBranchNum) as bCategory,
				(SELECT bCategory FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) as bCategory1,
				(SELECT bCategory FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) as bCategory2,
				(SELECT bName FROM tBranch AS b WHERE b.bId=rea.cBranchNum) as branch,
				(SELECT bName FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) as branch1,
				(SELECT bName FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) as branch2,
				rea.cBranchNum AS cBranchNum,
				rea.cBranchNum1 AS cBranchNum1,
				rea.cBranchNum2 AS  cBranchNum2,
				rea.cBrand AS cBrand,
				rea.cBrand1 AS cBrand1,
				rea.cBrand2 AS  cBrand2,
				cas.cCaseFeedback,
				cas.cCaseFeedback1,
				cas.cCaseFeedback2,
				cas.cFeedbackTarget,
				cas.cFeedbackTarget1,
				cas.cFeedbackTarget2,
				cas.cCaseFeedBackMoney,
				cas.cCaseFeedBackMoney1,
				cas.cCaseFeedBackMoney2,
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

			
			//AND csales.cTarget != 3 
			//ORDER BY b.bCategory,b.bBrand,csales.cCertifiedId,csales.cSalesId ASC
			$rs = $conn->Execute($sql);

			
			$checkSp = 0;//檢查地政士特殊回饋是否是新制
			while (!$rs->EOF) {
				$Arr = $rs->fields;
				$Arr['tBankLoansDate'] = $v['tBankLoansDate'];

				if ($Arr['sameCount'] > 1) {
					$Arr['sameStore'] = 1;
				}
				// echo $v['cCertifiedId'];
				// echo 'QQQ';
				
				##案件總回饋計算##
				if (($Arr['bid'] == $Arr['cBranchNum'])) {
					// echo 'BBBB';
					
					if ($Arr['caseSalesID'] == 57 && $Arr['cSignDate'] < '2019-09-19 00:00:00') {
							$Arr['caseSalesID'] = 3 ;
							$Arr['SalesName'] = '曾政耀' ;
							
					}else if( $Arr['cSignDate'] >= '2020-01-01'){
						// echo 'T';


						$Arr['caseSalesID'] = checkSalesResignation(substr($Arr['cSignDate'], 0,10),$Arr['caseSalesID'],$Arr['bid'],$Arr['cScrivener'],$Arr['cFeedbackTarget'],$Arr['sameCount'],$Arr['cCertifiedId'],$Arr['cCreator']);
						$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$Arr['caseSalesID']."'";
						$rs2 = $conn->Execute($sql);
						$Arr['SalesName'] = $rs2->fields['pName'];

						
					}



					// // echo 'AAAA';
					// die;
					//20220713
					$Arr['caseSalesID'] = ($Arr['cBrand'] == 1 || $Arr['cBrand'] == 49) ? $defaultTwHouseSales['id']   : $Arr['caseSalesID'];	//台屋跟優美算預設人員
					$Arr['SalesName']   = ($Arr['cBrand'] == 1 || $Arr['cBrand'] == 49) ? $defaultTwHouseSales['name'] : $Arr['SalesName'];		//台屋跟優美算預設人員
					##

					$Arr['cCaseFeedBackMoney'] = ($Arr['cCaseFeedback'] == 1)? 0:$Arr['cCaseFeedBackMoney'];//不回饋0元

					$dataCaseFeed[$v['cCertifiedId']]['countSales'][$Arr['caseSalesID']]++;
					$dataCaseFeed[$v['cCertifiedId']]['countPartSales'][$Arr['caseSalesID']]+= round(1/$Arr['sameCount'],2);

					$dataCaseFeed[$v['cCertifiedId']]['count']++;
					$dataCaseFeed[$v['cCertifiedId']]['countPart'] += round(1/$Arr['sameCount'],2);

					$dataCaseFeed[$v['cCertifiedId']]['feedMoney'][$Arr['caseSalesID']] += round($Arr['cCaseFeedBackMoney']/$Arr['sameCount']); //同家店有兩人所以要除

					

				}elseif ($Arr['bid'] == $Arr['cBranchNum1'] && $Arr['cBranchNum1'] != 0) {
					
					if ($Arr['caseSalesID'] == 57 && $Arr['cSignDate'] < '2019-09-19 00:00:00') {
							$Arr['caseSalesID'] = 3 ;
							$Arr['SalesName'] = '曾政耀' ;
							
					}else if( $Arr['cSignDate'] >= '2020-01-01'){
						// echo 'T';
						$Arr['caseSalesID'] = checkSalesResignation(substr($Arr['cSignDate'], 0,10),$Arr['caseSalesID'],$Arr['bid'],$Arr['cScrivener'],$Arr['cFeedbackTarget1'],$Arr['sameCount'],$Arr['cCertifiedId'],$Arr['cCreator']);
						$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$Arr['caseSalesID']."'";
						$rs2 = $conn->Execute($sql);
						$Arr['SalesName'] = $rs2->fields['pName'];
					}

					//20220713
					$Arr['caseSalesID'] = ($Arr['cBrand1'] == 1 || $Arr['cBrand1'] == 49) ? $defaultTwHouseSales['id']   : $Arr['caseSalesID'];	//台屋跟優美算預設人員
					$Arr['SalesName']   = ($Arr['cBrand1'] == 1 || $Arr['cBrand1'] == 49) ? $defaultTwHouseSales['name'] : $Arr['SalesName'];	//台屋跟優美算政耀
					##

					$Arr['cCaseFeedBackMoney1'] = ($Arr['cCaseFeedback1'] == 1)? 0:$Arr['cCaseFeedBackMoney1'];//不回饋0元
					
					$dataCaseFeed[$v['cCertifiedId']]['countSales'][$Arr['caseSalesID']]++;
					$dataCaseFeed[$v['cCertifiedId']]['countPartSales'][$Arr['caseSalesID']]+= round(1/$Arr['sameCount'],2);
					$dataCaseFeed[$v['cCertifiedId']]['count']++;
					$dataCaseFeed[$v['cCertifiedId']]['countPart'] += round(1/$Arr['sameCount'],2);

					$dataCaseFeed[$v['cCertifiedId']]['feedMoney'][$Arr['caseSalesID']] += round($Arr['cCaseFeedBackMoney1']/$Arr['sameCount']); //同家店有兩人所以要除同家店有兩人所以要除
				

				}elseif ($Arr['bid'] == $Arr['cBranchNum2'] && $Arr['cBranchNum2'] != 0) {
					
					if ($Arr['caseSalesID'] == 57 && $Arr['cSignDate'] < '2019-09-19 00:00:00') {
							$Arr['caseSalesID'] = 3 ;
							$Arr['SalesName'] = '曾政耀' ;
							
					}else if( $Arr['cSignDate'] >= '2020-01-01'){
						// echo 'T';
						$Arr['caseSalesID'] = checkSalesResignation(substr($Arr['cSignDate'], 0,10),$Arr['caseSalesID'],$Arr['bid'],$Arr['cScrivener'],$Arr['cFeedbackTarget2'],$Arr['sameCount'],$Arr['cCertifiedId'],$Arr['cCreator']);
						$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$Arr['caseSalesID']."'";
						$rs2 = $conn->Execute($sql);
						$Arr['SalesName'] = $rs2->fields['pName'];
					}

					//20220713
					$Arr['caseSalesID'] = ($Arr['cBrand2'] == 1 || $Arr['cBrand2'] == 49) ? $defaultTwHouseSales['id']   : $Arr['caseSalesID'];	//台屋跟優美算預設人員
					$Arr['SalesName']   = ($Arr['cBrand2'] == 1 || $Arr['cBrand2'] == 49) ? $defaultTwHouseSales['name'] : $Arr['SalesName'];	//台屋跟優美算預設人員
					##
					
					$Arr['cCaseFeedBackMoney2'] = ($Arr['cCaseFeedback2'] == 1)? 0:$Arr['cCaseFeedBackMoney2'];//不回饋0元
					
					$dataCaseFeed[$v['cCertifiedId']]['countSales'][$Arr['caseSalesID']]++;
					$dataCaseFeed[$v['cCertifiedId']]['countPartSales'][$Arr['caseSalesID']]+= round(1/$Arr['sameCount'],2);
					$dataCaseFeed[$v['cCertifiedId']]['count']++;
					$dataCaseFeed[$v['cCertifiedId']]['countPart'] += round(1/$Arr['sameCount'],2);
					$dataCaseFeed[$v['cCertifiedId']]['feedMoney'][$Arr['caseSalesID']] += round($Arr['cCaseFeedBackMoney2']/$Arr['sameCount']); //同家店有兩人所以要除

				}
				

				
				
				//其他回饋(下面會計算)
				


				##案件總回饋計算END##

				if (in_array($Arr['caseSalesID'],$sales_arr)) { //比對是否是查詢的業務

					
						
					if ($Arr['cTarget'] == 3) { //特殊回饋		
						

						// $Arr['caseSalesID'] = ($Arr['scrivenerSales2'])?$Arr['scrivenerSales2']:$Arr['scrivenerSales'];

						$sql = "SELECT cSalesId FROM tContractSales  WHERE cTarget = 3 AND cCertifiedId='".$Arr['cCertifiedId']."'";
						// echo $sql."<br>";
						$rs3 = $conn->Execute($sql);
						$spSalesCount = $rs3->RecordCount();
						
						if ($tmp['caseSalesID'] == 57 && $Arr['cSignDate'] < '2019-09-19 00:00:00') {
							$Arr['SalesName'] = '曾政耀' ;
							
						}else if( $Arr['cSignDate'] >= '2020-01-01'){
							// echo 'T';
							$Arr['caseSalesID'] = checkSalesResignation(substr($Arr['cSignDate'], 0,10),$Arr['caseSalesID'],$Arr['bid'],$Arr['cScrivener'],3,$Arr['sameCount'],$Arr['cCertifiedId'],$Arr['cCreator']);
							$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$Arr['caseSalesID']."'";
							$rs2 = $conn->Execute($sql);
							$Arr['SalesName'] = $rs2->fields['pName'];
						}

						//地政士特殊回饋(計算案件總回饋資訊)
						if ($Arr['cSpCaseFeedBackMoney'] > 0 ) {
							$dataCaseFeed[$v['cCertifiedId']]['countSales'][$Arr['caseSalesID']]++;
							$dataCaseFeed[$v['cCertifiedId']]['countPartSales'][$Arr['caseSalesID']]++;
							$dataCaseFeed[$v['cCertifiedId']]['count']++;
							$dataCaseFeed[$v['cCertifiedId']]['countPart']++;
							$dataCaseFeed[$v['cCertifiedId']]['feedMoney'][$Arr['caseSalesID']] += round($Arr['cSpCaseFeedBackMoney']/$spSalesCount);
							// echo $Arr['cSpCaseFeedBackMoney']."<br>"; 
							// echo $spSalesCount."<bR>";
							// echo round($Arr['cSpCaseFeedBackMoney']/$spSalesCount)."<br>"; 
							

							// print_r($dataCaseFeed[$v['cCertifiedId']]);
							// die;

						}

						$Arr['SalesName'] = getOtherFeedSales($Arr['caseSalesID']);
						$Arr['scrivener'] = $Arr['scrivener'];
						
						$Arr['fType'] = 1;





						$data2[] = $Arr;
						$checkSp++;
					}else{
						$dataArr[$Arr['order1']."_".$Arr['order2']."_".$Arr['order3']."_".$Arr['order4']] = $Arr;
						
					}
				}
			
				$rs->MoveNext();
			}
			


			//有資料
			if (is_array($Arr)) {
				//其他回饋		
				$tmp = getOtherFeedForReport($Arr);

				// if ($Arr['cCertifiedId'] == '080275343') {
				// 		header("Content-Type:text/html; charset=utf-8"); 
				// echo "<pre>";
				// print_r($tmp);

				// die;
				// }

				

				if (is_array($tmp)) {	
					foreach ($tmp as $key => $value) {
						if (in_array($value['caseSalesID'],$sales_arr)) {
							$data2[] = $value;
						}
						
					}
					
				}
				unset($tmp);
				$check++;
						
				if ($checkSp == 0 && $Arr['cSpCaseFeedBackMoney'] > 0) {
					
					$tmp = $Arr;

					$tmp['caseSalesID'] = ($tmp['scrivenerSales2'])?$tmp['scrivenerSales2']:$tmp['scrivenerSales'];
					if ($tmp['caseSalesID'] == 57 && $Arr['cSignDate'] < '2019-09-19 00:00:00') {
							$tmp['caseSalesID'] = 3 ;
							$tmp['SalesName'] = '曾政耀' ;
							
					}else if( $Arr['cSignDate'] >= '2020-01-01'){
						// echo 'T';
							$tmp['caseSalesID'] = checkSalesResignation(substr($Arr['cSignDate'], 0,10),$tmp['caseSalesID'],$Arr['bid'],$Arr['cScrivener'],3,$Arr['sameCount'],$Arr['cCertifiedId'],$Arr['cCreator']);
							$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$tmp['caseSalesID']."'";
							$rs2 = $conn->Execute($sql);
							$tmp['SalesName'] = $rs2->fields['pName'];
					}


					$tmp['SalesName'] = getOtherFeedSales($tmp['caseSalesID']);
					$tmp['scrivener'] = $Arr['scrivener'];
					$tmp['fType'] = 1;


					//地政士特殊回饋(計算案件總回饋資訊)
					if ($Arr['cSpCaseFeedBackMoney'] > 0 ) {
						
						$dataCaseFeed[$v['cCertifiedId']]['countSales'][$tmp['caseSalesID']]++;
						$dataCaseFeed[$v['cCertifiedId']]['countPartSales'][$tmp['caseSalesID']]++;
						$dataCaseFeed[$v['cCertifiedId']]['count']++;
						$dataCaseFeed[$v['cCertifiedId']]['countPart']++;
						$dataCaseFeed[$v['cCertifiedId']]['feedMoney'][$tmp['caseSalesID']] += $Arr['cSpCaseFeedBackMoney'];
					}

					if (in_array($tmp['caseSalesID'],$sales_arr)) { 
						$data2[] = $tmp;
					}

					unset($tmp);
				}



				//平均保證費
				if (is_array($dataCaseFeed[$v['cCertifiedId']]['countPartSales'])) {
					foreach ($dataCaseFeed[$v['cCertifiedId']]['countPartSales'] as $key => $value) {
						$dataCaseFeed[$v['cCertifiedId']]['avgCertifiedMoney'][$key] += round(($Arr['cCertifiedMoney']/$dataCaseFeed[$v['cCertifiedId']]['countPart'])*$value);
					}
				}
					
					
			}

			
			unset($Arr);
		}

	}

	unset($tmp);
	ksort($dataArr);

	//將其他回饋對象+地政士特殊回饋，加進原本的資料陣列
	if (is_array($data2)) {		
		$dataArr = array_merge($dataArr,$data2);
	}

	$tmpArr = array();
	$i = 0;
	foreach ($dataArr as $k => $v) {
		if ($v['fType'] == 1) {
			$v['showbId'] = 'SC'.str_pad($v['cScrivener'], "4","0",STR_PAD_LEFT);
			$v['showBrand'] = $v['scrivener'];
			$v['showCategory'] = 'sp';
			$v['showStore'] = '';
			$v['showBranch'] = '';
		}else if($v['fType'] == 2){

			$v['showbId'] = $v['bCode'];
			$v['showBrand'] = $v['brand'];
			$v['showCategory'] = $v['bCategory'];
			$v['showStore'] = $v['store'];
			$v['showBranch'] = $v['branch'];
			
			
		}else if($v['cBranchNum'] == $v['bid']){

			$v['showbId'] = $v['bCode'];
			$v['showBrand'] = $v['brand'];
			$v['showCategory'] = $v['bCategory'];
			$v['showStore'] = $v['store'];
			$v['showBranch'] = $v['branch'];
			
			
		}elseif ($v['cBranchNum1'] == $v['bid']){

			$v['showbId'] = $v['bCode'];
			$v['showBrand'] = $v['brand1'];
			$v['showCategory'] = $v['bCategory1'];
			$v['showStore'] = $v['store1'];
			$v['showBranch'] = $v['branch1'];

			
		}elseif ($v['cBranchNum2']==$v['bid']){
			$v['showbId'] = $v['bCode'];
			$v['showBrand'] = $v['brand2'];
			$v['showCategory'] = $v['bCategory2'];
			$v['showStore'] = $v['store2'];
			$v['showBranch'] = $v['branch2'];
		}else{
			continue;
		}

		//非仲介成交
		if ($v['showbId'] == 'NG00505'){
			$v['showbId'] = 'SC'.str_pad($v['cScrivener'],4,'0',STR_PAD_LEFT) ;
		}
		//其他回饋標示
		$v['showMark'] =($v['fType'])? '*': '';
		
		//業績
		$v['showCount'] = count($dataCaseFeed[$v['cCertifiedId']]['countSales']);//業績分配數

		
		//平均保證費
		// $v['showAvgCertifiedMoney'] = $dataCaseFeed[$v['cCertifiedId']]['avgCertifiedMoney'][$v['caseSalesID']];
		$v['showAvgCertifiedMoney'] = round($v['cCertifiedMoney']/$v['showCount'])  ;

		//業績
		$v['showSalesMoney'] = $v['showAvgCertifiedMoney'] - $dataCaseFeed[$v['cCertifiedId']]['feedMoney'][$v['caseSalesID']];



		//回饋金
		$v['showCaseFeedBackMoney'] = $dataCaseFeed[$v['cCertifiedId']]['feedMoney'][$v['caseSalesID']];

		//進案日期
		$v['showApplyDate'] =dateformate($v['cApplyDate']) ;
		##
		//實際點交日期
		$v['showFinishDate'] =dateformate($v['cFinishDate']) ;

		// 簽約日期
		$v['showSignDate'] = dateformate($v['cSignDate']) ;
		
		##
		
		// 結案日期
		$v['showEndDate'] = dateformate($v['cEndDate']) ;
		

		##
		//狀態日期

		if ($v['status']=='已結案') {
			$v['showStatusDate'] = $v['showEndDate'];
		}
		else {
			$v['showStatusDate'] = $v['showSignDate'];
		}

		//標的物坐落
		$v['showAddr'] = addr($v['cCertifiedId']);

		// 仲介類別
		if($v['showCategory']==1 && $v['showBrand'] !='台灣房屋' && $v['showBrand'] !='非仲介成交' && $v['showBrand'] != '優美地產'){//加盟(其他品牌)
			$v['showCategory'] ='加盟(其他品牌)';
			$cat = 11;
		}elseif ($v['showCategory'] == 1 && $v['showBrand'] == '台灣房屋') {//加盟(台灣房屋)
			$v['showCategory'] ='加盟(台灣房屋)';
			$cat = 12;
		}elseif ($v['showCategory'] == 1 && $v['showBrand'] == '優美地產') {//加盟(優美地產)
			$v['showCategory'] ='加盟(優美地產)';
			$cat = 13;
		}elseif ($v['showCategory'] == 1) {//加盟
			$v['showCategory'] ='加盟';
			$cat = 1;
		}elseif ($v['showCategory'] == 2) {//直營
			$v['showCategory'] ='直營';
			$cat = 2;
		}elseif ($v['showCategory'] == 3) {//非仲介成交
			$v['showCategory'] ='非仲介成交';
			$cat = 3;
		}elseif($v['showCategory'] == 'sp'){
			$v['showCategory'] ='特殊回饋地政士';
			$cat = 3;
		}else{
			$v['showCategory'] ='';
		}
		
		$check = 0; //0 濾掉 1 OK
		$check2 = 0;

		
		if (in_array($v['caseSalesID'], $sales_arr)) {
			$check = 1;
		}
		
		if (is_array($categoryArray)) {
			foreach ($categoryArray as $k2 => $v2) {

				if($v2==$cat){//加盟(其他品牌)
					$check2 = 1;
				}elseif ($v2==$cat) {//加盟(台灣房屋)
					$check2 = 1;
				}elseif ($v2==$cat) {//加盟(優美地產)
					$check2 = 1;
				}elseif ($v2==$cat) {//加盟
					$check2 = 1;
				}elseif ($v2==$cat) {//直營
					$check2 = 1;
				}elseif ($v2==$cat) {//非仲介成交
					$check2 = 1;
				}
			}

		}else{
			$check2 = 1;
		}

		
		if ($check == 1 && ($check2 == 1)) {
			if (in_array($v['cCertifiedId'].$v['caseSalesID'], $tmpArr)) {
				
				
				
				$list[$v['cCertifiedId'].$v['caseSalesID']]['showbId'] .=  '_'.$v['showbId'];
				$list[$v['cCertifiedId'].$v['caseSalesID']]['showBrand'] .=  '_'.$v['showBrand'];
				$list[$v['cCertifiedId'].$v['caseSalesID']]['showCategory'] .=  '_'.$v['showCategory'];
				$list[$v['cCertifiedId'].$v['caseSalesID']]['showStore'] .=  '_'.$v['showStore'];
				$list[$v['cCertifiedId'].$v['caseSalesID']]['showBranch'] .=  '_'.$v['showBranch'];
			}else{
				$list[$v['cCertifiedId'].$v['caseSalesID']] = $v ;
			}
			$tmpArr[] = $v['cCertifiedId'].$v['caseSalesID'];
		}	

		unset($tmp2);
	}



	// die;
	unset($tmpArr);unset($dataArr);unset($check);unset($dataCaseFeed);unset($check);unset($check2);
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
foreach ($list as $k => $v) {

	// if (substr($v['showbId'], 0,2) == 'TH' || substr($v['showbId'], 0,2) == 'UV') {
	// 	$v['SalesName'] = '曾政耀';
	// }
	// $sameStore = 1;

	$c=65;//欄
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,($no));
	$objPHPExcel->getActiveSheet()->getCell(chr($c++).$row)->setValueExplicit($v['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showBrand']);///C
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showbId']);//D
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showStore']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showBranch']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['owner']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['buyer']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['cCertifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showAvgCertifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showCaseFeedBackMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showCount']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showSalesMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['SalesName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showStatusDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showSignDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showFinishDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['scrivener']);//
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showAddr']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['status']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showCategory']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,$v['showEndDate']);

	if ($v['sameStore'] == 1) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$row,'店家有兩位業務');
	}

	$sales_total[$v['caseSalesID']] =$sales_total[$v['caseSalesID']]+$v['showSalesMoney'];//總業績


	//月業績
	##
	if ($report_type==1) {
		if($date_type==1){	

			// preg_match_all("/(.*)-/U",$v['showSignDate'] , $tmp);
			$month = substr($v['showSignDate'], 0,6) ;
							
		}elseif ($date_type==2) {
							// cEndDate
			// preg_match_all("/(.*)-/U",$export_date[$v['cCertifiedId']] , $tmp);
			$month = substr($v['tBankLoansDate'], 0,7) ;
			
		
		}

		// $month=$tmp[1][0].'-'.$tmp[1][1];
		$sales_month[$month] = $sales_month[$month]+$v['showSalesMoney'];
	}

	##


	$row++;
	$no++;
}
function getOtherFeedForReport($data) {//把部分欄位取代

	global $conn;
	global $sales_arr;
	global $dataCaseFeed;


	$sql = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='".$data['cCertifiedId']."' ".$str."  AND fDelete = 0";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);
	$total=$rs->RecordCount();
	$i = 0;
	// echo $total."<br>";
	
	if ($total ==0) {
		return false;
	}else{
		
		while (!$rs->EOF) {
			//可能會有一個以上的業務
			$sales = explode(',', $rs->fields['fSales']);
			$total2 = count($sales);
			foreach ($sales as $k => $v) {
				// $total2++; 
				$arr[$i] = $data; 
				$dataCaseFeed[$data['cCertifiedId']]['countSales'][$v]++;
				$dataCaseFeed[$data['cCertifiedId']]['countPartSales'][$v]+= round(1/$total2,2);

				//算平均保證費 
				$dataCaseFeed[$data['cCertifiedId']]['count']++;
				$dataCaseFeed[$data['cCertifiedId']]['countPart'] += round(1/$total2,2);
				$dataCaseFeed[$data['cCertifiedId']]['feedMoney'][$v] += round($rs->fields['fMoney']/$total2); 

				$tmp = getOtherFeed($rs->fields['fType'],$rs->fields['fStoreId']);

				if ($rs->fields['fType'] == 2) {
					$arr[$i]['bid'] = $rs->fields['fStoreId'];
					$arr[$i]['store'] = $tmp['Store'];
					$arr[$i]['branch'] = $tmp['Name'];
					$arr[$i]['cBranchNum'] = $rs->fields['fStoreId'];
					$arr[$i]['cBrand'] = $tmp['brandCode'];
					$arr[$i]['bCategory'] = $tmp['bCategory'];
					$arr[$i]['brand'] = $tmp['brand'];
					$arr[$i]['bCode'] = $tmp['Code'];
				}elseif ($rs->fields['fType'] == 1) {
					$arr[$i]['scrivener'] = $tmp['Name'];
					$arr[$i]['cScrivener'] = $rs->fields['fStoreId'];
					//brand1
					if ($arr[$i]['cBrand'] == 69 ) {
						$arr[$i]['brand'] = $arr[$i]['brand'];
						$arr[$i]['store'] = $arr[$i]['store'];
						$arr[$i]['bCategory'] = $arr[$i]['bCategory'];
						$arr[$i]['branch'] = $arr[$i]['branch'];

					}elseif ($arr[$i]['cBrand1'] == 69) {
						$arr[$i]['brand'] = $arr[$i]['brand1'];
						$arr[$i]['store'] = $arr[$i]['store1'];
						$arr[$i]['bCategory'] = $arr[$i]['bCategory1'];
						$arr[$i]['branch'] = $arr[$i]['branch1'];
					}elseif ($arr[$i]['cBrand2'] == 69) {
						$arr[$i]['brand'] = $arr[$i]['brand2'];
						$arr[$i]['store'] = $arr[$i]['store2'];
						$arr[$i]['bCategory'] = $arr[$i]['bCategory2'];
						$arr[$i]['branch'] = $arr[$i]['branch2'];

					}
							

				}
				$arr[$i]['caseSalesID'] = $v;
				$arr[$i]['SalesName'] = getOtherFeedSales($v);
				$arr[$i]['fType'] =$rs->fields['fType'];

				

				
				unset($tmp);
				$i++;
			}

		
			
			##
	
			unset($sales);	
					
			

			
			$rs->MoveNext();
			
		}


	}


	// header("Content-Type:text/html; charset=utf-8"); 
	// echo "<pre>";
	// print_r($arr);



	return $arr;
}
?>