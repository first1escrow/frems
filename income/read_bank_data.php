<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

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

//計算異動保證號碼餘額
Function cal_c($_no,$_conn) {
	//計算入帳保證號碼總金額
	$aa = $sql = 'SELECT * FROM tExpense WHERE eClose="2" AND eDepAccount="'.$_no.'";' ;
	$rs = $_conn->Execute($sql) ;
	$_Expense_total = array() ;
	while (!$rs->EOF) {
		$_account = substr($rs->fields["eDepAccount"],2) ;
		$_eLender = (int)substr($rs->fields["eLender"],0,13) ;		//存入
		$_eLender += 1 - 1 ;
		$_eDebit = (int)substr($rs->fields["eDebit"],0,13) ;		//支出
		$_eDebit += 1 - 1 ;
				
		$_Expense_total[$_account] = $_Expense_total[$_account] + $_eLender - $_eDebit ;
		
		$rs->MoveNext();
	}
	//print_r($_Expense_total) ;
	##
	
	//計算出款保證號碼總金額並與入帳金額進行運算
	foreach ($_Expense_total as $key => $value) {
		$sql = 'SELECT tVR_Code,SUM(tMoney) as M FROM tBankTrans WHERE tVR_Code="'.$key.'" AND tExport ="1" AND tPayOk="1";' ;
		$rsx = $_conn->Execute($sql) ;
		$_money = $rsx->fields["M"] ;
		$_Expense_total[$key] = $_Expense_total[$key] - $_money ;
	}
	##
	
	//將保證號碼餘額寫入
	foreach ($_Expense_total as $key => $value) {
		$sql = 'UPDATE tContractCase SET cCaseMoney="'.$value.'" WHERE cEscrowBankAccount="'.$key.'";' ;
		//echo $sql."\n" ;
		$_conn->Execute($sql) ;
	}
	##
	
	//return $_no.'='.$_Expense_total[substr($_no,2)] ;
}

//一銀

##

//永豐
$url = 'https://ecapi.sinopac.com/FOBA/WriteOff.ashx?cmpcode=94983588&type=trans&date='.date("Ymd") ;

// 讀取銷帳檔(API版)
unset($_t) ;
unset($data) ;

$curl = curl_init($url) ;

if (! $curl) {
     die( "Cannot allocate a new PHP-CURL handle" );
}

$ch = curl_init($url) ;
curl_setopt($ch, CURLOPT_HEADER, false) ;
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
$data = curl_exec($ch) ;
//echo curl_error($ch) ;
curl_close($ch) ;

$_t = explode("\n",$data) ;
##
//print_r($_t) ; 
//exit ;

// 如無資料
if (preg_match("/No DATA/",$_t[0])) {
	echo convert("無資料 ".date("Y-m-d H:i:s")." \n") ;
	exit ;
}

//print_r ($_t) ;
//exit ;

