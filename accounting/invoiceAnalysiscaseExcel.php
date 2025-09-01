<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;


$sql = "SELECT * FROM tContractInvoiceCount";

$rs = $conn->Execute($sql);


while (!$rs->EOF) {
	$tmp = explode('-', $rs->fields['cMonth']);
	$tmp[0] = $tmp[0]-1911;
	##月##
	$data[$tmp[0]][$tmp[1]]['total'] = $rs->fields['cInvoiceTotal'];//發票開立張數
	$data[$tmp[0]][$tmp[1]]['printY'] = $rs->fields['cInvoiceTotalPrint'];//列印紙本張數
	$data[$tmp[0]][$tmp[1]]['printN'] = $rs->fields['cInvoiceTotalPrintN'];//未列印紙本張數
	//二聯OR三聯
	
	$data[$tmp[0]][$tmp[1]]['二聯']['total'] = $rs->fields['cB2C']; 
	$data[$tmp[0]][$tmp[1]]['三聯']['total'] = $rs->fields['cB2B']; 

	$data[$tmp[0]][$tmp[1]]['二聯']['printY'] = $rs->fields['cB2Cprint'];
	$data[$tmp[0]][$tmp[1]]['三聯']['printY'] = $rs->fields['cB2Bprint'];

	$data[$tmp[0]][$tmp[1]]['二聯']['printN'] = $rs->fields['cB2CprintN'];
	$data[$tmp[0]][$tmp[1]]['三聯']['printN'] = $rs->fields['cB2BprintN'];
	##
	##年##
	$data2[$tmp[0]]['total'] += $rs->fields['cInvoiceTotal'];
	$data2[$tmp[0]]['printY'] += $rs->fields['cInvoiceTotalPrint'];
	$data2[$tmp[0]]['printN'] += $rs->fields['cInvoiceTotalPrintN'];
	$data2[$tmp[0]]['二聯']['total'] += $rs->fields['cB2C'];
	$data2[$tmp[0]]['三聯']['total'] += $rs->fields['cB2B'];
	$data2[$tmp[0]]['二聯']['printY'] += $rs->fields['cB2Cprint'];
	$data2[$tmp[0]]['三聯']['printY'] += $rs->fields['cB2Bprint'];
	$data2[$tmp[0]]['二聯']['printN'] += $rs->fields['cB2CprintN'];
	$data2[$tmp[0]]['三聯']['printN'] += $rs->fields['cB2BprintN'];

	##
	
	$sum2['total'] += $rs->fields['cInvoiceTotal'];
	$sum2['printY'] += $rs->fields['cInvoiceTotalPrint'];
	$sum2['printN'] += $rs->fields['cInvoiceTotalPrintN'];

	$sum2['二聯']['total'] += $rs->fields['cB2C'];
	$sum2['三聯']['total'] += $rs->fields['cB2B'];

	$sum2['二聯']['printY'] += $rs->fields['cB2Cprint'];
	$sum2['三聯']['printY'] += $rs->fields['cB2Bprint'];
	$sum2['二聯']['printN'] += $rs->fields['cB2CprintN'];
	$sum2['三聯']['printN'] += $rs->fields['cB2BprintN'];
	##
	// //(當年以有開發票的月列入計算)，因104年才開始有發票除以4個月，
	if (!in_array($tmp[1], $tmp_month) && $tmp[0] == $yr) {
		$tmp_month[] = $tmp[1];
		$month_count++;		
	}

	if (!in_array($tmp[0], $tmp_year)) {
		$tmp_year[] = $tmp[0];
		$sum2['count']++;
	}	

	unset($tmp);
	$rs->MoveNext();
}
// $sum2['count'] = count($data2);
// $month_count = count($data2[$yr]);
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("電子發票統計表");
$objPHPExcel->getProperties()->setDescription("第一建電子發票統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($yr.'年 '.$mn.'月統計');
$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->getColor()->setARGB('FF0000'); //紅色
$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setSize(18);
$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


//數量統計
$objPHPExcel->getActiveSheet()->setCellValue('A1',$yr.'年 '.$mn.'月統計') ;
$row = 3;
$col = 65;
draw_border($objPHPExcel,'A'.$row.':D'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'全部') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2B') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2C') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['total']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['三聯']['total']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['二聯']['total']) ;
draw_border($objPHPExcel,'A'.$row.':D'.$row) ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['printY']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['三聯']['printY']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['二聯']['printY']) ;
draw_border($objPHPExcel,'A'.$row.':D'.$row) ;
$row++;


$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['printN']) ;
if ($data[$yr][$search_m]['三聯']['printN'] == '') {
	$data[$yr][$search_m]['三聯']['printN'] = 0;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['三聯']['printN']) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$yr][$search_m]['二聯']['printN']) ;
draw_border($objPHPExcel,'A'.$row.':D'.$row) ;
$row++;$row++;
############################
//各佔比
//三聯VS二聯
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'各佔比') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
$row++;

