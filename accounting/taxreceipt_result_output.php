<?php
require_once('../bank/Classes/PHPExcel.php');
require_once('../bank/Classes/PHPExcel/Writer/Excel2007.php');
include_once '../session_check.php' ;
require_once dirname(__DIR__).'/first1DB.php';

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
        )*$%xs', $string);
} 

Function convert($content){	
	if (is_utf8($content)){
		return mb_convert_encoding($content, "Big5", "UTF-8");
	} else {
		return mb_convert_encoding($content, "UTF-8", "Big5");
	}
} 
// 半形轉全形
Function n_to_w($strs, $types = '0'){  // narrow to wide , or wide to narrow
	$nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
	);
	$wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
	);
 
	if ($types == '0') {
		// narrow to wide
		$strtmp = str_replace($nt, $wt, $strs);
	}
	else {
		// wide to narrow
		$strtmp = str_replace($wt, $nt, $strs);
	}
	return $strtmp;
}


Function regular_w($_str,$_max_char=20,$_str_type="utf-8") {
	$_str = preg_replace("/ /","　",$_str) ;
	$_max = mb_strlen($_str,$_str_type) ;
	$_max = $_max_char - $_max ;
	
	for ($index = 0 ; $index < $_max ; $index ++) {
		$_str .= "　" ;
	}
	return $_str ;
}

//計算實際金額歸屬
Function money_belong($_c = 0 ,$_b = 0) {
	if (($_c > 0)&&($_b <= 0)) {
		$_interest = $_c ;
	}
	else if (($_c <= 0)&&($_b > 0)) {
		$_interest = $_b ;
	}
	else if (($_c > 0)&&($_b > 0)) {
		$_interest = $_c + $_b ;
	}
	return $_interest ;
}

$unit_id = '53549920' ;
$feedback_year = trim(addslashes($_POST['yr'])) ;

$conn = new first1DB();

// 買賣方利息
$tmp = explode('-',$feedback_year ) ;
$f_date = $tmp[0].'-01-01 00:00:00' ;
$e_date = $tmp[0].'-12-31 23:59:59' ;
unset($tmp) ;
		
// 取得特定保證號碼
$sql = '
	SELECT 
		DISTINCT cCertifiedId
	FROM 
		tContractCase
	WHERE
		cFeedbackDate>="'.$f_date.'" AND cFeedbackDate<="'.$e_date.'" 
	ORDER BY
		cFeedbackDate
	ASC;
' ;
// echo "SQL=".$sql ;
		
// 取得年度範圍內的保證號碼資料
$rel = $conn->all($sql);
$max = count($rel);

for ($i = 0 ; $i < $max ; $i ++) {
	$list[$i] = $rel[$i];
	$list[$i]['sn'] = $list[$i]['cCertifiedId'] ;		//將保證號碼複製
			
	// 取得保證號碼利息之扣繳憑單對象
	$sql = '
		SELECT
			inv.cTaxReceiptTarget as cTaxReceiptTarget,
			cas.cEndDate as cEndDate
		FROM 
			tContractInvoice AS inv
		JOIN
			tContractCase AS cas ON cas.cCertifiedId=inv.cCertifiedId
		WHERE
			inv.cCertifiedId="'.$list[$i]['cCertifiedId'].'"
	' ;
	$tmp = $conn->one($sql);
	
	$list[$i]['obj'] = $tmp['cTaxReceiptTarget'] ;
	$list[$i]['cEndDate'] = $tmp['cEndDate'] ;
	unset($tmp) ;
	##
	
	// 取得利息資料
	$sql = '
		SELECT
			cInterest,
			bInterest,
			cTax,
			bTax,
			cTaxTitle,
			bTaxTitle
		FROM
			tChecklist
		WHERE
			cCertifiedId="'.$list[$i]['cCertifiedId'].'"
	' ;
	$tmp = $conn->one($sql);
	
	if ($tmp['cTaxTitle']!='代扣利息所得稅') { $tmp['cTax'] = 0 ; }
	if ($tmp['bTaxTitle']!='代扣利息所得稅') { $tmp['bTax'] = 0 ; }
	$list[$i]['cInterest'] = $tmp['cInterest'] + 1 - 1 ;
	$list[$i]['bInterest'] = $tmp['bInterest'] + 1 - 1 ;
	$list[$i]['cTax'] = $tmp['cTax'] + 1 - 1 ;
	$list[$i]['bTax'] = $tmp['bTax'] + 1 - 1 ;
	
	unset($tmp) ;
	##
}

