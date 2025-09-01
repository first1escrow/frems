<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

//大額繳稅
function callPayTaxTxt()
{
    global $pdf, $certifiedId, $bankBranch, $iNo, $iDate, $payItemMoney, $payTaker, $payTakerId;

    $pdf->SetFont('msungstdlight', '', 12);
    $pdf->SetTextColor(0, 0, 0);

    $html = '■ 繳交稅款金額：' . number_format($payItemMoney) . '元';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
    $pdf->Ln();
    $pdf->Ln();

    callReceipt();
}
##

//臨櫃領現
function callCash()
{
    global $pdf, $certifiedId, $bankBranch, $iNo, $iDate, $payItemMoney, $payTaker, $payTakerId;
    global $cashData;

    $pdf->SetFont('msungstdlight', '', 12);
    $pdf->SetTextColor(0, 0, 0);

    foreach ($cashData as $k => $v) {
        if ($v['bStop'] == 0) {
            $stopMsg = '禁止背書轉讓';
        } else {
            $stopMsg = '不禁止背書轉讓';
        }

        $html = '■ 開立本行支票，抬頭：' . $v['title'] . '，' . $stopMsg . '，金額：' . number_format($v['money']) . '元。';
        $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
        $pdf->SetY($pdf->GetY() + 0.2);
    }
    $pdf->Ln();
    $pdf->Ln();

    callReceipt();
}
##

//存根聯
function callReceipt()
{
    global $pdf, $certifiedId, $bankBranch, $iNo, $iDate, $payItemMoney, $payTaker, $payTakerId, $data, $postfix;

    $pdf->SetY($pdf->GetY() - 0.8);

    $html = '領票人/繳款人：' . $payTaker . '，身分證字號：' . $payTakerId;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '領票人簽收欄：';
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $html = '
	<table style="border:1px dashed black;">

		<tr><td style="width:50%;">&nbsp;&nbsp;姓名：</td><td style="width:50%;">&nbsp;&nbsp;日期：</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td style="width:50%;">&nbsp;&nbsp;電話：</td><td style="width:50%;">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
	</table>
	';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $y = $pdf->GetY() + 0.2;
    $pdf->SetXY(13, $y);
    $html = '信託處專案業務部';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->Ln();
    $y = $pdf->GetY() + 1;
    $pdf->Line(10.5, $y, 17, $y);

    $y = $pdf->GetY() + 1.2;

    $pdf->SetXY(1.5, $y);
    $creaerTime = str_replace('-', '', substr($data['bCreatTime'], 0, 10));
    if ($creaerTime >= "20210126" && $data['bCategory'] == 3) {
        $pdf->writeHTML('請回傳:' . $data['Fax'], $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');
    }

    $pdf->SetXY(10.5, $y);
    if ($creaerTime < "20200902") {
        $html = '分機&nbsp;4757&nbsp;黃小姐&nbsp;/&nbsp;&nbsp;分機&nbsp;4320&nbsp;蔡小姐';
    } else {
        $html = '分機&nbsp;1658&nbsp;李先生&nbsp;/&nbsp;&nbsp;分機&nbsp;4320&nbsp;林小姐';
    }
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);

    $html = '';
    for ($i = 0; $i < 73; $i++) {
        $html .= '- ';
    }
    $pdf->writeHTML($html, $ln = 0, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.5);
    $html = '第一建築經理(股)公司信託指示單(開立票據/繳交稅款專用)';
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'C');

    $pdf->SetY($pdf->GetY() + 1);
    $html = '傳真指示日期：' . str_replace('-', '.', $iDate);
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '指示單編號：' . $iNo;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '取款戶名：第一商業銀行受託信託財產專戶' . $postfix;
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '取款帳號：' . $data['cBankTrustAccount'];
    $pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

    $pdf->SetY($pdf->GetY() + 0.2);
    $html = '本指示單取款總金額：新臺幣' . NumtoStr($payItemMoney) . '元整';
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
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName,
			(SELECT cBankTrustAccount FROM tContractBank WHERE cId= bBank) AS cBankTrustAccount,
			(SELECT pFaxNum FROM tPeopleInfo WHERE pId = bCreatorId) AS Fax
		FROM
			tBankTrankBook
		WHERE
			bId = '" . $bId . "'";
$rs = $conn->Execute($sql);

$data            = $rs->fields;
$data['bReBank'] = str_replace('分行', '', $data['bReBank']);
$postfix         = ($data['bBank'] == 7) ? '' : '-第一建經價金履約保證';
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

if ($data['bCategory'] == 3) {
    $payItem      = '本行支票'; //付款類別
    $cashData     = $data_detail;
    $payItemMoney = $data['bMoney'];
} else if ($data['bCategory'] == 4) {
    $payItem      = '大額繳稅';
    $payItemMoney = $data_detail[0]['bMoney']; //付款金額
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
$html = $bankBranch . '分行聯行往來經辦台照：';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->Ln();
##

//
$pdf->SetFontSize(12);
$pdf->SetTextColor(0, 0, 0);
$html = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本指示單係客戶第一建築經理(股)公司委請本行辦理' . $payItem . '，請於確認接獲劃收電告(摘要：詳第一建經指示單編號' . $iNo . '款項後，依下列指示事項辦理：';
$pdf->writeHTML($html, $ln = 1, $fill = 0, $reseth = true, $cell = true, $align = 'L');

$pdf->Ln();
##

//
switch ($payItem) {
    case '大額繳稅':
        callPayTaxTxt();
        break;
    case '本行支票':
        callCash();
        break;
    default:
        break;
}
##

//
$pdf->Output();
##
