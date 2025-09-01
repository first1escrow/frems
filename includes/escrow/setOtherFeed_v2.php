<?php
include_once '../../configs/config.class.php';
include_once '../../openadodb.php';
include_once '../../session_check.php' ;

$_POST = escapeStr($_POST) ;

$certifiedId = $_GET['id'];
$sp = $_GET['sp'];

$sql = "SELECT
			cc.cSignDate,
				cc.cCertifiedId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cr.cBranchNum3,
				cc.cFeedbackTarget,
				cc.cFeedbackTarget1,
				cc.cFeedbackTarget2,
				cc.cFeedbackTarget3,
				cs.cScrivener,
				cc.cCaseFeedback,
				cc.cCaseFeedback1,
				cc.cCaseFeedback2,
				cc.cCaseFeedback3,
				cc.cCaseFeedBackMoney,
				cc.cCaseFeedBackMoney1,
				cc.cCaseFeedBackMoney2,
				cc.cCaseFeedBackMoney3,
				ci.cCertifiedMoney
			FROM
				tContractCase AS cc
			LEFT JOIN 
				tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
			LEFT JOIN
				tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
			LEFT JOIN
				tContractIncome AS ci ON ci.cCertifiedId = cc.cCertifiedId
			WHERE
				cc.cCertifiedId = '".$certifiedId."'";
// echo $sql;
// die;
$rs = $conn->Execute($sql);
$data = $rs->fields;

$checkBranch = array(); //回饋店家
$contractFeedBackData = array();
if ($data['cBranchNum'] > 0) {
	array_push($checkBranch, $data['cBranchNum']);

	//回饋資料，比對是否有回饋才能算總部
	$contractFeedBackData[$data['cBranchNum']]['feedback'] = $data['cCaseFeedback'];
	$contractFeedBackData[$data['cBranchNum']]['money'] = $data['cCaseFeedBackMoney'];
		
	if ($data['cFeedbackTarget'] == 1) {//仲
		$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$data['cBranchNum']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$sales[$rs->fields['bSales']] = $rs->fields['bSales'];

			$rs->MoveNext();
		}
	}else{
		$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$data['cScrivener']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$sales[$rs->fields['sSales']] = $rs->fields['sSales'];

			$rs->MoveNext();
		}
	}
}


if ($data['cBranchNum1'] > 0) {
	array_push($checkBranch, $data['cBranchNum1']);

	//回饋資料，比對是否有回饋才能算總部
	$contractFeedBackData[$data['cBranchNum1']]['feedback'] = $data['cCaseFeedback1'];
	$contractFeedBackData[$data['cBranchNum1']]['money'] = $data['cCaseFeedBackMoney1'];
		
	if ($data['cFeedbackTarget1'] == 1) {//仲
		$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$data['cBranchNum1']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$sales[$rs->fields['bSales']] = $rs->fields['bSales'];

			$rs->MoveNext();
		}
	}else{
		$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$data['cScrivener']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$sales[$rs->fields['sSales']] = $rs->fields['sSales'];

			$rs->MoveNext();
		}
	}
}

if ($data['cBranchNum2'] > 0) {
	array_push($checkBranch, $data['cBranchNum2']);
	//回饋資料，比對是否有回饋才能算總部
	$contractFeedBackData[$data['cBranchNum2']]['feedback'] = $data['cCaseFeedback2'];
	$contractFeedBackData[$data['cBranchNum2']]['money'] = $data['cCaseFeedBackMoney2'];

	if ($data['cFeedbackTarget2'] == 1) {//仲
		$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$data['cBranchNum2']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$sales[$rs->fields['bSales']] = $rs->fields['bSales'];

			$rs->MoveNext();
		}
	}else{
		$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$data['cScrivener']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$sales[$rs->fields['sSales']] = $rs->fields['sSales'];

			$rs->MoveNext();
		}
	}
}


// print_r($checkBranch);
// print_r($contractFeedBackData);
// die;

//計算品牌回饋
$total = 0;
if (!empty($checkBranch)) {
	$FeedBackBrand = array();
	$brandCount = array();
	$sql = "SELECT 
					a.bId,
					a.bBrand,
					b.bRecall,
					b.bBranch AS TargetBranch,
					b.bSignDate
				FROM
					tBranch AS a
				LEFT JOIN
				    tBrand AS b ON b.bId=a.bBrand
				WHERE
					a.bId IN(".implode(',', $checkBranch).")";
		// echo $sql;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		// print_r($rs->fields);
		if ($rs->fields['bRecall']) {
			// echo $data['cSignDate']."_".$rs->fields['bSignDate'];
			if ($data['cSignDate'] >= $rs->fields['bSignDate'] ) {
				$FeedBackBrand[$rs->fields['bBrand']] = $rs->fields;
				$brandCount[$rs->fields['bBrand']]++; //計算品牌數
				$total++;
			}

				
		}
		$rs->MoveNext();
	}

	
	if ($total > 0) {

		foreach ($FeedBackBrand as $k => $dataFeed) {
			$storeId = $dataFeed['TargetBranch'];
			$recall = $dataFeed['bRecall'];

			//檢查是否回饋

			// echo $contractFeedBackData[$dataFeed['bId']]['feedback']."_".$contractFeedBackData[$dataFeed['bId']]['money'];
			if ($contractFeedBackData[$dataFeed['bId']]['feedback'] == 0 && $contractFeedBackData[$dataFeed['bId']]['money'] > 0 && $sp == '') {

				$recall = (count($checkBranch) != $brandCount[$dataFeed['bBrand']])? (($recall*($brandCount[$dataFeed['bBrand']]/count($checkBranch)))/100):($recall/100);
				// echo 'CCCC';
				
				$money = round($data['cCertifiedMoney'] * $recall) ;
				if (!checkBrandFeed($certifiedId,$storeId)) {
					// echo 'A'.$money."_";
					// echo $certifiedId;
					addFeedbackmoney($certifiedId,$storeId,$money,$sales);
				}else{
					// echo 'B'.$money;
					updateFeedbackmoney($certifiedId,$storeId,$money,$sales);
				}
			}
		
		}
	}else{
			deleFeedbackmoney($certifiedId,'b',$storeId);//不是在條件內的店家之前算的回饋金要刪除
			
	}

}

