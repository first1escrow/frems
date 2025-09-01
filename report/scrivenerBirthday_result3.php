<?php

include_once 'class/intolog.php' ;
include_once '../tracelog.php' ;

include_once '../report/getBranchType.php';
include_once '../includes/maintain/feedBackData.php';
include_once '../report/getBranchType.php';


$tlog = new TraceLog() ;

//預載log物件
$logs = new Intolog() ;
##
$_POST = escapeStr($_POST) ;
##

$xls = trim($_POST['xls']) ;

$scrivener = $_POST['scrivener'] ;
$year = $_POST['year']+1911;
$month = $_POST['month'];

$exceptbId = array(632, 575,552,620,411,224) ;//地政士(排除奇怪的:632=業務專用 575=陳政祺 552=王泰翔 620=吳效承 411=吳),224

$str = "s.sStatus = 1";
if ($scrivener) {
	if ($str) { $str .= " AND " ; }
	$str .= ' s.sId="'.$scrivener.'" ' ;
}

if ($_SESSION['member_pDep'] == 7) {
	if ($str) { $str .= " AND " ; }
	$str = " ss.sSales ='".$_SESSION['member_id']."'";
}


if ($month) {
	if ($str) { $str .= " AND " ; }
	// $query .= ' s.sBirthday ="'.$scrivener.'" ' ;
	$str .= " MONTH(s.sBirthday) = '".$month."'";
	
}




$sql = "SELECT
			s.sId,
			s.sBirthday,
			s.sName,
			s.sOffice,
			CONCAT('SC',LPAD(s.sId,4,'0')) as Code
		FROM
			tScrivener AS s 
		LEFT JOIN
			tScrivenerSales AS ss ON ss.sScrivener = s.sId
		WHERE
			
			".$str."
			AND s.sId NOT IN (".implode(',',$exceptbId).")
			GROUP BY s.sId
			ORDER BY MONTH(s.sBirthday) ASC,Day(s.sBirthday) ASC
			" ;
// echo $sql;
$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$scrivnerArr[$i] = $rs->fields;
	$tmp = getLevelData($rs->fields['sId'],$year);

	$scrivnerArr[$i]['certifiedMoney'] = $tmp['sCertifiedMoney'];
	$scrivnerArr[$i]['caseFeedBackMoney'] = $tmp['sFeedBackMoney'];
	$scrivnerArr[$i]['income'] = $scrivnerArr[$i]['certifiedMoney']-$scrivnerArr[$i]['caseFeedBackMoney'];
	$scrivnerArr[$i]['sBirthday'] = dateCg($scrivnerArr[$i]['sBirthday']);
	$scrivnerArr[$i]['sLevel'] = $tmp['sLevel'];

	unset($tmp);
	$i++;
	$rs->MoveNext();
}
unset($tmp);
$max = count($scrivnerArr);
for ($i = 0 ; $i < $max ; $i ++) {
	for ($j = 0 ; $j < $max - 1 ; $j ++) {
		if ($scrivnerArr[$j]['income'] > $scrivnerArr[$j+1]['income']) {
			$tmp = $scrivnerArr[$j] ;
			$scrivnerArr[$j] = $scrivnerArr[$j+1] ;
			$scrivnerArr[$j+1] = $tmp ;
			unset($tmp) ;
		}
	}
}

krsort($scrivnerArr);

foreach ($scrivnerArr as $k => $v) {
	if ($v['sLevel'] == 1) {

			$Datalevel[1]['data'][] = $v;
		}elseif ($v['sLevel'] == 2) {
			$Datalevel[2]['data'][] = $v;
		}elseif ($v['sLevel'] == 3) {
			$Datalevel[3]['data'][] = $v;
		}elseif ($v['sLevel'] == 0) {
			$Datalevel[0]['data'][] = $v;
		}
}
// echo "<prE>";
// print_r($scrivnerArr);
// die;
// for ($i=0; $i < count($scrivnerArr); $i++) { 
	
	
		
// 		if ($scrivnerArr[$i]['sLevel'] == 1) {

// 			$Datalevel[1]['data'][] = $scrivnerArr[$i];
// 		}elseif ($scrivnerArr[$i]['sLevel'] == 2) {
// 			$Datalevel[2]['data'][] = $scrivnerArr[$i];
// 		}elseif ($scrivnerArr[$i]['sLevel'] == 3) {
// 			$Datalevel[3]['data'][] = $scrivnerArr[$i];
// 		}elseif ($scrivnerArr[$i]['sLevel'] == 0) {
// 			$Datalevel[0]['data'][] = $scrivnerArr[$i];
// 		}
	
// }



function getLevelData($sId,$year){

	global $conn;
	$sql = "SELECT * FROM tScrivenerLevel WHERE  sScrivener = '".$sId."' AND sYear = '".$year."'";
	$rs = $conn->Execute($sql);
	$total=$rs->RecordCount();

	return $rs->fields;
}


// for ($i=0; $i < count($scrivnerArr); $i++) { 
	
// 	if ($total > 0) {
// 		$scrivnerArr[$i]['certifiedMoney'] = $rs->fields['sCertifiedMoney'];
// 		$scrivnerArr[$i]['caseFeedBackMoney'] = $rs->fields['sFeedBackMoney'];
// 		$scrivnerArr[$i]['income'] = $scrivnerArr[$i]['certifiedMoney']-$scrivnerArr[$i]['caseFeedBackMoney'];
// 		$scrivnerArr[$i]['sBirthday'] = dateCg($scrivnerArr[$i]['sBirthday']);
// 		$scrivnerArr[$i]['sLevel'] = $rs->fields['sLevel'];
// 		// if ($rs->fields['sLevel'] == 1) {

