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
$objPHPExcel->getProperties()->setSubject("地政士生日禮名單");
$objPHPExcel->getProperties()->setDescription("第一建經地政士生日禮名單");

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
$col = 65;
$row = 1;
$date = '製作日期:'.(date('Y')-1911)."年".date('m')."月".date('d')."日";
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':K'.$row);
//寫入表頭資料
$col = 65;
$row = 2;
$objPHPExcel->getActiveSheet()->getStyle("A2:K2")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士事務所');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'生日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'品項');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'申請人');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'審核人');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'領取人');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'收據是否繳回');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'是否達標');
//  					 

if ($_POST['tax']) {
	$objPHPExcel->getActiveSheet()->getStyle("K".$row.":R".$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'扣憑格式代號');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'姓名');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'證件號碼');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地址');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'所得總額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'代扣稅額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'所得淨額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'傳票');
}

//寫入查詢資料
$col = 65;
$row = 3 ;	// 起始位置
for ($i = 0 ; $i < count($list) ; $i ++) {
	$col = 65;
	
	$address = $list[$i]['sZip'].$list[$i]['city'].$list[$i]['area'].$list[$i]['sAddress'];
	
	$money2 = 0;

	$objPHPExcel->getActiveSheet()->getStyle("A".$row.":K".$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sCode2']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sOffice']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sBirthday']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['gift']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sApplicant']);
	if ($year <= 109) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sInspetor']);
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['salesName']);
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sReceipt']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sLevel']);

	if ($_POST['tax']) {
		$objPHPExcel->getActiveSheet()->getStyle("L".$row.":S".$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'91');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['taxName']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sIdentifyIdNumber']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$address);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$money2);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($list[$i]['sMoney']-$money2));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list[$i]['sTicket']);
		
	}
	$row++;
}



// if (count($list) > 0) {
	$row += 3;

	$col = $col-3;
	if (count($list) == 0) {
		$col = 71;
	}

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'總經理簽章');
	 $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(28);
	//
	// die();
	//Rename sheet 重命名工作表標籤
	$objPHPExcel->getActiveSheet()->setTitle('申請表');

	//Set active sheet index to the first sheet, so Excel opens this as the first sheet
	
// }
$row++;
$col = 65;

$date = '製作日期:'.(date('Y')-1911)."年".date('m')."月".date('d')."日";
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
$objPHPExcel->setActiveSheetIndex(0);


//Save Excel 2007 file 保存
//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

//$file_name = date("Y_m_d").'.xlsx' ;
//$file_name = '銀行點交結算統計表.xlsx' ;

//$file_path = '/home/httpd/html/'.substr($web_addr,7).'/accounting/excel/' ;

//$_file = $file_path.$file_name ;
//$objWriter->save($_file);
$_file = iconv('UTF-8', 'BIG5', '地政士生日禮申請表單') ;


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