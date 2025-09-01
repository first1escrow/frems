<?php

//新增工作頁
$objPHPExcel->createSheet(1);
//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(1);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(16);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);

//繪製框線
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);

//總表標題列填色
$objPHPExcel->getActiveSheet()->getStyle('D2:G2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('D2:G2')->getFill()->getStartColor()->setARGB('00DBDCF2');

$objPHPExcel->getActiveSheet()->getStyle('U2:V2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('U2:V2')->getFill()->getStartColor()->setARGB('00DBDCF2');

//設定總表文字置中
$objPHPExcel->getActiveSheet()->getStyle('A:Y')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G1:AE1')->getAlignment()->setWrapText(true);

//設定總表所有案件金額千分位符號
//$objPHPExcel->getActiveSheet()->getStyle('D2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//設定字型大小
$objPHPExcel->getActiveSheet()->getStyle('A:AE')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1:AC1')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('AC1')->getFont()->setSize(9);

//設定字型顏色
$objPHPExcel->getActiveSheet()->getStyle('H1:I1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('R1:T1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('D1:G1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('J1:K1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('N1:P1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('AC1')->getFont()->getColor()->setARGB('00FF0000');

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('E1', '銀行入帳金額=A-B-代扣利息'); //應收金額
$objPHPExcel->getActiveSheet()->setCellValue('F1', '利息=B'); //利息出
$objPHPExcel->getActiveSheet()->setCellValue('G1', '應付履約保證費額=A'); //履保費收入總額
$objPHPExcel->getActiveSheet()->setCellValue('H1', '公式算出'); //收入未稅
$objPHPExcel->getActiveSheet()->setCellValue('I1', '公式算出'); //收入稅額
$objPHPExcel->getActiveSheet()->setCellValue('J1', '代扣利息所得稅'); //代扣10%稅款
$objPHPExcel->getActiveSheet()->setCellValue('K1', '代扣利息所得稅'); //代扣2%保費
$objPHPExcel->getActiveSheet()->setCellValue('L1', '公式算出'); //差異數
$objPHPExcel->getActiveSheet()->setCellValue('O1', '代扣佣金所得稅'); //代扣10%稅款
$objPHPExcel->getActiveSheet()->setCellValue('P1', '代扣佣金所得稅'); //代扣2%保費
$objPHPExcel->getActiveSheet()->setCellValue('R1', '公式算出'); //一銀/台新/永豐
$objPHPExcel->getActiveSheet()->setCellValue('S1', '公式算出'); //預付費用(回饋金)
$objPHPExcel->getActiveSheet()->setCellValue('T1', '公式算出'); //差異數

$objPHPExcel->getActiveSheet()->setCellValue('AC1', '金額 = G 欄(履保費收入總額) - AB 欄(回饋成本)'); //差異數

$objPHPExcel->getActiveSheet()->setCellValue('A2', '回饋日期');
$objPHPExcel->getActiveSheet()->setCellValue('B2', '序號');
$objPHPExcel->getActiveSheet()->setCellValue('C2', '存入金額');
$objPHPExcel->getActiveSheet()->setCellValue('D2', '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('E2', '應收金額');
$objPHPExcel->getActiveSheet()->setCellValue('F2', '利息支出');
$objPHPExcel->getActiveSheet()->setCellValue('G2', '履保費收入總額');
$objPHPExcel->getActiveSheet()->setCellValue('H2', '收入未稅');
$objPHPExcel->getActiveSheet()->setCellValue('I2', '收入稅額');
$objPHPExcel->getActiveSheet()->setCellValue('J2', '代扣10%稅款');
$objPHPExcel->getActiveSheet()->setCellValue('K2', '代扣2%保費');
$objPHPExcel->getActiveSheet()->setCellValue('L2', '差異數');
$objPHPExcel->getActiveSheet()->setCellValue('M2', '備註');
$objPHPExcel->getActiveSheet()->setCellValue('N2', '代書回饋');
$objPHPExcel->getActiveSheet()->setCellValue('O2', '代扣10%稅款');
$objPHPExcel->getActiveSheet()->setCellValue('P2', '代扣2%保費');
$objPHPExcel->getActiveSheet()->setCellValue('Q2', '實付回饋金');
$objPHPExcel->getActiveSheet()->setCellValue('R2', '一銀/台新/永豐');
$objPHPExcel->getActiveSheet()->setCellValue('S2', '預付費用(回饋金)');
$objPHPExcel->getActiveSheet()->setCellValue('T2', '差異數');
$objPHPExcel->getActiveSheet()->setCellValue('U2', '買方身份');
$objPHPExcel->getActiveSheet()->setCellValue('V2', '賣方身份');
$objPHPExcel->getActiveSheet()->setCellValue('W2', '應開發票數');
$objPHPExcel->getActiveSheet()->setCellValue('X2', '仲介類型');
$objPHPExcel->getActiveSheet()->setCellValue('Y2', '最後修改者');
$objPHPExcel->getActiveSheet()->setCellValue('Z2', '案件狀態');
$objPHPExcel->getActiveSheet()->setCellValue('AA2', '尚未匯出回饋金');
$objPHPExcel->getActiveSheet()->setCellValue('AB2', '總回饋成本');
$objPHPExcel->getActiveSheet()->setCellValue('AC2', '淨收入');
$objPHPExcel->getActiveSheet()->setCellValue('AD2', '銀行別');
$objPHPExcel->getActiveSheet()->setCellValue('AE2', '隨案結');

$last_first_count   = 0;
$last_first_money   = 0;
$last_taishin_count = 0;
$last_taishin_money = 0;
$last_sinopac_count = 0;
$last_sinopac_money = 0;

//寫入查詢資料
$max              = count($detail);
$totalPrePayMoney = 0;
$k                = 3; // 起始位置
$no               = 1; //序號
$j                = $k;
for ($i = 0; $i < $max; $i++) {
    if($detail[$i]['cFeedbackDate'] < $fds or $detail[$i]['cFeedbackDate'] > $fde) continue; //不是在撈取範圍內的回饋日 不要顯示

    //計算10%稅額
    $detail[$i]['paytax'] = $paytax = 0;
    $detail[$i]['paytax'] = payTax($detail[$i]['ownerId'], $detail[$i]['tInterest']);
    $detail[$i]['paytax'] += $paytax;

    //計算2%補充保費
    $detail[$i]['NHITax'] = $NHITax = 0;
    $detail[$i]['NHITax'] = payNHITax($detail[$i]['ownerId'], $detail[$i]['ownerNHI'], $detail[$i]['tInterest']);
    $detail[$i]['NHITax'] += $NHITax;

    $feedBackDate = date_create($detail[$i]['cFeedbackDate']);
    $feedBackDate = date_format($feedBackDate, 'm/d');
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $feedBackDate); //交易日期
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $no); //序號
    $no++;
    $amountReceivable = $detail[$i]['tMoney'];
    //正常案件
    if(is_null($detail[$i]['relayFeedBackMoney'])) {
        $deposits = $amountReceivable;
    } else {
        if($detail[$i]['cBankRelay'] == 'C') { //履保費先收過錢 回饋日表單 履保費要用0去計算
            $detail[$i]['relayMoney'] = 0;
        }
        //隨案案件
        $deposits = ($detail[$i]['relayMoney']) ? $detail[$i]['relayMoney'] : 0 ;
    }

    $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $deposits); //存入金額
    $objPHPExcel->getActiveSheet()->getCell('D' . $j)->setValueExplicit($detail[$i]['tCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING); //保證號碼

    if($detail[$i]['cBankRelay'] == 'C') {

        $sql = 'SELECT SUM(`tMoney`) AS tMoney FROM tBankTrans AS b WHERE b.tAccount = ' . $detail[$i]['VR_Code'] . ' AND b.tObjKind = "代墊利息" AND b.tPayOk = 1';
        $rs  = $conn->Execute($sql);

        $amountReceivable = 0;
        while (!$rs->EOF) {
            $amountReceivable = (int) $rs->fields['tMoney'];
            $rs->MoveNext();
        }
        $amountReceivable = ($amountReceivable) *(-1);
    }
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, $amountReceivable); //應收金額
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $j, $detail[$i]['tInterest']); //利息支出
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $j, $detail[$i]['cCertifiedMoney']); //履保費收入總額

    $money1 = round(($detail[$i]['cCertifiedMoney'] / 1.05), 0);
    $money2 = ($detail[$i]['cCertifiedMoney'] - $money1);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $j, $money1); //公式算出1 = 履保費/1.05
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $j, $money2); //公式算出2 = 履保費-公式算出1
    $objPHPExcel->getActiveSheet()->setCellValue('J' . $j, $detail[$i]['paytax']); //代扣10%稅款
    $objPHPExcel->getActiveSheet()->setCellValue('K' . $j, $detail[$i]['NHITax']); //代扣2%保費

    //差異數(L) = 應收金額(C) + 利息支出(F) - 收入未稅(H) - 收入稅額(I) - 代扣10%稅款(J) - 代扣2%保費(K)
    $money3 = $amountReceivable + $detail[$i]['tInterest'] - $money1 - $money2 - $detail[$i]['paytax'] - $detail[$i]['NHITax'];
    $objPHPExcel->getActiveSheet()->setCellValue('L' . $j, $money3); //差異數

    if($detail[$i]['cBankRelay'] == 'C') {
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $j, '履保費先收未結案'); //備註

        $pay_by_case = $paybycase->getPayByCase($detail[$i]['tCertifiedId']);
        $detail[$i]['scrivenerFeedBackMoney'] = empty($pay_by_case['detail']['total']) ? 0 : $pay_by_case['detail']['total']; //金額
        $detail[$i]['feedbackIncomeTax']      = empty($pay_by_case['fTax']) ? 0 : $pay_by_case['fTax']; //代扣10%稅款
        $detail[$i]['feedbackNHITax']         = empty($pay_by_case['fNHI']) ? 0 : $pay_by_case['fNHI']; //代扣2%保費
        $detail[$i]['branchFeedBackMoney']    = $detail[$i]['Feed'] - $detail[$i]['scrivenerFeedBackMoney'];
    }

    $objPHPExcel->getActiveSheet()->setCellValue('N' . $j, $detail[$i]['scrivenerFeedBackMoney']); //代書回饋
    $objPHPExcel->getActiveSheet()->setCellValue('O' . $j, $detail[$i]['feedbackIncomeTax']); //代扣10%稅款
    $objPHPExcel->getActiveSheet()->setCellValue('P' . $j, $detail[$i]['feedbackNHITax']); //代扣2%保費

    //應收金額 - 存入金額
    $bank = $amountReceivable - $detail[$i]['relayMoney'];
    if ($detail[$i]['scrivenerFeedBackMoney'] == 0) {
        $detail[$i]['relayFeedBackMoney'] = 0;
        $bank                             = 0;
    }
    //履保費已先出過款
    if($detail[$i]['cBankRelay'] == 'C') {
        $bank = 0;
    }
    $objPHPExcel->getActiveSheet()->setCellValue('R' . $j, $bank); //一銀/台新/永豐
    $objPHPExcel->getActiveSheet()->setCellValue('Q' . $j, $detail[$i]['relayFeedBackMoney']); //實付回饋金

    if ($detail[$i]['bankName'] == '第一銀行' and 0 != $detail[$i]['relayFeedBackMoney']) {
        $last_first_count++;
        $last_first_money += (int) $bank;
    }
    if ($detail[$i]['bankName'] == '台新銀行' and 0 != $detail[$i]['relayFeedBackMoney']) {
        $last_taishin_count++;
        $last_taishin_money += (int) $bank;
    }
    if ($detail[$i]['bankName'] == '永豐銀行' and 0 != $detail[$i]['relayFeedBackMoney']) {
        $last_sinopac_count++;
        $last_sinopac_money += (int) $bank;
    }

    //預付費用(回饋金) = 實付回饋金 + 存入金額 - 應收金額
    $prePayMoney = $detail[$i]['relayFeedBackMoney'] + $detail[$i]['relayMoney'] - $amountReceivable;
    if ($detail[$i]['scrivenerFeedBackMoney'] == 0) {
        $prePayMoney = 0;
    }
    //履保費先收
    if($detail[$i]['cBankRelay'] == 'C') {
        // 預付費用(回饋金) = 實付回饋金
        $prePayMoney = $detail[$i]['relayFeedBackMoney'];
    }
    $objPHPExcel->getActiveSheet()->setCellValue('S' . $j, $prePayMoney); //預付費用(回饋金)
    $totalPrePayMoney = $totalPrePayMoney + $prePayMoney;

    //差異數 = 實付回饋金 - 一銀/台新/永豐 - 預付費用(回饋金)
    $diffMoney = $detail[$i]['relayFeedBackMoney'] - $bank - $prePayMoney;
    //履保費先收
    if($detail[$i]['cBankRelay'] == 'C') {
        // 差異數= 預付費用(回饋金) + (一銀/台新/永豐) + 代扣2%保費 + 代扣10%稅款 - 代書回饋
        $diffMoney = $prePayMoney + $bank + $detail[$i]['feedbackNHITax'] + $detail[$i]['feedbackIncomeTax'] - $detail[$i]['scrivenerFeedBackMoney'];
    }
    $objPHPExcel->getActiveSheet()->setCellValue('T' . $j, $diffMoney); //差異數

    $objPHPExcel->getActiveSheet()->getCell('U' . $j)->setValueExplicit(obj_id($detail[$i]['buyerId']) . $detail[$i]['buyerNo']); //買方
    $objPHPExcel->getActiveSheet()->getCell('V' . $j)->setValueExplicit(obj_id($detail[$i]['ownerId']) . $detail[$i]['ownerNo']); //賣方
    $objPHPExcel->getActiveSheet()->setCellValue('W' . $j, $detail[$i]['invoiceNo']); //應開發票數

    //配件依據 "1.加盟(其他品牌)、2.加盟(台灣房屋)、3.優美、4.直營、5.非仲介成交" 順序掛帳
    $cBrand = '';
    $o      = 0; //加盟--其他品牌
    $t      = 0; //加盟--台灣房屋
    $u      = 0; //優美
    $s      = 0; //直營
    $n      = 0; //非仲介成交

    $bId = $detail[$i]['cBranchNum'];
    if ($bId > 0) { //第一組仲介品牌代號
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    $bId = $detail[$i]['cBranchNum1'];
    if ($bId > 0) { //第二組仲介是否存在
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    $bId = $detail[$i]['cBranchNum2'];
    if ($bId > 0) { //第三組仲介是否存在
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    $bId = $detail[$i]['cBranchNum3'];
    if ($bId > 0) { //第四組仲介是否存在
        countBrand($bId, $o, $t, $u, $s, $n);
    }

    if ($o > 0) {
        $cBrand = '加盟(其他品牌)';
    } else if ($t > 0) {
        $cBrand = '加盟(台灣房屋)';
    } else if ($u > 0) {
        $cBrand = '加盟(優美地產)';
    } else if ($s > 0) {
        $cBrand = '直營';
    } else {
        $cBrand = '非仲介成交';
    }
    ##

    //仲介類型
    $objPHPExcel->getActiveSheet()->setCellValue('X' . $j, $cBrand);

    //最後修改人
    $objPHPExcel->getActiveSheet()->setCellValue('Y' . $j, $detail[$i]['lastmodify']);
    $objPHPExcel->getActiveSheet()->setCellValue('Z' . $j, $detail[$i]['status']);

    //仲介回饋
    $objPHPExcel->getActiveSheet()->setCellValue('AA' . $j, $detail[$i]['branchFeedBackMoney']);

    //總回饋成本
    $objPHPExcel->getActiveSheet()->setCellValue('AB' . $j, $detail[$i]['Feed']);

    //淨收入
    $certifiedMoney = $detail[$i]['cBankRelay'] == 'C' ? $detail[$i]['paidMoney'] : $detail[$i]['cCertifiedMoney'];
    $objPHPExcel->getActiveSheet()->setCellValue('AC' . $j, ($certifiedMoney - $detail[$i]['Feed']));

    //銀行別
    $objPHPExcel->getActiveSheet()->setCellValue('AD' . $j, $detail[$i]['bankName']);

    //隨案結
    $isPayBycase = '';
    if($detail[$i]['feedDateCat'] == 2) { $isPayBycase = 'V'; }
    $objPHPExcel->getActiveSheet()->setCellValue('AE' . $j, $isPayBycase);

    if($detail[$i]['cBankRelay'] == 'C') {
        $objPHPExcel->getActiveSheet()->getStyle("A".$j.":AE".$j."")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle("A".$j.":AE".$j."")->getFill()->getStartColor()->setARGB('FFFF00');
    }
    $j++;
}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('結案回饋日');
