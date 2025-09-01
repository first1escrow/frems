<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
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
$objPHPExcel->getProperties()->setSubject("案件統計表");
$objPHPExcel->getProperties()->setDescription("第一建經案件統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('案件統計報表');

//寫入清單標題列資料
// $con = '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,案件狀態日期,進案日期,實際點交日期,銀行出款日期,地政士姓名,標的物座落,狀態'."\n" ;
$col = 65;
$row = 4;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'出款保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'銀行出款日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士業務');
if ($sEndDate && $eEndDate) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'事務所名稱');
}
if ($status == 10) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件餘額');
}
$row++;

// echo $max."<br>";



// $objPHPExcel->getActiveSheet()->setCellValue('I2',$transMoney);



$_file = iconv('UTF-8', 'BIG5', '案件統計表') ;
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header('Content-type:application/force-download');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename='.$_file.'.xlsx');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save("php://output");
	
			exit ;


?>
