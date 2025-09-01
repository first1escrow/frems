<?php
// ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;



$sSignDate = ($_REQUEST['date_start_y']+1911)."-".$_REQUEST['date_start_m']."-01";
$eSignDate = ($_REQUEST['date_end_y']+1911)."-".$_REQUEST['date_end_m']."-31";
$city = $_REQUEST['zipC']; //zipC[]
$area = $_REQUEST['zipA'];
$m1 = $_REQUEST['date_start_m'];
$m2 = $_REQUEST['date_end_m'];
$y1 = $_REQUEST['date_start_y'];
$y2 = $_REQUEST['date_end_y'];
$source = $_REQUEST['source'] ;
##


$ss = $source ;
if (!$ss) $ss = '台灣、永慶、信義' ;
$ss = '（'.$ss.'）' ;
##

//產品分類
$ptype = array('土地/廠辦','大樓/華廈','套房','透天','公寓','店面','其他') ;


//總價分類
$priceList = array('1000萬以下','1000萬-1500萬','1500萬-2000萬','2000萬-2500萬','2500萬-3000萬','3000萬以上') ;


//取得所在縣市的鄉鎮市區
$dist = array() ;
//區域## (有可能會有地區跟縣市同時存在)
if ($city) {
	for ($i=0; $i < count($city); $i++) { 
		
		$dist[] = $city[$i];
	}
	
	if ($area) {
		$sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip IN(".@implode(',', $area).")";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$dist[] = $rs->fields['zCity']."-".$rs->fields['zArea'];

			$rs->MoveNext();
		}

	}

}else if($area){
	$sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip IN(".@implode(',', $area).")";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$dist[] = $rs->fields['zCity']."-".$rs->fields['zArea'];

			$rs->MoveNext();
		}


}
##
##
// print_r($dist) ; exit ;

//撈出範圍資料(時間)
$distDetail = array() ;
$Firstdata2 = array() ;

$months = 0 ;
$months = countMonth(substr($sSignDate, 0, 7), substr($eSignDate, 0 , 7)) ;

foreach ($dist as $k => $v) {
    
    $d_startY = $y1 + 1911 ;
    $d_startM = $m1 ;
    for ($i = 0 ; $i < $months ; $i ++) {
		//$dt = explode('-',date("Y-m",strtotime("-".$j."month"))) ;
		// $dt = explode('-',date("Y-m",strtotime(($years + 1911).'-'.$months.'-10 -'.($j-1)."month"))) ;
		$dt = strtotime($d_startY.'-'.$d_startM) ;
        $dt = strtotime('+'.$i.'month', $dt) ;
        $dy = date("Y", $dt) ;
        $dm = date("m", $dt) ;
        
		$tmp = explode('-', $v);
		$distDetail[$k][$i]['city'] = $tmp[0] ;
		$distDetail[$k][$i]['district'] = $tmp[1] ;
		// $distDetail[$k]['time'] = $y1."-".$m1."至".$y2."-".$m2 ;
		$distDetail[$k][$i]['time'] = $dy.'-'.$dm ;
		// $distDetail[$k]['total'] = getAreaCount($tmp[0], $tmp[1],$y1.$m1, $y2.$m2, $source) ;
		$distDetail[$k][$i]['total'] = getAreaCount($tmp[0], $tmp[1], ($dy - 1911).$dm, ($dy - 1911).$dm, $source) ;
		// print_r($distDetail[$k][$i]['total']) ; exit ;
		//第一建經
		if (preg_match("/^.*台灣$/isu", $source)) {
            // $Firstdata2[$k]['city'] = $tmp[0] ;
            // $Firstdata2[$k]['district'] = $tmp[1] ;
            // $Firstdata2[$k]['time'] = $y1."-".$m1."至".$y2."-".$m2 ;
            // $Firstdata2[$k][ = getCaseData('money',$sSignDate,$eSignDate,$tmp[0],$tmp[1]);
            $arr = array() ;
            // $arr = getCaseData('money',$sSignDate,$eSignDate,$tmp[0],$tmp[1]) ;
            $arr = getCaseData('money', $dy.'-'.$dm.'-01', $dy.'-'.$dm.'-31', $tmp[0], $tmp[1]) ;
            
            // echo "<pre>";
                // print_r($arr) ;
            // echo "</pre>";
            $tt = 0 ;
            foreach ($arr[$tmp[0].$tmp[1]] as $ka => $va) {
                $tt += $va ;
            }
            // $Firstdata2[$k]['total'] = $tt ;
            $distDetail[$k][$i]['total'] += $tt ;
            // echo "<pre>";
                // print_r($Firstdata2) ;
            // echo "</pre>";
            unset($arr, $tt) ; 
		}
		
		unset($dt) ;
		unset($tmp);
    }
}
// echo '<pre>' ;print_r($distDetail) ; print_r($Firstdata2) ; exit ;

