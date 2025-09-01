<?php
require_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;

//取得相差年度
Function countAge($nowDate, $beforeDate) {
	$diff = 0 ;
	
	if (!empty($nowDate) && !empty($beforeDate)) {
		$diffN = strtotime($nowDate) ;
		$diffB = strtotime($beforeDate) ;
		
		$diff = $diffN - $diffB ;
		$diff = floor($diff / 60 / 60 / 24 / 365) ;	
	}
	
	return $diff ;
}
##

//取得 CPI 指數
Function getCPI() {
	global $conn ;
	$cpi = 0 ;
	
	$sql = 'SELECT * FROM tCPI WHERE 1 ORDER BY cId DESC LIMIT 1;' ;
	$rs = $conn->Execute($sql) ;
	$cpi = $rs->fields['cCPI'] ;
	
	return $cpi ;
}
##

//判斷、計算使用稅則公式
Function taxRatio($money1, $money2, $years, $level, $type = 1) {
	$tax = 0 ;
	
	if (preg_match("/^\d+$/",$years) && $level) {
		$ratio = array(0, 0) ;
		
		if ($years < 20) $ratio = getLevel($level, 'A') ;								//持有時間小於20年
		else if (($years >= 20) && ($years < 30)) $ratio = getLevel($level, 'B') ;		//持有時間大於等於20年且小於30年
		else if (($years >= 30) && ($years < 40)) $ratio = getLevel($level, 'C') ;		//持有時間大於等於30年且小於40年
		else  $ratio = getLevel($level, 'D') ;											//持有時間大於等於40年
		
		//計算土增稅額(一般稅率)
		if ($type == 1) $tax = ($money2 * $ratio[0]) - ($money1 * $ratio[1]) ;
		##
		
		//計算土增稅額(自用住宅稅率稅則)
		else if ($type == 2) $tax = $money2 * 0.1 ;
		##
	}
	
	return $tax ;
}
##

//取得級別
Function getLevel($level, $y) {
	$taxA = 0 ;
	$taxB = 0 ;
	
	$level = $level + 1 - 1 ;
	if (($level >= 0) && ($level < 1)) {				//第一級、0 ~ 1 倍
		if ($y == 'A') {
			$taxA = 0.2 ;
			$taxB = 0 ;
		}
		else if ($y == 'B') {
			$taxA = 0.2 ;
			$taxB = 0 ;
		}
		else if ($y == 'C') {
			$taxA = 0.2 ;
			$taxB = 0 ;
		}
		else if ($y == 'D') {
			$taxA = 0.2 ;
			$taxB = 0 ;
		}
	}
	else if (($level >= 1) && ($level <= 2)) {			//第二級、1(含) ~ 2(含) 倍
		if ($y == 'A') {
			$taxA = 0.3 ;
			$taxB = 0.1 ;
		}
		else if ($y == 'B') {
			$taxA = 0.28 ;
			$taxB = 0.08 ;
		}
		else if ($y == 'C') {
			$taxA = 0.27 ;
			$taxB = 0.07 ;
		}
		else if ($y == 'D') {
			$taxA = 0.26 ;
			$taxB = 0.06 ;
		}
	}
	else if ($level > 2) {								//第三級、2 倍以上
		if ($y == 'A') {
			$taxA = 0.4 ;
			$taxB = 0.3 ;
		}
		else if ($y == 'B') {
			$taxA = 0.36 ;
			$taxB = 0.24 ;
		}
		else if ($y == 'C') {
			$taxA = 0.34 ;
			$taxB = 0.21 ;
		}
		else if ($y == 'D') {
			$taxA = 0.32 ;
			$taxB = 0.18 ;
		}
	}
	
	return array($taxA, $taxB) ;
}
##

