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
//

$pdf->AddPage();

$pdf->SetFont('msungstdlight', 'B', 18);
$Header = '<span style="text-align:center;vertical-align:top">';
$Header .= '不動產買賣價金第一建經履約保證信託指示通知書';
$Header .= '</span><br>';
$Header .= '<span style="text-align:center;vertical-align:top">';
$Header .= '(保證號碼轉帳專用)';
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
	               <td>Fax：02-2506-0161</td>
	               <td>Fax：02-2751-8586/02-2752-8811</td>
	            </tr>
	            <tr>
	               <td>Tel：02-2183-5143/2183-5328
	               </td>
	               <td>Tel：02-2772-0111#888及101
	               </td>
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
    $txt  = '■保證號碼：99985-' . $data['CertifiedId_9'];
    $code = '99985';
    $pdf->writeHTMLCell(11, 0, $x, $y, $txt, $border, 1, 0, true, '', true);

    $border = array(
        'B' => array('width' => 0.05, 'color' => array(0, 0, 0)),
        'R' => array('width' => 0.1, 'color' => array(0, 0, 0)),
    );
    $x   = $pdf->getX() + 9.5; //
    $txt = '專戶帳號：104-018-1000199-9';
    $pdf->writeHTMLCell(7.5, 0, $x, $y, $txt, $border, 1, 0, true, '', true);
} else {
    //          $txt ='■保證號碼：99986-'.$data['CertifiedId_9'];
    //          $code = '99986';
    //           $pdf->writeHTMLCell(11,0, $x, $y,$txt, $border, 1, 0, true, '', true);

    //           $border = array(
    //    'B' => array('width' => 0.05, 'color' => array(0,0,0)),
    //    'R' => array('width' => 0.1, 'color' => array(0,0,0)),
    // );
    //    $x = $pdf->getX()+9.5; //
    //    $txt = '專戶帳號：126-018-0001599-9';
    //    $pdf->writeHTMLCell(7.5,0, $x, $y,$txt, $border, 1, 0, true, '', true);
    $txt  = '■保證號碼：99986-' . $data['CertifiedId_9'];
    $code = '99986';
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

$y = $pdf->getY() + $ln;

$txt = '一、	玆請貴行於接到本指示通知後，請將匯至貴行「永豐商';
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$y   = $pdf->getY() + $ln;
$txt = '業銀行受託信託財產專戶」之總金額新臺幣';
$pdf->MultiCell(9, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

$x              = $pdf->getX() - 1;
$y              = $pdf->getY() + $ln;
$data['bMoney'] = str_replace('元整', '', NumtoStr($data['bMoney']));
$txt            = '' . $data['bMoney'] . '元整。';

$pdf->MultiCell(10, 0, $txt, array('B' => array('width' => 0.01, 'color' => array(0, 0, 0))), 'L', false, 1, $x, $y, true, 0, false);

// $x = $pdf->getX()-1+4;
$y = $pdf->getY() + $ln;
if ($data['ToCertifiedFirst'] == '') {
    $txt = '匯至保證號碼：' . $code . '-' . $data['bToCertifiedId'] . '專戶之履保專戶。';
} else {
    $txt = '匯至保證號碼：' . $data['ToCertifiedFirst'] . '-' . $data['bToCertifiedId'] . '專戶之履保專戶。';
}
// $txt = '匯至保證號碼：'.$code.'-'.$data['bToCertifiedId'].'專戶之履保專戶。';
$pdf->MultiCell(11, 0, $txt, 0, 'L', false, 1, $x, $y, true, 0, false);

$x   = $pdf->getX();
$y   = $pdf->getY() + 1;
$txt = '二、總筆數：' . $data['bCount'] . ' 筆。';

$pdf->MultiCell(11.5, 0, $txt, 0, 'L', false, 1, 1.5, $y, true, 0, false);

//撐高用
$x = $pdf->getX();
$y = $pdf->getY() + 5;
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
//     $pdf->writeHTMLCell(0, 0, $x, ($y+90), $table, 0, 1, 0, true, '', true);
// die;
$pdf->Output();
