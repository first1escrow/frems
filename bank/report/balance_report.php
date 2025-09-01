<?php
include_once('../../openadodb.php') ;
require_once('../Classes/PHPExcel.php');
require_once('../Classes/PHPExcel/Writer/Excel2007.php');
require_once('../Classes/phpmailer/class.phpmailer.php') ;
//include_once '../../session_check.php' ;

//確認日期是否要產生餘額檔案
$today = date("Y-m-d") ;												//今天日期
$lastday = date("Y-m-d",mktime(0,0,0,(date("m")+1),0,date("Y"))) ;		//當月的最後一天日期
//$lastday = '2015-02-03' ;

if ($today != $lastday) {		//若當日不是本月的最後一天，則結束程式
	echo '今日('.$today.')非每月的最後一天('.$lastday.')!!'."\n" ;
	exit ;
}
else {
	echo '今日('.$today.')為本月的最後一天!!開始產出永豐餘額檔...'."\n" ;
}
##

//西門分行(999850)
$sql = '
	SELECT
		cCertifiedId,
		cEscrowBankAccount,
		cCaseMoney
	FROM
		tContractCase
	WHERE
		cEscrowBankAccount LIKE "999850%"
	ORDER BY
		cCertifiedId
	ASC ;
' ;
##

$rs = $conn->Execute($sql) ;

$file_name = '999850_'.date("Ymd").'.csv' ;
$file_path = "/home/httpd/html/first.twhg.com.tw/bank/report/excel/" ;
$_file1 = $file_path.$file_name ;

$fh = fopen($_file1,'w') ;
fwrite($fh,iconv("utf-8","big5","產出日期,保證號碼,餘額\n")) ;
while (!$rs->EOF) {
	fwrite($fh,date("Ymd").','.$rs->fields['cCertifiedId'].','.$rs->fields['cCaseMoney']."\n") ;
	$rs->MoveNext() ;
}
fclose($fh) ;
##

//城中分行(999860)
$sql = '
	SELECT
		cCertifiedId,
		cEscrowBankAccount,
		cCaseMoney
	FROM
		tContractCase
	WHERE
		cEscrowBankAccount LIKE "999860%"
	ORDER BY
		cCertifiedId
	ASC ;
' ;
##

$rs = $conn->Execute($sql) ;

$file_name = '999860_'.date("Ymd").'.csv' ;
$file_path = "/home/httpd/html/first.twhg.com.tw/bank/report/excel/" ;
$_file2 = $file_path.$file_name ;

$fh = fopen($_file2,'w') ;
fwrite($fh,iconv("utf-8","big5","產出日期,保證號碼,餘額\n")) ;
while (!$rs->EOF) {
	fwrite($fh,date("Ymd").','.$rs->fields['cCertifiedId'].','.$rs->fields['cCaseMoney']."\n") ;
	$rs->MoveNext() ;
}
fclose($fh) ;
##

//設定郵件資訊並發送
$mail = new PHPMailer() ;
$mail->IsSMTP() ;							//使用SMTP發信
$mail->SMTPDebug	= 0;   
$mail->SMTPAuth		= true ;  
$mail->SMTPSecure	= "" ;
//$mail->Host			= '192.168.1.106' ;		//SMTP server 
$mail->Host			= '192.168.1.73' ;		//SMTP server 
$mail->Port			= 25 ;  
$mail->Username		= "www_sender";  
$mail->Password		= "!www_sender!";  
$mail->CharSet		= "utf-8";				//設定郵件編碼
$mail->IsHTML(true);						//設定郵件內容為HTML

$mail->SetFrom('www_sender@twhg.com.tw','第一建築經理股份有限公司後台系統') ;		//設定寄件者信箱
$mail->AddReplyTo('jason.chen@twhg.com.tw','第一建築經理股份有限公司後台系統') ;	//設定回信信箱

$mail->AddAddress('stanhsu@sinopac.com','永豐 許晉嘉') ;
$mail->AddAddress('ap333@sinopac.com','永豐 廖心慧') ;
$mail->AddAddress('nicol.chuang@sinopac.com','永豐 莊文怡') ;
$mail->AddAddress('compo.fu@sinopac.com','永豐 富保琴') ;
$mail->AddAddress('helenlin@sinopac.com','永豐 林姿秀') ;
$mail->AddAddress('FWH112290@sinopac.com','永豐 范文瑄') ;
$mail->AddCC('pippen.wu@sinopac.com ','永豐 吳協理') ;
$mail->AddCC('ckc1207@yahoo.com.tw ','第一建經 周法務長') ;
$mail->AddCC('odd618@yahoo.com.tw ','第一建經 曾協理') ;
$mail->AddBcc('jason.chen@twhg.com.tw','台灣房屋 陳銘慶') ;
$mail->AddBcc('michliu.liu@gmail.com','台灣房屋 Michael') ;
//$mail->AddAddress('cmc569@gmail.com') ;

$mail->IsHTML(true) ;
$mail->Subject = '保證號碼帳戶餘額列表' ;
$mail->Body = '(本信為系統自動發送，請勿直接回覆)' ;
//$mail->AddAttachment($file_path.$file_name) ;		//增加附件
$mail->AddAttachment($_file1) ;						//增加附件(西門分行999850)
$mail->AddAttachment($_file2) ;						//增加附件(城中分行999860)

include('sms_send.php') ;

if ($mail->Send()) {
	echo date("Y-m-d H:i:s").' 郵件已送出!!'."\n" ;
	send_sms('0922785490','永豐餘額郵件回報',date("Y-m-d H:i:s").' 第一建經永豐保證號碼餘額郵件已發送!!') ;
}
else {
	echo date("Y-m-d H:i:s").' 郵件發送失敗!!'."\n" ;
	send_sms('0922785490','永豐餘額郵件回報',date("Y-m-d H:i:s").' 第一建經永豐保證號碼餘額郵件發送失敗!!') ;
}

?>
