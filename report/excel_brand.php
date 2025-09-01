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
$objPHPExcel->getProperties()->setSubject("品牌統計表");
$objPHPExcel->getProperties()->setDescription("品牌統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('品牌統計表');
$objPHPExcel->getActiveSheet()->mergeCells('A1:P1');

$sql = "SELECT bName FROM tBrand WHERE bId = '" . $brand . "'";
$rs  = $conn->Execute($sql);

$objPHPExcel->getActiveSheet()->setCellValue('A1', $rs->fields['bName'] . "進案統計");

//寫入清單標題列資料
$col = 65;
$row = 2;
//欄位:序號、(買賣)合約書編號/保證號碼、物件委託書編號、仲介店編號、仲介店名、賣方、買方、總價金、合約保證費、案件狀態日期、地政士事務所、地政士姓名、標的物座落、件數、經紀業、備註
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '(買賣)合約書編號/保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '物件委託書編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '簽約日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '結案日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士事務所');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '件數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '經紀業');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '備註');
$row++;

$CaseCountTotal = 0;
for ($i = 0; $i < count($data); $i++) {
    $storeArray = array();
    $sql        = "SELECT bNo72 FROM tBankCode WHERE bAccount = '" . $data[$i]['cEscrowBankAccount'] . "'";
    $rs         = $conn->Execute($sql);
    $buildNo    = $rs->fields['bNo72'];

    //取得各仲介店姓名與編號
    $bStore  = $realty[$data[$i]['branch']]['bStore'];
    $bNo     = $data[$i]['bCode'];
    $company = $data[$i]['branchName'];
    $storeArray[$data[$i]['brand']]++;
    $storeArray['total']++;

    if ($data[$i]['branch1'] > 0) {
        $bStore .= ' ' . $realty[$data[$i]['branch1']]['bStore'];
        $bNo .= ' ' . $data[$i]['bCode1'];
        $company .= ' ' . $data[$i]['branchName1'];
        $storeArray[$data[$i]['brand1']]++;
        $storeArray['total']++;
    }

    if ($data[$i]['branch2'] > 0) {
        $bStore .= ' ' . $realty[$data[$i]['branch2']]['bStore'];
        $bNo .= ' ' . $data[$i]['bCode2'];
        $company .= ' ' . $data[$i]['branchName2'];
        $storeArray[$data[$i]['brand2']]++;
        $storeArray['total']++;
    }

    if ($data[$i]['branch3'] > 0) {
        $bStore .= ' ' . $realty[$data[$i]['branch3']]['bStore'];
        $bNo .= ' ' . $data[$i]['bCode3'];
        $company .= ' ' . $data[$i]['branchName3'];
        $storeArray[$data[$i]['brand3']]++;
        $storeArray['total']++;
    }

    $data[$i]['bStore']    = $bStore;
    $data[$i]['bId']       = $bNo;
    $data[$i]['Com']       = $company;
    $data[$i]['caseCount'] = round($storeArray[$brand] / $storeArray['total'], 2);
    $CaseCountTotal += $data[$i]['caseCount'];

    $date = ($status == '3') ? $data[$i]['cEndDate'] : $date = $data[$i]['cSignDate'];

    $col = 65;

    $tmp = null;unset($tmp);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, ($i + 1));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $data[$i]['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $buildNo);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['bId']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['bStore']);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['owner']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['buyer']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cTotalMoney']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cCertifiedMoney']);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cSignDate']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cEndDate']);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['sOffice']); //
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['scrivener']);

    $zc                = $data[$i]['zCity'];
    $data[$i]['cAddr'] = preg_replace("/$zc/", "", $data[$i]['cAddr']);
    $zc                = $data[$i]['zArea'];
    $data[$i]['cAddr'] = preg_replace("/$zc/", "", $data[$i]['cAddr']);

    $data[$i]['cAddr'] = $data[$i]['cZip'] . $data[$i]['zCity'] . $data[$i]['zArea'] . $data[$i]['cAddr'];

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['cAddr']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['caseCount']);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $data[$i]['Com']);

    $buildNo = null;unset($buildNo);

    $row++;
}

$objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':N' . $row);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '合計');
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); //水平
$objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $CaseCountTotal);

$objPHPExcel->getActiveSheet()->getStyle("A1:" . 'R' . $row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

$_file = iconv('UTF-8', 'BIG5', '品牌統計表');
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