//撈出範圍資料(產品型態)
$prodDetail = array() ;
$Firstdata = array() ;
foreach ($dist as $k => $v) {
	$tmp = explode('-', $v);
	
	$prodDetail[$k]['city'] = $tmp[0] ;
	$prodDetail[$k]['district'] = $tmp[1] ;
	$prodDetail[$k]['time'] = $y1."-".$m1."至".$y2."-".$m2 ;
	$prodDetail[$k]['total'] = getProductCount($tmp[0], $tmp[1], $y1.$m1, $y2.$m2, $source) ;

	//第一建經
	if (preg_match("/^.*台灣$/isu", $source)) {

		// $Firstdata = getCaseData('objkind',$sSignDate,$eSignDate,$tmp[0],$tmp[1]);
        $arr = array() ;
		$arr = getCaseData('objkind',$sSignDate,$eSignDate,$tmp[0],$tmp[1]) ;
        foreach ($arr[$tmp[0].$tmp[1]] as $ka => $va) {
            $prodDetail[$k]['total'][$ka]  += $va ;
        }
		// echo "<pre>";
			// print_r($Firstdata) ;
		// echo "</pre>";
	}
	unset($dt) ;
		unset($tmp);
}
// echo '<pre>' ;print_r($prodDetail) ; exit ;//print_r($Firstdata) ; exit ;

//撈出範圍資料(總價分佈)
$priceDetail = array() ;
$Firstdata3 = array() ;
foreach ($dist as $k => $v) {
	// $dt = array($years, $months) ;
	$tmp = explode('-', $v);
	$priceDetail[$k]['city'] = $tmp[0] ;
	$priceDetail[$k]['district'] =  $tmp[1] ;
	$priceDetail[$k]['time'] =  $y1."-".$m1."至".$y2."-".$m2 ;
	$priceDetail[$k]['total'] = getPriceCount($tmp[0], $tmp[1], $y1.$m1, $y2.$m2, $source) ;

	//第一建經
	if (preg_match("/^.*台灣$/isu", $source)) {

		// $Firstdata3 = getCaseData('money',$sSignDate,$eSignDate,$tmp[0],$tmp[1]);
        $arr = array() ;
		$arr = getCaseData('money',$sSignDate,$eSignDate,$tmp[0],$tmp[1]) ;
        foreach ($arr[$tmp[0].$tmp[1]] as $ka => $va) {
            $priceDetail[$k]['total'][$ka]  += $va ;
        }
		// echo "<pre>";
			// print_r($Firstdata3) ;
		// echo "</pre>";
	}
	
	unset($dt) ;
}
// echo '<pre>' ;print_r($priceDetail) ; exit ;//print_r($Firstdata) ; exit ;
// echo $y1;
// echo "<pre>";
// print_r($prodDetail) ; echo "</pre>";exit ;

//print_r($distDetail) ; exit ;


// echo "<pre>";
// print_r($distDetail) ;
// echo "</pre>";
// echo "<pre>";
// print_r($priceDetail) ;
// echo "</pre>";

$Firstdata2 = getCaseData('money',$sSignDate,$eSignDate,$city,$area);
$Firstdata4 = getCaseData('count',$sSignDate,$eSignDate,$city,$area);

// echo "<pre>";
// print_r($distDetail) ;
// echo "</pre>";

// die;



