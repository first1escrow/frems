<?php
if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') ;
header('Content-Disposition: attachment;filename="houseExp.xlsx"') ;
header('Cache-Control: max-age=0') ;

require_once '../libs/PHPExcel/Classes/PHPExcel.php' ;
require_once '../libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb108.php';

//建物型態
$sql = 'SELECT * FROM tObjKind ORDER BY oTypeId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$ObjKind[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

//主要用途
$sql = 'SELECT * FROM tObjUse ORDER BY uId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$ObjUse[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

//使用分區
$sql = 'SELECT * FROM tCategoryArea ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$areaKind[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

//主要建材
$sql = 'SELECT * FROM tBuildingMaterials ORDER BY bId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$material[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

// 搜尋條件-地區
$sql1 = '' ;
$sql2 = '' ;
if ($zip) {
	$sql1 = ' AND cZip="'.$zip.'" ' ;
	$sql2 = ' AND pro.cZip="'.$zip.'" ' ;
}
else if ($city) {
	$zipArr = array() ;
	$zipStr = '' ;
	$sql3 = 'SELECT zZip FROM tZipArea WHERE zCity="'.$city.'" ORDER BY zCity,zZip ASC;' ;
	$rs = $conn->Execute($sql3) ;
	while (!$rs->EOF) {
		$zipArr[] = $rs->fields['zZip'] ;
		$rs->MoveNext() ;
	}
	
	$zipStr = implode('","',$zipArr) ;
	
	$sql1 = ' AND cZip IN ("'.$zipStr.'") ' ;
	$sql2 = ' AND pro.cZip IN ("'.$zipStr.'") ' ;
	
	unset($zipArr,$zipStr) ;
}
##

$sql = '' ;
// 搜尋條件-簽約日期
if ($cSignDateFrom) {
	$tmp = explode('-',$cSignDateFrom) ;
	$cSignDateFrom = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	$sql .= ' AND cas.cSignDate>="'.$cSignDateFrom.' 00:00:00" ' ;
}
if ($cSignDateTo) {
	$tmp = explode('-',$cSignDateTo) ;
	$cSignDateTo = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	$sql .= ' AND  cas.cSignDate<="'.$cSignDateTo.' 23:59:59" ' ;
}
##

// 取得所有資料
$sql ='
	SELECT 
		cas.cCertifiedId as cCertifiedId,
		cas.cSignDate as cSignDate,
		cas.cCaseStatus as cCaseStatus,
		inc.cTotalMoney as cTotalMoney
	FROM 
		tContractCase AS cas 
	JOIN 
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
	JOIN
		tContractProperty as pro ON pro.cCertifiedId=cas.cCertifiedId
	WHERE
		cas.cCertifiedId<>""
		'.$sql2.'
		'.$sql.' 
	GROUP BY
		cas.cCertifiedId
	ORDER BY 
		pro.cZip
	ASC;
