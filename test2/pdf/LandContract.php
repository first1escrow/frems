<?php
// include_once '../configs/config.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
require_once('../../tcpdf/tcpdf.php');
// include_once 'class/contract.class.php';
// include_once 'bookFunction.php';

$_POST = escapeStr($_POST) ;
$cId = '005079426' ;

// $sql = "SELECT
// 			*
// 		FROM
// 			tContractCase AS cc
// 		WHERE
// 		cc.cCertifiedId = '".$cId."'";


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
$pdf->Cell(18,0.8,"土地所有權買賣移轉契約書",1,1,'C',0); 

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
$pdf->Cell(18,1.2,"(6)買賣價款總金額：新台幣",1,1,'J',0); 

##
$pdf->SetFont('msungstdlight', 'B', 8);
$tmpY = $pdf->getY()+0.5;
$pdf->setY($tmpY);
$pdf->MultiCell(0.5, 4.5, "(7)\r\n申\r\n請\r\n登\r\n記\r\n以\r\n外\r\n之\r\n約\r\r定\r\n事\r\n項", 1, 'L', 0, 0);
$tmpX = $pdf->getX();
$pdf->Cell(8.5,0.5,"1.他項權利情形：",$border,1,'L',0); 
$pdf->setX($tmpX);
$pdf->Cell(8.5,4,"",$border3,0,'L',0); 
// $pdf->Output() ;
$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(0.9, 4.5, "(9)\r\n簽\r\n名\r\n或\r\n簽\r\n證", 1, 'C', 0, 0);
$pdf->Cell(8.1,4.5,"",1,1,'L',0); 
##
$tmpY = $pdf->getY();

$pdf->setY($tmpY);
$pdf->MultiCell(0.5, 7.07, "訂\r\n\r\n立\r\n\r\n契\r\n\r\n約\r\n\r\n人", 1, 'L', 0, 0);

$tmpX = $pdf->getX();
$tmpX2 = $tmpX;
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(2.2, 1, "(10)\r\n\r\n買受人或出賣人", 1, 'C', 0, 0);
$pdf->MultiCell(2.2, 1, "(11)\r\n\r\n姓名或名稱", 1, 'C', 0, 0);
$tmpX = $pdf->getX();
$pdf->MultiCell(2.2, 0.3, "(12)權利範圍", 1, 'C', 0, 1);

$pdf->setX($tmpX);
$pdf->MultiCell(1.1, 0.3, "買受\r\n持分", 1, 'C', 0, 0);
$pdf->MultiCell(1.1, 0.3, "出賣\r\n持分", 1, 'C', 0, 0);

$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(1.8, 1, "(13)\r\n\r\n出生年月日", 1, 'C', 0, 0);
$pdf->MultiCell(2, 1, "(14)\r\n\r\n統一編號", 1, 'C', 0, 0);

$tmpX = $pdf->getX();
$pdf->MultiCell(6, 0.3, "(15)住 所", 1, 'C', 0, 1);
$pdf->setX($tmpX);
$pdf->Cell(1,0.7,"縣市",1,0,'L',0); 
$pdf->Cell(1.3,0.7,"鄉鎮市區",1,0,'C',0); 
$pdf->Cell(0.7,0.7,"村里",1,0,'C',0); 
$pdf->Cell(0.4,0.7,"鄰",1,0,'C',0); 
$pdf->Cell(0.7,0.7,"街路",1,0,'C',0);
$pdf->Cell(0.4,0.7,"段",1,0,'C',0);
$pdf->Cell(0.7,0.7,"巷弄",1,0,'C',0);
$pdf->Cell(0.4,0.7,"號",1,0,'C',0);
$pdf->Cell(0.4,0.7,"樓",1,0,'C',0);

$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(1.1, 1, "(16)\r\n\r\n蓋章", 1, 'C', 0, 1);
$tmpY = $pdf->getY();
$pdf->setX($tmpX2);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.8, 1,"",1,0,'C',0);
$pdf->Cell(2, 1,"",1,0,'C',0);
$pdf->Cell(6, 1,"",1,1,'C',0);


$pdf->setX($tmpX2);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.8, 1,"",1,0,'C',0);
$pdf->Cell(2, 1,"",1,0,'C',0);
$pdf->Cell(6, 1,"",1,1,'C',0);

$pdf->setX($tmpX2);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.8, 1,"",1,0,'C',0);
$pdf->Cell(2, 1,"",1,0,'C',0);
$pdf->Cell(6, 1,"",1,1,'C',0);

$pdf->setX($tmpX2);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.8, 1,"",1,0,'C',0);
$pdf->Cell(2, 1,"",1,0,'C',0);
$pdf->Cell(6, 1,"",1,1,'C',0);

$pdf->setX($tmpX2);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.8, 1,"",1,0,'C',0);
$pdf->Cell(2, 1,"",1,0,'C',0);
$pdf->Cell(6, 1,"",1,1,'C',0);

$pdf->setX($tmpX2);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(2.2, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.1, 1,"",1,0,'C',0);
$pdf->Cell(1.8, 1,"",1,0,'C',0);
$pdf->Cell(2, 1,"",1,0,'C',0);
$pdf->Cell(6, 1,"",1,0,'C',0);

$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->Cell(1.1, 6,"",1,1,'C',0);

$pdf->Cell(18, 1,"(17)立約日期   中華民國      年     月     日",1,0,'L',0);
$pdf->Output() ;
##


// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')

// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
?>