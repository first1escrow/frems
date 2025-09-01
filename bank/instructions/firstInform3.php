<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

$_POST = escapeStr($_POST);
$bId   = $_POST['id'];
// $bId = 54946;

$sql = "SELECT
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cTrustAccountName FROM tContractBank WHERE cId=bBank) AS cTrustAccountName,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '" . $bId . "'";
$rs = $conn->Execute($sql);

$data                  = $rs->fields;
$tmp                   = expMoney($rs->fields['bExport_nu']);
$data['expMoney']      = $tmp['totalMoney'];
$data['bStatusName']   = BookStatus($rs->fields['bStatus']);
$data['bDate']         = dateformate($data['bDate']);
$data['expCount']      = $tmp['totalcount'];
$data['CertifiedId_9'] = substr($data['bCertifiedId'], 5);

//傳入參數
$iDate       = $data['bDate'];         //指示書日期
$iNo         = $data['bBookId'];       //指示書編號
$certifiedId = $data['CertifiedId_9']; //保證號碼

$bank           = getBank(substr($data['bObank'], 0, 3), substr($data['bObank'], 3)); //分行名稱
$bankDataMain   = $bank['BankName'];                                                  //解款行
$bankDataBranch = $bank['BanchName'];                                                 //解款行
$bank           = null;unset($bank);

$originBankDataMain   = $bankDataMain;   //顯示的解款行
$originBankDataBranch = $bankDataBranch; //顯示的解款行

$data['bCbank']        = empty($data['bCbank']) ? $data['bObank'] : $data['bCbank'];
$bank                  = getBank(substr($data['bCbank'], 0, 3), substr($data['bCbank'], 3)); //分行名稱
$correctBankDataMain   = $bank['BankName'];                                                  //解款行
$correctBankDataBranch = $bank['BanchName'];                                                 //解款行
$bank                  = null;unset($bank);

$errorAcc      = $data['bEaccount'];     //錯誤資料：帳號
$errorAccName  = $data['bEaccountName']; //錯誤資料：戶名
$errorAccMoney = $data['bEmoney'];       //錯誤資料：金額

$correctAcc      = $data['bCaccount'];     //正確資料：帳號
$correctAccName  = $data['bCaccountName']; //正確資料：戶名
$correctAccMoney = $data['bCmoney'];       //錯誤資料：金額
##

//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='" . $bId . "' AND bDel = 0";
$rs  = $conn->Execute($sql);

while (! $rs->EOF) {
    if ($rs->fields['bCat'] == '1') { //1:錯誤帳戶 //補通訊用
        $data_Error[] = $rs->fields;
    } else if ($rs->fields['bCat'] == '2') { //2:正確帳戶 //補通訊用
        $data_Correct[] = $rs->fields;
    }

    $rs->MoveNext();
}
##

//比對更正項目
$_account_item = '□';
$_name_item    = '□';
$_bank_item    = '□';
if ($data['bEaccount'] != $data['bCaccount']) {
    $_account_item = '■';
}

if ($data['bEaccountName'] != $data['bCaccountName']) {
    $_name_item = '■';
}

if ($data['bObank'] != $data['bCbank']) {
    $_bank_item = '■';
} else {
    $originBankDataMain   = $correctBankDataMain   = ''; //解款行
    $originBankDataBranch = $correctBankDataBranch = ''; //解款行
}

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFontSubsetting(true);
$pdf->SetFont('msungstdlight', '', 12);

$pdf->SetLeftMargin(1.5);
$pdf->SetRightMargin(1.5);
$pdf->AddPage();

//
$pdf->SetY(1); //頁面上原空白處變小
$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
$html = '第一銀行匯款更正申請作業指示單';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');
##

//
$pdf->SetY($pdf->GetY());
$pdf->SetFont('msungstdlight', '', 12);
$pdf->SetTextColor(0, 0, 0);

