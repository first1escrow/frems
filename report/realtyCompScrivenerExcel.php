<?php
require 'vendor/autoload.php';
include_once 'class/intolog.php' ;
include_once '../session_check.php' ;

##
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Style\Fill;
##

//預載log物件
$logs = new Intolog() ;
##

//取得地政士列表權限
$showList = $_SESSION['member_pRealtyCaseList'] ;
##
$exceptbId = array(632, 575,552,620,411,224) ;//地政士(排除奇怪的:632=業務專用 575=陳政祺 552=王泰翔 620=吳效承 411=吳)
//取得區域資訊
if ($city) {
	$sql = 'SELECT * FROM tZipArea WHERE zCity="'.$city.'"' ;

	if ($area) {
		$sql .= ' AND zArea="'.$area.'"' ;	
	}

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		$zip[] = $rs->fields['zZip'] ;

		$rs->MoveNext();
	}

		
	$zip_str = '' ;
	if ($zip) {
		$zip_str = implode('","',$zip) ;
		$zip_str = '"'.$zip_str.'"' ;
	}
}
##


//取得區域郵遞區號字串
$zip_sql = '' ;
if ($zip_str) {
	$zip_sql = ' AND a.sZip1 IN ('.$zip_str.')' ;
}
##
	
//取得地政士店名稱
$scrivener = array() ;
$scrivenerA_sql = '' ;

if ($statusOff == '1') $statusOff = ' AND a.sStatus = 1 ' ;		//過濾已關店頭資料 2015-09-03

$from_dateA = ($Af_year + 1911).'-'.str_pad($Af_month,2,'0',STR_PAD_LEFT).'-01 00:00:00' ;
$to_dateA = ($At_year + 1911).'-'.str_pad($At_month,2,'0',STR_PAD_LEFT).'-31 23:59:59' ;
$time_strA =' AND a.sCreat_time <="'.$to_dateA.'"';
if (($brA) && ($brB)) {
	$scrivenerA = array() ;
	$scrivenerB = array() ;
	
	//取得A地政士店名稱
	$scrivenerA_sql = ' AND a.sId="'.$brA.'"' ;
	//LPAD('字串','字串總共位數','填補的值');
	$sql = "SELECT
				a.sId,
				a.sName,
				a.sOffice,
				CONCAT('SC',LPAD(a.sId,4,'0')) as sCode,
				a.sBrand
			FROM
				tScrivener AS a
			WHERE a.sStatus != '3' AND a.sId NOT IN (".implode(',',$exceptbId).") ".$scrivenerA_sql.$zip_sql.$time_strA."ORDER BY a.sId ASC;";
	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$rs->fields['sBrand'] = CategoryScrinver($rs->fields['sBrand']);
		$scrivenerA[$i] = $rs->fields;
		$scrivenerA[$i]['caseMax'] = 0 ;
		$scrivenerA[$i]['caseMoney'] = 0 ; 
		$scrivenerA[$i]['HQ'] = 0 ;
		$scrivenerA[$i]['sales'] = getSalesName('s',$scrivenerA[$i]['sId']);
		$i++;
		$rs->MoveNext();
	}
	
	##
	
	//取得B地政士店名稱
	$scrivenerB_sql = ' AND a.sId="'.$brB.'"' ;
	
	$sql = "SELECT
				a.sId,
				a.sName,
				a.sOffice,
				CONCAT('SC',LPAD(a.sId,4,'0')) as sCode,
				a.sBrand
			FROM
				tScrivener AS a
			WHERE a.sStatus != '3' AND a.sId NOT IN (".implode(',',$exceptbId).") ".$scrivenerB_sql.$zip_sql.$time_strA."ORDER BY a.sId ASC;";
	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$rs->fields['sBrand'] = CategoryScrinver($rs->fields['sBrand']);
		$scrivenerB[$i] = $rs->fields;
		$scrivenerB[$i]['caseMax'] = 0 ;
		$scrivenerB[$i]['caseMoney'] = 0 ; 
		$scrivenerB[$i]['HQ'] = 0 ;
		$scrivenerB[$i]['sales'] = getSalesName('s',$scrivenerB[$i]['sId']);
		$i++;
		$rs->MoveNext();
	}
	##
	
	$scrivener = array_merge($scrivenerA,$scrivenerB) ;
	unset($scrivenerA) ; 
	unset($scrivenerB) ;
}
else {
if ($twhgBranch && $twhgBranch2) {//台屋加盟和直營
	$serchTxt = ' AND a.sBrand LIKE "%1%"';
}elseif ($twhgBranch) { //台屋加盟
	$serchTxt = ' AND a.sBrand LIKE "%1%" AND a.sCategory = 1';
}elseif ($twhgBranch2) {
	$serchTxt = ' AND a.sBrand LIKE "%1%" AND a.sCategory = 2';
}
	$sql = "SELECT 
				a.sId,
				a.sName,
				a.sOffice,
				CONCAT('SC',LPAD(a.sId,4,'0')) as sCode,
				a.sBrand
				FROM
					tScrivener AS a
				WHERE
					a.sStatus != '3'
					".$serchTxt."
					AND a.sId NOT IN (".implode(',',$exceptbId).") ".$statusOff.$zip_sql.$time_strA."ORDER BY a.sId ASC;";

	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$rs->fields['sBrand'] = CategoryScrinver($rs->fields['sBrand']);
		$scrivener[$i] = $rs->fields ;
		$scrivener[$i]['caseMax'] = 0 ;
		$scrivener[$i]['caseMoney'] = 0 ; 
		$scrivener[$i]['HQ'] = 0 ;
		$scrivener[$i]['sales'] = getSalesName('s',$scrivener[$i]['sId']);
		$i++;
		$rs->MoveNext();
	}
	
	
}