//群組
$total = 0;
	
if (!empty($checkBranch)) {
	$FeedBackBrand = array();
	$brandCount = array();
		$sql = "SELECT 
					a.bId,
					a.bBrand,
					b.bRecall,
					b.bBranch AS TargetBranch,
					b.bSignDate
				FROM
					tBranch AS a
				LEFT JOIN
				    tBranchGroup AS b ON b.bId=a.bGroup
				WHERE
					a.bId IN(".implode(',', $checkBranch).")";
		// echo $sql;
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			if ($rs->fields['bRecall']) {
				if ($data['cSignDate'] >= $rs->fields['bSignDate'] ) {
					$FeedBackBrand[$rs->fields['bBrand']] = $rs->fields;
					$brandCount[$rs->fields['bBrand']]++; //計算品牌數
					$total++;
				}
				
			}
			$rs->MoveNext();
		}

		if ($total > 0) {
			foreach ($FeedBackBrand as $k => $dataFeed) {
				$storeId = $dataFeed['TargetBranch'];
				$recall = $dataFeed['bRecall'];

				// echo $recall."_".$brandCount[$dataFeed['bBrand']]."_".count($checkBranch)."\r\n";
				if ($contractFeedBackData[$dataFeed['bId']]['feedback'] == 0 && $contractFeedBackData[$dataFeed['bId']]['money'] > 0 && $sp == '') {
					$recall = (count($checkBranch) != $brandCount[$dataFeed['bBrand']])? (($recall*($brandCount[$dataFeed['bBrand']]/count($checkBranch)))/100):($recall/100);

					$money = round($data['cCertifiedMoney'] * $recall) ;

					// echo $money;
					if (!checkBrandFeed($certifiedId,$storeId)) {
						// echo 'GA'.$moeny;
						addFeedbackmoney($certifiedId,$storeId,$money,$sales);
					}else{
						// echo 'GB'.$moeny;
						updateFeedbackmoney($certifiedId,$storeId,$money,$sales);
					}
				}
			}
		}else{
			deleFeedbackmoney($certifiedId,'g',$storeId);//不是在條件內的店家之前算的回饋金要刪除
			
		}

}

	

unset($sales);




function checkBrandFeed($cId,$branch){
	global $conn;

	if (!$branch) {
		return false;
	}

	$sql = "SELECT fId FROM tFeedBackMoney WHERE fType = '2' AND fDelete = 0 AND fCertifiedId = '".$cId."' AND fStoreId = '".$branch."'";
	// echo $sql;
	$rs = $conn->Execute($sql);
	$total = $rs->RecordCount();

	if ($total > 0) {
		return true; //有資料 不要新增
	}else{
		return false; //
	}
	

}
function addFeedbackmoney($cId,$branch,$money,$sales){
	global $conn;

	//先取得業務
	// $sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$branch."'";
	// // echo $sql."\r\n";
	// $rs = $conn->Execute($sql);
	// $sales = $rs->fields['bSales'];
	//案件回饋對象業務
	
	//
	// echo $money."_".$cId;
	if ($money > 0 && $cId != '') {
		
		$sql = "INSERT INTO
				tFeedBackMoney
			SET
				fType = 2,
				fCertifiedId = '".$cId."',
				fStoreId = '".$branch."',
				fMoney = '".$money."',
				fSales = '".@implode(',', $sales)."'
			";
			// echo $sql."<br>";		

		$conn->Execute($sql);
	}
	

	

}

function updateFeedbackmoney($cId,$branch,$money,$sales){
	global $conn;

	if ($money == 0) {
		$str = " AND fDelete = 1";
	}

	$sql = "UPDATE
				tFeedBackMoney
			SET
				fMoney = '".$money."',
				fSales = '".@implode(',', $sales)."'
			WHERE
			 	fCertifiedId = '".$cId."'
			 	AND fStoreId = '".$branch."'
			 	AND fType = 2".$str;
	// echo $sql."<br>";		
	$conn->Execute($sql);

}

function deleFeedbackmoney($cId,$type,$targetBranch){
	global $conn;

	if ($type == 'g') {
		$sql = "SELECT bBranch FROM tBranchGroup WHERE bBranch > 0";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$branchArr[] = $rs->fields['bBranch'];

			$rs->MoveNext();
		}

		

	}else if($type == 'b'){
		$sql = "SELECT bBranch FROM tBrand WHERE bBranch > 0";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$branchArr[] = $rs->fields['bBranch'];

			$rs->MoveNext();
		}

	}
	

	$sql = "UPDATE
				tFeedBackMoney
			SET
				fDelete = 1
			WHERE
				fType = '2'  AND fCertifiedId = '".$cId."' AND fStoreId = '".@implode(',', $branchArr)."'";
	// echo $type."<br>";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);

}
?>