$html = $data['cBranchName'] . '分行匯兌';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$html = '經辦台照：';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$y = $pdf->GetY() + 0.5;
$pdf->SetXY(2.1, $y);
$pdf->SetFont('msungstdlight', 'B', 14);
$html = '第一商業銀行    匯款更正申請兼通訊單';
$pdf->Text(2.1, $y + 0.3, $html);
$pdf->SetXY(2.1, $y);
$pdf->Cell(10, 0, '', 0, 'L');
##

//
$pdf->SetFont('msungstdlight', '', 12);
$xE = $pdf->GetX();
$pdf->Cell(2.5, 1.25, '', 1, 'C');
$x  = $pdf->GetX();
$y  = $pdf->GetY();
$yE = $pdf->GetY() + 0.3;
$pdf->Text($xE + 0.3, $yE, '更正項目');

$pdf->SetXY($x, $y);
$pdf->Text($x + 1.2, $y + 0.1, $_account_item . '帳號  ' . $_name_item . '戶名');
$pdf->Text($x + 1.7, $y + 0.6, $_bank_item . '解款行');

$pdf->SetXY($x, $y);
$pdf->Cell(5.9, 1.25, '', 1, 'C');
$y = $pdf->GetY();
$pdf->Ln();
##

//
$pdf->SetFont('msungstdlight', '', 12);

$y = $pdf->GetY();
$pdf->SetXY(0.5, $y);
$pdf->MultiCell(2.1, 1.3, "解款行\n", 1, 'C');

$pdf->SetFont('msungstdlight', '', 8);
$pdf->Text(0.63, ($y + 0.58), '(含分支單位)');

$pdf->SetFont('msungstdlight', '', 12);
$pdf->SetY($y);

$y = $pdf->GetY();
$pdf->Rect(2.6, $y, 3.5, 1.3);
$pdf->Text(3.3, ($y + 0.3), $data['bObank']);

$pdf->SetXY(6.1, $y);
$pdf->MultiCell(1.9, 1.3, "原匯出\n日期", 1, 'C');

$pdf->SetY($y);
$y = $pdf->GetY();
$pdf->Rect(8, $y, 2.5, 1.3);
$pdf->Text(8.3, ($y + 0.3), $data['bDate']);

$pdf->SetXY(10.5, $y);
$x  = $pdf->GetX();
$y  = $pdf->GetY();
$yE = $y;
$pdf->MultiCell(2.2, 1.3, "檢核編號\n", 1, 'C');
$pdf->SetFont('msungstdlight', '', 12);
$pdf->SetXY($x, $y);
$pdf->Rect(12.7, $pdf->GetY(), 0.7, 1.3);
$pdf->Rect(13.4, $pdf->GetY(), 0.7, 1.3);
$pdf->Rect(14.1, $pdf->GetY(), 0.7, 1.3);
$pdf->Rect(14.8, $pdf->GetY(), 0.7, 1.3);
$pdf->Rect(15.5, $pdf->GetY(), 0.7, 1.3);
$pdf->Rect(16.2, $pdf->GetY(), 0.7, 1.3);
$pdf->Rect(16.9, $pdf->GetY(), 0.7, 1.3);
$pdf->Rect(17.6, $pdf->GetY(), 2.9, 1.3);
$pdf->Ln();

$y = $pdf->GetY();
##

//(本行填寫)
$pdf->SetFont('msungstdlight', '', 8);
$pdf->Text(10.85, ($y - 0.7), '(本行填寫)');
$pdf->SetFont('msungstdlight', '', 12);

//
$pdf->SetXY(0.5, $y);
$pdf->MultiCell(2.1, 5.99, "", 1, 'C');

//更正內容
$pdf->Text(1, $y + 2.45, '更正');
$pdf->Text(1, $y + 2.95, '內容');

//
$pdf->SetXY(2.6, $y);
$pdf->MultiCell(1.3, 3, " ", 1, 'C');

