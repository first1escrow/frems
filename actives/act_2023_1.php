<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';
ini_set('memory_limit', '1024M');

//各別店家
if($_POST['act'] == 'excel') {
    $conn = new first1DB;

    $sql = 'SELECT c.*, s.aStoreName, s.aTitle, s.aName, s.aAmount, s.aSales, s.aBgFlag AS storeBg
            FROM `tActivityRecordsCase` AS c 
            LEFT JOIN `tActivityRecordsStore` AS s ON c.aStoreId = s.aId
            ORDER BY c.aStoreId
            ';
    $cases = $conn->all($sql);

    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 2023活動");
    $objPHPExcel->getProperties()->setDescription("第一建經 2023活動");

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
    $_file = 'Act_' . 2023 . '_' . 1 . '_' . uniqid() . '.xlsx';

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

//品牌
/*
if($_POST['brand_act'] == 'excel2') {

    $conn = new first1DB;

    $sql = 'SELECT 
                c.*, b.aBrandName, b.aTitle, b.aName, b.aAmount
            FROM 
                `tActivityRecordsCase` AS c 
            LEFT JOIN 
                `tActivityRecordsBrand` AS b ON c.aBrandId = b.aBrandId
            WHERE 
                c.aBrandId !=0
            ORDER BY 
                c.aBrandId
            ';
    $cases = $conn->all($sql);

    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 2023活動");
    $objPHPExcel->getProperties()->setDescription("第一建經 2023活動");

    //指定目前工作頁
    $objPHPExcel->setActiveSheetIndex(0);

    //
    $row = 1;

    $lastaBrandId = null;
    foreach ($cases as $k => $v) {
        if($v['aBrandId'] != $lastaBrandId) {
            if($lastaBrandId != null) {
                $row++;
                $row++;
            }

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '品牌名');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '辦法');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '禮券類型');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, '件數');
            $row++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $v['aBrandName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $v['aTitle']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $v['aName']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $v['aAmount']);

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
        $lastaBrandId = $v['aBrandId'];
    }

    // $_file = '2022Act.xlsx';
    $_file = 'Act_' . 2023 . '_' . 1 . '_brand' . uniqid() . '.xlsx';

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
*/

//集團
if($_POST['group_act'] == 'excel3') {
    //案件
    $conn = new first1DB;

    $sql = 'SELECT 
                c.*, g.aGroupName, g.aTitle, g.aName, g.aAmount
            FROM 
                `tActivityRecordsCase` AS c 
            LEFT JOIN 
                `tActivityRecordsGroup` AS g ON c.aGroupId = g.aId
            WHERE 
                c.aGroupId IS NOT NULL
            ORDER BY 
                g.aGroupName
            ';
    $cases = $conn->all($sql);

    //統計
    $sql = "SELECT 
                *
            FROM
                `tActivityGroup` AS g 
            ORDER BY
                g.aGroup
            ";
    $groupStores = $conn->all($sql);

    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 2023活動");
    $objPHPExcel->getProperties()->setDescription("第一建經 2023活動");

    //指定目前工作頁
    $objPHPExcel->setActiveSheetIndex(0);

    $row = 1;

    //案件明細
    $lastaGroupId = null;
    foreach ($cases as $k => $v) {
        if($v['aGroupId'] != $lastaGroupId) {
            if($lastaGroupId != null) {
                $row++;
                $row++;
            }

            $sql = "
            SELECT 
                group_concat(distinct s.aSales) as sales
            FROM 
                `tActivityRecordsCase` AS c 
            LEFT JOIN 
                `tActivityRecordsStore` AS s 
              ON c.aStoreId = s.aId
            WHERE 
                c.aGroupId = ". $v['aGroupId'] ."
            ";
            $groupConcatSales = $conn->one($sql);


            $groupSales = $groupConcatSales['sales'];

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '集團名稱');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '辦法');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '禮券類型');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, '件數');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, '業務');
            $row++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row . '');
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $v['aGroupName']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $v['aTitle']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $v['aName']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $v['aAmount']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $groupSales);

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
        $lastaGroupId = $v['aGroupId'];
    }

    //集團統計表
    $objPHPExcel->createSheet(1);
    $objPHPExcel->setActiveSheetIndex(1);


    $sheet2Row = 1;
    $lastGroupName = null;
    foreach ($groupStores as $groupStore) {
        if($groupStore['aGroup'] != $lastGroupName) {
            if($lastGroupName != null) {
                $sheet2Row++;
                $sheet2Row++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sheet2Row, $groupStore['aGroup']);
            $sheet2Row++;
        }
        $col        = 65;
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $sheet2Row, $groupStore['aFullStoreId']); //店編
        if($groupStore['aIdentity'] == 'S') {
            //代書
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $sheet2Row, $groupStore['aScrivenerName']); //地政士姓名
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $sheet2Row, $groupStore['aOffice']); //事務所名稱
        } else {
            //仲介
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $sheet2Row, $groupStore['aBrand']); //仲介品牌
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $sheet2Row, $groupStore['aBranchStore']); //仲介店名
            $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $sheet2Row, $groupStore['aBranchName']); //仲介公司全名
        }
        $objPHPExcel->getActiveSheet()->setCellValue(chr($col++) . $sheet2Row, $groupStore['aAmount']); //件數
        $sheet2Row++;
        $lastGroupName = $groupStore['aGroup'];
    }

    $_file = 'Act_' . 2023 . '_' . 1 . '_group' . uniqid() . '.xlsx';

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

