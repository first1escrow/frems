<?php
require_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
// require_once dirname(dirname(__FILE__)).'/includes/openadodb.php' ;
// require_once dirname(dirname(__FILE__)).'/pushover.php' ;
// require_once dirname(__FILE__).'/openadodb_test.php' ;
//檢查是否已有排程執行中
// if (checkPS('read_taishin_api.php')) {
//         $msg = date("Ymd/His")." read_taishin_api 排程已在執行中！本次排程觸發取消..." ;
//         echo $msg."\n\n" ;
//         linePushMsg($msg) ;
//         // exit ;
// }
##

// UTF-8 轉 Big-5 判斷
Function is_utf8($string) {
        return preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E] # ASCII
        | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
        | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
        | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
        )*$%xs', $string) ;
} 

Function convert($content) {	
	if (is_utf8($content)) {
		return mb_convert_encoding($content, "Big5", "UTF-8");
	}
	else {
		return mb_convert_encoding($content, "UTF-8", "Big5");
	}
} 
##

$connType = 1 ;

//決定連線方式
if ($connType == 1) {
	//台新 API 網址
	$url = 'https://www.b2bank.com.tw/housetrust/ap2aphousetrt.jsp' ;
	##
	
	// shell_exec('curl https://www.b2bank.com.tw/ -v --insecure') ; 
	
	// 讀取銷帳檔(API版)
	$curl = curl_init() ;
	
	if (! $curl) {
		die( "Cannot allocate a new PHP-CURL handle" );
	}
	
	$data = '' ;
	$tf = true ;
	curl_setopt($curl, CURLOPT_HEADER, false) ;
	curl_setopt($curl, CURLOPT_URL, $url) ;
	curl_setopt($curl, CURLOPT_POST, true) ; 		// 啟用POST
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('vabid'=>'99360890','password'=>'tsFcde6Bew53qbz','actno'=>'20680100135997','filetype'=>'1')));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
	
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_SSLVERSION, 6);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
	$data = curl_exec($curl) ;
	// if (curl_error($curl)) $tf = false ;
	
	$_arr = array() ;
	$_arr = curl_getinfo($curl) ;
	// print_r($_arr) ;
	if ($_arr['http_code'] != '200') {
		$tf = false ;
		print_r(curl_error($curl)) ;
		echo "\n" ;
	}
	unset($_arr) ;
	
	curl_close($curl) ;

	$_t = explode("\n",$data) ;

	//
	if ($tf) {
		if (date('i') >= '00' && date('i') < '10') {
			//$po->setPushTxt('台新銷帳檔','讀取成功!!('.date("Y-m-d H:i:s").')') ;
			//$po->sendPush() ;
		}
		
		//將 API 取得資料備份至 server 中
		$fh = fopen(dirname(dirname(__FILE__))."/log/taishin/cron/EXWO".date("Ymd").".txt",'w') ;
		foreach ($_t as $k => $v) {
			fwrite($fh,$v."\n") ;
		}
		fclose($fh) ;
		##
		
	}
	else {
		// $po->setPushTxt('台新銷帳檔','網路連線失敗!!('.date("Y-m-d H:i:s").')') ;
		// $po->sendPush() ;
		// pushover('台新銷帳檔', '網路連線失敗!!('.date("Y-m-d H:i:s").')') ;
		linePushMsg('台新銷帳檔網路連線失敗!!('.date("Y-m-d/H:i").')') ;
	}
	//else $po->setPushTxt('讀取失敗!!','台新銷帳檔!!('.date("Y-m-d H:i:s").')') ; $po->sendPush() ;
	##

}
else {
	// 讀取銷帳檔(FTP版)
	$_file = 'EXWO'.date("Ymd").'.txt' ;
	$_t = file($_file) ;
	##
	
	// 如無資料
	//echo "X=".count($_t) ;
	if (preg_match("/No DATA/",$_t[0])) {
		echo convert("無資料 ".date("Y-m-d H:i:s")." \n") ;
		exit ;
	}
	##
}
##