$col = 65;
draw_border($objPHPExcel,'A'.$row.':C'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2B') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'B2C') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
if($data[$yr][$search_m]['total'] != 0)
{
	$tmp = round($data[$yr][$search_m]['三聯']['total']/$data[$yr][$search_m]['total'],4)*100;
	$tmp2 = round($data[$yr][$search_m]['二聯']['total']/$data[$yr][$search_m]['total'],4)*100;
}else{
	$tmp = $tmp2 = 0;
}

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp2.'%') ;
$row++;
unset($tmp);unset($tmp2);


$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
if($data[$yr][$search_m]['printY'] != 0)
{
	$tmp = round($data[$yr][$search_m]['三聯']['printY']/$data[$yr][$search_m]['printY'],4)*100;
	$tmp2 = round($data[$yr][$search_m]['二聯']['printY']/$data[$yr][$search_m]['printY'],4)*100;
}else{
	$tmp = $tmp2 = 0;
}

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp2.'%') ;
$row++;
unset($tmp);unset($tmp2);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
if($data[$yr][$search_m]['printN'] != 0)
{
	$tmp = round($data[$yr][$search_m]['三聯']['printN']/$data[$yr][$search_m]['printN'],4)*100;
	$tmp2 = round($data[$yr][$search_m]['二聯']['printN']/$data[$yr][$search_m]['printN'],4)*100;
}else{
	$tmp = $tmp2 = 0;
}

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp2.'%') ;
unset($tmp);unset($tmp2);
$row++;$row++;
##################################################################################
//列印紙本張數VS未列印張數
$col = 65;
draw_border($objPHPExcel,'A'.$row.':B'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
if($data[$yr][$search_m]['total'] != 0)
{
	$tmp = round($data[$yr][$search_m]['printY']/$data[$yr][$search_m]['total'],4)*100;
}else{
	$tmp = 0;
}
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
if($data[$yr][$search_m]['total'] != 0)
{
	$tmp = round($data[$yr][$search_m]['printN']/$data[$yr][$search_m]['total'],4)*100;
}else{
	$tmp = 0;
}
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
$row++;$row++;
unset($tmp);
###########################################################################################

//發票開立張數VS列印紙本張數VS未列印張數

$col = 65;
draw_border($objPHPExcel,'A'.$row.':B'.$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'本月佔比') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
if($data[$yr][$search_m]['total'] != 0)
{
	$tmp = round($data[$yr][$search_m]['total']/$data2[$yr]['total'],4)*100;
}else{
	$tmp = 0;
}
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
if($data[$yr][$search_m]['printY'] != 0)
{
	$tmp = round($data[$yr][$search_m]['printY']/$data2[$yr]['printY'],4)*100;
}else{
	$tmp = 0;
}
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
if($data[$yr][$search_m]['printN'] != 0)
{
	$tmp = round($data[$yr][$search_m]['printN']/$data2[$yr]['printN'],4)*100;
}else{
	$tmp = 0;
}
draw_border($objPHPExcel,'A'.$row.':'.chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
$row++;$row++;
unset($tmp);
#####################################################
//ALL
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
$objPHPExcel->getActiveSheet()->setTitle('發票數統計');

$objPHPExcel->getActiveSheet()->setCellValue('A1',$yr.'年度各月份發票數/平均發票數') ;

$col = 65;
$row = 2;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份') ;

foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總數') ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$objPHPExcel->getActiveSheet()->mergeCells('A1:'.chr($col).'1');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'平均') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['total']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['total']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['total']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;

unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['printY']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['printY']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['printY']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['printN']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['printN']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['printN']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
unset($tmp);
$row++;$row++;
######################################################################
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各月發票數佔比') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份') ;

foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['total'] > 0) {
		$tmp = round($value['total']/$data2[$yr]['total'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['printY'] > 0) {
		$tmp = round($value['printY']/$data2[$yr]['printY'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['printN'] > 0) {
		$tmp = round($value['printN']/$data2[$yr]['printN'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;$row++;
###########################################################################
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'每年發票數/平均發票數') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總數') ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'平均') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['total']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['total']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['total']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;
unset($tmp);


$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['printY']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['printY']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['printY']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['printN']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['printN']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['printN']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;$row++;
unset($tmp);
###########################################################################################3
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'每年發票佔比') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['total']/$sum2['total'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['printY']/$sum2['printY'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['printN']/$sum2['printN'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);
###########################################################################################3
//B2C發票
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(2) ;
$objPHPExcel->getActiveSheet()->setTitle('B2C發票數統計');

$objPHPExcel->getActiveSheet()->setCellValue('A1',$yr.'年度各月份B2C發票數/平均發票數') ;

$col = 65;
$row = 2;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份') ;

foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總數') ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$objPHPExcel->getActiveSheet()->mergeCells('A1:'.chr($col).'1');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'平均') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['二聯']['total']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['二聯']['total']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['二聯']['total']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;

unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['二聯']['printY']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['二聯']['printY']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['二聯']['printY']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['二聯']['printN']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['二聯']['printN']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['二聯']['printN']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
unset($tmp);
$row++;$row++;
#########################################################################################
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各月發票數佔比') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份') ;

foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['二聯']['total'] > 0) {
		$tmp = round($value['二聯']['total']/$data2[$yr]['二聯']['total'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['二聯']['printY'] > 0) {
		$tmp = round($value['二聯']['printY']/$data2[$yr]['二聯']['printY'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['二聯']['printN'] > 0) {
		$tmp = round($value['二聯']['printN']/$data2[$yr]['二聯']['printN'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;$row++;
###########################################################################
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'每年發票數/平均發票數') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總數') ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'平均') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['二聯']['total']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['二聯']['total']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['二聯']['total']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;
unset($tmp);


$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['二聯']['printY']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['二聯']['printY']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['二聯']['printY']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['二聯']['printN']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['二聯']['printN']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['二聯']['printN']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;$row++;
unset($tmp);
###########################################################################################3
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'每年發票佔比') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['二聯']['total']/$sum2['二聯']['total'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['二聯']['printY']/$sum2['二聯']['printY'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['二聯']['printN']/$sum2['二聯']['printN'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);
###########################################################################################3
###########################################################################################3
//B2B發票
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(3) ;
$objPHPExcel->getActiveSheet()->setTitle('B2B發票數統計');

$objPHPExcel->getActiveSheet()->setCellValue('A1',$yr.'年度各月份B2B發票數/平均發票數') ;

$col = 65;
$row = 2;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份') ;

foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總數') ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$objPHPExcel->getActiveSheet()->mergeCells('A1:'.chr($col).'1');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'平均') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['三聯']['total']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['三聯']['total']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['三聯']['total']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;

unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['三聯']['printY']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['三聯']['printY']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['三聯']['printY']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
foreach ($data[$yr] as $key => $value) {
	if ($value['三聯']['printN'] == '') {
		$value['三聯']['printN'] = 0;
	}
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['三聯']['printN']) ;
}
if ($data2[$yr]['三聯']['printN'] == '') {
	$data2[$yr]['三聯']['printN'] = 0;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data2[$yr]['三聯']['printN']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = round($data2[$yr]['三聯']['printN']/$month_count,2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
unset($tmp);
$row++;$row++;
#########################################################################################
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$yr.'年度各月發票數佔比') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份') ;

foreach ($data[$yr] as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['三聯']['total'] > 0) {
		$tmp = round($value['三聯']['total']/$data2[$yr]['三聯']['total'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['三聯']['printY'] > 0) {
		$tmp = round($value['三聯']['printY']/$data2[$yr]['三聯']['printY'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;
foreach ($data[$yr] as $key => $value) {
	$tmp = 0;
	if ($data2[$yr]['三聯']['printN'] > 0) {
		$tmp = round($value['三聯']['printN']/$data2[$yr]['三聯']['printN'],4)*100;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp.'%') ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
unset($tmp);
$row++;$row++;
###########################################################################
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'每年發票數/平均發票數') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總數') ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'平均') ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['三聯']['total']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['三聯']['total']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['三聯']['total']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;
unset($tmp);


$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['三聯']['printY']) ;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['三聯']['printY']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['三聯']['printY']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未列印張數') ;

foreach ($data2 as $key => $value) {
	if ($value['三聯']['printN'] == '') {
		$value['三聯']['printN'] = 0;
	}
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['三聯']['printN']) ;
}
if ($sum2['三聯']['printN'] == '') {
	$sum2['三聯']['printN'] = 0;
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$sum2['三聯']['printN']) ;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$tmp = $sum2['三聯']['printN']/$sum2['count'];
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp) ;
$row++;$row++;
unset($tmp);
###########################################################################################3
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'每年發票佔比') ;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':'.chr($col).$row);
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'') ;

foreach ($data2 as $key => $value) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key) ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'發票開立張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['三聯']['total']/$sum2['三聯']['total'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['三聯']['printY']/$sum2['三聯']['printY'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);

$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'列印紙本張數') ;
foreach ($data2 as $key => $value) {
	$tmp = round($value['三聯']['printN']/$sum2['三聯']['printN'],4)*100;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp."%") ;
}
$col = $col-1;
draw_border($objPHPExcel,'A'.$row.":".chr($col).$row) ;
$row++;
unset($tmp);
###########################################################################################3

##
function draw_border($objPHPExcel,$cells) {
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getTop()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getBottom()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getLeft()->getColor()->setARGB('00000000');
	$objPHPExcel->getActiveSheet()->getStyle($cells)->getBorders()->getRight()->getColor()->setARGB('00000000');
}
##
$objPHPExcel->setActiveSheetIndex(0);
$_file = 'invoiceAnalysis.xlsx' ;

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