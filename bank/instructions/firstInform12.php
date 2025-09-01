<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

//存根聯
function callReceipt()
{
    global $pdf, $data_detail, $data, $certifiedId, $iDate;

    $pdf->SetXY(13, 9.8);
    $html = '信託處專案業務部';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->Ln();

    $pdf->Line(10.5, 12, 17, 12);

    $pdf->SetXY(10.5, 12.1);
    if (date('Ymd') < "20200902") {
        $html = '分機&nbsp;4757&nbsp;黃小姐&nbsp;/&nbsp;&nbsp;分機&nbsp;4320&nbsp;蔡小姐';
    } else {
        $html = '分機&nbsp;1658&nbsp;李先生&nbsp;/&nbsp;&nbsp;分機&nbsp;4320&nbsp;林小姐';
    }
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY(13);

    $html = '';
    for ($i = 0; $i < 73; $i++) {
        $html .= '- ';
    }
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.5);
    $html = '第一建築經理(股)公司信託指示單(臨櫃作業專用)';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');

    $pdf->SetY($pdf->GetY() + 1);
    $html = '傳真指示日期：' . str_replace('-', '.', $iDate);

    // $html = '傳真指示日期：'.$data['bDate'] ;
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->setX($pdf->GetX() + 1); //主管：
    $pdf->writeHTML("審辦：", $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->setX($pdf->GetX() + 3); //
    $pdf->writeHTML("主管：", $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '指示單編號：' . $data['bBookId'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '信託專戶：' . $data['cBankTrustAccount'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '信託專戶戶名：第一商業銀行受託信託財產專戶-第一建經';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    // $html = '本指示單係通知取消'.$data_detail[0]['bTicketDelay'].'日繳交稅款，金額'.number_format($data['bMoney']).'元，領票人'.$data['breName'].'，身分證字號：<br>'.$data['breIdentifyId'].'，請通知貴行 '.$data['bReBank'].'分行將該筆款項轉存入信託專戶原保證號碼:' ;
    $html = "本指示單係通知取消" . $data_detail[0]['bTicketDelay'] . "日繳交稅款，繳款金額" . number_format($data['bMoney']) . "元，繳款人" . $data['breName'] . " 身分證字號：" . $data['breIdentifyId'] . "，請通知  貴行" . $data['bReBank'] . "分行將該筆款項轉存入信託專戶原保證號碼";
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = substr($data['bCertifiedId'], 0, 5) . '-' . $certifiedId . '。';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
    $pdf->Ln();
    $pdf->Ln();

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

$data            = $rs->fields;
$data['bReBank'] = str_replace('分行', '', $data['bReBank']);

##########################################################
//細項
$sql = "SELECT *, bMoney as money, bName as title FROM tBankTrankBookDetail WHERE bTrankBookId ='" . $data['bId'] . "' AND bDel = 0 ORDER BY bId ASC";

$rs = $conn->Execute($sql);

while (! $rs->EOF) {
    $data_detail[] = $rs->fields;

    $rs->MoveNext();
}

############################################

//傳入參數
$certifiedId = substr($data['bCertifiedId'], 5); //保證號碼
$bankBranch  = $data['bReBank'];                 //分行名稱
$iDate       = dateformate($data['bDate']);      //指示書日期
$iNo         = $data['bBookId'];                 //指示書編號
##

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(2, 3.17, 1.5, 3.17);
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
$html = $data['bReBank'] . '分行聯行往來經辦台照：';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->Ln();
##

//
$pdf->SetFontSize(12);
$pdf->SetTextColor(0, 0, 0);

$data_detail[0]['bTicketDelay'] = (substr($data_detail[0]['bTicketDelay'], 0, 3) + 1911) . "." . substr($data_detail[0]['bTicketDelay'], 4, 2) . "." . substr($data_detail[0]['bTicketDelay'], 7, 2);
$html                           = "本指示單係客戶第一建築經理(股)公司委請本行於" . $data_detail[0]['bTicketDelay'] . "日辦理大額繳稅，並已將該筆稅款金額" . number_format($data['bMoney']) . "元存入本行" . $data['bReBank'] . "分行，今因故無法於本日辦理稅款繳納，請於收到本指示單後，將該筆款項存入客戶第一建經信託專戶原保證號碼:" . substr($data['bCertifiedId'], 0, 5) . "-" . $certifiedId . "。";
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->Ln();

callReceipt();
##

##

//
$pdf->Output();
##
