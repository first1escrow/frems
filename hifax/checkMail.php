<?php
@session_start() ;
ini_set('date.timezone','Asia/Taipei') ;

include_once '../openadodb.php' ;
include_once 'MailClass.php' ;

$pId = $_SESSION['member_id'] ;

$mailList = array() ;
$mail = new Evebit_Email() ;

//
if ($pId) {
	$sql = 'SELECT * FROM tPeopleInfo WHERE pId='.$pId.';' ;
	$rs = $conn->Execute($sql) ;
	
	$user = $rs->fields['pHiFaxAccount'] ;
	$pwd = $rs->fields['pHiFaxPassword'] ;
	
	$host = 'www.hibox.hinet.net' ;
	//$user = 'first123@hibox.hinet.net' ;
	//$pwd = '8888';
	$port = '143' ;
	

	//取得列表
	$connect = $mail->mailConnect($host,$port,$user,$pwd,'INBOX','novalidate-cert') ;
	
	if($connect) {
	$totalCount = $mail->mailTotalCount();
		//echo date("Y-m-d H:i:S") ."  total:".$totalCount."\n";
		for ($i = $totalCount ; $i > 0 ; $i --) {
			$tmp = $mail->mailHeader($i) ;
			//print_r($tmp) ;
			
			if ($tmp['seen'] == 'U') {
				$mailList[] = $mail->mailHeader($i) ;
			}
			
			unset($tmp) ;
		}
		//print_r($mailList);
		
	}
	
	if (count($mailList) > 0) {
		echo count($mailList) ;
	}
	//else echo '' ;
}
//else exit ;
##
?>