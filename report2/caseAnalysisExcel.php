<?php
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件報表");
$objPHPExcel->getProperties()->setDescription("案件統計報表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('案件統計報表');
//寫入表頭資料
##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->getStartColor()->setARGB('FDFF37');

##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','總計');		
$objPHPExcel->getActiveSheet()->setCellValue('B1','案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1','買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1','合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','合約總回饋金金額');

$objPHPExcel->getActiveSheet()->setCellValue('A2','');	
$objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0');


$objPHPExcel->getActiveSheet()->setCellValue('B2',$total['count']);
$objPHPExcel->getActiveSheet()->setCellValue('C2',$total['totalMoney']);
$objPHPExcel->getActiveSheet()->setCellValue('D2',$total['certifiedMoney']);
$objPHPExcel->getActiveSheet()->setCellValue('E2',$total['feedbackmoney']);

$col = 71;$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

$row++;
$col = 71;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	

$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countfirst']);

$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneyfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneyfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneyfirst']);
$col++;
$col++;
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countsinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneysinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneysinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneysinopac']);
$col++;
$col++;
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['counttaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneytaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneytaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneytaishin']);

##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
##
$row = 4;
foreach ($data as $key => $value) {



	$col = 65;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':Y'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$key.'年');	
	$row++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('E4BEB1');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;

	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;

	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;
	
	$row++;
	ksort($value);
	foreach ($value as $k => $v) {

		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney']);

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyfirst']);
		
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countsinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneysinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneysinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneysinopac']);

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['counttaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneytaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneytaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneytaishin']);

		$row++;
	}


	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('F8ECE9');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'小計');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney']);

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countfirst']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyfirst']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyfirst']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyfirst']);
	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countsinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneysinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneysinopac']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneysinopac']);

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['counttaishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneytaishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneytaishin']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneytaishin']);
	$row++;$row++;
}

// $objPHPExcel->setActiveSheetIndex(0);
##
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
$objPHPExcel->getActiveSheet()->setTitle('台屋直營');

##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->getStartColor()->setARGB('FDFF37');
##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','總計');		
$objPHPExcel->getActiveSheet()->setCellValue('B1','案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1','買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1','合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','合約總回饋金金額');

$objPHPExcel->getActiveSheet()->setCellValue('A2','');	
$objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0');


$objPHPExcel->getActiveSheet()->setCellValue('B2',$total['count2']);
$objPHPExcel->getActiveSheet()->setCellValue('C2',$total['totalMoney2']);
$objPHPExcel->getActiveSheet()->setCellValue('D2',$total['certifiedMoney2']);
$objPHPExcel->getActiveSheet()->setCellValue('E2',$total['feedbackmoney2']);

$col = 71;$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$row++;
$col = 71;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['count2first']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoney2first']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoney2first']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoney2first']));

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['count2sinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoney2sinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoney2sinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoney2sinopac']);

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['count2taishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoney2taishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoney2taishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoney2taishin']);
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
##
$row = 4;
foreach ($data as $key => $value) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':Y'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$key.'年');	
	$row++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('E4BEB1');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	
	$row++;
	ksort($value);
	foreach ($value as $k => $v) {

		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月');

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count2']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney2']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney2']);

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney2']);

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['count2first']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoney2first']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoney2first']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoney2first']));
		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count2sinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney2sinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney2sinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney2sinopac']);

		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count2taishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney2taishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney2taishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney2taishin']);
		$row++;
	}


	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('F8ECE9');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'小計');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count2']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney2']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney2']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney2']);

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['count2first']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoney2first']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoney2first']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoney2first']));
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count2sinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney2sinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney2sinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney2sinopac']);

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count2taishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney2taishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney2taishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney2taishin']);

	$row++;$row++;
}

##
##
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(2) ;
$objPHPExcel->getActiveSheet()->setTitle('台屋加盟');

##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->getStartColor()->setARGB('FDFF37');
##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','總計');		
$objPHPExcel->getActiveSheet()->setCellValue('B1','案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1','買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1','合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','合約總回饋金金額');

