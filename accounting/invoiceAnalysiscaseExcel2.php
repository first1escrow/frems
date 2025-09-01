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
$objPHPExcel->getProperties()->setSubject("電子發票統計表");
$objPHPExcel->getProperties()->setDescription("第一建電子發票統計表");

$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getStyle('A1:A34')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:A34')->getFont()->getColor()->setARGB('0066FF'); 
$objPHPExcel->getActiveSheet()->getStyle('A1:A34')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:K34')->getFont()->setName('微軟正黑體');
$objPHPExcel->getActiveSheet()->getStyle('A1:A34')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->setCellValue('A1',$mn.'月') ;
$objPHPExcel->getActiveSheet()->setCellValue('A10','B2B') ;
$objPHPExcel->getActiveSheet()->setCellValue('A19','B2C') ;
$objPHPExcel->getActiveSheet()->setCellValue('A28','全部') ;

#####################

###################

#####################################本月############################################################
$col = 66;
$row = 1;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'本月B2B') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'本月B2C') ;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'本月總計') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2B月平均') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2C月平均') ;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月平均總計') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2B平均年張數') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2C平均年張數') ;
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'年平均總計') ;
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['三聯']['total']) ;//本月B2B
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['二聯']['total']) ;//本月B2C
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['total']) ;//本月總計
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['total']['三聯']) ;//B2B月平均
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['total']['二聯']) ;//B2C月平均
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['total']['average']) ;//月平均總計
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['total']['三聯']) ;//B2B平均年張數
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['total']['二聯']) ;//B2C平均年張數
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['total']['average']) ;//年平均總計

$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本數') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['三聯']['printY']);//本月B2B
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['二聯']['printY']) ;//本月B2C
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['printY']) ;//本月總計
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['printY']['三聯']) ;//B2B月平均
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['printY']['二聯']) ;//B2C月平均
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['printY']['average']) ;//月平均總計
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['printY']['三聯']) ;//B2B平均年張數
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['printY']['二聯']) ;//B2C平均年張數
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['printY']['average']) ;//年平均總計
// unset($inv_yat);unset($inv_mat);
$row++;$row++;
// #########################################################################################
$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'本月B2B') ;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'本月B2C') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2B月平均') ;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2C月平均') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2B平均年張數') ;
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2C平均年張數') ;
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['total']['三聯']['total'].'%') ;//本月B2B
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['total']['二聯']['total'].'%') ;//本月B2C
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['total']['三聯']['average'].'%') ;//B2B月平均
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['total']['二聯']['average'].'%') ;//B2C月平均
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_per['total']['三聯'].'%') ;//B2B平均年張數
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_per['total']['二聯'].'%') ;//B2C平均年張數
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本數') ;
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['printY']['三聯']['total'].'%') ;//本月B2B
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['printY']['二聯']['total'].'%') ;//本月B2C
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['printY']['三聯']['average'].'%') ;//B2B月平均
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_per['printY']['二聯']['average'].'%') ;//B2C月平均
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_per['printY']['三聯'].'%') ;//B2B平均年張數
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_per['printY']['二聯'].'%') ;//B2C平均年張數
$row++;

$col = 66; 
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本總張數') ;
$col2= $col+1;
$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr['A']."%") ;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$col++;
$col2= $col+1;
$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr['B']."%") ;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$col++;
$col2= $col+1;
$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr['C']."%") ;
// $col = $col+2;
// $col2= $col++;
// $objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr['B']."%") ;
// $col++;
// $objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr['C']."%") ;
// $col++;


$row++;$row++;$row++;
######################################B2B###################################################
$col = 66;
$col2 = 66+$month_count;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
foreach ($data[$yr] as $k => $v) {	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月') ;	
}
draw_border2($objPHPExcel,chr($col2).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總計') ;
draw_border($objPHPExcel,chr($col).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月平均') ;
draw_border($objPHPExcel,chr($col).$row,'R');
draw_border($objPHPExcel,chr($col).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'年平均總計') ;
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['三聯']['total']) ;

}

draw_border2($objPHPExcel,chr($col2).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['三聯']['total']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['total']['三聯']) ;
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['total']['三聯']) ;
$row++;


$col = 66;
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'B');
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本數') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['三聯']['printY']) ;
}
draw_border2($objPHPExcel,chr($col2).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['三聯']['printY']) ;
draw_border($objPHPExcel,chr($col).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['printY']['三聯']) ;
draw_border($objPHPExcel,chr($col).$row,'B');
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['printY']['三聯']) ;
$row++;$row++;

############################################################################################

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
foreach ($data[$yr] as $k => $v) {
	draw_border2($objPHPExcel,chr($col).$row);//.-.-
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;
foreach ($b2b['total'] as $k => $v) {
 
	
	draw_border2($objPHPExcel,chr($col).$row);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count'].'%') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;


$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;

foreach ($b2b['printY'] as $k => $v) {
 
	
	draw_border2($objPHPExcel,chr($col).$row);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count'].'%') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;$row++;$row++;
unset($tmp);
#########################################B2C################################################
$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月') ;
}
draw_border2($objPHPExcel,chr($col2).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總計') ;
draw_border($objPHPExcel,chr($col).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月平均') ;
draw_border($objPHPExcel,chr($col).$row,'T');
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'年平均總計') ;
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['二聯']['total']) ;
}
draw_border2($objPHPExcel,chr($col2).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['二聯']['total']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['total']['二聯']) ;
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['total']['二聯']) ;
$row++;


