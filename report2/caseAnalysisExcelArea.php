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

$row++;
$col = 71;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');	

$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['countfirst']));

$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoneyfirst']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoneyfirst']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoneyfirst']));
$col++;

$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['countsinopac']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoneysinopac']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoneysinopac']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoneysinopac']));
$col++;
$col++;
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['counttaishin']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoneytaishin']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoneytaishin']));
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoneytaishin']));

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
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['count']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoney']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoney']));

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoney']));

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['countfirst']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoneyfirst']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoneyfirst']));

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoneyfirst']));
	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['countsinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoneysinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoneysinopac']));

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoneysinopac']));

	$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['counttaishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoneytaishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoneytaishin']));

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoneytaishin']));
	$row++;$row++;
}
##
// $objPHPExcel->setActiveSheetIndex(0);
##
$sql = "SELECT zCity FROM tZipArea WHERE zCity IN('台北市','新北市','台中市','台南市','高雄市','桃園市') GROUP BY zCity ORDER BY nid";

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$colCity[] = $rs->fields['zCity'];


	$rs->MoveNext();
}
// $colCity[] = '未知';



$sheet = 1;


foreach ($colCity as $key => $value) {

	$objPHPExcel->createSheet() ;
	$objPHPExcel->setActiveSheetIndex($sheet) ;
	$objPHPExcel->getActiveSheet()->setTitle($value);

	setTable($value);//縣市
	$sheet++;

	##

}



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
#########################################3

function setTable($cat){

	global $objPHPExcel,$total,$data,$year;

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

	$objPHPExcel->getActiveSheet()->setCellValue('B2',setZero($total['count'.$cat]));
	$objPHPExcel->getActiveSheet()->setCellValue('C2',setZero($total['totalMoney'.$cat]));
	$objPHPExcel->getActiveSheet()->setCellValue('D2',setZero($total['certifiedMoney'.$cat]));
	$objPHPExcel->getActiveSheet()->setCellValue('E2',setZero($total['feedbackmoney'.$cat]));


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
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['count'.$cat.'first']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoney'.$cat.'first']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoney'.$cat.'first']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoney'.$cat.'first']));

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['count'.$cat.'sinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoney'.$cat.'sinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoney'.$cat.'sinopac']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoney'.$cat.'sinopac']));

	$col++;$col++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['count'.$cat.'taishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['totalMoney'.$cat.'taishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['certifiedMoney'.$cat.'taishin']));
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($total['feedbackmoney'.$cat.'taishin']));
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
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['count'.$cat.'']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoney'.$cat.'']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoney'.$cat.'']));

			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoney'.$cat.'']));

			$col++;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['count'.$cat.'first']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoney'.$cat.'first']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoney'.$cat.'first']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoney'.$cat.'first']));
			$col++;$col++;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['count'.$cat.'sinopac']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoney'.$cat.'sinopac']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoney'.$cat.'sinopac']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoney'.$cat.'sinopac']));

			$col++;$col++;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['count'.$cat.'taishin']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['totalMoney'.$cat.'taishin']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['certifiedMoney'.$cat.'taishin']));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($v['feedbackmoney'.$cat.'taishin']));
			$row++;
		}


		$col = 65;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':Y'.$row)->getFill()->getStartColor()->setARGB('F8ECE9');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'小計');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['count'.$cat]));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoney'.$cat]));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoney'.$cat]));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoney'.$cat]));

		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['count'.$cat.'first']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoney'.$cat.'first']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoney'.$cat.'first']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoney'.$cat.'first']));
		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['count'.$cat.'sinopac']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoney'.$cat.'sinopac']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoney'.$cat.'sinopac']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoney'.$cat.'sinopac']));

		$col++;$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');	
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['count'.$cat.'taishin']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['totalMoney'.$cat.'taishin']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['certifiedMoney'.$cat.'taishin']));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,setZero($year[$key]['feedbackmoney'.$cat.'taishin']));

		$row++;$row++;
	}
}	





?>