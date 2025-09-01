<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
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


$sql = "SELECT *,(SELECT zCity FROM tZipArea WHERE zZip = cZip) AS city,(SELECT zArea FROM tZipArea WHERE zZip = cZip) AS area  FROM tContractLand WHERE cCertifiedId = '".$cId."'";


$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$land[] = $rs->fields;

	$rs->MoveNext();
}




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

$pdf->AddPage();
	
$tmpY = $pdf->getY();	

//左上角表格
$pdf->SetFont('msungstdlight', 'B', 8);
$txt = "(下面這一欄申報人不用填寫)";
$pdf->MultiCell(4, '', $txt, 0, 'C', 0, 1, '', '', true);

$txt = "地  政  士  事  務  所";
$pdf->MultiCell(4, '', $txt, 1, 'C', 0, 1, '', '', true);

$txt = "收";
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');

$txt = "日期";
$border = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');
$pdf->MultiCell(2,'','',$border,'C',0);

$txt = "文";
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');

$txt = "字號";
$border = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');
$pdf->MultiCell(2,'','',$border,'C',0);

$txt = "通知日期";
$border = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2,'',$txt,$border,0,'C');
$pdf->MultiCell(2,'','',$border,'C',0);

##
//中間
$pdf->SetFont('msungstdlight', 'B', 14);	
$txt= '土地增值稅(土地現值)申報書';
$border = array(
	'B' => array('width' => 0.01, 'color' => array(0,0,0))
);
	
$pdf->MultiCell(6.5, '', $txt, $border, 'C', 0, 1, 7, ($tmpY+0.4), true);
$pdf->SetFont('msungstdlight', 'B', 8);
$txt= '第一聯：本聯供稅捐機關查定土地增值稅';
$pdf->MultiCell(10, '', $txt, 0, 'C', 0, 1, 5.25, ($tmpY+1.2), true);
##
//右上角表格
$pdf->SetFont('msungstdlight', 'B', 8);

$txt = "(下面這一欄申報人不用填寫)";
$pdf->MultiCell(4, '', $txt, 0, 'C', 0, 1,15.5, $tmpY, true);

$txt = "稅捐稽徵處";
$pdf->MultiCell(4, '', $txt, 1, 'C', 0, 1,15.5, ($tmpY+0.4), true);

$pdf->setX(15.5);
$txt = "收";
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');

$txt = "日期";
$border = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');
$pdf->MultiCell(2,'','',$border,'C',0);

$pdf->setX(15.5);
$txt = "文";
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');

$txt = "號碼";
$border = array(
		'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(1,'',$txt,$border,0,'C');
$pdf->MultiCell(2,'','',$border,'C',0);
$pdf->Ln(0.8);
##
//主表格
$pdf->SetFont('msungstdlight', 'B', 8);
$leftX = 1.5;//左側距離
##line 1 
$tmpH = 0.7;
$txt = '(1)　受　理　機　關';
$pdf->Cell(5,$tmpH,$txt,1,0,'C');
$pdf->Cell(13,$tmpH,'',1,1,'C');

##line 2 
$txt = '(2)　土　地　座　落';
$pdf->Cell(5, $tmpH, $txt, 1,  0,'C', 0); 

$pdf->SetFont('msungstdlight', 'B', 7);
$txt = '(3) 轉 移 或 設 定 比 率';
$pdf->MultiCell(2, $tmpH, $txt, 1, 'C', 0, 0);


$pdf->SetFont('msungstdlight', 'B', 8);
$txt = '(4) 土　地　面　積';
// $pdf->MultiCell(4, $tmpH, $txt, 1, 'C', 0, 0,'','',false);
$pdf->Cell(4, $tmpH, $txt, 1,  0,'C', 0); 

$pdf->SetFont('msungstdlight', 'B', 7);
$txt = '(5)原規定地價或前次移轉申報現值';
$pdf->MultiCell(3, $tmpH, $txt, 1, 'C', 0, 0);

$pdf->SetFont('msungstdlight', 'B', 8);
$txt = '(6) 申報現值';
$pdf->Cell(4, $tmpH, $txt, 1,  1,'C', 0); 

##line 3 ##
// $tmpY = $pdf->getY();
$pdf->SetFont('msungstdlight', 'B', 6);

// $pdf->setY(($tmpY-0.01));
$tmpX = $pdf->getX();
// $pdf->MultiCell(1.2, 0.2, $txt, 1, 'C', 0, 0);
$pdf->Cell(1.2, 0.2, '鄉鎮市區', 1,  0,'C', 0); //鄉鎮市區
$pdf->Cell(1.2, 0.2, '段', 1,  0,'C', 0);
$pdf->Cell(1, 0.2, '小段', 1,  0,'C', 0);
$pdf->Cell(1, 0.2, '地號', 1,  0,'C', 0);
$pdf->Cell(0.6, 0.2, '地目', 1,  0,'C', 0);

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.28));
$pdf->setX($tmpX);
$pdf->SetFont('msungstdlight', 'B', 6);
$pdf->Cell(1.2, 1, $land[0]['city'].$list[0]['area'], 1,  0,'C', 0); //鄉鎮市區
$pdf->Cell(1.2, 1, '', 1,  0,'C', 0); //段
$pdf->Cell(1, 1, '', 1,  0,'C', 0); //小段
$pdf->Cell(1, 1, '', 1,  0,'C', 0); //地號
$pdf->Cell(0.6, 1, '', 1,  0,'C', 0);//地目

