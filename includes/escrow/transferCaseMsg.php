<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php';
include_once dirname(dirname(dirname(__FILE__))).'/class/lineMessage.php';
// include_once dirname(dirname(dirname(__FILE__))).'/libs/PHPMailer-master/PHPMailerAutoload.php';
include_once dirname(dirname(dirname(__FILE__))).'/libs/phpmailer/PHPMailerAutoload.php';


$_POST = escapeStr($_POST) ;
$id = $_POST['id'];
$cat = $_POST['cat'];//1:轉給法務 2:返還經辦

$line = new LineMsg();

$lineId = '';

if ($cat == 1) {
	$sql = "SELECT
			pi.pName,
			la.lLineId
		FROM
			tPeopleInfo AS pi
		JOIN
			tLineAccount AS la ON la.lpId=pi.pId
		WHERE
			pi.pDep = 6";
	// echo $sql;

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$lineId = 'U4b14569b842b0d5d4613b77b94af02b6';
		$v = enCrypt('lineId='.$lineId.'&certifiedId='.$id);
		
		$data['lineId'] = $lineId;
		$data['btn_url'] = 'https://www.first1.com.tw/line/legal/LegalCaseDetail.php?v='.$v;
		$data['title'] = '案件移轉通知';
		$data['text'] = '保證號碼:'.$id.'，移轉通知請確認';
		$data['btn_label'] = '點我確認';

		$line->sendFlexTemplateMsg($data);

		$v = enCrypt('lineId='.$rs->fields['lLineId'].'&certifiedId='.$id);
		
		$data['lineId'] = $rs->fields['lLineId'];
		$data['btn_url'] = 'https://www.first1.com.tw/line/legal/LegalCaseDetail.php?v='.$v;
		$data['title'] = '案件移轉通知';
		$data['text'] = '保證號碼:'.$id.'，移轉通知請確認';
		$data['btn_label'] = '點我確認';

		$line->sendFlexTemplateMsg($data);
		// die;
		$rs->MoveNext();
	}

	echo "已通知法務";
	// $sql = "SELECT FROM tLegalCase WHERE lCertifiedId =";
}elseif($cat == 2){

	$sql = "SELECT
				pHiFaxAccount
			FROM
				tContractScrivener AS cs
			JOIN
				tScrivener AS s ON s.sId = cs.cScrivener
			JOIN 
				tPeopleInfo AS p ON p.pId=s.sUndertaker1
			WHERE
				cs.cCertifiedId = '".$id."'";
	// echo $sql;
	$rs = $conn->Execute($sql);


	
	$body = "法務已完成作業，請點選連結進行轉移確認動作。<a href='http://".$_SERVER['HTTP_HOST']."/legal/setCaseStatus.php?v=".enCrypt('cat=2&check=ok&id='.$id)."' target='_blank'>確認案件已移轉</a>";
	// $send_check = email_send($rs->fields['pHiFaxAccount'],'保證號碼'.$id.'案件移轉通知',$body);

	// echo $rs->fields['pHiFaxAccount'];
	if ($rs->fields['pHiFaxAccount']) {
		#$send_check = email_send('jing.lou@twhg.com.tw','保證號碼'.$id.'案件移轉通知',$body);
                #$send_check = email_send('sam.chang@twhg.com.tw','保證號碼'.$id.'案件移轉通知',$body);
                $send_check = email_send('cmc569@gmail.com','保證號碼'.$id.'案件移轉通知',$body);
		// $send_check = email_send($rs->fields['pHiFaxAccount'],'保證號碼'.$id.'案件移轉通知',$body);
	}

	echo "已通知經辦";

	// $send_check = email_send('jing.lou@twhg.com.tw','保證號碼'.$id.'案件移轉通知',$body);
	// $send_check = email_send('jing.lou@twhg.com.tw','保證號碼'.$id.'案件移轉通知',$body);
}



Function email_send($send_to,$title='',$body='') {
	//設定郵件資訊並發送
	$mail = new PHPMailer() ;
	$mail->IsSMTP() ;							//使用SMTP發信
	$mail->SMTPDebug	= fales;   
	$mail->SMTPAuth		= true ;  
	$mail->SMTPAutoTLS = false;
	$mail->Priority = 1;
	$mail->SMTPSecure	= "" ;
	$mail->Host			= '192.168.1.73' ;		//SMTP server 
	$mail->Port			= 25 ;  
	$mail->Username		= "www_sender";  
	$mail->Password		= "!www_sender!";  
	$mail->CharSet		= "utf-8";				//設定郵件編碼
	$mail->IsHTML(true);						//設定郵件內容為HTML



	$mail->SetFrom('www_sender@twhg.com.tw','第一建築經理股份有限公司後台系統') ;		//設定寄件者信箱
	
	$mail->AddAddress($send_to) ;
	//$mail->AddCC('jason.chen@twhg.com.tw','台灣房屋 陳銘慶') ;
	
	##

	$mail->IsHTML(true) ;
	$mail->Subject = $title ;
	$mail->Body = $body ;
	
	if ($mail->Send()) {
		return true ;
	}
	else {
		return false ;
	}
}


?>
