<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;

$sEndDate = '2020-02-01 00:00:00' ;
$eEndDate = '2020-02-19 00:00:00' ;


$query = ' cc.cCertifiedId<>"" AND cc.cCaseStatus<>"8" AND cc.cCertifiedId !="005030342"' ;
$query .= ' AND cSignDate >= "'.$sEndDate.'" AND cSignDate <= "'.$eEndDate.'"';

if ($query) { $query = ' WHERE '.$query ; }

$query ='
SELECT 
	cc.cCertifiedId AS cCertifiedId,
	cc.cSignDate,
	cc.cEndDate,
	inc.cCertifiedMoney as cCertifiedMoney,
	inc.cFirstMoney as cFirstMoney,
	(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS BranchName,
	(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS BranchName1,
	(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS BranchName2,	
	(SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum) AS BranchGroup,
	(SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum1) AS BranchGroup1,
	(SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum2) AS BranchGroup2,	
	(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS BrandName,
	(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS BrandName1,
	(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS BrandName2,
	(SELECT bCode FROM tBrand WHERE bId = cr.cBrand) AS bCode,
	(SELECT bCode FROM tBrand WHERE bId = cr.cBrand1) AS bCode1,
	(SELECT bCode FROM tBrand WHERE bId = cr.cBrand2) AS bCode2,	
	cr.cBrand,
	cr.cBrand1,
	cr.cBrand2,
	cr.cBranchNum,
	cr.cBranchNum1,
	cr.cBranchNum2,
	(SELECT bFeedDateCat FROM tBranch WHERE bId=cr.cBranchNum)  AS bFeedDateCat,
    (SELECT bFeedDateCat FROM tBranch WHERE bId=cr.cBranchNum1)  AS bFeedDateCat1,
    (SELECT bFeedDateCat FROM tBranch WHERE bId=cr.cBranchNum2)  AS bFeedDateCat2,
    (SELECT bCategory FROM tBranch WHERE bId=cr.cBranchNum) category,
	(SELECT bCategory FROM tBranch WHERE bId=cr.cBranchNum1) category1,
	(SELECT bCategory FROM tBranch WHERE bId=cr.cBranchNum2) category2,	
	cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
	cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
	cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
	cc.cSpCaseFeedBackMoney AS ScrivenerSPFeedMoney,
	cc.cSpCaseFeedBackMoneyMark AS cSpCaseFeedBackMoneyMark,
	cc.cCaseFeedback AS cCaseFeedback,
	cc.cCaseFeedback1 AS cCaseFeedback1,
	cc.cCaseFeedback2 AS cCaseFeedback2,
	cc.cFeedbackTarget AS cFeedbackTarget,
	cc.cFeedbackTarget1 AS cFeedbackTarget1,
	cc.cFeedbackTarget2 AS cFeedbackTarget2,
	cc.cBranchScrRecall,
	cc.cBranchScrRecall1,
	cc.cBranchScrRecall2,
	cc.cBrandScrRecall,
	cc.cBrandScrRecall1,
	cc.cBrandScrRecall2,
	cc.cScrivenerSpRecall,	
	cs.cScrivener,
	(SELECT sName FROM tScrivener WHERE sId = cs.cScrivener) AS sName,
	(SELECT sOffice FROM tScrivener WHERE sId = cs.cScrivener) AS sOffice,
	(SELECT sFeedDateCat FROM tScrivener WHERE sId = cs.cScrivener) AS sFeedDateCat,
	(SELECT sCategory FROM tScrivener WHERE sId=cs.cScrivener) as scrivenerCategory,
	cc.cCaseFeedBackModifier,
	buy.cName AS buyer,
	own.cName AS owner,
	inc.cTotalMoney,
	cc.cCaseStatus,
	(SELECT sName FROM tStatusCase AS sc WHERE sc.sId=cc.cCaseStatus) AS status
FROM 
	tContractCase AS cc 
LEFT JOIN 
	tContractBuyer AS buy ON buy.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractOwner AS own ON own.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId 
LEFT JOIN 
	tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractProperty AS pro ON pro.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractIncome AS inc ON inc.cCertifiedId=cc.cCertifiedId 
LEFT JOIN
	tZipArea AS zip ON zip.zZip=pro.cZip
LEFT JOIN 
	tScrivener AS scr ON scr.sId = cs.cScrivener
'.$query.' 
GROUP BY
	cc.cCertifiedId
ORDER BY 
	cc.cApplyDate,cc.cId,cc.cSignDate ASC;
' ;
// echo $query."\r\n";
$cCertifiedId =array();
$rs = $conn->Execute($query);
$i = 0; $j= 0;
while (!$rs->EOF) {

	$cCertifiedId[] = $rs->fields['cCertifiedId'];

	unset($arr);

	$rs->MoveNext();
}

foreach ($cCertifiedId as $k => $v) {
	$sql = "SELECT * FROM tContractSales WHERE cSalesId = '47' AND cCertifiedId = '".$v."'";

	$rs = $conn->Execute($sql);

	if (!$rs->EOF) {
		$sql = "UPDATE tContractSales SET cSalesId = '3'  WHERE cSalesId = '47' AND cCertifiedId = '".$v."'";

		echo $sql.";<br>";
	}

	
}

// print_r($cCertifiedId);



######################################################

?>