//計算指定縣市區域與時間的交易件數
function getAreaCount($city, $district, $day, $day2, $source='') {
	global $conn ;
	$total = 0 ;
	if ($district) {
		$str .= 'AND sDistrict="'.$district.'"';
	}
		
	$sql = 'SELECT sDistrict, sTime, COUNT(sId) as cnt FROM tSalesRealValue WHERE sCity="'.$city.'" '.$str.'  AND sTimeId >= "'.$day.'" AND sTimeId <= "'.$day2.'"' ;		//全部
	//$sql = 'SELECT sDistrict, sTime, COUNT(sId) as cnt FROM tSalesRealValue WHERE sCity="'.$city.'" AND sDistrict="'.$district.'" AND sType NOT LIKE "%土%" AND sTimeId="'.$yr.$mn.'"' ;		//排除土地
	if ($source){
		$tmp = explode(',', $source);
		for ($i=0; $i < count($tmp); $i++) { 

			$tmp[$i] = '"'.$tmp[$i].'"';
		}
		$sql .= ' AND sSource IN ('.implode(',', $tmp).')' ;
		unset($tmp);
	}
	$sql .= ' ORDER BY sDistrict ASC;' ;
	$rs = $conn->Execute($sql) ;
	
	$total = (int)$rs->fields['cnt'] ;
	
	return $total ;
}
//計算指定縣市區域與時間的移轉件數
function getProductCount($city, $district, $day, $day2, $source='') {
	global $conn, $ptype ;
	$arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0) ;
	if ($district) {
		$str .= 'AND sDistrict="'.$district.'"';
	}
		
	$sql = 'SELECT sDistrict, sTime, sType FROM tSalesRealValue WHERE sCity="'.$city.'" '.$str.' AND sTimeId >= "'.$day.'" AND sTimeId <= "'.$day2.'"' ;		//全部
	//$sql = 'SELECT sDistrict, sTime, sType FROM tSalesRealValue WHERE sCity="'.$city.'" AND sDistrict="'.$district.'" AND sType NOT LIKE "%土%" AND sTimeId="'.$yr.$mn.'"' ;		//排除土地
	if ($source){
		$tmp = explode(',', $source);
		for ($i=0; $i < count($tmp); $i++) { 
			
			$tmp[$i] = '"'.$tmp[$i].'"';
		}
		$sql .= ' AND sSource IN ('.implode(',', $tmp).')' ;
		unset($tmp);
	}
	$sql .= ' ORDER BY sDistrict ASC;' ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$str = $rs->fields['sType'] ;
		
		if (preg_match("/土地/isu",$str)) {
			$arr[0] ++ ;
		}
		else if (preg_match("/廠/isu",$str)) {
			$arr[0] ++ ;
		}
		else if (preg_match("/大樓/isu",$str)) {
			$arr[1] ++ ;
		}
		else if (preg_match("/華廈/isu",$str)) {
			$arr[1] ++ ;
		}
		else if (preg_match("/套房/isu",$str)) {
			$arr[2] ++ ;
		}
		else if (preg_match("/透天/isu",$str)) {
			$arr[3] ++ ;
		}
		else if (preg_match("/公寓/isu",$str)) {
			$arr[4] ++ ;
		}
		else if (preg_match("/店面/isu",$str)) {
			$arr[5] ++ ;
		}
		else {
			$arr[6] ++ ;
		}
		
		$rs->MoveNext() ;
	}
	
	return $arr ;
}

