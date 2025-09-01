<?php
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/first1DB.php';

//取得政府/第一建經案件數量
function getCount($source, $city, $year, $from, $to)
{
    $table = ($source == 'first1') ? 'tSalesBuildingTransferFirst1' : 'tSalesBuildingTransfer';

    $land     = getBuildingTransferQuantity($table, $city, 'L', $year, $from, $to);
    $building = getBuildingTransferQuantity($table, $city, 'B', $year, $from, $to);

    //政府土地數量需減掉建物數量
    if ($table == 'tSalesBuildingTransfer') {
        $land -= $building;
    }
    ##

    return ['land' => $land, 'building' => $building];
}
##

//取得轉移棟數資料
function getBuildingTransferQuantity($table, $city, $type, $year, $from_month, $to_month)
{
    global $conn;

    $sql = 'SELECT SUM(`sQuantity`) as total FROM `' . $table . '` WHERE `sType` = :type AND `sYear` = :year AND `sMonth` >= :from_month AND `sMonth` <= :to_month AND `sCity` = :city;';
    $rs  = $conn->one($sql, [
        'type'       => $type,
        'year'       => $year,
        'from_month' => $from_month,
        'to_month'   => $to_month,
        'city'       => $city,
    ]);

    return empty($rs['total']) ? 0 : $rs['total'];
}
##

//設定儲存格背景色
function cellColor($objPHPExcel, $cells, $color)
{
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type'       => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => $color,
        ),
    ));
}
##

//取得責任區域業務
function getAreaSales($city)
{
    global $conn;

    $city = preg_replace("/^臺/u", '台', $city);

    $sql = 'SELECT zSales FROM tZipArea WHERE zCity = :city';
    $rs  = $conn->all($sql, ['city' => $city]);

    $list = [];
    foreach ($rs as $v) {
        $tmp = explode(',', $v['zSales']);
        foreach ($tmp as $va) {
            $list[] = $va;
        }
        $tmp = null;unset($tmp);
    }

    return getStaffName(array_unique($list));
}

function getStaffName($pId)
{
    global $conn;

    $list = [];
    foreach ($pId as $v) {
        $sql = 'SELECT pName FROM tPeopleInfo WHERE pId = :pId;';
        $rs  = $conn->one($sql, ['pId' => $v]);
        if (!empty($rs)) {
            $list[] = $rs['pName'];
        }

    }

    return implode(',', $list);
}
##

//
$from_month = str_pad($from_month, 2, '0', STR_PAD_LEFT);
$to_month   = str_pad($to_month, 2, '0', STR_PAD_LEFT);
$last_year  = date("Y", strtotime("-1 year", strtotime($year . '-1-1')));
##

$conn = new first1DB;

//取得業務姓名
$sales_name = getStaffName([$sales]);
##

//取得縣市資訊
$zipArea = [];
$sql     = 'SELECT zCity,zSales FROM tZipArea WHERE 1 GROUP BY zCity;';
$rs      = $conn->all($sql);
foreach ($rs as $v) {
    $city   = preg_replace('/^台/u', '臺', $v['zCity']);
    $_sales = getAreaSales($v['zCity']);

    if (empty($sales) || preg_match("/$sales_name/u", $_sales)) {
        $zipArea[$city] = [
            'this' => [
                'first1'     => getCount('first1', $city, $year, $from_month, $to_month),
                'government' => getCount('government', $city, $year, $from_month, $to_month),
            ],
            'last' => [
                'first1'     => getCount('first1', $city, $last_year, $from_month, $to_month),
                'government' => getCount('government', $city, $last_year, $from_month, $to_month),
            ],
        ];
    }

    $_sales = null;unset($_sales);
}
##

