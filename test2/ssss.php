<?php
include_once '../openadodb.php' ;

$sql ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate,
	cas.cEndDate as cEndDate, 
	cas.cEscrowBankAccount as cEscrowBankAccount,
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
	cas.cCaseMoney,
	cas.cFeedbackTarget,
	cas.cFeedbackTarget1,
	cas.cFeedbackTarget2,
	cas.cFeedbackTarget3

FROM 
	tContractCase AS cas 
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
WHERE
	cas.cCaseStatus = 2
GROUP BY
	cas.cCertifiedId
ORDER BY 
	cas.cApplyDate,cas.cId,cas.cSignDate ASC;
' ;



$rs = $conn->Execute($sql);
$list = array();
while (!$rs->EOF) {
	array_push($list, $rs->fields);

	$rs->MoveNext();
}

// print_r($list);
// die;
//
$data = array();
foreach ($list as $key => $value) {
	
	

	if ($value['branch'] > 0 ) {
		//回饋金對象(1:仲介、2:代書)
		if ($value['cFeedbackTarget'] == 1) {
			$sales = getBranchSales($value['branch']);
			$data[$value['cCertifiedId']][$sales] .= getBranchZip($value['branch']);
			unset($sales);
		}else{
			$sales = getScrivenerSales($value['cScrivener']);
			$data[$value['cCertifiedId']][$sales] .= getScrivenerZip($value['cScrivener']);
			unset($sales);
		}

		
	}

	if ($value['branch1'] > 0 ) {
		//回饋金對象(1:仲介、2:代書)
		if ($value['cFeedbackTarget1'] == 1) {
			$sales = getBranchSales($value['branch1']);
			$data[$value['cCertifiedId']][$sales] .= getBranchZip($value['branch1']);
			unset($sales);


		}else{
			$sales = getScrivenerSales($value['cScrivener']);
			$data[$value['cCertifiedId']][$sales] .= getScrivenerZip($value['cScrivener']);
			unset($sales);
		}

		
	}


	if ($value['branch2'] > 0 ) {
		//回饋金對象(1:仲介、2:代書)
		if ($value['cFeedbackTarget2'] == 1) {
			$sales = getBranchSales($value['branch2']);
			$data[$value['cCertifiedId']][$sales] .= getBranchZip($value['branch2']);
			unset($sales);
		}else{
			$sales = getScrivenerSales($value['cScrivener']);
			$data[$value['cCertifiedId']][$sales] .= getScrivenerZip($value['cScrivener']);
			unset($sales);
		}

		
	}


	if ($value['cSpCaseFeedBackMoney'] > 0) {
		$sales = getScrivenerSales($value['cScrivener']);
		$data[$value['cCertifiedId']][$sales] .= getScrivenerZip($value['cScrivener']);
		unset($sales);
	}
}

unset($list);

$fw = fopen('log/otherSales.log', 'a+');
foreach ($data as $key => $value) {
	
	if (count($value) > 1) {
		$txt = $key.",";
		foreach ($value as $k => $v) {
			$txt .= $k.",".$v.",";
		}
		
		$txt .= "\r\n";

		fwrite($fw, $txt);




		echo $key."\r\n";
		print_r($value);

		
	}
}
fclose($fw);

$conn->close();
// print_r($data);

die;
##
function getBranchZip($id){
	global $conn;

	$sql = "SELECT (SELECT zCity FROM tZipArea WHERE zZip = bZip) AS city FROM tBranch WHERE bId = '".$id."'";
	$rs = $conn->Execute($sql);

	return $rs->fields['city'];
}

function getScrivenerZip($id){
	global $conn;

	$sql = "SELECT (SELECT zCity FROM tZipArea WHERE zZip = sCpZip1) AS city FROM tScrivener WHERE sId = '".$id."'";
	$rs = $conn->Execute($sql);

	return $rs->fields['city'];
}

function getBranchSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS Name FROM tBranchSales WHERE bBranch = '".$id."' ORDER BY bSales ASC";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['Name'];

		$rs->MoveNext();
	}

	return @implode('_', $sales);
}

function getScrivenerSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '".$id."' ORDER BY sSales ASC";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['Name'];

		$rs->MoveNext();
	}

	return @implode('_', $sales);
}
?>