$j = 0 ;
for ($i = 0 ; $i < $max ; $i ++) {
	// 取得買賣方基本資料
	unset($tables) ;
	switch($list[$i]['obj']) {
		case '2':													// 買方
				$tables[0] = ' tContractBuyer ' ;
				break ;
		case '4':													// 買賣方
				$tables[0] = ' tContractOwner ' ;
				$tables[1] = ' tContractBuyer ' ;
				break ;
		default:													// 其他(賣方)
				$tables[0] = ' tContractOwner ' ;
				break ;
	}
	
	foreach ($tables as $key => $val) {
		$sql = '
			SELECT 
				cIdentifyId,
				cCategoryIdentify,
				cName, 
				cCountryCode,
				cTaxtreatyCode,
				cResidentLimit,
				cPaymentDate,
				SUBSTR(cRegistZip,1,3) as cRegistZip,
				(SELECT zCity FROM tZipArea WHERE zZip=SUBSTR(a.cRegistZip,1,3)) as cRegistCity,
				(SELECT zArea FROM tZipArea WHERE zZip=SUBSTR(a.cRegistZip,1,3)) as cRegistArea,
				cRegistAddr,
				SUBSTR(cBaseZip,1,3) as cBaseZip,
				(SELECT zCity FROM tZipArea WHERE zZip=SUBSTR(a.cBaseZip,1,3)) as cBaseCity,
				(SELECT zArea FROM tZipArea WHERE zZip=SUBSTR(a.cBaseZip,1,3)) as cBaseArea,
				cBaseAddr
			FROM
				'.$val.' AS a
			WHERE
				cCertifiedId="'.$list[$i]['cCertifiedId'].'"
		' ;
		$tmp = $conn->one($sql);

		$arr3[$j] = $list[$i] ;
		
		$arr3[$j]['cIdentifyId'] = $tmp['cIdentifyId'] ;
		$arr3[$j]['cCategoryIdentify'] = $tmp['cCategoryIdentify'] ;
		$arr3[$j]['cName'] = $tmp['cName'] ;
		$arr3[$j]['cCountryCode'] = $tmp['cCountryCode'] ;
		$arr3[$j]['cTaxtreatyCode'] = $tmp['cTaxtreatyCode'] ;
		$arr3[$j]['cResidentLimit'] = $tmp['cResidentLimit'] ;
		$arr3[$j]['cPaymentDate'] = $tmp['cPaymentDate'] ;
		$arr3[$j]['cRegistZip'] = $tmp['cRegistZip'] ;
		$arr3[$j]['cRegistCity'] = $tmp['cRegistCity'] ;
		$arr3[$j]['cRegistArea'] = $tmp['cRegistArea'] ;
		$arr3[$j]['cRegistAddr'] = $tmp['cRegistAddr'] ;
		$arr3[$j]['cBaseZip'] = $tmp['cBaseZip'] ;
		$arr3[$j]['cBaseCity'] = $tmp['cBaseCity'] ;
		$arr3[$j]['cBaseArea'] = $tmp['cBaseArea'] ;
		$arr3[$j]['cBaseAddr'] = $tmp['cBaseAddr'] ;
		$arr3[$j]['iden'] = '1' ;	//利息資料別
		
		/*
		if ($val == 'tContractBuyer') {
			$arr3[$j]['cInterest'] = $arr3[$j]['bInterest'] ;
			$arr3[$j]['cTax'] = $arr3[$j]['bTax'] ;
		}
		*/
		
		$arr3[$j]['cInterest'] = money_belong($list[$i]['cInterest'],$list[$i]['bInterest']) ;
		$arr3[$j]['cTax'] = money_belong($list[$i]['cTax'],$list[$i]['bTax']) ;
		
		unset($tmp) ;
		
		$j ++ ;
	}
}

unset($list) ;
$max = count($arr3) ;
$j = 0 ;
$arr = array() ;
		
