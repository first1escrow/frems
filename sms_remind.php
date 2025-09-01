<?php
include_once '../openadodb.php' ;
// include_once 'db/openadodb.php' ;
// $lineToken = 'U42a053dde4940102bf8c9c7b750bb9a1';
// $msg = "測試\r測試";

// if ($_POST['txt']) {
// 	$msg = $_POST['txt'];
// $msg = urlencode($msg);
// $url = "http://www.design.e-twlink.com/first1/lineBot/first1LinePush.php?userId=".$lineToken."&msg=".$msg ;
// file_get_contents($url) ;
// }


$now = date('Y-m-d H:i:s');
// echo "檢查時間".$now."\r\n";


$sql = '
	SELECT
		tId,
		tVR_Code,
		tBank_kind,
		tObjKind,
		tMoney,
		tMemo,
		SUM(tMoney) as total_M,
		tKind,
		tExport_time,
		tExport_nu

	FROM
		tBankTrans
	WHERE 
		tExport="1" AND
		tSend != 1 AND tExport_time > "2017-05-10"
	GROUP BY
		tExport_nu
' ;

$rs = $conn->Execute($sql);
//<strong>媒體檔匯出時間: </strong>'.$rs->fields["tExport_time"].'  <strong>出帳金額:</strong> '.number_format($rs->fields["M"]).' 元&nbsp;
while (!$rs->EOF) {
	$list[] = $rs->fields;

	$rs->MoveNext();
}
//確認是否有寄送 count($list)
for ($i=0; $i < count($list); $i++) { 
	$sql = "SELECT bId FROM tBankTransSmsLog WHERE bExport_nu ='".$list[$i]['tExport_nu']."'";
	$rs = $conn->Execute($sql) ;
	$check = $rs->RecordCount() ;
	
	
	// echo $list[$i]['tExport_time']."_".$list[$i]['tExport_nu']."_".floor((($second1%86400)%3600)%60).'秒';
	if ($check == 0) {
		$branch ='';
		if (substr($list[$i]['tVR_Code'],0,5) == '99985') { //西門
			$branch = '西門';
		}elseif (substr($list[$i]['tVR_Code'],0,5) == '99986') { //城中
			$branch = '城中';
		}

		$min=floor((strtotime($now)-strtotime($list[$i]['tExport_time']))/60);
		// echo $list[$i]['tExport_nu']."_".$now."_".$list[$i]['tExport_time']."_".$min."\r\n";

		if ($min >= 10 && $min < 12) {
			$msg .= $list[$i]['tExport_time']."出帳金額:".number_format($list[$i]["total_M"])."元(".$list[$i]['tBank_kind'].$branch.")";
			
			$msg .="超過十分鐘未發送簡訊(1)\r\n";
			// echo '超過十分鐘未發送簡訊(1)';
		}elseif ($min > 12 && ($min%2) == 0) {
			$msg .= $list[$i]['tExport_time']."出帳金額:".number_format($list[$i]["total_M"])."元(".$list[$i]['tBank_kind'].$branch.")";
			$msg .="超過十分鐘未發送簡訊(2)\r\n";		
		}

		// $unSms[$list[$i]['tExport_nu']] = $list[$i]['tExport_time'];
		

	}
	
}
// echo "\n";
echo $msg;

// $lineToken = 'U42a053dde4940102bf8c9c7b750bb9a1';
// $msg = urlencode($msg);
// $url = "http://www.design.e-twlink.com/first1/lineBot/first1LinePush.php?userId=".$lineToken."&msg=".$msg ;
// file_get_contents($url) ;
//


?>
