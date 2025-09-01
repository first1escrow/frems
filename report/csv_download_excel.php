<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;

##

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("店家資料表");
$objPHPExcel->getProperties()->setDescription("店家資料表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('店家資料表');

//寫入清單標題列資料
// $con = '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,案件狀態日期,進案日期,實際點交日期,銀行出款日期,地政士姓名,標的物座落,狀態'."\n" ;
$col = 65;
$row = 1;

foreach ($title as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value);
}

$row++;	


foreach ($list as $key => $value) {
	$col = 65;
	foreach ($fieldArray as $field) {
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value[$field]);
	}

	$row++;
	
}

//
$_file = iconv('UTF-8', 'BIG5', '店家資料表') ;
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-type:application/force-download');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$_file.'.xlsx');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");


exit;


?>
