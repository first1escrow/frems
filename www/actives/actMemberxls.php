<?php
require_once '../../bank/Classes/PHPExcel.php' ;
require_once '../../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../../openadodb.php' ;



$sql= "SELECT 
		ro.*,
		(SELECT nSubject FROM tNews AS n WHERE n.nId =ro.rActivity) AS nSubject
	FROM 
		`tRegistOnline` AS ro
	WHERE
		rActivity = '".$id."' AND rField = '".$field."'
	ORDER BY
		rId
	DESC";

$rs = $conn->Execute($sql);


while (!$rs->EOF) {
	
	$list[] = $rs->fields;
	$rs->MoveNext();
}

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("課程名單");
$objPHPExcel->getProperties()->setDescription("課程名單");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
##
$c = 65; //B欄開始
$r =1;

// $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle("A1:H1")->getFill()->getStartColor()->setARGB('FFEBEB');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'課程名稱');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'場次');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'報名者姓名');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'身分別');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'單位');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'參加人數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'聯絡市話1');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'聯絡市話2');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'聯絡市話3');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'聯絡手機號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'電子郵件');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'聯絡地址');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'備註');

$r++;

for ($i=0; $i < count($list); $i++) { 
	$c = 65;
	switch ($list[$i]['rIdentity']) {
		case '1':
			$list[$i]['rIdentity'] = "一般人士";
			break;
		case '2':
			$list[$i]['rIdentity'] = "地政士";
			break;
		case '3':
			$list[$i]['rIdentity'] = "仲介";
			break;
		
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['nSubject']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rField']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rName']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rIdentity']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rUnit']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rNo']);

	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($c++).$r, $list[$i]['rTel1'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($c++).$r, $list[$i]['rTel2'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($c++).$r, $list[$i]['rTel3'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($c++).$r, $list[$i]['rMobile'],PHPExcel_Cell_DataType::TYPE_STRING); 

	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rEmail']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rAddr']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$list[$i]['rMemo']);

	$r++;


}


$_file = 'actmemberlist.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
exit;


##
?>