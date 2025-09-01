<?php
//亞太簡訊發送系統
Function send_sms($mobile,$subjects,$txt){
	$api_kind = "APIRTRequest" ;
	$url = 'xsms.aptg.com.tw' ;
	$fp = fsockopen($url,80,$errno,$errstr,30) ;
	if (!$fp) {
		echo 'Could not open connection.';
		return 0 ;
	}
	else {
		$xmlpacket ='<soap-env:Envelope xmlns:soap-env=\'http://schemas.xmlsoap.org/soap/envelope/\'> 
			<soap-env:Header/> 
			<soap-env:Body> 
				<Request>
					<MDN>0980013768</MDN> 
					<UID>firstEng</UID> 
					<UPASS>87973222</UPASS> 
					<Subject>'.$subjects.'</Subject> 
					<Retry>Y</Retry>
					<AutoSplit>Y</AutoSplit><Message>'.$txt.'</Message> 
					<MDNList><MSISDN>'.$mobile.'</MSISDN></MDNList> 
				</Request> 
			</soap-env:Body> 
			</soap-env:Envelope>' ;
		
		$contentlength = strlen($xmlpacket);
			
		$out = "POST /XSMSAP/api/".$api_kind." HTTP/1.1\r\n";
		$out .= "Host: 210.200.219.138\r\n";
		$out .= "Connection: close\r\n";
		$out .= "Content-type: text/xml;charset=utf-8\r\n";
		$out .= "Content-length: $contentlength\r\n\r\n";
		$out .= "$xmlpacket";

		fwrite($fp, $out);
		$theOutput='';
		while (!feof($fp)) {
			$theOutput .= fgets($fp, 128);
		}
			
		fclose($fp);
		$res = $theOutput;
		
		preg_match("/<Reason>(.*)<\/Reason><Code>(.*)<\/Code><MDN>(.*)<\/MDN><TaskID>(.*)<\/TaskID><RtnDateTime>(.*)<\/RtnDateTime>/",$res,$_data) ;
		
		$reason = $_data[1] ;
		$code = $_data[2] ;
		$mdn = $_data[3] ;
		$tid = $_data[4] ;
		$RDT = $_data[5] ;

			
		if (trim($code)=="0") {
			//echo date("Y-m-d H:i:s")." SMS has been sent!!\n" ;
			return 1 ;
		}
		else {
			//echo date("Y-m-d H:i:s")." SMS falured...\n" ;
			return 2 ;
		}
	}
}
##

//中華簡訊發送系統
Function cht_sms_send($mobile,$txt) {
	$acc_china = '10792' ;			//中華電信帳號
	$pwd_china = '10792' ;			//中華電信密碼
	$from_addr = '0911510792' ;		//發話方電話號碼
	$max_ch = 70 ;					//最大簡訊文字數量
	$sms_success = 0 ;				//發送成功簡訊數量
	$_error_code = '' ;				//簡訊錯誤碼
		
	$txt_big5 = n_to_w($txt) ;									//將訊息中的半形數字轉為全形數字
	$txt_big5 = mb_convert_encoding($txt_big5,'BIG5','UTF-8') ; //將簡訊內容轉成Big-5編碼 
	$max_len = mb_strlen($txt_big5,'big5') ;					//計算簡訊長度
	$_divid = 1 ;												//預設發送一則簡訊
		
	//若單封簡訊長度超長
	if ($max_len > $max_ch) {
		$_divid = ceil($max_len / $max_ch) ;					//簡訊發送次數(單筆內容多筆發送)
	}
	##
		
	//分批發送簡訊
	for ($i = 0 ; $i < $_divid ; $i ++) {
		$_start = $i * $max_ch ;
		$_big5_str = mb_substr($txt_big5,$_start,$max_ch,'big5') ;
		$sms_success ++ ;
			
		//https 版本
		$url = 'https://imsp.emome.net:4443/imsp/sms/servlet/SubmitSM' ;		//網址
		$url .= '?account='.$acc_china.'&password='.$pwd_china ;				//帳號密碼
		$url .= '&from_addr_type=0&from_addr='.$from_addr ;						//發話方手機號碼
		$url .= '&to_addr_type=0&to_addr='.$mobile ;							//發送至手機號碼
		$url .= '&msg_expire_time=0&msg_type=0' ;								//設定資料格式
		$url .= '&msg='.urlencode($_big5_str) ;									//發送內容
		##
			
		//http 版本
		/*
		$url = 'http://imsp.emome.net:8008/imsp/sms/servlet/SubmitSM' ;			//網址
		$url .= '?account='.$acc_china.'&password='.$pwd_china ;				//帳號密碼
		$url .= '&from_addr_type=0&from_addr='.$from_addr ;						//發話方手機號碼
		$url .= '&to_addr_type=0&to_addr='.$mobile ;							//發送至手機號碼
		$url .= '&msg_expire_time=0&msg_type=0' ;								//設定資料格式
		$url .= '&msg='.urlencode($_big5_str) ;									//發送內容
		*/
		##
			
		//預設簡訊 ID
		$messageid = $msgid = uniqid() ;
		##
			
		//開始發送簡訊
		//$res = $this->file_get_contents_curl($url,1) ;		// 1 : https 連線方式、2 : http 連線方式
		$ch = curl_init() ;
		
		curl_setopt($ch, CURLOPT_HEADER, 0) ;
	
		//選擇使用的 http 連線方式 1:https、2:http
		//if ($ver == 1) {
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0) ;
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0) ;
		//}
		##
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ; //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url) ;
	
		$res = curl_exec($ch) ;
		
		curl_close($ch) ;
		##
				
		//假資料測試
		//$res = "<html>\n<header>\n</header>\n<body>\n".$mobile.'|48|'.$messageid."|Success<br>\n</body>\n</html>" ;
		##
		
		//取得發送簡訊回傳之相關訊息
		$wSQL = 1 ;
		if (preg_match("/<html>\n<header>\n<\/header>\n<body>\n(.*)\|(.*)\|(.*)\|(.*)<br>\n<\/body>\n<\/html>/",$res,$_data)) {		//連線正常取得回傳訊息
			$_tel = trim($_data[1]) ;					//收訊端手機號碼
			$code = trim($_data[2]) ;					//回傳代碼
			$messageid = trim($_data[3]) ;				//中華電信簡訊 ID
			$description = trim($_data[4]) ;			//描述
			
			//if ($i == 1) { $code = 2 ; }	/////////////////////////////////////// 為了測試單筆多封簡訊 ////////////
			
			if ($code == '0') {
				echo "發送成功<br>\n" ;
			}
			else {
				$reason = '發送失敗' ;
				$_res = $reason.' -[ '.$url.' ]'."<br>\n" ;
				$_res .= '************ error messages ************'."<br>\n" ;
				$_res .= $res."<br>\n" ;
				$_res .= '****************************************'."<br>\n" ;
			
				echo $_res ;
			}
		}
		else {		//網路發生錯誤時之處置
			$reason = '發送失敗' ;
			$_res = $reason.' -[ '.$url." ] - [ 網路連線錯誤!! ] <br>\n" ;
			
			echo $_res ;
		}
		##
			
	}
	##
}
##

