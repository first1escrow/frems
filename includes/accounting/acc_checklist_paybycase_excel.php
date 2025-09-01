<?php
$detail = $list = null;
unset($detail, $list);

$border_style = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('rgb' => '0080C0'),
        ),
    ),
);

//
$detail     = [];
$last_count = 0; //共 n 筆
$last_money = 0; //總共金額


foreach ($paybycase_data as $k => $v) {
    $data = [];
    $memo = '';

    //日期
    $tDate = explode('-', $v['tDate']);
    $tDate[0] -= 1911; //換算民國年

    //為了取得日期年度，所以乾脆每次回圈都設定一次
    $last = [
        'year' => $tDate[0],
        'date' => implode('', $tDate),
        'code' => '1110003',
        'loan' => 2,
        'memo' => $tDate[1] . '/' . $tDate[2] . '-隨案結-履保收入&回饋金',
    ];

    //一銀
    $last_first = [
        'year' => $tDate[0],
        'date' => implode('', $tDate),
        'code' => '1110017',
        'loan' => 2,
        'memo' => $tDate[1] . '/' . $tDate[2] . '-隨案結-履保收入&回饋金',
    ];

    //台新
    $last_taishin = [
        'year' => $tDate[0],
        'date' => implode('', $tDate),
        'code' => '1110010',
        'loan' => 2,
        'memo' => $tDate[1] . '/' . $tDate[2] . '-隨案結-履保收入&回饋金',
    ];

    //永豐
    $last_sinopac = [
        'year' => $tDate[0],
        'date' => implode('', $tDate),
        'code' => '1110008',
        'loan' => 2,
        'memo' => $tDate[1] . '/' . $tDate[2] . '-隨案結-履保收入&回饋金',
    ];

    $memo = $tDate[1] . '/' . $tDate[2] . '-隨案結-回饋金-' . $v['fCertifiedId'] . '-SC' . str_pad($v['detail']['cScrivener'], 4, '0', STR_PAD_LEFT) . '-' . $v['fBankAccountName'];

    $data[] = [
        'year'  => $tDate[0],
        'date'  => implode('', $tDate),
        'code'  => '5910',
        'loan'  => 1,
        'money' => (int) $v['detail']['total'],
        'memo'  => $memo,
    ];

    $last_count++;

    if (!empty($v['fTax'])) {
        $memo = $tDate[1] . '/' . $tDate[2] . '-隨案結-回饋金-代扣稅款10%-' . $v['fBankAccountName'] . ' ' . $v['fIdentityIdNumber'];

        $data[] = [
            'year'  => $tDate[0],
            'date'  => implode('', $tDate),
            'code'  => '2983',
            'loan'  => 2,
            'money' => (int) $v['fTax'],
            'memo'  => $memo,
        ];

    }

    if (!empty($v['fNHI'])) {
        $memo = $tDate[1] . '/' . $tDate[2] . '-隨案結-回饋金-二代健保2.11%-' . $v['fBankAccountName'] . ' ' . $v['fIdentityIdNumber'];

        $data[] = [
            'year'  => $tDate[0],
            'date'  => implode('', $tDate),
            'code'  => '2991',
            'loan'  => 2,
            'money' => (int) $v['fNHI'],
            'memo'  => $memo,
        ];

    }

    $detail = array_merge($detail, $data);

    $data = $tDate = $memo = $identify_type = null;
    unset($data, $tDate, $memo, $identify_type);
}

$paybycase_data = null;unset($paybycase_data);

if (!empty($detail)) {
    $last['money'] = $totalPrePayMoney;
    $detail[] = $last;

    $last_first['money'] = $last_first_money;
    $detail[] = $last_first;

    $last_sinopac['money'] = $last_sinopac_money;
    $detail[] = $last_sinopac;

    $last_taishin['money'] = $last_taishin_money;
    $detail[] = $last_taishin;
    
}

$last = $last_money = $last_count = null;
unset($last, $last_money, $last_count);

//新增工作頁
$objPHPExcel->createSheet(2);

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(2);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(4);

$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(4);

$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(4);

$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(54);

