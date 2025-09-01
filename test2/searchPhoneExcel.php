<?php
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
$objPHPExcel->getProperties()->setSubject("第一建經");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('第一建經');

//寫入清單標題列資料
// $con = '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,案件狀態日期,進案日期,實際點交日期,銀行出款日期,地政士姓名,標的物座落,狀態'."\n" ;
$col = 65;
$row = 1;
//編號	店名	業務	狀態	第二季是否有回饋

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋');
$row++;

// echo $max."<br>";
// echo "<pre>";
// print_r($notfind);
// die;
for ($i = 0 ; $i < count($notfind) ; $i ++) {
	$col = 65;
	// echo $notfind[$i]['code']."<br>";
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$notfind[$i]['code']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$notfind[$i]['storeName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$notfind[$i]['sales']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$notfind[$i]['status']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$notfind[$i]['feed']);
	
	
	$row++;
}

// die;

$_file = iconv('UTF-8', 'BIG5', '第一建經') ;
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
