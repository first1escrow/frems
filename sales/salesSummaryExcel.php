<?php
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
$objPHPExcel->getActiveSheet()->setCellValue('A1',$sales_name.'--'.$yr.'年 '.$mn.'月統計表') ;

$objPHPExcel->getActiveSheet()->mergeCells('A2:M2');
$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A2','本月份達成率'.$target.'%') ;

$row = 4;
$col = 65;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'建檔日') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'事務所名稱') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'區域') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'備註') ;
$objPHPExcel->getActiveSheet()->mergeCells('F'.$row.':M'.$row);
$row++;

if (is_array($Scrivener)) {
	foreach ($Scrivener as $k => $v) {
		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['no']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sSignDate']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sName']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sOffice']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['city'].$v['area']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sRemark4']) ;
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$row.':M'.$row);
		draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
		$row++;
	}
}

$row++;
$col = 65;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'建檔日') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'加盟店') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名稱') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'區域') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'備註') ;
$objPHPExcel->getActiveSheet()->mergeCells('G'.$row.':M'.$row);

$row++;
// foreach ($newBranch as $k => $v) {

if (is_array($Branch)) {
	foreach ($Branch as $k => $v) {
		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['no']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sSignDate']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['brand']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['bStore']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['bName']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['city'].$v['area']) ;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['bCashierOrderMemo']) ;
		
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$row.':M'.$row);
		draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
		$row++;
	}
}
$row++;


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

$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':M'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各月份簽約數/達成率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'月份') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'簽約數') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'達成率') ;


foreach ($summary1 as $k => $v) {
	$row = $row2;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['targetcount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	

	if ($k > $now_month) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'0%') ;
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['target'].'%') ;
	}

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
$row++;

##



##
$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':M'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各月份進件量/成長率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'月份') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'進件量(台屋)') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'進件量(非台屋)') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'成長率') ;
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
foreach ($summary1 as $k => $v) {
	$row = $row2;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['twcount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['othercount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



	if ($k > $now_month) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'0%') ;
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['group'].'%') ;
	}
	
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
$row++;
##

#############使用量/使用率################

$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':M'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各月份使用量/使用率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'月份') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'使用量') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;

$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'使用率') ;
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
foreach ($summary1 as $k => $v) {
	$row = $row2;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['usecount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


	// if ($k > $now_month) {
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'0%') ;
	// }else{
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['use'].'%') ;
	// }
	// $objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	// // draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
$row++;
##

#############保證費/貢獻率################

$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':M'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各月份保證費/貢獻率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'月份') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'保證費') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'回饋金') ;
draw_border($objPHPExcel,'A'.$row.':M'.$row) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'貢獻率') ;
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
foreach ($summary1 as $k => $v) {
	$row = $row2;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['crtifiedMoney']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['feedBackMoney']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	if ($k > $now_month) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'0%') ;
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['contribution'].'%') ;
	}

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).($row))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
$row++;
##


$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各季簽約數/達成率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'季') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'簽約數') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'達成率') ;


foreach ($season1 as $k => $v) {
	$row = $row2;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['targetcount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	if ($v['target'] > 100) $v['target'] = 100 ;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$season2[$k]['target'].'%') ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
$row++;

##

$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各季進件量/成長率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'季') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'簽約數(台屋)') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'簽約數(非台屋)') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'達成率') ;


foreach ($season1 as $k => $v) {
	$row = $row2;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['twcount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['othercount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	if ($v['group'] > 100) $v['group'] = 100 ;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$season2[$k]['group'].'%') ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
$row++;

##

##

$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各季使用量/使用率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'季') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'使用量') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'達成率') ;


foreach ($season1 as $k => $v) {
	$row = $row2;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['usecount']) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	if ($v['use'] > 100) $v['use'] = 100 ;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$season2[$k]['use'].'%') ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
$row++;
$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->getFont()->getColor()->setARGB('E69500'); 

$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各季保證費/貢獻率') ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(18);
$row++;

$col = 65;
$row2 = $row;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'季') ;
// draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'保證費') ;
// draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'回饋金') ;
draw_border($objPHPExcel,'A'.$row.':E'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).($row++),'貢獻率') ;


foreach ($season1 as $k => $v) {
	$row = $row2;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$cht[$k]) ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['crtifiedMoney']) ;
	// $objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$v['feedBackMoney']) ;
	// $objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	if ($v['contribution'] > 100) $v['contribution'] = 100 ;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),$season2[$k]['contribution'].'%') ;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
	$col++;
}
// draw_border($objPHPExcel,'A'.$row2.':M'.$row) ;
$row++;
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