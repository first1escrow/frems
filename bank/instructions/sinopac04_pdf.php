<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

$_POST = escapeStr($_POST);
$bId   = $_POST['id'];
// $bId = 38 ;

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

$data                  = $rs->fields;
$data['CertifiedId_9'] = substr($data['bCertifiedId'], 5);
$data['bReBank']       = str_replace('分行', '', $data['bReBank']);

##########################################################
//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='" . $data['bId'] . "' AND bDel = 0 ORDER BY bId ASC";
$rs  = $conn->Execute($sql);

$data_detail = $rs->fields;
############################################
//人員傳真號碼
$sql = "SELECT pFaxNum FROM tPeopleInfo WHERE pId ='" . $data['bCreatorId'] . "'";
$rs  = $conn->Execute($sql);
$Fax = $rs->fields['pFaxNum'];
######################################################################

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
$Header .= '<u>第一建築經理(股)公司信託指示單</u>'; //(代收票據領回/代收票據延期提示/退票領回專用)
$Header .= '</span><br>';

$Header .= '<span style="text-align:center;vertical-align:top">';
$Header .= '<u>(代收票據領回/代收票據延期提示/退票領回專用)</u>'; //(代收票據領回/代收票據延期提示/退票領回專用)
$Header .= '</span><br>';

$Header .= '<span style="text-align:center;vertical-align:top">';
$Header .= '指示通知書';
$Header .= '</span>';

$pdf->writeHTML($Header, true, 0, true, true);
$pdf->SetFont('msungstdlight', 'B', 12);
$day = $data['bCreatTime'];
if ($day < '2017-06-09') { //從這天開始換人
    $txt = '<table width="100%">
	            <tr>
	               <td width="60%">致：永豐銀行信託部</td>
	               <td width="40%">自：第一建經</td>
	            </tr>
	            <tr>
	               <td><u>莊文怡/廖心慧</u></td>
	               <td><u>蕭家津/周展雪</u></td>
	            </tr>
	            <tr>
	               <td>Fax：02-2506-0161</td>
	               <td></td>
	            </tr>
	            <tr>
	               <td>Tel：02-2183-5123/2183-5169
	               </td>
	               <td>Tel：' . $company['tel'] . '#888及117
	               </td>
	            </tr>
	      </table>';
} else {
    if ($data['bCreatTime'] > '2018-06-04 13:00:00') { //
        $txt = '<table width="100%">
	            <tr>
	               <td width="60%">致：永豐銀行信託部</td>
	               <td width="40%">自：第一建經</td>
	            </tr>
	            <tr>
	               <td><u>許晉嘉/蕭育伶/葉尚恬/游家凡/林姿秀</u></td>
	               <td><u>蕭家津/吳佩琦</u></td>
	            </tr>
	            <tr>
	               <td>Fax：02-2506-0161</td>
	               <td>Fax：02-2751-8586/02-2752-8811</td>
	            </tr>
	            <tr>
	               <td>Tel：02-2183-5143/2183-5123
	               </td>
	               <td>Tel：02-2772-0111#888及101
	               </td>
	            </tr>
	      </table>';
    } else {
        $txt = '<table width="100%">
	            <tr>
	               <td width="60%">致：永豐銀行信託部</td>
	               <td width="40%">自：第一建經</td>
	            </tr>
	            <tr>
	               <td><u>許晉嘉/富保琴/蕭育伶/廖心慧/林姿秀</u></td>
	               <td><u>蕭家津/吳佩琦</u></td>
	            </tr>
	            <tr>
	               <td>Fax：02-2506-0161</td>
	               <td>Fax：02-2751-8586/02-2752-8811</td>
	            </tr>
	            <tr>
	               <td>Tel：02-2183-5143/2183-5123
	               </td>
	               <td>Tel：02-2772-0111#888及101
	               </td>
	            </tr>
	      </table>';
    }
}

$pdf->writeHTMLCell(0, 0, '', '', $txt, 0, 1, 0, true, '', true);
$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->Ln(1);
$x   = $pdf->getX() - 1;
$y   = $pdf->getY();
$y2  = $y;
$tmp = explode('-', dateformate($data['bDate']));

$txt = '指示日期：' . $tmp[0] . "年" . $tmp[1] . "月" . $tmp[2] . "日";

$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    // 'R' => array('width' => 0.1, 'color' => array(0,0,0)),
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(11, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    // 'L' => array('width' => 0.1, 'color' => array(0,0,0)),
);

$x   = $pdf->getX() + 9.5;
$txt = '指示單編號：' . $data['bBookId'];
$pdf->writeHTMLCell(7.5, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

$x = $pdf->getX() - 1;
$y = $pdf->getY();

$txt = '■保證號碼：' . substr($data['bCertifiedId'], 0, 5) . '-' . substr($data['bCertifiedId'], 5);

$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    // 'R' => array('width' => 0.1, 'color' => array(0,0,0)),
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);
$pdf->writeHTMLCell(11, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

$x      = $pdf->getX() + 9.5; //
$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    // 'L' => array('width' => 0.1, 'color' => array(0,0,0)),
);

$txt = '';
if ($data['bBank'] == 4) {
    $txt = '專戶帳號：104-018-1000199-9';
} else if ($data['bBank'] == 6) {
    $txt = '專戶帳號：126-018-0001599-9';
}

$pdf->writeHTMLCell(7.5, 0, $x, $y, $txt, $border, 1, 0, true, '', true);
################

$x = $pdf->getX() - 1;
$y = $pdf->getY();

$y3 = $y; //畫線用
// $y += 1 ;
$pdf->SetFont('msungstdlight', 'B', 14);
$txt = '指示內容：';
$pdf->writeHTMLCell(11, 0, $x, ($y + 0.1), $txt, 0, 1, 0, true, '', true);