$pdf->SetFont('msungstdlight', 'B', 6);
$tmpX = $pdf->getX();
$pdf->setY(($tmpY));
$pdf->setX($tmpX);
$pdf->Cell(2, 0.6, "□全　筆", 1,  0,'L', 0); 

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.6));
$pdf->setX($tmpX);

$pdf->Cell(2, 0.68, "□持分____________", 1,  0,'L', 0); 

$tmpX = $pdf->getX();
$tmpY2 = $tmpY;
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->SetFont('msungstdlight', 'B', 4);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(0.8, 0.1, '面積', $border,  0,'R', 0); 

$pdf->SetFont('msungstdlight', 'B', 6.25);
$pdf->Cell(0.8, 0.14, '公頃', 1,  0,'C', 0); 
$pdf->Cell(0.8, 0.14, '公畝', 1,  0,'C', 0); 

$pdf->SetFont('msungstdlight', 'B', 4);
$pdf->Cell(0.8, 0.28, '平方公尺', 1,  0,'C', 0); 
$pdf->Cell(0.8, 0.28, '平方公寸', 1,  0,'C', 0);
$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.1));
$pdf->setX($tmpX);

$pdf->SetFont('msungstdlight', 'B', 4);
$border = array(
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 
	);
$pdf->Cell(0.8, 0.1, '項目', $border,  0,'L', 0);

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.19));
$pdf->setX($tmpX);

$pdf->Cell(0.8, 0.59, '整  筆', 1,  0,'C', 0); 

$pdf->Cell(0.8, 0.59, '', 1,  0,'C', 0); //公頃
$pdf->Cell(0.8, 0.59, '', 1,  0,'C', 0); //公畝

$pdf->Cell(0.8, 0.59, '', 1,  0,'C', 0); //平方公尺
$pdf->Cell(0.8, 0.59, '', 1,  0,'C', 0); //平方公寸

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.59));
$pdf->setX($tmpX);

$pdf->MultiCell(0.8, 0.4, '移轉或設典面積', 1, 'C', 0, 0);

$pdf->Cell(0.8, 0.4, '', 1,  0,'C', 0); //公頃
$pdf->Cell(0.8, 0.4, '', 1,  0,'C', 0); //公畝

$pdf->Cell(0.8, 0.4, '', 1,  0,'C', 0); //平方公尺
$pdf->Cell(0.8, 0.4, '', 1,  0,'C', 0); //平方公寸

$tmpX = $pdf->getX();
$pdf->setY($tmpY2);
$pdf->setX($tmpX);
$pdf->SetFont('msungstdlight', 'B', 7);
$pdf->MultiCell(1.5, 0.5, "原因發\r\n生日期", 1, 'C', 0, 0);

$tmpX2 = $pdf->getX();
$pdf->Cell(0.5, 0.3, '年', 1,  0,'C', 0); 
$pdf->Cell(0.5, 0.3, '月', 1,  0,'C', 0); 
$pdf->Cell(0.5, 0.3, '日', 1,  0,'C', 0); 

$tmpY = $pdf->getY();
$tmpY2 = $tmpY;
$pdf->setY(($tmpY+0.31));
$pdf->setX($tmpX2);

