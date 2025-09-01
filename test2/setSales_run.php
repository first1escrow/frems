<?php
include_once '/var/www/html/first.twhg.com.tw/openadodb.php';

$year = date('Y');
$month = date('m');

if ($month > 1 && $month <= 3) {
	$sEndDate = $year."-01-01 00:00:00";
	$eEndDate = $year."-03-31 23:59:59";
}elseif ($month > 4 && $month <= 6) {
	$sEndDate = $year."-04-01 00:00:00";
	$eEndDate = $year."-06-30 23:59:59";
}elseif ($month > 7 && $month <= 9) {
	$sEndDate = $year."-07-01 00:00:00";
	$eEndDate = $year."-09-31 23:59:59";
}elseif ($month > 10 && $month <=12) {
	$sEndDate = $year."-10-01 00:00:00";
	$eEndDate = $year."-12-31 23:59:59";
}

// $sEndDate = $year."-06-01 00:00:00";
// $eEndDate = $year."-06-30 23:59:59";

$spCertifiedId = "'090029217','080100049','090046849','080038496'";

//取得合約銀行帳號
$_sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" GROUP BY cBankAccount ORDER BY cId ASC;' ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$conBank[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}

$conBank_sql = implode('","',$conBank) ;
##

$contractDate = '' ;

$_sql = '
	SELECT 
		tra.tMemo as cCertifiedId
	FROM
		tBankTrans AS tra
	JOIN
		tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=tra.tMemo
	WHERE
		
		tra.tAccount IN ("'.$conBank_sql.'")
		AND tra.tKind = "保證費"
		AND (tra.tBankLoansDate>="'.$sEndDate.'" AND tra.tBankLoansDate<="'.$eEndDate.'")
		AND tra.tMemo NOT IN('.$spCertifiedId.')
	GROUP BY
		tra.tMemo
	ORDER BY
		tra.tExport_time
	ASC ;
' ;

$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	if ($rs->fields['cCertifiedId'] != '090046849') {
		$cid_arr[] = $rs->fields['cCertifiedId'] ;
	}
	

	$rs->MoveNext();
}

//取出範圍內未收履保費但仍要回饋(有利息)的案件
if ($contractDate) {
	$_sql = 'SELECT cCertifiedId FROM tContractCase WHERE cBankList>="'.$sEndDate.'" AND cBankList<="'.$eEndDate.'" AND cCertifiedId NOT IN('.$spCertifiedId.')' ;
	$rs = $conn->Execute($_sql);
	while (!$rs->EOF) {
		if ($rs->fields['cCertifiedId'] != '090046849') {
			$cid_arr[] = $rs->fields['cCertifiedId'] ;
		}

		$rs->MoveNext();
	}
}
// $cId_str= implode('","',$cid_arr) ;

// unset($cid_arr);

// $cid_arr[] = '090284523';
// $cid_arr[] = '090149840';
// $cid_arr[]='090029088';
// $cid_arr[]='090038894';
// $cid_arr[]='090343740';
// $cid_arr[]='090343740';
// $cid_arr[]='090168328';
// $cid_arr[]='090012035';
// $cid_arr[]='090000070';
// $cid_arr[]='090469686';
// $cid_arr[]='090469584';
// $cid_arr[]='080006549';
// $cid_arr[]='080006549';
// $cid_arr[]='090024315';
// $cid_arr[]='090160810';
// $cid_arr[]='081531574';


##
$query = ' cc.cCertifiedId<>"" AND cc.cCaseStatus<>"8" AND cc.cCertifiedId !="005030342"' ;
$query .= 'AND cc.cCertifiedId IN("'.@implode('","', $cid_arr).'")';

// $query = ' AND cc.cCertifiedId IN ()';




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
$rs = $conn->Execute($query);

while (!$rs->EOF) {
	$list[] = $rs->fields;


	$rs->MoveNext();
}

