<?php

require_once '../../libs/phpmailer/class.phpmailer.php';

$mail = new PHPMailer();

$mail->IsSMTP() ;							//使用SMTP發信
$mail->SMTPDebug	= 0;   
$mail->SMTPAuth		= true ;  
$mail->SMTPSecure	= "" ;
$mail->Host			= '192.168.1.73' ;		//SMTP server 
$mail->Port			= 25 ;  
$mail->Username		= "www_sender";  
$mail->Password		= "!www_sender!";  
$mail->CharSet		= "utf-8";				//設定郵件編碼
$mail->IsHTML(true);						//設定郵件內容為HTML

// $mail->AddReplyTo($_POST["email"], $_POST["name"]);
$mail->SetFrom('www_sender@twhg.com.tw','第一建築經理股份有限公司');

// peggy@first1.com.tw
// annahsiao@first1.com.tw
$mail->AddAddress('annahsiao@first1.com.tw','蕭家津');
$mail->AddAddress('peggy@first1.com.tw','吳佩琦');
#$mail->AddAddress('jing.lou@twhg.com.tw', 'Jing.lou');
#$mail->AddAddress('sam_chang@twhg.com.tw', '台灣房屋 Sam');
$mail->AddAddress('cmc569@gmail.com', '第一建經 陳銘慶');

$mail->Subject = "關店通知".date('Y-m-d');


if ($brand['bId']!='') {
	
	// $mail->AltBody = 'testcontent';
	$body=$data[0]['bCode2']."-".$brand['bName'].$data[0]['bStore']."狀態已改為停用(".$data[0]['bEditor'].date('Y-m-d H:i:s').")";
}else{
	$body=$data['sName']."-".$data['sOffice']."狀態已改為停用(".$data['sEditor'].date('Y-m-d H:i:s').")";
}

// echo $body;

$mail->MsgHTML($body);

$mail->send();
?>


