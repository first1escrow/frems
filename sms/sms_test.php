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

$mobile_name = mb_convert_encoding('���ʼy','utf8','big5') ;
//$mobile_name = mb_convert_encoding('�i��S','utf8','big5') ;

//$sms_txt = mb_convert_encoding('������� ABC(�O�Ҹ��X:020001123)����q�����׬O70�Ӧr��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r','utf8','big5') ;
$sms_txt = mb_convert_encoding('�Ĥ@�ظg�q���G�R����ժ̤@,�����ժ�0921120868�G�]�O�Ҹ��X010000000�^���|��3600����12��31��s�J�i�O�M��C','utf8','big5') ;
//$sms_txt = mb_convert_encoding('�Ĥ@�ظg�H�U�i���O�ұM��w��5��27�馬��O�ҽs��020353691�]�R����������n�^�s�J�ΦL��900000��(161)','utf8','big5') ;

$target = mb_convert_encoding('income','utf8','big5') ;
$pid = '99985002000456' ;
$tid = '2874' ;
//echo $ans = $testMail->manual_sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;


echo $ans = send_fet_sms($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;

//���ǹq�T²�T�o�e
function send_fet_sms($mobile,$mobile_name,$txt,$tg,$pid,$tid) {
		$from_addr = '0936019428' ;									//��ܪ��o�ܤ踹�X
		$url = 'http://61.20.32.60:6600/mpushapi/smssubmit' ;		//����API���}
		$fet_SysId = 'twhg5354' ;									//API�b���N��
		$fet_SrcAddress = '01916800021169200223' ;					//�o�e�T�����ӷ���}(20�ӼƦr)
		$sms_str = '' ;
		$_error_code = '' ;

		//�s��ǰe²�T�r��
		$max_len = strlen(base64_encode($txt)) ;					//�p��²�T����(Base64�[�K��)
		
		$sms_str = '<?xml version="1.0" encoding="UTF-8"?>'.
			'<SmsSubmitReq>'.
				'<SysId>'.$fet_SysId.'</SysId>'.
				'<SrcAddress>'.$fet_SrcAddress.'</SrcAddress>'.
				'<DestAddress>'.$mobile.'</DestAddress>'.
				'<SmsBody>'.base64_encode($txt).'</SmsBody>'.
				'<DrFlag>true</DrFlag>'.
			'</SmsSubmitReq>' ;
		##
		
		//�}�l�ǰe²�T�B�z�Lcurl�o�e
		$url .= '?xml='.urlencode($sms_str) ;						//�z�LGET�覡�A�ǰe�U�o�e��²�T���
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
$mobile_name = mb_convert_encoding('�i��S','utf8','big5') ;
$sms_txt = mb_convert_encoding('������� ABC(�O�Ҹ��X:020001123)����q�����׬O70�Ӧr��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r��r','utf8','big5') ;

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