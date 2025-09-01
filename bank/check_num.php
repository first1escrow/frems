<?php
$cId = $_REQUEST["cId"];
if (!$cId) {
	$cId = '60001011400326' ;
}

//依據銀行別進行保證號碼合法性檢核
if (preg_match("/^60001/",$cId)) {		//一銀
	$n1 = substr($cId,0,1) * 3 ;
	$n2 = substr($cId,1,1) * 7 ;
	$n3 = substr($cId,2,1) * 9 ;
	$n4 = substr($cId,3,1) * 3 ;
	$n5 = substr($cId,4,1) * 7 ;
	$n6 = substr($cId,5,1) * 9 ;
	$n7 = substr($cId,6,1) * 3 ;
	$n8 = substr($cId,7,1) * 7 ;
	$n9 = substr($cId,8,1) * 9 ;
	$n10 = substr($cId,9,1) * 3 ;
	$n11 = substr($cId,10,1) * 7 ;
	$n12 = substr($cId,11,1) * 9 ;
	$n13 = substr($cId,12,1) * 3 ;
	$_k = ($n1 + $n2 + $n3 + $n4 + $n5 + $n6 + $n7 + $n8 + $n9 + $n10 + $n11 + $n12 + $n13) % 11 ;
	//echo "==>".$_k."=";
	
	if ($_k == 0) {
		$_t = 0 ;
	}
	else if ($_k == 1) {
		$_t = 1 ;
	}
	else {
		$_t = 11 - $_k ;
	}
}
else if (preg_match("/^9998[56]0/",$cId)) {	//永豐
	$n1 = substr($cId,0,1) * 9 ;
	$n2 = substr($cId,1,1) * 8 ;
	$n3 = substr($cId,2,1) * 7 ;
	$n4 = substr($cId,3,1) * 6 ;
	$n5 = substr($cId,4,1) * 5 ;
	$n6 = substr($cId,5,1) * 4 ;
	$n7 = substr($cId,6,1) * 3 ;
	$n8 = substr($cId,7,1) * 2 ;
	$n9 = substr($cId,8,1) * 1 ;
	$n10 = substr($cId,9,1) * 2 ;
	$n11 = substr($cId,10,1) * 3 ;
	$n12 = substr($cId,11,1) * 4 ;
	$n13 = substr($cId,12,1) * 5 ;
	$_k = ($n1 + $n2 + $n3 + $n4 + $n5 + $n6 + $n7 + $n8 + $n9 + $n10 + $n11 + $n12 + $n13) % 10 ;
	//echo "==>".$_k."="; 
	
	$_t = $_k ;
}
else if (preg_match("/^96988/",$cId)) {	//台新
	$n14 = substr($cId,0,1) ;
	$n13 = substr($cId,1,1) ;
	$n12 = substr($cId,2,1) ;
	$n11 = substr($cId,3,1) ;
	$n10 = substr($cId,4,1) ;
	$n9 = substr($cId,5,1) ;
	$n8 = substr($cId,6,1) ;
	$n7 = substr($cId,7,1) ;
	$n6 = substr($cId,8,1) ;
	$n5 = substr($cId,9,1) ;
	$n4 = substr($cId,10,1) ;
	$n3 = substr($cId,11,1) ;
	$n2 = substr($cId,12,1) ;
	
	$even_no = ($n14 + $n12 + $n10 + $n8 + $n6 + $n4 + $n2) * 3 ;		//偶數位相加 * 3
	
	$odd_no =  $n13 + $n11 + $n9 + $n7 + $n5 + $n3 ;					//奇數位相加
	
	$_t = substr(($even_no + $odd_no),-1,1) + 1 - 1 ;					//奇偶相加取個位數
	
	if ($_t > 0) {														//如個位數不為 0
		$_t = 10 - $_t ;												//取 10 的補數
	}
}
##

//檢核結果
$ans = '&nbsp;' ;
if ($cId) {
	//echo '_t='.$_t ;
	if ($_t >= 0) {
		if (preg_match("/$_t$/",$cId)) {
			$ans = '經驗算後檢核碼 = <span style="font-size:12pt;">"'.$_t.'"</span>，<br>與保證號碼 "'.$cId.'" 檢查碼 <span style="font-size:12pt;color:blue;font-weight:bold;">"相符"</span>!!' ;
		}
		else {
			$ans = '經驗算後檢核碼 =  <span style="font-size:12pt;">"'.$_t.'"</span>，<br>與保證號碼 "'.$cId.'" 檢查碼 <span style="font-size:12pt;color:red;font-weight:bold;">"不符"</span>!!' ;
		}
	}
	else {
		$ans = "保證號碼格式不正確!!<br>\n[ 60001 ==> 一銀 ]、[ 999850 ==> 永豐 ]、[ 96988 ==> 台新 ]" ;
	}
}
//echo "t=>".$_t;
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>虛擬帳號合法性檢查</title>
<link rel="stylesheet" href="colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<script>
$(document).ready(function() {
	$('[name="cId"]').focus() ;
	$('[name="cId"]').select() ;
}) ;

</script>
</head>
<body>
<form method="POST" name="myform">
<table style="border:1px groove;width:400px;">
	<tr>
		<td>欲檢查保證號碼：</td>
		<td><input type="text" name="cId" maxlength="14" value="<?=$cId?>"></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="text-align:right;"><input type="submit" value="　查詢　"></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;font-size:10pt;"><?=$ans?></td>
	</tr>
</table>
</form>
</body>
</html>