// 		// 	// $Datalevel[1]['data'][] = $scrivnerArr[$i];
// 		// }elseif ($rs->fields['sLevel'] == 2) {
// 		// 	// $Datalevel[2]['data'][] = $scrivnerArr[$i];
// 		// }elseif ($rs->fields['sLevel'] == 3) {
// 		// 	// $Datalevel[3]['data'][] = $scrivnerArr[$i];
// 		// }elseif ($rs->fields['sLevel'] == 0) {
// 		// 	// $Datalevel[0]['data'][] = $scrivnerArr[$i];
// 		// }
// 	}
// }

// echo "<pre>";
// print_r($Datalevel);
// 以收入


// foreach ($Datalevel[3]['data'] as $k => $v) {
// 	$tmpData[$v['income']] = $v;
// }
// krsort($tmpData);
// unset($Datalevel[3]['data']);
// $Datalevel[3]['data'] = $tmpData;
// unset($tmpData);

// foreach ($Datalevel[2]['data'] as $k => $v) {
// 	$tmpData[$v['income']] = $v;
// }
// krsort($tmpData);
// unset($Datalevel[2]['data']);
// $Datalevel[2]['data'] = $tmpData;
// unset($tmpData);

// foreach ($Datalevel[1]['data'] as $k => $v) {
// 	$tmpData[$v['income']] = $v;
// }
// krsort($tmpData);
// unset($Datalevel[1]['data']);
// $Datalevel[1]['data'] = $tmpData;
// unset($tmpData);

// foreach ($Datalevel[0]['data'] as $k => $v) {
// 	$tmpData[$v['income']] = $v;
// }


// krsort($tmpData);
// unset($Datalevel[0]['data']);
// $Datalevel[0]['data'] = $tmpData;
// unset($tmpData);


$Datalevel[3]['count'] = count($Datalevel[3]['data']);
$Datalevel[2]['count'] = count($Datalevel[2]['data']);
$Datalevel[1]['count'] = count($Datalevel[1]['data']);
$Datalevel[0]['count'] = count($Datalevel[0]['data']);
// echo "<pre>";
// 	print_r($Datalevel);
// die;

function getCaseDataIncome($scrivener,$sApplyDate,$eApplyDate){
	global $conn;
	global $realestate;

	$totalMoney = 0;
	$certifiedMoney = 0;
	$cCaseFeedBackMoney = 0;

	$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate>="'.$sApplyDate.' 00:00:00" ' ;
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate<="'.$eApplyDate.' 23:59:59" ' ;

	// 搜尋條件-地政士
	if ($scrivener) {
		if ($query) { $query .= " AND " ; }
		$query .= ' csc.cScrivener="'.$scrivener.'" ' ;
	}

	// 搜尋條件-案件狀態
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus<>"8" ' ;

	if ($query) { $query = ' WHERE '.$query ; }

	$query ='
		SELECT 
			cas.cCertifiedId as cCertifiedId, 
			cas.cApplyDate as cApplyDate, 
			cas.cSignDate as cSignDate, 
			cas.cFinishDate as cFinishDate,
			cas.cEndDate as cEndDate, 
			inc.cTotalMoney as cTotalMoney, 
			inc.cCertifiedMoney as cCertifiedMoney, 
			cas.cCaseFeedBackMoney,
			cas.cCaseFeedBackMoney1,
			cas.cCaseFeedBackMoney2,
			cas.cCaseFeedBackMoney3,
			cas.cSpCaseFeedBackMoney,
			rea.cBranchNum as branch,
			rea.cBranchNum1 as branch1,
			rea.cBranchNum2 as branch2,
			rea.cBranchNum3 as branch3,
			cas.cCaseFeedback,
			cas.cCaseFeedback1,
			cas.cCaseFeedback2,
			cas.cCaseFeedback3,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			rea.cBrand2 as brand3
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
	$check = true;
	$rs = $conn->Execute($query);
	while (!$rs->EOF) {

	
		if ($check) {
			$totalMoney += $rs->fields['cTotalMoney'] ;
			$certifiedMoney += $rs->fields['cCertifiedMoney'] ;

			//總回饋金額
		
			if ($rs->fields['brand'] > 0 ) {
				if ($rs->fields['cCaseFeedback'] == 0) {
					$cCaseFeedBackMoney += $rs->fields['cCaseFeedBackMoney'];
				}
			}

			if ($rs->fields['brand1'] > 0) {
				if ($rs->fields['cCaseFeedback1'] == 0) {
					$cCaseFeedBackMoney += $rs->fields['cCaseFeedBackMoney1'];
				}
			}

			if ($rs->fields['brand2'] > 0) {
				if ($rs->fields['cCaseFeedback2'] == 0) {
					$cCaseFeedBackMoney += $rs->fields['cCaseFeedBackMoney2'];
				}
			}

			if ($rs->fields['cSpCaseFeedBackMoney'] > 0) {
				$cCaseFeedBackMoney += $rs->fields['cSpCaseFeedBackMoney'];
			}

			$tmp = getOtherFeedMoney($rs->fields['cCertifiedId']);
			if ($tmp['fMoney'] > 0) {
				$cCaseFeedBackMoney += $tmp['fMoney'];
			}
						
			unset($tmp);
		}
		
		$rs->MoveNext();
	}

	$income['total'] = $totalMoney;
	$income['certifiedMoney'] = $certifiedMoney;
	$income['caseFeedBackMoney'] = $cCaseFeedBackMoney;

	return $income;

}





?>