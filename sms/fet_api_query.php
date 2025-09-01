<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once 'sms_delivery_status_fet.php' ;
include_once 'sms_return_code_fet.php' ;
include_once '../session_check.php' ;

$Q_mobile = $_POST['qMobile'] ;												//收訊方手機號碼
$Q_msgid = $_POST['qId'] ;													//遠傳電信 message id

//進行 FET API Query
//if (preg_match("/^09[0-9]{8}$/",$Q_mobile)) {
if (preg_match("/^09[0-9]{8}$/",$Q_mobile) && (preg_match("/^[0-9]{13,14}$/",$Q_msgid))) {
	// Query 參數設定
	$fet_SysId = 'twhg5354' ;												//遠傳API帳號代碼
	$res = '' ;
	
	//遠傳 URL
	$url = 'http://61.20.32.60:6600/mpushapi/smsquerydr' ;					// http 版	
	##
	
	//透過 curl 發動查詢
	$sms_str = '<?xml version="1.0" encoding="UTF-8"?>'.
		'<SmsQueryDrReq>'.
			'<SysId>'.$fet_SysId.'</SysId>'.
			'<MessageId>'.$Q_msgid.'</MessageId>'.
			'<DestAddress>'.$Q_mobile.'</DestAddress>'.
		'</SmsQueryDrReq>' ;
	##
	
	//進行curl發送
	$url .= '?xml='.urlencode($sms_str) ;
	$ch = curl_init($url) ;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
	//$res = curl_exec($ch) ;
	
	//echo 'A:'.$res ; exit ;
	if ($res = curl_exec($ch)) {
		$res = str_replace("\n","",$res) ;
		curl_close($ch) ;
		$resA = preg_replace("/\</","＜",$res) ;
		$resA = preg_replace("/\>/","＞",$res) ;
		$resA = preg_replace("/\//","／",$res) ;
	}
	else {		//若連線失敗時，結束連線並發送簡訊
		$ans = "遠傳簡訊 API 檢查(連線)失敗\n" ;
		curl_close($ch) ;
		exit ;
	}
	##
			
	//檢核並取出 query 結果
	$detail = array() ;
	
	preg_match("/<SmsQueryDrRes><ResultCode>(.*)<\/ResultCode><ResultText>(.*)<\/ResultText>(.*)<\/SmsQueryDrRes>/",$res,$opt) ;
	$ResultCode = $opt[1] ;
	$ResultText = $opt[2] ;
	
	$arr = explode('</Receipt>',$opt[3]) ;
	$arr[0] = preg_replace("/<Receipt>/","",$arr[0]) ;
	
	preg_match("/^<MessageId>(.*)<\/MessageId><DestAddress>(.*)<\/DestAddress><DeliveryStatus>(.*)<\/DeliveryStatus>(.*)<SubmitDate>(.*)<\/SubmitDate>(.*)<Seq>/",$arr[0],$_data) ;		//僅取第一組解析
	unset($opt) ;
	unset($arr) ;
	
	$ErrorCode = $_data[4] ;
	if ($ErrorCode) {
		preg_match("/<ErrorCode>(.*)<\/ErrorCode>/",$ErrorCode,$arrTmp) ;
		$ErrorCode = $arrTmp[1] ;
	}
	
	$detail['ResultCode'] = $ResultCode ;
	$detail['ResultText'] = $ResultText ;
	$detail['MessageId'] = $_data[1] ;
	$detail['DestAddress'] = $_data[2] ;
	$detail['DeliveryStatus'] = $_data[3] ;
	$detail['ErrorCode'] = $ErrorCode ;
	if ($_data[5]) {
		$_data[5] = '20'.substr($_data[5],0,2).'-'.substr($_data[5],2,2).'-'.substr($_data[5],4,2).' '.substr($_data[5],6,2).':'.substr($_data[5],8,2).':'.substr($_data[5],10,2) ;
	}
	$detail['SubmitDate'] = $_data[5] ;
	
	$_data[6] = str_replace("<DoneDate>","",$_data[6]) ;
	$_data[6] = str_replace("</DoneDate>","",$_data[6]) ;
	if ($_data[6]) {
		$_data[6] = '20'.substr($_data[6],0,2).'-'.substr($_data[6],2,2).'-'.substr($_data[6],4,2).' '.substr($_data[6],6,2).':'.substr($_data[6],8,2).':'.substr($_data[6],10,2) ;
	}
	$detail['DoneDate'] = $_data[6] ;	
	##
}
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>遠傳簡訊狀態列表查詢</title>
<script>
$(function() {
	
}) ;
</script>
<style>
</style>
</head>

<body>
<div id="returnCode" style="">
遠傳回覆字串：<br><?=$resA?>
<hr>
<?php
echo '回傳結果描述：'.$detail['ResultCode']."<br><br>\n" ;
echo '回傳結果代碼：'.$detail['ResultText']."<br><br>\n" ;
echo '遠傳編號(Message Id)：'.$detail['MessageId']."<br><br>\n" ;
echo '發送對象手機號碼：'.$detail['DestAddress']."<br><br>\n" ;
echo '發送狀態：'.$detail['DeliveryStatus']."<br><br>\n" ;

if ($detail['ErrorCode'] != '000') {
	include_once 'fet_delivery_code.php' ;
	echo '錯誤代碼：'.$detail['ErrorCode']."<br><br>\n" ;
	
	$errId = preg_replace("/^0+/","",$detail['ErrorCode']) ;
	echo '錯誤代碼說明：<span style="font-weight:bold;color:red;">'.$dStatus[$errId]."</span><br><br>\n" ;
}

echo '發送時間：'.$detail['SubmitDate']."<br><br>\n" ;
echo '完成時間：'.$detail['DoneDate']."<br><br>\n" ;
?>
</div>

</body>
</html>
