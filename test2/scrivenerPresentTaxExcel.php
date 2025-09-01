<?php

require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;



// echo "<pre>";
// print_r($list);

// die;

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("扣繳申報");
$objPHPExcel->getProperties()->setDescription("第一建經地政士扣繳申報");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

// //調整欄位寬度
// $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
// $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
// $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(16);
// $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(16);
// $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16);

//調整欄位高度
// $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);



//設定總表文字置中
// $objPHPExcel->getActiveSheet()->getStyle('A:k')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('A:k')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F1:P1')->getAlignment()->setWrapText(true);


//設定總表所有案件金額千分位符號
//$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//設定字型大小
// $objPHPExcel->getActiveSheet()->getStyle('A:P')->getFont()->setSize(10);
// $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setSize(12);
//$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getFont()->setSize(10);

//寫入表頭資料
$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->getStyle("A1:K1")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'扣憑格式代號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'證件號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地址');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'所得總額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'代扣稅額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'所得淨額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'傳票');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'申請人');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士編號');
//寫入查詢資料
$col = 65;
$row = 2 ;	// 起始位置
for ($i = 0 ; $i < count($list) ; $i ++) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle("A".$row.":K".$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'91');//固定
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sIdentifyIdNumber']);
	$address = $list[$i]['sZip'].$list[$i]['city'].$list[$i]['area'].$list[$i]['sAddress'];
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$address);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sMoney']);
	$money2 = 0;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$money2);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($list[$i]['sMoney']-$money2));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sTicket']);
	if ($year <= 109) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sApplicant']);
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['salesName']);
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['scrivnerName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sCode2']);
	$row++;
}



	//Rename sheet 重命名工作表標籤
	$objPHPExcel->getActiveSheet()->setTitle('扣繳申報');


$objPHPExcel->setActiveSheetIndex(0);


//Save Excel 2007 file 保存
//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

//$file_name = date("Y_m_d").'.xlsx' ;
//$file_name = '銀行點交結算統計表.xlsx' ;

//$file_path = '/home/httpd/html/'.substr($web_addr,7).'/accounting/excel/' ;

//$_file = $file_path.$file_name ;
//$objWriter->save($_file);
$_file = iconv('UTF-8', 'BIG5', '扣繳申報') ;


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