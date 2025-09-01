<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';
ini_set('memory_limit', '1024M');

//個別店家
if($_POST['act'] == 'excel') {
    $conn = new first1DB;

    $sql = "SELECT c.*, s.aStoreName, s.aTitle, s.aName, s.aAmount, s.aSales, s.aBgFlag AS storeBg
            FROM `tActivityRecordsCase` AS c 
            LEFT JOIN `tActivityRecordsStore` AS s ON c.aStoreId = s.aId
            WHERE c.aCreatedAt > '2025-08-01'
            ORDER BY c.aStoreId
            ";
    $cases = $conn->all($sql);

    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 2025活動");
    $objPHPExcel->getProperties()->setDescription("第一建經 2025活動");

    //指定目前工作頁
    $objPHPExcel->setActiveSheetIndex(0);

    //
    $row = 1;

    $lastStoreId = null;
    foreach ($cases as $k => $v) {
        if($v['aStoreId'] != $lastStoreId) {
            if($lastStoreId != null) {
                $row++;
                $row++;
            }

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '店名');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '辦法');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '禮券類型');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, '件數');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, '績效分數業務');
            $row++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $v['aStoreName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $v['aTitle']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $v['aName']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $v['aAmount']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $v['aSales']);

            if($v['storeBg'] == 1) {
                //設定背景顏色為"淡黃色"
                $backcolor = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color'=> array('rgb' => 'FFFFAB')
                    ),
                );
                $objPHPExcel->getActiveSheet()->getStyle("A".$row.":G".$row)->applyFromArray($backcolor);
            }

            $row++;
            $row++;

            $_storeName = null;unset($_storeName);
            $col = 65;
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '序號');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '保證號碼');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店編號');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介店名');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '賣方');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '買方');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '總價金');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '合約保證費');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '進案日期');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '簽約日期');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '實際點交日期');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士姓名');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '標的物座落');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '狀態');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '仲介業務');
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, '地政士業務');

            $row++;

            $j = 0;
        }


        $j++;
        $col        = 65;
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $j);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $v['aCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aBranchCode']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aBranchName']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aOwner']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aBuyer']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aTotalMoney']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aCertifiedMoney']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aApplyDate']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aSignDate']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aEndDate']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aScrivener']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aAddress']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aCaseStatus']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['realtySales']);
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $row, $v['aScrivenerSales']);
        if($v['aBgFlag'] == 1) {
            //設定背景顏色為"淡黃色"
            $backcolor = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color'=> array('rgb' => 'FFC4AB')
                ),
            );
            $objPHPExcel->getActiveSheet()->getStyle("A".$row.":P".$row)->applyFromArray($backcolor);
        }

        $row++;

        unset($applyDate, $signDate, $endDate);
        $lastStoreId = $v['aStoreId'];
    }

    // $_file = '2022Act.xlsx';
    $_file = 'Act_' . 2025 . '_' . 1 . '_' . uniqid() . '.xlsx';

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
}

$smarty->display('act_2025_1.inc.tpl', '', 'actives');