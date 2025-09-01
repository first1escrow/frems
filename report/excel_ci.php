<?php
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

//取得相關案件的其他人資訊
function getOthers(&$conn, $cIds)
{
    if (empty($cIds)) {
        return [];
    }

    $sql = 'SELECT cCertifiedId, cIdentity, cName FROM tContractOthers WHERE cCertifiedId IN ("' . implode('","', $cIds) . '") AND cIdentity IN (1, 2, 5)';
    $rs  = $conn->Execute($sql);

    $others = [];
    while (!$rs->EOF) {
        $record = $rs->fields;

        if (in_array($record['cIdentity'], [1, 5])) {
            $others[$record['cCertifiedId']]['buyer'][] = $record['cName'];
        }

        if ($record['cIdentity'] == 2) {
            $others[$record['cCertifiedId']]['owner'][] = $record['cName'];
        }

        $record = null;unset($record);

        $rs->MoveNext();
    }

    return $others;
}

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("群義案件統計表");
$objPHPExcel->getProperties()->setDescription("第一建經群義案件統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('群義案件統計報表');

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(39);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(38);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(38);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(39);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(46);

//設定字體
$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

//設定標題
$objPHPExcel->getActiveSheet()->setCellValue('A1', '序號');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '物件編號(委託書編號)');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '合約書編號');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '買仲');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '賣仲');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '出賣人');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '買受人');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '買賣總價金');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '保證費');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '簽約日期');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '進案日期');
// $objPHPExcel->getActiveSheet()->setCellValue('L1', '結案日期');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '地政士');
$objPHPExcel->getActiveSheet()->setCellValue('N1', '地政士事務所');
$objPHPExcel->getActiveSheet()->setCellValue('O1', '標的座落');

//設定標題文字為粗體
$objPHPExcel->getActiveSheet()->getStyle('A1:O1')->getFont()->setBold(true);

//設定文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//內容
$others = getOthers($conn, array_column($data, 'cCertifiedId'));

foreach ($data as $k => $v) {
    $row = ($k + 2);

    //買仲
    $realty_buy = [];
    if (!empty($v['branch']) && in_array($v['cServiceTarget'], [1, 3])) {
        $realty_buy[] = $realty[$v['branch']]['brand'] . $realty[$v['branch']]['bStore'];
    }

    if (!empty($v['branch1']) && in_array($v['cServiceTarget1'], [1, 3])) {
        $realty_buy[] = $realty[$v['branch1']]['brand'] . $realty[$v['branch1']]['bStore'];
    }

    if (!empty($v['branch2']) && in_array($v['cServiceTarget2'], [1, 3])) {
        $realty_buy[] = $realty[$v['branch2']]['brand'] . $realty[$v['branch2']]['bStore'];
    }

    if (!empty($v['branch3']) && in_array($v['cServiceTarget3'], [1, 3])) {
        $realty_buy[] = $realty[$v['branch3']]['brand'] . $realty[$v['branch3']]['bStore'];
    }

    //賣仲
    $realty_sell = [];
    if (!empty($v['branch']) && in_array($v['cServiceTarget'], [1, 2])) {
        $realty_sell[] = $realty[$v['branch']]['brand'] . $realty[$v['branch']]['bStore'];
    }

    if (!empty($v['branch1']) && in_array($v['cServiceTarget1'], [1, 2])) {
        $realty_sell[] = $realty[$v['branch1']]['brand'] . $realty[$v['branch1']]['bStore'];
    }

    if (!empty($v['branch2']) && in_array($v['cServiceTarget2'], [1, 2])) {
        $realty_sell[] = $realty[$v['branch2']]['brand'] . $realty[$v['branch2']]['bStore'];
    }

    if (!empty($v['branch3']) && in_array($v['cServiceTarget3'], [1, 2])) {
        $realty_sell[] = $realty[$v['branch3']]['brand'] . $realty[$v['branch3']]['bStore'];
    }

    //出賣人
    $realty_owner = $v['owner'];
    $realty_owner .= empty($others[$v['cCertifiedId']]['owner']) ? '' : '等' . (count($others[$v['cCertifiedId']]['owner']) + 1) . '人';

    //買受人
    $realty_buyer = $v['buyer'];
    $realty_buyer .= empty($others[$v['cCertifiedId']]['buyer']) ? '' : '等' . (count($others[$v['cCertifiedId']]['buyer']) + 1) . '人';

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, ($k + 1));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $v['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
    // $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
    // $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, implode(' ', $realty_buy), PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, implode(' ', $realty_sell), PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $realty_owner, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $realty_buyer, PHPExcel_Cell_DataType::TYPE_STRING);
    // $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $v['cCaseMoney'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $v['cTotalMoney']);
    // $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row, $v['cCaseFeedBackMoney'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $v['cCertifiedMoney']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $v['cSignDate'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row, $v['cApplyDate'], PHPExcel_Cell_DataType::TYPE_STRING);
    // $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row, $v['cEndDate'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('M' . $row, $v['scrivener'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('N' . $row, $v['sOffice'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('O' . $row, $v['zCity'] . $v['zArea'] . $v['cAddr'], PHPExcel_Cell_DataType::TYPE_STRING);

    $realty_buy = $realty_sell = $realty_owner = $realty_buyer = null;
    unset($realty_buy, $realty_sell, $realty_owner, $realty_buyer);
}

//產出
$_file = iconv('UTF-8', 'BIG5', '群義案件統計報表');

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
