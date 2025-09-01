<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';

include_once '../openadodb.php' ;


 $_POST = escapeStr($_POST) ;
##

// echo 'AAA';
// $_POST['sApplyDate'] = '104-10-01 00:00:00';
// $_POST['eApplyDate'] = '104-10-20 23:59:59';
// $_POST['current_page'] = 2;

##

// $sales = 25;
$sApplyDate = '108-01-01 00:00:00';
$eApplyDate = '108-12-31 23:59:59';
$query = '' ; 
$sEndDate = '';
$eEndDate ='';
$sSignDate = '';
$eSignDate = '';


##

//取得所有出款保證費紀錄("27110351738","10401810001889","20680100135997")

##

$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子
$queryO = ' AND fb.fDelete = 0 ';

// 搜尋條件-進案日期
if ($sApplyDate) {
	$tmp = explode('-',$sApplyDate) ;
	$sApplyDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate>="'.$sApplyDate.' 00:00:00" ' ;
}
if ($eApplyDate) {
	$tmp = explode('-',$eApplyDate) ;
	$eApplyDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate<="'.$eApplyDate.' 23:59:59" ' ;
}
##

##

// 搜尋條件-結案日期
if ($sEndDate) {
	$tmp = explode('-',$sEndDate) ;
	$sEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate>="'.$sEndDate.' 00:00:00" ' ;

}
if ($eEndDate) {
	$tmp = explode('-',$eEndDate) ;
	$eEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate<="'.$eEndDate.' 23:59:59" ' ;
}
##

// 搜尋條件-簽約日期
if ($sSignDate) {
	$tmp = explode('-',$sSignDate) ;
	$sSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
}
if ($eSignDate) {
	$tmp = explode('-',$eSignDate) ;
	$eSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;
}
##



if ($query) { $query = ' WHERE '.$query ; }
//LEFT JOIN
//	tBankTrans AS tra ON tra.tMemo=cas.cCertifiedId AND tra.tAccount="27110351738"
//	tra.tMoney tMoney,

$query ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate,
	cas.cEndDate as cEndDate, 
	cas.cBankList,
	buy.cName as buyer, 
	own.cName as owner, 
	inc.cTotalMoney as cTotalMoney, 
	inc.cCertifiedMoney as cCertifiedMoney, 
	csc.cScrivener as cScrivener, 
	(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener, 
	pro.cAddr as cAddr, 
	pro.cZip as cZip, 
	zip.zCity as zCity, 
	zip.zArea as zArea, 
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2,
	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
	cas.cCaseStatus as cCaseStatus,
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
// $max = $rs->RecordCount();
$list = array();
while (!$rs->EOF) {
	$list[] = $rs->fields;

	$rs->MoveNext();
}

for ($i=0; $i < count($list); $i++) { 
	if ($list[$i]['cCaseFeedBackMoney'] > 0 && $list[$i]['cCaseFeedback'] == 0) {

		if ($list[$i]['cFeedbackTarget'] == 2 || $list[$i]['branch'] == 505) { //scrivener
			$sales = getScrivenerSales($list[$i]['cScrivener']);
			foreach ($sales as $key => $value) {
				$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '".$list[$i]['cFeedbackTarget']."',
						cSalesId = '".$value['sales']."',
						cStoreId = '".$list[$i]['cScrivener']."',
						cFeedBackMoney = '".$list[$i]['cCaseFeedBackMoney']."'
					";
				$conn->Execute($sql);
				echo $sql;
			}
			
					
		}elseif ($list[$i]['cFeedbackTarget'] == 1 ) { 
			$sales = getBranchSales($list[$i]['branch']);
			foreach ($sales as $key => $value) {
				$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '".$list[$i]['cFeedbackTarget']."',
						cSalesId = '".$value['sales']."',
						cStoreId = '".$list[$i]['branch']."',
						cFeedBackMoney = '".$list[$i]['cCaseFeedBackMoney']."'
					";
				$conn->Execute($sql);
				echo $sql;
			}
			
					
		}
	}
	unset($sales);
	//2
	if ($list[$i]['cCaseFeedBackMoney1'] > 0 && $list[$i]['cCaseFeedback1'] == 0) {

		if ($list[$i]['cFeedbackTarget1'] == 2 || $list[$i]['branch1'] == 505) { //scrivener
			$sales = getScrivenerSales($list[$i]['cScrivener']);
			foreach ($sales as $key => $value) {
				$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '".$list[$i]['cFeedbackTarget1']."',
						cSalesId = '".$value['sales']."',
						cStoreId = '".$list[$i]['cScrivener']."',
						cFeedBackMoney = '".$list[$i]['cCaseFeedBackMoney1']."'
					";
				$conn->Execute($sql);
				echo $sql;
			}
			
					
		}elseif ($list[$i]['cFeedbackTarget1'] == 1 ) { 
			$sales = getBranchSales($list[$i]['branch1']);
			foreach ($sales as $key => $value) {
				$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '".$list[$i]['cFeedbackTarget1']."',
						cSalesId = '".$value['sales']."',
						cStoreId = '".$list[$i]['branch1']."',
						cFeedBackMoney = '".$list[$i]['cCaseFeedBackMoney1']."'
					";
				$conn->Execute($sql);
				echo $sql;
			}
			
		}
	}
	unset($sales);

	//3
	if ($list[$i]['cCaseFeedBackMoney2'] > 0 && $list[$i]['cCaseFeedback2'] == 0) {

		if ($list[$i]['cFeedbackTarget2'] == 2 || $list[$i]['branch2'] == 505) { //scrivener
			$sales = getScrivenerSales($list[$i]['cScrivener']);
			foreach ($sales as $key => $value) {
				$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '".$list[$i]['cFeedbackTarget2']."',
						cSalesId = '".$value['sales']."',
						cStoreId = '".$list[$i]['cScrivener']."',
						cFeedBackMoney = '".$list[$i]['cCaseFeedBackMoney2']."'
					";
				$conn->Execute($sql);
				echo $sql;
			}
			
		}elseif ($list[$i]['cFeedbackTarget2'] == 1 ) { 
			$sales = getBranchSales($list[$i]['branch2']);
			foreach ($sales as $key => $value) {
				$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '".$list[$i]['cFeedbackTarget2']."',
						cSalesId = '".$value['sales']."',
						cStoreId = '".$list[$i]['branch2']."',
						cFeedBackMoney = '".$list[$i]['cCaseFeedBackMoney2']."'
					";
				$conn->Execute($sql);
				echo $sql;
			}
			
					
		}
	}
	unset($sales);

	if ($list[$i]['cSpCaseFeedBackMoney'] > 0)  {
		$sales = getScrivenerSales($list[$i]['cScrivener']);
			$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '".$list[$i]['cFeedbackTarget2']."',
						cSalesId = '".$sales."',
						cStoreId = '".$list[$i]['cScrivener']."',
						cFeedBackMoney = '".$list[$i]['cSpCaseFeedBackMoney']."'
					";
			$conn->Execute($sql);
				echo $sql;
	}
	unset($sales);

	$sales = getOtherFeed($list[$i]['cCertifiedId']);
	
	if (is_array($sales)) {
		// print_r($sales);
		foreach ($sales as $key => $value) {
			if ($value['fType'] == 1) { //類型1地政2仲介
				$sql = "INSERT INTO
						tContractSales_2
					SET 
						cCertifiedId = '".$list[$i]['cCertifiedId']."',
						cTarget = '2',
						cSalesId = '".$value['fSales']."',
						cStoreId = '".$value['fStoreId']."',
						cFeedBackMoney = '".$value['fMoney']."'
					";
					// echo $sql;
					// die;
				$conn->Execute($sql);
			}
		}
	}
	


	
}

die;

##
//取得仲介店名
function getRealtyName($no=0) {
	global $conn;
	unset($tmp) ;
	if ($no > 0) {
		$sql = 'SELECT bStore FROM tBranch WHERE bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);

		return $rs->fields['bStore'] ;
	}
	else {
		return false ;
	}
}
##

//取得仲介店編號
function getRealtyNo($lnk,$no=0) {//找舊有的品牌(20150908)
	global $conn;
	unset($tmp) ;
	if ($no > 0) {
		$sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);
		
		return strtoupper($rs->fields['bCode']).str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT) ;
	}
	else {
		return false ;
	}
}

