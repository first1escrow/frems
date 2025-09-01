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
$objPHPExcel->getProperties()->setDescription("第一建經案件統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('案件統計報表');

//寫入清單標題列資料
$col = 65;
$row = 4;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '出款保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '進案日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '銀行出款日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '簽約日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士業務');

if ($sEndDate && $eEndDate) {
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '事務所名稱');
}

if ($status == 10) {
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '案件餘額');
}

$row++;

for ($i = 0; $i < count($data); $i++) {
    if ($data[$i]['branch'] > 0) {
        $tmp_sales[] = getBranchSales($data[$i]['branch']);
    }

    if ($data[$i]['branch1'] > 0) {
        $tmp_sales[] = getBranchSales($data[$i]['branch1']);
    }

    if ($data[$i]['branch2'] > 0) {
        $tmp_sales[] = getBranchSales($data[$i]['branch2']);
    }

    if ($data[$i]['branch3'] > 0) {
        $tmp_sales[] = getBranchSales($data[$i]['branch3']);
    }

    $data[$i]['salesName'] = @implode(',', $tmp_sales);
    $tmp_sales             = null;unset($tmp_sales);

    $data[$i]['Scrsales'] = getScrivenerSales($data[$i]['cScrivener']);

    //取得各仲介店姓名與編號
    $bStore = $realty[$data[$i]['branch']]['bStore'];
    $bNo    = $data[$i]['bCode'];

    if ($data[$i]['branch1'] > 0) {
        $bStore .= ' ' . $realty[$data[$i]['branch1']]['bStore'];
        $bNo .= ' ' . $data[$i]['bCode1'];
    }

    if ($data[$i]['branch2'] > 0) {
        $bStore .= ' ' . $realty[$data[$i]['branch2']]['bStore'];
        $bNo .= ' ' . $data[$i]['bCode2'];
    }

    if ($data[$i]['branch3'] > 0) {
        $bStore .= ' ' . $realty[$data[$i]['branch3']]['bStore'];
        $bNo .= ' ' . $data[$i]['bCode3'];
    }

    $data[$i]['bStore'] = $bStore;
    $data[$i]['bId']    = $bNo;

    $col = 65;

    $tmp = null;unset($tmp);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($i + 1));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $data[$i]['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['bId']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['bStore']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['owner']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['buyer']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cTotalMoney']);

    $tmp = round(($data[$i]['cTotalMoney'] - $data[$i]['cFirstMoney']) * 0.0006); //萬分之六

    if ($tmp > ($data[$i]['cCertifiedMoney'] + 10)) {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cCertifiedMoney']);
    } else {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cCertifiedMoney']);
    }

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['tMoney']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['showcCaseFeedBackMoney']);

    $date = ($status == '3') ? $data[$i]['cEndDate'] : $data[$i]['cSignDate'];

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $date);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cApplyDate']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cEndDate']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['tBankLoansDate']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cSignDate']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['scrivener']);

    $zc                = $data[$i]['zCity'];
    $data[$i]['cAddr'] = preg_replace("/$zc/", "", $data[$i]['cAddr']);
    $zc                = $data[$i]['zArea'];
    $data[$i]['cAddr'] = preg_replace("/$zc/", "", $data[$i]['cAddr']);

    $data[$i]['cAddr'] = $data[$i]['cZip'] . $data[$i]['zCity'] . $data[$i]['zArea'] . $data[$i]['cAddr'];

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cAddr']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['status']);

    #顏色
    if ($data[$i]['branch1'] > 0 && ($data[$i]['bScrRecall'] != 0 || $data[$i]['bScrRecall1'] != 0 || $data[$i]['bScrRecall2'] != 0)) {
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':P' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':P' . $row)->getFill()->getStartColor()->setARGB('FF7878');
    }

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['salesName']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['Scrsales']);

    if ($sEndDate && $eEndDate) {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['sOffice']);
    }

    if ($status == 10) {
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $arr[$i]['cCaseMoney']);
    }

    $tmp = $tmp2 = $date = null;
    unset($tmp, $tmp2, $date);

    $row++;
}

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:B2');
$objPHPExcel->getActiveSheet()->mergeCells('C1:E1');
$objPHPExcel->getActiveSheet()->mergeCells('C2:E2');
$objPHPExcel->getActiveSheet()->mergeCells('F1:H1');
$objPHPExcel->getActiveSheet()->mergeCells('F2:H2');
$objPHPExcel->getActiveSheet()->mergeCells('I1:J1');
$objPHPExcel->getActiveSheet()->mergeCells('I2:J2');

$objPHPExcel->getActiveSheet()->setCellValue('A1', '案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '回饋總金額');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '收入');
$objPHPExcel->getActiveSheet()->setCellValue('A2', $max);
$objPHPExcel->getActiveSheet()->setCellValue('C2', $totalMoney);
$objPHPExcel->getActiveSheet()->setCellValue('F2', $certifiedMoney);
$objPHPExcel->getActiveSheet()->setCellValue('I2', $cCaseFeedBackMoney);
$objPHPExcel->getActiveSheet()->setCellValue('K2', ($certifiedMoney - $cCaseFeedBackMoney));

$_file = iconv('UTF-8', 'BIG5', '案件統計表');
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
