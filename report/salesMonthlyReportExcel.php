<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="業務業績主管月報表_' . date('Ymd') . '.xlsx"');
header('Cache-Control: max-age=0');

include_once '../configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../session_check.php';
require_once dirname(__DIR__) . '/includes/lib/contractBank.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

/**
 * 2024-09-30
 * 業務業績月報表 Excel
 */

$menu_sales[0] = '總表';
$menu_sales[66] = '公司';
$menu_sales[2] = '台屋體系';
//取得業務人員清單
$sql = 'SELECT * FROM tPeopleInfo WHERE pDep IN ("7","8") AND pJob =1 AND pId != 3 AND pId != 66 ORDER BY pId ASC;';
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $menu_sales[$rs->fields['pId']] = $rs->fields['pName'];

    if ($rs->fields['pJob'] == 2) {
        $menu_sales[$rs->fields['pId']] .= '(離)';
    }

    $rs->MoveNext();
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("First Real Estate Management Co.")
    ->setLastModifiedBy("First Real Estate Management Co.")
    ->setTitle("業務業績主管月報表")
    ->setSubject("業務業績主管月報表")
    ->setDescription("業務業績主管月報表")
    ->setKeywords("業務業績主管月報表")
    ->setCategory("業務業績主管月報表");
$objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$kind = (int)$_GET['kind'];
$dataKind = ($kind < 1 || $_GET['kind'] > 3) ? 2 : $_GET['kind'];
$year = date('Y');

$sheetIndex = 0;
foreach ($menu_sales as $sales_k => $sales_v) {
    $sid = $sales_k;
    $salesId = $sid;

    if ($sheetIndex > 0) {
        $objPHPExcel->createSheet();
    }
    $objPHPExcel->setActiveSheetIndex($sheetIndex);

    $objPHPExcel->getActiveSheet()
        ->setCellValue('A2', '資料月份')
//        ->setCellValue('B2', '履保費總額')
        ->setCellValue('B2', '平分後履保費總額')
        ->setCellValue('C2', '回饋金成本')
        ->setCellValue('D2', '實收')
        ->setCellValue('E2', '成本趴數')
        ->setCellValue('F2', '件數')
        ->setCellValue('J2', '資料月份')
//        ->setCellValue('J2', '履保費總額')
        ->setCellValue('K2', '平分後履保費總額')
        ->setCellValue('L2', '回饋金成本')
        ->setCellValue('M2', '實收')
        ->setCellValue('N2', '成本趴數')
        ->setCellValue('O2', '件數')
        ->setCellValue('P2', '實收漲跌幅')
        ->setCellValue('Q2', '件數漲跌幅');

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
//    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '業務業績主管月報表 - ' . $sales_v);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->mergeCells('P1:Q1');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '' . date("Y/m/d"));
    $objPHPExcel->getActiveSheet()->getStyle('P1')->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('P1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $objPHPExcel->getActiveSheet()->setTitle($sales_v);

    $sql = "SELECT * FROM www_first1_report.salesPerformanceReport where salesId = " . $sid . " AND dataKind = " . $dataKind;
    $rs = $conn->Execute($sql);

    $allSales = [];

    if ($rs) {
        while (!$rs->EOF) {
            if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoney'])) {
                $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoney'] = 0;
            }
            if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoneyAvg'])) {
                $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoneyAvg'] = 0;
            }
            if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['feedbackMoney'])) {
                $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['feedbackMoney'] = 0;
            }
            if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['performanceMoney'])) {
                $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['performanceMoney'] = 0;
            }
            if (!isset($allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['contractCaseCountAvg'])) {
                $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['contractCaseCountAvg'] = 0;
            }

            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoney'] += $rs->fields['certifiedMoney'];
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['certifiedMoneyAvg'] += $rs->fields['certifiedMoneyAvg'];
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['feedbackMoney'] += $rs->fields['feedbackMoney'];
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['performanceMoney'] += $rs->fields['performanceMoney'];
            $allSales[$rs->fields['salesId']][$rs->fields['dataYM']]['contractCaseCountAvg'] += $rs->fields['contractCaseCountAvg'];

            $rs->MoveNext();
        }
    }

    $allData = [];
    if(date('m') <= '03'){
        $startYM = (int)(($year - 2) . '01');
        $endYM = (int)(($year - 2) . '12');
    } else {
        $startYM = (int)(($year - 1) . '01');
        $endYM = (int)(($year - 1) . '12');
    }

    $tbl = "";
    $k = 3;

    for ($x = $startYM; $x <= $endYM; $x++) {

        $allData['certifiedMoney'] += $allSales[$salesId][$x]['certifiedMoney'];
        $allData['certifiedMoneyAvg'] += $allSales[$salesId][$x]['certifiedMoneyAvg'];
        $allData['feedbackMoney'] += $allSales[$salesId][$x]['feedbackMoney'];
        $allData['performanceMoney'] += $allSales[$salesId][$x]['performanceMoney'];
        $allData['contractCaseCountAvg'] += $allSales[$salesId][$x]['contractCaseCountAvg'];

        $ccc = ($allSales[$salesId][$x]['certifiedMoneyAvg'] > 0) ? round($allSales[$salesId][$x]['feedbackMoney']*100 / $allSales[$salesId][$x]['certifiedMoneyAvg'],2) : 0;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $k, substr($x, 0, 4) . '/' . substr($x, 4, 2));
