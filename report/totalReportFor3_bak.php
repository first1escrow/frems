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
				tFeedBackMoney WHERE fDelete = 0 ".$FeedBackMoneyQueryStr;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$OtherFeedBackData[$rs->fields['fCertifiedId']][] = $rs->fields;

		$rs->MoveNext();
	}
	unset($FeedBackMoneyQueryStr);
	
	##
	$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子

	$query .= " AND cas.cApplyDate >= '".($_POST['year']+1911)."-01-01 00:00:00' AND cas.cApplyDate <= '".($_POST['year2']+1911)."-12-31 00:00:00'";

	if ($query) { $query = ' WHERE '.$query ; }


	$query ='
	SELECT 
		cas.cCertifiedId,
		cas.cApplyDate,
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




		if ($sales) {
			// if ($rs->fields['cCertifiedId'] == '002096316') {
			// 	echo $count."_".$sales."_".$rs->fields['branch'];
			// }

			$type = checkCat2019($rs->fields['branch'],$rs->fields['brand'],$rs->fields['category']);//案件類別判定
			// if ($type == 'N') {
			// 	$WW[] = $rs->fields;
			// }
			// // echo $type;
			if ($rs->fields['branch'] != 505 && $rs->fields['branch'] != 0) {
				
				
				if (in_array($rs->fields['branch'],$branchCheck[$_POST['sales']])) {
					$checkArr[$rs->fields['cCertifiedId']] += $count; 
					countData($rs->fields,$type,$type2,$count,$branchCount,'');

					coutTotalData($rs->fields,$type,$type2,$count,$branchCount,'');
				}
			}else{
				if (in_array($rs->fields['cScrivener'],$scrivenerCheck[$_POST['sales']])) {
					
					countData($rs->fields,$type,$type2,$count,$branchCount,'');

					coutTotalData($rs->fields,$type,$type2,$count,$branchCount,'');
				}
			}
			
			
				if ($rs->fields['branch1'] > 0) {
					$type = checkCat2019($rs->fields['branch1'],$rs->fields['brand1'],$rs->fields['category1']);
					$checkArr[$rs->fields['cCertifiedId']] += $count;
					
					if ($rs->fields['branch1'] != 505) {
						
						if (in_array($rs->fields['branch1'],$branchCheck[$_POST['sales']])) {
							
							countData($rs->fields,$type,$type2,$count,$branchCount,1);
							coutTotalData($rs->fields,$type,$type2,$count,$branchCount,1);

						}
					}else{
						if (in_array($rs->fields['cScrivener'],$scrivenerCheck[$_POST['sales']])) {
						
							countData($rs->fields,$type,$type2,$count,$branchCount,1);
							coutTotalData($rs->fields,$type,$type2,$count,$branchCount,1);
						}
					}
					
				}

				if ($rs->fields['branch2'] > 0) {
					$type = checkCat2019($rs->fields['branch2'],$rs->fields['brand2'],$rs->fields['category2']);
					if ($type == 'N') {
							$WW[] = $rs->fields;
						}
						
					if ($rs->fields['branch2'] != 505) { //505 非仲成交
						if (in_array($rs->fields['branch2'],$branchCheck[$_POST['sales']])) {
							countData($rs->fields,$type,$type2,$count,$branchCount,2);
							coutTotalData($rs->fields,$type,$type2,$count,$branchCount,2);
						}
					}else{
						if (in_array($rs->fields['cScrivener'],$scrivenerCheck[$_POST['sales']])) {
						
							countData($rs->fields,$type,$type2,$count,$branchCount,1);
							coutTotalData($rs->fields,$type,$type2,$count,$branchCount,1);
						}
					}
					
				}

				if ($rs->fields['branch3'] > 0) {
					if ($rs->fields['branch3'] != 505) {
						$type = checkCat2019($rs->fields['branch3'],$rs->fields['brand3'],$rs->fields['category3']);
						if ($type == 'N') {
							$WW[] = $rs->fields;
						}
						if (in_array($rs->fields['branch3'],$branchCheck[$_POST['sales']])) {
							
							countData($rs->fields,$type,$type2,$count,$branchCount,3);
							coutTotalData($rs->fields,$type,$type2,$count,$branchCount,3);
						}
					}else{
						if (in_array($rs->fields['cScrivener'],$scrivenerCheck[$_POST['sales']])) {
							countData($rs->fields,$type,$type2,$count,$branchCount,1);
							coutTotalData($rs->fields,$type,$type2,$count,$branchCount,1);
						}
					}
					
				}

				if (is_array($OtherFeedBackData[$rs->fields['cCertifiedId']])) {
					//setOtherfeed2($data)
					foreach ($OtherFeedBackData[$rs->fields['cCertifiedId']] as $k => $v) {
						// otherfeed($v);
						setOtherfeed2($v,$type2);
						// coutTotalData($rs->fields,$type,$type2,$count,$branchCount,3);
					}
				}
			

			


		}else{
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
