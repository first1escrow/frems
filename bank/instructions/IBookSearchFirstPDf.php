<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

$_POST = escapeStr($_POST);

$sd = explode('-', $_POST['StartDate']); //startdate
$ed = explode('-', $_POST['EndDate']);   //enddate
$td = explode('-', $_POST['Date']);      //titledate

$totalPage = 1;
$StartDate = ($sd[0] + 1911) . "-" . $sd[1] . "-" . $sd[2];
$EndDate   = ($ed[0] + 1911) . "-" . $ed[1] . "-" . $ed[2];
$bank      = $_POST['bank'];

$sql = "SELECT
			bDate,
			bBookId,
			bMoney,
			bCategory,
			(SELECT cName FROM tCategoryBook WHERE cId = bCategory) AS CatName
		FROM
			tBankTrankBook
		WHERE
			bDel = 0 AND bBank = '" . $bank . "' AND bDate >='" . $StartDate . "' AND bDate <='" . $EndDate . "' ORDER BY bDate,bBookId ASC";
$rs = $conn->Execute($sql);

while (! $rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}

$pdf = new TCPDF('P', 'cm', 'A4', true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFontSubsetting(true);

$pdf->SetLeftMargin(1.5);
$pdf->SetRightMargin(1.5);
$pdf->SetRightMargin(1.5);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetY(2.3);

$pdf->SetFont('msungstdlight', 'B', 16);
$Header = '<span style="text-align:center;vertical-align:top">';
$Header .= '第一建經履約保證價金信託每月撥款核對表';

$Header .= '</span><br>';
$pdf->writeHTML($Header, true, 0, true, true);

$pdf->SetFont('msungstdlight', 'B', 14);

if ($bank == 1 or $bank == 7) {
    $Header = '<span style="text-align:center;vertical-align:top">';
    $Header .= '戶名：第一商業銀行受託信託財產專戶-第一建經價金履約保證';
    $Header .= '</span><br>';

    $pdf->writeHTML($Header, true, 0, true, true);

} elseif ($bank == 5) {
    $Header = '<span style="text-align:left;vertical-align:top;padding-left:20px;">';
    $Header .= '戶名：台新國際商業銀行受託信託財產專戶';
    $Header .= '</span><br>';

    $x = $pdf->getX() + 1;
    $y = $pdf->getY();

    $pdf->writeHTMLCell(100, 0, $x, $y, $Header, '', 1, 0, true, '', true);
}

$x = $pdf->getX() + 1;
$y = $pdf->getY();

$Header = '<span style="text-align:left;vertical-align:top">';
if ($bank == 1) {
    $Header .= '帳號：271-10-352556';
} elseif ($bank == 7) {
    $Header .= '帳號：144-10-531988';
}elseif ($bank == 5) {
    $Header .= '帳號：2068-01-0013599-7';
}

$Header .= '</span><br>';
$pdf->writeHTMLCell(100, 0, $x, $y, $Header, '', 1, 0, true, '', true);

$Header = null;unset($Header);

$border = [
    'T' => ['width' => 0.05, 'color' => [0, 0, 0]],
    'B' => ['width' => 0.05, 'color' => [0, 0, 0]],
    'L' => ['width' => 0.05, 'color' => [0, 0, 0]],
    'R' => ['width' => 0.05, 'color' => [0, 0, 0]],
];

$ox = $pdf->getX() + 1; //預留位置
$oy = $pdf->getY();     //預留位置

$x = $pdf->getX() + 0.8;
$y = $pdf->getY() + 0.8;

$txt = '<span style="text-align:center;vertical-align:middle;line-height:40px;">傳真指示日期</span>';
$pdf->writeHTMLCell(4, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

$x = $pdf->getX() + 4.8;

$txt = '<span style="text-align:center;vertical-align:middle;line-height:40px;">指示單編號</span>';
$pdf->writeHTMLCell(3, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

$x = $pdf->getX() + 7.8;

$txt = '<span style="text-align:center;vertical-align:middle;line-height:40px;">指示單類別</span>';
$pdf->writeHTMLCell(4, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

$x = $pdf->getX() + 11.8;

$txt = '<span style="text-align:center;vertical-align:middle;line-height:40px;">匯款支付總金額(新臺幣)</span>';
$pdf->writeHTMLCell(6.5, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

$border = [
    'T' => ['width' => 0.05, 'color' => [0, 0, 0]],
    'B' => ['width' => 0.05, 'color' => [0, 0, 0]],
    'L' => ['width' => 0.05, 'color' => [0, 0, 0]],
    'R' => ['width' => 0.05, 'color' => [0, 0, 0]],
];

foreach ($list as $k => $v) {
    $x = $pdf->getX() + 0.8;
    $y = $pdf->getY();

    if ($y > 26) {
        $pdf->Text(10.3, 28.5, $pdf->getAliasNumPage() . ' / ' . $pdf->getAliasNbPages());
        $pdf->AddPage();
        $totalPage++;
        $y = $pdf->getY();
    }

    $txt = '<span style="text-align:center;vertical-align:middle;line-height:40px;">' . trim($v['bDate']) . '</span>';
    $pdf->writeHTMLCell(4, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

    $x = $pdf->getX() + 4.8;

    $txt = '<span style="text-align:center;vertical-align:middle;line-height:40px;">' . trim($v['bBookId']) . '</span>';
    $pdf->writeHTMLCell(3, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

    $x = $pdf->getX() + 7.8;

    $txt = '<span style="text-align:center;vertical-align:middle;line-height:40px;">' . trim($v['CatName']) . '</span>';
    $pdf->writeHTMLCell(4, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

    $x = $pdf->getX() + 11.8;

    $txt = '<span style="text-align:right;vertical-align:middle;line-height:40px;">' . number_format($v['bMoney']) . '</span>';
    $pdf->writeHTMLCell(6.5, 0, $x, $y, $txt, $border, 1, 0, true, '', false);

}

//下面長度11
if (($y + 11) > 26.5) {
    $pdf->Text(10.3, 28.5, $pdf->getAliasNumPage() . ' / ' . $pdf->getAliasNbPages());
    $pdf->AddPage();
    $totalPage++;
}

$x = $pdf->getX() + 0.8;
$y = $pdf->getY();

$txt = '<span style="text-align:left;vertical-align:top;line-height:40px;">';
$txt .= '上述資料如有不符，以原始傳真資料為準。';
$txt .= '</span><br>';
$pdf->writeHTMLCell(0, 0, $x, $y, $txt, '', 1, 0, true, '', true);

$y = $pdf->getY();

$txt = '<span style="text-align:left;vertical-align:top;line-height:40px;">';
$txt .= '致';
$txt .= '</span><br>';
$pdf->writeHTMLCell(0, 0, $x, $y, $txt, '', 1, 0, true, '', true);

$y = $pdf->getY();

$txt = '<span style="text-align:left;vertical-align:top;line-height:40px;">';
if ($bank == 1) {
    $txt .= '第一商業銀行&nbsp;&nbsp;信託處';
} elseif ($bank == 5) {
    $txt .= '台新國際商業銀行&nbsp;&nbsp;法金信託部';
}

$txt .= '</span><br>';
$pdf->writeHTMLCell(0, 0, ($x + 1.8), $y, $txt, '', 1, 0, true, '', true);

$y = $pdf->getY();

$txt = '<span style="text-align:left;vertical-align:top;line-height:40px;">';
$txt .= '委託人：第一建築經理股份有限公司';
$txt .= '</span><br>';
$pdf->writeHTMLCell(0, 0, $x, $y, $txt, '', 1, 0, true, '', true);

$y = $pdf->getY();

$txt = '<span style="text-align:left;vertical-align:top;line-height:40px;">';
$txt .= '有權簽章人：';
$txt .= '</span><br>';
$pdf->writeHTMLCell(0, 0, $x, $y, $txt, '', 1, 0, true, '', true);

$y = $pdf->getY() + 5;
$pdf->Line($x, $y, ($x + 17), $y, ['width' => 0.11]);

$y = $pdf->getY() + 5;

$txt = '<span style="text-align:left;vertical-align:top;line-height:40px;">';
$txt .= '指示日期：&nbsp;&nbsp;' . $td[0] . '&nbsp;&nbsp;年&nbsp;&nbsp;' . $td[1] . '&nbsp;&nbsp;月&nbsp;&nbsp;' . $td[2] . '&nbsp;&nbsp;日';
$txt .= '</span><br>';
$pdf->writeHTMLCell(0, 0, $x, $y, $txt, '', 1, 0, true, '', true);
$pdf->Text(10.3, 28.5, $pdf->getAliasNumPage() . ' / ' . $pdf->getAliasNbPages());

$pdf->setPage(1);

$Header = '<span style="text-align:left;vertical-align:top">';
$Header .= '日期起迄：' . $sd[0] . '/' . $sd[1] . '/' . $sd[2] . '~' . $ed[0] . '/' . $ed[1] . '/' . $ed[2] . '，共' . $pdf->getAliasNbPages() . '頁';
$Header .= '</span><br>';
$pdf->writeHTMLCell(100, 0, $ox, $oy, $Header, '', 1, 0, true, '', true);

$pdf->Output();
