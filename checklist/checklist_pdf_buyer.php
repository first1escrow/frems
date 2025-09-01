<?php
$pdf->SetFontSize(6);
$pdf->Text(185, 5, $detail['last_modify']);

$pdf->SetFontSize(14);
$pdf->Cell(190, $cell_y1, '第一建築經理(股)公司', 0, 1, 'C'); // 寫入文字

$pdf->SetFontSize(12); // 設定字體大小
$title_txt = ($detail['bNote'] != 1) ? '專戶收支明細表暨點交確認單(買方)' : '履約專戶收支明細表暨換約確認單(買方)';
$pdf->Cell(190, $cell_y1, $title_txt, 0, 1, 'C');

$pdf->Cell(190, $cell_y3, '', 0, 1); // 手動換行

$pdf->SetFontSize(12);
$pdf->Cell(190, $cell_y2, '案件基本資料', 0, 1);
$pdf->SetFontSize(12);

//畫線(雙線條)
$pdf->SetFontSize(12);
$xy = $pdf->GetY();
$xy += ($line_gap / 2);
$pdf->Line(10, $xy, 200, $xy);

$pdf->SetFontSize(12);
$xy = $pdf->GetY();
$xy -= $line_gap;
$pdf->Line(10, $xy, 200, $xy);
##

//基本資料明細
$pdf->Cell(20, $cell_y1, '保證號碼：');

if ($cCertifiedId == '090020924') {
    $pdf->Cell(82, $cell_y1, $cCertifiedId . '(080146177)');
} else {
    $pdf->Cell(82, $cell_y1, $cCertifiedId);
}

if ($cCertifiedId == '030119750') {
    $pdf->SetX(130);
} else {
    $pdf->SetX(120);
}
$pdf->Cell(25, $cell_y1, '特約地政士：');
$pdf->Cell(82, $cell_y1, $detail['bScrivener'], 0, 1);

if (strlen($detail['bBuyerId']) == 10) {
    $idNew = substr($detail['bBuyerId'], 1, 4) . '****' . substr($detail['bBuyerId'], -1);
} else {
    $idNew = substr($detail['bBuyerId'], 1);
}

$pdf->Cell(20, $cell_y1, '買方姓名：');
$xx = ceil($pdf->GetStringWidth($detail['bBuyer']));

$pdf->Cell($xx, $cell_y1, $detail['bBuyer']);

$pdf->Cell(5, $cell_y1, substr($detail['bBuyerId'], 0, 1), 0, 0, 'R');
$addX = 1;
if (preg_match("/[0-9]/", substr($detail['bBuyerId'], 0, 1))) {
    $addX = 2;
}
$pdf->SetX($pdf->GetX() - $addX);
$xx = 82 - $xx - 7;
$pdf->Cell($xx, $cell_y1, $idNew, 0, 0, 'L');

$pdf->SetFontSize(12);

$yy = $pdf->getY();

if ($cCertifiedId == '030119750') {
    $pdf->SetX(130);
} else {
    $pdf->SetX(120);
}

$pdf->Cell(25, $cell_y1, '仲介店名：');

if (!$detail['bMoreStore']) {
    $pdf->Cell(52, $cell_y1, $detail['bBrand'], 0, 1);
} else {
    $detail['bMoreStore'] = str_replace('(待停用)', '', $detail['bMoreStore']);
    $bMoreStore           = explode(',', $detail['bMoreStore']);
    $pdf->MultiCell(52, $cell_y1, $bMoreStore[0], 0, 1);
    $yy2 = $pdf->getY();
}

if (strlen($detail['bOwnerId']) == 10) {
    $idNew = substr($detail['bOwnerId'], 1, 4) . '****' . substr($detail['bOwnerId'], -1);
} else {
    $idNew = substr($detail['bOwnerId'], 1);
}

if ($detail['bMoreStore']) {
    $yy = $pdf->setY($yy + $cell_y1);
}

$pdf->Cell(20, $cell_y1, '賣方姓名：');
$xx = ceil($pdf->GetStringWidth($detail['bOwner']));
$pdf->Cell($xx, $cell_y1, $detail['bOwner']);
$pdf->Cell(5, $cell_y1, substr($detail['bOwnerId'], 0, 1), 0, 0, 'R');
$addX = 1;
if (preg_match("/[0-9]/", substr($detail['bOwnerId'], 0, 1))) {
    $addX = 2;
}
$pdf->SetX($pdf->GetX() - $addX);
$xx = 82 - $xx - 5;
$pdf->Cell($xx, $cell_y1, $idNew, 0, 0, 'L');

