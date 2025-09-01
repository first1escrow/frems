<?php

//公元年轉民國年
function ChineseDate($date)
{
    if (!preg_match("/^\d+\-\d+\-\d+$/", $date)) {
        return '';
    }

    $tmp = explode('-', $date);
    if ($tmp[0] >= 1911) {
        $tmp[0] -= 1911;
    }

    return implode('/', $tmp);
}
##

//產生Excel表
$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("代墊利息專戶");
$objPHPExcel->getProperties()->setDescription("代墊利息專戶明細表");

/**
 * 一銀城東
 */
$account = '55006110050011';

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//設置標題等相關定義
require __DIR__ . '/prePaidInterestExcelTemplate.php';

$index   = 1; //序號
$cell_no = 3; //列

//建立內容
$objPHPExcel->getActiveSheet()->setCellValue('A' . $cell_no, $index++);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $cell_no, \First1\V1\Util\Util::convertDateToEast($from_date, '-', '/'));
$objPHPExcel->getActiveSheet()->setCellValue('C' . $cell_no, $chengdong_balance);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $cell_no, $chengdong_balance);

//千分位
$objPHPExcel->getActiveSheet()->getStyle('C' . $cell_no . ':E' . $cell_no)->getNumberFormat()->setFormatCode('#,##0');

//繪製外框
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->applyFromArray($border_frame);

//水平垂直置中欄位
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$cell_no++;

if (!empty($chengdong)) {
    $total = $chengdong_balance;
    foreach ($chengdong as $v) {
        $total += $v['income'];
        $total -= $v['money'];

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $cell_no, $index++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $cell_no, ChineseDate($v['date']));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $cell_no, $v['income']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $cell_no, $v['money']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $cell_no, $total);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $cell_no, $v['kind']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $cell_no, $v['case']['bank']);

        $objPHPExcel->getActiveSheet()->getCell('H' . $cell_no)->setValueExplicit($v['case']['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);

        $objPHPExcel->getActiveSheet()->setCellValue('I' . $cell_no, ChineseDate($v['case']['signDate']));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $cell_no, $v['case']['scrivener']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $cell_no, $v['case']['buyer']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $cell_no, $v['case']['owner']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $cell_no, $v['case']['undertaker']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $cell_no, $v['case']['status']);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $cell_no, ChineseDate($v['case']['cCertifyDate']));
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $cell_no, $v['case']['cBankList']);

        //繪製外框
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->applyFromArray($border_frame);

        //水平垂直置中欄位
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //千分位
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getNumberFormat()->setFormatCode('#,##0');

        //設定顯示背景色
        $background_color = ($v['income'] > 0) ? '00F4B084' : '00D9D9D9';
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getFill()->getStartColor()->setARGB($background_color);

        $cell_no++;
    }

    $total = $background_color = null;
    unset($total, $background_color);
}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('一銀城東');
############################

/**
 * 一銀桃園
 */
$account = '60001110019411';

//指定目前工作頁
$objWorkSheet = $objPHPExcel->createSheet(1); //Setting index when creating

$objPHPExcel->setActiveSheetIndex(1);

//設置標題等相關定義
require __DIR__ . '/prePaidInterestExcelTemplate.php';

//建立內容
$index   = 1; //序號
$cell_no = 3; //列

//建立內容
$objPHPExcel->getActiveSheet()->setCellValue('A' . $cell_no, $index++);
$objPHPExcel->getActiveSheet()->setCellValue('B' . $cell_no, \First1\V1\Util\Util::convertDateToEast($from_date, '-', '/'));
$objPHPExcel->getActiveSheet()->setCellValue('C' . $cell_no, $taoyuan_balance);
$objPHPExcel->getActiveSheet()->setCellValue('E' . $cell_no, $taoyuan_balance);

//千分位
$objPHPExcel->getActiveSheet()->getStyle('C' . $cell_no . ':E' . $cell_no)->getNumberFormat()->setFormatCode('#,##0');

//繪製外框
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->applyFromArray($border_frame);

//水平垂直置中欄位
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$cell_no++;

if (!empty($taoyuan)) {
    $total = $taoyuan_balance;
    foreach ($taoyuan as $v) {
        $total += $v['income'];
        $total -= $v['money'];

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $cell_no, $index++);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $cell_no, ChineseDate($v['date']));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $cell_no, $v['income']);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $cell_no, $v['money']);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $cell_no, $total);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $cell_no, $v['kind']);
        $objPHPExcel->getActiveSheet()->setCellValue('G' . $cell_no, $v['case']['bank']);
        $objPHPExcel->getActiveSheet()->getCell('H' . $cell_no)->setValueExplicit($v['case']['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('I' . $cell_no, ChineseDate($v['case']['signDate']));
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $cell_no, $v['case']['scrivener']);
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $cell_no, $v['case']['buyer']);
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $cell_no, $v['case']['owner']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $cell_no, $v['case']['undertaker']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $cell_no, $v['case']['status']);
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $cell_no, ChineseDate($v['case']['cCertifyDate']));
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $cell_no, $v['case']['cBankList']);

        //繪製外框
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->applyFromArray($border_frame);

        //水平垂直置中欄位
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //千分位
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getNumberFormat()->setFormatCode('#,##0');

        //設定顯示背景色
        $background_color = ($v['income'] > 0) ? '00F4B084' : '00D9D9D9';
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getFill()->getStartColor()->setARGB($background_color);

        $cell_no++;
    }

    $total = $background_color = null;
    unset($total, $background_color);
}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('一銀桃園');
############################

//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$_file = 'prePaidInterest_' . date("YmdHis") . '.xlsx';

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;