//計算指定縣市區域與時間的交易總價件數
function getPriceCount($city, $district, $day, $day2, $source='') {
	global $conn, $ptype ;
	$arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0) ;

	if ($district) {
		$str .= 'AND sDistrict="'.$district.'"';
	}
		
	$sql = 'SELECT sDistrict, sTime, sDealPrice FROM tSalesRealValue WHERE sCity="'.$city.'" '.$str.' AND sTimeId >= "'.$day.'" AND sTimeId <= "'.$day2.'"' ;		//全部
	//$sql = 'SELECT sDistrict, sTime, sDealPrice FROM tSalesRealValue WHERE sCity="'.$city.'" AND sDistrict="'.$district.'" AND sType NOT LIKE "%土%" AND sTimeId="'.$yr.$mn.'"' ;		//排除土地
	if ($source){
		$tmp = explode(',', $source);
		for ($i=0; $i < count($tmp); $i++) { 
			
			$tmp[$i] = '"'.$tmp[$i].'"';
		}
		$sql .= ' AND sSource IN ('.implode(',', $tmp).')' ;
		unset($tmp);
	}
	$sql .= ' ORDER BY sDistrict ASC;' ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$prc = 0 ;
		$str = $rs->fields['sDealPrice'] ;
		$str = str_replace(',','',$str) ;
		$tmp = explode('萬',$str) ;
		$prc = (int)$tmp[0] ;
		unset($tmp) ;
		
		if ($prc < 1000) {
			$arr[0] ++ ;
		}
		else if (($prc >= 1000) && ($prc < 1500)) {
			$arr[1] ++ ;
		}
		else if (($prc >= 1500) && ($prc < 2000)) {
			$arr[2] ++ ;
		}
		else if (($prc >= 2000) && ($prc < 2500)) {
			$arr[3] ++ ;
		}
		else if (($prc >= 2500) && ($prc < 3000)) {
			$arr[4] ++ ;
		}
		else if ($prc >= 3000) {
			$arr[5] ++ ;
		}

		$rs->MoveNext() ;
	}
	
	return $arr ;
}
##

function getCaseData($cat,$sSignDate,$eSignDate,$city,$area){

	global $conn;

	$query = 'cas.cCaseStatus<>"8" AND cas.cCertifiedId<>"" ';
	// 搜尋條件-簽約日期
	if ($sSignDate) {
		
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
		//$query .= ' tra.tExport_time>="'.$sSignDate.' 00:00:00" ' ;
	}
	if ($eSignDate) {
		
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;

	}
	
    if ($city) $query .= " AND zip.zCity IN('".$city."')" ;
    if($area) $query .= " AND zip.zArea IN('".$area."')" ;
        
	unset($tmp);
	if ($query) { $query = ' WHERE '.$query ; }

	$sql ='
		SELECT 
			cas.cCertifiedId as cCertifiedId, 
			cas.cSignDate as cSignDate, 
			inc.cTotalMoney as cTotalMoney, 
			inc.cCertifiedMoney as cCertifiedMoney, 
			pro.cAddr as cAddr, 
			pro.cZip as cZip,
			pro.cTownHouse,
			pro.cLevelHighter, 
			pro.cLevelNow, 
			zip.zCity as zCity,
			zip.zArea as zArea,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			rea.cBranchNum as branch,
			rea.cBranchNum1 as branch1,
			rea.cBranchNum2 as branch2
		FROM 
			tContractCase AS cas 
		LEFT JOIN 
			tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
		LEFT JOIN
			tZipArea AS zip ON zip.zZip=pro.cZip
		'.$query.'  AND pro.cItem = 0 
		GROUP BY
			cas.cCertifiedId
		ORDER BY 
			cas.cSignDate ASC' ;
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$month = substr($rs->fields['cSignDate'], 0,7);  //月份
		$tmpObj = '';

		// $tmpArea = '';
		
		//物件類型
		if ($cat == 'objkind') {
			
			$Totalfloor = checkSp(TxtToNum($rs->fields['cLevelHighter']));
			$floor = checkSp(TxtToNum($rs->fields['cLevelNow']));

			//判斷樓層是否KEY反
			if (is_numeric($Totalfloor) && is_numeric($floor) ) { 
				if ($Totalfloor < $floor) {
					$tmpV = $Totalfloor;
					$Totalfloor = $floor;
					$floor = $tmpV;
					unset($tmpV);
				}
			}elseif (preg_match("/^.*\-.*$/isu", $Totalfloor)) {
				$tmpV = $Totalfloor;
				$Totalfloor = $floor;
				$floor = $tmpV;
				unset($tmpV);
			}

			
			##
			//$arr = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0) ;
			if ($Totalfloor == '' && $floor == '') {
				$tmpObj = 0;
			}else if ($Totalfloor >= 11 ) {
				$tmpObj = 1;
			}elseif ($Totalfloor >= 6 && $Totalfloor <= 10) {
				$tmpObj = 1;
			}else {
				if (checkProperty($floor,$Totalfloor,$rs->fields['cTownHouse'])) {
					$tmpObj = 3;
				}else{
					$tmpObj = 4;
				}
			}
		}else if($cat == 'money'){
			if ($rs->fields['cTotalMoney'] > 30000000) {  //3000萬以上
				$tmpObj = 5;
			}else if ($rs->fields['cTotalMoney'] > 25000000 && $rs->fields['cTotalMoney'] <= 30000000) { //2500萬-3000萬
				$tmpObj = 4;
			}else if ($rs->fields['cTotalMoney'] > 20000000 && $rs->fields['cTotalMoney'] <= 25000000) { // 2000萬-2500萬
				$tmpObj = 3;
			}elseif ($rs->fields['cTotalMoney'] >15000000 && $rs->fields['cTotalMoney'] <= 20000000) { // 1500~2000萬(含) 4
				$tmpObj = 2;
			}elseif ($rs->fields['cTotalMoney'] >10000000 && $rs->fields['cTotalMoney'] <= 15000000) { // 1000~1500萬(含) 3
				$tmpObj = 1;
			}else{ //1000萬以下
				$tmpObj = 0;
			}
		}else{
			$tmpObj = '數量';
		}
		##地區
		// if ($city) {
			// $data[$rs->fields['zCity']][$tmpObj]++;
			// if ($area) {
				// $data[$rs->fields['zCity'].$rs->fields['zArea']][$tmpObj]++;
			// }
		// }else if($area){
			// $data[$rs->fields['zCity'].$rs->fields['zArea']][$tmpObj]++;
		// }
        if ($area) $data[$rs->fields['zCity'].$rs->fields['zArea']][$tmpObj] ++ ;
		else if ($city) $data[$rs->fields['zCity']][$tmpObj] ++ ;

		unset($month);unset($tmpObj);
		$rs->MoveNext();
	}

	return $data;
}