$pdf->Cell(0.5, 0.3, '', 1,  0,'C', 0); 
$pdf->Cell(0.5, 0.3, '', 1,  0,'C', 0); 
$pdf->Cell(0.5, 0.3, '', 1,  0,'C', 0); 


$pdf->setY(($tmpY2+0.63));
$pdf->setX($tmpX);
$pdf->MultiCell(1.5, 0.65, "每平\r\n方公尺", 1, 'C', 0, 0);
$pdf->Cell(1.5, 0.65, '', 1,  0,'C', 0); 

$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$txt = "□按土地公告現值每平方公尺\r\n___________元計課";

$pdf->MultiCell(4,0.6, $txt, $border, 'L', 0, 0);


$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.6));
$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

$txt = "□按每平方公尺\r\n___________元計課";
$pdf->MultiCell(4,0.69, $txt, $border, 'L', 0, 1);

##Line 4
$pdf->SetFont('msungstdlight', 'B', 6);
$tmpH = 0.5;
$txt = "(7)本筆土地契約所載金額";
$pdf->Cell(3,$tmpH,$txt,1,0,'L');
$txt = "元";
$pdf->Cell(4.5,$tmpH,$txt,1,0,'R');

$txt = "(8)有無「遺產及贈與稅法」第五條規定視同贈與各款情事之一";
$pdf->Cell(8,$tmpH,$txt,1,0,'L');

$txt = "□有　　　□無";
$pdf->Cell(2.5,$tmpH,$txt,1,1,'L');

##line5
$tmpH = 0.1;
$pdf->SetFont('msungstdlight', 'B', 7);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

$txt = '　　上列土地於民國___年___月___日訂約□買賣□贈與□配偶贈與□交換□共有土地分割□設定典權□土地合併□ ______，依法據實申報現值如上。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');


$txt = '　　□檢附土地改良費用證明書___張，工程受益費繳納收據___張，重劃費用證明書___張，捐贈土地公告現值證明文件___張，請依法扣除土地漲價總數額。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　□本筆土地符合□土地稅法第34條第1項至第4項規定，□全部□部分（第__層供自用住宅使用面積_______平方公尺，非自用住宅使用面積_______平方公尺）';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　　　　　　　　　符合自用注定條件。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '(9)　　　　　　　　　□土地稅法第34條第5項規定，（另附申請適用土地稅法第34條第5項規定申明書）茲檢附建築改良物資料影本___份，戶口名簿影本___份，';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　　　　　　　　　請按自用住宅用地稅率核課。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');


$txt = '　　□本筆土地為農業用地，茲檢附農業用地作農業使用證明書等相關證明文件___份，請依土地稅法第39條之2第1項規定不課徵土地增值稅，□並申請依89年1月';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　28日土地稅法修正生效當期公告土地現值調整原地價。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　□本筆土地於89年1月28日土地稅法修正公布生效時，為作農業使用之農業用地，茲檢附相關證明文件___份，請依修正生效當期公告土地現值為原地價';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　課徵土地增值稅。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　□本筆土地為公共設施保留地，茲檢附相關證明文件___份，請依土地稅法第39條第2項免徵土地增值稅。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$txt = '　　□本筆土地為配偶相互贈與之土地，茲檢附相關證明文件___份，請依土地稅法第39條第2項免徵土地增值稅。';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');

