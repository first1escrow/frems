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

$sql = "SELECT
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cBranchFullName FROM tContractBank WHERE cId=bBank) AS cBranchFullName,
			(SELECT cBankAccount fROM tContractBank WHERE cId = bBank) AS cBankAccount,
			(SELECT cBankTrustAccount FROM tContractBank WHERE cId= bBank) AS cBankTrustAccount,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '" . $bId . "'";

$rs = $conn->Execute($sql);

$data                  = $rs->fields;
$data['CertifiedId_9'] = substr($data['bCertifiedId'], 5);

###########################################
//傳入參數
$iDate = dateformate($data['bDate']); //指示書日期
$iNo   = $data['bBookId']; //指示書編號

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
$html = '不動產買賣價金第一建經履約保證信託指示書';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');
##

//
$pdf->SetY($pdf->GetY() + 0.5);
$pdf->SetFont('msungstdlight', '', 12);
$pdf->SetFontSize(16);
$pdf->SetTextColor(0, 0, 0);

// $html = '玆請貴行於接到本指示通知後，於貴行桃園分行「第一商業銀行' ;
$html = '玆請貴行於接到本指示通知後，於貴行' . $data['cBranchFullName'] . '「第一商業銀行';
$pdf->Cell(0, 0, $html, 0, 1);
// $pdf->writeHTML($html, $ln=1, $fill=0, $reseth=true, $cell =true, $align='L') ;

$pdf->SetY($pdf->GetY() + 0.5);
$html = '受託信託財產專戶-第一建經價金履約保證」中支付利息款項，總金額';
// $pdf->writeHTML($html, $ln=1, $fill=0, $reseth=true, $cell =true, $align='L') ;
$pdf->Cell(0, 0, $html, 0, 1);

$pdf->SetY($pdf->GetY() + 0.5);
$x    = $pdf->GetX();
$y    = $pdf->GetY();
$html = '新台幣' . str_replace('元整', '', NumtoStr($payItemMoney));
$pdf->Cell(0, 0, $html);

#x
// $pdf->SetX(11.2);
$pdf->SetX(12.2);
$pdf->Cell(0, 0, '元', 0, 1);

// $pdf->Line(1.5,$pdf->GetY(),11.2,$pdf->GetY()) ;
$pdf->Line(1.5, $pdf->GetY(), 12.2, $pdf->GetY());

// $pdf->SetXY(12, $y);
$pdf->SetXY(13, $y);
$html = '，至如下帳戶';
// $pdf->writeHTML($html, $ln=1, $fill=0, $reseth=true, $cell =true, $align='L') ;
$pdf->cell(0, 0, $html, 0, 1);

$pdf->SetY($pdf->GetY() + 0.5);
$html = '戶名:第一建築經理股份有限公司';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY($pdf->GetY() + 0.5);
// $html = '帳號:271-10-351738' ;
$html = '帳號:' . $data['cBankAccount'];
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY($pdf->GetY() + 0.5);
$html = '銀行:第一商業銀行' . $data['cBranchFullName'];
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY($pdf->GetY() + 1);
$html = '此致';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetXY(2.6, $pdf->GetY() + 0.5);
$html = '第一商業銀行 信託處';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY($pdf->GetY() + 0.5);
$html = '委託人：第一建築經理股份有限公司';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY(17.5);
$html = '權責簽章人：';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->SetY(23.5);
$pdf->Rect(1.5, 23.5, 18, 0.06, 'F');

$img_file = 'images/stamp.png';
$pdf->Image($img_file, 5, 19, 6, 3.43);

$pdf->SetY($pdf->GetY() + 0.1);
$pdf->SetFontSize(13);

$html = '傳真指示日期：' . substr($iDate, 0, 3) . '年' . substr($iDate, 4, 2) . '月' . substr($iDate, 7, 2) . '日';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$html = '指示單編號：' . $iNo;
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$html = '取款戶名：第一商業銀行受託信託財產專戶-第一建經價金履約保證';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$html = '取款帳號：' . $data['cBankTrustAccount'];
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
##

//
$pdf->Output();
##
