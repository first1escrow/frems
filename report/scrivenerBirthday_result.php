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

$xls = trim(addslashes($_POST['xls'])) ;

$scrivener = $_POST['scrivener'] ;
$year = $_POST['year']+1911;
$month = $_POST['month'];
$realestate = $_POST['realestate'];

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
			ORDER BY sId ASC
			" ;

$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	if ($rs->fields['sBirthday'] != '0000-00-00') {
		$tmp = explode('-', $rs->fields['sBirthday']);

		$birthday = strtotime($year."-".$tmp[1]."-".$tmp[2]);
		// echo $birthday."<br>";
		
		$scrivnerArr[$i] = $rs->fields;
		$scrivnerArr[$i]['notify'] = date('Y-m-d', strtotime('-7 day', $birthday)); //date('Y-m-d', strtotime('+2 year, +10 days', $rs->fields['sBirthday']))
		$scrivnerArr[$i]['eApplyDate'] = date('Y-m-d', strtotime('-1 year', strtotime($scrivnerArr[$i]['notify'])));
		
		$scrivnerArr[$i]['sApplyDate'] = date('Y-m-d', strtotime('-1 year', strtotime($scrivnerArr[$i]['eApplyDate'])));

		$income = getCaseDataIncome($scrivnerArr[$i]['sId'],$scrivnerArr[$i]['sApplyDate'],$scrivnerArr[$i]['eApplyDate']);
	
		$scrivnerArr[$i]['total'] = $income['total'];
		$scrivnerArr[$i]['certifiedMoney'] = $income['certifiedMoney'];
		$scrivnerArr[$i]['caseFeedBackMoney'] = $income['caseFeedBackMoney'];
		$scrivnerArr[$i]['income'] = $scrivnerArr[$i]['certifiedMoney']-$scrivnerArr[$i]['caseFeedBackMoney'];

		$scrivnerArr[$i]['sBirthday'] = dateCg($scrivnerArr[$i]['sBirthday']);
		unset($tmp);unset($birthday);unset($income);
		$i++;
	}
	
	$rs->MoveNext();
}

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
	
	if ($v['income'] > 100000 && $v['income'] <= 300000) { //10萬~30萬 level3
		$v['level'] = 3;

		$Datalevel[3]['data'][] = $v;
	}elseif ($v['income'] > 300000 && $v['income'] <= 500000) { //30萬~50萬 level2
		$v['level'] = 2;
		$Datalevel[2]['data'][] = $v;
	}elseif ($v['income'] > 500000) { //50萬UP level1
		$v['level'] = 1;
		$Datalevel[1]['data'][] = $v;
	}else{
		$v['level'] = 0;
		$Datalevel[0]['data'][] = $v;
	}
}



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

		if ($realestate) {
			$check = false;
			$type = branch_type($conn,$rs->fields);

			if ($realestate == '11' && $type == 'O') {
				$check = true;
			}elseif($realestate == '6' && ($type == 'O' || $type == '3')){ //他牌+非仲
				$check = true;

			}elseif($realestate == '5' && ($type == 'T' || $type == 'U' || $type == '2')){ //台屋集團
				$check = true;
			}
			else if ($realestate == '12' && $type == 'T') {
				$check = true;
			}else if ($realestate == '13' && $type == 'U') {
				$check = true;
			}else if ($realestate == '14' && $type == 'F') {
				$check = true;
			}
			else if ($realestate == '1' && ($type == 'O' || $type == 'T' || $type == 'U' || $type == 'F')) {
				$check = true;
			}
			else if ($realestate == '2' && $type == '2') {
				$check = true;
			}
			else if ($realestate == '3' && $type == '3') {
				$check = true;
			}
			else if ($realestate == '4' && $type == 'N' ) {
				$check = true;
			}
		}

		// echo "<pre>";
		// print_r($rs->fields);
		// $arr[$i] = $rs->fileds;

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