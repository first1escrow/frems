<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("業務統計表");
$objPHPExcel->getProperties()->setDescription("第一建經業務統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet(0)->getStyle('A1:A2')->getFont()->getColor()->setARGB('FF0000'); //紅色
$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(18);
$objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A1',$sales_name.'統計表') ;





$cht = array(
				'1' => '一',
				'2' => '二',
				'3' => '三',
				'4' => '四',
				'5' => '五',
				'6' => '六',
				'7' => '七',
				'8' => '八',
				'9' => '九',
				'10' => '十',
				'11' => '十一',
				'12' => '十二'
 			);


$row = 2;
$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':M'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'簽約數') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':M'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'※數量有小數表示店家簽約的業務不只一位') ;

$row++;


$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'月份') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'仲介簽約數') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'地政士簽約數') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'簽約數') ;
// draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'達成率') ;

$col = 66;
if (is_array($summary1)) {
	foreach ($summary1 as $k => $v) {

		$row = $row2;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$k) ;
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['BranchCount']) ;
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['ScrivenerCount']) ;
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['targetcount']) ;
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		// if ($k > $now_month && $now_check != 1) {
		// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'0%') ;
		// }else{
		// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['target'].'%') ;
		// }
		
		
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
		$col++;
	}
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$row++;
}


##
#新增並指定工作頁
$row = 1;

	$objPHPExcel->createSheet() ;
	$objPHPExcel->setActiveSheetIndex(1) ;
	$objPHPExcel->getActiveSheet()->setTitle('地政士');

	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'建檔日') ; //A
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':D'.$row); /// B D
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'地政士') ;//B
	$objPHPExcel->getActiveSheet()->mergeCells('E'.$row.':G'.$row);//E G
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'事務所名稱') ;//E
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$row.':J'.$row);//H J
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,'區域') ;//H
	// $objPHPExcel->getActiveSheet()->mergeCells('K'.$row.':M'.$row); //KM
	// $objPHPExcel->getActiveSheet()->setCellValue('K'.$row,'備註') ;
	$row++;


if (is_array($summary1)) {
	foreach ($summary1 as $key => $value) {

		if (is_array($Scrivener[$key])) {
			foreach ($Scrivener[$key] as $k => $v) {

					$col = 65;
					
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$v['sSignDate']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':D'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$v['sName']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('E'.$row.':G'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$v['sOffice']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$row.':J'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,$v['city'].$v['area']) ;
					// $objPHPExcel->getActiveSheet()->mergeCells('K'.$row.':M'.$row);
					// $objPHPExcel->getActiveSheet()->setCellValue('K'.$row,$v['sRemark4']) ;
					// draw_border($objPHPExcel,'A'.$row.':Q'.$row) ;
					$row++;
				
			}
		}
		
	}
}

##
	$row = 1;

	$objPHPExcel->createSheet() ;
	$objPHPExcel->setActiveSheetIndex(2) ;
	$objPHPExcel->getActiveSheet()->setTitle('仲介');


	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'建檔日') ;
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':D'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'品牌') ;
					$objPHPExcel->getActiveSheet()->mergeCells('E'.$row.':G'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'加盟店') ;
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$row.':J'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,'仲介店名稱') ;
					$objPHPExcel->getActiveSheet()->mergeCells('K'.$row.':M'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$row,'區域') ;
					$objPHPExcel->getActiveSheet()->mergeCells('N'.$row.':P'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$row,'備註') ;
	$row++;

if (is_array($summary1)) {
	foreach ($summary1 as $key => $value) {

		if (is_array($Branch[$key])) {
			foreach ($Branch[$key] as $k => $v) {

					$col = 65;
					
					
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$v['sSignDate']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':D'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$v['brand']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('E'.$row.':G'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$v['bStore']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$row.':J'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$row,$v['bName']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('K'.$row.':M'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$row,$v['city'].$v['area']) ;
					$objPHPExcel->getActiveSheet()->mergeCells('N'.$row.':P'.$row);
					$objPHPExcel->getActiveSheet()->setCellValue('N'.$row,$v['oldStore']) ;
					$row++;
				
			}
		}
		
		
	}
}




##


function draw_border($objPHPExcel,$cells) {
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getTop()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getBottom()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getLeft()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getRight()->getColor()->setARGB('00000000');
}
##
$_file = 'sales.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit ;
?>