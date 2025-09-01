<?php
//設定顯示外框參數
$border_frame = [
    'borders' => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            // 'color' => ['rgb' => 'FF000000'],
            'color' => ['rgb' => PHPExcel_Style_Color::COLOR_BLACK],
        ],
    ],
];
##

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(24);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(18);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(33);
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(33);

//設定字體大小
$objPHPExcel->getActiveSheet()->getStyle('A:P')->getFont()->setSize(12);

$cell_no = 1;

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('A' . $cell_no . ':P' . $cell_no);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $cell_no, substr($account, 0, 5) . '-' . substr($account, 5) . '代墊利息暫放專戶');

//水平置中欄位
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//垂直置中欄位
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//設定字體大小
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no)->getFont()->setSize(16);

$cell_no++;

//清單標題列填色
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no)->getFill()->getStartColor()->setARGB('00F8CBAD');

$objPHPExcel->getActiveSheet()->getStyle('B' . $cell_no . ':F' . $cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('B' . $cell_no . ':F' . $cell_no)->getFill()->getStartColor()->setARGB('00E4BEB1');

$objPHPExcel->getActiveSheet()->getStyle('G' . $cell_no . ':P' . $cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('G' . $cell_no . ':P' . $cell_no)->getFill()->getStartColor()->setARGB('00FFE699');

//寫入清單標題列資料
$objPHPExcel->getActiveSheet()->setCellValue('A' . $cell_no, '序號');
$objPHPExcel->getActiveSheet()->setCellValue('B' . $cell_no, '日期');
$objPHPExcel->getActiveSheet()->setCellValue('C' . $cell_no, '收入');
$objPHPExcel->getActiveSheet()->setCellValue('D' . $cell_no, '支出');
$objPHPExcel->getActiveSheet()->setCellValue('E' . $cell_no, '餘額');
$objPHPExcel->getActiveSheet()->setCellValue('F' . $cell_no, '備註');
$objPHPExcel->getActiveSheet()->setCellValue('G' . $cell_no, '銀行別');
$objPHPExcel->getActiveSheet()->setCellValue('H' . $cell_no, '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('I' . $cell_no, '簽約日期');
$objPHPExcel->getActiveSheet()->setCellValue('J' . $cell_no, '地政士');
$objPHPExcel->getActiveSheet()->setCellValue('K' . $cell_no, '買方');
$objPHPExcel->getActiveSheet()->setCellValue('L' . $cell_no, '賣方');
$objPHPExcel->getActiveSheet()->setCellValue('M' . $cell_no, '承辦人');
$objPHPExcel->getActiveSheet()->setCellValue('N' . $cell_no, '案件狀態');
$objPHPExcel->getActiveSheet()->setCellValue('O' . $cell_no, '履保費出款日');
$objPHPExcel->getActiveSheet()->setCellValue('P' . $cell_no, '代墊利息欄位勾選' . "\r\n" . '(日期要壓點交日)');
$objPHPExcel->getActiveSheet()->getStyle('P' . $cell_no)->getAlignment()->setWrapText(true);

//水平垂直置中欄位
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//設定粗體字
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->getFont()->setBold(true);

//繪製外框
$objPHPExcel->getActiveSheet()->getStyle('A' . $cell_no . ':P' . $cell_no)->applyFromArray($border_frame);