' ;
//echo 'sql='.$sql ; exit ; cas.cSignDate,cas.cCertifiedId
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$list[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

//取得建物土地資料
for ($i = 0 ; $i < count($list) ; $i ++) {
	//土地
	$list[$i]['land'] = array() ;
	$sql = '
		SELECT
			cLand1,
			cLand2,
			cLand3,
			cLand4,
			cMeasure,
			cPower1,
			cPower2,
			cZip,
			cCategory
		FROM 
			tContractLand
		WHERE
			cCertifiedId="'.$list[$i]['cCertifiedId'].'"
			'.$sql1.' 
		ORDER BY 
			cId,cCertifiedId
		ASC;
	' ;
	//echo 'sql='.$sql ; exit ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$list[$i]['land'][] = $rs->fields ;
		$rs->MoveNext() ;
	}
	##
	
	//建物
	$sql = '
		SELECT
			cBudMaterial,
			cBuildDate,
			cLevelNow,
			cLevelHighter,
			cAddr,
			cZip,
			(SELECT zCity FROM tZipArea WHERE zZip=a.cZip) as zCity,
			(SELECT zArea FROM tZipArea WHERE zZip=a.cZip) as zArea,
			cBuildNo,
			cTownHouse,
			cObjKind,
			cObjUse,
			cRoom,
			cParlor,
			cToilet,
			cHasCar,
			cMeasureTotal
		FROM 
			tContractProperty AS a
		WHERE
			cCertifiedId="'.$list[$i]['cCertifiedId'].'"
			'.$sql1.' 
		ORDER BY 
			cId,cCertifiedId
		ASC;
	' ;
	//echo 'sql='.$sql ; exit ; 
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$list[$i]['building'][] = $rs->fields ;
		$rs->MoveNext() ;
	}
	##
}
##
//print_r($list) ; exit ;
//修正資料顯示
for ($i = 0 ; $i < count($list) ; $i ++) {
	//類型
	if ((preg_match("/地號/",$list[$i]['building'][0]['cAddr'])) && ($list[$i]['building'][0]['cMeasureTotal'] > 0)) {
		$list[$i]['objClass'] = '房地(土地+建物)' ;
	}
	else if ((preg_match("/地號/",$list[$i]['building'][0]['cAddr'])) && ($list[$i]['building'][0]['cMeasureTotal '] == 0)) {
		$list[$i]['objClass'] = '土地' ;
	}
	else {
		$list[$i]['objClass'] = '建物' ;
	}
	##
	
	//建物型態
	foreach ($ObjKind as $k => $v) {
		if ($v['oTypeId'] == $list[$i]['building'][0]['cObjKind']) {
			$list[$i]['building'][0]['cObjKind'] = $v['oTypeName'] ;
			break ;
		}
	}

	if ($list[$i]['building'][0]['cObjKind'] == '' || $list[$i]['building'][0]['cObjKind'] == '------') {
		$list[$i]['building'][0]['cObjKind'] = $list[$i]['building'][0]['cLevelNow']."/".$list[$i]['building'][0]['cLevelHighter'];
	}
	##
	
	//交易月份(簽約日期)
	$list[$i]['cSignDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$list[$i]['cSignDate'])) ;
	$tmp = explode('-',$list[$i]['cSignDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) {
		$list[$i]['cSignDate'] = '' ;
	}
	else {
		$tmp[0] -= 1911 ;
		$list[$i]['cSignDate'] = $tmp[0].'年'.$tmp[1].'月份' ;
	}
	unset($tmp) ;
	##
	
	//總價(萬)
	if ($list[$i]['cTotalMoney'] > 0) {
		$list[$i]['cTotalMoney'] = round(($list[$i]['cTotalMoney'] / 10000),2) ;
	}
	## 
	
	//土地部分
	$landMeasure = 0 ;
	$landSec = array() ;
	$landArea = array() ;
	foreach ($list[$i]['land'] as $k => $v) {
		//土地移轉總面積(坪)
		$eachLand = 0 ;
		if ($v['cPower2'] > 0) {
			$eachLand = round(($v['cMeasure'] * $v['cPower1'] / $v['cPower2']),2) ;
		}
		$landMeasure += $eachLand ;
		##
		
		//土地區段位置
		if ($v['cLand1']) $v['cLand1'] .= '段' ;
		if ($v['cLand2']) $v['cLand2'] .= '小段' ;
		if ($v['cLand3']) $v['cLand3'] = '地號'.$v['cLand3'] ;
		if ($v['cLand4']) $v['cLand4'] = '地目'.$v['cLand4'] ;
		if ($v['cLand1']||$v['cLand2']||$v['cLand3']||$v['cLand4']) $landSec[] = $v['cLand1'].$v['cLand2'].$v['cLand3'].$v['cLand4'] ;
		##
		
		//使用分區或編定
		for ($j = 0 ; $j < count($areaKind) ; $j ++) {
			if (($areaKind['cId'] == $v['cCategory']) && ($areaKind['cCategory'] == '0')) {
				$landArea[] = $areaKind['cName'] ;
				break ;
			}
		}
		##
	}

	//判斷土地移轉總面積(坪)&建物移轉總面積(坪)為0的狀況(需撈55)

	if ($landMeasure==0 && $list[$i]['building'][0]['cMeasureTotal']==0) {

		// $list[$i]['cCaseStatus']=$list[$i]['building'][0]['cZip'].",".$list[$i]['building'][0]['cAddr'];
		
		// $sql2='SELECT cLandPin, cBuildingPin FROM tCase WHERE cZip="'.$list[$i]['building'][0]['cZip'].'" AND cAddr="'.$list[$i]['building'][0]['cAddr'].'"';
		$str_addr=iconv('utf-8', 'big5',$list[$i]['building'][0]['cAddr'] );
		// $str_addr = $list[$i]['building'][0]['cAddr'];
		$sql = 'SELECT AREA_BASE_ALL_1, AREA_BASE_ALL_2 FROM PDA03 WHERE ZIP="'.$list[$i]['building'][0]['cZip'].'" AND CONCAT(ADD_1,ADD_2) = "'.$str_addr.'"' ;	
	
		$rs = $conn108->Execute($sql);
		
		if($rs->EOF){
			$full=array("１","２","３","４","５","６","７","８","９","０"); 
			$half=array("1","2","3","4","5","6","7","8","9","0"); 

			$str_addr=str_replace($half,$full,$list[$i]['building'][0]['cAddr']);
			$str_addr=iconv('utf-8', 'big5',$str_addr );

			$sql = 'SELECT AREA_BASE_ALL_1, AREA_BASE_ALL_2 FROM PDA03 WHERE ZIP="'.$list[$i]['building'][0]['cZip'].'" AND CONCAT(ADD_1,ADD_2) = "'.$str_addr.'"' ;	
			$rs = $conn108->Execute($sql);
			while (!$rs->EOF) {
				$list[$i]['building'][0]['cMeasureTotal'] = $rs->fields['AREA_BASE_ALL_1'];
				$landMeasure = $rs->fields['AREA_BASE_ALL_2'];

				$rs->MoveNext();
			}
		}else{
			while (!$rs->EOF) {
				$list[$i]['building'][0]['cMeasureTotal']=$rs->fields['AREA_BASE_ALL_1'];
				$landMeasure=$rs->fields['AREA_BASE_ALL_2'];
				$rs->MoveNext();
			}
		}
	}


	##
	//總面積(坪)
	if ($list[$i]['building'][0]['cMeasureTotal'] > 0) {
		$list[$i]['building'][0]['cMeasureTotal'] = round(($list[$i]['building'][0]['cMeasureTotal'] / 3.30579),2) ;
	}
	##
	
	//單價(萬/坪)
	$list[$i]['building'][0]['unitMeasure'] = 0 ;
	if ($list[$i]['building'][0]['cMeasureTotal'] > 0) {
		$list[$i]['building'][0]['unitMeasure'] = round(($list[$i]['cTotalMoney'] / $list[$i]['building'][0]['cMeasureTotal']),2) ;
	}
	##
	
	//車坪改為是否有車位
	
	if ($list[$i]['building'][0]['cHasCar'] == '1') $list[$i]['building'][0]['cHasCar'] = '有' ;
	else $list[$i]['building'][0]['cHasCar'] = '' ;
	##
	
	//標的位置
	$c = $list[$i]['building'][0]['zCity'] ;
	$a = $list[$i]['building'][0]['zArea'] ;
	$list[$i]['building'][0]['cAddr'] = preg_replace("/$c/","",$list[$i]['building'][0]['cAddr']) ;
	$list[$i]['building'][0]['cAddr'] = preg_replace("/$a/","",$list[$i]['building'][0]['cAddr']) ;
	$list[$i]['building'][0]['cAddr'] = $c.$a.$list[$i]['building'][0]['cAddr'] ;
	unset($a,$c) ;
	##
	
	


	if ($landMeasure > 0) $landMeasure = round(($landMeasure / 3.30578),2) ;
	$list[$i]['LandMeasure'] = $landMeasure ;
	
	if (count($landSec) > 0) $list[$i]['LandSector'] = implode(',',$landSec) ;
	if (count($landArea) > 0) $list[$i]['LandArea'] = implode(',',$landArea) ;
	##
	
	//主要用途
	foreach ($ObjUse as $k => $v) {
		$arr = array() ;
		$arr = explode(',',$list[$i]['building'][0]['cObjUse']) ;
		
		for ($j = 0 ; $j < count($arr) ; $j ++) {
			if ($v['uId'] == $arr[$j]) {
				$arr[$j] = $v['uName'] ;
			}
		}
		$list[$i]['building'][0]['cObjUse'] = implode(',',$arr) ;
		unset($arr) ;
	}
	##
	
	//主要建材
	foreach ($material as $k => $v) {
		if ($v['bTypeId'] == $list[$i]['building'][0]['cBudMaterial']) {
			$list[$i]['building'][0]['cBudMaterial'] = $v['bTypeName'] ;
			break ;
		}
	}
	##
	
	//建築完成年月
	if (!preg_match("/^0000/",$list[$i]['building'][0]['cBuildDate'])) {
		$tmp = explode("-",substr($list[$i]['building'][0]['cBuildDate'],0,10)) ;
		$list[$i]['building'][0]['cBuildDate'] = ($tmp[0] - 1911).'年'.$tmp[1].'月' ;
		unset($tmp) ;
	}
	else {
		$list[$i]['building'][0]['cBuildDate'] = '' ;
	}
	##

	//案件狀態
	$sql='SELECT sName  FROM tStatusCase WHERE sId ='.$list[$i]['cCaseStatus'];

	$status=$conn->Execute($sql);
	$list[$i]['cCaseStatus']=$status->fields['sName'];


}
##

//PHPExcel 版本
$objPHPExcel = new PHPExcel() ;
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經") ;
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經") ;
$objPHPExcel->getProperties()->setTitle("第一建經統計報表") ;
$objPHPExcel->getProperties()->setSubject("房價指數") ;
$objPHPExcel->getProperties()->setDescription("第一建經房價指數統計報表") ;
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0) ;
##

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(50) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(24) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(60) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(24) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(24) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20) ;

