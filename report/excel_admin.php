<?php
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件統計表");
$objPHPExcel->getProperties()->setDescription("第一建經行政報表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('表一');

//寫入清單標題列資料
$col = 65;
$row = 1;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '實際點交日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '經辦');

$row++;
$statistics = [];
for ($i = 0; $i < count($data); $i++) {
    $col = 65;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($i + 1));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $data[$i]['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, substr($data[$i]['cEndDate'], 0, 10));
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['sUndertaker1']);

    $statistics[$data[$i]['sUndertaker1']]++;
    $row++;
}

//指定第二頁工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1);

//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('表二');

//寫入清單標題列資料
$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '經辦');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '件數');

$row++;
$i = 0;
foreach ($statistics as $key => $statistic) {
    $col = 65;
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($i + 1));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $key, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $statistic);

    $i++;
    $row++;
}



$_file = iconv('UTF-8', 'BIG5', '行政報表');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file . '.xlsx');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;