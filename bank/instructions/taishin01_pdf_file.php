<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'cm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetMargins('2.4', '1.8', '2.4');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->AddPage();

$pdf->SetFont('msungstdlight', 'B', 14);
$Header = '<span style="text-align:center;vertical-align:top">';
$Header .= '第一建築經理股份有限公司';
$Header .= '</span><br>';
$Header .= '<span style="text-align:center;vertical-align:top">';
$Header .= '信託財產管理及運用指示書';
$Header .= '</span>';

$day = '2020-06-09 10:30:00'; //換人

if ($data['bCreatTime'] > $day) {
    $pdf->writeHTML($Header, true, 0, true, true);
    $pdf->SetFont('msungstdlight', 'B', 10);
    $txt = "聯絡人：陳雪莉\r\n電  話：02-55761609\r\n傳  真：02-37076944";
    $pdf->MultiCell(3.5, '', $txt, 0, 'L', 0, 1, 15, 2.5, true);
} else {
    $pdf->writeHTML($Header, true, 0, true, true);
    $pdf->SetFont('msungstdlight', 'B', 10);
    $txt = "聯絡人：邱新怡\r\n電  話：02-55761078\r\n傳  真：02-37076944";
    $pdf->MultiCell(3.5, '', $txt, 0, 'L', 0, 1, 15, 2.5, true);
}

$pdf->Ln(0.1);
$pdf->SetFont('msungstdlight', 'B', 12);
$txt = '　　本交易指示書為委託人指示受託人(台新國際商業銀行)行使下述交易付款之書面指示，相關約定係依雙方簽訂之信託契約辦理。';
$pdf->MultiCell('', '', $txt, 0, 'L', 0, 1, '', '', true);
$pdf->Ln(0.5);

$pdf->SetFont('msungstdlight', 'B', 14);

$txt = '指　　示　　事　　由';

$border = array(
    'B' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'T' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);

$pdf->MultiCell('', 1.2, $txt, $border, 'C', 0, 1, '', '', true, 0, false, true, 1.2, 'M');

$y = $pdf->getY();

$txt = '1.匯出款項戶名：台新國際商業銀行受託信託財產專戶';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '2.匯出款項帳號：2068-01-0013599-7';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$tmp = explode('-', dateformate($data['bDate']));
$txt = '3.指示日期：' . $tmp[0] . "年" . $tmp[1] . "月" . $tmp[2] . "日";
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
unset($tmp);

$txt = '4.指示單編號：' . $data['bBookId'];
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '5.匯款總金額：' . number_format($data['bMoney']);
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '6.匯款總筆數：' . $data['bCount']; //$transBank['main'].'/'.$transBank['branch']
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

##
$y2 = $pdf->getY();
$h  = $y2 - $y;

$border = array(
    'T' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell('', $h, '', $y, '', $border, 2, 0, true, '', true);
$pdf->Ln(0.5);
$x = $pdf->getX() + 9;
$y = $pdf->getY();

$pdf->SetFont('msungstdlight', 'B', 12);
$txt = '此　　致';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '台　新　國　際　商　業　銀　行';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '契約編號：' . $data['bContractID'];
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '委託人姓名：第一建築經理股份有限公司';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '統一編號：53549920';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '連絡電話：' . $company['tel'];
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$pdf->Ln(2);
$txt = '主管：                            覆核：                       經辦：';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
//帳號                             戶名：

$border = array(
    'T' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(7, 7, $x, $y, '', $border, 2, 0, true, '', true);

// $x = $pdf->getX();
// $y = $pdf->getY();
$pdf->SetFont('msungstdlight', 'B', 9);
$txt = '委託人簽章處：';
$pdf->MultiCell('', '1', $txt, 0, 'C', 0, 1, $x, $y, true, 0, false, true, 1, 'M');
$img_file = dirname(__FILE__) . '/images/stamp.png';
$pdf->Image($img_file, ($x + 0.2), ($y + 1), 6, 3.43);

$pdf->Output();
