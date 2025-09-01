<?php
include_once '../../configs/config.class.php';
include_once 'class/contract.class.php';
include_once '../../openadodb.php' ;
// include_once '../../js/IDCheck.js' ;

$cid = addslashes($_POST['cid']);

$contract = new Contract();
$data_otherbuyer = $contract->GetOthers($cid,1); //買
$ck = 0; //0:正確 1:買方錯誤 2:賣方錯誤

for ($i=0; $i < count($data_otherbuyer); $i++) { 
	// $data_otherbuyer[$i]['cIdentifyId'] = 'AA20060243';
	
	if (!checkUID($data_otherbuyer[$i]['cIdentifyId'])) {
		$ck = 1;
	}elseif ($data_otherbuyer[$i]['cRegistZip']=='' ) {
		$ck = 1;
	}elseif ($data_otherbuyer[$i]['cRegistAddr']=='') {
		$ck = 1;
	}elseif ($data_otherbuyer[$i]['cBaseZip']=='') {
		$ck = 1;
	}elseif ($data_otherbuyer[$i]['cBaseAddr']=='') {
		$ck = 1;
	}

}

$data_otherowner = $contract->GetOthers($cid,2); //賣


for ($i=0; $i < count($data_otherowner); $i++) { 
	// $data_otherbuyer[$i]['cIdentifyId'] = 'AA20060243';
	
	if (!checkUID($data_otherowner[$i]['cIdentifyId'])) {
		$ck = 2;
	}elseif ($data_otherowner[$i]['cRegistZip']=='' ) {
		$ck = 2;
	}elseif ($data_otherowner[$i]['cRegistAddr']=='') {
		$ck = 2;
	}elseif ($data_otherowner[$i]['cBaseZip']=='') {
		$ck = 2;
	}elseif ($data_otherowner[$i]['cBaseAddr']=='') {
		$ck = 2;
	}

}

echo $ck;
die();


function checkUID($sn) {
	$result = false ;
	
	if (mb_strlen($sn)== 8) {			//檢查統一編號
		$result = UNID($sn) ;

	}
	else if (mb_strlen($sn) == 10) {

		

		$sn = strtoupper($sn);		//將英文字母設定為大寫
		
		$reg = "/^[A-Z]{1}[A-D]{1}[0-9]{8}$/" ;
		if (preg_match($reg,$sn)) {			//檢查居留證字號

			
			$result = RID($sn) ;
		}
		else {						//檢查身分證字號

			
			$result = PID($sn) ;
		}
	}
	
	return $result ;
}


/* 統一編號檢核 */
function UNID($sn) {
	$cx = array(1,2,1,2,1,2,4,1) ;		//驗算基數
	$sum = 0 ;
	if (mb_strlen($sn) != 8) {
		// echo "統編錯誤，要有 8 個數字";
		return false ;
	}
	
	$cnum[0] = substr($sn, 0,1);
	$cnum[1] = substr($sn, 1,1);
	$cnum[2] = substr($sn, 2,1);
	$cnum[3] = substr($sn, 3,1);
	$cnum[4] = substr($sn, 4,1);
	$cnum[5] = substr($sn, 5,1);
	$cnum[6] = substr($sn, 6,1);
	$cnum[7] = substr($sn, 7,1);
	for ($i = 0 ; $i <= 7 ; $i ++) {
		if (ord($cnum[$i])< 48 || ord($cnum[$i])  > 57) {

			// echo "統編錯誤，要有 8 個 0-9 數字組合";
			return false ;
		}
		$sum += cc($cnum[$i] * $cx[$i]) ;		//加總運算碼結果
	}
	
	if ($sum % 10 == 0) {
		
		// echo "統一編號：".$sn." 正確!";
		return true ;
	}
	else if ($cnum[6] == 7 && ($sum + 1) % 10 == 0) {
		// echo "統一編號：".$sn." 正確!";
		return true ;
	}
	else {
		// echo "統一編號：".$sn." 錯誤!";
		return false ;
	}
}
////
/* 計算數字大於 10 之處理 */
function cc($n){
  if ($n > 9) {
    $s = $n + "";

    $n1 = substr($s, 0,1) * 1;
    $n2 = substr($s, 1,2) * 1;
    $n = $n1 + $n2;
  }
  return $n;
}