//print_r ($_t) ;
//exit ;
// $_t[0] = '2068010013599710803140001IBB 7777777000000000000000000000000020000+000000000080000969880800294250';
for ($i = 0 ; $i < count($_t) ; $i ++) {
	//若非台新帳號，則跳過繼續執行下一行
	if (!preg_match("/^20680100135997/",$_t[$i])) {
		continue ;
	}
	##
	//因為有新的出款回應檔所以不撈取出款部分的帳務資訊，沖正要取的

	// if ((!preg_match("/^000000000000000$/",$_t6)) && (preg_match("/^000000000000000$/",$_t7))) {			//支出($_t6)不為0、存入($_t7)為0
	// 	$_t14 = $_t12 ;				// 若是、則將 ePayTitle 改存到 eRemark
	// 	$_t12 = '網路整批' ;		// 且將 ePayTitle 改成 網路整批
		
	// 	//辨別是否為利息帳務
	// 	if (preg_match("/^0096988000000008$/",$_t10)) {
	// 		$_t4 = '1560' ;			// 利息轉出代碼
	// 		$_t12 = '利息提領' ;	// 將 ePayTitle 改成 利息提領
	// 	}
	// }
	##
		
	$_line = $_t[$i] ;													// 讀取單筆(行)資料內容
	//$_line = iconv("big5","utf-8",$_line) ;							// 讀取單筆(行)資料內容(Big5->UTF8)
		
	$_line = preg_replace("/\r\n/","",$_line) ;
	$_line = preg_replace("/^\?/","",$_line) ;
		
	//取出銷帳檔欄位資訊
	$_t1 = substr($_line,0,14) ;										// 台新存款帳號(14碼)
	$_t2 = substr($_line,14,7) ;										// 交易日期(yyymmdd、7碼)
	$_t3 = substr($_line,21,4) ;										// 交易序號(流水號、4碼)
	$_t4 = preg_replace("/ +/","",substr($_line,25,4)) ;				// 交易代號(4碼)
	$_t5 = preg_replace("/ +/","",substr($_line,29,7)) ;				// 匯出行代號(7碼)
		
	$_t6 = substr($_line,36,15)	;										// 提領(借方金額、15碼)
	$_t7 = substr($_line,51,15) ;										// 存入(貸方金額、15碼)
	$_t8 = substr($_line,66,1) ; 										// 餘額正負號(1碼)
	$_t9 = substr($_line,67,15) ; 										// 餘額(15碼)
	$_t10 = preg_replace("/ +/","",substr($_line,82,14)) ;				// 帳號(客戶虛擬帳號、14碼)
	$_t10 = str_pad($_t10,16,'0',STR_PAD_LEFT) ;						// 將虛擬帳號補足16位
		
	$_t11 = substr($_line,96,1) ;										// 交易狀態(0:正常交易、1:更正交易、1碼)	
	$_t12 = preg_replace("/ +/","",convert(substr($_line,97,10))) ;		// 匯款申請人戶名(10碼)
	$_t13 = preg_replace("/ +/","",convert(substr($_line,107,11))) ;	// 交易日期+交易日序號
	##
	echo $_line."\r\n";
	
	// if ((!preg_match("/^000000000000000$/",$_t6)) && (preg_match("/^000000000000000$/",$_t7)) && $_t11 == 0) { //網路整批
	// 	continue;
	// }
	
	//取得本件狀態等相關資料
	$arr[$i]['account'] = $_t1 ;
	$arr[$i]['date'] = $_t2 ;
	$arr[$i]['sn'] = $_t3 ;
	$arr[$i]['certifiedId'] = $_t10 ;
	$arr[$i]['status'] = $_t11 ;
	$arr[$i]['eSerialNo'] = $_t13 ;
	##
	
	//辨別是否為出款帳務
	if ((!preg_match("/^000000000000000$/",$_t6)) && (preg_match("/^000000000000000$/",$_t7))) {			//支出($_t6)不為0、存入($_t7)為0
		$_t14 = $_t12 ;				// 若是、則將 ePayTitle 改存到 eRemark
		$_t12 = '沖正' ;		// 且將 ePayTitle 改成 網路整批
		$_t11 = 1;
		
		//辨別是否為利息帳務
		if (preg_match("/^0096988000000008$/",$_t10)) {
			$_t4 = '1560' ;			// 利息轉出代碼
			$_t12 = '利息提領' ;	// 將 ePayTitle 改成 利息提領
		}
	}
	else if ((preg_match("/^000000000000000$/",$_t6)) && (!preg_match("/^000000000000000$/",$_t7))) {		//支出($_t6)為0、存入($_t7)不為0
		$_t14 = '' ;
				
		//辨別是否為利息帳務
		if (preg_match("/^0096988000000008$/",$_t10)) {
			$_t4 = '1912' ;				// 利息存入代碼
			$_t14 = $_t12 ;				// 若是、則將 ePayTitle 改存到 eRemark
			$_t12 = '利息存入' ;		// 且將 ePayTitle 改成 利息存入
			
		}
		
		//若為退匯存入時，改寫付款人資料
		if (preg_match("/^8888888$/",$_t5)) {
			$_t12 = '台新退匯' ;
		}
		##
	}
	##
	if ($_t12 == '' && $_t4 == 'ATM') {
		$_t12 = 'ATM';
	}

	##
	//20680100135997 1080425 0056 OTC 7777777 000000009916700 000000000000000 + 000094245767700 96988080033736 0  
	//echo "$_t1 / $_t2 / $_t3 / $_t4 / $_t5 / $_t6 / $_t7 / $_t8 / $_t9 / $_t10 / $_t11 / $_t12 / $_t13 / $_t14 <br>\n";
	// $sql = "select * from tExpense where eAccount = '$_t1' and eTradeDate ='$_t2' and eTradeNum='$_t3' ";
	$sql = "SELECT * FROM tExpense WHERE eBank = 4 AND eTradeDate = '".$_t2."' AND eDebit = '".$_t6."' AND eLender ='".$_t7."' AND eTaishinTradeNum = '".$_t3."' AND eDepAccount = '".$_t10."' AND eTradeStatus = '".$_t11."'";
	// echo $sql."<br>\n";// exit ;
	$rs = $conn->Execute($sql);
	$_total = $rs->RecordCount();
	//echo $_total;exit;
	if ($_total == 0 && $_t3 != '0000') {
		$sql = "SELECT eTradeNum,eTotal FROM tExpense WHERE eBank = 4 AND eTradeDate = '".$_t2."' ORDER BY id DESC LIMIT 1"; //  eTradeDate = '".$_t2."'
		$rs = $conn->Execute($sql);
		$eTradeNum = str_pad(((int)$rs->fields['eTradeNum']+1), 4,0,STR_PAD_LEFT);
		$sql = "SELECT eTradeNum,eTotal FROM tExpense WHERE eBank = 4  ORDER BY id DESC LIMIT 1"; //  eTradeDate = 
		$rs = $conn->Execute($sql);
			$eTotal = (int)substr($rs->fields['eTotal'],0,13)+(int)substr($_t7,0,13)-(int)substr($_t6, 0,13);
			$eTotal = str_pad($eTotal."00", 15,0,STR_PAD_LEFT);
		

		$sql = '
			INSERT INTO 
				tExpense
			(
				eBank,
				eAccount,
				eTradeDate,
				eTradeNum,
				eTradeCode,
				eExportCode,
				eDebit,
				eLender,
				eMark,
				eTotal,
				eDepAccount,
				eTradeStatus,
				ePayTitle,
				eSerialNo,
				eRemark,
				eTaishinTradeNum
			)
			VALUES
			(
				"4",
				"'.$_t1.'",
				"'.$_t2.'",
				"'.$eTradeNum.'",
				"'.$_t4.'",
				"'.$_t5.'",
				"'.$_t6.'",
				"'.$_t7.'",
				"'.$_t8.'",
				"'.$eTotal.'",
				"'.$_t10.'",
				"'.$_t11.'",
				"'.$_t12.'",
				"'.$_t13.'",
				"'.$_t14.'",
				"'.$_t3.'"
			) ;
		' ;
		// echo "sql=".$sql."<br>\n" ;
		$conn->Execute($sql) ;

		$_time = date("Y-m-d H:i:s");
		echo $_t10 . " Successfully Updated!! $_time \n";
	}
	else {
		$_time = date("Y-m-d H:i:s");
		echo "No Upate!! $_time \n";
		//echo $_t10 . "無資料 $_time \n";
	}
}

