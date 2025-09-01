<?php
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經案件資料查詢明細結果");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(36);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(36);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(36);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(12);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(24);

//繪製框線
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);

//總表標題列填色
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getFill()->getStartColor()->setARGB('00D9D9D9');

//設定垂直文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//設定水平文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('A1', '序號');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '對象');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '發票方式');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '發票金額');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '指定');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '利息');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '指定');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '總價金');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '實際點交日');
$objPHPExcel->getActiveSheet()->setCellValue('N1', '簽約日');
$objPHPExcel->getActiveSheet()->setCellValue('O1', '地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue('P1', '標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue('Q1', '狀態');

//寫入查詢資料
$certifiedId = '';
if (!empty($detail)) {
    $record_count = 0;
    foreach ($detail as $k => $v) {
        $row_index = $k + 2; //儲存格列數

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, ($k + 1));

        if ($certifiedId != $v['cCertifiedId']) { //當前一筆保證號碼與目前的保證號碼不相同時，輸入顯示相關欄位
            $record_count++;

            $objPHPExcel->getActiveSheet()->getCell('B' . $row_index)->setValueExplicit($v['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING); //保證號碼

            $realty = empty($v['Realty']['code']) ? '' : $v['Realty']['code'];
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $realty);

            $realty = empty($v['Realty']['name']) ? '' : $v['Realty']['name'];
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_index, $realty);
        }

        $objPHPExcel->getActiveSheet()->getCell('E' . $row_index)->setValueExplicit($v['target']);

        if (!empty($v['cInvoiceMoney'])) {
            $invoice_method = '電子';
            $invoice_method = ($v['cInvoiceDonate'] == 1) ? '捐贈' : $invoice_method;
            $invoice_method = ($v['cInvoicePrint'] == 'Y') ? '列印' : $invoice_method;

            $objPHPExcel->getActiveSheet()->getCell('F' . $row_index)->setValueExplicit($invoice_method);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_index, $v['cInvoiceMoney']);
        }

        if (!empty($v['invoiceExt'])) {
            $ext = [];

            foreach ($v['invoiceExt'] as $va) {
                $invoice_method = '電子';
                $invoice_method = ($v['cInvoiceDonate'] == 1) ? '捐贈' : $invoice_method;
                $invoice_method = ($v['cInvoicePrint'] == 'Y') ? '列印' : $invoice_method;

                $ext[] = $va['cName'] . '(' . $va['cIdentifyId'] . '、$' . $va['cInvoiceMoney'] . '、' . $invoice_method . ')';
                $objPHPExcel->getActiveSheet()->getCell('E' . $row_index)->setValueExplicit($va['target']); //因為不想額外取得仲介店名，所以重複更新E欄
            }

            $ext = implode(', ', $ext);
            $objPHPExcel->getActiveSheet()->getCell('H' . $row_index)->setValueExplicit($ext);
        }

        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_index, empty($v['cInterestMoney']) ? '' : $v['cInterestMoney']);
        if (!empty($v['interestExt'])) {
            $ext = [];

            foreach ($v['interestExt'] as $va) {
                $ext[] = $va['cName'] . '(' . $va['cIdentifyId'] . '、$' . $va['cInterestMoney'] . ')';
                $objPHPExcel->getActiveSheet()->getCell('E' . $row_index)->setValueExplicit($va['target']); //因為不想額外取得仲介店名，所以重複更新E欄
            }

            $ext = implode(', ', $ext);
            $objPHPExcel->getActiveSheet()->getCell('J' . $row_index)->setValueExplicit($ext);
        }

        if ($certifiedId != $v['cCertifiedId']) { //當前一筆保證號碼與目前的保證號碼不相同時，輸入顯示相關欄位
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row_index, $v['cTotalMoney']);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row_index, $v['cCertifiedMoney']);

            // $deliver_date = empty($v['tBankLoansDate']) ? $v['cBankList'] : $v['tBankLoansDate'];
            // $objPHPExcel->getActiveSheet()->setCellValue('M' . $row_index, $deliver_date);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $row_index, $v['endDate']);

            $objPHPExcel->getActiveSheet()->setCellValue('N' . $row_index, $v['signDate']);
            $objPHPExcel->getActiveSheet()->getCell('O' . $row_index)->setValueExplicit($v['scrivener']);
            $objPHPExcel->getActiveSheet()->getCell('P' . $row_index)->setValueExplicit($v['address']);
            $objPHPExcel->getActiveSheet()->getCell('Q' . $row_index)->setValueExplicit($v['caseStatus']);
        }

        if ($record_count % 2 == 0) {
            //總表標題列填色
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index . ':Q' . $row_index)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index . ':Q' . $row_index)->getFill()->getStartColor()->setARGB('00DAEEF3');
        }

        $certifiedId = $v['cCertifiedId']; //更新目前保證號碼已進行下筆紀錄比對
    }
}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('案件利息與發票清單');

$objPHPExcel->setActiveSheetIndex(0);

$_file = 'interestInvoiceList_' . time() . '.xlsx';

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
