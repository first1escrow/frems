<?php
@session_start() ;
ini_set('date.timezone','Asia/Taipei') ;

include_once 'MailClass.php' ;

$mailList = array() ;
$mail = new Evebit_Email() ;


$user = 'slack@first1.com.tw' ;
$pwd = 'TwhG5008' ;

$host = 'mail.first1.com.tw' ;
//$user = 'first123@hibox.hinet.net' ;
//$pwd = '8888';
$port = '143' ;


//取得列表
$connect = $mail->mailConnect($host,$port,$user,$pwd,'INBOX','novalidate-cert') ;

if($connect) {
$totalCount = $mail->mailTotalCount();
	echo date("Y-m-d H:i:S") ."  total:".$totalCount."\n";
	for ($i = $totalCount ; $i > 0 ; $i --) {
		// $tmp = $mail->mailHeader($i) ;
		$tmp = $mail->getBody($i) ;
		print_r($tmp) ; exit ;
		
		if ($tmp['seen'] == 'U') {
			$mailList[] = $mail->mailHeader($i) ;
		}
		
		unset($tmp) ;
	}
	print_r($mailList);
	
}

if (count($mailList) > 0) {
	echo count($mailList) ;
}

##
?>