if ($detail['bMoreStore']) {
    $yy = $pdf->getY();
    $pdf->setY($yy2);
}

if ($cCertifiedId == '030119750') {
    $pdf->SetX(130);
} else {
    $pdf->SetX(120);
}
$pdf->Cell(25, $cell_y1, '');

if (!$detail['bMoreStore']) {
    $detail['bStore'] = str_replace('(待停用)', '', $detail['bStore']);
    $pdf->Cell(52, $cell_y1, $detail['bStore'], 0, 1);
} else {
    $pdf->MultiCell(52, $cell_y1, $bMoreStore[1], 0, 1);
    $yy2 = $pdf->getY();

}

if ($detail['bMoreStore']) {

    $pdf->setY($yy2 + $cell_y1);
}

$pdf->Cell(25, $cell_y1, '買賣總金額：');

$tt = "$" . @number_format($detail['bTotalMoney']) . "元";
if ($detail['bTotalMoneyNote']) {
    $tt .= "(" . $detail['bTotalMoneyNote'] . ")";
}

$pdf->Cell(82, $cell_y1, $tt);
unset($tt);
if ($detail['bMoreStore'] && $bMoreStore[2]) {
    // if ($detail['bMoreStore']) {
    //     $yy = $pdf->getY();

    // }

    $pdf->setY($yy2);

    $pdf->SetX(120);
    $pdf->Cell(25, $cell_y1, '');
    $pdf->MultiCell(52, $cell_y1, $bMoreStore[2], 0, 1);

}
// $pdf->SetX(120) ;
// $pdf->Cell(25,$cell_y1,'代償金額：') ;
// $pdf->Cell(52,$cell_y1,"$".@number_format($detail['bCompensation'])."元",0,1) ;

if ($detail['bCompensation2'] > 0) {
    if ($cCertifiedId == '030119750') {
        $pdf->SetX(130);
    } else {
        $pdf->SetX(120);
    }
    $pdf->Cell(30, $cell_y1, '專戶代償金額：');
    $pdf->Cell(52, $cell_y1, "$" . @number_format($detail['bCompensation2']) . "元", 0, 1);
} elseif ($detail['bCompensation3'] > 0 && $detail['bCompensation2'] <= 0) {
    $pdf->SetX(120);
    $pdf->Cell(25, $cell_y1, '買方銀行代償：' . "$" . @number_format($detail['bCompensation3']) . "元", 0, 1);

    // $pdf->Cell(55,$cell_y1,,0,1) ;

}

if ($detail['bNotIntoMoney'] > 0) {

    $pdf->Cell(25, $cell_y1, '未入專戶：');
    $pdf->Cell(82, $cell_y1, "$" . @number_format($detail['bNotIntoMoney']) . "元");
} else {
    $pdf->Cell(25, $cell_y1, '');
    $pdf->Cell(82, $cell_y1, "");
}

if ($detail['cCompensation2'] > 0 && $detail['cCompensation3'] > 0) {
    $pdf->setY($pdf->getY());
    $pdf->SetX(120);
    $pdf->Cell(28, $cell_y1, '買方銀行代償：');

    $pdf->Cell(54, $cell_y1, "$" . @number_format($detail['cCompensation3']) . "元", 0, 1);

    if ($detail['cCompensation4'] == 0) {
        $detail['cCompensation4'] = $detail['cCompensation2'] + $detail['cCompensation3'];
    }
    $pdf->SetX(120);
    $pdf->Cell(28, $cell_y1, '代償總金額：');
    $pdf->Cell(52, $cell_y1, "$" . @number_format($detail['cCompensation4']) . "元", 0, 1);
} else {
    $pdf->SetX(120);
    $pdf->Cell(40, $cell_y1, '');

    $pdf->Cell(82, $cell_y1, "", 0, 1);
}

//建物
for ($i = 0; $i < $property_max; $i++) {
    $addr = n_to_w($property[$i]['city'] . $property[$i]['area'] . $property[$i]['cAddr']);
    // $property[$i]['cAddr'] = $property[$i]['city'].$property[$i]['area'].$property[$i]['cAddr'];
    // $property[$i]['cAddr'] = n_to_w($property[$i]['cAddr']) ;
    $pdf->Cell(25, $cell_y1, '買賣標的物：');
    $pdf->MultiCell(162, $cell_y1, $addr, 0, 1);
}
##

