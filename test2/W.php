<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php';
$sales  = $_POST['sales'];

$sales = 67;

$sql = "SELECT bBranch,COUNT(bBranch)AS bcount,bId  FROM tBranchSales WHERE bSales = '68' GROUP BY bBranch";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	if ($rs->fields['bcount'] > 1) {
		$sql = "DELETE FROM tBranchSales WHERE bId = '".$rs->fields['bId']."'";
		echo $sql.";<br>";
	}
	

	$rs->MoveNext();
}



$sql = "SELECT sScrivener,COUNT(sScrivener) AS sCount,sId FROM tScrivenerSales WHERE sSales ='68' GROUP BY sScrivener";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	// $scrivener[] = $rs->fields['sScrivener'];
	if ($rs->fields['sCount'] > 1) {
		$sql = "DELETE FROM tScrivenerSales WHERE sId = '".$rs->fields['sId']."'";
		echo $sql.";<br>";
	}
	$rs->MoveNext();
}

// $sql = "SELECT
// 			cc.cCertifiedId,
// 			cc.cFeedbackTarget,
// 			cc.cFeedbackTarget1,
// 			cc.cFeedbackTarget2,
// 			cr.cBranchNum,
// 			cr.cBranchNum1,
// 			cr.cBranchNum2,
// 			cc.cSpCaseFeedBackMoney,
// 			cs.cScrivener
// 		FROM
// 			tContractCase AS cc
// 		LEFT JOIN
// 			tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
// 		LEFT JOIN
// 			tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
// 		WHERE
// 			cc.cSignDate >= '2020-06-11' AND (cr.cBranchNum IN(".@implode(',', $branch).") OR cr.cBranchNum1 IN (".@implode(',', $branch).") OR cr.cBranchNum2 IN (".@implode(',', $branch).") OR cs.cScrivener IN (".@implode(',', $scrivener)."))";

// $rs = $conn->Execute($sql); //回饋金對象(1:仲介、2:代書)

// while (!$rs->EOF) {
// 	$sales = array();
// 	if ($rs->fields['cFeedbackTarget'] ==2) {
// 		$sales = getScrivenerSales($rs->fields['cScrivener']);
// 	}else{
// 		$sales = getBranchSales($rs->fields['cBranchNum']);
// 	}
	

	

// 	foreach ($sales as $key => $value) { //服務對象、1:仲介、2:地政士、3特殊回饋地政士
				
// 		$data[$rs->fields['cCertifiedId']][] = "INSERT INTO tContractSales (cCertifiedId,cTarget,cBranch,cSalesId) VALUES('".$rs->fields['cCertifiedId']."','".$rs->fields['cFeedbackTarget']."','".$rs->fields['cBranchNum']."','".$value."')";
// 				// echo $sql.";<br>";
// 	}


// 	if ($rs->fields['cBranchNum1'] > 0) {
// 		$sales = array();
// 		if ($rs->fields['cFeedbackTarget'] ==2) {
// 			$sales = getScrivenerSales($rs->fields['cScrivener']);
// 		}else{
// 			$sales = getBranchSales($rs->fields['cBranchNum1']);
// 		}
		

// 			foreach ($sales as $key => $value) { //服務對象、1:仲介、2:地政士、3特殊回饋地政士
				
// 				// $data[$rs->fields['cCertifiedId']][] = "INSERT INTO tContractSales SET cCertifiedId = '".$rs->fields['cCertifiedId']."' AND cTarget = '".$rs->fields['cFeedbackTarget1']."' AND cBranch = '".$rs->fields['cBranchNum1']."' AND cSalesId = '".$value."'";
// 				$data[$rs->fields['cCertifiedId']][] = "INSERT INTO tContractSales (cCertifiedId,cTarget,cBranch,cSalesId) VALUES('".$rs->fields['cCertifiedId']."','".$rs->fields['cFeedbackTarget1']."','".$rs->fields['cBranchNum1']."','".$value."')";
// 				// echo $sql.";<br>";
// 			}
		

// 		unset($sales);
// 	}

	
// 	unset($sales);

// 	if ($rs->fields['cBranchNum2'] > 0) {
// 		$sales = array();
// 		if ($rs->fields['cFeedbackTarget'] ==2) {
// 			$sales = getScrivenerSales($rs->fields['cScrivener']);
// 		}else{
// 			$sales = getBranchSales($rs->fields['cBranchNum2']);
// 		}
		

		
// 		foreach ($sales as $key => $value) { //服務對象、1:仲介、2:地政士、3特殊回饋地政士
				
// 			$data[$rs->fields['cCertifiedId']][] = "INSERT INTO tContractSales (cCertifiedId,cTarget,cBranch,cSalesId) VALUES('".$rs->fields['cCertifiedId']."','".$rs->fields['cFeedbackTarget2']."','".$rs->fields['cBranchNum2']."','".$value."')";
// 				// echo $sql.";<br>";
// 		}
		
// 		unset($sales);
// 	}

// 	if ($rs->fields['cSpCaseFeedBackMoney'] > 0) {
// 		$sales = getScrivenerSales($rs->fields['cScrivener']);
// 		foreach ($sales as $key => $value) { //服務對象、1:仲介、2:地政士、3特殊回饋地政士
// 			$data[$rs->fields['cCertifiedId']][] = "INSERT INTO tContractSales SET cCertifiedId = '".$rs->fields['cCertifiedId']."' AND cTarget = '3' AND cBranch = '".$rs->fields['cScrivener']."' AND cSalesId = '".$value."'";
// 		}
// 	}

// 	$rs->MoveNext();
// }

// foreach ($data as $key => $value) {
// 	echo "##".$key."<br>";
// 	$sql_search = "DELETE FROM tContractSales WHERE cCertifiedId = '".$key."'";
// 	echo $sql_search.";<br>";

// 	foreach ($value as $k => $v) {
// 		echo $v.";<br>";
// 	}

// }



// function getBranchSales($bId){
// 	global $conn;

// 	$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$bId."'";

// 	$rs = $conn->Execute($sql);
// 	while (!$rs->EOF) {
// 		$sales[] = $rs->fields['bSales'];

// 		$rs->MoveNext();
// 	}
// 	return $sales;
// }
// function getScrivenerSales($sId){
// 	global $conn;

// 	$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$sId."'";
// 	$rs = $conn->Execute($sql);
// 	while (!$rs->EOF) {
// 		$sales[] = $rs->fields['sSales'];

// 		$rs->MoveNext();
// 	}
// 	return $sales;
// }
?>