$scrivenerA = array_merge($scrivener) ;
$scrivenerB = array_merge($scrivener) ;
##

//取得"比較"時間範圍之保證號碼

	
$sql = '
	SELECT 
		cas.cCertifiedId as cCertifiedId,
		inc.cCertifiedMoney as cCertifiedMoney,
		cs.cScrivener
	FROM 
		tContractCase AS cas
	JOIN
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=cas.cCertifiedId
	WHERE 
		cas.cApplyDate>="'.$from_dateA.'" 
		AND cas.cApplyDate<="'.$to_dateA.'" 
	ORDER BY 
		cas.cApplyDate 
	ASC;
' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$listA[] = $rs->fields ;

	$rs->MoveNext();
}

##
//print_r($listA) ; exit ;
//計算"比較"期間地政士店件數與金額
for ($i = 0 ; $i < count($listA) ; $i ++) {
	
	for ($j=0; $j < count($scrivenerA); $j++) { 
		if ($scrivenerA[$j]['sId'] == $listA[$i]['cScrivener']) {
			$scrivenerA[$j]['caseMax'] ++ ;
			$scrivenerA[$j]['caseMoney'] += $listA[$i]['cCertifiedMoney'];
		}
	}
	
	
}
##
function CategoryScrinver($arr){

	$ss = array();
	$tmp = explode(',', $arr);

	for ($i=0; $i < count($tmp); $i++) { 
		if ($tmp[$i] == 2) {
			$ss[]= '非仲介成交';
		}elseif ($tmp[$i] == 1) {
			$ss[]= '台灣房屋';
		}elseif ($tmp[$i] == 49) {
			$ss[]= '優美地產';
		}
	}

	rsort($ss);
	return @implode(',', $ss);
}

//取得"對照"時間範圍之保證號碼
$from_dateB = ($Bf_year + 1911).'-'.str_pad($Bf_month,2,'0',STR_PAD_LEFT).'-01 00:00:00' ;
$to_dateB = ($Bt_year + 1911).'-'.str_pad($Bt_month,2,'0',STR_PAD_LEFT).'-31 23:59:59' ;

$sql = '
	SELECT 
		cas.cCertifiedId as cCertifiedId,
		inc.cCertifiedMoney as cCertifiedMoney,
		cs.cScrivener
	FROM 
		tContractCase AS cas
	JOIN
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=cas.cCertifiedId
	WHERE 
		cas.cApplyDate>="'.$from_dateB.'" 
		AND cas.cApplyDate<="'.$to_dateB.'" 
	ORDER BY 
		cas.cApplyDate 
	ASC;
' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$listB[] = $rs->fields;

	$rs->MoveNext();
}
##

//計算"對照"期間地政士店件數與金額
for ($i = 0 ; $i < count($listB) ; $i ++) {
	
	for ($j=0; $j < count($scrivenerB); $j++) { 
		if ($scrivenerB[$j]['sId'] == $listB[$i]['cScrivener']) {
			$scrivenerB[$j]['caseMax'] ++ ;
			$scrivenerB[$j]['caseMoney'] += $listB[$i]['cCertifiedMoney'];
		}
	}
	
	
}	
##

$logs->writelog('realtyCompExcel') ;

$objPHPExcel = new Spreadsheet();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("地政士比較表");
$objPHPExcel->getProperties()->setDescription("第一建經");
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('比較表');
##

//設定欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20) ;

$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20) ;
##