$pdf->Cell(190, $cell_gap, '', 0, 1); // 手動換行

$pdf->SetFontSize(12);
$pdf->Cell(190, $cell_y4, '買賣價金收支明細', 0, 1);
##

//畫線(雙線條)
$pdf->SetFontSize(12);
$xy = $pdf->GetY();
$xy += ($line_gap / 2);
$pdf->Line(10, $xy, 200, $xy);

$pdf->SetFontSize(12);
$xy = $pdf->GetY();
$xy -= $line_gap;
$pdf->Line(10, $xy, 200, $xy);
##

//收入明細 title
$pdf->SetFontSize(12);
$pdf->Cell(23.75, $cell_y4, '日期', 0, 0, 'C');
$pdf->Cell(35, $cell_y4, '摘要', 0, 0, 'C');
$pdf->Cell(23.75, $cell_y4, '收入金額', 0, 0, 'R');
$pdf->Cell(23.75, $cell_y4, '支出金額', 0, 0, 'R');
$pdf->Cell(33.75, $cell_y4, '小計', 0, 0, 'R');
$pdf->Cell(50, $cell_y4, '備註', 0, 1, 'C');
##

//畫線(雙線條)
$pdf->SetFontSize(12);
$xy = $pdf->GetY();
$xy += ($line_gap / 2);
$pdf->Line(10, $xy, 200, $xy);

$pdf->SetFontSize(12);
$xy = $pdf->GetY();
$xy -= $line_gap;
$pdf->Line(10, $xy, 200, $xy);
##

$pdf->SetFontSize(12);
$pdf->Cell(190, $cell_y4, '【專戶收款】', 0, 1);

// 買方收入明細
$total = 0;
for ($i = 0; $i < $buyer_max; $i++) {
    $total += $buyer_income[$i]['bIncome'];
    $showIncome = '';

    if ($i == ($buyer_max - 1)) {
        $showIncome = @number_format($total);
    }
    $buyer_income[$i]['bRemark'] = n_to_w($buyer_income[$i]['bRemark']);
    $buyer_income[$i]['bRemark'] = preg_replace("/^＋/", "含", $buyer_income[$i]['bRemark']);

    $pdf->Cell(23.75, $cell_y1, $buyer_income[$i]['bDate']); //,'RB'
    $pdf->Cell(35, $cell_y1, $buyer_income[$i]['bKind']); //,'RB'
    $pdf->Cell(23.75, $cell_y1, @number_format($buyer_income[$i]['bIncome']), 0, 0, 'R'); //RB
    $pdf->Cell(23.75, $cell_y1, @number_format($buyer_income[$i]['bExpense']), 0, 0, 'R'); //RB
    $pdf->Cell(33.75, $cell_y1, $showIncome, 0, 0, 'R'); //RB
    $pdf->SetX(160);
    $pdf->SetFontSize(9);
    $pdf->MultiCell(50, $cell_y1, $buyer_income[$i]['bRemark'], 0, 1); //B
    $pdf->SetFontSize(12);
}
##

$pdf->Cell(190, $cell_gap, '', 0, 1); // 手動換行

if ($buyer_max_e > 0) {
    $pdf->SetFontSize(12);
    $pdf->Cell(190, $cell_y4, '【專戶出款】', 0, 1);
}

// 買方支出明細
for ($i = 0; $i < $buyer_max_e; $i++) {
    $total -= $buyer_expense[$i]['bExpense'];
    $expense += $buyer_expense[$i]['bExpense'];

    $showExpense = '';
    if ($i == ($buyer_max_e - 1)) {
        $showExpense = @number_format($expense);
    }

    $pdf->Cell(23.75, $cell_y1, $buyer_expense[$i]['bDate']); //,'RB'
    $pdf->Cell(35, $cell_y1, $buyer_expense[$i]['bKind']); //,'RB'
    $pdf->Cell(23.75, $cell_y1, @number_format($buyer_expense[$i]['bIncome']), 0, 0, 'R'); //RB
    $pdf->Cell(23.75, $cell_y1, @number_format($buyer_expense[$i]['bExpense']), 0, 0, 'R'); //RB
    $pdf->Cell(33.75, $cell_y1, $showExpense, 0, 0, 'R'); //RB
    $pdf->SetX(160);
    $pdf->SetFontSize(9);
    $pdf->MultiCell(50, $cell_y1, $buyer_expense[$i]['bRemark'], 0, 1); //B
    $pdf->SetFontSize(12);
}
##

