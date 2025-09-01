<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
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

$sql = "SELECT
			tBankCode,
			tAccount,
			tAccountName,
			tTxt,
			(SELECT bBank4_name FROM tBank WHERE bBank3 = SUBSTR(tBankCode,1,3) AND bBank4 ='') AS bank,
			(SELECT bBank4_name FROM tBank WHERE bBank3 = SUBSTR(tBankCode,1,3) AND bBank4 =SUBSTR(tBankCode,-4)) AS bankBranch,
			tMemo,
			tMoney
		FROM
			tBankTrans
		WHERE tExport_nu = '" . $data['bExport_nu'] . "'";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $dataTran[] = $rs->fields;
    $rs->MoveNext();
}

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

$lineHeight = 0.5;

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

$txt = '清償台新銀行房貸專用';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '1.匯出款項戶名：台新國際商業銀行受託信託財產專戶';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '2.匯出款項帳號：2068-01-0013599-7';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$tmp = explode('-', dateformate($data['bDate']));
$txt = '3.指示日期：' . $tmp[0] . "年" . $tmp[1] . "月" . $tmp[2] . "日";
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
unset($tmp);

if (count($dataTran) == 1) {
    $txt = '4.指示單編號：' . $data['bBookId'];
    $pdf->MultiCell(10, $lineHeight, $txt, 0, 'L', 0, 0, '', '', true, 0, false, true, 1, 'M');
    $txt = '保證號碼：' . $dataTran['tMemo'];
    $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

    $txt = '5.滙款銀行：' . $dataTran['bank'] . $dataTran['bankBranch']; //.'('.$dataTran['tBankCode'].")"
    $pdf->MultiCell(10, $lineHeight, $txt, 0, 'L', 0, 0, '', '', true, 0, false, true, 1, 'M');
    $txt = '滙款帳號：' . $dataTran['tAccount'];
    $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

    $txt = '6.滙款戶名：' . $dataTran['tAccountName'];
    $pdf->MultiCell(10, $lineHeight, $txt, 0, 'L', 0, 0, '', '', true, 0, false, true, 1, 'M');
    $txt = '匯款總金額：' . number_format($data['bMoney']);
    $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

    $txt = '7.匯款總筆數：' . $data['bCount']; //$transBank['main'].'/'.$transBank['branch']
    $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

    $txt = '附言：' . $dataTran['tTxt']; //$transBank['main'].'/'.$transBank['branch']
    $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
} else {
    for ($i = 0; $i < count($dataTran); $i++) {
        $txt = '4.指示單編號：' . $data['bBookId'];
        $pdf->MultiCell(10, $lineHeight, $txt, 0, 'L', 0, 0, '', '', true, 0, false, true, 1, 'M');
        $txt = '保證號碼：' . $dataTran[$i]['tMemo'];
        $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

        $txt = '5.滙款銀行：' . $dataTran[$i]['bank'] . $dataTran[$i]['bankBranch']; //.'('.$dataTran[$i]['tBankCode'].")"
        $pdf->MultiCell(10, $lineHeight, $txt, 0, 'L', 0, 0, '', '', true, 0, false, true, 1, 'M');
        $txt = '滙款帳號：' . $dataTran[$i]['tAccount'];
        $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

        $txt = '6.滙款戶名：' . $dataTran[$i]['tAccountName'];
        $pdf->MultiCell(10, $lineHeight, $txt, 0, 'L', 0, 0, '', '', true, 0, false, true, 1, 'M');
        $txt = '匯款金額：' . number_format($dataTran[$i]['tMoney']);
        $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

        $txt = '7.匯款總筆數：' . $data['bCount']; //$transBank['main'].'/'.$transBank['branch']
        $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

        $txt = '附言：' . $dataTran[$i]['tTxt']; //$transBank['main'].'/'.$transBank['branch']
        $pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

        if (count($dataTran) > 1 && $i != (count($dataTran) - 1)) {
            $border = array(
                'T' => array('width' => 0.01, 'color' => array(0, 0, 0)),
            );
            // $txt = '-----------------------------------------------------';
            $pdf->MultiCell('', 0.05, '', $border, 'L', 0, 1, '', '', true, 0, false, true, 0.05, 'M');
        }
    }
}

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
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '台　新　國　際　商　業　銀　行';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '契約編號：' . $data['bContractID'];
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '委託人姓名：第一建築經理股份有限公司';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '統一編號：53549920';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '連絡電話：' . $company['tel'];
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

if ($y > 21) {
    $pdf->AddPage();
    $y       = $pdf->getY();
    $pageAdd = 1;
}

$pdf->Ln(2);
$txt = '主管：                            覆核：                       經辦：';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
//帳號                             戶名：
//writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

if ($pageAdd == 1) {
    $y = ($pdf->getY() + 1);
}
$border = array(
    'T' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.01, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(7, 6, $x, $y, '', $border, 2, 0, true, '', true);

// $x = $pdf->getX();
// $y = $pdf->getY();
$pdf->SetFont('msungstdlight', 'B', 9);
$txt = '委託人簽章處：';
$pdf->MultiCell('', $lineHeight, $txt, 0, 'C', 0, 1, $x, $y, true, 0, false, true, 1, 'M');
$img_file = 'images/stamp.png';
$pdf->Image($img_file, ($x + 0.2), ($y + 1), 6, 3.43);

$pdf->Output();