function dateCg($val){

	$val = substr($val, 0,10);


	// $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
		
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'/'.$tmp[1].'/'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}

function checkCat($no,$brand) {
	global $conn;
	$val = '' ;
	
	if ($no) {
		$sql = 'SELECT
					(SELECT bId FROM tBrand AS br WHERE br.bId = '.$brand.') AS bBrand,
					bCategory
					
				FROM
					tBranch
				WHERE
					bId="'.$no.'" AND bId<>"0";' ;
		
		$rs = $conn->Execute($sql);

		
		if ($rs->fields['bCategory'] == '1') {
			
			if ($rs->fields['bBrand'] == '1') {
				$val = 12 ;
			}
			else if ($rs->fields['bBrand'] == '2') {
				$val = 3 ;
			}
			else if ($rs->fields['bBrand'] == '49') {
				$val = 13 ;
			}
			else if ($rs->fields['bBrand'] == '56') {
				$val = 14 ;
			}
			else {
				$val = 11 ;
			}
		}
		else if ($rs->fields['bCategory'] == '2') {
			$val = 2;
		}
		else if ($rs->fields['bCategory'] == '3') {
			$val = 3 ;
		}
		
	}
	// echo $val."-----";
	return $val ;
}

function getSalse($cId,$bId){
	global $conn;

	$sql = "SELECT cSalesId FROM tContractSales WHERE cCertifiedId = '".$cId."' AND cBranch = '".$bId."'";

	$rs = $conn->Execute($sql);

	return $rs->fields['cSalesId'];
}

function getTransDate($id){
	global $conn;

	//取得實際出款日
		$sql = 'SELECT tBankLoansDate FROM tBankTrans WHERE tMemo="'.$id.'" AND tKind="保證費"  ORDER BY tExport_time DESC LIMIT 1;' ;
		$rs = $conn->Execute($sql);

		return $rs->fields['tBankLoansDate'];
}
function getBranchSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS Name,bSales AS sales FROM tBranchSales WHERE bBranch = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields;

		$rs->MoveNext();
	}

	return $sales;
}

function getScrivenerSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name,sSales AS sales FROM tScrivenerSales WHERE sScrivener = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields;

		$rs->MoveNext();
	}

	return $sales;
}

function getOtherFeed($id){
	global $conn;
	$sales = array();
	$sql = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='".$id."' AND fDelete = 0 AND (fType = 1 OR fType = 2) ";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$sales[] = $rs->fields;

		$rs->MoveNext();
	}

	return $sales;
}



?>