$border = array(
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

$txt = '　　□本筆土地符合____________________________________規定，茲檢附有關證明文件，請准予________________________________土地增值稅';
$pdf->Cell(18,$tmpH,$txt,$border,1,'L');
##line6
$pdf->SetFont('msungstdlight', 'B', 6);
$tmpH = 0.3;
$txt = "(10)茲委託______君代辦土地現值申報、領取土地增值稅繳款書或免稅證明書及應納未納土地稅繳款書、工程受益費繳款書等事項。";
$pdf->Cell(18,$tmpH,$txt,1,1,'L');

##
$tmpY = $pdf->getY();
$x = $pdf->getX();

$pdf->MultiCell(0.6, 0.6, '', 1, 'C', 0, 0);
$x +=0.6;

$pdf->SetFont('msungstdlight', 'B', 6);
$txt = '義務人';
$pdf->MultiCell(1.1, 0.3, $txt, 1, 'C', 0, 0);

$txt = '權利人';
$pdf->MultiCell(1.1, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x+=1.1;

$pdf->SetFont('msungstdlight', 'B', 6);
$txt = "姓名或\r\n名稱";
$pdf->MultiCell(2.5, 0.6, $txt, 1, 'C', 0, 0,$x,$tmpY,false);
$x +=2.5;

$txt = "國民身分證\r\n統一編號";
$pdf->MultiCell(2, 0.6, $txt, 1, 'C', 0, 0,$x,$tmpY);
$x +=2;

$txt = "出生年";
$pdf->MultiCell(1, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "月";
$pdf->MultiCell(0.5, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 0.5;

$txt = "日";
$pdf->MultiCell(0.5, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 0.5;

$txt = "權利移轉範圍";
$pdf->MultiCell(1.2, 0.6, $txt, 1, 'C', 0, 0,$x,$tmpY);
$x += 1.2;

$pdf->SetFont('msungstdlight', 'B', 4);
$txt = "戶籍地址";
$pdf->MultiCell(1, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "通訊地址";
$pdf->MultiCell(1, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 1;

$pdf->SetFont('msungstdlight', 'B', 6);
$txt = "縣　市";
$pdf->MultiCell(1, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "街　路";
$pdf->MultiCell(1, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 1;

$txt = "鄉鎮市區";
$pdf->MultiCell(1.2, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "段";
$pdf->MultiCell(1.2, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 1.2;

$txt = "村里";
$pdf->MultiCell(1, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "巷";
$pdf->MultiCell(1, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 1;

$txt = "鄰";
$pdf->MultiCell(0.8, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "弄";
$pdf->MultiCell(0.8, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 0.8;

$txt = "號";
$pdf->MultiCell(0.8, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "樓(室)";
$pdf->MultiCell(0.8, 0.3, $txt, 1, 'C', 0, 0,$x,($tmpY+0.3));
$x += 0.8;

$pdf->SetFont('msungstdlight', 'B', 8);
$txt = "蓋　章";
$pdf->MultiCell(2, 0.6, $txt, 1, 'C', 0, 0,$x,$tmpY);
$x += 2;

$pdf->MultiCell(0.1, 0.6, '', 1, 'C', 0, 0,$x,$tmpY);
$x += 0.1;

$pdf->SetFont('msungstdlight', 'B', 6);
$txt = "身分代號";
$pdf->MultiCell(1.7, 0.3, $txt, 1, 'C', 0, 0,$x,$tmpY);

$txt = "電話";
$pdf->MultiCell(1.7, 0.3, $txt, 1, 'C', 0, 1,$x,($tmpY+0.3));

##line

$Y = $pdf->getY();

for ($i=0; $i < 4 ; $i++) { 
	$pdf->SetFont('msungstdlight', 'B', 6);
	$x = 2.1;
	$pdf->setX($x);

	$pdf->Cell(1.1, 0.8, '', 1,  0,'C', 0); //權益人
	// $x += 1.1;

	$pdf->Cell(2.5, 0.8, '', 1,  0,'C', 0); //姓名
	// $x += 2.5;

	$pdf->Cell(2, 0.8, '', 1,  0,'C', 0);//身分證ID
	// $x += 2;

	$pdf->Cell(0.5, 0.8, '', 1,  0,'C', 0);//出生年
	// $x += 0.5;

	$pdf->Cell(0.5, 0.8, '', 1,  0,'C', 0);//出生月
	// $x += 0.5;

	$pdf->Cell(1.2, 0.8, '', 1,  0,'C', 0);//權利移轉
	// $x += 1.2;

	$tmpX = $pdf->getX();
	$pdf->SetFont('msungstdlight', 'B', 4);
	$pdf->Cell(1, 0.4, '', 1,  0,'C', 0); //戶籍地址
	$pdf->Cell(4.8, 0.4, '', 1,  0,'C', 0); //戶籍地址
	
	$tmpY = $pdf->getY();
	$pdf->setY(($tmpY+0.4));
	$pdf->setX($tmpX);
	$pdf->Cell(1, 0.4, '', 1,  0,'C', 0); //通訊地址
	$pdf->Cell(4.8, 0.4, '', 1,  0,'C', 0); //戶籍地址
	// $pdf->Cell(1, 0.5, '通訊地址', 1,  0,'C', 0); //通訊地址

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(2, 0.8, '', 1,  0,'C', 0); //蓋章

	$pdf->Cell(0.1, 0.8, '', 1,  0,'C', 0); //空

	$tmpX = $pdf->getX();
	$pdf->Cell(1.7, 0.4, '', 1,  0,'C', 0); //身分代號

	$pdf->setY(($tmpY+0.4));
	$pdf->setX($tmpX);

	$pdf->Cell(1.7, 0.4, '', 1,  1,'C', 0); //電話

	
	$tmpY = $pdf->getY();
	
}



$pdf->setX($x);
$pdf->SetFont('msungstdlight', 'B', 7.5);
$txt = '(11)申報人';
$h = $tmpY-$Y;
$pdf->MultiCell(0.6, $h, $txt, 1, 'C', 0, 1,1.5,$Y);//身分代號

##

$pdf->setY($tmpY);
$x = $pdf->GetX();
$pdf->Cell(1.7, 1, '代理人', 1,  0,'C', 0);


$pdf->Cell(2.5, 1, '', 1,  0,'C', 0); //姓名

$pdf->Cell(2, 1, '', 1,  0,'C', 0);//身分證ID
	
$pdf->Cell(0.5, 1, '', 1,  0,'C', 0);//出生年
	
$pdf->Cell(0.5, 1, '', 1,  0,'C', 0);//出生月
	
$pdf->Cell(1.2, 1, '', 1,  0,'C', 0);//權利移轉
	
$tmpX = $pdf->getX();
$pdf->SetFont('msungstdlight', 'B', 4);
$pdf->Cell(1, 0.5, '', 1,  0,'C', 0); //戶籍地址
$pdf->Cell(4.8, 0.5, '', 1,  0,'C', 0); //戶籍地址
	
$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.5));
$pdf->setX($tmpX);
$pdf->Cell(1, 0.5, '', 1,  0,'C', 0); //通訊地址
$pdf->Cell(4.8, 0.5, '', 1,  0,'C', 0); //戶籍地址

$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->Cell(2, 1, '', 1,  0,'C', 0); //蓋章

$pdf->Cell(0.1, 1, '', 1,  0,'C', 0); //空

$tmpX = $pdf->getX();
$pdf->Cell(1.7, 0.5, '', 1,  0,'C', 0); //身分代號

$pdf->setY(($tmpY+0.5));
$pdf->setX($tmpX);

$pdf->Cell(1.7, 0.5, '', 1,  1,'C', 0); //電話
###
$pdf->SetFont('msungstdlight', 'B', 6);
$tmpY = $pdf->getY();
$pdf->setY($tmpY);

$pdf->MultiCell(1.5, 0.5, "(12)填報日期中華民國:", 1, 'R', 0,0);

$txt = "____年___月___日";
$pdf->Cell(2.5, 0.54, $txt, 1,  0,'L', 0); 

$pdf->MultiCell(1.5, 0.5, "(13)繳款書送達方式:", 1, 'L', 0,0);

$tmpX = $pdf->getX();
$border = array(
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

$txt = "□郵寄送達:受送達人:_________地址:";
$pdf->Cell(12.5, 0.25, $txt, $border,  0,'L', 0);

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.22));
$pdf->setX($tmpX);

$border = array(
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$txt = "□親自領取  (本欄如未勾劃者視為親自領取)";
$pdf->Cell(12.5, 0.32, $txt, $border,  1,'L', 0);
##

$txt = "(14)移轉後新所有權人地價稅繳款書寄送地址:同第(11)所填。□戶籍地址、□住居所。□請寄："; //□親自領取  
$pdf->Cell(18, 0.32, $txt, 1,  1,'L', 0);

$txt = "(15)本案土地係購供自用並於9月22日前遷入戶籍(或適用特別稅率或符合減免地價稅)。請務必則一勾選:\r\n"; //□親自領取 
$txt .= "   □茲先提出申請，俟辦妥登記候補送有關證明文件，於當年9月22日前符合適用自住稅率(或特別稅率或減免地價稅者)條件者，准自當年起適用。\r\n"; //□親自領取 
$txt .= "   □不申請";
$pdf->MultiCell(18, 0.5, $txt, 1, 'L', 0,1);
$pdf->SetFont('msungstdlight', 'B', 8);
$txt = "稅  額  查  定  基  本  資  料  表     原 土 地 所 有 權 人：                                長期減徵起算日期:    年    月   日";
$pdf->Cell(18, 0.32, $txt, 1,  1,'L', 0);

##
$pdf->SetFont('msungstdlight', 'B', 6);
$Y = $pdf->getY();
$txt = "重測(劃)前\r\n土地標示";
$pdf->MultiCell(2, 0.5, $txt, 1, 'C', 0,0);


$tmpX = $pdf->getX();
$pdf->Cell(2, 0.25, "段", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.25, "小  段", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.25, "地  號", 1,  0,'C', 0);

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.27));
$pdf->setX($tmpX);

$pdf->Cell(2, 0.26, "", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.26, "", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.26, "", 1,  0,'C', 0);



$tmpX = $pdf->getX();
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(2, 0.5, "原規定地價或\r\n前次移轉現值", 1, 'C', 0,0);


$tmpX = $pdf->getX();
$pdf->Cell(1.5, 0.25, "坪單價", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.25, "平方公尺單價", 1,  0,'C', 0);
$pdf->SetFont('msungstdlight', 'B', 4);
$pdf->Cell(2, 0.27, "移轉日期文號(底冊號)", 1,  0,'C', 0);


$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.27));
$pdf->setX($tmpX);
$txt = "元";
$pdf->Cell(1.5, 0.27, $txt, 1,  0,'R', 0);
$pdf->Cell(1.5, 0.27, $txt, 1,  0,'R', 0);
$pdf->Cell(2, 0.27, $txt, 1,  0,'R', 0);

$pdf->SetFont('msungstdlight', 'B', 6);
$tmpX = $pdf->getX();
$X = $tmpX;
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->MultiCell(2, 0.5, "每平方公尺\r\n公告現值", 1, 'C', 0,1);


$txt = "已繳工程受益費\r\n或土地改良費或\r\n土地重劃費用或\r\n捐贈土地現值總額";
$pdf->MultiCell(2, 0.5, $txt, 1, 'C', 0,0);

$tmpX = $pdf->getX();
$pdf->SetFont('msungstdlight', 'B', 6);
$pdf->Cell(2, 0.34, "本宗土地面積", 1,  0,'C', 0); 
$pdf->Cell(1.5, 0.34, "本宗土地金額", 1,  0,'C', 0); 
$pdf->writeHTMLCell(1.5, 0.34,'', '', "<div>每m<sup>2</sup>金額</div>", 1, 0, 0, true, C);
$pdf->Cell(2, 0.34, "本次移轉面積", 1,  0,'C', 0); 
$pdf->Cell(1.5, 0.34, "本次移轉攤計額", 1,  0,'C', 0);
$pdf->Cell(3.5, 0.34, "底冊或證明單號碼", 1,  0,'C', 0);
$pdf->Cell(2, 1.07, "元", 1,  0,'R', 0); 

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.35));
$pdf->setX($tmpX);

// $pdf->Cell(1.5, 0.46, "", 1,  0,'R', 0);
$pdf->writeHTMLCell(2, 0.37,'', '', "m<sup>2</sup>", 1, 0, 0, true, R, true);
$pdf->Cell(1.5, 0.37, "元", 1,  0,'R', 0); 
$pdf->Cell(1.5, 0.37, "元", 1,  0,'R', 0);
$pdf->writeHTMLCell(2, 0.37,'', '', "m<sup>2</sup>", 1, 0, 0, true, R, true); 
$pdf->Cell(1.5, 0.37, "元", 1,  0,'R', 0);
$pdf->Cell(3.5, 0.37, "", 1,  0,'C', 0);

$tmpY = $pdf->getY();
$pdf->setY(($tmpY+0.37));
$pdf->setX($tmpX);
$pdf->Cell(2, 0.35, "", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.35, "", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.35, "", 1,  0,'C', 0);
$pdf->Cell(2, 0.35, "", 1,  0,'C', 0);
$pdf->Cell(1.5, 0.35, "", 1,  0,'R', 0);
$pdf->Cell(3.5, 0.35, "", 1,  0,'C', 0);

$pdf->SetFont('msungstdlight', 'B', 4);

$border = array(
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

$X += 2;
$pdf->MultiCell(0.4, 1.61, "現值審核人員意見", $border, 'C', 0,0,$X,$Y);

$border = array(
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->MultiCell(2, 1.61, "", $border, 'C', 0,1,$X,$Y);

$pdf->SetFont('msungstdlight', 'B', 6);
$tmpY = $pdf->getY();

$pdf->Cell(2, 1, "1.宗地面積", 1,  1,'L', 0);
$pdf->Cell(2, 0.5, "2.移轉持分", 1,  1,'L', 0);
$pdf->Cell(2, 0.5, "3.移轉現值", 1,  1,'L', 0);
$pdf->MultiCell(2, 0.5, "4.原規定地價或\r\n   前次移轉現值", 1, 'L', 0,1);
$pdf->Cell(2, 0.5, "5.物價指數", 1,  1,'L', 0);
$pdf->Cell(2, 0.5, "6.改良土地費用", 1,  1,'L', 0);
$pdf->MultiCell(2, 0.5, "7.空荒地未改良\r\n   移轉加徵比例", 1, 'L', 0,1);
$pdf->MultiCell(2, 0.5, "8.空荒地改良後\r\n   移轉減徵比例", 1, 'L', 0,1);
$pdf->MultiCell(2, 0.5, "9.重劃後第一次\r\n   移轉減徵比例", 1, 'L', 0,1);
$pdf->Cell(2, 0.5, "10.增繳之地價稅", 1,  1,'L', 0);
$pdf->Cell(2, 0.5, "11.已繳納稅款", 1,  1,'L', 0);

$tmpX = $pdf->getX()+2;
$pdf->setY($tmpY);
$pdf->setX($tmpX);

$pdf->Cell(2, 1, "平方公尺", 1,  1,'R', 0); //宗地面積

$pdf->setX($tmpX); //移轉持分
$pdf->Cell(2, 0.5, "／", 1,  1,'C', 0);//移轉現值

$pdf->SetFont('msungstdlight', 'B', 4);
$pdf->setX($tmpX);


$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "每平方公尺", $border,  1,'L', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "元", $border,  1,'R', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "每平方公尺", $border,  1,'L', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.29, "元", $border,  1,'R', 0);

$pdf->SetFont('msungstdlight', 'B', 6);
$pdf->setX($tmpX);
$pdf->Cell(2, 0.5, "%", 1,  1,'R', 0);

$pdf->setX($tmpX);
$pdf->Cell(2, 0.5, "元", 1,  1,'R', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "(＋)", $border,  1,'L', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "%", $border,  1,'R', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "(－)", $border,  1,'L', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "%", $border,  1,'R', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "(－)", $border,  1,'L', 0);

$pdf->setX($tmpX);
$border = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(2, 0.25, "%", $border,  1,'R', 0);

$pdf->setX($tmpX);
$pdf->Cell(2, 0.5, "   %或            元", 1,  1,'R', 0);

$pdf->setX($tmpX);
$pdf->Cell(2, 0.5, "元", 1,  1,'R', 0);

$tmpX = $pdf->getX()+4;
$pdf->setY($tmpY);
$pdf->setX($tmpX);
$pdf->Cell(14, 0.3, "稅地種類代號：以打勾表示", 1,  1,'L', 0);

$tmpX = $pdf->getX()+4;
$tmpH = 0.32;
$pdf->setX($tmpX);
$pdf->Cell(3.5, $tmpH, "自住/稅種", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "信託歸屬", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "30", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "抵繳稅款", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "46", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "領抵價地", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "76", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用買賣", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "01", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般買賣", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "31", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "遺贈", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "47", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "農地買賣", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "77", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "一生一屋", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "02", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般贈與", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "32", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "分期繳納", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "48", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "農地贈與", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "78", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用交換", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "03", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般交換", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "33", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "促產記存", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "49", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "農地交換", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "79", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用分割", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "05", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般典權", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "34", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "其他記存", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "50", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "農地分割", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "80", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用法拍", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "06", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般分割", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "35", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "都更記存", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "51", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "農地法拍", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "81", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用徵收", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "07", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般法拍", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "36", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "水一般20", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "52", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "徵收全免", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "82", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用徵收", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "08", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般徵收", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "37", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "水一般30", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "53", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "協議價購", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "83", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用收買", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "09", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般徵收", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "38", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "水一般40", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "54", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "農地合併", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "84", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用合併", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "10", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般收買", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "39", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "公設移轉", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "85", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "自用重購", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "11", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般合併", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "40", 1,  0,'C', 0);
$pdf->Cell(3.5, $tmpH, "免稅/稅種", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "信託移轉", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "86", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "水自用20", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "12", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "一般重購", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "41", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "水一般免", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "55", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "金融合併", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "87", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "水自用30", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "13", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "信託取得", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "42", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "公有移轉", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "71", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "賸財不課", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "88", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "水自用50", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "14", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "判決移轉", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "43", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "政府受贈", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "72", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "夫妻贈與", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "89", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(3.5, $tmpH, "一般/稅種", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "判決分割", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "44", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "政府贈與", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "73", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "都市更新", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "90", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "都更減徵", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "28", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "最低稅率", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "45", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "社福受贈", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "74", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "視為農地", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "91", 1,  1,'C', 0);

$pdf->setX($tmpX);
$pdf->Cell(1.5, $tmpH, "一般賸財", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "29", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "抵繳稅款", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "46", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "私校受贈", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "75", 1,  0,'C', 0);
$pdf->Cell(1.5, $tmpH, "公同共有", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "", 1,  0,'C', 0);
$pdf->Cell(1, $tmpH, "92", 1,  1,'C', 0);

$pdf->setX($tmpX);

$border1 = array(
		 'L' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

$pdf->Cell(8, ($tmpH+0.1), "適用自用住宅用地稅率面積", $border,  0,'C', 0);

$border2 = array(
		 'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);

$pdf->writeHTMLCell(2.5, ($tmpH+0.1),'', '', "m<sup>2</sup>", $border, 0, 0, true, C, true);

$pdf->Cell(1.5, ($tmpH+0.1), "水利不課", 1,  0,'C', 0);
$pdf->Cell(1, ($tmpH+0.1), "", 1,  0,'C', 0);
$pdf->Cell(1, ($tmpH+0.1), "93", 1,  1,'C', 0);


$border3 = array(
		 'T' => array('width' => 0.01, 'color' => array(0,0,0)),
		 'B' => array('width' => 0.01, 'color' => array(0,0,0)),
	);
$pdf->Cell(3.5, ($tmpH+0.1), "　　資料查證人員", $border1,  0,'L', 0);
$pdf->Cell(3.5, ($tmpH+0.1), "登錄計算人員", $border3,  0,'L', 0);
$pdf->Cell(3, ($tmpH+0.1), "覆核人員", $border3,  0,'L', 0);
$pdf->Cell(2.5, ($tmpH+0.1), "股  長", $border3,  0,'L', 0);
$pdf->Cell(2.5, ($tmpH+0.1), "審核員", $border3,  0,'L', 0);
$pdf->Cell(3, ($tmpH+0.1), "分處主任", $border2,  1,'L', 0);


$pdf->Cell(1.5, '', "注意事項:", 0,  1,'L', 0);

$txt = '一、本申報書需填寫1式2聯，如第(11)欄所留空格不夠填寫時，請另依格式用紙黏貼於該欄下，並於黏貼處加蓋申報人印章。';
$pdf->Cell(1.5, '', $txt, 0,  1,'L', 0);
$txt = '二、本申報書金額各欄均應以新台幣填寫。';
$pdf->Cell(1.5, '', $txt, 0,  1,'L', 0);
$txt = '三、上面格式雙線以上各欄請申報人參閱填寫說明書填妥，雙線以下各欄申報人不必填寫。';
$pdf->Cell(1.5, '', $txt, 0,  1,'L', 0);
$txt = '四、本申報書第(6)欄內如「□按每平方公尺______元計課」一項如有錯誤、缺漏且未勾選按申報當期公告現值計課、塗改、挖補情';
$txt .='事者不予收件。其餘各欄項如有上列情事，當事人應註記刪改文字';
$pdf->Cell(1.5, '', $txt, 0,  1,'L', 0);
$txt = '字數，加蓋與原申報書同一之印章後，始予受理收件。';
$pdf->Cell(1.5, '', $txt, 0,  1,'L', 0);
$txt = '五、依土地稅法第49條規定申報土地移轉現值應檢附契約書影本及有關文件。';
$pdf->Cell(1.5, '', $txt, 0,  1,'L', 0);
$txt = '六、權利人住址在國外者，請在本申報書第(11)欄最後一行填寫在國內之納稅代理人姓名，國民身分證統一編號及住址。';
$pdf->Cell(1.5, '', $txt, 0,  1,'L', 0);
$pdf->Output() ;





// $pdf->Cell(10, 0.3, "稅地種類代號：以打勾表示", 1,  1,'L', 0);


// $pdf->Cell(1.5, 0.25, "小  段", 1,  0,'C', 0);


// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')

// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')


?>