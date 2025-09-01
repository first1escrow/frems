<?php

include_once '../configs/config.class.php';
include_once 'class/intolog.php' ;
include_once '../tracelog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../report/getBranchType.php';
include_once 'includes/maintain/feedBackData.php';
include_once '../sms/sms_function_manually.php' ;
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


$exceptbId = array(632, 575,552,620,411) ;//地政士(排除奇怪的:632=業務專用 575=陳政祺 552=王泰翔 620=吳效承 411=吳),224



$notifyDate = date('Y-m-d', strtotime('+7 day', strtotime(date('Y-m-d')))); //往後七天的壽星

$tmp = explode('-', $notifyDate);
$year = $tmp[0];
$str .= "s.sStatus = 1 AND  MONTH(s.sBirthday) = '".$tmp[1]."' AND DAY(s.sBirthday) = '".$tmp[2]."'";
// echo $notifyDate;

unset($tmp);
$sql = "SELECT
			s.sId,
			s.sBirthday,
			s.sName,
			s.sOffice,
			CONCAT('SC',LPAD(s.sId,4,'0')) as Code,
			ss.sSales
		FROM
			tScrivener AS s 
		LEFT JOIN
			tScrivenerSales AS ss ON ss.sScrivener = s.sId
		WHERE
			".$str."
			AND s.sId NOT IN (".implode(',',$exceptbId).")
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
		unset($tmp);unset($birthday);
		$i++;
	}
	
	$rs->MoveNext();
}



for ($i=0; $i < count($scrivnerArr); $i++) { 
	$income = getCaseDataIncome($scrivnerArr[$i]['sId'],$scrivnerArr[$i]['sApplyDate'],$scrivnerArr[$i]['eApplyDate']);
	
	$scrivnerArr[$i]['total'] = $income['total'];
	$scrivnerArr[$i]['certifiedMoney'] = $income['certifiedMoney'];
	$scrivnerArr[$i]['caseFeedBackMoney'] = $income['caseFeedBackMoney'];
	$scrivnerArr[$i]['income'] = $scrivnerArr[$i]['certifiedMoney']-$scrivnerArr[$i]['caseFeedBackMoney'];

	if ($scrivnerArr[$i]['income'] > 100000 && $scrivnerArr[$i]['income'] <= 300000) { //10萬~30萬 level3
		$scrivnerArr[$i]['level'] = 3;
		$Datalevel[$scrivnerArr[$i]['sSales']][3][] = $scrivnerArr[$i];
		// $Datalevel[3]['data'][] = $scrivnerArr[$i];
	}elseif ($scrivnerArr[$i]['income'] > 300000 && $scrivnerArr[$i]['income'] <= 500000) { //30萬~50萬 level2
		$scrivnerArr[$i]['level'] = 2;
		$Datalevel[$scrivnerArr[$i]['sSales']][2][] = $scrivnerArr[$i];
		// $Datalevel[2]['data'][] = $scrivnerArr[$i];
	}elseif ($scrivnerArr[$i]['income'] > 500000) { //50萬UP level1
		$scrivnerArr[$i]['level'] = 1;
		$Datalevel[$scrivnerArr[$i]['sSales']][1][] = $scrivnerArr[$i];
		// $Datalevel[1]['data'][] = $scrivnerArr[$i];
	}else{
		$scrivnerArr[$i]['level'] = 0;
		$Datalevel[$scrivnerArr[$i]['sSales']][0][] = $scrivnerArr[$i];
		// $Datalevel[0]['data'][] = $scrivnerArr[$i];
	}
	unset($income);
}


	# code...
	$sql = "SELECT * FROM tPeopleInfo WHERE pDep IN (4,7) AND pJob = 1";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		if (is_array($Datalevel[$rs->fields['pId']])) {
			foreach ($Datalevel[$rs->fields['pId']] as $k => $v) {	
				
				foreach ($v as $key => $value) {
					// print_r($value);
					// die;
					if ($key != 0) {
						$txt = "親愛的".$rs->fields['pName']."同仁，下週（".substr($value['sBirthday'], 5)."）（日期）您服務的".$value['sName']."代書即將過生日，他為等級（".$key."）之代書，趕緊準備生日禮前往拜訪喔！";
						echo $txt;
					}
					
					// if ($key == 3) {
					// 	$txt3 .= $value['Code'].$value['sName']."_";
					// }elseif ($key == 2) {
					// 	$txt2 .= $value['Code'].$value['sName']."_";
					// }elseif ($key == 1) {
					// 	$txt1 .= $value['Code'].$value['sName']."_";
					// }else{
					// 	$txt0 .= $value['Code'].$value['sName']."_";
					// }

					// echo "<pre>";
					// print_r($value);
					// die;
				}
			}
		}

		$rs->MoveNext();
	}
// 
	// $sms->manual_send($mobile,$txt,'y',$_SESSION['member_name']);
// echo "等級1<br>".$txt1."<br>";
// echo "等級2<br>".$txt2."<br>";
// echo "等級3<br>".$txt3."<br>";
// echo "等級4<br>".$txt0."<br>";
// echo '123';
// echo "<pre>";
// 	print_r($Datalevel);
die;

function getCaseDataIncome($scrivener,$sApplyDate,$eApplyDate){
	global $conn;

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
	// echo $query;
	$rs = $conn->Execute($query);
	while (!$rs->EOF) {
		// echo "<pre>";
		// print_r($rs->fields);
		// $arr[$i] = $rs->fileds;
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
		$rs->MoveNext();
	}

	$income['total'] = $totalMoney;
	$income['certifiedMoney'] = $certifiedMoney;
	$income['caseFeedBackMoney'] = $cCaseFeedBackMoney;

	return $income;

}





?>