$objPHPExcel->getActiveSheet()->setCellValue('A2','');	
$objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0');


$objPHPExcel->getActiveSheet()->setCellValue('B2',$total['countT']);
$objPHPExcel->getActiveSheet()->setCellValue('C2',$total['totalMoneyT']);
$objPHPExcel->getActiveSheet()->setCellValue('D2',$total['certifiedMoneyT']);
$objPHPExcel->getActiveSheet()->setCellValue('E2',$total['feedbackmoneyT']);

$col = 71;$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

$row++;
$col = 71;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countTfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneyTfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneyTfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneyTfirst']);
$col++;$col++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countTsinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneyTsinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneyTsinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneyTsinopac']);

$col++;$col++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countTtaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneyTtaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneyTtaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneyTtaishin']);
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
##
$row = 4;
foreach ($data as $key => $value) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':Y'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$key.'年');	
	$row++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('E4BEB1');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	
	$row++;
	ksort($value);
	foreach ($value as $k => $v) {

		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月');

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countT']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyT']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyT']);

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyT']);

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countTfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyTfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyTfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyTfirst']);
		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countTsinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyTsinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyTsinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyTsinopac']);
		

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countTtaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyTtaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyTtaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyTtaishin']);
		$row++;
	}


	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('F8ECE9');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'小計');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countT']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyT']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyT']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyT']);

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countTfirst']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyTfirst']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyTfirst']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyTfirst']);
	
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countTsinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyTsinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyTsinopac']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyTsinopac']);

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countTtaishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyTtaishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyTtaishin']);

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyTtaishin']);

	$row++;$row++;
}

##
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(3) ;
$objPHPExcel->getActiveSheet()->setTitle('優美地產');

##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->getStartColor()->setARGB('FDFF37');
##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','總計');		
$objPHPExcel->getActiveSheet()->setCellValue('B1','案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1','買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1','合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','合約總回饋金金額');

$objPHPExcel->getActiveSheet()->setCellValue('A2','');	
$objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0');


$objPHPExcel->getActiveSheet()->setCellValue('B2',setZero($total['countU']));
$objPHPExcel->getActiveSheet()->setCellValue('C2',setZero($total['totalMoneyU']));
$objPHPExcel->getActiveSheet()->setCellValue('D2',setZero($total['certifiedMoneyU']));
$objPHPExcel->getActiveSheet()->setCellValue('E2',setZero($total['feedbackmoneyU']));

$col = 71;$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$row++;
$col = 71;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['countUfirst']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoneyUfirst']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoneyUfirst']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoneyUfirst']));

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['countUsinopac']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoneyUsinopac']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoneyUsinopac']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoneyUsinopac']));

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['countUtaishin']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoneyUtaishin']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoneyUtaishin']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoneyUtaishin']));
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
##
$row = 4;
foreach ($data as $key => $value) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':Y'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$key.'年');	
	$row++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('E4BEB1');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	
	$row++;
	ksort($value);
	foreach ($value as $k => $v) {

		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月');

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['countU']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoneyU']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoneyU']));

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoneyU']));

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['countUfirst']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoneyUfirst']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoneyUfirst']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoneyUfirst']));
		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['countUsinopac']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoneyUsinopac']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoneyUsinopac']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoneyUsinopac']));

		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['countUtaishin']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoneyUtaishin']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoneyUtaishin']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoneyUtaishin']));
		$row++;
	}


	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('F8ECE9');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'小計');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['countU']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoneyU']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoneyU']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoneyU']));

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['countUfirst']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoneyUfirst']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoneyUfirst']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoneyUfirst']));
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['countUsinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoneyUsinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoneyUsinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoneyUsinopac']));

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['countUtaishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoneyUtaishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoneyUtaishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoneyUtaishin']));

	$row++;$row++;
}


##
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(4) ;
$objPHPExcel->getActiveSheet()->setTitle('加盟其他');

##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->getStartColor()->setARGB('FDFF37');
##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','總計');		
$objPHPExcel->getActiveSheet()->setCellValue('B1','案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1','買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1','合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','合約總回饋金金額');

