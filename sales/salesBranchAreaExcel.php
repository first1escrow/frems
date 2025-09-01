<?php
include_once '../openadodb.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../includes/sales/getSalesArea.php';

// echo 'ok';

// exit;
$sql = "
		SELECT 
			za.zZip,
			za.zCity,
			za.zArea,
			za.zSales AS AreaSales
		FROM
			tZipArea AS za
	
		ORDER BY zZip ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$list[$rs->fields['zCity']][$rs->fields['zArea']] = getBranch($rs->fields['zZip'],'');
	
	$rs->MoveNext();
}


// ##
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("仲介店資料");
$objPHPExcel->getProperties()->setDescription("第一建經");
//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('業務區域資料');

$col =65;

## 
$objPHPExcel->getActiveSheet()->mergeCells("A1:C1");
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'1','仲介店業務歸屬');
$col =65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','地區');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','仲介店');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','店東/店長');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','電話');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','行動電話');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','地址');

$row = 3;
foreach ($list as $k => $v) {
	$col =65;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k);
	$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":H".$row."");
	$objPHPExcel->getActiveSheet()->getStyle("A".$row.":H".$row."")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle("A".$row.":H".$row."")->getFill()->getStartColor()->setARGB('FFDEDE');
	$row++;

	foreach ($list[$k] as $key => $value) {
		

		
		if (is_array($list[$k][$key])) {
			foreach ($list[$k][$key] as $ke => $branch) {
				$col =65;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branch['bCode']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branch['brand'].$branch['bStore']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branch['salesname']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branch['bManager']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branch['bTelArea'].'-'.$branch['bTelMain']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $branch['bMobileNum'],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $branch['city'].$branch['area'].$branch['bAddress'],PHPExcel_Cell_DataType::TYPE_STRING); 
	
				
				$row++;
			}	
		}else{
			$col =65;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$key);
			$row++;
		}
		

		
	}
	

	
}


$_file = 'salesAreaB'.date('Y-m-d').'.xlsx' ;
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