//格線樣式
$border_style = [
    'top'    => array(
        'style' => PHPExcel_Style_Border::BORDER_NONE,
    ),
    'bottom' => array(
        'style' => PHPExcel_Style_Border::BORDER_THIN,
    ),
    'left'   => array(
        'style' => PHPExcel_Style_Border::BORDER_NONE,
    ),
    'right'  => array(
        'style' => PHPExcel_Style_Border::BORDER_NONE,
    ),
];
##

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("年度業績分析");
$objPHPExcel->getProperties()->setDescription("第一建經年度市場趨勢分析表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

//畫分隔線
$objPHPExcel->getActiveSheet()->getStyle('A4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('B4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('C4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('D4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('E4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('F4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('G4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('H4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('I4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('J4')->getBorders()->applyFromArray($border_style);
$objPHPExcel->getActiveSheet()->getStyle('K4')->getBorders()->applyFromArray($border_style);
##

//寫入總表資料
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(24);

$objPHPExcel->getActiveSheet()->setCellValue('A1', '統計年度期間');
$objPHPExcel->getActiveSheet()->setCellValue('B1', ($year - 1911) . ' 年度 ' . $from_month . ' - ' . $to_month . ' 月份');

$objPHPExcel->getActiveSheet()->setCellValue('A3', '區域');
$objPHPExcel->getActiveSheet()->mergeCells('A3:A4');
$objPHPExcel->getActiveSheet()->setCellValue('B3', '種類');
$objPHPExcel->getActiveSheet()->mergeCells('B3:B4');
$objPHPExcel->getActiveSheet()->setCellValue('K3', '負責業務');
$objPHPExcel->getActiveSheet()->mergeCells('K3:K4');

$objPHPExcel->getActiveSheet()->setCellValue('C3', ($last_year - 1911) . ' 年度(A)');
$objPHPExcel->getActiveSheet()->mergeCells('C3:E3');
$objPHPExcel->getActiveSheet()->setCellValue('F3', ($year - 1911) . ' 年度(B)');
$objPHPExcel->getActiveSheet()->mergeCells('F3:H3');
$objPHPExcel->getActiveSheet()->setCellValue('I3', '趨勢((B ÷ A - 1) × 100)');
$objPHPExcel->getActiveSheet()->mergeCells('I3:J3');

$objPHPExcel->getActiveSheet()->getStyle('A3:K4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:K4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$objPHPExcel->getActiveSheet()->setCellValue('C4', '建經(件)');
$objPHPExcel->getActiveSheet()->setCellValue('D4', '市場(件)');
$objPHPExcel->getActiveSheet()->setCellValue('E4', '市佔率(%)');

$objPHPExcel->getActiveSheet()->setCellValue('F4', '建經(件)');
$objPHPExcel->getActiveSheet()->setCellValue('G4', '市場(件)');
$objPHPExcel->getActiveSheet()->setCellValue('H4', '市佔率(%)');

$objPHPExcel->getActiveSheet()->setCellValue('I4', '建經(%)');
$objPHPExcel->getActiveSheet()->setCellValue('J4', '市場(%)');

$objPHPExcel->getActiveSheet()->getStyle("A3:K4")->getFont()->setSize(12)->setBold(true);

$index = 5;
foreach ($zipArea as $k => $v) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $index, $k);

    //土地
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, '土地');
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $index, $v['last']['first1']['land']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $index, $v['last']['government']['land']);

    // $objPHPExcel->getActiveSheet()->getStyle('C' . $index . ':D' . $index)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    $avg = round($v['last']['first1']['land'] / $v['last']['government']['land'] * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $index, $avg);
    cellColor($objPHPExcel, 'E' . $index, 'DCE6F1');

    $objPHPExcel->getActiveSheet()->setCellValue('F' . $index, $v['this']['first1']['land']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $index, $v['this']['government']['land']);

    // $objPHPExcel->getActiveSheet()->getStyle('F' . $index . ':G' . $index)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    $avg = round($v['this']['first1']['land'] / $v['this']['government']['land'] * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $index, $avg);
    cellColor($objPHPExcel, 'E' . $index, 'DCE6F1');
    cellColor($objPHPExcel, 'H' . $index, 'DCE6F1');

    $objPHPExcel->getActiveSheet()->setCellValue('K' . $index, empty($sales) ? getAreaSales($k) : $sales_name);
    $objPHPExcel->getActiveSheet()->mergeCells('K' . $index . ':K' . ($index + 1));

    $objPHPExcel->getActiveSheet()->getStyle('A' . $index . ':K' . ($index + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $index . ':K' . ($index + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    ##

    //建經趨勢
    $avg = round(($v['this']['first1']['land'] / $v['last']['first1']['land'] - 1) * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $index, $avg);
    cellColor($objPHPExcel, 'I' . $index, 'F2DCDB');
    ##

    //市場趨勢
    $avg = round(($v['this']['government']['land'] / $v['last']['government']['land'] - 1) * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $index, $avg);
    cellColor($objPHPExcel, 'J' . $index, 'E6B8B7');
    ##

    $objPHPExcel->getActiveSheet()->mergeCells('A' . $index . ':A' . (++$index));

    //建物
    if ($index % 2 == 0) {
        cellColor($objPHPExcel, 'A' . $index . ':H' . $index, 'F2F2F2');
    }

    $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, '建物');
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $index, $v['last']['first1']['building']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $index, $v['last']['government']['building']);

    $avg = round($v['last']['first1']['building'] / $v['last']['government']['building'] * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $index, $avg);
    cellColor($objPHPExcel, 'E' . $index, 'DCE6F1');

    $objPHPExcel->getActiveSheet()->setCellValue('F' . $index, $v['this']['first1']['building']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $index, $v['this']['government']['building']);

    $avg = round($v['this']['first1']['building'] / $v['this']['government']['building'] * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $index, $avg);
    cellColor($objPHPExcel, 'E' . $index, 'DCE6F1');
    cellColor($objPHPExcel, 'H' . $index, 'DCE6F1');
    ##

    //建經趨勢
    $avg = round(($v['this']['first1']['building'] / $v['last']['first1']['building'] - 1) * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $index, $avg);
    cellColor($objPHPExcel, 'I' . $index, 'F2DCDB');
    ##

    //市場趨勢
    $avg = round(($v['this']['government']['building'] / $v['last']['government']['building'] - 1) * 100, 2);
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $index, $avg);
    cellColor($objPHPExcel, 'J' . $index, 'E6B8B7');
    ##

    //畫分隔線
    $objPHPExcel->getActiveSheet()->getStyle('A' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('C' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('D' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('E' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('F' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('G' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('H' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('I' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('J' . $index)->getBorders()->applyFromArray($border_style);
    $objPHPExcel->getActiveSheet()->getStyle('K' . $index)->getBorders()->applyFromArray($border_style);
    ##

    $index++;
}

$objPHPExcel->getActiveSheet()->getStyle('A3:B' . $index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:B' . $index)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//Rename sheet 重命名工作表標籤
$sheet_title = empty($sales) ? '全部' : $sales_name;
$objPHPExcel->getActiveSheet()->setTitle($sheet_title);

//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$_file = 'sheet_' . ($year - 1911) . '.xlsx';

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
