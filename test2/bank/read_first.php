<?php
require_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
// require_once dirname(dirname(__FILE__)).'/includes/openadodb.php' ;


##

$_file = "FIR".date("Ymd").".TXT";		
$local_file = dirname(dirname(__FILE__))."/log/first/cron/$_file";

require_once dirname(__FILE__)."/conn.php";
// $handle = fopen($local_file, 'a+');
$remote_file = "";

if (!file_exists($local_file)) {
	echo '檔案不存在';
	exit;
}

$fw = fopen($local_file, 'r');
$i= 0;
while (!feof($fw)) {

    // $str[]= fgets($fw);

    $_line =  fgets($fw);
    if ($_line != '') {
    	//讀取銷帳檔所有紀錄
		$_t1 = substr($_line,0,11);
		$_t2 = substr($_line,11,7);
		$_t3 = substr($_line,18,4);
		$_t4 = substr($_line,22,4);
		$_t5 = substr($_line,26,7);
		$_t6 = substr($_line,33,15); // 提領
		$_t7 = substr($_line,48,15); // 存入
		$_t8 = substr($_line,63,1); // 正負號
		$_t9 = substr($_line,64,15); //餘額
		$_t10 = substr($_line,79,16); //帳號
		$_t11 = substr($_line,95,1); //狀態
		//$_t12 = substr($_line,96,13); //戶名
		$_t12 = iconv("big5","utf-8//TRANSLIT",substr($_line,96,13)); //戶名
		$_t12 = trim(preg_replace("/　+/","",$_t12)) ;
		//echo "$_t1 / $_t2 / $_t3 / $_t4 / $_t5 / $_t6 / $_t7 / $_t8 / $_t9 / $_t10 / $_t11 / $_t12 \n";
		##
		
		//將每筆記錄存入陣列中
		$arr[$i]['eAccount'] = $_t1 ;
		$arr[$i]['eTradeDate'] = $_t2 ;
		$arr[$i]['eTradeNum'] = $_t3 ;
		$arr[$i]['eDepAccount'] = $_t10 ;
		$arr[$i]['eTradeStatus'] = $_t11 ;
		$i++;
		##
		
		$sql = "select * from tExpense where eAccount = '$_t1' and eTradeDate ='$_t2' and eTradeNum='$_t3' ";
		//echo $sql."\n";
		$rs = $conn->Execute($sql);
		$_total = $rs->RecordCount();
		//echo $_total;exit;
		if ($_total == 0) {
			//20200528 利息之前戶名欄位INT 跟交易代碼1912 ，前幾個月戶名變成存單息 交易代碼1793 ，所以做調整
			if ($_t12 == '存單息') {
				$_t12 = 'INT';
				$_t4 = '1912';
			}
			$record["eAccount"] = $_t1;
			$record["eTradeDate"] = $_t2;
			$record["eTradeNum"] = $_t3;
			$record["eTradeCode"] = $_t4;
			$record["eExportCode"] = $_t5;
			$record["eDebit"] = $_t6;
			$record["eLender"] = $_t7;
			$record["eMark"] = $_t8;
			$record["eTotal"] = $_t9;
			$record["eDepAccount"] = $_t10;
			$record["eTradeStatus"] = $_t11;
			$record["ePayTitle"] = $_t12;
			$ss = '
				INSERT INTO 
					tExpense
				(
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
					ePayTitle
				)
				VALUES
				(
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
					"'.$_t12.'"
				) ;
			' ;
			// echo $ss."\r\n";
			$conn->Execute($ss) ;
			$_time = date("Y-m-d H:i:s");
			echo $_t10." 處理完成 $_time \n";
		} else {
			$_time = date("Y-m-d H:i:s");
			//echo "無資料 $_time \n";
			echo $_t10." 無資料 $_time \n";
		}
    }
    
   
}

