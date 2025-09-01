<?php

//數字轉國字(1~9999)
Function no2ch($no) {
	$val = '' ;
	
	$mul = array('','十','百','千') ;
	$pat = array('','一','二','三','四','五','六','七','八','九','十') ;
	
	$arr = array() ;
	$maxLen = strlen($no) ;
	
	$j = 0 ;
	for ($i = $maxLen - 1 ; $i >= 0 ; $i --) {
		$ch = substr($no,$i,1) ;
		echo 'ch='.$ch."<br>\n" ;
		
		if (($ch == '0') && ($j > 0)) $arr[$j] = '零' ;
		else if ($ch == '0') $arr[$j] = '' ;
		else {

			$arr[$j] = $pat[$ch].$mul[$j] ;
			
		
		}
		$j ++ ;
	}
	print_r($arr) ;
	$val = implode('',array_reverse($arr)) ;
	
	$val = preg_replace("/[零]+/isu","零",$val) ;
	$val = preg_replace("/零+$/isu","",$val) ;
	
	//if ($val == '一十') $val = '十' ;
	$val = preg_replace("/^一十/","十",$val) ;
	
	return $val ;
	//return $pat[4] ;
}
##

$addr = '新竹市新竹市經國路三段92巷16弄47號14樓' ;
echo no2ch('104') ;
//echo '四' ;
?>