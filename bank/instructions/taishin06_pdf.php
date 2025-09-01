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

//解匯行
$transBank = array();
if (preg_match("/^\d{7}$/", $data['bObank'])) {
    //總行
    $sql               = 'SELECT * FROM tBank WHERE bBank3="' . substr($data['bObank'], 0, 3) . '" AND bBank4="";';
    $rs                = $conn->Execute($sql);
    $transBank['main'] = $rs->fields['bBank4_name'];
    ##

    //分行
    $sql                 = 'SELECT * FROM tBank WHERE bBank3="' . substr($data['bObank'], 0, 3) . '" AND bBank4="' . substr($data['bObank'], 3) . '";';
    $rs                  = $conn->Execute($sql);
    $transBank['branch'] = $rs->fields['bBank4_name'];
    ##
}
##

##########################################################
//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='" . $bId . "' AND bDel = 0";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    if ($rs->fields['bCat'] == '1') { //1:錯誤帳戶 //補通訊用
        $data_Error[] = $rs->fields;
    } elseif ($rs->fields['bCat'] == '2') { //2:正確帳戶 //補通訊用
        $data_Correct[] = $rs->fields;
    }

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

$pdf->SetFont('msungstdlight', 'B', 14);
$Header = '<span style="text-align:center;vertical-align:top">';
$Header .= '第一建築經理股份有限公司';
$Header .= '</span><br>';
$Header .= '<span style="text-align:center;vertical-align:top">';
$Header .= '信託財產管理及運用指示書';
$Header .= '</span>';

$day = '2020-06-09 10:30:00';

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

// $pdf->writeHTML($Header, true, 0, true, true);
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
// $pdf->Ln(0.5);
$y = $pdf->getY();

$txt = '匯款資料更正申請';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$tmp = explode('-', dateformate($data['bODate']));
$txt = '原匯款日期：' . $tmp[0] . "年" . $tmp[1] . "月" . $tmp[2] . "日";
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
unset($tmp);

$txt = '原指示單編號：' . $data['bOBookId'];
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '保證號碼：' . $data['CertifiedId_9'];
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$txt = '解款行：' . $transBank['main'] . '銀行' . $transBank['branch']; //$transBank['main'].'/'.$transBank['branch']
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
// $pdf->writeHTMLCell('',0, $x, $y,$txt, $border, 2.5, 0, true, '', true);
$pdf->SetFont('msungstdlight', 'B', 12);
$txt = '錯誤資料：';
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$tmp_y = $pdf->getY();
$txt   = '　　帳號' . $data['bEaccount'];
$pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$tmp_x = $pdf->getX() + 6;
$txt   = '戶名：' . $data['bEaccountName'];
$pdf->MultiCell(10, '1', $txt, 0, 'L', 0, 1, $tmp_x, $tmp_y, true, 0, false, true, 1, 'M', true);

for ($i = 0; $i < count($data_Error); $i++) {
    $tmp_y = $pdf->getY();

    $txt = '　　帳號' . $data_Error[$i]['bEaccount'];
    $pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 0.5, 'M');

    $tmp_x = $pdf->getX() + 6;
    $txt   = '戶名：' . $data_Error[$i]['bEaccountName'];
    $pdf->MultiCell(10, '1', $txt, 0, 'L', 0, 1, $tmp_x, $tmp_y, true, 0, false, true, 1, 'M', true);
}

$txt = '更正資料：';
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$tmp_y = $pdf->getY();
$txt   = '　　帳號' . $data['bCaccount'];
$pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

$tmp_x = $pdf->getX() + 6;

$txt = '戶名：' . $data['bCaccountName'];
$pdf->MultiCell(10, '1', $txt, 0, 'L', 0, 1, $tmp_x, $tmp_y, true, 0, false, true, 1, 'M', true);
$pdf->SetFont('msungstdlight', 'B', 14);

for ($i = 0; $i < count($data_Correct); $i++) {
    $txt = '　　帳號' . $data_Error[$i]['bEaccount'] . '戶名：' . $data_Error[$i]['bEaccountName'];
    $pdf->MultiCell('', "1", $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 0.5, 'M');
}

if ($data['bOther']) {
    $txt = '其他：';
    $pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');

    $tmp_y = $pdf->getY();
    $txt   = '　　' . $data['bOther'];
    $pdf->MultiCell('', '1', $txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 1, 'M');
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
//writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

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
$img_file = 'images/stamp.png';
$pdf->Image($img_file, ($x + 0.2), ($y + 1), 6, 3.43);

$pdf->Output();
