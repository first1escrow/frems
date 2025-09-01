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

$data                  = $rs->fields;
$data['CertifiedId_9'] = substr($data['bCertifiedId'], 5);
$data['bReBank']       = str_replace('分行', '', $data['bReBank']);

##########################################################
//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='" . $data['bId'] . "' AND bDel = 0 ORDER BY bId ASC";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $data_detail[] = $rs->fields;
    $rs->MoveNext();
}
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

$pdf->SetFont('msungstdlight', 'B', 18);
$Header = '<span style="text-align:center;vertical-align:top">';
$Header .= '不動產買賣價金第一建經履約保證信託指示通知書';
$Header .= '</span><br>';
$Header .= '<span style="text-align:center;vertical-align:top">(';
if ($data['bCategory'] == 3) { //3開票4繳稅5臨櫃
    $Header .= '開立票據';
} elseif ($data['bCategory'] == 4) {
    $Header .= '繳交稅款';
} elseif ($data['bCategory'] == 5) {
    $Header .= '臨櫃現金取款';
}
$Header .= '專用)';
$Header .= '</span>';

$pdf->writeHTML($Header, true, 0, true, true);
$pdf->SetFont('msungstdlight', 'B', 12);

$ln  = 0.3; //高
$day = '2017-08-31 17:00:00'; //永豐開始用新電話號碼

if ($data['bCreatTime'] > $day) {
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
	               <td></td>
	            </tr>
	            <tr>
	               <td>Tel：02-2183-5143/2183-5123
	               </td>
	               <td>Tel：' . $company['tel'] . '#888及101
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
	           <td>Fax：02-256-0161</td>
	           <td>Fax：02-2751-8586/02-2752-8811</td>
	        </tr>
	            <tr>
	               <td>Tel：02-2183-5143/2183-5328</td>
	               <td>Tel：02-2772-0111#888及101</td>
	            </tr>
	            <tr>
	            	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;02-2183-5169/2183-5334</td>
	            	<td></td>
	            </tr>
		</table>';
}

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
    // 'R' => array('width' => 0.1, 'color' => array(0,0,0)),
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