// 半形(narrow)、全形(wide)互換 -- 數字版
Function n_to_w($strs, $types = '0') {
	$nt = array(
		"0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
	) ;
	$wt = array(
		"０", "１", "２", "３", "４", "５", "６", "７", "８", "９"
	) ;

	if ($types == '0') {
		$strtmp = str_replace($nt,$wt,$strs) ;			// narrow to wide (半形轉全形)
	}
	else {
		$strtmp = str_replace($wt,$nt,$strs) ;			// wide to narrow (全形轉半形)
	}
	
	return $strtmp ;
}
##
	
//中華電信錯誤代碼解析
Function cht_sms_code($no=0) {
	// '77' 為網路錯誤所自行加入之錯誤碼
	$code_des = array(
		'0'=>'已發出、系統將開始發送簡訊',
		'2'=>'訊息傳送失敗',
		'3'=>'訊息預約時間超過48小時',
		'5'=>'訊息從Big-5轉碼到UCS失敗',
		'11'=>'參數錯誤',
		'12'=>'訊息的失效時間數值錯誤',
		'13'=>'SMS訊息的訊息種類不屬於合法的message type',
		'14'=>'用戶具備改發訊息權限，請填發訊號碼',
		'15'=>'簡訊號碼格式錯誤',
		'16'=>'系統無法執行msisdn<->subno，請稍後再試',
		'17'=>'系統無法找出對應此subno支電話號碼，請查明subno是否正確',
		'18'=>'請檢查受訊方號碼格式是否正確',
		'19'=>'受訊號碼數目超過系統限制(目前為20)',
		'20'=>'訊息長度不正確',
		'22'=>'帳號或是密碼錯誤',
		'23'=>'你登入的IP未在系統註冊',
		'24'=>'帳號已停用',
		'33'=>'企業預付帳號沒金額，請儲值',
		'34'=>'企業預付儲值系統發生介接錯誤，請洽服務人員',
		'35'=>'抱歉、企業預付系統扣款錯誤、請再試',
		'36'=>'抱歉、企業預付扣款系統鎖住，暫時無法使用、請再試',
		'37'=>'企業預付扣款帳號鎖住，暫時無法使用(可能多條連線同時發訊所產生、請再重試)',
		'41'=>'發訊內容含有系統不允許發送字集，請修改訊息內容再發訊',
		'43'=>'這個受訊號碼是空號(此錯誤碼只會發生在限發CHT的用戶發訊時產生)',
		'44'=>'無法判斷號碼是否屬於中華電信門號。無法決定費率，而停止發訊',
		'45'=>'放心講客戶餘額不足、無法發訊',
		'46'=>'無法決定計費客戶屬性、而停止服務',
		'47'=>'該特碼帳號無法提供預付式客戶使用',
		'48'=>'受訊客戶要求拒收加值簡訊、請不要重送',
		'49'=>'顯示於手機之發訊號碼格式不對',
		'50'=>'放心講系統扣款錯誤、請再試',
		'51'=>'預付客戶餘額不足、無法發訊',
		'52'=>'抱歉、預付式系統扣款錯誤、請再試',
		'77'=>'網路連線錯誤、請連絡相關人員'
	) ;
	
	if ($no < 0) {
		return '中華電信系統或是資料庫故障' ;
	}
	else {
		return $code_des[$no] ;
	}
}
##
?>