<?php
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;



$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("經辦區域統計表");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('經辦區域統計表');

//寫入清單標題列資料
//代書姓名/合約份數/申請日期/負責業務/經辦

$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'人/地');

foreach ($rowTitle as $k => $v) {
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v);

}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未知');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總計');
$row++;


foreach ($Undertaker as $k => $v) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['name']);
	// echo $v['name']."_";
	$total = 0;
	foreach ($rowTitle as $key => $value) {

		$data[$k]['count'][$value] = ($data[$k]['count'][$value] == '')? '0':$data[$k]['count'][$value];
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$k]['count'][$value]);
		$total +=$data[$k]['count'][$value];
		// echo $data[$k]['count'][$value]."_";
	}
	$data[$k]['count']['未知'] = ($data[$k]['count']['未知'] == '')? '0':$data[$k]['count']['未知'];
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$k]['count']['未知']);

	$total +=$data[$k]['count']['未知'];

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total);

	// echo $v['count'][$value]."<Br>";
	$row++;
}


// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'代書姓名');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約份數');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'申請日期');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'負責業務');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'經辦');
// $row++;

// foreach ($data as $k => $v) {
// 	$col = 65;
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['code']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Name']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['total']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['bCreateDate']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sales']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Undertaker']);
// 	// print_r($v);
// 	// die;
// 	// preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);
// 	$row++;
// }


// $objPHPExcel->createSheet(1) ;
// $objPHPExcel->setActiveSheetIndex(1);
// $objPHPExcel->getActiveSheet()->setTitle('有效保證號碼');
// $col = 65;
// $row = 1;
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'編號');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'代書姓名');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'申請日期');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'負責業務');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'經辦');
// $row++;


// foreach ($data as $k => $v) {

// 	foreach ($v['CertifiedId'] as $key => $value) {
// 		$col = 65;
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['code']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Name']);
// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value,PHPExcel_Cell_DataType::TYPE_STRING); 
		
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['bCreateDate']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sales']);
// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Undertaker']);

// 		$row++;
// 	}
	
	
	
	
// }

$objPHPExcel->setActiveSheetIndex(0);



$_file = iconv('UTF-8', 'BIG5', '經辦區域統計表') ;
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
