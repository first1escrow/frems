<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php';


$query = "WHERE cas.cSignDate >='2020-01-01 00:00:00' AND cas.cSignDate <= '2020-10-19 23:59:59'";

$query ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate,
	cas.cEndDate as cEndDate, 
	cas.cEscrowBankAccount as cEscrowBankAccount,
	buy.cName as buyer, 
	own.cName as owner, 
	inc.cTotalMoney as cTotalMoney, 
	inc.cCertifiedMoney as cCertifiedMoney, 
	csc.cScrivener as cScrivener, 
	(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener, 
	(SELECT b.sOffice FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as sOffice,
	(SELECT b.sCategory FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivenerCategory, 
	pro.cAddr as cAddr, 
	pro.cZip as cZip, 
	zip.zCity as zCity, 
	zip.zArea as zArea, 
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2,
	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,	
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brandname3,
	rea.cBrand as brand,
	rea.cBrand1 as brand1,
	rea.cBrand2 as brand2,
	rea.cBrand2 as brand3,
	rea.cBranchNum as branch,
	rea.cBranchNum1 as branch1,
	rea.cBranchNum2 as branch2,
	rea.cBranchNum3 as branch3,
	(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) category,
	(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) category1,
	(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) category2,
	(SELECT bName FROM tBranch WHERE bId=rea.cBranchNum) branchName,
	(SELECT bName FROM tBranch WHERE bId=rea.cBranchNum1) branchName1,
	(SELECT bName FROM tBranch WHERE bId=rea.cBranchNum2) branchName2,
	scr.sBrand as scr_brand,
	scr.sCategory as scr_cat,
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
	cas.cFeedbackTarget3
FROM 
	tContractCase AS cas 
LEFT JOIN 
	tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
LEFT JOIN 
	tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN
	tZipArea AS zip ON zip.zZip=pro.cZip
LEFT JOIN 
	tScrivener AS scr ON scr.sId = csc.cScrivener
'.$query.' 
GROUP BY
	cas.cCertifiedId
ORDER BY 
	cas.cApplyDate,cas.cId,cas.cSignDate ASC;
' ;
$rs = $conn->Execute($query);
// echo $query;
// die;
while (!$rs->EOF) {
	$list[] = $rs->fields;


	$rs->MoveNext();
}
//仲介店型態
Function realtyCat($id) {
	global $conn;
	$sql = 'SELECT bBrand,bCategory FROM tBranch WHERE bId="'.$id.'";' ;
	// echo $sql;

	$rs= $conn->Execute($sql);
	
	return array($rs->fields['bBrand'],$rs->fields['bCategory']) ;
}

$i = 0;
$scrivenerIncome = array();
	$branchIncome = array();
	$scrivenerExpense = array();
	$branchExpense = array();
	$totalIncome = 0;
	$totalExpense = 0;
	$income = array();
	$expense = array();

foreach ($list as $k => $v) {
	$cBrand = '' ;
	$arrTmp = array();
	$store = array('o'=>'','t'=>'','u'=>'','s'=>'','n'=>'');
	

	$o = 0 ;			//加盟--其他品牌
	$t = 0 ;			//加盟--台灣房屋
	$u = 0 ;			//優美
	$s = 0 ;			//直營
	$n = 0 ;			//非仲介成交
	
	$bId = $v['branch'] ;					//第一組仲介品牌代號
	if ($bId > 0) {
		$arrTmp = realtyCat($bId) ;
		if ($arrTmp[0] == '1') {			//台灣房屋
			if ($arrTmp[1] == '2') {			//直營
				$s++ ;
				$store['s'] = $bId;
			}
			else {								//加盟
				$t++ ;
				$store['t'] = $bId;
			}
		}
		else if ($arrTmp[0] == '2') {		//非仲介成交
			$n++ ;
			$store['n'] = $v['cScrivener'];
		}
		else if ($arrTmp[0] == '49') {		//優美
			$u++ ;

			$store['u'] = $bId;
		}
		else {								//其他品牌
			$o++ ;
			$store['o'] = $bId;
		}
		//回饋金

		if ($v['cCaseFeedback'] == 0) {
			// echo 'C';
			$totalExpense+=$v['cCaseFeedBackMoney'];

			if ($bId == 505 || $v['cFeedbackTarget'] == 2) {
				// echo 'A';
				if (empty($scrivenerExpense[$v['cScrivener']])) {
					$scrivenerExpense[$v['cScrivener']] = 0;
					$expense['s'.$v['cScrivener']] = 0;

				}
				$scrivenerExpense[$v['cScrivener']] += $v['cCaseFeedBackMoney'];
				$expense['s'.$v['cScrivener']] += $v['cCaseFeedBackMoney'];



			}else{
				// echo 'B';
				if (empty($branchExpense[$bId])) {
					$branchExpense[$bId] = 0;
					$expense['b'.$bId] = 0;
				}
				$branchExpense[$bId] += $v['cCaseFeedBackMoney'];
				$expense['b'.$bId] += $v['cCaseFeedBackMoney'];
			}
		}
		

	}
	//$scrivener[$v['cScrivener']] += $v['cCertifiedMoney'];

	$bId = $v['branch1'] ;
	if ($bId > 0) {										//第二組仲介是否存在
		$arrTmp = realtyCat($bId) ;
		if ($arrTmp[0] == '1') {			//台灣房屋
			if ($arrTmp[1] == '2') {			//直營
				$s ++ ;
				if (empty($store['s'])) {
					$store['s'] = $bId;
				}
			}	
			else {								//加盟
				$t ++ ;
				if (empty($store['t'])) {
					$store['t'] = $bId;
				}
			}
		}
		else if ($arrTmp[0] == '2') {		//非仲介成交
			$n ++ ;
			$store['n'] = $v['cScrivener'];
		}
		else if ($arrTmp[0] == '49') {		//優美
			$u ++ ;
			$store['u'] = $bId;
			if (empty($store['u'])) {
				$store['u'] = $bId;
			}
		}
		else {								//其他品牌
			$o ++ ;
			if (empty($store['o'])) {
				$store['o'] = $bId;
			}
			
		}
		//回饋金
		if ($v['cCaseFeedback1'] == 0) {
			$totalExpense+=$v['cCaseFeedBackMoney1'];
			if ($bId == 505 || $v['cFeedbackTarget1'] == 2) {
				if (empty($scrivenerExpense[$v['cScrivener']])) {
					$scrivenerExpense[$v['cScrivener']] = 0;
					$expense['s'.$v['cScrivener']] = 0;
				}
				$scrivenerExpense[$v['cScrivener']] += $v['cCaseFeedBackMoney1'];
				$expense['s'.$v['cScrivener']] += $v['cCaseFeedBackMoney1'];
			}else{
				if (empty($branchExpense[$bId])) {
					$branchExpense[$bId] = 0;
					$expense['b'.$bId] = 0;
				}
				$branchExpense[$bId] += $v['cCaseFeedBackMoney1'];
				$expense['b'.$bId] += $v['cCaseFeedBackMoney1'];
			}
			// echo $bId."_".$v['cCaseFeedBackMoney1']."_";
			// die;
		}
		
	}
	
	$bId = $v['branch2'] ;
	if ($bId > 0) {										//第三組仲介是否存在
		$arrTmp = realtyCat($bId) ;
		if ($arrTmp[0] == '1') {			//台灣房屋
			if ($arrTmp[1] == '2') {			//直營
				$s ++ ;
				if (empty($store['s'])) {
					$store['s'] = $bId;
				}
			}
			else {								//加盟
				$t ++ ;
				if (empty($store['t'])) {
					$store['t'] = $bId;
				}

			}
		}
		else if ($arrTmp[0] == '2') {		//非仲介成交
			$n ++ ;
			$store['n'] = $v['cScrivener'];
		}
		else if ($arrTmp[0] == '49') {		//優美
			$u ++ ;
			if (empty($store['u'])) {
					$store['u'] = $bId;
				}
		}
		else {								//其他品牌
			$o ++ ;
			if (empty($store['o'])) {
					$store['o'] = $bId;
				}
		}

		//回饋金
		if ($v['cCaseFeedback2'] == 0) {
			$totalExpense+=$v['cCaseFeedBackMoney2'];
			if ($bId == 505 || $v['cFeedbackTarget2'] == 2) {
				if (empty($scrivenerExpense[$v['cScrivener']])) {
					$scrivenerExpense[$v['cScrivener']] = 0;
					$expense['s'.$v['cScrivener']] = 0;
				}
				$scrivenerExpense[$v['cScrivener']] += $v['cCaseFeedBackMoney2'];
				$expense['s'.$v['cScrivener']] += $v['cCaseFeedBackMoney2'];
			}else{
				if (empty($branchExpense[$bId])) {
					$branchExpense[$bId] = 0;
					$expense['b'.$bId] = 0;
				}
				$branchExpense[$bId] += $v['cCaseFeedBackMoney2'];
				$expense['b'.$bId] += $v['cCaseFeedBackMoney2'];
			}
		}
		
	}

	//地政士特殊回饋
	if ($v['cSpCaseFeedBackMoney'] > 0) {
		$totalExpense+=$v['cSpCaseFeedBackMoney'];
		if (empty($scrivenerExpense[$v['cScrivener']])) {
			$scrivenerExpense[$v['cScrivener']] = 0;
			$expense['s'.$v['cScrivener']] = 0;
		}
		$scrivenerExpense[$v['cScrivener']] += $v['cSpCaseFeedBackMoney'];
		$expense['s'.$v['cScrivener']] += $v['cSpCaseFeedBackMoney'];
	}

	//其他回饋
	$sql = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId = '".$v['cCertifiedId']."' AND fDelete = 0 AND (fType = 1 OR fType = 2)";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$totalExpense+=$rs->fields['fMoney'];

		if ($rs->fields['fType'] == 1) { //地政士 //fStoreId
			if (empty($scrivenerExpense[$rs->fields['fStoreId']])) {
				$scrivenerExpense[$rs->fields['fStoreId']] = 0;
				$expense['s'.$rs->fields['fStoreId']]  = 0;
			}
			$scrivenerExpense[$rs->fields['fStoreId']] += $rs->fields['fMoney'];
			$expense['s'.$rs->fields['fStoreId']]  += $rs->fields['fMoney'];
		}else{
			if (empty($branchExpense[$rs->fields['fStoreId']])) {
				$branchExpense[$rs->fields['fStoreId']] = 0;
				$expense['b'.$rs->fields['fStoreId']]  = 0;
			}
			$branchExpense[$rs->fields['fStoreId']] += $rs->fields['fMoney'];
			$expense['b'.$rs->fields['fStoreId']] += $rs->fields['fMoney'];
		}
		

		$rs->MoveNext();
	}
	
	// print_r($store);
	$totalIncome += $v['cCertifiedMoney'];
	if ($o > 0) {

		if (empty($branchIncome[$store['o']])) {
			$branchIncome[$store['o']] = 0;
			$income['b'.$store['o']] = 0;
		}

		$branchIncome[$store['o']] += $v['cCertifiedMoney'];
		$income['b'.$store['o']] += $v['cCertifiedMoney'];
	}
	else if ($t > 0) {
		if (empty($branchIncome[$store['t']])) {
			$branchIncome[$store['t']] = 0;
			$income['b'.$store['t']] = 0;
		}
		$branchIncome[$store['t']] += $v['cCertifiedMoney'];
		$income['b'.$store['t']] += $v['cCertifiedMoney'];
	}
	else if ($u > 0) {
		if (empty($branchIncome[$store['u']])) {
			$branchIncome[$store['u']] = 0;
			$income['b'.$store['u']] = 0;
		}
		$branchIncome[$store['u']] += $v['cCertifiedMoney'];
		$income['b'.$store['u']] += $v['cCertifiedMoney'];
	}
	else if ($s > 0) {
		if (empty($branchIncome[$store['s']])) {
			$branchIncome[$store['s']] = 0;
			$income['b'.$store['s']] = 0;
		}
		$branchIncome[$store['s']] += $v['cCertifiedMoney'];
		$income['b'.$store['s']] += $v['cCertifiedMoney'];
	}
	else {
		if (empty($scrivenerIncome[$store['n']])) {
			$scrivenerIncome[$store['n']] = 0;
			$income['s'.$store['n']] = 0;
		}

		$scrivenerIncome[$store['n']] += $v['cCertifiedMoney'];
		$income['s'.$store['n']] += $v['cCertifiedMoney'];
	}

	
}
// print_r($branchIncome);
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件報表");
$objPHPExcel->getProperties()->setDescription("案件統計報表");

$index = 0;
$objPHPExcel->setActiveSheetIndex($index);
$objPHPExcel->getActiveSheet()->setCellValue('A1','總收入');
$objPHPExcel->getActiveSheet()->setCellValue('A2',$totalIncome);
$objPHPExcel->getActiveSheet()->setCellValue('B1','總成本');
$objPHPExcel->getActiveSheet()->setCellValue('B2',$totalExpense);


$index++;

//指定目前工作頁
$objPHPExcel->createSheet($index) ;
$objPHPExcel->setActiveSheetIndex($index);
$objPHPExcel->getActiveSheet()->setTitle('綜合收入');


$objPHPExcel->getActiveSheet()->setCellValue('A1','店編');
$objPHPExcel->getActiveSheet()->setCellValue('B1','統編');
$objPHPExcel->getActiveSheet()->setCellValue('C1','公司名');
$objPHPExcel->getActiveSheet()->setCellValue('D1','金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','收入比率');

arsort($income);
$row = 2;
foreach ($income as $key => $v) {
	$col = 65;
	if (substr($key, 0,1) == 's') {
		$sql = "SELECT sOffice,sId,sSerialnum FROM tScrivener WHERE sId = ".(int)substr($key, 1);
	
		$rs = $conn->Execute($sql);
		$part = (round($v/$totalIncome,4)*100).'%';
		$code = 'SC'.str_pad($rs->fields['sId'], 4,0,STR_PAD_LEFT);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sSerialnum']);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['sSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sOffice']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
	}else{

		// echo $key."\r\n";
		$sql = "SELECT bName,bId,bSerialnum,(SELECT bCode FROM tBrand WHERE bId = bBrand) AS code FROM tBranch WHERE bId = ".(int)substr($key, 1);
		
		$rs = $conn->Execute($sql);
		$part = (round($v/$totalIncome,4)*100).'%';
		$code = $rs->fields['code'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bSerialnum']);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['bSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bName']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
		
		
	}
	// echo $sql;
	// die;
	$row++;
}

$index++;

//指定目前工作頁
$objPHPExcel->createSheet($index) ;
$objPHPExcel->setActiveSheetIndex($index);
$objPHPExcel->getActiveSheet()->setTitle('綜合成本');


$objPHPExcel->getActiveSheet()->setCellValue('A1','店編');
$objPHPExcel->getActiveSheet()->setCellValue('B1','統編');
$objPHPExcel->getActiveSheet()->setCellValue('C1','公司名');
$objPHPExcel->getActiveSheet()->setCellValue('D1','金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','收入比率');

arsort($expense);
$row = 2;
foreach ($expense as $key => $v) {
	$col = 65;
	if (substr($key, 0,1) == 's') {
		$sql = "SELECT sOffice,sId,sSerialnum FROM tScrivener WHERE sId = ".(int)substr($key, 1);
	
		$rs = $conn->Execute($sql);
		$part = (round($v/$totalExpense,4)*100).'%';
		$code = 'SC'.str_pad($rs->fields['sId'], 4,0,STR_PAD_LEFT);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sSerialnum']);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['sSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sOffice']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
	}else{

		// echo $key."\r\n";
		$sql = "SELECT bName,bId,bSerialnum,(SELECT bCode FROM tBrand WHERE bId = bBrand) AS code FROM tBranch WHERE bId = ".(int)substr($key, 1);
		
		$rs = $conn->Execute($sql);
		$part = (round($v/$totalExpense,4)*100).'%';
		$code = $rs->fields['code'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bSerialnum']);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['bSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bName']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
		
		
	}
	// echo $sql;
	// die;
	$row++;
}
##
$index++;

//指定目前工作頁
$objPHPExcel->createSheet($index) ;
$objPHPExcel->setActiveSheetIndex($index);
$objPHPExcel->getActiveSheet()->setTitle('仲介收入');


$objPHPExcel->getActiveSheet()->setCellValue('A1','店編');
$objPHPExcel->getActiveSheet()->setCellValue('B1','統編');
$objPHPExcel->getActiveSheet()->setCellValue('C1','公司名');
$objPHPExcel->getActiveSheet()->setCellValue('D1','金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','收入比率');

arsort($branchIncome);

$row = 2;
foreach ($branchIncome as $key => $v) {
	$col = 65;
	// echo $key."\r\n";
	$sql = "SELECT bName,bId,bSerialnum,(SELECT bCode FROM tBrand WHERE bId = bBrand) AS code FROM tBranch WHERE bId = ".$key;
	
	$rs = $conn->Execute($sql);
	$part = (round($v/$totalIncome,4)*100).'%';
	$code = $rs->fields['code'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bSerialnum']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['bSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
	
	$row++;
	
}
$index++;

//指定目前工作頁
$objPHPExcel->createSheet($index) ;
$objPHPExcel->setActiveSheetIndex($index);
$objPHPExcel->getActiveSheet()->setTitle('地政士收入');


$objPHPExcel->getActiveSheet()->setCellValue('A1','店編');
$objPHPExcel->getActiveSheet()->setCellValue('B1','統編');
$objPHPExcel->getActiveSheet()->setCellValue('C1','公司名');
$objPHPExcel->getActiveSheet()->setCellValue('D1','金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','收入比率');

arsort($scrivenerIncome);

$row = 2;
foreach ($scrivenerIncome as $key => $v) {
	$col = 65;
	// echo $key."\r\n";
	$sql = "SELECT sOffice,sId,sSerialnum FROM tScrivener WHERE sId = ".$key;
	
	$rs = $conn->Execute($sql);
	$part = (round($v/$totalIncome,4)*100).'%';
	$code = 'SC'.str_pad($rs->fields['sId'], 4,0,STR_PAD_LEFT);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sSerialnum']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['sSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sOffice']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
	
	$row++;
	
}
$index++;

//指定目前工作頁
$objPHPExcel->createSheet($index) ;
$objPHPExcel->setActiveSheetIndex($index);
$objPHPExcel->getActiveSheet()->setTitle('仲介成本');


$objPHPExcel->getActiveSheet()->setCellValue('A1','店編');
$objPHPExcel->getActiveSheet()->setCellValue('B1','統編');
$objPHPExcel->getActiveSheet()->setCellValue('C1','公司名');
$objPHPExcel->getActiveSheet()->setCellValue('D1','金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','成本比率');

arsort($branchExpense);

// print_r($branchExpense);

// die;

$row = 2;
foreach ($branchExpense as $key => $v) {
	$col = 65;
	// echo $key."\r\n";
	$sql = "SELECT bName,bId,bSerialnum,(SELECT bCode FROM tBrand WHERE bId = bBrand) AS code FROM tBranch WHERE bId = ".$key;
	
	$rs = $conn->Execute($sql);
	$part = (round($v/$totalExpense,4)*100).'%';
	$code = $rs->fields['code'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bSerialnum']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['bSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
	
	$row++;
	
}
$index++;

//指定目前工作頁
$objPHPExcel->createSheet($index) ;
$objPHPExcel->setActiveSheetIndex($index);
$objPHPExcel->getActiveSheet()->setTitle('地政士成本');


$objPHPExcel->getActiveSheet()->setCellValue('A1','店編');
$objPHPExcel->getActiveSheet()->setCellValue('B1','統編');
$objPHPExcel->getActiveSheet()->setCellValue('C1','公司名');
$objPHPExcel->getActiveSheet()->setCellValue('D1','金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','成本比率');

arsort($scrivenerExpense);

$row = 2;
foreach ($scrivenerExpense as $key => $v) {
	$col = 65;
	// echo $key."\r\n";
	$sql = "SELECT sOffice,sId,sSerialnum FROM tScrivener WHERE sId = ".$key;
	
	$rs = $conn->Execute($sql);
	$part = (round($v/$totalExpense,4)*100).'%';
	$code = 'SC'.str_pad($rs->fields['sId'], 4,0,STR_PAD_LEFT);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sSerialnum']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $rs->fields['sSerialnum'],PHPExcel_Cell_DataType::TYPE_STRING); 
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['sOffice']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$part);
	
	$row++;
	
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("/var/www/html/first.twhg.com.tw/test2/log/20201020.xlsx");
// print_r($branchIncome);

?>