$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本數') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['二聯']['printY']) ;
}
draw_border2($objPHPExcel,chr($col2).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['二聯']['printY']) ;
draw_border($objPHPExcel,chr($col).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['printY']['二聯']) ;
draw_border($objPHPExcel,chr($col).$row,'B');
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['printY']['二聯']) ;
$row++;$row++;
############################################################################################

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
foreach ($data[$yr] as $k => $v) {
	draw_border2($objPHPExcel,chr($col).$row);//.-.-
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;
foreach ($b2c['total'] as $k => $v) {
 
	
	draw_border2($objPHPExcel,chr($col).$row);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count'].'%') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;
unset($tmp);


$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($b2c['printY'] as $k => $v) {
 
	
	draw_border2($objPHPExcel,chr($col).$row);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count'].'%') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;$row++;$row++;
unset($tmp);

#########################################全部################################################
$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月') ;
}
draw_border2($objPHPExcel,chr($col2).$row);
draw_border($objPHPExcel,chr($col).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總計') ;
draw_border($objPHPExcel,chr($col).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月平均') ;
draw_border($objPHPExcel,chr($col).$row,'T');
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'年平均總計') ;
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['total']) ;
}
draw_border2($objPHPExcel,chr($col2).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['total']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['total']['average']) ;
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['total']['average']) ;
$row++;


$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本數') ;
foreach ($data[$yr] as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['printY']) ;
}
draw_border2($objPHPExcel,chr($col2).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['printY']) ;
draw_border($objPHPExcel,chr($col).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$month_data['printY']['average']) ;
draw_border($objPHPExcel,chr($col).$row,'B');
draw_border($objPHPExcel,chr($col).$row,'R');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year_data['printY']['average']) ;
$row++;$row++;
############################################################################################

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'T');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
foreach ($data[$yr] as $k => $v) {
	draw_border2($objPHPExcel,chr($col).$row);//.-.-
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'開立總數') ;

foreach ($all['total'] as $k => $v) {
 
	
	draw_border2($objPHPExcel,chr($col).$row);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count'].'%') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;
unset($tmp);

$col = 66;
draw_border2($objPHPExcel,chr($col).$row);//.-.-
draw_border($objPHPExcel,chr($col).$row.":".chr($col2).$row,'B');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;

foreach ($all['printY'] as $k => $v) {
 
	
	draw_border2($objPHPExcel,chr($col).$row);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count'].'%') ;
}
draw_border($objPHPExcel,chr($col2).$row,'R');
$row++;$row++;$row++;
unset($tmp);
####################################框線########################################
draw_border($objPHPExcel,'B1:B3','L');//BY one 粗線的
draw_border($objPHPExcel,'B5:B8','L');//BY one 粗線的
draw_border($objPHPExcel,'B11:B13','L');//BY one 粗線的
draw_border($objPHPExcel,'B15:B17','L');//BY one 粗線的
draw_border($objPHPExcel,'B20:B22','L');//BY one 粗線的
draw_border($objPHPExcel,'B24:B26','L');//BY one 粗線的
draw_border($objPHPExcel,'B29:B31','L');//BY one 粗線的
draw_border($objPHPExcel,'B33:B35','L');//BY one 粗線的

draw_border($objPHPExcel,'B1:K1','T');//BY one 粗線的
draw_border($objPHPExcel,'B5:H5','T');//BY one 粗線的


draw_border($objPHPExcel,'B3:K3','B');//BY one 粗線的
draw_border($objPHPExcel,'B8:H8','B');//BY one 粗線的



draw_border($objPHPExcel,'K1:K3','R');//BY one 粗線的
draw_border($objPHPExcel,'H5:H8','R');//BY one 粗線的

#########################################顏色###################################################
// $objPHPExcel->getActiveSheet()->getStyle('C6:H7')->getFont()->getColor()->setARGB('FF0000'); 
// $objPHPExcel->getActiveSheet()->getStyle('C15:F16')->getFont()->getColor()->setARGB('FF0000'); 
// $objPHPExcel->getActiveSheet()->getStyle('C24:F25')->getFont()->getColor()->setARGB('FF0000'); 
// $objPHPExcel->getActiveSheet()->getStyle('C33:F34')->getFont()->getColor()->setARGB('FF0000'); 
############################################################################################
function draw_border($objPHPExcel,$cells,$type) {

	switch ($type) {
		case 'L':
			$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

		break;
		case 'R':
			$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

		break;
		case 'T':
			$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

		break;
		case 'B':
			$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		break;
			
		default:
		$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			break;
	}

	
}

function draw_border2($objPHPExcel,$cells) {


	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT);

	
}

	

############################################################################################
$_file = 'invoiceAnalysis2.xlsx' ;

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