<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../web_addr.php' ;
include_once '../tracelog.php' ;
// include_once 'getBranchType.php';
// include_once '../report/getBranchType.php';
$brand = '' ;
$status = '' ;
$category = '' ;
$contract_bank = '' ;

$_POST = escapeStr($_POST) ;
$sDate = ($_POST['sDate'])?$_POST['sDate']:(date('Y')-1911)."-".date("m")."-01" ;
$eDate = ($_POST['eDate'])?$_POST['eDate']:(date('Y')-1911)."-".date("m")."-".date("t") ;
$target = ($_POST['target'])?$_POST['target']:0;
$sales = empty($_POST["sales"]) 
        ? $_SESSION['member_id']
        : $_POST["sales"];



##業務選單##
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(4,7) AND pJob = 1 ";
$menuSales= array();
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuSales[$rs->fields['pId']] = $rs->fields['pName'];
	$rs->MoveNext();
}

##

##
if ($_POST['ok'] == 'ok') {

	//統計目標
	$targetData = array();
	if ($target == 0) { //業務
		foreach ($menuSales as $k => $v) {
			$targetData[$k]['name'] = $v;
			$targetData[$k]['count'] = 0;//未收足
			$targetData[$k]['countTotal'] = 0;//總數
		}
	}elseif ($target == 1) { //地政士
		$sql = "SELECT sId,sName FROM tScrivener WHERE sStatus = 1";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$targetData[$rs->fields['sId']]['name'] = $rs->fields['sName'];
			$targetData[$rs->fields['sId']]['count'] = 0;//未收足
			$targetData[$rs->fields['sId']]['countTotal'] = 0;//總數
			$rs->MoveNext();
		}

	}elseif ($target == 2) { //仲介
		$sql = "SELECT bId,bStore,(SELECT bName FROM tBrand WHERE bId =bBrand) As brandName FROM tBranch WHERE bStatus = 1";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$targetData[$rs->fields['bId']]['name'] = $rs->fields['brandName'].$rs->fields['bStore'];
			$targetData[$rs->fields['bId']]['count'] = 0;//未收足
			$targetData[$rs->fields['bId']]['countTotal'] = 0;//總數

			$rs->MoveNext();
		}
	}
	
	// print_r($targetData);
	##

	$query = ' cc.cCertifiedId<>"" AND cc.cCaseStatus<>"8" AND cc.cCertifiedId !="005030342" AND inc.cCertifiedMoney > 0 AND cCaseStatus = 2' ;
	// 搜尋條件-日期

	if ($sDate) {
		$tmp = explode('-',$sDate) ;

		if ($query) { $query .= " AND " ;}
		$query .= ' cc.cSignDate >="'.($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2].' 00:00:00" ' ;
		unset($tmp) ;		
	}
	if ($eDate) {
		$tmp = explode('-',$eDate) ;
		
		if ($query) { $query .= " AND " ; }
		$query .= ' cc.cSignDate <="'.($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2].' 23:59:59" ' ;
		unset($tmp) ;
	
	}

	if ($query) { $query = ' WHERE '.$query ; }

	$sql ='
	SELECT 
		cc.cSignDate,
		cc.cCertifiedId AS cCertifiedId,
		inc.cCertifiedMoney as cCertifiedMoney,
		inc.cFirstMoney as cFirstMoney,
		(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS BranchName,
		(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS BranchName1,
		(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS BranchName2,	
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
		(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS BrandName,
		(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS BrandName1,
		(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS BrandName2,
		cs.cScrivener,
		(SELECT sName FROM tScrivener WHERE sId = cs.cScrivener) AS sName,
		(SELECT sOffice FROM tScrivener WHERE sId = cs.cScrivener) AS sOffice,
		(SELECT sFeedDateCat FROM tScrivener WHERE sId = cs.cScrivener) AS sFeedDateCat,
		cc.cCaseFeedBackModifier,
		buy.cName AS buyer,
		own.cName AS owner,
		inc.cTotalMoney,
		inc.cInspetor,
		inc.cInspetor2,
		(SELECT pName FROM tPeopleInfo WHERE pId = inc.cInspetor) AS cInspetorName,
		(SELECT pName FROM tPeopleInfo WHERE pId = inc.cInspetor2) AS cInspetorName2,
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

	$rs = $conn->Execute($sql);
	$list = array();
	while (!$rs->EOF) {
		array_push($list, $rs->fields);

		$rs->MoveNext();
	}
	unset($query);
	$data = array(); $check = array();
	foreach ($list as $v) {
		$checkMoney = round(($v['cTotalMoney']-$v['cFirstMoney'])*0.0006);
		
		if ($target == 0) { //業務
			$search = array();
			$caseSales = array();
			if ($v['cBranchNum'] > 0 && $v['cBranchNum'] != 505) {
				array_push($search, $v['cBranchNum']);
			}

			if ($v['cBranchNum1'] > 0 && $v['cBranchNum1'] != 505) {
				array_push($search, $v['cBranchNum1']);
			}

			if ($v['cBranchNum2'] > 0 && $v['cBranchNum2'] != 505) {
				array_push($search, $v['cBranchNum2']);
			}

			if ($v['cBranchNum3'] > 0 && $v['cBranchNum3'] != 505) {
				array_push($search, $v['cBranchNum3']);
			}
			//仲介業務
			if (!empty($search)) {
				$sql = "SELECT bSales FROM tBranchSales WHERE bBranch IN(".@implode(',', $search).")";
				$rs = $conn->Execute($sql);
				while (!$rs->EOF) {
						
					if (!in_array($rs->fields['bSales'], $caseSales)) {
						array_push($caseSales, $rs->fields['bSales']);		
					}

					$rs->MoveNext();
				}
			}

			//地政士業務
			$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$v['cScrivener']."' ";
			$rs = $conn->Execute($sql);
			while (!$rs->EOF) {
				if (!in_array($rs->fields['sSales'], $caseSales)) {
					array_push($caseSales, $rs->fields['sSales']);
						
				}

				$rs->MoveNext();
			}
		
			foreach ($caseSales as $value) {
				if ($checkMoney > ($v['cCertifiedMoney']+10)) { //未收足
					 $targetData[$value]['count']++;
				}

				$targetData[$value]['countTotal']++;
					
				$check[$v['cCertifiedId']][] = $value;

			}
			unset($search);unset($caseSales);
			
		}elseif ($target == 1) { //地政士
			if ($checkMoney > ($v['cCertifiedMoney']+10)) { //未收足
				 $targetData[$v['cScrivener']]['count']++;
				 $check[$v['cCertifiedId']][] = $v['cScrivener'];
			}
			$targetData[$v['cScrivener']]['countTotal']++;
		}elseif ($target == 2) {
			if ($v['cBranchNum'] > 0 && $v['cBranchNum'] != 505) {
				if ($checkMoney > ($v['cCertifiedMoney']+10)) { //未收足
					 $targetData[$v['cBranchNum']]['count']++;


				}

				$targetData[$v['cBranchNum']]['countTotal']++;


			}

			if ($v['cBranchNum1'] > 0 && $v['cBranchNum1'] != 505) {
				if ($checkMoney > ($v['cCertifiedMoney']+10)) { //未收足
					 $targetData[$v['cBranchNum1']]['count']++;
				}

				$targetData[$v['cBranchNum1']]['countTotal']++;
			}

			if ($v['cBranchNum2'] > 0 && $v['cBranchNum2'] != 505) {
				// array_push($search, $v['cBranchNum2']);
				if ($checkMoney > ($v['cCertifiedMoney']+10)) { //未收足
					 $targetData[$v['cBranchNum2']]['count']++;
				}

				$targetData[$v['cBranchNum2']]['countTotal']++;
			}

			if ($v['cBranchNum3'] > 0 && $v['cBranchNum3'] != 505) {
				if ($checkMoney > ($v['cCertifiedMoney']+10)) { //未收足
					 $targetData[$v['cBranchNum3']]['count']++;
				}

				$targetData[$v['cBranchNum3']]['countTotal']++;

			}
		}
			
			
	}

	foreach ($targetData as $key => $value) {
		if ($value['countTotal'] > 0) {
			$targetData[$key]['ratio'] = round($value['count']/$value['countTotal'],2)*100;
		}
		
	}
	

}

##
$smarty->assign('target',$target);
$smarty->assign('list',$list);
$smarty->assign('menuSales',$menuSales);
$smarty->assign('sDate',$sDate);
$smarty->assign('eDate',$eDate);
$smarty->assign('targetData',$targetData);
$smarty->assign('menuTarget',array(0=>'業務',1=>'地政士',2=>'仲介'));
$smarty->display('certifiedFeeAnalysis.inc.tpl', '', 'report') ;
?> 