$x = $pdf->getX() + 9.5; //
$pdf->writeHTMLCell(7.5, 0, $x, $y, '', 0, 2, 0, true, '', true);

$pdf->SetFont('msungstdlight', '', 12);
// $x = $pdf->getX();
$y = $pdf->getY();

$txt = '一、本指示單領取票據總金額：'; //
$pdf->writeHTMLCell(11, 0, 1.5, $pdf->GetY(), $txt, 0, 1, 0, true, '', true);
$y   = $pdf->GetY();
$txt = preg_replace("/元整/isu", '', NumtoStr($data['bMoney']));
$txt = '新臺幣<u>' . $txt . '元整</u>。';
$pdf->writeHTMLCell(11, 0, 2.5, $pdf->GetY() + 0.1, $txt, 0, 1, 0, true, '', true);

$txt = '■';

if ($data['bCategory'] == 8) {
    $txt .= '代收票據領回：';
} else if ($data['bCategory'] == 7) {
    $txt .= '退票領回：';
} else if ($data['bCategory'] == 9) {
    $txt .= '代收票據延期提示：';
}

$pdf->writeHTMLCell(11, 0, 2.5, $pdf->GetY() + 0.1, $txt, 0, 1, 0, true, '', true);

$txt = '支票號碼：<u>' . $data_detail['bTicketNo'] . '</u>';
$pdf->writeHTMLCell(11, 0, 3, $pdf->GetY() + 0.1, $txt, 0, 1, 0, true, '', true);

$txt = '金額：<u>' . number_format($data_detail['bMoney']) . '</u>元';
$pdf->writeHTMLCell(11, 0, 3, $pdf->GetY() + 0.1, $txt, 0, 1, 0, true, '', true);

if ($data['bCategory'] == 9) {
    $tmpD         = explode('-', $data_detail['bTicketDelay']);
    $bTicketDelay = $tmpD[0] . '年' . $tmpD[1] . "月" . $tmpD[2] . "日";
    $txt          = '延後支票發票日至<u>' . $bTicketDelay . '</u>';
    $pdf->writeHTMLCell(11, 0, 3, $pdf->GetY() + 0.1, $txt, 0, 1, 0, true, '', true);
    unset($bTicketDelay, $tmpD);
}

$txt = '二、領票人：' . $data['breName'];
$pdf->writeHTMLCell(11, 0, 1.5, $pdf->GetY(), $txt, 0, 1, 0, true, '', true);

$txt = '身分證字號：<u>' . $data['breIdentifyId'] . '</u>';
$pdf->writeHTMLCell(11, 0, 2.25, $pdf->GetY() + 0.1, $txt, 0, 1, 0, true, '', true);

$txt = '三、請依上列指示事項通知　貴行<u>' . $data['bReBank'] . '</u>分行';
$pdf->writeHTMLCell(11, 0, 1.5, $pdf->getY() + 0.1, $txt, 0, 1, 0, true, '', true);

if ($data['bSpNote1'] != '') {
    $txt = '四、' . nl2br($data['bSpNote1']);
    $pdf->writeHTMLCell(11, 0, 1.5, $pdf->getY() + 0.1, $txt, 0, 1, 0, true, '', true);
}

if ($data['bSpNote2'] != '') {
    $txt = '五、' . nl2br($data['bSpNote2']);
    $pdf->writeHTMLCell(11, 0, 1.5, $pdf->getY() + 0.1, $txt, 0, 1, 0, true, '', true);
}
//撐高用
$x = $pdf->getX() - 1;
$y = $pdf->getY() + 5;
$pdf->MultiCell(11, 0, '', 0, 'L', false, 1, 5, $y, true, 0, false);

$y  = $pdf->getY();
$y4 = $y; //畫線用
$y  = $y - 10;
$x  = $pdf->getX() + 9.5;

$pdf->SetFont('msungstdlight', 'B', 14);
$txt = '有權簽章人簽章：';
$pdf->Text(12, $y, $txt);

$img_file = 'images/stamp.png';
$pdf->Image($img_file, ($x + 0.1), ($y + 1), 6, 3.43);

$pdf->SetFont('msungstdlight', '', 12);
$pdf->Line(12, 17, 19, 17);
// $pdf->writeHTMLCell(7.5,0, $x, $y,$txt, 0, 2, 0, true, '', true);

$y = $pdf->getY();

// $x = $pdf->getX()+10;
$y      = $pdf->getY();
$h      = $y4 - $y3;
$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(11, $h, 1.4, $y3, '', $border, 2, 0, true, '', true);

$border = array(
    'T' => array('width' => 0.05, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(18, $h1, 1.4, ($y3 + 0.1), '', $border, 2, 0, true, '', true);

//中間單線
$h = $y3 - $y2;

$border = array(
    'R' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);
$pdf->writeHTMLCell(0.1, $h, 11.8, $y2, '', $border, 2, 0, true, '', true);

//中間雙線
$h = $y4 - $y3;

$border = array(
    'R' => array('width' => 0.01, 'color' => array(0, 0, 0)),
);
$pdf->writeHTMLCell(0.1, $h, 11.8, $y3, '', $border, 2, 0, true, '', true);

$pdf->writeHTMLCell(0.1, $h, 11.85, $y3, '', $border, 2, 0, true, '', true);
##

$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(8.4, $h, 11, $y3, '', $border, 2, 0, true, '', true);

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
//     $pdf->writeHTMLCell(0, 0, $x, ($y+100), $table, 0, 1, 0, true, '', true);
// die;

$file_name = date('Ymd') . '_' . str_pad($data['bCategory'], '2', '0', STR_PAD_LEFT);

$pdf->Output();
