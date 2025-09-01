<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

//
function callTicket()
{
    global $pdf, $certifiedId, $bankBranch, $iNo, $iDate, $payItemMoney, $payTaker, $payTakerId, $ticketNo;
    global $cashData;

    $pdf->SetFont('msungstdlight', '', 12);
    $pdf->SetTextColor(0, 0, 0);

    $html = '■ 退票領回   支票號碼：' . $ticketNo . '，金額：' . number_format($payItemMoney) . '元。';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
    $pdf->SetY($pdf->GetY() + 0.2);

    $pdf->Ln();
    $pdf->Ln();

    callReceipt();
}

function callTicket2()
{
    global $pdf, $certifiedId, $bankBranch, $iNo, $iDate, $payItemMoney, $payTaker, $payTakerId, $ticketNo;
    global $cashData;

    $pdf->SetFont('msungstdlight', '', 12);
    $pdf->SetTextColor(0, 0, 0);

    $html = '■ 代收票據領回   支票號碼：' . $ticketNo . '，金額：' . number_format($payItemMoney) . '元。';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
    $pdf->SetY($pdf->GetY() + 0.2);

    $pdf->Ln();
    $pdf->Ln();

    callReceipt();
}
##

//存根聯
function callReceipt()
{
    global $pdf, $certifiedId, $bankBranch, $iNo, $iDate, $payItemMoney, $payTaker, $payTakerId, $bankTrustAccount, $postfix;

    $pdf->SetY(8);
    $html = '領票人：' . $payTaker . '，身分證字號：' . $payTakerId;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '領票人簽收欄：';
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $html = '
	<table style="border:1px dashed black;">
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td style="width:50%;">&nbsp;&nbsp;姓名：</td><td style="width:50%;">&nbsp;&nbsp;日期：</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td style="width:50%;">&nbsp;&nbsp;電話：</td><td style="width:50%;">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
	</table>
	';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetXY(13, 12.8);
    $html = '信託處專案業務部';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->Ln();

    $pdf->Line(10.5, 15, 17, 15);

    $pdf->SetXY(10.5, 15.1);
    $html = '分機&nbsp;1658&nbsp;李先生&nbsp;/&nbsp;&nbsp;分機&nbsp;4320&nbsp;林小姐';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY(16);
    $html = '';
    for ($i = 0; $i < 73; $i++) {
        $html .= '- ';
    }
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.5);
    $html = '第一建築經理(股)公司信託指示單(代收票據領回/退票領回專用)';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');

    $pdf->SetY($pdf->GetY() + 1);
    $html = '傳真指示日期：' . str_replace('-', '.', $iDate);
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->setX($pdf->GetX() + 1); //主管：
    $pdf->writeHTML("審辦：", $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->setX($pdf->GetX() + 3); //
    $pdf->writeHTML("主管：", $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '指示單編號：' . $iNo;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '保證號碼：' . $certifiedId;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '取款戶名：第一商業銀行受託信託財產專戶' . $postfix;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '取款帳號：' . $bankTrustAccount;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '本指示單取款總金額：新臺幣' . NumtoStr($payItemMoney) . '元整。';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '請依上列指示事項通知 貴行' . $bankBranch . '分行。';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '此致';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '第一商業銀行 信託處';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() - 2);
    $pdf->SetX(10.5);
    $html = '第一建築經理股份有限公司';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->Line(10.5, 27, 17, 27);

    $img_file = 'images/stamp.png';
    $pdf->Image($img_file, 11, 23, 6, 3.43);
}
##

$_POST = escapeStr($_POST);
$bId   = $_POST['id'];

$sql = "SELECT
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cBankTrustAccount FROM tContractBank WHERE cId= bBank) AS cBankTrustAccount,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '" . $bId . "'";

$rs = $conn->Execute($sql);

$data             = $rs->fields;
$data['bReBank']  = str_replace('分行', '', $data['bReBank']);
$bankTrustAccount = $data['cBankTrustAccount'];
$postfix          = ($data['bBank'] == 7) ? '' : '-第一建經價金履約保證';
##

//細項
$sql = "SELECT *, bMoney as money, bName as title FROM tBankTrankBookDetail WHERE bTrankBookId ='" . $data['bId'] . "' AND bDel = 0 ORDER BY bId ASC";
$rs  = $conn->Execute($sql);

while (! $rs->EOF) {
    $data_detail[] = $rs->fields;
    $rs->MoveNext();
}
##

//傳入參數
$certifiedId = substr($data['bCertifiedId'], 5); //保證號碼
$bankBranch  = $data['bReBank'];                 //分行名稱
$iDate       = dateformate($data['bDate']);      //指示書日期
$iNo         = $data['bBookId'];                 //指示書編號

if ($data['bCategory'] == 7) {
    $payItem      = '退票領回';
    $payItemMoney = $data_detail[0]['bMoney'];    //付款金額
    $ticketNo     = $data_detail[0]['bTicketNo']; //支票號碼
} else if ($data['bCategory'] == 8) {         //代收票據領回
    $payItem      = '代收票據領回';
    $payItemMoney = $data_detail[0]['bMoney'];    //付款金額
    $ticketNo     = $data_detail[0]['bTicketNo']; //支票號碼
}

$payTaker   = $data['breName'];       //領票人
$payTakerId = $data['breIdentifyId']; //領票人身分證字號
##

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFontSubsetting(true);
$pdf->SetFont('msungstdlight', '', 12);

$pdf->AddPage();

//
$pdf->SetY(2.3);
$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
$html = '第一銀行櫃檯交易傳真作業指示單';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');
##

//
$pdf->SetY($pdf->GetY() + 0.5);
$pdf->SetFont('msungstdlight', '', 12);
$pdf->SetFontSize(12);
$pdf->SetTextColor(0, 0, 0);
$html = $data['bReBank2'] . '分行台照：';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->Ln();
##

//
$pdf->SetFontSize(12);
$pdf->SetTextColor(0, 0, 0);
$html = "本指示單係客戶第一建築經理(股)公司辦理代收票據及退票領回等相關業務，請於接獲傳真指示確認內容後";
$html .= "(摘要：第一建經指示單編號" . $iNo . ")，依下列指示事項辦理：";

$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
$pdf->Ln();
##

//
switch ($payItem) {
    case '退票領回':
        callTicket();
        break;
    case '代收票據領回':
        callTicket2();
        break;
    default:
        break;
}
##

//
$pdf->Output();
##