function TxtToNum($val){

	$arr = array( 1=>'一',2=> '二',3=> '三',4=> '四',5=> '五', 6=>'六',7=>'七',8=>'八',9=>'九');
	// $val = str_replace('十', '', $val);
	if (preg_match("/^十(.*)層$/isu", $val)) { //10幾
		$val = str_replace('十', '1', $val);
	}elseif(preg_match("/^(.*)十層$/isu", $val)){ //?十
		$val = str_replace('十', '0', $val);
	}else{ //
		$val = str_replace('十', '', $val); //?十?
	}
	
	$val = str_replace('層', '', $val);
	foreach ($arr as $k => $v) {
		$val = str_replace($v, $k, $val);
	}

	return $val;
}

function checkSp($val){


	$arr = array('.','-','_',',','，','+','~','、');

	foreach ($arr as $k => $v) {
		$val = str_replace($v, '-', $val);
	}

	return $val;
}

function checkProperty($floor,$Totalfloor,$TownHouse){

	global $conn;

	if (($TownHouse > 0) || ($floor == '' && $Totalfloor != '')) { //有勾透天或目前樓層是空 但總樓層有填寫都是透天
		return true;
	}

	
	if (preg_match("/^.*-.*$/isu", $floor)){
		$check = 0;
		$tmp = explode('-', $floor);

		sort($tmp);//數字排列 怕有1-3-2
		//計算擁有的總樓層
		for ($i=$tmp[0]; $i <= $tmp[(count($tmp)-1)]; $i++) { 
			$check++;
		}
		
		if ($check == $Totalfloor) {

			return true;
		}
	}

	


	return false;

}

//計算月份數
Function countMonth($a, $b) {
    // $a = '2016-01';
    // $b = '2017-02';
    
    if (preg_match("/^\d{4}\-\d{1,2}$/isu", $a) && preg_match("/^\d{4}\-\d{1,2}$/isu", $b)) {
        $sa = strtotime($a) ;
        $sb = strtotime($b) ;

        $ya = date('y', strtotime($a)) * 12 + date('m', strtotime($a)) ;
        $yb = date('y', strtotime($b)) * 12 + date('m', strtotime($b)) ;

        // echo "差的月数" ;
        // echo $yb-$ya ;
        // echo "<br>" ;
        // echo "差的天数" ;
        // echo ceil(($sb-$sa) / (60*60*24)) ;
        // echo "<br>" ;
        // echo "差的小时" ;
        // echo ceil(($sb-$sa) / (60*60)) ;
        
        return ($yb - $ya + 1) ;
    }
    else return false ;
}
##

