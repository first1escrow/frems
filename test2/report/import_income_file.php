<?php
// ini_set("display_errors", "OFF"); 
// error_reporting(E_ALL & ~E_NOTICE);

include_once '../../openadodb.php' ;

require_once('../../bank/Classes/PHPExcel.php');
require_once('../../bank/Classes/PHPExcel/Writer/Excel2007.php');
require_once("../../bank/Classes/PHPExcel/IOFactory.php");
require_once("../../bank/Classes/PHPExcel/Reader/Excel5.php");


##
//讀取 excel 檔案
$xls = '11001.xlsx';
$objReader = new PHPExcel_Reader_Excel2007(); 
$objReader->setReadDataOnly(true); 

//檔案名稱
$objPHPExcel = $objReader->load($xls); 
$currentSheet = $objPHPExcel->getSheet(0);//讀取第一個工作表(編號從 0 開始) 
$allLine = $currentSheet->getHighestRow() ;//取得總列數


$i=0;
$list = array();
for($excel_line = 1;$excel_line<=$allLine;$excel_line++) {
	$data = array();
	$data['cCertifiedId'] = $currentSheet->getCell("B{$excel_line}")->getValue() ;
	$data['cCertifiedMoney'] = $currentSheet->getCell("H{$excel_line}")->getValue() ;
	$data['cCaseFeedBackMoney'] = $currentSheet->getCell("J{$excel_line}")->getValue() ;

	print_r($data);

	array_push($list, $data);
	unset($data);
	
}


$fw = fopen('txt/case.txt', 'a+');
foreach ($list as $val) {
	$txt = $val['cCertifiedId']."_".$val['cCertifiedMoney']."_".$val['cCaseFeedBackMoney']."\r\n";
	echo $txt;
	fwrite($fw, $txt);
}

fclose($fw);
unset($list);
?>