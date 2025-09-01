<?php
//include_once 'sms_function.php' ;
//
//$testMail = new SMS_Gateway() ;

$mobile_tel = '0922785490' ;
//$mobile_tel = '0918518066' ;
//$mobile_tel = '85297138310' ;

//$mobile_tel = '0983083069' ;	//Joanna
//$mobile_tel = '092194642' ;	//Vivian
//$mobile_tel = '0987371000' ;	//Jason

$mobile_name = mb_convert_encoding('陳銘慶','utf8','big5') ;
//$mobile_name = mb_convert_encoding('張鳳娟','utf8','big5') ;

//$sms_txt = mb_convert_encoding('中文測試 ABC(保證號碼:020001123)分兩段的長度是70個字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字','utf8','big5') ;
$sms_txt = mb_convert_encoding('第一建經通知：買方測試者一,賣方測試者0921120868二（保證號碼010000000）完稅款3600元於12月31日存入履保專戶。','utf8','big5') ;
//$sms_txt = mb_convert_encoding('第一建經信託履約保證專戶已於5月27日收到保證編號020353691（買方ＸＸＸ賣方ＸＸＸ南）存入用印款900000元(161)','utf8','big5') ;

$target = mb_convert_encoding('income','utf8','big5') ;
$pid = '99985002000456' ;
$tid = '2874' ;
//echo $ans = $testMail->manual_sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;


echo $ans = send_fet_sms($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;

//遠傳電訊簡訊發送
function send_fet_sms($mobile,$mobile_name,$txt,$tg,$pid,$tid) {
		$from_addr = '0936019428' ;									//顯示的發話方號碼
		$url = 'http://61.20.32.60:6600/mpushapi/smssubmit' ;		//遠傳API網址
		$fet_SysId = 'twhg5354' ;									//API帳號代號
		$fet_SrcAddress = '01916800021169200223' ;					//發送訊息的來源位址(20個數字)
		$sms_str = '' ;
		$_error_code = '' ;

		//編輯傳送簡訊字串
		$max_len = strlen(base64_encode($txt)) ;					//計算簡訊長度(Base64加密後)
		
		$sms_str = '<?xml version="1.0" encoding="UTF-8"?>'.
			'<SmsSubmitReq>'.
				'<SysId>'.$fet_SysId.'</SysId>'.
				'<SrcAddress>'.$fet_SrcAddress.'</SrcAddress>'.
				'<DestAddress>'.$mobile.'</DestAddress>'.
				'<SmsBody>'.base64_encode($txt).'</SmsBody>'.
				'<DrFlag>true</DrFlag>'.
			'</SmsSubmitReq>' ;
		##
		
		//開始傳送簡訊、透過curl發送
		$url .= '?xml='.urlencode($sms_str) ;						//透過GET方式，傳送愈發送的簡訊資料
		$ch = curl_init($url) ;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
		$output = curl_exec($ch) ;
		echo curl_error($ch) ;
		curl_close($ch) ;
		##
		
		return $output ;
}		
/*
$mobile_tel = '0983083069' ;	//Joanna
$mobile_name = mb_convert_encoding('張鳳娟','utf8','big5') ;
$sms_txt = mb_convert_encoding('中文測試 ABC(保證號碼:020001123)分兩段的長度是70個字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字塞字','utf8','big5') ;

echo $ans = $testMail->manual_sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;
*/
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=big-5" />
</head>
<body>

</body>
</html>