//print_r($priceDetail) ; exit ;
##

//編輯 Excel
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性

$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("業績統計");
$objPHPExcel->getProperties()->setDescription("第一建經業績統計");

/* 新增第 1 頁 */

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0) ;
##

$Alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N') ;

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('A3:'.$Alpha[count($distDetail[0])].'3') ;
##

//設定儲存格底色
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('00FAB636') ;
##

//設定文字粗體、顏色
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(20) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE) ;
##

// $str = '' ;
// $arr = explode('-',$distDetail[0][12]['time']) ;
// $str = $city.$arr[0].'年'.$arr[1].'月-' ;
// unset($arr) ;
// $arr = explode('-',$distDetail[0][1]['time']) ;
// $str .= $arr[0].'年'.$arr[1].'月' ;
// unset($arr) ;

$str = '' ;
$str = $y1.'年度 '.$m1.'月份 ~ '.$y2.'年度 '.$m2.'月份 建物土地買賣移轉量 '.$ss ;

$objPHPExcel->getActiveSheet()->setCellValue('A3',$str) ;
$d_startY = $y1 + 1911 ;
$d_startM = $m1 ;
// foreach ($Alpha as $k => $v) {
for ($k = 0 ; $k <= count($distDetail[0]) ; $k ++) {
	$str = '' ;
	
	if ($k == 0) $str = '區域 \ 月份' ;
	// else if ($k == count($distDetail[0])) $str = "與上月比較百分比" ;
	else {
        $dt = strtotime($d_startY.'-'.$d_startM) ;
        $dt = strtotime('+'.($k-1).'month', $dt) ;
        $dy = date("Y", $dt) ;
        $dm = date("m", $dt) ;
        
		$tt = $k - 12 ;
		if ($tt >= 0) $tt = '+ '.$tt ;
		
		$str .= ($dy - 1911).'年' ;
		$str .= $dm.'月份' ;
        
        unset($dt, $dy, $dm) ;
	}
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha[$k].'4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha[$k].'4')->getFill()->getStartColor()->setARGB('004AA157') ;
	##
	
	//設定文字粗體、顏色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha[$k].'4')->getFont()->setBold(true) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha[$k].'4')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE) ;
	##
	
	$objPHPExcel->getActiveSheet()->getColumnDimension($Alpha[$k])->setWidth(16) ;
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha[$k].'4',$str) ;
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12) ;

//設定文字粗體、顏色
$objPHPExcel->getActiveSheet()->getStyle($Alpha[$k].'4')->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->getStyle($Alpha[$k].'4')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE) ;
##

// $objPHPExcel->getActiveSheet()->getColumnDimension($Alpha[$k])->setWidth(16) ;
// $objPHPExcel->getActiveSheet()->setCellValue($Alpha[$k].'4','與上月比較百分比') ;

// $objPHPExcel->getActiveSheet()->getColumnDimension($Alpha[count($distDetail[0])+1])->setWidth(20) ;
// $objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(24) ;

//設定儲存格底色
// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].'4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].'4')->getFill()->getStartColor()->setARGB('0046788') ;
// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+2].'4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+2].'4')->getFill()->getStartColor()->setARGB('003B0302') ;
##