foreach ($list as $k => $v) {

	if ($v['cSignDate'] < '2020-01-01 00:00:00') { //只計算109年
		continue;
	}

	echo "######".$v['cCertifiedId']."######\r\n";
	$sql = "DELETE FROM tContractSales WHERE cCertifiedId = '".$v['cCertifiedId']."'";
	$conn->Execute($sql);
	

	$sales = array();
	if ($v['cBranchNum'] > 0) {
			echo $v['cSignDate']."\r\n";
		$sales = array_merge($sales,Sales($v['cCertifiedId'],$v['cBranchNum'],$v['cScrivener'],$v['cFeedbackTarget'],$v['cSignDate']));
			
	}	

	if ($v['cBranchNum1'] > 0) {
			
		$sales = array_merge($sales,Sales($v['cCertifiedId'],$v['cBranchNum1'],$v['cScrivener'],$v['cFeedbackTarget1'],$v['cSignDate']));
			
	}

	if ($v['cBranchNum2'] > 0) {
			
		$sales = array_merge($sales,Sales($v['cCertifiedId'],$v['cBranchNum2'],$v['cScrivener'],$v['cFeedbackTarget2'],$v['cSignDate']));
			
	}


	if ($v['ScrivenerSPFeedMoney'] > 0) {
		
		$sales = array_merge($sales,Sales($v['cCertifiedId'],'',$v['cScrivener'],3,$v['cSignDate']));

		
	}



	if (is_array($sales)) {
		# code...
		foreach ($sales as $key => $val) {
			$sql = " INSERT INTO `tContractSales` 
			            (`cId`,
			             `cCertifiedId`,
			             `cTarget`,
			             `cSalesId`,  
			             `cBranch`
			             ) VALUES (
			             null,
			             '".$v['cCertifiedId']."',
			             '".$val['cFeedbackTarget']."',
			             '".$val['Sales']."',            
			             '".$val['branch']."'
			              );";
			if ($v['cCertifiedId'] != '090046849') {
				$conn->Execute($sql);
			}
			
			echo $sql."\r\n";
		}
		
	}

	// echo $v['cCertifiedId']."\r\n";

	// print_r($sales);
}
function Sales($id,$branch,$scrivener,$cFeedbackTarget,$date)
{
	global $conn;

	
	if($branch ==505 || $cFeedbackTarget == 2 || $cFeedbackTarget == 3){ //給代書
		$type  = 1;	
		// echo $branch;
		// die;
		if ($cFeedbackTarget == 3) {
			$store = $scrivener;
			$storeSA = $scrivener;

			 $scr= 1;
		}elseif($branch ==505){
			$store = $branch;
			$storeSA = $scrivener;

		}else{
			//回饋代書
			$store = $scrivener;
			$storeSA = $scrivener;

		}
		// echo 'QQQQ';
		
	}else{
		$type  = 2;		
		$store = $branch;
		$storeSA = $branch;
	}

	$sql = "SELECT sDate FROM tSalesRegionalAttribution WHERE sDelete = 0 AND sType = '".$type."' AND sStoreId = '".$storeSA."' AND sDate <= '".$date."' ORDER BY sDate DESC LIMIT 1"; //取得最接近的一筆
	// echo $sql."\r\n";
	$rs = $conn->Execute($sql);


	if (!$rs->EOF) {
		$date = $rs->fields['sDate'];
		$sql = "SELECT * FROM tSalesRegionalAttribution WHERE sDelete = 0 AND sType = '".$type."' AND sStoreId = '".$storeSA."' AND sDate = '".$date."'";
		$rs = $conn->Execute($sql);
		$i = 0;
		while (!$rs->EOF) {
			$sales[$i]['cFeedbackTarget'] = $cFeedbackTarget;
			$sales[$i]['branch'] = $store;
			$sales[$i]['Sales'] = $rs->fields['sSales'];
			

			$i++;
			$rs->MoveNext();
		}

	}

	

	

	if (is_array($sales)) {
		return $sales;
	}else{
		if($type == 1){
			

			//地政士業務

					$sql='SELECT
							a.sId,
							a.sSales AS Sales,
							(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
							b.sOffice
						FROM
							tScrivenerSales AS a,
							tScrivener AS b
						WHERE
							a.sScrivener='.$scrivener.' AND
							b.sId=a.sScrivener
						ORDER BY
							sId
						ASC';
					
		}else{

					$sql='SELECT
								a.bId,
								a.bSales AS Sales,
								(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
								b.bName,
								b.bStore
							FROM
								tBranchSales AS a,
								tBranch AS b
							WHERE
								bBranch='.$branch.' AND
								b.bId=a.bBranch 

							ORDER BY
								bId
							ASC';
							
		}
		$rs = $conn->Execute($sql) ;
				$i = 0;
		while (!$rs->EOF) {
			// $sales = $rs->fields['Sales'];
			$list[$i]['Sales'] = $rs->fields['Sales'];
			$list[$i]['cFeedbackTarget'] = $cFeedbackTarget;
			$list[$i]['branch'] = $store;


			$i++;
			$rs->MoveNext() ;
		}

		return $list;

		
	}

		
	

		
}
die;
// $sql = "SELECT
// 		 cr.cBranchNum,
// 		 cr.cBranchNum1,
// 		 cr.cBranchNum2,
// 		 cc.cFeedbackTarget,
// 		 cc.cFeedbackTarget1,
// 		 cc.cFeedbackTarget2,
// 		 cc.cSpCaseFeedBackMoney,
// 		 cc.cSignDate,
// 		 (SELECT cScrivener FROM tContractScrivener AS cs WHERE cs.cCertifiedId=cr.cCertifyId) AS cScrivener,
		 
// 		 cc.cCertifiedId
// 	  FROM 
// 	  	tContractRealestate AS cr
// 	  LEFT JOIN
// 	  	tContractCase AS cc ON cc.cCertifiedId=cr.cCertifyId
	  
// 	  WHERE
// 	  	cc.cEndDate >='2020-05-01 00:00:00'";

// $rs = $conn->Execute($sql);

// while (!$rs->EOF) {
	
	
// 		$list[] = $rs->fields;
	


// 	$rs->MoveNext();
// }


// $fw = fopen('/var/www/html/first.twhg.com.tw/test2/log/setSales_run.log', 'a+');

// foreach ($list as $k => $v) {

// 	##檢查##
	
// 	$sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$v['cCertifiedId']."'";
// 	$rs = $conn->Execute($sql);
// 	$total = $rs->RecordCount();
// 	$sales = array();
// 	// if ($check != $total) {
		
// 		if ($v['cBranchNum'] > 0) {
// 			// $sales[] = Sales($id,$v['cBranchNum'],$v['cFeedbackTarget'],$v['cScrivener']);
// 			// $sales[] = Sales($v['cCertifiedId'],$v['cBranchNum'],$v['cScrivener'],$v['cFeedbackTarget'],$v['cSignDate']);

// 			// array_merge($sales, Sales($v['cCertifiedId'],$v['cBranchNum'],$v['cScrivener'],$v['cFeedbackTarget'],$v['cSignDate']));
// 			$sales = array_merge($sales,Sales($v['cCertifiedId'],$v['cBranchNum'],$v['cScrivener'],$v['cFeedbackTarget'],$v['cSignDate']));
			
// 		}		

// 		if ($v['cBranchNum1'] > 0) {
// 			$sales = array_merge($sales,Sales($v['cCertifiedId'],$v['cBranchNum1'],$v['cScrivener'],$v['cFeedbackTarget1'],$v['cSignDate']));
// 		}

// 		if ($v['cBranchNum2'] > 0) {
// 			$sales = array_merge($sales,Sales($v['cCertifiedId'],$v['cBranchNum2'],$v['cScrivener'],$v['cFeedbackTarget2'],$v['cSignDate']));
// 		}

// 		if ($v['cSpCaseFeedBackMoney'] > 0) {
// 			// $sales[] = Sales($v['cCertifiedId'],$v['cBranchNum2'],$v['cFeedbackTarget2'],$v['cScrivener']);

// 			$sales = array_merge($sales,Sales($v['cCertifiedId'],'',$v['cScrivener'],3,$v['cSignDate']));

		
// 		}

		

// 		if ($total != count($sales)) {
			
			
// 				if (is_array($sales)) {
// 					foreach ($sales as $key => $val) {
						
// 						$sql = " INSERT INTO `tContractSales` 
// 			            (`cId`,
// 			             `cCertifiedId`,
// 			             `cTarget`,
// 			             `cSalesId`,  
// 			             `cBranch`
// 			             ) VALUES (
// 			             null,
// 			             '".$v['cCertifiedId']."',
// 			             '".$val['cFeedbackTarget']."',
// 			             '".$val['Sales']."',            
// 			             '".$val['branch']."'
// 			              );";
// 						echo $sql."<br>";
// 						fwrite($fw, $sql."\r\n");
// 			              $conn->Execute($sql);
// 						// $contract->AddContract_Sales($id,$v['cFeedbackTarget'],$v['Sales'],$v['branch']);
							
								
// 					}
// 				}
				
			
// 		}
		

// 	// }

// 	##

	
// 	unset($sales);
// }

// fclose($fw);



?>