$border = array(
    'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

if ($data['bBank'] == 4) {
    $txt = '■保證號碼：99985-' . $data['CertifiedId_9'];

    $pdf->writeHTMLCell(11, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

    $border = array(
        'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
        'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    );
    $x   = $pdf->getX() + 9.5; //
    $txt = '專戶帳號：104-018-1000199-9';
    $pdf->writeHTMLCell(7.5, 0, $x, $y, $txt, $border, 1, 0, true, '', true);
} else {
    $txt = '■保證號碼：99986-' . $data['CertifiedId_9'];

    $pdf->writeHTMLCell(11, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

    $border = array(
        'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
        'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    );
    $x   = $pdf->getX() + 9.5; //
    $txt = '專戶帳號：126-018-0001599-9';
    $pdf->writeHTMLCell(7.5, 0, $x, $y, $txt, $border, 1, 0, true, '', true);
}

################
$x = $pdf->getX();
$y = $pdf->getY();

$y3 = $y; //畫線用

$pdf->SetFont('msungstdlight', 'B', 14);
$txt = '指示內容：';
$pdf->writeHTMLCell(11, 0.5, ($x - 1), ($y + 0.1), $txt, 0, 1, 0, true, '', true);

$x = $pdf->getX() + 9; //
$pdf->writeHTMLCell(7.5, 0, $x, $y, '', 0, 2, 0, true, '', true);

$pdf->SetFont('msungstdlight', 'B', 12);

$y = $pdf->getY();

$txt = '一、本指示單取款總金額：';
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$x   = $pdf->getX();
$y   = $pdf->getY() + $ln;
$txt = '新臺幣' . NumtoStr($data['bMoney']) . '。';
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 2.4, $y, true, 0, false);

if ($data['bCategory'] == 3) {
    for ($i = 0; $i < count($data_detail); $i++) { //3開票4繳稅5領現
        //0:禁止 1:不禁止
        if ($data_detail[$i]['bStop'] == 1) {
            $data_detail[$i]['bStop'] = '不禁止';
        } else {
            $data_detail[$i]['bStop'] = '禁止';
        }
        if ($data_detail[$i]['bName'] != '' && $data_detail[$i]['bMoney'] != 0) {

            $txt3 = "■ 開立本行支票，抬頭：" . $data_detail[$i]['bName'] . "，" . $data_detail[$i]['bStop'] . "背書轉";

            $y = $pdf->getY() + $ln;
            $pdf->MultiCell(9, 0, $txt3, 0, 'L', false, 1, 2.4, $y, true, 0, false);

            $txt3 = "讓，金額：" . number_format($data_detail[$i]['bMoney']) . "元。";
            $y    = $pdf->getY() + $ln;
            $pdf->MultiCell(9, 0, $txt3, 0, 'L', false, 1, 3, $y, true, 0, false);
        }
    }
}

if ($data['bCategory'] == 4) {
    $txt4 = "■ 繳交稅款金額：" . number_format($data_detail[0]['bMoney']) . "元。";
    $x    = $pdf->getX();
    $y    = $pdf->getY() + $ln;
    $pdf->MultiCell(8.6, 0, $txt4, 0, 'L', false, 1, 2.4, $y, true, 0, false);
}

if ($data['bCategory'] == 5) {
    $txt5 = "■ 臨櫃現金取款金額：" . number_format($data_detail[0]['bMoney']) . "元。";
    $x    = $pdf->getX();
    $y    = $pdf->getY() + $ln;
    $pdf->MultiCell(8.6, 0, $txt5, 0, 'L', false, 1, 2.4, $y, true, 0, false);
}

$txt = '二、領票人/繳款人/取款人：' . $data['breName'];
$x   = $pdf->getX();
$y   = $pdf->getY() + 1;
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$txt = '身分證字號：' . $data['breIdentifyId'];
$x   = $pdf->getX();
$y   = $pdf->getY() + $ln;
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 2.4, $y, true, 0, false);

$txt = '三、請依上列指示事項通知 貴行' . $data['bReBank'] . '分行';
$x   = $pdf->getX();
$y   = $pdf->getY() + 1;
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$txt = '四、開立本行支票/臨櫃領現/臨櫃繳稅取款交易，';
$x   = $pdf->getX();
$y   = $pdf->getY() + 1;

$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$txt = '請協助回傳簽收單至' . $Fax . '。';
$x   = $pdf->getX();
$y   = $pdf->getY() + $ln;
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 2.4, $y, true, 0, false);

if ($data['bSpNote1'] != '') {
    $txt = '五、' . $data['bSpNote1'];
    $x   = $pdf->getX();
    $y   = $pdf->getY() + 1;

    $pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);
}

if ($data['bSpNote2'] != '') {
    $txt = '六、' . $data['bSpNote2'];
    $x   = $pdf->getX();
    $y   = $pdf->getY() + 1;

    $pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);
}

//撐高用
$x = $pdf->getX();
$y = $pdf->getY();
$pdf->MultiCell(11, 0, '', 0, 'L', false, 1, 1, $y, true, 0, false);

$y  = $pdf->getY();
$y4 = $y; //畫線用
$y  = $y - 7;
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
$y = $pdf->getY();

$h1     = $y4 - $y3;
$border = array(
    'T' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'L' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    'B' => array('width' => 0.1, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(11, $h1, 1.4, $y3, '', $border, 2, 0, true, '', true);

$border = array(
    'T' => array('width' => 0.05, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(11, $h1, 1.4, ($y3 + 0.1), '', $border, 2, 0, true, '', true);

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

$pdf->writeHTMLCell(8.4, $h1, 11, $y3, '', $border, 2, 0, true, '', true);

$border = array(
    'T' => array('width' => 0.05, 'color' => array(0, 0, 0)),
);

$pdf->writeHTMLCell(8.4, $h, 11, ($y3 + 0.1), '', $border, 2, 0, true, '', true);

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