//各分區統計寫入
foreach ($distDetail as $ka => $va) {
	$cells = 5 + $ka ;
	
	$colorIndex = '' ;
	if ($cells % 2 == 1) $colorIndex = '00D0E0E1' ;
	else $colorIndex = '00E9F0EA' ;
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cells,$va[0]['city'].$va[0]['district']) ;
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	##
	
	$percent = 0 ;
	
	if (($va[1]['total'] == 0) && ($va[2]['total'] == 0)) $percent = 0 ;
	else if ($va[1]['total'] == 0) $percent = -1 ;
	else if ($va[2]['total'] == 0) $percent = 1 ;
	else $percent = ($va[1]['total'] / $va[2]['total']) ;
	//else $percent = (($va[1]['total'] / $va[2]['total']) - 1) ;
	//$percent = $va[1]['total'] / $va[2]['total'] ;
	
	// $objPHPExcel->getActiveSheet()->setCellValue($Alpha[count($distDetail[0])+1].$cells,$percent) ;
	// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT) ;
	// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].$cells)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00) ;
	
	//設定文字粗體、顏色
	// $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].$cells)->getFont()->setBold(true) ;
	// if ($percent < 0) $objPHPExcel->getActiveSheet()->getStyle($Alpha[count($distDetail[0])+1].$cells)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED) ;
	##

	foreach ($va as $k => $v) {		
        $al = $k + 1 ;
        $objPHPExcel->getActiveSheet()->setCellValue($Alpha[$al].$cells,$v['total']) ;
        
        //設定儲存格底色
        $objPHPExcel->getActiveSheet()->getStyle($Alpha[$al].$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
        $objPHPExcel->getActiveSheet()->getStyle($Alpha[$al].$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
        ##
	}
}
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("建物土地買賣移轉量") ;
##



/* 新增第 2 頁 */

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
##

$Alpha1 = array('A','B','C','D','E','F','G','H','I') ;

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('A3:I3') ;
##

//設定儲存格底色
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('00FAB636') ;
##

//設定文字粗體、顏色
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(20) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE) ;
##

// $str = '' ;
// $str = $years.'年'.$months.'月份'.$city.'中古屋成交產品分析 '.$ss ;
$str = '' ;
$str = $y1.'年度 '.$m1.'月份 ~ '.$y2.'年度 '.$m2.'月份 中古屋成交產品分析 '.$ss ;

$objPHPExcel->getActiveSheet()->setCellValue('A3',$str) ;

foreach ($Alpha1 as $k => $v) {
	$str = '' ;
	
	if ($k == 0) $str = '區域 \ 產品' ;
	else if ($k == 8) $str = '統計' ;
	else $str = $ptype[($k-1)] ;
	
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFill()->getStartColor()->setARGB('004AA157') ;
	##
	
	//設定文字粗體、顏色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFont()->setBold(true) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE) ;
	##
	
	$objPHPExcel->getActiveSheet()->getColumnDimension($Alpha1[$k])->setWidth(16) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[$k].'4',$str) ;
	
}
$objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(24) ;

//各分區統計寫入
$arr = array() ;
$arrTotal = 0 ;
foreach ($prodDetail as $ka => $va) {
	$cells = 5 + $ka ;
	$t = 0 ;
	
	$colorIndex = '' ;
	if ($cells % 2 == 1) $colorIndex = '00D0E0E1' ;
	else $colorIndex = '00E9F0EA' ;
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	##
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cells,$va['city'].$va['district']) ;
	
	foreach ($va['total'] as $k => $v) {
		$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+1)].$cells,$v) ;
		
		//設定儲存格底色
		$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+1)].$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
		$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+1)].$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
		##
		
		$t += $v ;
		$arr[$k] += $v ;
		$arrTotal += $v ;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+2)].$cells,$t) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+2].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+2)].$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+2)].$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	##
	
	unset($t) ;
}

//總計
$cells ++ ;

$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cells,'總計') ;

//設定儲存格底色
$objPHPExcel->getActiveSheet()->getStyle('A'.$cells.':I'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cells.':I'.$cells)->getFill()->getStartColor()->setARGB('00FFFF00') ;
##

foreach ($ptype as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+1)].$cells,$arr[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+1].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
}

$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+2)].$cells,$arrTotal) ;
$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+2].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
##

//占比
$cells ++ ;

$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cells,'占比') ;

//設定儲存格底色
$objPHPExcel->getActiveSheet()->getStyle('A'.$cells.':I'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cells.':I'.$cells)->getFill()->getStartColor()->setARGB('00D06F1E') ;
##

$t = 0 ;
foreach ($ptype as $k => $v) {
	$val = 0 ;
	$val += $arr[$k] ;
	@$val = $val / $arrTotal ;
	$t += $val ;
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+1)].$cells,$val) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+1].$cells)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+1].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
	unset($val) ;
}

$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+2)].$cells,$t) ;
$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+2].$cells)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE) ;
$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+2].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
unset($t, $arr, $arrTotal) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("中古屋成交產品分析") ;
##


/* 新增第 3 頁 */

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(2) ;
##

