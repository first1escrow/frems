<?php
#顯示錯誤
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php' ;
 include_once '../../sms/sms_function_manually.php';
header("Content-Type:text/html; charset=utf-8"); 


$sms = new SMS_Gateway();

$mobile = array();
$fw = fopen('../log/sms_line.log', 'a+');
$sms_txt = '親愛的地政士及仲介朋友們～您好，爲因應疫情升級的可能性，第一建經已完成持續營運的超前部署，啟動異地分流、居家辦公機制，敬請各位合作夥伴們放心，只要國內銀行機構正常營運，第一建經出入款作業及案件服務皆會維持正常運作，不受疫情升級影響，祝福大家平安健康！第一建經與您一起加油！';

$sql = "SELECT sId FROM tScrivener WHERE sStatus = 1";
$rs = $conn->Execute($sql);
$scrivener = array(); 
while (!$rs->EOF) {
	array_push($scrivener, $rs->fields['sId']);

	$rs->MoveNext();
}

$fw = fopen('../log/line_sms.log', 'a+');

foreach ($scrivener as $k => $v) {
	$sql = "SELECT sName AS name ,sMobile AS mobile FROM tScrivenerSms WHERE sScrivener = '".$v."' AND sDel = 0";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		if (!checkLineExt($rs->fields['mobile'])) {
			// $txt = 's'.$v.'_'.$rs->fields['name']."_".$rs->fields['mobile']."\r\n";
			// fwrite($fw, $txt);
			// echo $txt;

			if (empty($mobile[$rs->fields['mobile']])) {
				$rs->fields['cat'] = 's'.$v;
				$mobile[$rs->fields['mobile']] = array();
				$mobile[$rs->fields['mobile']] = $rs->fields;
			}
			
		}

		$rs->MoveNext();
	}
}


$sql = "SELECT bId FROM tBranch WHERE bStatus = 1";
$rs = $conn->Execute($sql);
$branch = array();
while (!$rs->EOF) {
	array_push($branch, $rs->fields['bId']);

	$rs->MoveNext();
}

foreach ($branch as $k => $v) {
	$sql = "SELECT bName AS name ,bMobile AS mobile FROM tBranchSms WHERE bBranch = '".$v."' AND bDel = 0";
	$rs=  $conn->Execute($sql);
	while (!$rs->EOF) {
		if (!checkLineExt($rs->fields['mobile'])) {
			// $txt = 'b'.$v.'_'.$rs->fields['name']."_".$rs->fields['mobile']."\r\n";
			// fwrite($fw, $txt);
			// echo $txt;

			if (empty($mobile[$rs->fields['mobile']])) {
				$rs->fields['cat'] = 'b'.$v;
				$mobile[$rs->fields['mobile']] = array();
				$mobile[$rs->fields['mobile']] = $rs->fields;
			}
			
		}

		$rs->MoveNext();
	}
}



foreach ($mobile as $k => $v) {
	// $v['mobile'] = '0937185661';
	$sms->manual_send($v['mobile'],$sms_txt,'y','');

	// die;
	$txt = $v['cat'].'_'.$v['name']."_".$v['mobile']."\r\n";
	fwrite($fw, $txt);

	echo $txt;
}
fclose($fw);

function checkLineExt($mobile){

	global $conn;

	$sql = "SELECT * FROM tLineAccount WHERE lCaseMobile = '".$mobile."' OR lCaseMobile2 = '".$mobile."'";
	$rs= $conn->Execute($sql);

	if ($rs->EOF) { //沒有資料
		return false;
	}


	return true;

}

// foreach ($list as $k => $v) {
// 	$sql = "SELECT * FROM tLineAccount WHERE lTargetCode = '".$v['Code']."'";
// 	$rs = $conn->Execute($sql);

// 	if ($rs->EOF) {
// 		echo $v['Code']."_".
// 			$sms->manual_send($v['sMobileNum'],$txt,'n','');
// 	}
// }


// fwrite($fw, $txt);
// fclose($fw);

?>