//總表標題列填色
$objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFill()->getStartColor()->setARGB('00A4C8DB');
$objPHPExcel->getActiveSheet()->getStyle('E1:G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('E1:G1')->getFill()->getStartColor()->setARGB('00D9D9D9');
$objPHPExcel->getActiveSheet()->getStyle('K1:L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('K1:L1')->getFill()->getStartColor()->setARGB('00D9D9D9');
$objPHPExcel->getActiveSheet()->getStyle('O1:V1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('O1:V1')->getFill()->getStartColor()->setARGB('00D9D9D9');

//設定總表文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//粗體字
$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);

//設定字體顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('M1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->getColor()->setARGB('00008000');
$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->getColor()->setARGB('00008000');
$objPHPExcel->getActiveSheet()->getStyle('N1')->getFont()->getColor()->setARGB('00008000');
$objPHPExcel->getActiveSheet()->getStyle('V1')->getFont()->getColor()->setARGB('00008000');

//設定字型大小
$objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A:AW')->getFont()->setSize(12);

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('A1', '年度');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '傳票號碼');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '傳票種類');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '日期');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '外幣代號');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '匯率');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '轉B帳註記');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '唯一流水號(主檔 )');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '序號');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '科目代號');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '客戶供應商代號');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '摘要');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '借/貸');
$objPHPExcel->getActiveSheet()->setCellValue('N1', '金額');
$objPHPExcel->getActiveSheet()->setCellValue('O1', '部門編號');
$objPHPExcel->getActiveSheet()->setCellValue('P1', '專案編號 ');
$objPHPExcel->getActiveSheet()->setCellValue('Q1', '工程項目序號');
$objPHPExcel->getActiveSheet()->setCellValue('R1', '收據');
$objPHPExcel->getActiveSheet()->setCellValue('S1', '分攤|調整註記');
$objPHPExcel->getActiveSheet()->setCellValue('T1', '列印註記');
$objPHPExcel->getActiveSheet()->setCellValue('U1', '原傳票號碼');
$objPHPExcel->getActiveSheet()->setCellValue('V1', '外幣金額');
$objPHPExcel->getActiveSheet()->setCellValue('W1', '長摘要');
$objPHPExcel->getActiveSheet()->setCellValue('X1', '主檔自定義欄位一');
$objPHPExcel->getActiveSheet()->setCellValue('Y1', '主檔自定義欄位二');
$objPHPExcel->getActiveSheet()->setCellValue('Z1', '主檔自定義欄位三');
$objPHPExcel->getActiveSheet()->setCellValue('AA1', '主檔自定義欄位四');
$objPHPExcel->getActiveSheet()->setCellValue('AB1', '主檔自定義欄位五');
$objPHPExcel->getActiveSheet()->setCellValue('AC1', '主檔自定義欄位六');
$objPHPExcel->getActiveSheet()->setCellValue('AD1', '主檔自定義欄位七');
$objPHPExcel->getActiveSheet()->setCellValue('AE1', '主檔自定義欄位八');
$objPHPExcel->getActiveSheet()->setCellValue('AF1', '主檔自定義欄位九');
$objPHPExcel->getActiveSheet()->setCellValue('AG1', '主檔自定義欄位十');
$objPHPExcel->getActiveSheet()->setCellValue('AH1', '主檔自定義欄位十一');
$objPHPExcel->getActiveSheet()->setCellValue('AI1', '主檔自定義欄位十二');
$objPHPExcel->getActiveSheet()->setCellValue('AJ1', '明細自定義欄位一');
$objPHPExcel->getActiveSheet()->setCellValue('AK1', '明細自定義欄位二');
$objPHPExcel->getActiveSheet()->setCellValue('AL1', '明細自定義欄位三');
$objPHPExcel->getActiveSheet()->setCellValue('AM1', '明細自定義欄位四');
$objPHPExcel->getActiveSheet()->setCellValue('AN1', '明細自定義欄位五');
$objPHPExcel->getActiveSheet()->setCellValue('AO1', '明細自定義欄位六');
$objPHPExcel->getActiveSheet()->setCellValue('AP1', '明細自定義欄位七');
$objPHPExcel->getActiveSheet()->setCellValue('AQ1', '明細自定義欄位八');
$objPHPExcel->getActiveSheet()->setCellValue('AR1', '明細自定義欄位九');
$objPHPExcel->getActiveSheet()->setCellValue('AS1', '明細自定義欄位十');
$objPHPExcel->getActiveSheet()->setCellValue('AT1', '明細自定義欄位十一');
$objPHPExcel->getActiveSheet()->setCellValue('AU1', '明細自定義欄位十二');
$objPHPExcel->getActiveSheet()->setCellValue('AV1', '來源別');
$objPHPExcel->getActiveSheet()->setCellValue('AW1', '來源單號');

//繪製框線
$objPHPExcel->getActiveSheet()->getStyle("A1:AW1")->applyFromArray($border_style);

//隨案支付紀錄
if (!empty($detail)) {
    foreach ($detail as $k => $v) {
        //內容
        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($k + 2), $v['year']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . ($k + 2), 'DEFAULT');
        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($k + 2), 3);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($k + 2), $v['date']);
        $objPHPExcel->getActiveSheet()->getCell('I' . ($k + 2))->setValueExplicit(str_pad(($k + 1), 4, '0', STR_PAD_LEFT), PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('J' . ($k + 2), $v['code']);
        $objPHPExcel->getActiveSheet()->setCellValue('M' . ($k + 2), $v['loan']);
        $objPHPExcel->getActiveSheet()->setCellValue('N' . ($k + 2), $v['money']);
        $objPHPExcel->getActiveSheet()->setCellValue('W' . ($k + 2), $v['memo']);

        //總表標題列填色
        $objPHPExcel->getActiveSheet()->getStyle('E' . ($k + 2) . ':G' . ($k + 2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('E' . ($k + 2) . ':G' . ($k + 2))->getFill()->getStartColor()->setARGB('00D9D9D9');
        $objPHPExcel->getActiveSheet()->getStyle('K' . ($k + 2) . ':L' . ($k + 2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('K' . ($k + 2) . ':L' . ($k + 2))->getFill()->getStartColor()->setARGB('00D9D9D9');
        $objPHPExcel->getActiveSheet()->getStyle('O' . ($k + 2) . ':V' . ($k + 2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle('O' . ($k + 2) . ':V' . ($k + 2))->getFill()->getStartColor()->setARGB('00D9D9D9');

        //繪製框線
        $objPHPExcel->getActiveSheet()->getStyle('A' . ($k + 2) . ':AW' . ($k + 2))->applyFromArray($border_style);
    }
}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('隨案結成本拋轉');