$Alpha1 = array('A','B','C','D','E','F','G','H','I') ;

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('A3:I3') ;
##

//設定儲存格底色
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()->getStartColor()->setARGB('00FAB636') ;
##

//設定文字粗體、顏色
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(20) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE) ;
##

// $str = '' ;
// $str = $years.'年'.$months.'月份'.$city.'中古屋成交總價分析 '.$ss ;

$str = '' ;
$str = $y1.'年度 '.$m1.'月份 ~ '.$y2.'年度 '.$m2.'月份 中古屋成交總價分析 '.$ss ;

$objPHPExcel->getActiveSheet()->setCellValue('A3',$str) ;

foreach ($Alpha1 as $k => $v) {
	$str = '' ;
	
	if ($k == 0) $str = '區域 \ 總價' ;
	else if ($k == 7) $str = '統計' ;
	else if ($k == 8) $str = '占比' ;
	else $str = $priceList[($k-1)] ;
	
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFill()->getStartColor()->setARGB('004AA157') ;
	##
	
	//設定文字粗體、顏色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFont()->setBold(true) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE) ;
	##
	
	$objPHPExcel->getActiveSheet()->getColumnDimension($Alpha1[$k])->setWidth(16) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k].'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[$k].'4',$str) ;
	
}
$objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(24) ;

//各分區統計寫入
$arrX = array() ;
$arrY = array() ;
foreach ($priceDetail as $ka => $va) {
	$cells = 5 + $ka ;
	
	$colorIndex = '' ;
	if ($cells % 2 == 1) $colorIndex = '00D0E0E1' ;
	else $colorIndex = '00E9F0EA' ;
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	##
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cells,$va['city'].$va['district']) ;
	
	foreach ($va['total'] as $k => $v) {
		$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+1)].$cells,$v) ;
		
		//設定儲存格底色
		$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+1)].$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
		$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+1)].$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
		##
		
		$arrX[$ka] += $v ;
		$arrY[$k] += $v ;
	}
	
	//總計( x 軸)
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+2)].$cells,$arrX[$ka]) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+2].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
	##
	
	//設定儲存格底色
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+2)].$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[($k+2)].$cells)->getFill()->getStartColor()->setARGB($colorIndex) ;
	##
}

//總計( y 軸)
$cells ++ ;

$objPHPExcel->getActiveSheet()->getStyle('A'.$cells)->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cells,'總計') ;

//設定儲存格底色
$objPHPExcel->getActiveSheet()->getStyle('A'.$cells.':I'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$cells.':I'.$cells)->getFill()->getStartColor()->setARGB('00FFFF00') ;
##

$totalArr = 0 ;
foreach ($arrY as $k => $v) {
	$totalArr += $arrY[$k] ;
	$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+1)].$cells,$arrY[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+1].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
}

$objPHPExcel->getActiveSheet()->setCellValue($Alpha1[($k+2)].$cells,$totalArr) ;
$objPHPExcel->getActiveSheet()->getStyle($Alpha1[$k+2].$cells)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
##

//占比
$all = 0 ;
foreach ($arrX as $k => $v) {
	$t = 0 ;
	@$t = $v / $totalArr ;
	$all += $t ;
	$objPHPExcel->getActiveSheet()->getStyle('I'.(5 + $k))->getFont()->setBold(true) ;
	$objPHPExcel->getActiveSheet()->setCellValue('I'.(5 + $k),$t) ;
	$objPHPExcel->getActiveSheet()->getStyle('I'.(5 + $k))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00) ;
    
    //設定儲存格底色
    $objPHPExcel->getActiveSheet()->getStyle('I'.(5 + $k))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
    $objPHPExcel->getActiveSheet()->getStyle('I'.(5 + $k))->getFill()->getStartColor()->setARGB('00D06F1E') ;
    ##

}
unset($t, $arrX, $arrY, $totalArr) ;
##

$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->setCellValue('I'.$cells,$all) ;
$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE) ;

//設定儲存格底色
$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) ;
$objPHPExcel->getActiveSheet()->getStyle('I'.$cells)->getFill()->getStartColor()->setARGB('00D06F1E') ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("中古屋成交總價分佈") ;
##


//產出檔案
$_file = 'salesData.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
##

exit ;
?>