//計算一般土增稅
/* 
前次移轉年月：$e
土地公告現值：$f
前次移轉現值：$g
土地面積	：$i
移轉比例分子：$n
移轉比例分母：$m
*/
Function calTax($e, $f, $g, $i, $n = 0, $m = 0, $type = 1) {
	// echo '前次移轉年月:'.$e."_土地公告現值:".$f."_前次移轉現值:".$g."_土地面積:".$i."_移轉比例分子:".$n."_移轉比例分母:".$m;
	// echo "<br>";
	$tax = 0 ;
	$level = 0 ;
	$money1 = 0 ;
	$money2 = 0 ;
	
	//取得 CPI 值
	$cpi = getCPI() ;

	// echo '消費者物價總指數:'.$cpi."_";

	
	##
	
	//取得相差時間(年)
	if ($e == '0000-00-00') $e = '' ;
	
	if (preg_match("/^\d{4}\-\d{2}/",$e)) {
		$tmp = explode('-', $e) ;
		
		$years = countAge(date("Y-m-d"), $tmp[0].'-'.$tmp[1].'-01') ;
		unset($tmp) ;
	}
	else $years = 0 ;
	##
	
	//轉移面積
	if ($m != 0) {
		$_area = round(($i * $n / $m), 2) ;
	}else{
		$_area = 0;
	}
	
	// echo '轉移面積 = '.$_area."<br>" ;
	##
	
	//轉移現值
	$_money = $f * $_area ;
	// echo '轉移現值 = '.$_money."<br>" ;
	##
	
	//前次移轉現值
	$money1 = round($g * $cpi / 100 * $_area) ;
	// echo '前次移轉現值 = '.$money1."<br>" ;
	##
	
	//漲價總額
	$money2 = $_money - $money1 ;
	// echo '漲價總額 = '.$money2."<br>" ;
	##
	
	//漲價倍數
	if ($money1 != 0) {
		$level = round(($money2 / $money1), 3) ;
	}else{
		$level = 0;
	}
	
	// echo '漲價倍數 = '.$level."<bR>" ;
	##
	
	//應繳土增稅額
	$tax = round(taxRatio($money1, $money2, $years, $level, $type)) ;

	// echo '應繳土增稅額 ='.$tax."<bR>" ;
	##
	
	return $tax ;
}
##

//計算履保案件土增稅額
Function calCase($cid) {
	global $conn ;
	$tax = 0 ;
	
	//
	$data = array() ;
	
	// $sql = 'SELECT * FROM tContractLand WHERE cCertifiedId = "'.$cid.'" ORDER BY cId ASC;' ;
	$sql = 'SELECT
				cp.cMoveDate,
				cl.cMoney,
				cp.cLandPrice,
				cl.cMeasure,
				cp.cPower1,
				cp.cPower2
			FROM
				tContractLand AS cl
			LEFT JOIN
			   tContractLandPrice AS cp ON cp.cCertifiedId=cl.cCertifiedId AND cp.cLandItem=cl.cItem
			WHERE
				cp.cCertifiedId = "'.$cid.'"
			ORDER BY cp.cLandItem,cp.cItem ASC';

	$rs = $conn->Execute($sql) ;
	
	while (!$rs->EOF) {
		if (!empty($rs->fields['cMeasure'])) {
			if ($rs->fields['cCategory'] == '') {
				$rs->fields['cCategory'] = 1;
			}
			$data[] = array(
				'e' => $rs->fields['cMoveDate'],
				'f' => $rs->fields['cMoney'],
				'g' => $rs->fields['cLandPrice'],
				'i' => $rs->fields['cMeasure'],
				'n' => $rs->fields['cPower1'],
				'm' => $rs->fields['cPower2']
			) ;
		}
		
		$rs->MoveNext() ;
	}
	##
	// echo "<pre>";
	// print_r($data);
	//計算案件土增稅總額
	foreach ($data as $k => $v) {
		$tax += calTax($v['e'], $v['f'], $v['g'], $v['i'], $v['n'], $v['m'], 1) ;
	}
	##
	
	return $tax ;
}

##

//echo number_format(calTax('1991-02', '120000', '10000', '100', '1'))."\n" ;
//echo number_format(calCase('005006601'))."\n" ;
?>