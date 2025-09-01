<?php
include_once '../../openadodb.php' ;
// include_once '../../session_check.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/tcpdf/tcpdf.php' ;
// include_once 'bookFunction.php';

##
$_POST = escapeStr($_POST) ;
// $bId = $_POST['id'] ;

$sd = explode('-', $_POST['StartDate']);//startdate
$ed = explode('-', $_POST['EndDate']);//enddate
$td = explode('-',$_POST['Date']);//titledate
$totalPage = 1;
$StartDate = ($sd[0]+1911)."-".$sd[1]."-".$sd[2];
$EndDate = ($ed[0]+1911)."-".$ed[1]."-".$ed[2];
$bank = $_POST['bank'];


$StartDate = '2016-09-01';
$StartDate = '2016-10-31';
$bank = 1;
// $iDate = $data['Date'] ;//指示書日期

$sql = "SELECT 
			bDate,
			bBookId,
			bMoney
		FROM
			tBankTrankBook
		WHERE
			bDel = 0 AND bBank = '".$bank."' AND bDate >='".$StartDate."' AND bDate <='".$EndDate."' ORDER BY bDate,bBookId ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$list[] = $rs->fields;
	$rs->MoveNext();
}


###########################################



$pdf = new TCPDF('P', 'cm', 'A4', true, 'BIG5', false) ;

$pdf->setPrintHeader(false) ; 
$pdf->setPrintFooter(false) ;
$pdf->setFontSubsetting(true) ;

$pdf->SetLeftMargin(1.5) ;
$pdf->SetRightMargin(1.5) ;
$pdf->SetRightMargin(1.5) ;
$pdf->AddPage() ;
$pdf->SetAutoPageBreak(TRUE, 0);
//
$pdf->SetY(2.3) ;
// $pdf->SetFont('msungstdlight', 'B', 18) ;
// $pdf->SetTextColor(0,0,0) ;
$html = '第一建經履約保證價金信託每月撥款核對表' ;
$pdf->writeHTML($html, $ln=1, $fill=0, $reseth=true, $cell =true, $align='C') ;

// $pdf->SetFont('msungstdlight', 'B', 16) ;
// $html = '戶名:第一商業銀行受託信託財產專戶-第一建經價金履約保證' ;
// $pdf->writeHTML($html, $ln=1, $fill=0, $reseth=true, $cell =true, $align='C') ;


// $pdf->SetFont('msungstdlight', 'B', 18);
// $Header='<span style="text-align:center;vertical-align:top">';	
$Header= 'TEST';
// $Header.='</span><br>';
// $pdf->Cell(0, 10,$Header, 0, 0, '', false, '', 0);
$pdf->	MultiCell ($w, $h, 'TTT', 0, 'J', false, 1, '', '', true);
// $pdf->writeHTML($Header, true, 0, true, true);

// $pdf->SetFont('msungstdlight', 'B', 16);
// $Header='<span style="text-align:center;vertical-align:top">';	
// $Header.= '戶名：第一商業銀行受託信託財產專戶-第一建經價金履約保證';
// $Header.='</span><br>';
// $pdf->writeHTML($Header, true, 0, true, true);

// $x = $pdf->getX()+1;
// $y= $pdf->getY();
// $Header='<span style="text-align:left;vertical-align:top">';	
// $Header.= '帳號：271-10-352556';
// $Header.='</span><br>';
// $pdf->writeHTMLCell(100,0, $x, $y, $Header, '', 1, 0, true, '', true);
// unset($Header);
// ############
// //日期起迄:105/7/1~105/7/31，共x頁
// $ox = $pdf->getX()+1; //預留位置
// $oy= $pdf->getY();//預留位置


// ##

// $x =  $pdf->getX()+0.8;
// $y = $pdf->getY()+0.8;
// $txt = '<span style="text-align:left;vertical-align:middle;line-height:40px;">傳真指示日期</span>';
// $border = array(
// 	'T' => array('width' => 0.05, 'color' => array(0,0,0)),
// 	'B' => array('width' => 0.05, 'color' => array(0,0,0)),
// 	'L' => array('width' => 0.05, 'color' => array(0,0,0)),
// 	'R' => array('width' => 0.05, 'color' => array(0,0,0)),
// );
// $pdf->writeHTMLCell(5,0, $x, $y, $txt, $border, 1, 0, true, '', false);

// $x =  $pdf->getX()+5.8;

// $txt = '<span style="text-align:left;vertical-align:middle;line-height:40px;">指示單編號</span>';


// $pdf->writeHTMLCell(5,0, $x, $y, $txt, $border, 1, 0, true, '', false);

// $x =  $pdf->getX()+10.8;

// $txt = '<span style="text-align:left;vertical-align:middle;line-height:40px;">匯款支付總金額(新臺幣)</span>';

// $pdf->writeHTMLCell(6.5,0, $x, $y, $txt, $border, 1, 0, true, '', false);

// ###

