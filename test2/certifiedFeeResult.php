<?php

$query = '' ; 
$functions = '' ;


if ($_SESSION['member_test'] != 0) {
			$sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
		    $rs = $conn->Execute($sql);
		    while (!$rs->EOF) {
		       $tmpZip[] = "'".$rs->fields['zZip']."'";

		       $rs->MoveNext();
		    }
		    $branchCheck = array();
		    $sql = "SELECT bId FROM tBranch WHERE bZip IN(".@implode(',', $tmpZip).")";
		  
		   	$rs = $conn->Execute($sql);
		    while (!$rs->EOF) {
		    	array_push($branchCheck, $rs->fields['bId']);

		    	$rs->MoveNext();
		    }

		    $scrivenerCheck = array();
		    $sql = "SELECT sId FROM tScrivener WHERE sCpZip1  IN(".@implode(',', $tmpZip).")" ;

		    $rs = $conn->Execute($sql);
		    while (!$rs->EOF) {
		    	array_push($scrivenerCheck, $rs->fields['sId']);

		    	$rs->MoveNext();
		    }
		    unset($tmpZip);
}


$query = ' cc.cCertifiedId<>"" AND cc.cCaseStatus<>"8" AND cc.cCertifiedId !="005030342" AND inc.cCertifiedMoney > 0 AND cCaseStatus = 2' ;

if ($cCertifiedId) {
	if ($query) { $query .= " AND " ;}
	$query .= 'cc.cCertifiedId ="'.$cCertifiedId.'"';
}

// 搜尋條件-日期

if ($cat == 'sign') {
	$dateCat = 'cSignDate';
}elseif ($cat == 'end') {
	$dateCat = 'cEndDate';
}

if ($sDate) {
	$tmp = explode('-',$sDate) ;
	$sDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
		
	if ($query) { $query .= " AND " ;}
	$query .= ' cc.'.$dateCat.'>="'.$sDate.' 00:00:00" ' ;
	
				
}
if ($eDate) {
	$tmp = explode('-',$eDate) ;
	$eDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cc.'.$dateCat.'<="'.$eDate.' 23:59:59" ' ;
		
}
unset($dateCat);

//審核狀態
if ($review == 1 ) {
	$query .= " AND (inc.cInspetor = 0 AND inc.cInspetor2 = 0)";
}elseif ($review == 2) {
	// $check = true;
	$query .= " AND (inc.cInspetor > 0 AND inc.cInspetor2 = 0)";
}elseif ($review == 3) {
	// $check = true;
	$query .= " AND (inc.cInspetor > 0 AND inc.cInspetor2 > 0)";

}else{
	// if ($_SESSION['member_pDep'] == 7) {
	// 	$query .= "AND (inc.cInspetor2 = 0  )";
	// }
}

##



if ($query) { $query = ' WHERE '.$query ; }

$query ='
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
// if ($_SESSION['member_id'] == 6) {
// 	print_r($query);
// 	die;
// }

// 
$rs = $conn->Execute($query);
$i = 0; 

while (!$rs->EOF) {
	if (checkSales($rs->fields,$sales,$conn)) {
		
		$checkMoney = round(($rs->fields['cTotalMoney']-$rs->fields['cFirstMoney'])*0.0006);
		if ($checkMoney > ($rs->fields['cCertifiedMoney']+10)) {
			// echo $checkMoney .">".($rs->fields['cCertifiedMoney']+10)."_".$rs->fields['cCertifiedId']."<br>";
			$check = true;
			// $check = false;
			
			$note = getNote($rs->fields['cCertifiedId']);

			// //沒有原因的不顯示
			// if (!$note) {
			// 	$check = false;
			// }


			if ($check) {
				$arr[$i] = $rs->fields;
				$arr[$i]['cSignDate'] = substr($arr[$i]['cSignDate'], 0,10);

				$arr[$i]['show'] = 1; //可修改

				if ($rs->fields['cInspetor'] > 0 && $rs->fields['cInspetor2'] == 0) { //業務審核通過 限 主管觀看
					if ($_SESSION['member_pDep'] == 7) {
						$arr[$i]['show'] = 2;
					}


				}elseif ($rs->fields['cInspetor'] > 0 && $rs->fields['cInspetor2'] > 0) { //主管審核通過 禁止修改
					$arr[$i]['show'] = 3;
				}


				$showbrand = $arr[$i]['BrandName'];
				$showBranch = $arr[$i]['BranchName'];
				if ($arr[$i]['cBrand1'] > 0) {


					// $showbrand = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$showbrand;
					$showbrand = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$showbrand."<br>";
					$showbrand .= '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$arr[$i]['BrandName1']."<br>";

					$showBranch = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$showBranch."<br>";
					$showBranch .= '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$arr[$i]['BranchName1']."<br>";
				}

				if ($arr[$i]['cBrand2'] > 0) {
					$showbrand .= '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$arr[$i]['BrandName2'];
					$showBranch .= '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$arr[$i]['BranchName2'];
				}
				$arr[$i]['showBrand'] = $showbrand;
				$arr[$i]['showBranch'] = $showBranch;

				if (($i%2) == 0) {
					$arr[$i]['color'] = "#F8ECE9";
				}else{
					$arr[$i]['color'] = "white";
				}

				$arr[$i]['cNote'] = getNote($rs->fields['cCertifiedId']);
				$i++;
			}

			
		}
	}
	

	$rs->MoveNext();
}