$pdf->SetFontSize(12);

$count = 0;
$check = 0;
if ($detail['bRealestateBalance'] > 0) { //買方應付仲介費餘額
    $count++;
}
if ($detail['bCertifiedMoney'] > 0) { //買方履保費
    $count++;
    $check = 1;
}
if ($detail['bScrivenerMoney'] > 0) { //買方代書費
    $count++;
}
if ($detail['bNHITax'] > 0) { //代扣補充保費
    $count++;
}
if ($detail['bTax'] > 0) { //代扣所得稅
    $count++;
}

if (count($tax_buyer) > 0) { //其它代扣
    $count++;
}

//若代扣款明細有值則顯示下列帳戶資料
if ($count > 0) {
    $pdf->Cell(190, $cell_gap, '', 0, 1); // 手動換行

    //畫線(雙線條)
    $pdf->SetFontSize(12);
    $xy = $pdf->GetY();
    $xy += ($line_gap / 2);
    $pdf->Line(10, $xy, 200, $xy);

    $pdf->SetFontSize(12);
    $xy = $pdf->GetY();
    $xy -= $line_gap;
    $pdf->Line(10, $xy, 200, $xy);
    ##

    //結清付款項 Title
    $pdf->SetFontSize(12);
    $pdf->Cell(47.5, $cell_y4, '待扣款項明細');
    $pdf->Cell(30, $cell_y4, '金額', 0, 0, 'R');
    $pdf->Cell(17.5, $cell_y4, '');

    $pdf->SetX(105);
    $pdf->Cell(95, $cell_y4, '備註', 0, 1);
    ##

    //畫線(雙線條)
    $pdf->SetFontSize(12);
    $xy = $pdf->GetY();
    $xy += ($line_gap / 2);
    $pdf->Line(10, $xy, 200, $xy);

    $pdf->SetFontSize(12);
    $xy = $pdf->GetY();
    $xy -= $line_gap;
    $pdf->Line(10, $xy, 200, $xy);
    ##

    //買方應付仲介費餘額
    if ($detail['bRealestateBalanceHide'] == 0) {
        $pdf->Cell(47.5, $cell_y2, '*應付仲介服務費餘額');
        $pdf->Cell(30, $cell_y2, @number_format(round($detail['bRealestateBalance'])), 0, 0, 'R');

        $pdf->SetX(105);
        $pdf->Cell(95, $cell_y2, '買方應付仲介服務費', 0, 1);
        $total -= (int) $detail['bRealestateBalance'];
    }
    ##

    //買方履保費
    if ($detail['bCertifiedMoney'] > 0) {
        $pdf->Cell(47.5, $cell_y2, '*買方應付履約保證費');
        $pdf->Cell(30, $cell_y2, @number_format(round($detail['bCertifiedMoney'])), 0, 0, 'R');

        $pdf->SetX(105);
        $pdf->Cell(95, $cell_y2, $detail['bcertify_remark'], 0, 1);
        $total -= (int) $detail['bCertifiedMoney'];
        if($buyer > 0 and $detail['bCertifyQue'] != '') {
            $ans1 = '□';
            $ans2 = '□';
            if($detail['bCertifyAns'] == '1') $ans1 = '■';
            if($detail['bCertifyAns'] == '0') $ans2 = '■';
            $pdf->Cell(68.75, $cell_y1, '', 0, 0);
            $pdf->SetFont('','B',12) ;
            $pdf->Cell(121.25, $cell_y1, $detail['bCertifyQue'].':'.$ans1 . $detail['bCertifyOption1'] . ' '.$ans2. $detail['bCertifyOption2'] .' 說明:' . $detail['bCertifyDesc'] , 0, 1);
            $pdf->SetFont('','',12) ;
        }
    }
    ##

    //買方代書費
    if ($detail['bScrivenerMoney'] > 0) {
        $pdf->Cell(47.5, $cell_y2, '*應付代書費用及代支費');
        $pdf->Cell(30, $cell_y2, @number_format(round($detail['bScrivenerMoney'])), 0, 0, 'R');

        $pdf->SetX(105);
        $pdf->Cell(95, $cell_y2, $detail['scrivener_remark2'], 0, 1);
        $total -= (int) $detail['bScrivenerMoney'];
    }
    ##

    //代扣補充保費
    if ($detail['bNHITax'] > 0) {
        $pdf->Cell(47.5, $cell_y2, '*代扣健保補充保費');
        $pdf->Cell(30, $cell_y2, @number_format(round($detail['bNHITax'])), 0, 0, 'R');

        $pdf->SetX(105);
        $pdf->Cell(95, $cell_y2, '代買方扣繳 2.11% 補充保費', 0, 1);
        $total -= (int) $detail['bNHITax'];
    }
    ##

    //代扣所得稅
    if ($detail['bTax'] > 0) {
        // $pdf->Cell(47.5,$cell_y2,'*'.$detail['bTaxTitle']) ;
        $pdf->Cell(47.5, $cell_y2, '*代扣利息所得稅');
        $pdf->Cell(30, $cell_y2, @number_format(round($detail['bTax'])), 0, 0, 'R');

        $pdf->SetX(105);
        // $pdf->Cell(95,$cell_y2,$detail['bTaxRemark'],0,1) ;

        // $pdf->Cell(95,$cell_y2,'代買方扣繳10%利息所得稅',0,1) ;
        if (preg_match("/[A-Za-z]{2}/", $detail['bBuyerId'])) { // 判別是否為外國人(兩碼英文字母者) 外國人20%
            $pdf->Cell(95, $cell_y2, '代買方扣繳20% 利息所得稅', 0, 1);
        } else {
            $pdf->Cell(95, $cell_y2, '代買方扣繳10% 利息所得稅', 0, 1);
        }

        $total -= (int) $detail['bTax'];
    }
    ##代扣利息所得稅

    ##買方待扣款項明細它項

    for ($i = 0; $i < count($tax_buyer); $i++) {

        // $pdf->Write($cell_y2, '*'.$tax_buyer[$i]['cTaxTitle']);
        // $p->MultiCell(60,40,'中文单元格内容',1,'C');
        // $p->setxy($x+60,$y);
        // $p->MultiCell(60,40,'中文单元格内容',1,'C');
        $y = $pdf->gety();
        $x = $pdf->getx();

        $pdf->MultiCell(47.5, $cell_y2, '*' . $tax_buyer[$i]['cTaxTitle']);

        $pdf->setxy($x + 47.5, $y);
        $pdf->Cell(30, $cell_y2, @number_format(round($tax_buyer[$i]['cTax'])), 0, 0, 'R');

        $pdf->setxy(105, $y);
        $pdf->MultiCell(95, $cell_y2, $tax_buyer[$i]['cTaxRemark'], 0, 1);

    }
    $pdf->Ln();
    ##

}
##

