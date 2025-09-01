<?php
#顯示錯誤
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php' ;
include_once '../../sms/sms_function_manually.php' ;

$sms = new SMS_Gateway();


header("Content-Type:text/html; charset=utf-8"); 
$sql = "SELECT CONCAT('SC',LPAD(sId,4,'0')) as Code,sName,sOffice,sMobileNum,sFaxArea,sFaxMain,sId FROM tScrivener WHERE sStatus = 1 AND sCategory = 1 AND sId NOT IN(620,170,1084) ORDER BY sId";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	if (!preg_match("/業務/", $rs->fields['sName'])) {
		$list[] = $rs->fields;
	}
	

	$rs->MoveNext();
}


$fw = fopen('../log/noline.log', 'a+');
$fw2 = fopen('../log/line.log', 'a+');
$txt = '';
$data = array();
$dataSms = array();
foreach ($list as $k => $v) {


	$sql = "SELECT * FROM tLineAccount WHERE lTargetCode = '".$v['Code']."' AND lStatus = 'Y' AND lCaseMobile2 = '".$v['sMobileNum']."'";
	$rs = $conn->Execute($sql);

	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$Info = array();
			if ($rs->fields['lLineId'] != 'U62691ec18d2e6bdb30e6df9992b3ff85' && $rs->fields['lineId'] != 'U6e14511ab2f238ec375671403ca967bd' && $rs->fields['lineId'] != 'Ub02f93d1f76c6dfb95d05d8408ee6e80' && $rs->fields['lineId'] != 'Ue544b9b2025975ccfd65d2cb0a31865e' && $rs->fields['lineId'] != 'U65f561d12bfd688bf9e69ddbbbfc3bf3' && $rs->fields['lLineId'] != 'U44781fbb96f2de34aef9e42caaebbebe') {
				$Info = $rs->fields;
				$Info['Code'] = $v['Code'];
				$Info['sName'] = $v['name'];
				$Info['sOffice'] = $v['sOffice'];
				$Info['sId'] = $v['sId'];
				array_push($data, $Info);
			}
			unset($Info);

			$rs->MoveNext();
		}
		fwrite($fw2, $v['Code']."\r\n");
	}else{
		array_push($dataSms, $v);
		fwrite($fw, $v['Code']."\r\n");
	}
	
	
	// if ($rs->EOF) {
	// 	echo $v['Code']."_".$v['sName']."_".$v['sOffice']."_".$v['sFaxArea']."-".$v['sFaxMain']."\r\n";
	// 	$txt .= $v['Code']."_".$v['sName']."_".$v['sOffice']."_".$v['sFaxArea']."-".$v['sFaxMain']."\r\n";
		
	// }
}
fclose($fw);
fclose($fw2);
echo count($data)."<br>";
$txt = '號外！號外！為感謝地政士熱烈支持，第一建經累積送件贈獎活動特別延長至今年12月31日，請各位地政士集中火力、全力送件至第一建經，累積越多、獲獎越多！';

// $fw = fopen('log/sendMsg.txt', 'a+');
foreach ($data as $v) {

	// $v['lLineId'] = 'U4b14569b842b0d5d4613b77b94af02b6';
	
	$url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=".$v['lLineId']."&txt=".urlencode($txt);

	echo $url."\r\n";
	$msg = file_get_contents($url);

	// echo $msg."\r\n";
	// $url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=".$v['lLineId']."&img=https://www.first1.com.tw/images/print/act_2021.jpg";


	// echo $url."\r\n";
	// file_get_contents($url);
		// die;
	// echo $v['Code']."_".$v['sOffice']."_".$v['lLineId']."-".$v['lNickName']."-"."\r\n";
	// $v['sId'] = 1;
	// $sql = "UPDATE tScrivener SET sSendLine = 1 WHERE sId = '".$v['sId']."'";
	// $conn->Execute($sql);
	// echo $sql."\r\n";

	// fwrite($fw, $v['Code']."\r\n");
	// die;
}

foreach ($dataSms as $value) {
	// print_r($value);

	// die;
	// $value['sMobileNum'] = '0919200247';

	// $value['sMobileNum']
	$tt = $sms->manual_send($value['sMobileNum'],$txt,"y",$value['sName'],'活動2021'); //手動簡訊(電話),文字,是否發送
	
}
// fwrite($fw, $txt);
// fclose($fw);

?>