for ($i = 0 ; $i < $max ; $i ++) {
	if ($arr3[$i]['cInterest']>0) {		//利息需大於零
		$arr[$j++] = $arr3[$i] ;
	}
}
unset($arr3) ;	

//合併不同店家、相同回饋對象者
$max = count($arr) ;

//依據身份證號碼排序
for ($i = 0 ; $i < $max ; $i ++) {
	for ($j = 0 ; $j < $max - 1 ; $j ++) {
		if ($arr[$i]['cIdentifyId'] < $arr[$j]['cIdentifyId']) {
			$tmp = $arr[$i] ;
			$arr[$i] = $arr[$j] ;
			$arr[$j] = $tmp ;
			unset($tmp) ;
		}
	}
}

//若本筆資料與下筆相同時，則累加...
$j = 0 ;
$list = array() ;
for ($i = 0 ; $i < $max ; $i ++) {
	if ($arr[$i]['cIdentifyId'] != $arr[$i+1]['cIdentifyId']) {				//若與下筆為不同者複製
		$list[$j++] = $arr[$i] ;
	}
	else {																	//若與下一筆身份證字號相同者
		$arr[$i+1]['cInterest'] += $arr[$i]['cInterest'] ;					//將本筆的利息金額加入到下一筆去
		$arr[$i+1]['cTax'] += $arr[$i]['cTax'] ;							//將本筆的代扣稅款加入到下一筆去
		if ($arr[$i]['cEndDate'] > $arr[$i+1]['cEndDate']) {				//若本筆匯款時間比下一筆匯款時間晚時
			$arr[$i+1]['cEndDate'] = $arr[$i]['cEndDate'] ;					//將下筆匯款時間覆蓋成較晚的時間
					$arr[$i+1]['cRegistZip'] = $arr[$i]['cRegistZip'] ;
					$arr[$i+1]['cRegistCity'] = $arr[$i]['cRegistCity'] ;
					$arr[$i+1]['cRegistArea'] = $arr[$i]['cRegistArea'] ;
					$arr[$i+1]['cRegistAddr'] = $arr[$i]['cRegistAddr'] ;
		}
	}
}

unset($arr) ;
$arr = array() ;
$arr = array_merge($arr,$list) ;
unset($list) ;

##

//個人回饋金
//本年度前三季
$sql = '
	SELECT 
		tax.cBranchNum cBranchNum,
		tax.FBYear FBYear,
		tax.FBS1 FBS1,
		tax.FBS2 FBS2,
		tax.FBS3 FBS3,
		tax.FBS4 FBS4,
		CONCAT(
			(SELECT bCode FROM tBrand WHERE bId=bra.bBrand),
			LPAD(bra.bId,5,"0")
		) sn,
		bra.bStore bStore,
		bra.bTtitle cName,
		bra.bIdentityNumber cIdentifyId,
		bra.bZip2 cRegistZip,
		(SELECT zCity FROM tZipArea WHERE zZip=bra.bZip2) cRegistCity,
		(SELECT zArea FROM tZipArea WHERE zZip=bra.bZip2) cRegistArea,
		bra.bAddr2 cRegistAddr
	FROM 
		tTaxFeedBack AS tax
	JOIN 
		tBranch AS bra ON bra.bId=tax.cBranchNum
	WHERE
		FBYear="'.$feedback_year.'" 
	ORDER BY 
		cBranchNum 
	ASC ;
' ;
//echo "SQL=".$sql."<br>\n" ;
$rel = $conn->all($sql);
$max = count($rel);
for ($i = 0 ; $i < $max ; $i ++) {
	$arr1[$i] = $rel[$i];
	//$detail[$i]['cInterest'] = $detail[$i]['FBS1'] + $detail[$i]['FBS2'] + $detail[$i]['FBS3'] + $detail[$i]['FBS4'] ;
	$arr1[$i]['cInterest'] = $arr1[$i]['FBS1'] + $arr1[$i]['FBS2'] + $arr1[$i]['FBS3'] ; 	//本年度 1~3 季
	$arr1[$i]['iden'] = '2' ;		//回饋金資料別
}
		
//去年度第四季
$_feedback_year = date("Y",mktime(0,0,0,1,1,($feedback_year-1))) ;