for ($i = 0 ; $i < count($_t) ; $i ++) {
	//若非永豐帳號，則跳過繼續執行下一行
	if ((!preg_match("/^10401810001999/",$_t[$i])) && (!preg_match("/^12601800015999/",$_t[$i]))) {
		continue ;
	}
	##
	
	//$_line = $_t[$i] ;												// 讀取單筆(行)資料內容
	$_line = iconv("utf-8","big5",$_t[$i]) ;							// 讀取單筆(行)資料內容
	$_line = preg_replace("/\r\n/","",$_line) ;
	$_line = preg_replace("/^\?/","",$_line) ;

	$_t1 = substr($_line,0,14) ;										// 永豐存款帳號(14碼)
	$_t2 = substr($_line,14,7) ;										// 交易日期(yyymmdd、7碼)
	$_t3 = substr($_line,21,4) ;										// 交易序號(流水號、4碼)
	$_t4 = preg_replace("/ +/","",substr($_line,25,4)) ;				// 交易代號(4碼)
	$_t5 = preg_replace("/ +/","",substr($_line,29,7)) ;				// 匯出行代號(7碼)
		
	$_t51 = '' ;
	if (preg_match("/9999999/",$_t5)) {									// 若為退票時
		$_t51 = $_t5 ;													// 則記錄原始匯出行代號
		$_t5 = '6666666' ;												// 將永豐的退票代號改為"6666666"
	}
		
	$_t6 = substr($_line,36,15)	;										// 提領(借方金額、15碼)
	$_t7 = substr($_line,51,15) ;										// 存入(貸方金額、15碼)
	$_t8 = substr($_line,66,1) ; 										// 餘額正負號(1碼)
	$_t9 = substr($_line,67,15) ; 										// 餘額(15碼)
	$_t10 = preg_replace("/ +/","",substr($_line,82,14)) ;				// 帳號(客戶虛擬帳號、14碼)
	$_t10 = str_pad($_t10,16,'0',STR_PAD_LEFT) ;						// 將虛擬帳號補足16位
		
	$_t11 = substr($_line,96,1) ;										// 交易狀態(0:正常交易、1:更正交易、1碼)	
	$_t12 = preg_replace("/ +/","",convert(substr($_line,97,10))) ;		// 匯款申請人戶名(10碼)
		
	$_t13 = preg_replace("/ +/","",convert(substr($_line,107,8))) ;		// 交易摘要(存放中文摘要資訊，如利息、所得稅、退票等、8碼)
	$_t14 = preg_replace("/ +/","",convert(substr($_line,115,20))) ;	// 備註欄(20碼)
	$_t15 = preg_replace("/ +/","",convert(substr($_line,135,10))) ;	// 支票號碼(10碼)
	$_t16 = preg_replace("/ +/","",convert(substr($_line,145,35))) ;	// 永豐交易編號
		
	if($_t4=='') {
		if (preg_match("/利息存入/",$_t13)) {
			$_t4 = '1912' ;												// 填入利息存入代碼(套用一銀代碼)
		}
		else if (preg_match("/所得稅/",$_t13)) {
			$_t4 = '1920' ;												// 填入所得稅代碼(套用一銀代碼)
		}
	}
		
	if (preg_match("/利息提領/",$_t14)) {
		$_t4 = '1560' ;													// 填入利息支出代碼(套用一銀代碼)
	}
		
	// 寫入匯款申請人戶名
	if (preg_match("/8888888/",$_t5)) {
		$_t12 = '退匯存入' ;
	}
	else if (preg_match("/6666666/",$_t5)) {
		$_t12 = '票據退票' ;
	}
	else {
		if (preg_match("/更正/",$_t14)) {
			$_t12 = '更正交易' ;
		}
		else if (preg_match("/整批/",$_t14)) {
			$_t12 = '網路整批' ;
		}
	
		// 台幣匯款顯示戶名
		else if (preg_match("/台幣匯款/",$_t13)) {
			$tmp = explode('/',$_t14) ;
			$_t12 = @$tmp[1] ;
			if (!$_t12) {
				$_t12 = join('/',$tmp) ;
			}
			unset($tmp) ;
		}
		else {
			$_t12 = $_t13 ;
		}
	}
		
	//決定銀行代號
	$BankId = '' ;
	if ($_t1 == '10401810001999') {
		$BankId = '3' ;
	}
	else if ($_t1 == '12601800015999') {
		$BankId = '5' ;
	}
	##
		
	//echo "$_t1 / $_t2 / $_t3 / $_t4 / $_t5 / $_t6 / $_t7 / $_t8 / $_t9 / $_t10 / $_t11 / $_t12 / $_t13 / $_t14 / $_t15 / $_t16 <br>\n";
	$sql = "select * from tExpense where eAccount = '$_t1' and eTradeDate ='$_t2' and eTradeNum='$_t3' ";
	//echo $sql."<br>\n";
	$rs = $conn->Execute($sql);
	$_total = $rs->RecordCount();
	//echo $_total;exit;
	if ($_total == 0) {			
	
		// 當更正交易時，將對應的永豐序號案件狀態改為"9"
		if ($_t11=='1') {
			$sql = '
				UPDATE 
					tExpense
				SET
					eTradeStatus="9"
				WHERE
					eSerialNo="'.$_t16.'"
			' ;
			//echo "sql=".$sql."<br><br>\n\n" ;
			$conn->Execute($sql) ;
		}
		
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
				eSummary,
				eRemark,
				eCheckNo,
				eSerialNo
			)
			VALUES
			(
				"'.$BankId.'",
				"'.$_t1.'",
				"'.$_t2.'",
				"'.$_t3.'",
				"'.$_t4.'",
				"'.$_t5.'",
				"'.$_t6.'",
				"'.$_t7.'",
				"'.$_t8.'",
				"'.$_t9.'",
				"'.$_t10.'",
				"'.$_t11.'",
				"'.$_t12.'",
				"'.$_t13.'",
				"'.$_t14.'",
				"'.$_t15.'",
				"'.$_t16.'"
			) ;
		' ;
		//echo "sql=".$sql."<br>\n" ;
		$conn->Execute($sql) ;

		//計算異動保證號碼餘額
		if ($_t10 != '0000000000000000') {
			//echo "\n".cal_c($_t10,$conn)."\n" ;
			cal_c($_t10,$conn) ;
		}
		##

		$_time = date("Y-m-d H:i:s");
		//echo $_t10 . "Successfully Updated!! $_time \n";
		fwrite ($fh,$_t10."處理完成 ".$_time." \n") ;
	}
	else {
		$_time = date("Y-m-d H:i:s");
		//echo "No Upate!! $_time \n";
		//echo $_t10 . "無資料 $_time \n";
		fwrite ($fh,$_t10."無資料 ".$_time." \n") ;
	}
}
##

//台新


?>