unset($list);
$list = $arr;

######################################################
function getNote($cId){
	global $conn;
	$sql = "SELECT cNote FROM tContractNote WHERE cCategory = 5 AND cCertifiedId='".$cId."' ORDER BY cCreatTime DESC LIMIT 1";
	$rs = $conn->Execute($sql);

	return $rs->fields['cNote'];
}
function checkSales($arr,$pId,$conn){

	global $branchCheck;
	global $scrivenerCheck;

	$sql = "SELECT * FROM tPeopleInfo WHERE pDep !=7 AND pId ='".$pId."'";

	$rs = $conn->Execute($sql);

	$max = $rs->RecordCount();

	if ($max > 0) {return true;}
	
	// if ($_SESSION['member_test'] == 1 || $_SESSION['member_test'] == 2 || $_SESSION['member_test'] == 3) {
	// 	return true;
	// }
	
	
		$sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$arr['cCertifiedId']."' AND cSalesId = '".$pId."'";
		// echo $sql;
		$rs2 = $conn->Execute($sql);
			
		if ($rs2->fields['cSalesId']) {
			$max = 1;
		}	

	$tmp = getOtherFeed3($arr['cCertifiedId']);

	for ($i=0; $i < count($tmp); $i++) { 
		$sales[] = $tmp['salesId'];
	}
	
	

	if ($max > 0) { // 
		return true;
	}else{
		
		if ($_SESSION['member_test'] != 0) {
			// $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
		 //    $rs = $conn->Execute($sql);
		 //    while (!$rs->EOF) {
		 //       $tmpZip[] = "'".$rs->fields['zZip']."'";

		 //       $rs->MoveNext();
		 //    }
		 //    $branch = array();
		 //    $sql = "SELECT bId FROM tBranch WHERE bZip IN(".@implode(',', $tmpZip).")";

		 //   	$rs = $conn->Execute($sql);
		 //    while (!$rs->EOF) {
		 //    	array_push($branch, $rs->fields['bId']);

		 //    	$rs->MoveNext();
		 //    }

		 //    $scrivener = array();
		 //    $sql = "SELECT sId FROM tScrivener WHERE sCpZip1  IN(".@implode(',', $tmpZip).")" ;

		 //    $rs = $conn->Execute($sql);
		 //    while (!$rs->EOF) {
		 //    	array_push($scrivener, $rs->fields['sId']);

		 //    	$rs->MoveNext();
		 //    }
		 //    unset($tmpZip);

		    if (in_array($arr['cBranchNum'], $branchCheck)) {

		    	// echo $arr['cCertifiedId'];
		    	return true;
		    }

		    if (in_array($arr['cBranchNum1'], $branchCheck)) {
		    	return true;
		    }

		    if (in_array($arr['cBranchNum2'], $branchCheck)) {
		    	return true;
		    }


		    if (in_array($arr['cScrivener'], $scrivenerCheck)) {
		    	return true;
		    }


		}

		if (is_array($sales)) {
			if (in_array($pId, $sales)) {
				return true;
			}
		}
		return false;
		
		
	}

}

?>
