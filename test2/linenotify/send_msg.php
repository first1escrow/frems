<?php
include_once '../../openadodb.php';
include_once '../../sms/sms_function_manually.php';

$sms = new SMS_Gateway();

$txt = '第一建經通知：親愛的地政士您好，因今日11/4(四)下午員工教育訓練，服務時間至下午5點止，如有需要製作點交單或其他服務者，敬請提早來電,第一建經感謝您的支持!!!';
$sql = "SELECT sId,sName,sMobileNum FROM tScrivener WHERE sStatus ='1'";

$rs = $conn->Execute($sql);

$sId = array();

while (!$rs->EOF) {
	// 

	if (empty($sId[$rs->fields['sId']])) {
		$sId[$rs->fields['sId']] = array();
	}
	$sId[$rs->fields['sId']][$rs->fields['sMobileNum']]=$rs->fields;

	// array_push($sId[$rs->fields['sId']], $rs->fields);

	$rs->MoveNext();
}

foreach ($sId as $key => $value) {
	$sql=  "SELECT sScrivener AS sId,sName,sMobile AS sMobileNum FROM tScrivenerSms WHERE sDel = 0 AND sLock = 0 AND sScrivener ='".$key."' ";
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sId[$key][$rs->fields['sMobileNum']]=$rs->fields;

		
		$rs->MoveNext();
	}

}

$fw = fopen('../log/SSS.log', 'a+');

foreach ($sId as $key => $value) {

	foreach ($value as $data) {
		// print_r($data);
		if ($data['sMobileNum'] != '') {
			fwrite($fw, $data['sId']."_".$data['sName']."_".$data['sMobileNum']."\r\n");

			echo $data['sId']."_".$data['sName']."_".$data['sMobileNum']."\r\n";

			// $sms->manual_send('0919200247',$txt,'y','');
			// die;

			$sms->manual_send($data['sMobileNum'],$txt,'y','');
		}
		

		// die;

		// die;
	}
}
fclose($fw);


die;

print_r($sId);
?>