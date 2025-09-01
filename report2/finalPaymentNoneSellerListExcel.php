<?php
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("");
$objPHPExcel->getProperties()->setDescription("");

$objPHPExcel->setActiveSheetIndex(0);

$col = 65;
$row = 1;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '結案日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '買方姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '賣方姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '買方匯出帳號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '賣方匯出帳號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '原因');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '經辦');

$row++;
for ($i = 0; $i < count($list); $i++) {
    $col = 65;

    $buyer = getBuyer($list[$i]['tMemo'], 'cName');
    $owner = getOwner($list[$i]['tMemo'], 'cName');

    $buyerAcc = array();
    $sql      = "SELECT tAccountName FROM tBankTrans WHERE tBankLoansDate BETWEEN '" . $sDate . "' AND '" . $eDate . "' AND tMemo = '" . $list[$i]['tMemo'] . "' AND tPayOk = 1 AND tBank_kind = '一銀' AND tObjKind IN ('解除契約','點交(結案)','建經發函終止') AND tKind ='買方'";
    $rs       = $conn->Execute($sql);
    while (!$rs->EOF) {
        array_push($buyerAcc, $rs->fields['tAccountName']);
        $rs->MoveNext();
    }

    $tmp = explode(',', $list[$i]['tAnother']);
    for ($j = 0; $j < count($tmp); $j++) {
        $str = '';
        if ($tmp[$j] == 5) {
            $noteArr[] = $Item[$tmp[$j]] . "-" . $list[$i]['tAnotherNote'];
        } else {
            if(in_array($tmp[$j], [1,3,4])) {
                $str = '授領人的關係：' . $list[$i]['relation' . $tmp[$j]];
            }
            $noteArr[] = $Item[$tmp[$j]] . $str;
        }
    }
    $note = @implode(';', $noteArr);

    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $list[$i]['tMemo'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $list[$i]['tBankLoansDate']);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $buyer);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $owner);

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, @implode('_', $buyerAcc));
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, @implode('_', $list[$i]['tAccountName']));

    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $note);
    $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, getUnderstack($list[$i]['tMemo']));

    $tmp = $noteArr = null;
    unset($tmp, $noteArr);

    $row++;

    $str = $rs->fields['tMemo'] . "," . $rs->fields['tBankLoansDate'] . "," . $buyer . "," . $owner . "," . iconv("utf-8", "big5", $note) . "\n";
}

$_file = 'seller.xlsx';

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
