<?php
include_once '../openadodb.php' ;

$sDate = ($_POST['sDate'])?$_POST['sDate']:'110-03-01';
$eDate = ($_POST['eDate'])?$_POST['eDate']:'110-12-31';

$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" ' ; 

	

	// 搜尋條件-簽約日期
	if ($sDate) {
		$tmp = explode('-',$sDate) ;
		$sSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
		
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
	}
	if ($eDate) {
		$tmp = explode('-',$eDate) ;
		$eSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;

		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;
	}


	$sql ='
		SELECT 
			cas.cCertifiedId as cCertifiedId, 
			cas.cEscrowBankAccount as cEscrowBankAccount,
			cas.cApplyDate as cApplyDate, 
			cas.cSignDate as cSignDate, 
			cas.cFinishDate as cFinishDate,
			cas.cEndDate as cEndDate, 
			buy.cName as cBuyer, 
			own.cName as cOwner, 
			inc.cTotalMoney as cTotalMoney, 
			inc.cCertifiedMoney as cCertifiedMoney, 
			inc.cFirstMoney as cFirstMoney,
			csc.cScrivener as cScrivener, 
			CONCAT("SC",LPAD(csc.cScrivener,4,"0")) as cScrivenerCode,
			(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as cScrivenerName, 
			pro.cAddr as cBuildAddress, 
			pro.cZip as cBuildAddressZip, 
			zip.zCity as cBuildAddressCity, 
			zip.zArea as cBuildAddressArea, 
			(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as cCaseStatus,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,	
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brandname3,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand) AS brandCode,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand1) AS brandCode1,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand2) AS brandCode2,	
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand3) AS brandCode3,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			rea.cBrand2 as brand3,
			rea.cBranchNum as branch,
			rea.cBranchNum1 as branch1,
			rea.cBranchNum2 as branch2,
			rea.cBranchNum3 as branch3,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS branchName,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS branchName1,	
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS branchName2,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum3) AS branchName3,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum) as branchCategory,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum1) as branchCategory1,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum2) as branchCategory2,
			(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum) as branchCategory3
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
		WHERE
		'.$query.' 
		GROUP BY
			cas.cCertifiedId
		ORDER BY 
			cas.cApplyDate,cas.cId,cas.cSignDate ASC;
		' ;
	$rs = $conn->Execute($sql);
	$temp_data = array();
	while (!$rs->EOF) {

		array_push($temp_data, $rs->fields);

		$rs->MoveNext();
	}

	$conn->Execute("use www_first1_report;");

	// print_r($conn);
	// die;


	foreach ($temp_data as  $v) {
		$cStoreBrand = array();
		$cStoreBranch = array();
		$cStoreCode = array();
		$cStoreCompany = array();

		if ($v['branch'] > 0) {
			array_push($cStoreBrand, $v['brandname']);
			array_push($cStoreBranch, $v['branchName']);
			array_push($cStoreCode, $v['brandCode'].str_pad($v['branch'] , 5,'0',STR_PAD_LEFT));
			// array_push($cStoreCompany, $v['brandname']);
		}

		if ($v['branch1'] > 0) {
			array_push($cStoreBrand, $v['brandname1']);
			array_push($cStoreBranch, $v['branchName1']);
			array_push($cStoreCode, $v['brandCode1'].str_pad($v['branch1'] , 5,'0',STR_PAD_LEFT));
			// array_push($cStoreCompany, $v['brandname']);
		}

		if ($v['branch2'] > 0) {
			array_push($cStoreBrand, $v['brandname2']);
			array_push($cStoreBranch, $v['branchName2']);
			array_push($cStoreCode, $v['brandCode2'].str_pad($v['branch2'] , 5,'0',STR_PAD_LEFT));
			// array_push($cStoreCompany, $v['brandname']);
		}
		
		if ($v['branch3'] > 0) {
			array_push($cStoreBrand, $v['brandname3']);
			array_push($cStoreBranch, $v['branchName3']);
			array_push($cStoreCode, $v['brandCode3'].str_pad($v['branch3'] , 5,'0',STR_PAD_LEFT));
			// array_push($cStoreCompany, $v['brandname']);
		}


		$sql = "SELECT * FROM tContractCaseReport WHERE cCertifiedId = '".$v['cCertifiedId']."'";
		$rs = $conn->Execute($sql);

		if (!$rs->EOF) {
			$sql = "UPDATE tContractCaseReport 
				SET
				cSignDate = '".substr($v['cSignDate'],0,10)."',
				cEndDate = '".substr($v['cEndDate'],0,10)."',
				cBuyer = '".$v['cBuyer']."',
				cOwner = '".$v['cOwner']."',
				cTotalMoney = '".$v['cTotalMoney']."',
				cCertifiedMoney = '".$v['cCertifiedMoney']."',
				cFirstMoney = '".$v['cFirstMoney']."',
				cScrivenerName = '".$v['cScrivenerName']."',
				cScrivenerCode = '".$v['cScrivenerCode']."',
				cBuildAddressZip = '".$v['cBuildAddressZip']."',
				cBuildAddressCity = '".$v['cBuildAddressCity']."',
				cBuildAddressArea = '".$v['cBuildAddressArea']."',
				cBuildAddress = '".$v['cBuildAddress']."',
				cCaseStatus = '".$v['cCaseStatus']."',
				cStoreBrand = '".@implode(',', $cStoreBrand )."',
				cStoreBranch = '".@implode(',', $cStoreBranch )."',
				cStoreCode = '".@implode(',', $cStoreCode )."'
				WHERE
					cCertifiedId = '".$v['cCertifiedId']."',
				
				";
		}else{
			$sql = "INSERT INTO tContractCaseReport 
				SET
				cCertifiedId = '".$v['cCertifiedId']."',
				cEscrowBankAccount = '".$v['cEscrowBankAccount']."',
				cApplyDate = '".substr($v['cApplyDate'], 0,10)."',
				cSignDate = '".substr($v['cSignDate'],0,10)."',
				cEndDate = '".substr($v['cEndDate'],0,10)."',
				cBuyer = '".$v['cBuyer']."',
				cOwner = '".$v['cOwner']."',
				cTotalMoney = '".$v['cTotalMoney']."',
				cCertifiedMoney = '".$v['cCertifiedMoney']."',
				cFirstMoney = '".$v['cFirstMoney']."',
				cScrivenerName = '".$v['cScrivenerName']."',
				cScrivenerCode = '".$v['cScrivenerCode']."',
				cBuildAddressZip = '".$v['cBuildAddressZip']."',
				cBuildAddressCity = '".$v['cBuildAddressCity']."',
				cBuildAddressArea = '".$v['cBuildAddressArea']."',
				cBuildAddress = '".$v['cBuildAddress']."',
				cCaseStatus = '".$v['cCaseStatus']."',
				cStoreBrand = '".@implode(',', $cStoreBrand )."',
				cStoreBranch = '".@implode(',', $cStoreBranch )."',
				cStoreCode = '".@implode(',', $cStoreCode )."'
				";
		}

		
		// echo $sql;
		$conn->Execute($sql);
		echo $sql."\r\n";
				// print_r($conn_report);
		die;
		// print_r($v);
	}		

	// print_r($temp_data);
	die;
?>