//比對銷帳檔紀錄交易狀態是否被變更並更正
for ($i = 0 ; $i < count($arr) ; $i ++) {
	$sql = '
	SELECT 
		* 
	FROM 
		tExpense 
	WHERE 
		eAccount="'.$arr[$i]['eAccount'].'" 
		AND eTradeDate="'.$arr[$i]['eTradeDate'].'" 
		AND eTradeNum="'.$arr[$i]['eTradeNum'].'" 
		AND eDepAccount="'.$arr[$i]['eDepAccount'].'"
	;' ;
	//echo "SQL=".$sql ;
	$rsC = $conn->Execute($sql) ;
	
	if ($arr[$i]['eTradeStatus']!=$rsC->fields['eTradeStatus']) {
		$sql = '
		UPDATE
			tExpense
		SET
			eTradeStatus="'.$arr[$i]['eTradeStatus'].'"
		WHERE
			id="'.$rsC->fields['id'].'"
			AND eAccount="'.$arr[$i]['eAccount'].'"
			AND eTradeDate="'.$arr[$i]['eTradeDate'].'"
			AND eTradeNum="'.$arr[$i]['eTradeNum'].'"
			AND eDepAccount="'.$arr[$i]['eDepAccount'].'"
		;' ;
		
		
		$conn->Execute($sql) ;
		echo date("Y-m-d H:i:s")." ".$arr[$i]['eDepAccount']." 交易狀態由 '".$rsC->fields['eTradeStatus']."' 改為 '".$arr[$i]['eTradeStatus']."' !!\n" ;
		
	}
	//else {
	//	echo date("Y-m-d H:i:s")." ".$arr[$i]['eDepAccount']."無需被更新交易狀態\n" ;
	//}
	
}

// print_r($str);
die;

//$_file = "FIR20130403.TXT";
//echo $_file."\n";
//https://210.59.232.39/FV09/27113/FIR20120409.TXT
// $curl = curl_init("https://210.59.232.39/FV09/27113/".$_file);
//https://mft.firstbank.com.tw/myfilegateway
//cu079004
// 004cu079
// $curl = curl_init("https://mft.firstbank.com.tw/Inbox/".$_file);
// if (! $curl) {
//      die( "Cannot allocate a new PHP-CURL handle" );
// }
// $data = '' ;
// $tf = true ;
// curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
// curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
// curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, 1);
// curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt ($curl, CURLOPT_HEADER, 0); // gets a HTTP page.
// $data = curl_exec($curl);
// echo $data;
// die;


// if (curl_error($curl)) $tf = false ;
// //echo $data." !!\n"; //輸出傳回值
// $pos = strpos($data, "404 - HTTP_NOT_FOUND");
// if ($pos > 0) { exit;}
// curl_close($curl);
// $_t = explode("\n",$data);
// // print_r($data);

// //pushover
// // $po = new pushover ;
// if ($tf) {
// 	if ((date('i') >= '00') && (date('i') < '10')) {
		
// 		echo '一銀銷帳檔', date("Y-m-d H:i:s").' 讀取成功!!('.date("Y-m-d H:i:s").')';
// 	}
	
// 	//
// 	$fh = fopen(dirname(__FILE__)."/cron/EXWO".date("Ymd_His").".TXT",'w') ;
// 	fwrite($fh,$data) ;
// 	fclose($fh) ;
// 	##
// }
// else {
	
// 	echo '一銀銷帳檔 網路連線失敗!!('.date("Y-m-d H:i:s").')';
// }
// //else $po->setPushTxt('讀取失敗!!','一銀銷帳檔!!('.date("Y-m-d H:i:s").')') ; $po->sendPush() ;

// ##

// //print_r($_t);
// exit;
// for ($i=0;$i<count($_t)-1;$i++){
// $_line = $buffer = $_t[$i];
// //$_line = iconv("big5","utf-8",$buffer);
// 		//讀取銷帳檔所有紀錄
// 		$_t1 = substr($_line,0,11);
// 		$_t2 = substr($_line,11,7);
// 		$_t3 = substr($_line,18,4);
// 		$_t4 = substr($_line,22,4);
// 		$_t5 = substr($_line,26,7);
// 		$_t6 = substr($_line,33,15); // 提領
// 		$_t7 = substr($_line,48,15); // 存入
// 		$_t8 = substr($_line,63,1); // 正負號
// 		$_t9 = substr($_line,64,15); //餘額
// 		$_t10 = substr($_line,79,16); //帳號
// 		$_t11 = substr($_line,95,1); //狀態
// 		//$_t12 = substr($_line,96,13); //戶名
// 		$_t12 = iconv("big5","utf-8//TRANSLIT",substr($_line,96,13)); //戶名
// 		$_t12 = trim(preg_replace("/　+/","",$_t12)) ;
// 		//echo "$_t1 / $_t2 / $_t3 / $_t4 / $_t5 / $_t6 / $_t7 / $_t8 / $_t9 / $_t10 / $_t11 / $_t12 \n";
// 		##
		