##

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20) ;
##

//設定文字置中
$objPHPExcel->getActiveSheet()->getStyle('A:AD')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
$objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT) ;
$objPHPExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
$objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT) ;
$objPHPExcel->getActiveSheet()->getStyle('R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
##

//設定自動換行
$objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setWrapText(true) ;
$objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setWrapText(true) ;
$objPHPExcel->getActiveSheet()->getStyle('S')->getAlignment()->setWrapText(true) ;
##

//寫入清單標題列資料
// $objPHPExcel->getActiveSheet()->setCellValue('A1','案件狀態') ;
// $objPHPExcel->getActiveSheet()->setCellValue('D1','房') ;
// $objPHPExcel->getActiveSheet()->setCellValue('E1','廳') ;
// $objPHPExcel->getActiveSheet()->setCellValue('F1','衛') ;
// $objPHPExcel->getActiveSheet()->setCellValue('G1','室') ;
// $objPHPExcel->getActiveSheet()->setCellValue('N1','車價(萬)') ;
// $objPHPExcel->getActiveSheet()->setCellValue('O1','車坪') ;
// $objPHPExcel->getActiveSheet()->setCellValue('I1','有無管理組織') ;
// $objPHPExcel->getActiveSheet()->setCellValue('J1','標的位置') ;
// $objPHPExcel->getActiveSheet()->setCellValue('L1','土地區段位置') ;
// $objPHPExcel->getActiveSheet()->setCellValue('M1','使用分區或編定') ;
// $objPHPExcel->getActiveSheet()->setCellValue('N1','非都市土地使用分區') ;
// $objPHPExcel->getActiveSheet()->setCellValue('O1','非都市土地使用地') ;

// $objPHPExcel->getActiveSheet()->setCellValue('S1','建築完成年月') ;

$objPHPExcel->getActiveSheet()->setCellValue('A1','類型') ;
$objPHPExcel->getActiveSheet()->setCellValue('B1','建物型態') ;
$objPHPExcel->getActiveSheet()->setCellValue('C1','交易月份') ;
$objPHPExcel->getActiveSheet()->setCellValue('D1','總價(萬)') ;
$objPHPExcel->getActiveSheet()->setCellValue('E1','單價(萬)') ;
$objPHPExcel->getActiveSheet()->setCellValue('F1','總面積(坪)') ;
$objPHPExcel->getActiveSheet()->setCellValue('G1','縣市') ;
$objPHPExcel->getActiveSheet()->setCellValue('H1','區域') ;

$objPHPExcel->getActiveSheet()->setCellValue('I1','土地移轉總面積(坪)') ;

$objPHPExcel->getActiveSheet()->setCellValue('J1','建物移轉總面積(坪)') ;
$objPHPExcel->getActiveSheet()->setCellValue('K1','主要用途') ;
$objPHPExcel->getActiveSheet()->setCellValue('L1','主要建材') ;
$objPHPExcel->getActiveSheet()->setCellValue('M1','總樓層數') ;
$objPHPExcel->getActiveSheet()->setCellValue('N1','移轉層次') ;
$objPHPExcel->getActiveSheet()->setCellValue('O1','車位類別') ;
$objPHPExcel->getActiveSheet()->setCellValue('P1','交易筆棟數') ;
##

$cell_no = 2 ;	//愈填寫查詢結果起始的儲存格位置
//寫入查詢結果
for ($i = 0 ; $i < count($list) ; $i ++) {
	// $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+$cell_no),$list[$i]['cCaseStatus']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+$cell_no),$list[$i]['building'][0]['cRoom']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+$cell_no),$list[$i]['building'][0]['cParlor']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+$cell_no),$list[$i]['building'][0]['cToilet']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+$cell_no),'0') ;
	// $objPHPExcel->getActiveSheet()->setCellValue('N'.($i+$cell_no),'0') ;
	// $objPHPExcel->getActiveSheet()->setCellValue('O'.($i+$cell_no),$list[$i]['building'][0]['cHasCar']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+$cell_no),'') ;
	// $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+$cell_no),$list[$i]['building'][0]['cAddr']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('L'.($i+$cell_no),$list[$i]['LandSector']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('M'.($i+$cell_no),'') ;
	// $objPHPExcel->getActiveSheet()->setCellValue('N'.($i+$cell_no),$list[$i]['LandArea']) ;
	// $objPHPExcel->getActiveSheet()->setCellValue('O'.($i+$cell_no),'') ;
	// $objPHPExcel->getActiveSheet()->setCellValue('S'.($i+$cell_no),$list[$i]['building'][0]['cBuildDate']) ;
	

	$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+$cell_no),$list[$i]['objClass']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+$cell_no),$list[$i]['building'][0]['cObjKind']) ;	
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+$cell_no),$list[$i]['cSignDate']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+$cell_no),$list[$i]['cTotalMoney']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+$cell_no),$list[$i]['building'][0]['unitMeasure']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+$cell_no),$list[$i]['building'][0]['cMeasureTotal']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+$cell_no),$list[$i]['building'][0]['zCity']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+$cell_no),$list[$i]['building'][0]['zArea']) ;	
	
	$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+$cell_no),$list[$i]['LandMeasure']) ;
	
	$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+$cell_no),$list[$i]['building'][0]['cMeasureTotal']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+$cell_no),$list[$i]['building'][0]['cObjUse']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+$cell_no),$list[$i]['building'][0]['cBudMaterial']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('M'.($i+$cell_no),$list[$i]['building'][0]['cLevelHighter']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('N'.($i+$cell_no),$list[$i]['building'][0]['cLevelNow']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+$cell_no),'') ;
	$objPHPExcel->getActiveSheet()->setCellValue('P'.($i+$cell_no),'') ;
}
##

//設定開啟頁為第一頁
$objPHPExcel->setActiveSheetIndex(0) ;
##

//Save Excel 2007 file 保存
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
##

$objWriter->save('php://output');
exit ;

##
?>
