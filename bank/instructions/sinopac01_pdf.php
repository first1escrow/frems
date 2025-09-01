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

$sql = "SELECT
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '" . $bId . "'";
$rs = $conn->Execute($sql);

$data = $rs->fields;

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'cm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetMargins('2.4', '1.8', '2.4');
$pdf->SetCreator(PDF_CREATOR);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//

$pdf->AddPage();

$pdf->SetFont('msungstdlight', 'B', 18);
$Header = '<span style="text-align:center;vertical-align:top">';
$Header .= '不動產買賣價金第一建經履約保證信託';
$Header .= '</span><br>';
$Header .= '<span style="text-align:center;vertical-align:top">';
$Header .= '指示通知書';
$Header .= '</span>';

$pdf->writeHTML($Header, true, 0, true, true);
$pdf->SetFont('msungstdlight', 'B', 12);

$txt = '<table width="100%">
            <tr>
                <td width="60%">致：永豐銀行信託處</td>
                <td width="40%">自：第一建築經理股份有限公司</td>
            </tr>
            <tr>
                <td><u>信託處作業經辦</u></td>
                <td><u>蕭家津/吳佩琦</u></td>
            </tr>
            <tr>
                <td>Fax：02-2506-0161</td>
                <td></td>
            </tr>
            <tr>
                <td>Tel：02-2183-5123/2183-5332
                </td>
                <td>Tel：' . $company['tel'] . '#888及101
                </td>
            </tr>
        </table>';

$pdf->writeHTMLCell(0, 0, '', '', $txt, 0, 1, 0, true, '', true);

$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->Ln(1);
$x   = $pdf->getX() - 1;
$y   = $pdf->getY();
$y2  = $pdf->getY();
$tmp = explode('-', dateformate($data['bDate']));

$txt = '指示日期：' . $tmp[0] . "年" . $tmp[1] . "月" . $tmp[2] . "日";

$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(11, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
);

$x   = $pdf->getX() + 9.5;
$txt = '指示單編號：' . $data['bBookId'];
$pdf->writeHTMLCell(7.5, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

$x = $pdf->getX() - 1;
$y = $pdf->getY();

if ($data['bBank'] == 4) {
    $txt  = '■專戶帳號：104-018-1000199-9';
    $txt2 = '保證號碼：99985';
} else {
    $txt  = '■專戶帳號：126-018-0001599-9';
    $txt2 = '保證號碼：99986';
}

$border = array(
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(11, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

$border = array(
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);
$x = $pdf->getX() + 9.5; //

$pdf->writeHTMLCell(7.5, 0, $x, $y, $txt2, $border, 1, 0, true, '', true);

################
$x = $pdf->getX();
$y = $pdf->getY();

$y3 = $y; //畫線用

$pdf->SetFont('msungstdlight', 'B', 14);
$txt = '指示內容：';
$pdf->writeHTMLCell(11, 0, ($x - 1), ($y + 0.1), $txt, 0, 1, 0, true, '', true);

$x = $pdf->getX() + 11.5; //
$pdf->writeHTMLCell(7.5, 0, $x, $y, '', 0, 2, 0, true, '', true);

$pdf->SetFont('msungstdlight', 'B', 12);

$y = $pdf->getY();

$txt = '一、玆請貴行於接到本指示通知後，於貴行';
$pdf->MultiCell(11.5, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$x   = $pdf->getX();
$y   = $pdf->getY();
$txt = '「永豐商業銀行受託信託財產專戶」中支付相關款項，';
$pdf->MultiCell(11.5, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$x   = $pdf->getX() - 1;
$y   = $pdf->getY();
$txt = '總金額新台幣' . str_replace('元整', '', NumtoStr($data['bMoney'])) . '元整';

$txt .= '，相關支付明細如本公司於永豐信託網上傳資料。';
$pdf->MultiCell(10.5, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$y   = $pdf->getY() + 5;
$txt = '二、總筆數：' . $data['bCount'] . ' 筆。';

$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

//撐高用
$x = $pdf->getX();
$y = $pdf->getY() + 1;
$pdf->MultiCell(11, 0, '', 0, 'L', false, 1, 1, $y, true, 0, false);

$y  = $pdf->getY();
$y4 = $y; //畫線用
$y  = $y - 6;
$x  = $pdf->getX() + 10;

$txt = '有權簽章人簽章：';
$pdf->writeHTMLCell(7.5, 0, $x, $y, $txt, 0, 2, 0, true, '', true);

$img_file = 'images/stamp.png';
$pdf->Image($img_file, ($x - 0.2), ($y + 1), 6, 3.43);

$y = $pdf->getY() + 4;

$border = array(
    'B' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(5, 0, $x, $y, '', $border, 2, 0, true, '', true);

//特殊線條
$y      = $pdf->getY();
$h1     = $y4 - $y3;
$border = array(
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(11, $h1, 1.4, $y3, '', $border, 2, 0, true, '', true);

//中間雙線
$h = $y4 - $y2;

$border = array(
    'R' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);
$pdf->writeHTMLCell(0.1, $h, 11.8, $y2, '', $border, 2, 0, true, '', true);

$pdf->writeHTMLCell(0.1, $h, 11.85, $y2, '', $border, 2, 0, true, '', true);
##

$border = array(
    'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(8.4, $h1, 11, $y3, '', $border, 2, 0, true, '', true);

################################
$x      = 1.5;
$y      = $pdf->getY() + 1;
$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$txt = '永豐銀行執行狀況<font size="10px">（以下由永豐銀行填寫）</font>：';
$pdf->writeHTMLCell(18, 5, $x, $y, $txt, $border, 2, 0, true, '', true);

$pdf->Output();
