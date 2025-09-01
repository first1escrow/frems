<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

//一銀行內虛轉虛
function vr2vr(&$pdf, $data, $type = 1)
{
    ($type == 1) ? vrType1($pdf, $data) : vrType2($pdf, $data);
}

function vrType1(&$pdf, $data)
{
    $pdf->SetTextColor(0, 0, 0);

    $pdf->SetFontSize(14);
    $pdf->setCellHeightRatio(2);

    $html = '本指示單係通知，信託帳戶帳號：' . $data['tVR_Code'] . '，金額 ' . number_format($data['bMoney']) . ' 元，請將該筆款項存入客戶第一銀行信託專戶第一建經保證號碼： ' . $data['tAccount'] . '。';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $y = $pdf->GetY() + 10;
    $pdf->SetXY(114, $y);

    $pdf->SetFontSize(14);
    $pdf->setCellHeightRatio(1);

    $html = '信託處專案業務部';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $y = $pdf->GetY() + 30;
    $pdf->Line(110, $y, 160, $y);
    $pdf->SetXY(110, ($y + 2));

    $html = '分機 4757 / 分機 4320';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
}

function vrType2(&$pdf, $data)
{
    $pdf->SetTextColor(0, 0, 0);

    $pdf->SetFont('msungstdlight', '', 14);
    $pdf->setCellHeightRatio(1);

    $y = $pdf->GetY();

    $html = '傳真指示日期：' . str_replace('-', '.', $data['bDate']);
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetXY(100, $y);
    $html = '審辦：';
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetXY(140, $y);
    $html = '主管：';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->Ln();

    $html = '指示單編號：' . $data['bBookId'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $y = $pdf->GetY();
    $pdf->SetY($y);
    $pdf->setCellHeightRatio(2);

    $html = '信託專戶：' . $data['cBankTrustAccount'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetFontSize(14);

    $html = '信託專戶戶名：' . $data['cTrustAccountNameEC'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $y = $pdf->GetY() + 5;
    $pdf->SetY($y);

    $html = '本指示單係通知由信託帳戶帳號：' . $data['tVR_Code'] . '金額 ' . number_format($data['bMoney']) . ' 元轉存入 ' . $data['tAccount'] . '，請於收到本指示單後，將該筆款項存入客戶第一銀行信託專戶保證號碼：' . $data['tAccount'] . '。';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $y = $pdf->GetY() + 30;
    $pdf->SetY($y);
    $html = '此致';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
    $html = '第一商業銀行  信託處';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $y = $pdf->GetY() - 35;
    $pdf->setY($y);
    $html = '第一建築經理股份有限公司';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'R');

    $y        = $pdf->GetY() + 1;
    $img_file = __DIR__ . '/images/stamp.png';
    $pdf->Image($img_file, 128, $y, 60, 34.3);

    $y = $pdf->GetY();
    $y += 35;
    $pdf->SetY($y);

    $pdf->SetFontSize(8);
    $html = '(加蓋有權人員簽章)';
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'R');

    $x1 = 118;
    $x2 = $x1 + 68;
    $y += 6;

    $pdf->Line($x1, $y, $x2, $y);
}

$_POST = escapeStr($_POST);
$bId   = $_POST['id'];
// $bId = 54628;

if (!is_numeric($bId)) {
    http_response_code(400);
    exit('Invalid ID indicated');
}

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

$sql = 'SELECT
            a.bBookId,
            a.bDate,
            a.bMoney,
            b.tVR_Code,
            b.tAccount,
            (SELECT cBankTrustAccount FROM tContractBank WHERE SUBSTRING(cBankVR, 1, 5) = SUBSTRING(b.tVR_Code, 1 , 5)) AS cBankTrustAccount,
            (SELECT cTrustAccountNameEC FROM tContractBank WHERE SUBSTRING(cBankVR, 1, 5) = SUBSTRING(b.tVR_Code, 1 , 5)) AS cTrustAccountNameEC
        FROM
            tBankTrankBook AS a
        JOIN
            tBankTrans AS b ON a.bExport_nu = b.tExport_nu AND b.tCode2 = "一銀內轉"
        WHERE
            a.bId = ' . $bId . ';';
$rs   = $conn->Execute($sql);
$data = $rs->fields;

// $data['tAccount'] = '60001100547559';

$dash_border = '';
for ($i = 0; $i < 100; $i++) {
    $dash_border .= '-';
}

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(30, 20, 30);
$pdf->setFontSubsetting(true);
$pdf->SetFont('msungstdlight', '', 12);

$pdf->AddPage();

//
$pdf->SetY(18);
$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
$html = '第一銀行櫃檯交易傳真作業指示單';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');
##

//間隔
$pdf->SetY($pdf->GetY() + 0.5);
$pdf->SetFont('msungstdlight', '', 12);
$pdf->Ln();

//
vr2vr($pdf, $data, 1);

//
$y = $pdf->GetY() + 5;
$pdf->SetY($y);

//分隔線
$pdf->Cell(0, 0, $dash_border, 0, 1);

//
$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->SetTextColor(0, 0, 0);

$html = '第一建築經理(股)公司信託指示單(臨櫃作業專用)';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');
$pdf->SetY($pdf->GetY() + 10);

//
vr2vr($pdf, $data, 2);

//
$pdf->Output();
##
