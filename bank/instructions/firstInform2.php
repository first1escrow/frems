<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

##
$_POST = escapeStr($_POST);
$bId   = $_POST['id'];
//$bId = '44314';

$sql = "SELECT
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cBankTrustAccount FROM tContractBank WHERE cId= bBank) AS cBankTrustAccount,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName,
            (SELECT count(tId) AS count FROM tBankTrans WHERE tExport_nu = bExport_nu) AS count
		FROM
			tBankTrankBook
		WHERE
			bId = '" . $bId . "'";
$rs = $conn->Execute($sql);

$data                  = $rs->fields;
$data['CertifiedId_9'] = substr($data['bCertifiedId'], 5);
$postfix               = ($data['bBank'] == 7) ? '' : '-第一建經價金履約保證';
##

//傳入參數
$iDate = dateformate($data['bDate']); //指示書日期
$iNo   = $data['bBookId'];            //指示書編號

$payItemMoney = $data['bMoney']; //付款金額
##

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFontSubsetting(true);

$pdf->SetLeftMargin(1.5);
$pdf->SetRightMargin(1.5);
$pdf->AddPage();

//
$pdf->SetY(2.3);
$pdf->SetFont('msungstdlight', 'B', 18);
$pdf->SetTextColor(0, 0, 0);
$html = '第一建經價金履約保證信託指示書';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');
$html = '(價金撥付)';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');
##

//
$pdf->SetY($pdf->GetY() + 0.5);
$pdf->SetFont('msungstdlight', '', 12);
$pdf->SetFontSize(16);
$pdf->SetTextColor(0, 0, 0);

$x    = $pdf->GetX();
$y    = $pdf->GetY();
$html = "玆請    貴行於接到本指示通知後，於    貴行" . $data['cBranchName'] . "分行「第一商業銀行受託";
$pdf->Text($x, $y, $html);
$pdf->Ln();

$x = $pdf->GetX();
$y = $pdf->GetY();
// $html = "信託財產專戶-第一建經價金履約保證」中支付相關款項，總金額";
$html = "信託財產專戶」中支付相關款項，總金額";
$pdf->Text($x, $y, $html);
$pdf->Ln();

$pdf->SetY($pdf->GetY() + 0.5);
$x    = $pdf->GetX();
$y    = $pdf->GetY();
$_y   = $pdf->GetY();
$html = '新台幣';
$pdf->Cell(0, 0, $html);

$pdf->SetFontSize(24);
$html = str_replace('元整', '', number_format($payItemMoney));
$pdf->Text(4, ($_y - 0.3), $html);
$pdf->SetFontSize(16);

$pdf->SetXY(13.2, $y);
$pdf->Cell(0, 0, '元', 0, 1);

$pdf->Line(1.5, ($y + 0.8), 14.2, ($y + 0.8));

$pdf->SetXY(14.2, $y);
$recordCount = empty($data['bSpecificCount']) ? $data['count'] : $data['bSpecificCount'];
$html        = '整，共 ' . $recordCount . ' 筆相關支付';
$pdf->cell(0, 0, $html, 0, 1);

$pdf->SetY($pdf->GetY() + 0.5);
$html = '明細，如本公司於第e金網上傳資料。';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

//
$pdf->SetXY(14.2, ($pdf->GetY() - 0.8));
$pdf->cell(5, 2, '', 1, 0, 'C');
$pdf->SetXY(14.2, $pdf->GetY());
$pdf->cell(5, 0.8, '', 1, 0, 'C', 0);
$pdf->SetFontSize(10);
$pdf->SetX(20);
$txt = '第一商業銀行信託處';
$pdf->Text(15, ($pdf->GetY() + 0.2), $txt);
$y = $pdf->GetY() + 0.6;
$pdf->Rect(14.82, ($pdf->GetY() + 0.6), 0.01, 1.17);
$txt = '經';
$y1  = $pdf->GetY() + 0.8;
$pdf->Text(14.2, ($pdf->GetY() + 0.8), $txt);
$txt = '辦';
$y2  = $pdf->GetY() + 0.5;
$pdf->Text(14.2, ($pdf->GetY() + 0.5), $txt);

$pdf->Rect(16.8, $y, 0.01, 1.17);
$txt = '主';
$pdf->Text(16.78, $y1, $txt);
$pdf->Rect(17.4, $y, 0.01, 1.17);
$txt = '管';
$pdf->Text(16.78, $y2, $txt);
##

$pdf->SetFontSize(16);
$pdf->SetY($pdf->GetY() + 1);
$html = '此致';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetXY(2.6, 11.5);
$html = '第一商業銀行 信託處';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY(14.5);
$html = '委託人：第一建築經理股份有限公司';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY(15.5);
$html = '權責簽章人：';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$y = $pdf->GetY();
$pdf->Rect(18, $y, 2, 0.8);
$pdf->SetFontSize(10);
$pdf->Rect(18, $pdf->GetY(), 2, 2.5);
$txt = '核章訖';
$pdf->Text(18.4, ($pdf->GetY() + 0.2), $txt);

$pdf->SetY(23);
$pdf->Rect(1.5, 23, 18, 0.06, 'F');

$img_file = 'images/stamp.png';
$pdf->Image($img_file, 5, 19, 6, 3.43);

$pdf->SetY($pdf->GetY() + 0.1);
$pdf->SetFontSize(13);

$html = '傳真指示日期：' . substr($iDate, 0, 3) . '年' . substr($iDate, 4, 2) . '月' . substr($iDate, 7, 2) . '日';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$html = '第e金網付款批號：' . $iNo;
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$html = '取款戶名：第一商業銀行受託信託財產專戶' . $postfix;
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$html = '取款帳號：' . $data['cBankTrustAccount'];
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
##

//
$pdf->SetY($pdf->GetY() + 0.5);
$pdf->SetFontSize(13);

$html = '第一商業銀行聯絡窗口:';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
$html = '信託部-李先生 (02) 2348-1658';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
$html = '傳真電話:(02) 2311-6351';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
##

//
$pdf->Output();
##