//(原)資料錯誤
$_y = $y + 0.75;
$_x = 2.8;
$pdf->Text($_x, $_y, '(原)');
$_y += 0.5;
$_x -= 0.1;
$pdf->Text($_x, $_y, '資料');
$_y += 0.5;
$pdf->Text($_x, $_y, '錯誤');

//
$pdf->SetXY(3.9, $y);
$pdf->Cell(2.2, 1, '　帳號　', 1, 'C');

$y = $pdf->GetY();

$pdf->Rect(6.1, $y, 0.86, 1);
$pdf->Rect(6.96, $y, 0.86, 1);
$pdf->Rect(7.82, $y, 0.86, 1);
$pdf->Rect(8.68, $y, 0.86, 1);
$pdf->Rect(9.54, $y, 0.86, 1);
$pdf->Rect(10.4, $y, 0.86, 1);
$pdf->Rect(11.26, $y, 0.86, 1);
$pdf->Rect(12.12, $y, 0.86, 1);
$pdf->Rect(12.98, $y, 0.86, 1);
$pdf->Rect(13.84, $y, 0.86, 1);
$pdf->Rect(14.7, $y, 0.86, 1);
$pdf->Rect(15.56, $y, 0.86, 1);
$pdf->Rect(16.42, $y, 0.86, 1);
$pdf->Rect(17.28, $y, 3.22, 1);

if ($_account_item == '■') {
    $_account_no = $errorAcc;

    $pdf->Text((6.1 + 0.2), ($y + 0.3), substr($_account_no, 0, 1));
    $pdf->Text((6.96 + 0.2), ($y + 0.3), substr($_account_no, 1, 1));
    $pdf->Text((7.82 + 0.2), ($y + 0.3), substr($_account_no, 2, 1));
    $pdf->Text((8.68 + 0.2), ($y + 0.3), substr($_account_no, 3, 1));
    $pdf->Text((9.54 + 0.2), ($y + 0.3), substr($_account_no, 4, 1));
    $pdf->Text((10.4 + 0.2), ($y + 0.3), substr($_account_no, 5, 1));
    $pdf->Text((11.26 + 0.2), ($y + 0.3), substr($_account_no, 6, 1));
    $pdf->Text((12.12 + 0.2), ($y + 0.3), substr($_account_no, 7, 1));
    $pdf->Text((12.98 + 0.2), ($y + 0.3), substr($_account_no, 8, 1));
    $pdf->Text((13.84 + 0.2), ($y + 0.3), substr($_account_no, 9, 1));
    $pdf->Text((14.7 + 0.2), ($y + 0.3), substr($_account_no, 10, 1));
    $pdf->Text((15.56 + 0.2), ($y + 0.3), substr($_account_no, 11, 1));
    $pdf->Text((16.42 + 0.2), ($y + 0.3), substr($_account_no, 12, 1));
    $pdf->Text((17.28 + 0.2), ($y + 0.3), substr($_account_no, 13, 1));

    $_account_no = null;unset($_account_no);
}

$pdf->Ln();

$pdf->Rect(3.9, $y, 16.6, 1);

$pdf->SetXY(3.9, ($y + 1));
$pdf->Cell(2.2, 1, '　戶名　', 1, 'C');

if ($_name_item == '■') {
    $pdf->Cell(14.4, 1, $errorAccName, 1, 'C');
}
##

//解匯行
$y = $pdf->GetY();
$pdf->SetXY(3.9, ($y + 1));
$pdf->Cell(2.2, 1, '   解匯行', 0, 'C');
$pdf->Rect(6.1, ($y + 1), 14.4, 1);
$pdf->Ln();
$pdf->Text(6.2, ($y + 1.2), $originBankDataMain . $originBankDataBranch);

//
$y = 8.725695;
$pdf->SetXY(2.6, $y);
$pdf->MultiCell(1.3, 2.99, ' ', 1, 'C');

