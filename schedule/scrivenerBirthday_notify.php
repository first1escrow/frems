<?php

include_once '/home/httpd/html/first.twhg.com.tw/openadodb.php' ;
include_once '/home/httpd/html/first.twhg.com.tw/report/getBranchType.php';
include_once '/home/httpd/html/first.twhg.com.tw/includes/maintain/feedBackData.php';
include_once '/home/httpd/html/first.twhg.com.tw/sms/sms_function_manually.php' ;

##

$today = date('Y-m-d');
$sms = new SMS_Gateway();
##
echo "=======================".$today ."=======================\r\n";

$exceptbId = array(632, 575,552,620,411) ;//地政士(排除奇怪的:632=業務專用 575=陳政祺 552=王泰翔 620=吳效承 411=吳),224



$notifyDate = date('Y-m-d', strtotime('+14 day', strtotime($today))); //往後七天的壽星

$tmp = explode('-', $notifyDate);
$year = $tmp[0];
$str = "s.sStatus = 1 AND  MONTH(s.sBirthday) = '".$tmp[1]."' AND DAY(s.sBirthday) = '".$tmp[2]."'";
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
$scrivnerArr = $Datalevel = array();
while (!$rs->EOF) {
	if ($rs->fields['sBirthday'] != '0000-00-00') {
		$tmp = explode('-', $rs->fields['sBirthday']);

		$birthday = strtotime($year."-".$tmp[1]."-".$tmp[2]);
		// echo $birthday."<br>";
		
		$scrivnerArr[$i] = $rs->fields;
		$scrivnerArr[$i]['notify'] = date('Y-m-d', strtotime('-14 day', $birthday)); //date('Y-m-d', strtotime('+2 year, +10 days', $rs->fields['sBirthday']))
		// $scrivnerArr[$i]['eApplyDate'] = date('Y-m-d', strtotime('-1 year', strtotime($scrivnerArr[$i]['notify'])));
		
		// $scrivnerArr[$i]['sApplyDate'] = date('Y-m-d', strtotime('-1 year', strtotime($scrivnerArr[$i]['eApplyDate'])));
		unset($tmp);unset($birthday);
		$i++;
	}
	
	$rs->MoveNext();
}

// print_r($scrivnerArr);
// die;


for ($i=0; $i < count($scrivnerArr); $i++) { 
	$sql = "SELECT * FROM tScrivenerLevel WHERE  sScrivener = '".$scrivnerArr[$i]['sId']."' AND sYear = '".$year."'";
	$rs = $conn->Execute($sql);
	$total=$rs->RecordCount();
	if ($total > 0) {
	
		// $scrivnerArr[$i]['sBirthday'] = dateCg($scrivnerArr[$i]['sBirthday']);
		if ($rs->fields['sLevel'] == 1) {
			$scrivnerArr[$i]['level'] = 1;
			$Datalevel[$scrivnerArr[$i]['sSales']][1][] = $scrivnerArr[$i];
		}elseif ($rs->fields['sLevel'] == 2) {
			$scrivnerArr[$i]['level'] = 2;
			$Datalevel[$scrivnerArr[$i]['sSales']][2][] = $scrivnerArr[$i];
		}elseif ($rs->fields['sLevel'] == 3) {
			$scrivnerArr[$i]['level'] = 3;
			$Datalevel[$scrivnerArr[$i]['sSales']][3][] = $scrivnerArr[$i];
		}elseif ($rs->fields['sLevel'] == 0) {
			$scrivnerArr[$i]['level'] = 0;
			$Datalevel[$scrivnerArr[$i]['sSales']][0][] = $scrivnerArr[$i];
		}
	}

	
	// unset($income);
}

	$sql = "SELECT * FROM tPeopleInfo WHERE pDep IN (4,7) AND pJob = 1";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		
			foreach ($Datalevel[$rs->fields['pId']] as $k => $v) {	
				
				foreach ($v as $key => $value) {
					
					
						
					if ($k != 0) {
						$txt = "【達標】親愛的".$rs->fields['pName']."同仁，您服務的".$value['sName']."代書即將過生日（".substr($value['sBirthday'], 5)."），他為等級（".$k."）之代書，趕緊準備生日禮前往拜訪喔！";
						
						$sms->manual_send($rs->fields['pMobile'],$txt,'y','');

						if ($rs->fields['pName'] != '曾政耀') {
							$sms->manual_send('0930945670',$txt,'y','');
						}
						echo 'SEND:';
					}else{
						// $txt = "親愛的".$rs->fields['pName']."同仁，下週（".substr($value['sBirthday'], 5)."）您服務的".$value['sName']."代書即將過生日，代書「未達」等級標準";
						$txt = "【未達標】親愛的".$rs->fields['pName']."同仁，您服務的".$value['sName']."代書即將過生日（".substr($value['sBirthday'], 5)."）";
						$sms->manual_send($rs->fields['pMobile'],$txt,'y','');

						if ($rs->fields['pName'] != '曾政耀') {
							$sms->manual_send('0930945670',$txt,'y','');
						}
						echo 'SEND:';
					}
					
					echo $txt."\r\n";
					
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






?>