//確認更新所有案件交易狀態並沖正
// for ($i = 0 ; $i < count($_t) ; $i ++) {
// 	//若非台新帳號，則跳過繼續執行下一行
// 	if (!preg_match("/^20680100135997/",$_t[$i])) {
// 		continue ;
// 	}
// 	##
	
// 	//確認取出該帳號是否存在資料庫
// 	$sql = '
// 		SELECT 
// 			* 
// 		FROM 
// 			tExpense 
// 		WHERE 
// 			eAccount="'.$arr[$i]['account'].'" 
// 			AND eTradeDate="'.$arr[$i]['date'].'" 
// 			AND eTradeNum="'.$arr[$i]['sn'].'" 
// 	;' ;
// 	$rs = $conn->Execute($sql) ;
// 	$tmp = $rs->fields ;
// 	##
	
// 	//依據案件資料進行更新與沖正
// 	if ($rs->RecordCount() > 0) {
// 		//確認更新案件狀態
// 		if ($arr[$i]['status'] != $tmp['eTradeStatus']) {
// 			$sql = '
// 				UPDATE 
// 					tExpense 
// 				SET 
// 					eTradeStatus="'.$arr[$i]['status'].'", 
// 					ePayTitle="沖正交易", 
// 					eSerialNo="'.$arr[$i]['eSerialNo'].'" 
// 				WHERE 
// 					id="'.$tmp['id'].'"
// 			' ;
// 			$conn->Execute($sql) ;
// 			$tmp['eTradeStatus'] = $arr[$i]['status'] ;				//更新交易狀態
// 			$tmp['eSerialNo'] = $arr[$i]['eSerialNo'] ;				//更新沖正交易線索
// 		}
// 		##
		
// 		//依據沖正案件線索找出被沖正資料
// 		if ($tmp['eTradeStatus'] == '1') {							//沖正案件
// 			$tradeDate = substr($tmp['eSerialNo'],0,7) ;			//沖正案件交易日期
// 			$tradeNum = substr($tmp['eSerialNo'],7) ;				//沖正案件交易序號
			
// 			$sql = 'UPDATE tExpense SET eTradeStatus="9", ePayTitle="沖正交易" WHERE eTradeDate="'.$tradeDate.'" AND eTradeNum="'.$tradeNum.'" ;' ;
// 			$conn->execute($sql) ;
// 		}
// 		##
// 	}
// 	##
	
// 	unset($tmp) ;
// }
##
?>