//更正資料
$_y = $y + 0.95;
$_x = 2.7;
$pdf->Text($_x, $_y, '更正');
$_y += 0.5;
$pdf->Text($_x, $_y, '資料');

$pdf->SetXY(3.9, $y);
$pdf->Cell(2.2, 1, '正確帳號', 1, 'C');

//
$y = $pdf->GetY();

$pdf->Rect(6.1, $y, 0.86, 1);
$pdf->Rect(6.96, $y, 0.86, 1);
$pdf->Rect(7.82, $y, 0.86, 1);
$pdf->Rect(8.68, $y, 0.86, 1);
$pdf->Rect(9.54, $y, 0.86, 1);
$pdf->Rect(10.4, $y, 0.86, 1);
$pdf->Rect(11.26, $y, 0.86, 1);
$pdf->Rect(12.12, $y, 0.86, 1);
$pdf->Rect(12.98, $y, 0.86, 1);
$pdf->Rect(13.84, $y, 0.86, 1);
$pdf->Rect(14.7, $y, 0.86, 1);
$pdf->Rect(15.56, $y, 0.86, 1);
$pdf->Rect(16.42, $y, 0.86, 1);
$pdf->Rect(17.28, $y, 3.22, 1);

if ($_account_item == '■') {
    $_account_no = $correctAcc;

    $pdf->Text((6.1 + 0.2), ($y + 0.3), substr($_account_no, 0, 1));
    $pdf->Text((6.96 + 0.2), ($y + 0.3), substr($_account_no, 1, 1));
    $pdf->Text((7.82 + 0.2), ($y + 0.3), substr($_account_no, 2, 1));
    $pdf->Text((8.68 + 0.2), ($y + 0.3), substr($_account_no, 3, 1));
    $pdf->Text((9.54 + 0.2), ($y + 0.3), substr($_account_no, 4, 1));
    $pdf->Text((10.4 + 0.2), ($y + 0.3), substr($_account_no, 5, 1));
    $pdf->Text((11.26 + 0.2), ($y + 0.3), substr($_account_no, 6, 1));
    $pdf->Text((12.12 + 0.2), ($y + 0.3), substr($_account_no, 7, 1));
    $pdf->Text((12.98 + 0.2), ($y + 0.3), substr($_account_no, 8, 1));
    $pdf->Text((13.84 + 0.2), ($y + 0.3), substr($_account_no, 9, 1));
    $pdf->Text((14.7 + 0.2), ($y + 0.3), substr($_account_no, 10, 1));
    $pdf->Text((15.56 + 0.2), ($y + 0.3), substr($_account_no, 11, 1));
    $pdf->Text((16.42 + 0.2), ($y + 0.3), substr($_account_no, 12, 1));
    $pdf->Text((17.28 + 0.2), ($y + 0.3), substr($_account_no, 13, 1));

    $_account_no = null;unset($_account_no);
}

$pdf->Ln();

$pdf->Rect(3.9, $y, 16.6, 1);

$pdf->SetXY(3.9, ($y + 1));
$pdf->Cell(2.2, 1, '正確戶名', 1, 'C');

if ($_name_item == '■') {
    $pdf->Cell(14.4, 1, $correctAccName, 1, 'C');
}
##

//正確解匯行
$y = $pdf->GetY();
$pdf->SetXY(3.9, ($y + 1));
$pdf->Cell(2.2, 1, '正確解匯行', 1, 'C');
$pdf->Rect(6.1, ($y + 1), 14.4, 1);
$pdf->Ln();
$pdf->Text(6.2, ($y + 1.2), $correctBankDataMain . $correctBankDataBranch);

//
$y = 11.725695;
$pdf->SetXY(0.5, $y);
$pdf->MultiCell(13.2, 2, "\n□匯款人申請更正：\n（簽名或蓋章）", 1, 'L');

$pdf->SetXY(13.7, $y);
$pdf->Cell(6.8, 2, '□本行內部更正', 1, 'L');
##