$pdf->Cell(190, $cell_gap, '', 0, 1);
$pdf->SetFontSize(12); // 手動換行
if ($detail['other_remark_buyer']) {
    // $pdf->Cell(190,$cell_y2,$detail['other_remark_buyer'],0,1) ;
    $pdf->MultiCell(190, $cell_y2, $detail['other_remark_buyer'], 0);
}

for ($i = 0; $i < count($remark_buy); $i++) {
    // $pdf->Cell(190,$cell_y2,$remark_buy[$i]['cRemark'],0,1) ;
    $pdf->MultiCell(190, $cell_y2, $remark_buy[$i]['cRemark'], 0);
}

if ($count > 0) {
    ##
    if ($check == 0 || $count > 1) {
        # code...

        $pdf->Cell(190, $cell_gap, '', 0, 1); // 手動換行

        $pdf->SetFontSize(12);
        $pdf->Cell(190, $cell_y2, '指定收受價金之帳戶', 0, 1);

        //畫線(雙線條)
        $pdf->SetFontSize(12);
        $xy = $pdf->GetY();
        $xy += ($line_gap / 2);
        $pdf->Line(10, $xy, 200, $xy);

        $pdf->SetFontSize(12);
        $xy = $pdf->GetY();
        $xy -= $line_gap;
        $pdf->Line(10, $xy, 200, $xy);
        ##

        $pdf->SetFontSize(12);
        $pdf->Cell(18.75, $cell_y4, '對象'); // 指定帳戶 Title
        $pdf->Cell(60, $cell_y4, '解匯行/分行');
        $pdf->Cell(44.25, $cell_y4, '帳號');
        $pdf->Cell(46.25, $cell_y4, '戶名');
        $pdf->Cell(20.75, $cell_y4, '金額', 0, 1);

        //畫線(單線條)
        $pdf->SetFontSize(12);
        $xy = $pdf->GetY();
        $xy -= $line_gap;
        $pdf->Line(10, $xy, 200, $xy);
        ##

        //建立銀行帳號表格
        $pdf->SetFontSize(12);
        $sql = '
			SELECT
				*,
				(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as bankMain,
				(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as bankBranch
			FROM
				tChecklistBank AS a
			WHERE
				cCertifiedId="' . $detail['cCertifiedId'] . '"
				AND cIdentity IN ("1","33","43","53")
				AND cHide = 0
			ORDER BY
				cOrder,cId
			ASC,
				cBankAccountNo
			DESC;
		';
        $rs = $conn->Execute($sql);

        while (!$rs->EOF) {
            $tmp = $rs->fields;
            //確認身分顯示
            switch ($tmp['cIdentity']) {
                case '1':
                    $tmp['cIdentity'] = '買方';
                    break;
                case '33':
                    $tmp['cIdentity'] = '仲介';
                    break;
                case '43':
                    $tmp['cIdentity'] = '地政士';
                    break;
                case '53':
                    $tmp['cIdentity'] = '';
                    break;
                default:
                    $tmp['cIdentity'] = '';
                    break;
            }
            ##

            //確認銀行顯示
            if ($tmp['bankMain'] && $tmp['bankBranch']) {
                $tmpArr = array();
                $tmpArr = explode('（', $tmp['bankBranch']);

                $tmp['bankMain'] = str_replace('（農金資中心所屬會員）', '', $tmp['bankMain']);

                $tmp['bank'] = $tmp['bankMain'] . '/' . $tmpArr[0];
            }
            ##

            // 指定帳戶表格
            $pdf->Cell(18.75, $cell_y5, $tmp['cIdentity'], 1); // 對象

            $pdf->SetFontSize(9);
            $tmp['bank'] = preg_replace('/\（.*?\）/', '', $tmp['bank']);
            $pdf->Cell(60, $cell_y5, $tmp['bank'], 1); // 解匯行/分行

            $pdf->SetFontSize(12);
            $pdf->Cell(44.25, $cell_y5, $tmp['cBankAccountNo'], 1); // 帳號

            $strLen = mb_strlen($tmp['cBankAccountName']);

            if (mb_strlen($tmp['cBankAccountName']) > 27) {
                $pdf->SetFontSize(8);
            } else {
                $pdf->SetFontSize(12);
            }
            $pdf->Cell(46.25, $cell_y5, $tmp['cBankAccountName'], 1); // 戶名
            //$pdf->MultiCell(41.25,$cell_y5,$tmp['cBankAccountName'],1,0) ;                    // 戶名

            $pdf->SetFontSize(12);
            if ($tmp['cMoney'] == 0) {
                $tmp['cMoney'] = '';
            }
            $pdf->Cell(20.75, $cell_y5, $tmp['cMoney'], 1, 1, 'R'); // 金額
            ##

            unset($tmp);
            $rs->MoveNext();
        }
    }
}

$itemNo = 1;
$pdf->SetFontSize(10);
$pdf->Cell(190, $cell_y6, '其他注意事項', 0, 1);

if ($detail['bNote'] != 1) {
    $pdf->Cell(5, $cell_y6, $itemNo . '.');
    $pdf->Cell(190, $cell_y6, '第一建築經理股份有限公司以點交確認單為專戶結算之依據，請確認上述內容後，於下方簽章處簽名蓋章：', 0, 1);
    $itemNo++;
}

$pdf->Cell(5, $cell_y6, $itemNo . '.');
$pdf->Cell(190, $cell_y6, '相關產權證明文件領取核對。', 0, 1);
$itemNo++;

$pdf->SetFontSize(10);
$pdf->Cell(5, $cell_y6, $itemNo . '.');
$pdf->Cell(45, $cell_y6, '本公司依財政部「電子發票實施作業要點」,電子發票於結案日後５日內開立完成,將不郵寄實體發票,請勾選:');

$y = $pdf->GetY();
$pdf->SetY($y + 5);
$x = $pdf->GetX();
$pdf->SetX($x + 5);
$pdf->Cell($x, $cell_y6, '□我不需索取紙本電子發票,由第一建經託管並兌獎,中獎後由第一建經主動通知我領獎事宜。');

$y = $pdf->GetY();
$pdf->SetY($y + 5);
$x = $pdf->GetX();
$pdf->SetX($x + 5);
$pdf->Cell($x, $cell_y6, '□捐贈「財團法人台灣兒童暨家庭扶助基金會」　我要索取紙本電子發票 □同戶籍地址□同買賣標的物地址');

$y = $pdf->GetY();
$pdf->SetY($y + 5);
$pdf->SetX($x + 5);
// $pdf->Cell($x,$cell_y6,'□指定地址:_______縣（市）_________鄉（鎮、市、區）________________路（街）____段');
$pdf->Cell($x, $cell_y6, '□指定地址:_______縣（市）_________鄉（鎮、市、區）________________路（街）__________________段');
$y = $pdf->GetY();
$pdf->SetY($y + 5);
$pdf->SetX($x + 5);
// $pdf->Cell($x,$cell_y6,'____巷_____弄____號____樓之 ___。');
$pdf->Cell($x, $cell_y6, '________________巷_________________弄________________號________________樓之 _______________。');

// $x = $pdf->GetX()+12 ;
// $pdf->Line($x,($y+10), ($x+165), ($y+10));

// $x = $pdf->GetX()-9 ;
// $y = $pdf->GetY() ;
// $pdf->Line($x,($y+12), ($x+185), ($y+12));

$y = $pdf->GetY();
$pdf->SetY($y + 8);
$x = $pdf->GetX();
$pdf->SetX($x + 5);
$pdf->Cell($x, $cell_y6, '未勾選視為同意不索取紙本電子發票,台端簽名後即代表知悉上開通知內容,您可至本公司官網查詢發票內容。');
$itemNo++;

$pdf->Ln();
$pdf->Cell(190, $cell_y3, '', 0, 1); // 手動換行

$pdf->Cell(5, $cell_y6, $itemNo . '.');
$pdf->Cell(190, $cell_y6, '專戶結算後之保留款及未收取履約保證手續費案件，專戶價金不計息。', 0, 1);
$pdf->Ln();
$pdf->Cell(63, $cell_y6, '買方簽章：');
$pdf->Cell(64, $cell_y6, '仲介方簽章：');
$pdf->Cell(63, $cell_y6, '地政士簽章：', 0, 1);
$itemNo++;

for ($i = 0; $i < 5; $i++) {
    $pdf->Cell(190, $cell_y6, '', 0, 1);
}
/* 2014/11/01 for 美亞 *///20150507時間已過直接隱藏
// if (($cSignDate >= '2014-11-01 00:00:00') && ($cSignDate <= '2015-04-30 23:59:59')) {

//     $pdf->SetFont('','B',11) ;
//     $y = $pdf->GetY() ;
//     $pdf->Cell(5,$cell_y6,'5.') ;
//     $pdf->Cell(185,$cell_y6,'※恭禧您獲得第一建經提供『個人居家綜合險』三個月保障，請填寫要保書郵寄地址：',0,1) ;
//     $pdf->SetY($pdf->GetY() + 1) ;
//     $pdf->SetX(19) ;

//     $pdf->Cell(185,$cell_y6,'□與履保費發票郵寄地址相同　□',0,2) ;
//     $pdf->Cell(50,$cell_y6,'') ;
//     $pdf->Cell(20,$cell_y6,'',0,2) ;
//     $pdf->Line(80,$pdf->GetY()-0.5,195,$pdf->GetY()-0.5) ;
//     $pdf->Rect(15,($y-1),185,15) ;

//     $title_no ++;
// }

/*20150505加入預售屋換約備註事項*/
if ($detail['bNote'] == 1) {
    $y = $pdf->GetY();
    $pdf->SetY($y + 10);
    $pdf->Cell(5, $cell_y6, $itemNo . '.');
    $pdf->Cell(200, $cell_y6, '※買賣雙方業於____年____月____日已向建設公司完成換約事宜，經買方確認無誤，請第一建築經理股份有限', 0, 1);

    $pdf->SetY($pdf->GetY() + 1);
    $pdf->SetX(19);
    $pdf->Cell(63, $cell_y6, '公司將履保專戶款項全數撥付至賣方指定帳戶。', 0, 1);
    $itemNo++;
}

unset($itemNo);

$pdf->SetFontSize(10);
$pdf->Text(12, 290, '中華民國 ________ 年 ________ 月 ________ 日　　聯絡電話：' . $company['tel'] . ' Ext.' . $undertaker['Ext'] . '　　傳真電話：' . $undertaker['FaxNum']);