$sql = '
	SELECT 
		tax.cBranchNum cBranchNum,
		tax.FBYear FBYear,
		tax.FBS1 FBS1,
		tax.FBS2 FBS2,
		tax.FBS3 FBS3,
		tax.FBS4 FBS4,
		CONCAT(
			(SELECT bCode FROM tBrand WHERE bId=bra.bBrand),
			LPAD(bra.bId,5,"0")
		) sn,
		bra.bStore bStore,
		bra.bTtitle cName,
		bra.bIdentityNumber cIdentifyId,
		bra.bZip2 cRegistZip,
		(SELECT zCity FROM tZipArea WHERE zZip=bra.bZip2) cRegistCity,
		(SELECT zArea FROM tZipArea WHERE zZip=bra.bZip2) cRegistArea,
		bra.bAddr2 cRegistAddr
	FROM 
		tTaxFeedBack AS tax
	JOIN 
		tBranch AS bra ON bra.bId=tax.cBranchNum
	WHERE
		FBYear="'.$_feedback_year.'"
	ORDER BY 
		cBranchNum 
	ASC ;
' ;

//echo "SQL2=".$sql."<br>\n" ;

$max = count($arr1) ;
$rel = $conn->all($sql);
$j = 0 ;
$arr2 = array() ;

foreach ($rel as $tmp) {
	$fg = 0 ; 
	for ($i = 0 ; $i < $max ; $i ++) {
		if ($arr1[$i]['cBranchNum']==$tmp['cBranchNum']) {
			$arr1[$i]['cInterest'] += ($tmp['FBS4'] + 1 - 1) ;
			$fg ++ ;						// 若有相同仲介編號，則將旗標 +1
		}
	}
	if (!$fg) {								// 若查無此公司編號，則加入陣列中
		$arr2[$j] = $tmp ; 
		$arr2[$j++]['iden'] = '2' ;			//回饋金資料別
	}
	unset($tmp) ;
}

$list = @array_merge($arr1,$arr2) ;
unset($arr1) ;
unset($arr2) ;


//合併不同店家、相同回饋對象者
$max = count($list) ;

//依據身份證號碼排序
for ($i = 0 ; $i < $max ; $i ++) {
	for ($j = 0 ; $j < $max - 1 ; $j ++) {
		if ($list[$i]['cIdentifyId'] < $list[$j]['cIdentifyId']) {
			$tmp = $list[$i] ;
			$list[$i] = $list[$j] ;
			$list[$j] = $tmp ;
			unset($tmp) ;
		}
	}
}

//若本筆資料與下筆相同時，則累加...
$j = 0 ;
$arr1 = array() ;
for ($i = 0 ; $i < $max ; $i ++) {
	if ($list[$i]['cIdentifyId'] != $list[$i+1]['cIdentifyId']) {		//若與下筆為不同者複製
		$arr1[$j++] = $list[$i] ;
	}
	else {																//若與下一筆身份證字號相同者
		$list[$i+1]['cInterest'] += $list[$i]['cInterest'] ;			//將本筆的利息金額加入到下一筆去
		$list[$i+1]['cTax'] += $list[$i]['cTax'] ;						//將本筆的代扣稅款加入到下一筆去
	}
}

unset($list) ;
$list = array() ;
$list = array_merge($list,$arr1) ;
unset($arr1) ;

##

$detail = array_merge($list,$arr) ;
unset($list) ;
unset($arr) ;

$max = count($detail) ;

$filename = $unit_id.'.'.($feedback_year - 1911) ;
$fp = fopen('./excel/'.$filename,"w") ;
$fp1 = fopen("./excel/blank_word.txt","w") ;

$prefix_i = 1 ;															//利息流水號起始值
$prefix_c = 1 ;															//回饋金流水號起始值