$objPHPExcel->getActiveSheet()->setCellValue('A2','');	
$objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0');


$objPHPExcel->getActiveSheet()->setCellValue('B2',$total['countO']);
$objPHPExcel->getActiveSheet()->setCellValue('C2',$total['totalMoneyO']);
$objPHPExcel->getActiveSheet()->setCellValue('D2',$total['certifiedMoneyO']);
$objPHPExcel->getActiveSheet()->setCellValue('E2',$total['feedbackmoneyO']);

$col = 71;$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$row++;
$col = 71;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countOfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneyOfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneyOfirst']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneyOfirst']);
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countOsinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneyOsinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneyOsinopac']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneyOsinopac']);
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['countOtaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoneyOtaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoneyOtaishin']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoneyOtaishin']);
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
##
$row = 4;
foreach ($data as $key => $value) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':Y'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$key.'年');	
	$row++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('E4BEB1');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$row++;
	ksort($value);
	foreach ($value as $k => $v) {

		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月');

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countO']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyO']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyO']);

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyO']);

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countOfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyOfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyOfirst']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyOfirst']);
		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countOsinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyOsinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyOsinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyOsinopac']);

		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['countOtaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoneyOtaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoneyOtaishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoneyOtaishin']);
		$row++;
	}


	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('F8ECE9');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'小計');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countO']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyO']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyO']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyO']);

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countOfirst']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyOfirst']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyOfirst']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyOfirst']);

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countOsinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyOsinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyOsinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyOsinopac']);

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['countOtaishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoneyOtaishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoneyOtaishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoneyOtaishin']);
	

	$row++;$row++;
}


##
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(5) ;
$objPHPExcel->getActiveSheet()->setTitle('代書個人');

##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->getFill()->getStartColor()->setARGB('FDFF37');
##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','總計');		
$objPHPExcel->getActiveSheet()->setCellValue('B1','案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1','買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1','合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('E1','合約總回饋金金額');

$objPHPExcel->getActiveSheet()->setCellValue('A2','');	
$objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle('E2')->getNumberFormat()->setFormatCode('#,##0');


$objPHPExcel->getActiveSheet()->setCellValue('B2',$total['count3']);
$objPHPExcel->getActiveSheet()->setCellValue('C2',$total['totalMoney3']);
$objPHPExcel->getActiveSheet()->setCellValue('D2',$total['certifiedMoney3']);
$objPHPExcel->getActiveSheet()->setCellValue('E2',$total['feedbackmoney3']);

$col = 71;$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
$row++;
$col = 71;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['count3first']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoney3first']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoney3first']);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoney3first']);

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['count3sinopac']);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoney3sinopac']);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoney3sinopac']);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoney3sinopac']);

$col++;$col++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['count3taishin']);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['totalMoney3taishin']);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['certifiedMoney3taishin']);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$total['feedbackmoney3taishin']);
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
##
$row = 4;
foreach ($data as $key => $value) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':Y'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$key.'年');	
	$row++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('E4BEB1');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'月份');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'一銀');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'永豐');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新');		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件總筆數');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買賣總價金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總保證費金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約總回饋金金額');
	
	$row++;
	ksort($value);
	foreach ($value as $k => $v) {

		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k.'月');

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count3']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney3']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney3']);

		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney3']);

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count3first']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney3first']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney3first']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney3first']);
		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count3sinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney3sinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney3sinopac']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney3sinopac']);

		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['count3taishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['totalMoney3taishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney3taishin']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['feedbackmoney3taishin']);
		$row++;
	}


	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('F8ECE9');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'小計');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count3']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney3']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney3']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney3']);

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count3first']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney3first']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney3first']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney3first']);
	
	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count3sinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney3sinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney3sinopac']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney3sinopac']);

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['count3taishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['totalMoney3taishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['certifiedMoney3taishin']);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$year[$key]['feedbackmoney3taishin']);

	$row++;$row++;
}

$objPHPExcel->setActiveSheetIndex(0);
// echo "<pre>";
// print_r($list2);
// echo "</pre>";
// die;

##
$_file = 'case.xlsx' ;

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