#新簽約店家
if($_POST['new_act'] == 'excel4') {
    $conn = new first1DB;

    $sql = "
            SELECT *
            FROM (
                 SELECT  s.sType,
                     ('SC') AS code,
                     s.sStore, scr.sOffice AS name, s.sSignDate, SalesForP.sSales,
                     (SELECT pName FROM tPeopleInfo WHERE pId=SalesForP.sSales) as bSalesName,
                     a.aId,
                     (SELECT COUNT(*) 
                     FROM tActivityRecordsCase AS c 
                     WHERE c.aApplyDate >= '112-03-01' AND c.aApplyDate <= '112-08-31' AND c.aMainStoreId = s.sStore AND c.aIdentity = 'S') AS amount
                 FROM tSalesSign AS s
                     LEFT JOIN tActivityRecords AS a ON (s.sStore = a.aStoreId AND a.aIdentity = 'S' AND a.aActivityId = 2)
                     LEFT JOIN tScrivener AS scr ON s.sStore = scr.sId
                     LEFT JOIN tScrivenerSalesForPerformance AS SalesForP ON s.sStore = SalesForP.sScrivener
                 WHERE 
                     a.aId IS NOT NULL 
                   AND
                     s.sSignDate >= '2023-03-01' 
                   AND s.sType = 1
                UNION ALL
                SELECT  s.sType, 
                   (SELECT bCode FROM tBrand WHERE bId=branch.bBrand) as code,
                    s.sStore, branch.bName AS name, s.sSignDate, SalesForP.bSales,
                   (SELECT pName FROM tPeopleInfo WHERE pId=SalesForP.bSales) as bSalesName,
                   a.aId,
                   (SELECT COUNT(*) 
                   FROM tActivityRecordsCase AS c 
                   WHERE c.aApplyDate >= '112-03-01' AND c.aApplyDate <= '112-08-31' AND c.aMainStoreId = s.sStore AND c.aIdentity = 'R') AS amount
                FROM tSalesSign AS s
                   LEFT JOIN tActivityRecords AS a ON (s.sStore = a.aStoreId AND a.aIdentity = 'R' AND a.aActivityId = 2)
                   LEFT JOIN tBranch AS branch ON s.sStore = branch.bId
                   LEFT JOIN tBranchSalesForPerformance AS SalesForP ON s.sStore = SalesForP.bBranch
                WHERE 
                   a.aId IS NOT NULL 
                  AND
                   s.sSignDate >= '2023-03-01' 
                  AND s.sType = 2
            ) AS store ORDER BY sSales ,sSignDate
            ";

    $store = $conn->all($sql);

    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
    require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

    $objPHPExcel = new PHPExcel();
    //Set properties 設置文件屬性
    $objPHPExcel->getProperties()->setCreator("第一建經");
    $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
    $objPHPExcel->getProperties()->setTitle("第一建經");
    $objPHPExcel->getProperties()->setSubject("第一建經 2023活動");
    $objPHPExcel->getProperties()->setDescription("第一建經 2023活動");

    $sheetIndex = 0;
    foreach ($store as $key => $value) {
        if($value['sSales'] != $store[$key-1]['sSales']) {
            //指定目前工作頁
            $objPHPExcel->createSheet($sheetIndex);
            $objPHPExcel->setActiveSheetIndex($sheetIndex);
            $objPHPExcel->getActiveSheet()->setTitle($value['bSalesName']);

            $row = 1;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '類型');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, '店編');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, '名稱');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, '店家簽約日');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '成長件數');
            $sheetIndex++;
        }

        if($value['amount'] != 0) {
            $row++;

            $value['sType'] = ($value['sType'] == 1) ? '地政士' : '仲介';
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $value['sType']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $value['code'] . $value['sStore']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $value['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $value['sSignDate']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $value['amount']);
        }
    }

    $_file = 'Act_' . 2023 . '_' . 2 . '_new' . uniqid() . '.xlsx';

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

$smarty->display('act_2023_1.inc.tpl', '', 'actives');