//建立格式檔
for ($i = 0 ; $i < $max ; $i ++) {
	$line_word = '' ;
	
	//縣市機關別
	$line_str = 'A14' ;
	##
	
	//流水號
	//$line_str .= str_pad(($i+1),8,'0',STR_PAD_LEFT) ;
	if ($detail[$i]['iden']=='1') {										//利息
		$line_str .= 'I'.str_pad($prefix_i++,7,'0',STR_PAD_LEFT) ;
	}
	else {																//其他(回饋金)
		$line_str .= 'C'.str_pad($prefix_c++,7,'0',STR_PAD_LEFT) ;
	}
	##
	
	//申報單位統一編號、註記
	$line_str .= $unit_id.' ' ;
	##
	
	//格式、所得人統一編號、證號別
	$detail[$i]['cIdentifyId'] = strtoupper($detail[$i]['cIdentifyId']) ;
	if ($detail[$i]['iden']=='1') {	$line_str .= '5B' ;	}								//利息
	else if ($detail[$i]['iden']=='2') { $line_str .= '9A' ;	}						//回饋金
	
	$detail[$i]['cIdentifyId'] = trim($detail[$i]['cIdentifyId']) ;
	
	if (preg_match("/^[a-zA-Z0-9]{10}$/",$detail[$i]['cIdentifyId'])) {					//10碼-->代表為身分證(本國人)或居留證號(外國人)
		$line_str .= $detail[$i]['cIdentifyId'] ;
		if (preg_match("/[a-zA-Z]{2}/",$detail[$i]['cIdentifyId'])) {					//若包含2碼英文字母-->代表外國人
			if ($detail[$i]['cResidentLimit']) {										//住滿183天
				$id_code = '3' ;
			}
			else {																		//未住滿183天
				$id_code = '7' ;
			}
			$line_str .= $id_code ;
		}
		else {
			$line_str .= '0' ;															//證號別(本國人)
			$id_code = '1' ;
		}
	}
	else if (preg_match("/^[0-9]{8}$/",$detail[$i]['cIdentifyId'])) {					//營利事業登記證編號
		$line_str .= $detail[$i]['cIdentifyId'].'  ' ;
		$line_str .= '1' ;																//證號別(事業團體)
		$id_code = '1' ;
	}
	##
	
	//給付總額(A)
	$line_str .= str_pad($detail[$i]['cInterest'],10,'0',STR_PAD_LEFT) ;
	##
	
	//扣繳稅額(B)
	$line_str .= str_pad($detail[$i]['cTax'],10,'0',STR_PAD_LEFT) ;
	##
	
	//給付淨額(C=A-B)
	$str = $detail[$i]['cInterest'] - $detail[$i]['cTax'] + 1 - 1 ;
	$line_str .= str_pad($str,10,'0',STR_PAD_LEFT) ;
	##
	
	//共用欄位一
	if ($detail[$i]['iden']=='1') {						//利息
		$line_str .= $detail[$i]['sn'].'   ' ;			//保證號碼+3碼空白
	}
	else if ($detail[$i]['iden']=='2') {				//回饋金
		$line_str .= '76          ' ;					//一般經紀人+10碼空白
	}
	##
	
	//軟體註記
	$line_str .= 'A' ;									//自行撰寫程式
	##
	
	//錯誤註記
	$line_str .= ' ' ;									//邏輯檢查無誤
	##
	
	//所得給付年度
	$line_str .= ($feedback_year - 1911) ;				//所得給付年度
	##
	
	//所得人姓名/名稱
	$str = n_to_w($detail[$i]['cName']) ;
	
	mb_regex_encoding('BIG5') ;
	mb_substitute_character('long') ;
	$str = mb_convert_encoding($str,'BIG5','UTF-8') ;
	
	$patt1 = mb_convert_encoding('　','BIG5','UTF-8') ;
	$patt2 = mb_convert_encoding(' ','BIG5','UTF-8') ;
	
	//紀錄特殊字無法呈現者
	$word_match = preg_match('/U\+([0-9A-F]{4})/e',$str) ;
	if ($word_match) {
		$line_word .= $detail[$i]['sn'].', 姓名:'.$detail[$i]['cName']."\r\n" ;
	}
	##
	
	$str = preg_replace('/U\+([0-9A-F]{4})/e','chr(32)',$str) ;
	$str = preg_replace("/$patt2/",$patt1,$str) ;
	$str_no = mb_strlen($str) ;
	for ($x = $str_no ; $x < 40 ; $x ++) {
		$str .= ' ' ;
	}
	
	$line_str .= $str ;
	##
	
	//所得人地址
	$str = $detail[$i]['cRegistAddr'] ;
	
	$patt = $detail[$i]['cRegistCity'] ;
	$str = preg_replace("/$patt/","",$str) ;
	$patt = $detail[$i]['cRegistArea'] ;
	$str = preg_replace("/$patt/","",$str) ;
	$str = $detail[$i]['cRegistCity'].$detail[$i]['cRegistArea'].$str ;
	
	//刪掉鄰
	$str = preg_replace("/\d+鄰/",'',$str) ;
	$str = n_to_w($str) ;
	
	mb_regex_encoding('BIG5') ;
	mb_substitute_character('long') ;
	$str = mb_convert_encoding($str,'BIG5','UTF-8') ;
	
	$patt1 = mb_convert_encoding('　','BIG5','UTF-8') ;
	$patt2 = mb_convert_encoding(' ','BIG5','UTF-8') ;
	
	//紀錄特殊字無法呈現者
	$word_match = preg_match('/U\+([0-9A-F]{4})/e',$str) ;
	if ($word_match) {
		$line_word .= $detail[$i]['sn'].', 地址:'.$detail[$i]['cRegistAddr']."\r\n" ; 
	}
	##
	
	$str = preg_replace('/U\+([0-9A-F]{4})/e','chr(32)',$str) ;
	$str = preg_replace("/$patt2/",$patt1,$str) ;
	$str_no = mb_strlen($str) ;
	for ($x = $str_no ; $x < 60 ; $x ++) {
		$str .= ' ' ;
	}
	
	$line_str .= $str ;
	##
	
	//所得所屬期間
	$line_str .= ($feedback_year - 1911).'01'.($feedback_year - 1911).'12' ;
	##
	
	//49個空白
	for ($x = 0 ; $x < 49 ; $x ++) {
		$line_str .= ' ' ;
	}
	##
	
	//是否滿183天
	if ($id_code == '7') {		//證號別為 "7" 時
		$line_str .= 'N' ;
	}
	else {
		$line_str .= ' ' ;
	}
	##
	
	//國家代碼
	if ($id_code == '7') {
		if ($detail[$i]['cCountryCode']) {
			$line_str .= $detail[$i]['cCountryCode'] ;
		}
		else {
			$line_str .= '  ' ;
		}
	}
	else {
		$line_str .= '  ' ;
	}
	##
	
	//租稅協定代碼
	if ($id_code == '7') {
		if ($detail[$i]['cTaxtreatyCode']) {
			$line_str .= $detail[$i]['cTaxtreatyCode'] ;
		}
		else {
			$line_str .= '  ' ;
		}
	}
	else {
		$line_str .= '  ' ;
	}
	##
	
	//空白
	$line_str .= '  ' ;
	##
	
	//居住者檔案製作日期/非居住者所得給付日期
	if ($id_code == '7') {
		if (!preg_match("/^0000-00-00$/",$detail[$i]['cPaymentDate'])) {
			$tmp = explode('-',$detail[$i]['cPaymentDate']) ;
			$detail[$i]['cPaymentDate'] = $tmp[1].$tmp[2] ;
			$line_str .= $detail[$i]['cPaymentDate'] ;
			unset($tmp) ;
		}
		else {
			$line_str .= '    ' ;
		}
	}
	else {
		$line_str .= date("md") ;
	}
	##
	
	//echo $line_str."\r\n" ;
	fwrite($fp,$line_str."\r\n") ;
	fwrite($fp1,$line_word) ;
}
fclose($fp) ;
fclose($fp1) ;

//header('Content-type:application/force-download') ;
//header('Content-Transfer-Encoding: Binary') ;
//header('Content-Disposition:attachment;filename=/accounting/excel/output.txt') ;
 
?>
<html>
<head>
<script>
//var url = './excel/<?=$file_name?>' ;
//location = url ;
</script>
<body>
檔案已產出!!
<ul>
	<li><a href="/accounting/excel/<?=$filename?>" target="_blank">點我下載...(申報檔)</a></li>
	<li><a href="/accounting/excel/blank_word.txt" target="_blank">點我下載...(難字檔)</a></li>
</ul>
</body>
</head>
</html>
