<?php
//簡訊系統發送 專用API
//include '../openadodb.php' ;
include '/home/httpd/html/first.twhg.com.tw/openadodb.php' ;
include '/home/httpd/html/first.twhg.com.tw/sms/sms_send.php' ;
require_once('/home/httpd/html/first.twhg.com.tw/bank/Classes/phpmailer/class.phpmailer.php') ;

//查詢回傳碼解析
Function cht_sms_query_code($no) {
		$code_des = array(
			'0'	=>	'簡訊已成功發送至接收端',
			'1'	=>	'簡訊傳送中',
			'2'	=>	'系統無法找到您要找的訊息',
			'3'	=>	'簡訊無法成功送達手機',
			'4'	=>	'中華電信系統或是資料庫故障',
			'5'	=>	'簡訊狀態不明。此筆簡訊已被刪除',
			'8'	=>	'接收端SIM已滿，造成訊息傳送失敗',
			'9'	=>	'錯誤的接收端號碼，可能是空號',
			'11'=>	'號碼格式錯誤',
			'12'=>	'收訊手機已設定拒收簡訊',
			'13'=>	'手機錯誤',
			'16'=>	'系統無法執行 msisdn <-> subdo，請稍後再試。',
			'17'=>	'系統無法找出對應此subno知道電話號碼，請查明subdo是否正確',
			'18'=>	'請檢查受訊方號碼格式是否正確',
			'21'=>	'請檢查 Message id 格式是否正確',
			'23'=>	'你的登入 IP 未在系統註冊',
			'24'=>	'帳號已停用',
			'31'=>	'訊息尚未傳送到 SMSC',
			'32'=>	'訊息無法傳送到簡訊中心',
			'33'=>	'訊息無法傳送到簡訊中心(訊務繁忙)',
			'48'=>	'受訊客戶要求拒收加值簡訊，請不要再重送'
		) ;
		
		if ($no < 0) {
			return '中華電信系統或是資料庫故障' ;
		}
		else {
			return $code_des[$no] ;
		}

}
##

//搜尋未確認簡訊
$sql = '
	SELECT
		a.*,
		b.sSend_Time
	FROM
		tSMS_Check AS a
	JOIN
		tSMS_Log as b ON a.tTaskID=b.tTID
	WHERE
		a.tChecked = "n"
		AND a.tSystem = "1"
	ORDER BY 
		a.id
	DESC LIMIT 100 ;
' ;
//echo "SQL=".$sql."<br>\n" ;
$rs = $conn->Execute($sql) ;
$total = $rs->RecordCount() ;
$count = 0 ;
$ans = "中華簡訊 API 檢查完成" ;
$body_txt = '' ;

//
//echo $rs->RecordCount();exit;
while (!$rs->EOF) {
	$id = $rs->fields['id'] ;												//欲查詢簡訊 tSMS_Check 的 id
	$ny = 'y' ;																//是否要繼續追蹤檢查訊息狀態 y:不檢查、n:繼續檢查
	
	// Query 參數設定
	$cht_acc = '10792' ;													//中華電信帳號
	$cht_pwd = '10792' ;													//中華電信密碼
	
	$Q_mobile = $rs->fields['tMSISDN'] ;									//收訊方手機號碼
	$Q_msgid = $rs->fields['tTaskID'] ;										//中華電信 message id

	//echo $Q_msgid ;
	if (preg_match("/^H/",$Q_msgid)) {
		//echo "Right!!\n" ;
		$url = 'https://imsp.emome.net:4443/imsp/sms/servlet/QuerySM?' ;		// https 版
		//$url = 'http://imsp.emome.net:8008/imsp/sms/servlet/QuerySM?' ;		// http 版
	
		$url .= 'account='.$cht_acc.'&password='.$cht_pwd ;
		$url .= '&to_addr_type=0&to_addr='.$Q_mobile.'&messageid='.$Q_msgid ;	
		##
	
		//進行 http query 動作
		$ch = curl_init() ;
	
		curl_setopt($ch,CURLOPT_HEADER,0) ;
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0) ;								//設定 SSL 連線用
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0) ;								//設定 SSL 連線用
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1) ;
		curl_setopt($ch,CURLOPT_URL,$url) ;
	
		$res = curl_exec($ch) ;
		curl_close($ch) ;
		##
		
		//檢核並取出 query 結果
		$res = str_replace("\n","",$res) ;
		if (preg_match("/<html><header><\/header><body>(.*)\|(.*)\|(.*)\|(.*)<br><\/body><\/html>/",$res,$_data)) {
			//取出反饋資料
			//echo $Q_msgid.'|'.$res ;
			$_tel = trim($_data[1]) ;					//收訊端手機號碼
			$code = trim($_data[2]) ;					//回傳代碼
			$done_time = trim($_data[3]) ;				//簡訊成功時間
			$description = trim($_data[4]) ;			//描述
		
			if (preg_match("/null/",$done_time)||($done_time == '')) {		//修改送達時間格式(無時間回傳)
				$done_time = '' ;
			}
			##
		
			//更新資料庫的簡訊狀態
			if ($code == '1') {												//訊息傳送中
				$ny  = 'n' ;
				$count ++ ;
			}
			else {															//其他狀態(正常或失敗)
				$ny = 'y' ;
			}
		
			$sql = '
				UPDATE 
					tSMS_Check
				SET
					tChecked="'.$ny.'",
					tCode="'.$code.'",
					tReason="'.cht_sms_query_code($code).'",
					tDrDateTime="'.$done_time.'"
				WHERE
					tTaskID = "'.$Q_msgid.'"
			' ;
		
			$conn->Execute($sql) ;
			//$count ++ ;
			
			//echo date("Y-m-d H:i:s").' [ '.$_tel." ] was updated!!\n" ; 
			echo $_txt = date("Ymd H:i:s").' 本次更新：TID:'.$Q_msgid.',TEL:'.$_tel.',ID:'.$id.',Code='.$code.',Reason='.cht_sms_query_code($code).',tChecked='.$ny."\n" ;
			$body_txt .= "<br>".$_txt ;
			
			$ans = "中華簡訊 API 檢查完成\n" ;
			##
			//exit ;
		}
		else {
			//echo date("Y-m-d H:i:s").' Could not open connection.'."\n" ; 
			echo '['.date("Y-m-d H:i:s").']'." 接收資料錯誤!!\n" ;  
			
			$ans = "中華簡訊 API 檢查(連線)失敗\n" ;
		}
		##
	}
	//sleep(5) ;
	$rs->MoveNext() ;
}

if (preg_match("/失敗/",$ans)) {
	$mobile = '0922785490' ;
	cht_sms_send($mobile,$ans) ;
}
//if (preg_match("/完成/",$ans)) {
else {
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

	//$mail->AddAddress('jiver@ms16.hinet.net','陳銘慶') ;
	$mail->AddBcc('jason.chen@twhg.com.tw','台灣房屋 陳銘慶') ;
	//$mail->AddAddress('cmc569@gmail.com') ;

	$mail->IsHTML(true) ;
	//$mail->Subject = 'API 簡訊查詢' ;
	$mail->Subject = $ans ;
	$mail->Body = '本次共有 '.number_format($total).' 筆查詢資料。尚有 '.number_format($count).' 筆簡訊待確認'."<br>\n" ;
	$mail->Body .= $body_txt ;
	$mail->Send() ;								//開始發送
}

?>