// 		//將每筆記錄存入陣列中
// 		$arr[$i]['eAccount'] = $_t1 ;
// 		$arr[$i]['eTradeDate'] = $_t2 ;
// 		$arr[$i]['eTradeNum'] = $_t3 ;
// 		$arr[$i]['eDepAccount'] = $_t10 ;
// 		$arr[$i]['eTradeStatus'] = $_t11 ;
// 		##
		
// 		$sql = "select * from tExpense where eAccount = '$_t1' and eTradeDate ='$_t2' and eTradeNum='$_t3' ";
// 		//echo $sql."\n";
// 		$rs = $conn->Execute($sql);
// 		$_total = $rs->RecordCount();
// 		//echo $_total;exit;
// 		if ($_total == 0) {
// 			$record["eAccount"] = $_t1;
// 			$record["eTradeDate"] = $_t2;
// 			$record["eTradeNum"] = $_t3;
// 			$record["eTradeCode"] = $_t4;
// 			$record["eExportCode"] = $_t5;
// 			$record["eDebit"] = $_t6;
// 			$record["eLender"] = $_t7;
// 			$record["eMark"] = $_t8;
// 			$record["eTotal"] = $_t9;
// 			$record["eDepAccount"] = $_t10;
// 			$record["eTradeStatus"] = $_t11;
// 			$record["ePayTitle"] = $_t12;
// 			$ss = '
// 				INSERT INTO 
// 					tExpense
// 				(
// 					eAccount,
// 					eTradeDate,
// 					eTradeNum,
// 					eTradeCode,
// 					eExportCode,
// 					eDebit,
// 					eLender,
// 					eMark,
// 					eTotal,
// 					eDepAccount,
// 					eTradeStatus,
// 					ePayTitle
// 				)
// 				VALUES
// 				(
// 					"'.$_t1.'",
// 					"'.$_t2.'",
// 					"'.$_t3.'",
// 					"'.$_t4.'",
// 					"'.$_t5.'",
// 					"'.$_t6.'",
// 					"'.$_t7.'",
// 					"'.$_t8.'",
// 					"'.$_t9.'",
// 					"'.$_t10.'",
// 					"'.$_t11.'",
// 					"'.$_t12.'"
// 				) ;
// 			' ;
// 			$conn->Execute($ss) ;
// 			//$conn->AutoExecute("tExpense", $record, 'INSERT');
// 			$_time = date("Y-m-d H:i:s");
// 			echo $_t10." 處理完成 $_time \n";
// 		} else {
// 			$_time = date("Y-m-d H:i:s");
// 			//echo "無資料 $_time \n";
// 			echo $_t10." 無資料 $_time \n";
// 		}
// }

// //比對銷帳檔紀錄交易狀態是否被變更並更正
// for ($i = 0 ; $i < count($arr) ; $i ++) {
// 	$sql = '
// 	SELECT 
// 		* 
// 	FROM 
// 		tExpense 
// 	WHERE 
// 		eAccount="'.$arr[$i]['eAccount'].'" 
// 		AND eTradeDate="'.$arr[$i]['eTradeDate'].'" 
// 		AND eTradeNum="'.$arr[$i]['eTradeNum'].'" 
// 		AND eDepAccount="'.$arr[$i]['eDepAccount'].'"
// 	;' ;
// 	//echo "SQL=".$sql ;
// 	$rsC = $conn->Execute($sql) ;
	
// 	if ($arr[$i]['eTradeStatus']!=$rsC->fields['eTradeStatus']) {
// 		$sql = '
// 		UPDATE
// 			tExpense
// 		SET
// 			eTradeStatus="'.$arr[$i]['eTradeStatus'].'"
// 		WHERE
// 			id="'.$rsC->fields['id'].'"
// 			AND eAccount="'.$arr[$i]['eAccount'].'"
// 			AND eTradeDate="'.$arr[$i]['eTradeDate'].'"
// 			AND eTradeNum="'.$arr[$i]['eTradeNum'].'"
// 			AND eDepAccount="'.$arr[$i]['eDepAccount'].'"
// 		;' ;
		
// 		//echo "update sql=".$sql."\n" ;
		
// 		$conn->Execute($sql) ;
// 		echo date("Y-m-d H:i:s")." ".$arr[$i]['eDepAccount']." 交易狀態由 '".$rsC->fields['eTradeStatus']."' 改為 '".$arr[$i]['eTradeStatus']."' !!\n" ;
		
// 	}
// 	//else {
// 	//	echo date("Y-m-d H:i:s")." ".$arr[$i]['eDepAccount']."無需被更新交易狀態\n" ;
// 	//}
	
// }
// ##

?>