//
$y = $pdf->GetY() + 2;
$pdf->SetXY(1.5, $y);
$pdf->MultiCell(0, 0, '請於接獲匯款更正申請兼通訊單(摘要：詳第一建經指示單編號' . $iNo . ')後，依下列指示事項辦理', 0, 'L');
##

//
$pdf->SetXY(9.5, 14.8);
$html = '信託處專案業務部';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
$pdf->Ln();

$pdf->Line(9, 17, 15.5, 17);

$pdf->SetXY(9, 17.1);
if (date('Ymd') < "20200902") {
    $html = '分機&nbsp;4757&nbsp;黃小姐&nbsp;/&nbsp;&nbsp;分機&nbsp;4320&nbsp;蔡小姐';
} else {
    $html = '分機&nbsp;1658&nbsp;李先生&nbsp;/&nbsp;&nbsp;分機&nbsp;4320&nbsp;林小姐';
}
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
##

//
$pdf->SetY(21.4);
$pdf->SetFontSize(12);

$pdf->SetY(18);
$html = '';
for ($i = 0; $i < 69; $i++) {
    $html .= '- ';
}
$pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');
##

//
$pdf->SetY($pdf->GetY() + 0.5);
$html = '第一建築經理(股)公司信託指示單(匯款更正專用)';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');

$pdf->SetY($pdf->GetY() + 0.3);
$html = '傳真指示日期：' . str_replace('-', '.', $iDate);
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY($pdf->GetY() + 0.2);
$html = '指示單編號：' . $iNo;
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY($pdf->GetY() + 0.2);
$html = '保證號碼：' . $certifiedId;
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

if (! empty($originBankDataBranch)) {
    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '解&nbsp;&nbsp;款&nbsp;&nbsp;行：' . $originBankDataMain . '&nbsp;/&nbsp;' . $originBankDataBranch;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
}

$pdf->SetY($pdf->GetY() + 0.2);
$html = '錯誤資料：帳號：' . $errorAcc . '&nbsp;&nbsp;戶名：' . $errorAccName . '&nbsp;&nbsp;金額:' . $errorAccMoney;
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

for ($i = 0; $i < count($data_Error); $i++) {
    $html = '錯誤資料：帳號：' . $data_Error[$i]['bEaccount'] . '&nbsp;&nbsp;戶名：' . $data_Error[$i]['bEaccountName'] . '&nbsp;&nbsp;金額:' . $data_Error[$i]['bEmoney'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
}

if (! empty($correctBankDataBranch)) {
    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '正確解匯行：' . $correctBankDataMain . '&nbsp;/&nbsp;' . $correctBankDataBranch;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
}

$pdf->SetY($pdf->GetY() + 0.2);
$html = '更正資料：帳號：' . $correctAcc . '&nbsp;&nbsp;戶名：' . $correctAccName . '&nbsp;&nbsp;金額:' . $correctAccMoney;
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

for ($i = 0; $i < count($data_Correct); $i++) {
    $html = '更正資料：帳號：' . $data_Correct[$i]['bEaccount'] . '&nbsp;&nbsp;戶名：' . $data_Correct[$i]['bEaccountName'] . '&nbsp;&nbsp;金額:' . $data_Correct[$i]['bEmoney'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
}
if ($data['bOther']) {
    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '其他：' . $data['bOther'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
}
##

$pdf->SetY($pdf->GetY() + 0.2);
$html = '&nbsp;&nbsp;&nbsp;&nbsp;此致';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY($pdf->GetY() + 0.2);
$html = '第一商業銀行 信託處';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$y = $pdf->GetY() - 0.5;
$pdf->SetY($y);
$pdf->SetX(8);
$html = '第一建築經理股份有限公司';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
$img_file = 'images/stamp.png';
$pdf->Image($img_file, 14, 24.2, 6, 3.43);

$pdf->Line(8, 28, 17, 28);
##

//
$pdf->Output();
##