//設定文字置中
// $objPHPExcel->getActiveSheet()->getStyle('A1:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:I2')->getAlignment() ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getAlignment() ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
##

//寫入日期範圍
$objPHPExcel->getActiveSheet()->mergeCells('A1:E1') ;				// A 地政士
$objPHPExcel->getActiveSheet()->mergeCells('A2:E2') ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$Af_year.'年/'.$Af_month.'月 ~ '.$At_year.'年/'.$At_month.'月') ;
$objPHPExcel->getActiveSheet()->setCellValue('A2','比較組') ;

$objPHPExcel->getActiveSheet()->mergeCells('H1:M1') ;				// B 地政士
$objPHPExcel->getActiveSheet()->mergeCells('H2:M2') ;
$objPHPExcel->getActiveSheet()->setCellValue('H1',$Bf_year.'年/'.$Bf_month.'月 ~ '.$Bt_year.'年/'.$Bt_month.'月') ;
$objPHPExcel->getActiveSheet()->setCellValue('H2','對照組') ;
##

//寫入title資料
$objPHPExcel->getActiveSheet()->setCellValue('A3','名稱') ;			// A 地政士
$objPHPExcel->getActiveSheet()->setCellValue('B3','履保收入') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','進案件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','總部成交件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','合作仲介品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','負責業務') ;
if ($showList < 2) {
	$objPHPExcel->getActiveSheet()->getComment('D3')->getText()->createTextRun('本欄位需經主管授權方能檢視!!') ;
}


$objPHPExcel->getActiveSheet()->setCellValue('H3','名稱') ;			// B 地政士
$objPHPExcel->getActiveSheet()->setCellValue('I3','履保收入') ;
$objPHPExcel->getActiveSheet()->setCellValue('J3','進案件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('K3','總部成交件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('L3','合作仲介品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('M3','負責業務') ;
if ($showList < 2) {
	$objPHPExcel->getActiveSheet()->getComment('K3')->getText()->createTextRun('本欄位需經主管授權方能檢視!!') ;
}
##

//寫入各店家資料
$index = 4 ;

for ($i = 0 ; $i < count($scrivenerA) ; $i ++) {							// A 地政士
	$objPHPExcel->getActiveSheet()->setCellValue('A'.($index+$i),$scrivenerA[$i]['sCode'].$scrivenerA[$i]['sName'].'('.$scrivenerA[$i]['sOffice'].')') ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($index+$i),$scrivenerA[$i]['caseMoney']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($index+$i),$scrivenerA[$i]['caseMax']) ;
	if ($showList > 1) {		//確認是否擁有檢視總部件數權限
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($index+$i),$scrivenerA[$i]['HQ']) ;
	}
	else {
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($index+$i),'0') ;
	}
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($index+$i),$scrivenerA[$i]['sBrand']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('F'.($index+$i),$scrivenerA[$i]['sales']) ;
}

for ($i = 0 ; $i < count($scrivenerB) ; $i ++) {							// B 地政士
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($index+$i),$scrivenerB[$i]['sCode'].$scrivenerB[$i]['sName'].'('.$scrivenerB[$i]['sOffice'].')') ;
	$objPHPExcel->getActiveSheet()->setCellValue('I'.($index+$i),$scrivenerB[$i]['caseMoney']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('J'.($index+$i),$scrivenerB[$i]['caseMax']) ;
	if ($showList > 1) {		//確認是否擁有檢視總部件數權限
		$objPHPExcel->getActiveSheet()->setCellValue('K'.($index+$i),$scrivenerB[$i]['HQ']) ;
	}
	else {
		$objPHPExcel->getActiveSheet()->setCellValue('K'.($index+$i),'0') ;
	}
	$objPHPExcel->getActiveSheet()->setCellValue('L'.($index+$i),$scrivenerB[$i]['sBrand']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('M'.($index+$i),$scrivenerB[$i]['sales']) ;
}
##

##

//Save Excel 2007 file 保存
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header('Content-type:application/force-download');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename=compScrivener.xlsx');
			
$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');
$objPHPExcel->disconnectWorksheets();
unset($objPHPExcel);
// $objPHPExcel->setActiveSheetIndex(0) ;
// $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel) ;
// ##


// ##
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="compScrivener.xlsx"');
// header('Cache-Control: max-age=0');

// $objWriter->save('php://output');
exit ;

function getSalesName($type,$id){
	global $conn;

	if ($type =='s') {
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS sales FROM tScrivenerSales WHERE sScrivener ='".$id."'";
	}else{
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS sales FROM tBranchSales WHERE bBranch ='".$id."'";
	}

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['sales'];

		$rs->MoveNext();
	}
	
	return @implode(',', $tmp);
}
?>