//        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $k, ($allSales[$salesId][$x]['certifiedMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $k, ($allSales[$salesId][$x]['certifiedMoneyAvg']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $k, ($allSales[$salesId][$x]['feedbackMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $k, $ccc .'%');
        $objPHPExcel->getActiveSheet()->getStyle('E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $k, ($allSales[$salesId][$x]['performanceMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $k, $allSales[$salesId][$x]['contractCaseCountAvg'], PHPExcel_Cell_DataType::TYPE_NUMERIC);

        if (substr($x, -2, 2) == 12) {
            $x = (int)(substr($x, 0, 4) . '99') + 1;
        }
        $k++;
    }

    $ccc = ($allData['certifiedMoneyAvg'] > 0) ? round($allData['feedbackMoney']*100 / $allData['certifiedMoneyAvg'],2) : 0;
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $k, '總計');
//    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $k, ($allData['certifiedMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $k, ($allData['certifiedMoneyAvg']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $k, ($allData['feedbackMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $k, $ccc .'%');
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $k, ($allData['performanceMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $k, round($allData['contractCaseCountAvg'], 2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->getStyle('E' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $k . ':F' . $k)->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F8ECE9')
            )
        )
    );

    $per_performanceMoney = (int)$allData['performanceMoney'];
    $per_contractCaseCountAvg = (float)$allData['contractCaseCountAvg'];

    $allData = [];
    if(date('m') <= '03'){
        $startYM = (int)(($year - 1) . '01');
        $endYM = (int)(($year - 1) . '12');
    } else {
        $startYM = (int)($year . '01');
        $endYM = (int)($year . date('m'));
    }

    $tbl2 = "";
    $k = 3;

    for ($x = $startYM; $x <= $endYM; $x++) {

        $performanceIncrease = 0;
        if (isset($allSales[$salesId][$x - 100]['performanceMoney']) && $allSales[$salesId][$x - 100]['performanceMoney'] > 0) {
            $performanceIncrease = round((($allSales[$salesId][$x]['performanceMoney'] - $allSales[$salesId][$x - 100]['performanceMoney']) / $allSales[$salesId][$x - 100]['performanceMoney']) * 100);
        }
        $countIncrease = 0;
        if (isset($allSales[$salesId][$x - 100]['contractCaseCountAvg']) && $allSales[$salesId][$x - 100]['contractCaseCountAvg'] > 0) {
            $countIncrease = round((($allSales[$salesId][$x]['contractCaseCountAvg'] - $allSales[$salesId][$x - 100]['contractCaseCountAvg']) / $allSales[$salesId][$x - 100]['contractCaseCountAvg']) * 100);
        }

        $allData['certifiedMoney'] += $allSales[$salesId][$x]['certifiedMoney'];
        $allData['certifiedMoneyAvg'] += $allSales[$salesId][$x]['certifiedMoneyAvg'];
        $allData['feedbackMoney'] += $allSales[$salesId][$x]['feedbackMoney'];
        $allData['performanceMoney'] += $allSales[$salesId][$x]['performanceMoney'];
        $allData['contractCaseCountAvg'] += $allSales[$salesId][$x]['contractCaseCountAvg'];

        $ccc = ($allSales[$salesId][$x]['certifiedMoneyAvg'] > 0) ? round($allSales[$salesId][$x]['feedbackMoney']*100 / $allSales[$salesId][$x]['certifiedMoneyAvg'],2) : 0;
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $k, substr($x, 0, 4) . '/' . substr($x, 4, 2));
//        $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $k, ($allSales[$salesId][$x]['certifiedMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $k, ($allSales[$salesId][$x]['certifiedMoneyAvg']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $k, ($allSales[$salesId][$x]['feedbackMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $k, $ccc . '%');
        $objPHPExcel->getActiveSheet()->getStyle('N' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('M' . $k, ($allSales[$salesId][$x]['performanceMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('O' . $k, $allSales[$salesId][$x]['contractCaseCountAvg'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $k, $performanceIncrease . '%');
        $objPHPExcel->getActiveSheet()->setCellValue('Q' . $k, $countIncrease . '%');
        $objPHPExcel->getActiveSheet()->getStyle('P' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('Q' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        if (substr($x, -2, 2) == 12) {
            $x = (int)(substr($x, 0, 4) . '99') + 1;
        }
        $k++;
    }

    $aaa = ($per_performanceMoney > 0) ? round((($allData['performanceMoney'] - $per_performanceMoney)*100)/$per_performanceMoney) : '';
    $bbb = ($per_performanceMoney > 0) ? round((($allData['contractCaseCountAvg'] - $per_contractCaseCountAvg)*100)/$per_contractCaseCountAvg) : '';

    $ccc = ($allData['certifiedMoneyAvg'] > 0) ? round($allData['feedbackMoney']*100 / $allData['certifiedMoneyAvg'],2) : 0;
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $k, '總計');
//    $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $k, ($allData['certifiedMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $k, ($allData['certifiedMoneyAvg']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $k, ($allData['feedbackMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValue('N' . $k, $ccc .'%');
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('M' . $k, ($allData['performanceMoney']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('O' . $k, round($allData['contractCaseCountAvg'], 2), PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $objPHPExcel->getActiveSheet()->setCellValue('P' . $k, $aaa . '%');
    $objPHPExcel->getActiveSheet()->setCellValue('Q' . $k, $bbb . '%');
    $objPHPExcel->getActiveSheet()->getStyle('N' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('P' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('Q' . $k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('J' . $k . ':Q' . $k)->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F8ECE9')
            )
        )
    );

    $sheetIndex++;
}

##

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>