<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../opendb.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php';
##

##業務##
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE (pDep = 7 AND pJob = 1)";
$rs = $conn->Execute($sql);
$menuSales[0] = '全部';
// $menuSales[3] = '曾政耀';
while (!$rs->EOF) {
	$menuSales[$rs->fields['pId']] = $rs->fields['pName'];


	$rs->MoveNext();
}

##

if ($_POST) {
 	
	$_POST = escapeStr($_POST) ;
	$sales = $_POST['sales'];
	$data = $data1 = $dataTotal = $dataTotal1 = $originalDataTotal = $originalDataTotal1 =  array();
	//$dataTotal 計算加總
	//$originalDataTotal 原始資料
	##
	$data['O']['name'] = '其他品牌';
	$data1['O']['name'] = '其他品牌';
	
	$data['2']['name'] = '台灣房屋直營';
	$data1['2']['name'] = '台灣房屋直營';

	$data['T']['name'] = '台灣房屋加盟';
	$data1['T']['name'] = '台灣房屋加盟';

	$data['3']['name'] = '非仲介成交';
	$data1['3']['name'] = '非仲介成交';

	$sql = "SELECT bId FROM tBranch AS b LEFT JOIN tZipArea AS z ON b.bZip = z.zZip WHERE z.zCity IN('基隆市','宜蘭縣')"; //基隆市、宜蘭縣
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$branch[] = $rs->fields['bId'];

		$rs->MoveNext();
	}

	$sql = "SELECT sId FROM tScrivener AS s LEFT JOIN tZipArea AS z ON s.sCpZip1 = z.zZip WHERE z.zCity IN('基隆市','宜蘭縣')";
	while (!$rs->EOF) {
		$scrivener[] = $rs->fields['sId'];

		$rs->MoveNext();
	}

	// print_r($branch);
	##
	//業務
	if ($_POST['sales']) {
		$sql = "SELECT 	bSales,bBranch FROM tBranchSales";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$branchCheck[$rs->fields['bSales']][] = $rs->fields['bBranch'];
			// $branchSales[$rs->fields['bBranch']][$rs->fields['bSales']] = $rs->fields['bSales'];

			$rs->MoveNext();
		}

		$sql = "SELECT sSales,sScrivener FROM tScrivenerSales";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$scrivenerCheck[$rs->fields['sSales']][] = $rs->fields['sScrivener'];
			// $scrivenerSales[$rs->fields['sScrivener']][$rs->fields['sSales']] = $rs->fields['sSales'];

			$rs->MoveNext();
		}

		$FeedBackMoneyQueryStr = " AND fSales = '".$_POST['sales']."'";

	}
	
	// echo "<pre>";
	// print_r($branchCheck);

	//其他回饋
	$sql = "SELECT 
				fCertifiedId,
				fType,
				fStoreId,
				fMoney,
				(SELECT bCategory FROM tBranch WHERE bId = fStoreId) AS category,
				(SELECT bBrand FROM tBranch WHERE bId = fStoreId) AS brand,
				fSales
			FROM
				tFeedBackMoney WHERE fDelete = 0 AND (fType = 1 OR fType = 2) ".$FeedBackMoneyQueryStr;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		if ($rs->fields['fType'] == 1) {
			if (in_array($rs->fields['fStoreId'], $scrivener)) {
				$OtherFeedBackData[$rs->fields['fCertifiedId']][] = $rs->fields;
			}
		}else{
			if (in_array($rs->fields['fStoreId'], $branch)) {
				$OtherFeedBackData[$rs->fields['fCertifiedId']][] = $rs->fields;
			}
		}
		

		$rs->MoveNext();
	}
	unset($FeedBackMoneyQueryStr);
	
	##
	$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子

	$query .= " AND cas.cApplyDate >= '".($_POST['year']+1911)."-01-01 00:00:00' AND cas.cApplyDate <= '".($_POST['year2']+1911)."-06-30 00:00:00'";

	if ($query) { $query = ' WHERE '.$query ; }


	$query ='
	SELECT 
		cas.cCertifiedId,
		cas.cApplyDate,
		cas.cSignDate,
		rea.cBrand as brand,
		rea.cBrand1 as brand1,
		rea.cBrand2 as brand2,
		rea.cBrand2 as brand3,
		rea.cBranchNum as branch,
		rea.cBranchNum1 as branch1,
		rea.cBranchNum2 as branch2,
		rea.cBranchNum3 as branch3,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) AS category,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) AS category1,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) AS category2,
		cas.cCaseFeedBackMoney,
		cas.cCaseFeedBackMoney1,
		cas.cCaseFeedBackMoney2,
		cas.cCaseFeedBackMoney3,
		cas.cSpCaseFeedBackMoney,
		cas.cCaseFeedback,
		cas.cCaseFeedback1,
		cas.cCaseFeedback2,
		cas.cCaseFeedback3,
		inc.cTotalMoney,
		inc.cCertifiedMoney,
		(SELECT cScrivener FROM tContractScrivener WHERE cCertifiedId = cas.cCertifiedId) AS cScrivener
	FROM 
		tContractCase AS cas 
	LEFT JOIN 
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
	LEFT JOIN 
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
	'.$query.' 
	GROUP BY
		cas.cCertifiedId
	' ;

	// echo $query;
	$checkArr = array();
	$rs = $conn->Execute($query);
	while (!$rs->EOF) {





		if ($sales) {
			$type2 = (substr($rs->fields['cApplyDate'], 0,4) == ($_POST['year']+1911))? 1 : 2;

			$CaseCount = getContractSales($rs->fields);
			$branchCount = $count = 0;
			if ($CaseCount['countPart'] > 0) { //有分配到該業務
				$branchCount = $CaseCount['count'];
				$count = round($CaseCount['countPart']/$CaseCount['count'],2);


				if (in_array($rs->fields['branch'],$CaseCount['branch'])) {
					$type = checkCat2019($rs->fields['branch'],$rs->fields['brand'],$rs->fields['category']);//案件類別判定
					countDataSales($rs->fields,$type,$type2,$count,$branchCount,'');
					// coutTotalDataSales($rs->fields,$type,$type2,$count,$branchCount,'');
				}

				if (in_array($rs->fields['branch1'],$CaseCount['branch'])) {
					$type = checkCat2019($rs->fields['branch1'],$rs->fields['brand1'],$rs->fields['category1']);//案件類別判定
					countDataSales($rs->fields,$type,$type2,$count,$branchCount,'');
					// coutTotalDataSales($rs->fields,$type,$type2,$count,$branchCount,'');
				}

				if (in_array($rs->fields['branch2'],$CaseCount['branch'])) {
					$type = checkCat2019($rs->fields['branch2'],$rs->fields['brand2'],$rs->fields['category2']);//案件類別判定
					countDataSales($rs->fields,$type,$type2,$count,$branchCount,'');
					// coutTotalDataSales($rs->fields,$type,$type2,$count,$branchCount,'');
				}

				if (is_array($OtherFeedBackData[$data['cCertifiedId']])) {
					foreach ($OtherFeedBackData[$data['cCertifiedId']] as $k => $v) {
						if ($sales == $v['fSales']) {
							if ($type2 == 1) {
								// echo 'countData'.$arr['cCertifiedId']."_".$count."_".round($arr['cTotalMoney']/$branchCount)."_".round($arr['cCertifiedMoney']/$branchCount)."<bR>";
									
								$data[$type]['count'] += $count;
								$data[$type]['totalMoney'] += round($arr['cTotalMoney']*$count);
								$data[$type]['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);
								//加總
								$dataTotal['count'] += $count;
								$dataTotal['totalMoney'] += round($arr['cTotalMoney']*$count);
								$dataTotal['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

								//總計
								$originalDataTotal['count']+=$count;
								$originalDataTotal['totalMoney'] += round($arr['cTotalMoney']*$count);
								$originalDataTotal['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);


								
								$data[$type]['feedbackMoney'] += $v['fMoney'];
								//總額
								$originalDataTotal['feedbackMoney'] += $v['fMoney'];
								//加總
								$dataTotal['feedbackMoney'] += $v['fMoney'];
								
							}else{
									$data1[$type]['count'] += $count;
									$data1[$type]['totalMoney'] += round($arr['cTotalMoney']*$count);
									$data1[$type]['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

									//加總
									$dataTotal1['count'] += $count;
									$dataTotal1['totalMoney'] += round($arr['cTotalMoney']*$count);
									$dataTotal1['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

									//總計
									$originalDataTotal1['count']+=$count;
									$originalDataTotal1['totalMoney'] += round($arr['cTotalMoney']*$count);
									$originalDataTotal1['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);
											
									$data1[$type]['feedbackMoney'] += $v['fMoney'];
									//總額
									$originalDataTotal1['feedbackMoney'] += $v['fMoney'];
									//加總
									$dataTotal1['feedbackMoney'] += $v['fMoney'];
							}

						}
						
					}
					
				}
				
			}

			



			// echo $rs->fields['cCertifiedId']."<br>";
			// print_r($CaseCount);
			// echo "<br>";
			// die;
			unset($CaseCount);


		}else{

			$branchCount = $count = 0;
			$type = $type2= ''; //type :仲介類別 type2 : 1->比對1 2->比對2

			$type2 = (substr($rs->fields['cApplyDate'], 0,4) == ($_POST['year']+1911))? 1 : 2;

			$branchCount++;

			if ($rs->fields['branch1'] > 0) {
				$branchCount++;
			}

			if ($rs->fields['branch2'] > 0) {
				$branchCount++;
			}

			if ($rs->fields['branch3'] > 0) {
				$branchCount++;
			}
			$count = round(1/$branchCount,2);

			$type = checkCat2019($rs->fields['branch'],$rs->fields['brand'],$rs->fields['category']);//案件類別判定
			countData($rs->fields,$type,$type2,$count,$branchCount,'');//計算


			if ($rs->fields['branch1'] > 0) {
				$type = checkCat2019($rs->fields['branch1'],$rs->fields['brand1'],$rs->fields['category1']);
				countData($rs->fields,$type,$type2,$count,$branchCount,1);
			}

			if ($rs->fields['branch2'] > 0) {
				$type = checkCat2019($rs->fields['branch2'],$rs->fields['brand2'],$rs->fields['category2']);
				countData($rs->fields,$type,$type2,$count,$branchCount,2);
			}

			if ($rs->fields['branch3'] > 0) {
				$type = checkCat2019($rs->fields['branch3'],$rs->fields['brand3'],$rs->fields['category3']);
				countData($rs->fields,$type,$type2,$count,$branchCount,3);
			}

			if (is_array($OtherFeedBackData[$rs->fields['cCertifiedId']])) {
				//setOtherfeed2($data)
				foreach ($OtherFeedBackData[$rs->fields['cCertifiedId']] as $k => $v) {
					// otherfeed($v);
					setOtherfeed2($v,$type2);
				}
			}
		
		

			##
			// coutTotalData($rs->fields,$type,$type2,$count,$branchCount,'');

			if ($type2 == 1) {
				//原始的資料
				$originalDataTotal['count']++;
				$originalDataTotal['totalMoney'] += $rs->fields['cTotalMoney'];
				$originalDataTotal['certifiedMoney'] += $rs->fields['cCertifiedMoney'];
				$originalDataTotal['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];
				//
				$data[3]['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
				$dataTotal['feedbackMoney']  += $rs->fields['cSpCaseFeedBackMoney'];
			}else{
				$originalDataTotal1['count']++;
				$originalDataTotal1['totalMoney'] += $rs->fields['cTotalMoney'];
				$originalDataTotal1['certifiedMoney'] += $rs->fields['cCertifiedMoney'];
				$originalDataTotal1['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];
				$data1[3]['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
				$dataTotal1['feedbackMoney']  += $rs->fields['cSpCaseFeedBackMoney'];

			}
		}

		
		
		
		
		


		$rs->MoveNext();
	}

	//同年資料一樣
	if ($_POST['year'] == $_POST['year2']) {
		$data1 = $data;
		$originalDataTotal1 = $originalDataTotal;
		$dataTotal1 = $dataTotal;
	}

	// if ($sales == 25) {
	// 	if ($_POST['year'] == 107 ) {
	// 		$originalDataTotal['count'] = 3228;
	// 		$originalDataTotal['totalMoney'] = 23526557743;
	// 		$originalDataTotal['certifiedMoney'] = 12185178;
	// 		$originalDataTotal['feedbackMoney'] = 4357351;
	// 	}elseif ($_POST['year'] == 108) {
	// 		$originalDataTotal['count'] = 3592;
	// 		$originalDataTotal['totalMoney'] = 35100000590;
	// 		$originalDataTotal['certifiedMoney'] = 19032953;
	// 		$originalDataTotal['feedbackMoney'] = 5958410;
	// 	}
		

	// }

	// if ($sales == 25) {
	// 	if ($_POST['year'] == 107 ) {
	// 		$originalDataTotal['count'] = 3228;
	// 		$originalDataTotal['totalMoney'] = 23526557743;
	// 		$originalDataTotal['certifiedMoney'] = 12185178;
	// 		$originalDataTotal['feedbackMoney'] = 4357351;
	// 	}elseif ($_POST['year'] == 108) {
	// 		$originalDataTotal['count'] = 3592;
	// 		$originalDataTotal['totalMoney'] = 35100000590;
	// 		$originalDataTotal['certifiedMoney'] = 19032953;
	// 		$originalDataTotal['feedbackMoney'] = 5958410;
	// 	}
	// }
	// echo 'AA'.count($AA);
	// echo "<pre>";
	// print_r($dataTotal);


	// echo "<pre>";
	// print_r($checkArr);
}	


//102年開始
for ($i = (date('Y')-1911); $i > 102; $i--) {  
	$menuYear[$i] = $i;
}
function getContractSales($data){ //業務歸屬該計算業務
	global $conn;
	global $sales;
	global $OtherFeedBackData;
	global $branch;
	global $scrivener;

	$checkSp = 0;
	$salesCountData = array('countPart'=>0,'count'=>0,'branch'=>array());
	$sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$data['cCertifiedId']."'";
	// if ($data['cCertifiedId'] == '080109073') {
	// 	echo $sql;
	// 		echo $checkSp;
	// 	echo "<pre>";
	// 	print_r($data);

	// 	print_r($salesCountData);
	// }
	
	// echo $sql;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		
		if ($rs->fields['cTarget'] == 3) { //特殊回饋		
			if (in_array($rs->fields['cBranch'], $scrivener)) {
				
			

				//地政士特殊回饋(計算案件總回饋資訊)
				if ($data['cSpCaseFeedBackMoney'] > 0 ) {

					if ($rs->fields['cSalesId'] == $sales) {
						$salesCountData['countPart']++;//分配數
						$salesCountData['feedbackMoney'] += $data['cSpCaseFeedBackMoney'];
						$salesCountData['branch'][] = 505; //505 非仲介成交
						
					}
					$salesCountData['count']++;//總計
					// if ($data['cCertifiedId'] == '070721811') {
					// 	echo 'AAAA';
					// }

					$checkSp= 1;//有算過特殊回饋業務
				}
			}
		}else{
			if ($sales == 57 && $data['cSignDate'] < '2019-09-19 00:00:00') {
						// $Arr['caseSalesID'] = 3 ;
						
			}else{


				if ($data['branch'] == $rs->fields['cBranch']) {

					if ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505) {
						echo 'A';
						if (in_array($rs->fields['cBranch'], $scrivener)) {
							echo 'B';
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505)?'505':$rs->fields['cBranch']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
					}else{
						echo 'C';
						if (in_array($rs->fields['cBranch'], $branch)) {
							echo 'D';
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505)?'505':$rs->fields['cBranch']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
					}

					

					$salesCountData['count']++;//總計

					// if ($data['cCertifiedId'] == '070721811') {
					// 	echo 'CC';
					// }
					

				}elseif ($data['branch1'] > 0 && $data['branch1'] == $rs->fields['cBranch']) {

					if ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505) {
						if (in_array($rs->fields['cBranch'], $scrivener)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505)?'505':$rs->fields['cBranch']; //505 非仲介成交 
								
							}
						}
					}else{
						if (in_array($rs->fields['cBranch'], $branch)) {
							if (in_array($rs->fields['cBranch'], $scrivener)) {
								if ($rs->fields['cSalesId'] == $sales) {
									$salesCountData['countPart']++;//分配數
									$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505)?'505':$rs->fields['cBranch']; //505 非仲介成交 
									
								}
							}
						}
					}

					

					// if ($data['cCertifiedId'] == '070721811') {
					// 	echo 'DD';
					// }

					$salesCountData['count']++;//總計
				}elseif ($data['branch2'] > 0 && $data['branch2'] == $rs->fields['cBranch']) {
					if ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505) {
						if (in_array($rs->fields['cBranch'], $scrivener)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505)?'505':$rs->fields['cBranch']; //505 非仲介成交 
								
							}
						}
					}else{
						if (in_array($rs->fields['cBranch'], $branch)) {
							if (in_array($rs->fields['cBranch'], $scrivener)) {
								if ($rs->fields['cSalesId'] == $sales) {
									$salesCountData['countPart']++;//分配數
									$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505)?'505':$rs->fields['cBranch']; //505 非仲介成交 
									
								}
							}
						}
					}

					// if ($data['cCertifiedId'] == '070721811') {
					// 	echo 'EE';
					// }

					$salesCountData['count']++;//總計
				}
				
			}
			
			
		}



		$rs->MoveNext();
	}

	if ($rs->EOF) {
		$sql = "SELECT * FROM tContractSales_2 WHERE cCertifiedId = '".$data['cCertifiedId']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			if ($rs->fields['cTarget'] == 3) { //特殊回饋		

			//地政士特殊回饋(計算案件總回饋資訊)
			if ($data['cSpCaseFeedBackMoney'] > 0 ) {
				if (in_array($rs->fields['cBranch'], $scrivener)) {
					if ($rs->fields['cSalesId'] == $sales) {
						$salesCountData['countPart']++;//分配數
						$salesCountData['feedbackMoney'] += $data['cSpCaseFeedBackMoney'];
						$salesCountData['branch'][] = 505; //505 非仲介成交
						
					}
				}
				$salesCountData['count']++;//總計
				// if ($data['cCertifiedId'] == '070721811') {
				// 	echo 'AAAA';
				// }

				$checkSp= 1;//有算過特殊回饋業務
			}
		}else{
			if ($sales == 57 && $data['cSignDate'] < '2019-09-19 00:00:00') {
						// $Arr['caseSalesID'] = 3 ;
						
			}else{

				if ($data['branch'] == $rs->fields['cStoreId']) {
					if ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505) {
						if (in_array($rs->fields['cStoreId'], $scrivener)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cStoreId'] == 505)?'505':$rs->fields['cStoreId']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
						
					}else{
						if (in_array($rs->fields['cStoreId'], $branch)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cStoreId'] == 505)?'505':$rs->fields['cStoreId']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
					}
					

					$salesCountData['count']++;//總計

					// if ($data['cCertifiedId'] == '070721811') {
					// 	echo 'CC';
					// }
					

				}elseif ($data['branch1'] > 0 && $data['branch1'] == $rs->fields['cStoreId']) {
					if ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505) {
						if (in_array($rs->fields['cStoreId'], $scrivener)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cStoreId'] == 505)?'505':$rs->fields['cStoreId']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
						
					}else{
						if (in_array($rs->fields['cStoreId'], $branch)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cStoreId'] == 505)?'505':$rs->fields['cStoreId']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
					}

					// if ($data['cCertifiedId'] == '070721811') {
					// 	echo 'DD';
					// }

					$salesCountData['count']++;//總計
				}elseif ($data['branch2'] > 0 && $data['branch2'] == $rs->fields['cStoreId']) {
					if ($rs->fields['cTarget'] == 2 || $rs->fields['cBranch'] == 505) {
						if (in_array($rs->fields['cStoreId'], $scrivener)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cStoreId'] == 505)?'505':$rs->fields['cStoreId']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
						
					}else{
						if (in_array($rs->fields['cStoreId'], $branch)) {
							if ($rs->fields['cSalesId'] == $sales) {
								$salesCountData['countPart']++;//分配數
								$salesCountData['branch'][] = ($rs->fields['cTarget'] == 2 || $rs->fields['cStoreId'] == 505)?'505':$rs->fields['cStoreId']; //505 非仲介成交 
								$salesCountData['feedbackMoney'] += $data['cCaseFeedBackMoney1'];
							}
						}
					}

					// if ($data['cCertifiedId'] == '070721811') {
					// 	echo 'EE';
					// }

					$salesCountData['count']++;//總計
				}
				
			}
			
			
		}

			$rs->MoveNext();
		}

	}

	if ($checkSp == 0 && $data['cSpCaseFeedBackMoney'] > 0) {
		if (in_array($data['cScrivener'], $scrivener)) {
		//地政士特殊回饋(計算案件總回饋資訊)
			if ($data['cSpCaseFeedBackMoney'] > 0 ) {
				if ($data['scrivenerSales'] == $sales) {
					$salesCountData['countPart']++;//分配數
					$salesCountData['branch'][] = 505; //505 非仲介成交
				}
				$salesCountData['count']++;//總計
				// if ($data['cCertifiedId'] == '070721811') {
				// 			echo 'FF';
				// 		}
				
				$checkSp= 1;//有算過特殊回饋業務
			}
		}
	}

	//其他回饋對象
	if (is_array($OtherFeedBackData[$data['cCertifiedId']])) {

		foreach ($OtherFeedBackData[$data['cCertifiedId']] as $k => $v) {
			if ($sales == $v['fSales']) {
				$salesCountData['countPart']++;//分配數
				$salesCountData['branch'][] = ($v['cTarget'] == 2)?505:$v['fStoreId']; //505 非仲介成交

			}
			$salesCountData['count']++;//總計
			// if ($data['cCertifiedId'] == '070721811') {
			// 			echo 'GG';
			// 		}
		}
		
	}
	unset($tmp);
	// if ($data['cCertifiedId'] == '080109073') {
	// 		echo $checkSp;
	// 	echo "<pre>";
	// 	print_r($data);

	// 	print_r($salesCountData);
	// }
	// if ($data['cCertifiedId'] == '070721811') {
	// 	echo $checkSp;
	// 	echo "<pre>";
	// 	print_r($data);

	// 	print_r($salesCountData);
	// 	die;
	// }

	return $salesCountData;
}
function coutTotalData($arr,$type,$type2,$count,$branchCount,$index){
	global $originalDataTotal;
	global $originalDataTotal1;
	global $sales;

	##

	if ($sales) {
		
		##
		if ($type2 == 1) {
				//原始的資料
			
				// echo 'coutTotalData'.$arr['cCertifiedId']."_".$count."_".floor($arr['cTotalMoney']/$branchCount)."_".floor($arr['cCertifiedMoney']/$branchCount)."<bR>";
				$originalDataTotal['count']+=$count;
				

				$originalDataTotal['totalMoney'] += round($arr['cTotalMoney']/$branchCount);
				$originalDataTotal['certifiedMoney'] += round($arr['cCertifiedMoney']/$branchCount);

				if ($index == '') { //地政士特殊回饋一個案件算一次
					$originalDataTotal['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];
					$data[3]['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal['feedbackMoney']  += $rs->fields['cSpCaseFeedBackMoney'];
				}
				
		}else{
				$originalDataTotal1['count']+=$count;
				$originalDataTotal1['totalMoney'] += round($arr['cTotalMoney']/$branchCount);
				$originalDataTotal1['certifiedMoney'] += round($arr['cCertifiedMoney']/$branchCount);

				if ($index == '') {
					$originalDataTotal1['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];
					$data1[3]['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal1['feedbackMoney']  += $rs->fields['cSpCaseFeedBackMoney'];
				}
				

		}
	}else{
		// if ($type2 == 1) {
		// 	//原始的資料
		// 	$originalDataTotal['count']++;
		// 	$originalDataTotal['totalMoney'] += $arr['cTotalMoney'];
		// 	$originalDataTotal['certifiedMoney'] += $arr['cCertifiedMoney'];

		// 	$originalDataTotal['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
		// 	$data[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
		// 	$dataTotal['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];
		// }else{
		// 	$originalDataTotal1['count']++;
		// 	$originalDataTotal1['totalMoney'] += $arr['cTotalMoney'];
		// 	$originalDataTotal1['certifiedMoney'] += $arr['cCertifiedMoney'];

		// 	$originalDataTotal1['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
		// 	$data1[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
		// 	$dataTotal1['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];

		// }

		if ($type2 == 1) {
				//原始的資料
				$originalDataTotal['count']++;
				$originalDataTotal['totalMoney'] += $arr['cTotalMoney'];
				$originalDataTotal['certifiedMoney'] += $arr['cCertifiedMoney'];
				if ($arr['cSpCaseFeedBackMoney'] > 0) {
					$originalDataTotal['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
					$data[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];
				}
				
			}else{
				$originalDataTotal1['count']++;
				$originalDataTotal1['totalMoney'] += $arr['cTotalMoney'];
				$originalDataTotal1['certifiedMoney'] += $arr['cCertifiedMoney'];
				if ($arr['cSpCaseFeedBackMoney'] > 0) {
					$originalDataTotal1['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
					$data1[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal1['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];
				}
				

			}
	}
			
}


##
function countData($arr,$type,$type2,$count,$branchCount,$index){
	global $data;
	global $originalDataTotal;
	global $data1;
	global $originalDataTotal1;
	global $dataTotal;
	global $dataTotal1;
	global $sales;

	
	if ($type2 == 1) {
			// echo 'countData'.$arr['cCertifiedId']."_".$count."_".round($arr['cTotalMoney']/$branchCount)."_".round($arr['cCertifiedMoney']/$branchCount)."<bR>";
				
			$data[$type]['count'] += $count;
			$data[$type]['totalMoney'] += round($arr['cTotalMoney']/$branchCount);
			$data[$type]['certifiedMoney'] += round($arr['cCertifiedMoney']/$branchCount);
			//加總
			$dataTotal['count'] += $count;
			$dataTotal['totalMoney'] += round($arr['cTotalMoney']/$branchCount);
			$dataTotal['certifiedMoney'] += round($arr['cCertifiedMoney']/$branchCount);

			if ($arr['cCaseFeedback'.$index] == 0) {
				$data[$type]['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
				//總額
				$originalDataTotal['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
				//加總
				$dataTotal['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
			}
	}else{
			$data1[$type]['count'] += $count;
			$data1[$type]['totalMoney'] += round($arr['cTotalMoney']/$branchCount);
			$data1[$type]['certifiedMoney'] += round($arr['cCertifiedMoney']/$branchCount);

			//加總
			$dataTotal1['count'] += $count;
			$dataTotal1['totalMoney'] += round($arr['cTotalMoney']/$branchCount);
			$dataTotal1['certifiedMoney'] += round($arr['cCertifiedMoney']/$branchCount);
					
			if ($arr['cCaseFeedback'.$index] == 0) {
				$data1[$type]['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
				//總額
				$originalDataTotal1['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];

				//加總
				$dataTotal1['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
			}
	}
	
	

	


}

function countDataSales($arr,$type,$type2,$count,$branchCount,$index){
	global $data;
	global $originalDataTotal;
	global $data1;
	global $originalDataTotal1;
	global $dataTotal;
	global $dataTotal1;
	global $sales;

	
	if ($type2 == 1) {
			// echo 'countData'.$arr['cCertifiedId']."_".$count."_".round($arr['cTotalMoney']/$branchCount)."_".round($arr['cCertifiedMoney']/$branchCount)."<bR>";
				
			$data[$type]['count'] += $count;
			$data[$type]['totalMoney'] += round($arr['cTotalMoney']*$count);
			$data[$type]['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);
			//加總
			$dataTotal['count'] += $count;
			$dataTotal['totalMoney'] += round($arr['cTotalMoney']*$count);
			$dataTotal['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

			//總計
			$originalDataTotal['count']+=$count;
			$originalDataTotal['totalMoney'] += round($arr['cTotalMoney']*$count);
			$originalDataTotal['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);


			if ($arr['cCaseFeedback'.$index] == 0) {
				$data[$type]['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
				//總額
				$originalDataTotal['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
				//加總
				$dataTotal['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
			}
	}else{
			$data1[$type]['count'] += $count;
			$data1[$type]['totalMoney'] += round($arr['cTotalMoney']*$count);
			$data1[$type]['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

			//加總
			$dataTotal1['count'] += $count;
			$dataTotal1['totalMoney'] += round($arr['cTotalMoney']*$count);
			$dataTotal1['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

			//總計
			$originalDataTotal1['count']+=$count;
			$originalDataTotal1['totalMoney'] += round($arr['cTotalMoney']*$count);
			$originalDataTotal1['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);
					
			if ($arr['cCaseFeedback'.$index] == 0) {
				$data1[$type]['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
				//總額
				$originalDataTotal1['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];

				//加總
				$dataTotal1['feedbackMoney'] += $arr['cCaseFeedBackMoney'.$index];
			}
	}
	
	

	


}

function coutTotalDataSales($arr,$type,$type2,$count,$branchCount,$index){
	global $originalDataTotal;
	global $originalDataTotal1;
	global $sales;

	##

	if ($sales) {
		
		##
		if ($type2 == 1) {
				//原始的資料
			
				// echo 'coutTotalData'.$arr['cCertifiedId']."_".$count."_".floor($arr['cTotalMoney']/$branchCount)."_".floor($arr['cCertifiedMoney']/$branchCount)."<bR>";
				$originalDataTotal['count']+=$count;
				

				$originalDataTotal['totalMoney'] += round($arr['cTotalMoney']*$count);
				$originalDataTotal['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

				if ($index == '') { //地政士特殊回饋一個案件算一次
					$originalDataTotal['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];
					$data[3]['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal['feedbackMoney']  += $rs->fields['cSpCaseFeedBackMoney'];
				}
				
		}else{
				$originalDataTotal1['count']+=$count;
				$originalDataTotal1['totalMoney'] += round($arr['cTotalMoney']*$count);
				$originalDataTotal1['certifiedMoney'] += round($arr['cCertifiedMoney']*$count);

				if ($index == '') {
					$originalDataTotal1['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];
					$data1[3]['feedbackMoney'] += $rs->fields['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal1['feedbackMoney']  += $rs->fields['cSpCaseFeedBackMoney'];
				}
				

		}
	}else{
		// if ($type2 == 1) {
		// 	//原始的資料
		// 	$originalDataTotal['count']++;
		// 	$originalDataTotal['totalMoney'] += $arr['cTotalMoney'];
		// 	$originalDataTotal['certifiedMoney'] += $arr['cCertifiedMoney'];

		// 	$originalDataTotal['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
		// 	$data[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
		// 	$dataTotal['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];
		// }else{
		// 	$originalDataTotal1['count']++;
		// 	$originalDataTotal1['totalMoney'] += $arr['cTotalMoney'];
		// 	$originalDataTotal1['certifiedMoney'] += $arr['cCertifiedMoney'];

		// 	$originalDataTotal1['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
		// 	$data1[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
		// 	$dataTotal1['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];

		// }

		if ($type2 == 1) {
				//原始的資料
				$originalDataTotal['count']++;
				$originalDataTotal['totalMoney'] += $arr['cTotalMoney'];
				$originalDataTotal['certifiedMoney'] += $arr['cCertifiedMoney'];
				if ($arr['cSpCaseFeedBackMoney'] > 0) {
					$originalDataTotal['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
					$data[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];
				}
				
			}else{
				$originalDataTotal1['count']++;
				$originalDataTotal1['totalMoney'] += $arr['cTotalMoney'];
				$originalDataTotal1['certifiedMoney'] += $arr['cCertifiedMoney'];
				if ($arr['cSpCaseFeedBackMoney'] > 0) {
					$originalDataTotal1['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];
					$data1[3]['feedbackMoney'] += $arr['cSpCaseFeedBackMoney'];//地政士特殊暫時算非仲
					$dataTotal1['feedbackMoney']  += $arr['cSpCaseFeedBackMoney'];
				}
				

			}
	}
			
}
function setOtherfeed2($arr,$type2){
	global $data;
	global $originalDataTotal;
	global $data1;
	global $originalDataTotal1;
	global $dataTotal;
	global $dataTotal1;
	
	$type = '';
	if ($arr['fType'] == 1) { //1地政2仲介
			// $data[3]['feedbackMoney'] += $arr['fMoney'];
		if ($type2 == 1) {			
			$data[3]['feedbackMoney'] += $arr['fMoney'];
			$originalDataTotal['feedbackMoney'] += $arr['fMoney'];
			
			//加總
			$dataTotal['feedbackMoney'] += $arr['fMoney'];


		}else{			
			$data1[3]['feedbackMoney'] += $arr['fMoney'];
			$originalDataTotal1['feedbackMoney'] += $arr['fMoney'];

			//加總
			$dataTotal1['feedbackMoney'] += $arr['fMoney'];
		}
		
	}else{
		$type = checkCat2019($arr['fStoreId'],$arr['brand'],$arr['category']);
		// echo $type;
			
		if ($type2 == 1) {	
			$data[$type]['feedbackMoney'] += $arr['fMoney'];
			$originalDataTotal['feedbackMoney'] += $arr['fMoney'];
			//加總
			$dataTotal['feedbackMoney'] += $arr['fMoney'];
		}else{
			$data1[$type]['feedbackMoney'] += $arr['fMoney'];
			$originalDataTotal1['feedbackMoney'] += $arr['fMoney'];
			//加總
			$dataTotal1['feedbackMoney'] += $arr['fMoney'];
		}
	}
	

}

function checkCat2019($bId,$brand,$category) {
	global $conn;
	$val = '' ;
	
	
		
		if ($category == '1') {
			// $val = '加盟' ;
			if ($brand == '1') {
				$val = 'T' ;
			}
			else if ($brand == '2' || $brand == '49') {
				$val = '3' ;
			}
			else {
				$val = 'O' ;
			}
		}
		else if ($category == '2') {
			$val = '2' ;
		}else if ($category == '3') {
			$val = '3' ;
		}else{
			$val = '3' ;
		}
	

	
	// echo $val"-----";
	return $val ;
}
##
$smarty->assign('sales',$sales);
$smarty->assign('menuSales',$menuSales);
$smarty->assign('originalDataTotal',$originalDataTotal);
$smarty->assign('originalDataTotal1',$originalDataTotal1);
$smarty->assign('dataTotal',$dataTotal);
$smarty->assign('dataTotal1',$dataTotal1);
$smarty->assign('data',$data);
$smarty->assign('data1',$data1);
$smarty->assign('menuYear',$menuYear);
$smarty->assign('year',$_POST['year']);
$smarty->assign('year2',$_POST['year2']);
$smarty->display('totalReportFor3.inc.tpl', '', 'report') ;

?> 