// foreach ($list as $k => $v) {
// 	$x =  $pdf->getX()+0.8;
// 	$y = $pdf->getY();
	
		
// 	if ($y > 26) { //
// 		// $pdf->SetFont('msungstdlight', '', 10);
// 		$pdf->Text(10.3,28.5,$pdf->getAliasNumPage().' / '.$pdf->getAliasNbPages()) ;
// 		// $pdf->SetFont('msungstdlight', 'B', 16);
// 		$pdf->AddPage() ;
// 		$totalPage++;
// 		$y = $pdf->getY();
// 	}

// 	$txt = '<span style="text-align:left;vertical-align:middle;line-height:40px;">'.$v['bDate'].'</span>';
// 	$border = array(
// 		'T' => array('width' => 0.05, 'color' => array(0,0,0)),
// 		'B' => array('width' => 0.05, 'color' => array(0,0,0)),
// 		'L' => array('width' => 0.05, 'color' => array(0,0,0)),
// 		'R' => array('width' => 0.05, 'color' => array(0,0,0)),
// 	);
// 	$pdf->writeHTMLCell(5,0, $x, $y, $txt, $border, 1, 0, true, '', false);
// 	###
// 	$x =  $pdf->getX()+5.8;

// 	$txt = '<span style="text-align:left;vertical-align:middle;line-height:40px;">'.$v['bBookId'].'</span>';


// 	$pdf->writeHTMLCell(5,0, $x, $y, $txt, $border, 1, 0, true, '', false);

// 	$x =  $pdf->getX()+10.8;

// 	$txt = '<span style="text-align:left;vertical-align:middle;line-height:40px;">'.number_format($v['bMoney']).'</span>';

// 	$pdf->writeHTMLCell(6.5,0, $x, $y, $txt, $border, 1, 0, true, '', false);

	

// }
// //下面長度11
// if (($y+11) > 26.5) {
// 	$pdf->Text(10.3,28.5,$pdf->getAliasNumPage().' / '.$pdf->getAliasNbPages()) ;
// 	$pdf->AddPage() ;
// 	$totalPage++;
// }

// $x =  $pdf->getX()+0.8;
// $y = $pdf->getY();  

// $txt='<span style="text-align:left;vertical-align:top;line-height:40px;">';	
// $txt.= '上述資料如有不符，以原始傳真資料為準。';
// $txt.='</span><br>';
// $pdf->writeHTMLCell(0,0, $x, $y, $txt, '', 1, 0, true, '', true);

// $y = $pdf->getY();

// $txt='<span style="text-align:left;vertical-align:top;line-height:40px;">';	
// $txt.= '致';
// $txt.='</span><br>';
// $pdf->writeHTMLCell(0,0, $x, $y, $txt, '', 1, 0, true, '', true);

// $y = $pdf->getY();

// $txt='<span style="text-align:left;vertical-align:top;line-height:40px;">';	
// $txt.= '第一商業銀行&nbsp;&nbsp;信託處';
// $txt.='</span><br>';
// $pdf->writeHTMLCell(0,0, ($x+1.8), $y, $txt, '', 1, 0, true, '', true);   

// $y = $pdf->getY();

// $txt='<span style="text-align:left;vertical-align:top;line-height:40px;">';	
// $txt.= '委託人：第一建築經理股份有限公司';
// $txt.='</span><br>';
// $pdf->writeHTMLCell(0,0, $x, $y, $txt, '', 1, 0, true, '', true);

// $y = $pdf->getY();

// $txt='<span style="text-align:left;vertical-align:top;line-height:40px;">';	
// $txt.= '有權簽章人：';
// $txt.='</span><br>';
// $pdf->writeHTMLCell(0,0, $x, $y, $txt, '', 1, 0, true, '', true); 
// // 

// $y = $pdf->getY()+5;
// $pdf->Line($x,$y,($x+17),$y,array('width' => 0.11)); 


// $y = $pdf->getY()+5;

// $txt='<span style="text-align:left;vertical-align:top;line-height:40px;">';	
// $txt.= '指示日期：&nbsp;&nbsp;'.$td[0].'&nbsp;&nbsp;年&nbsp;&nbsp;'.$td[1].'&nbsp;&nbsp;月&nbsp;&nbsp;'.$td[2].'&nbsp;&nbsp;日';
// $txt.='</span><br>';
// $pdf->writeHTMLCell(0,0, $x, $y, $txt, '', 1, 0, true, '', true); 
// $pdf->Text(10.3,28.5,$pdf->getAliasNumPage().' / '.$pdf->getAliasNbPages()) ;
// // 

// ####################################
// $pdf->setPage(1);
// $Header='<span style="text-align:left;vertical-align:top">';	
// $Header.= '日期起迄：'.$sd[0].'/'.$sd[1].'/'.$sd[2].'~'.$ed[0].'/'.$ed[1].'/'.$ed[2].'，共'.$totalPage.'頁';
// $Header.='</span><br>';
// $pdf->writeHTMLCell(100,0, $ox, $oy, $Header, '', 1, 0, true, '', true);
//
############################

$sFile = $pdf->Output('/home/httpd/html/first.twhg.com.tw/test2/pdf/test.pdf','F') ;

$img = new Imagick(); 
$img -> readImage('test.pdf');
$img -> resetIterator();
$img -> setImageFormat('jpg');
$img->writeImage('output.jpg');
##
?>