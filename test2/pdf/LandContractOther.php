<?php
// include_once '../configs/config.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
require_once('../../tcpdf/tcpdf.php');
// include_once 'class/contract.class.php';
// include_once 'bookFunction.php';

$_POST = escapeStr($_POST) ;
$cId = '005079426' ;



$sql = "SELECT * FROM tContractLand WHERE cCertifiedId = '".$cId."'";
$rs = $conn->Execute($sql);




###
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'cm', 'A4', true, 'UTF-8', false);

// 頁面設定
$pdf->SetCreator(PDF_CREATOR);	
$pdf->SetMargins('1.5', '1', '1.5');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetAutoPageBreak(false);
##
//左上右
$border = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
//左右
$border2 = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
//左下右
$border3 = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
//左上下
$border4 = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

//上下
$border5 = array(
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
//右上下
$border6 = array(
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
//左下
$border7 = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
//右下
$border8 = array(
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
//上右
$border9 = array(
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

##
$pdf->AddPage();

$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->Cell(18,0.8,"土 地 所 有 權 買 賣 移 轉 契 約 書 附 表",1,1,'C',0); 


$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->Cell(1.6,0.8,"下列土地經",$border4,0,'J',0); 

$pdf->SetFont('msungstdlight', 'B', 8);
$pdf->MultiCell(1.3, 0.8, "買受人\r\n出賣人", $border5, 'R', 0, 0);

$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->Cell(15.1,0.8,"雙方同意買賣所有權轉移，特訂立本契約:",$border6,1,'J',0);

##
$tmpY = $pdf->getY();
$pdf->MultiCell(0.7, 8.4, "土\r\n\r\n地\r\n\r\n標\r\n\r\n示", 1, 'C', 0, 0);

$tmpX = $pdf->getX();
$tmpX2 = $tmpX;
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(1, 3.6, "(1)\r\n坐\r\n落", 1, 'C', 0, 0);

$pdf->SetFont('msungstdlight', 'B', 6);
$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(0.9, 1.2, "鄉\r\n鎮\r\n市\r\n區", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX);
$pdf->MultiCell(0.9, 1.2, "\r\n段", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX);
$pdf->MultiCell(0.9, 1.2, "\r\n小\r\n段", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->setX($tmpX2);
$pdf->Cell(1.9,1.2,"(2)地  號",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX2);
$pdf->Cell(1.9,1.2,"(3)地  目",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX2);
$pdf->MultiCell(1.9, 1.2, "(4)面  積\r\n(平方公尺)", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0);

$pdf->setX($tmpX2);
$pdf->Cell(1.9,1.2,"(5)權利範圍",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

##
$tmpY = $pdf->getY()+1;
$pdf->setY($tmpY);

$pdf->SetFont('msungstdlight', 'B', 14);
$pdf->Cell(18,0.8,"土 地 所 有 權 買 賣 移 轉 契 約 書 附 表",1,1,'C',0); 


$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->Cell(1.6,0.8,"下列土地經",$border4,0,'J',0); 

$pdf->SetFont('msungstdlight', 'B', 8);
$pdf->MultiCell(1.3, 0.8, "買受人\r\n出賣人", $border5, 'R', 0, 0);

$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->Cell(15.1,0.8,"雙方同意買賣所有權轉移，特訂立本契約:",$border6,1,'J',0);

##
$tmpY = $pdf->getY();
$pdf->MultiCell(0.7, 8.4, "土\r\n\r\n地\r\n\r\n標\r\n\r\n示", 1, 'C', 0, 0);

$tmpX = $pdf->getX();
$tmpX2 = $tmpX;
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(1, 3.6, "(1)\r\n坐\r\n落", 1, 'C', 0, 0);

$pdf->SetFont('msungstdlight', 'B', 6);
$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(0.9, 1.2, "鄉\r\n鎮\r\n市\r\n區", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX);
$pdf->MultiCell(0.9, 1.2, "\r\n段", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX);
$pdf->MultiCell(0.9, 1.2, "\r\n小\r\n段", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->SetFont('msungstdlight', 'B', 10);
$pdf->setX($tmpX2);
$pdf->Cell(1.9,1.2,"(2)地  號",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX2);
$pdf->Cell(1.9,1.2,"(3)地  目",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

$pdf->setX($tmpX2);
$pdf->MultiCell(1.9, 1.2, "(4)面  積\r\n(平方公尺)", 1, 'C', 0, 0);
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0);

$pdf->setX($tmpX2);
$pdf->Cell(1.9,1.2,"(5)權利範圍",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(1.9,1.2,"",1,0,'J',0); 
$pdf->Cell(2.1,1.2,"",1,1,'J',0); 

##
$pdf->Output() ;



##


// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')

// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
?>