function PID($sn) {	
	// echo $sn;
	/* 定義字母對應的數字 */
	$a = array ('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z') ;
	$b = array ('10','11','12','13','14','15','16','17','34','18','19','20','21','22','35','23','24','25','26','27','28','29','32','30','31','33') ;
	$max = count($a) ;
	$alphabet = array() ;
	for ($i = 0 ; $i < $max ; $i ++) {
		$alphabet[$i] = array($a[$i],$b[$i]) ;
	}
	////
	
	$sn = strtoupper($sn);			//將英文字母設定為大寫;
	$snLen = mb_strlen($sn);			//計算字數長度
	
	/* 若號碼長度不等於10，代表輸入長度不合格式 */
	if ($snLen != 10) {
		//alert('輸入字號長度不正確!!') ;
		return false ;
	}
	////
	
	/* 取出第一個英文字母 */
	$ch = substr($sn,0,1) ;
	$chVal = '' ;
	for ($i = 0 ; $i < $max ; $i ++) {
		if ($alphabet[$i][0] == $ch) {
			$chVal = $alphabet[$i][1] ;
			break ;
		}
	}
	////
	
	/* 取出檢查碼 */
	$lastch = substr($sn,-1,1) ;
	////
	
	$ch1 = substr($chVal,0,1) ;		//十位數
	$ch2 = substr($chVal,1,1) ;		//個位數
	
	$_val = ($ch2 * 9) + $ch1 ;			//個位數 x 9 再加上十位數
	$_val = $_val % 10 ;						//除以10取餘數
	
	/* 計算檢核碼 */
	$t1 = $_val * 1 ;
	$t2 = substr($sn,1,1) * 8 ;
	$t3 = substr($sn,2,1) * 7 ;
	$t4 = substr($sn,3,1) * 6 ;
	$t5 = substr($sn,4,1) * 5 ;
	$t6 = substr($sn,5,1) * 4 ;
	$t7 = substr($sn,6,1) * 3 ;
	$t8 = substr($sn,7,1) * 2 ;
	$t9 = substr($sn,8,1) * 1 ;
	
	$checkCode = ($t1 + $t2 + $t3 + $t4 + $t5 + $t6 + $t7 + $t8 + $t9) % 10 ;
	if ($checkCode == 0) {
		$checkCode = 0 ;
	}
	else {
		$checkCode = 10 - $checkCode ;			//檢查碼
	}
	////
	// echo $checkCode."-".$lastch."\r\n";
	/* 比對檢核碼是否相符 */
	if ($checkCode == $lastch) {
		//alert('checkCode=' + checkCode) ;
		return true ;
	}
	else {
		//alert('checkCode<>' + checkCode) ;
		return false ;
	}
	////
}


/* 居留證字號檢核 */
function RID($sn) {	
	/* 定義字母對應的數字 */
	$a = array ('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z') ;
	$b = array ('10','11','12','13','14','15','16','17','34','18','19','20','21','22','35','23','24','25','26','27','28','29','32','30','31','33') ;
	$max = count($a) ;
	$alphabet = array() ;
	for ($i = 0 ; $i < $max ; $i ++) {
		$alphabet[$i] = array($a[$i],$b[$i]) ;
	}
	////
	
	$sn = strtoupper($sn);;			//將英文字母設定為大寫
	$snLen = mb_strlen($sn) ;			//計算字數長度
	
	/* 若號碼長度不等於10，代表輸入長度不合格式 */
	if ($snLen != 10) {
		//alert('輸入字號長度不正確!!') ;
		return false ;
	}
	////
	
	/* 取出英文字母 */
	$ch1 = substr($sn,0,1) ;		//
	$ch2 = substr($sn,1,1) ;		//
	$chVal1 = '' ;
	$chVal2 = '' ;
	for ($i = 0 ; $i < $max ; $i ++) {
		/* 取出第一個英文字母對應的數值 */
		if ($alphabet[$i][0] == $ch1) {
			$chVal1 = $alphabet[$i][1] ;
			//break ;
		}
		////
		
		/* 取出第二個英文字母對應的數值 */
		if ($alphabet[$i][0] == $ch2) {
			$chVal2 = $alphabet[$i][1] ;
			//break ;
		}
		////
	}
	////
	
	/* 取出檢查碼 */
	$lastch = substr($sn,-1,1) ;
	////
	
	/* 第一碼英文字的轉換 */
	$ch1 = substr($chVal1,0,1) ;		//十位數
	$ch2 = substr($chVal1,1,1) ;		//個位數
	$t0 = ($ch2 * 9 + $ch1) % 10 ;
	////
	
	/* 第二碼英文字的轉換 */
	$_val =substr($chVal2,-1,1) * 1 ;	//個位數
	////
	
	/* 計算檢核碼 */
	$t1 = $_val * 8 ; 
	$t2 = substr($sn,2,1) * 7 ;
	$t3 = substr($sn,3,1) * 6 ;
	$t4 = substr($sn,4,1) * 5 ;
	$t5 = substr($sn,5,1) * 4 ;
	$t6 = substr($sn,6,1) * 3 ;
	$t7 = substr($sn,7,1) * 2 ;
	$t8 = substr($sn,8,1) * 1 ;
	
	$checkCode = $t0 + $t1 + $t2 + $t3 + $t4 + $t5 + $t6 + $t7 + $t8 + 1 - 1 ;
	$checkCode = 10 - ($checkCode % 10) ;
	
	/* 比對檢核碼是否相符 */
	if ($checkCode == $lastch) {
		return true ;
	}
	else {
		return false ;
	}
	////
}
?>