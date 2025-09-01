<?php

###
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'cm', 'A4', true, 'UTF-8', false);

// 頁面設定
$pdf->SetCreator(PDF_CREATOR);	
$pdf->SetMargins('1.5', '1', '1.5');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetAutoPageBreak(false);

getBuileContract($cId,$today);
$pdf->Output() ;
// for ($i=0; $i < count($property); $i++) { 
	
// 	

	

	
// 	
// 	die;
// }

$pdf->Output() ;

function getBuileContract($cId,$today){
	global $pdf;
	global $property;
	global $income;
	global $buyer;
	global $owner;
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
	$pdf->Cell(18,0.8,"建築改良物所有權買賣移轉契約書",1,1,'C',0); 

	$pdf->SetFont('msungstdlight', 'B', 10);
	$pdf->Cell(1.6,0.8,"下列建物經",$border4,0,'J',0); 

	$pdf->SetFont('msungstdlight', 'B', 8);
	$pdf->MultiCell(1.3, 0.8, "買受人\r\n出賣人", $border5, 'R', 0, 0);

	$pdf->SetFont('msungstdlight', 'B', 10);
	$pdf->Cell(15.1,0.8,"雙方同意買賣所有權轉移，特訂立本契約:",$border6,1,'J',0); 
	##
	$tmpY = $pdf->getY();
	$pdf->MultiCell(0.7, 11.5, "建\r\n物\r\n標\r\n示", 1, 'L', 0, 0);

	$tmpX = $pdf->getX();
	$tmpX2 = $pdf->getX();
	
	$pdf->Cell(2.3,0.5,"(1)建  號",1,0,'J',0); 
	for ($i=0; $i < 4; $i++) { 
		if ($i < 3) {
			$pdf->Cell(3.75,0.5,$property[$i]['cBuildNo'],1,0,'C',0); 
		}else{
			$pdf->Cell(3.75,0.5,$property[$i]['cBuildNo'],1,1,'C',0); 
		}
		
	}
	
	// $pdf->Cell(3.75,0.5,"",1,0,'J',0); 
	// $pdf->Cell(3.75,0.5,"",1,0,'J',0); 
	

	$pdf->setX($tmpX);
	$tmpY = $pdf->getY();
	$pdf->MultiCell(0.7, 2, "(2)\r\n門\r\n牌", 1, 'L', 0, 0);

	$tmpX = $pdf->getX();
	$pdf->Cell(1.6,0.5,"鄉鎮市區",1,0,'J',0); 
	for ($i=0; $i < 4; $i++) { 
		if ($i < 3) {
			$pdf->Cell(3.75,0.5,$property[$i]['Area2'],1,0,'C',0); 
		}else{
			$pdf->Cell(3.75,0.5,$property[$i]['Area2'],1,1,'C',0); 
		}
		
	}

	$pdf->setX($tmpX);
	$pdf->Cell(1.6,0.5,"街 路",1,0,'J',0); 
	for ($i=0; $i < 4; $i++) { 
		if ($i < 3) {
			$pdf->Cell(3.75,0.5,$property[$i]['AddrRoad'],1,0,'C',0); 
		}else{
			$pdf->Cell(3.75,0.5,$property[$i]['AddrRoad'],1,1,'C',0); 
		}
		
	}

	// $pdf->Cell(3.75,0.5,"",1,0,'J',0); 
	// $pdf->Cell(3.75,0.5,"",1,0,'J',0); 
	// $pdf->Cell(3.75,0.5,"",1,0,'J',0); 
	// $pdf->Cell(3.75,0.5,"",1,1,'J',0); 

	$pdf->setX($tmpX);
	$pdf->Cell(1.6,0.5,"段 巷 弄",1,0,'J',0); 
	for ($i=0; $i < 4; $i++) { 
		if ($i < 3) {
			$pdf->Cell(3.75,0.5,$property[$i]['AddrSec'],1,0,'C',0); 
		}else{
			$pdf->Cell(3.75,0.5,$property[$i]['AddrSec'],1,1,'C',0); 
		}
		
	}

	$pdf->setX($tmpX);
	$pdf->Cell(1.6,0.5,"號 樓",1,0,'J',0); 
	for ($i=0; $i < 4; $i++) { 
		if ($i < 3) {
			$pdf->Cell(3.75,0.5,$property[$i]['no'],1,0,'C',0); 
		}else{
			$pdf->Cell(3.75,0.5,$property[$i]['no'],1,1,'C',0); 
		}
		
	}

	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->setX($tmpX2);
	$pdf->MultiCell(0.7, 1.5, "(3)\r\n建\r\n物\r\n坐\r\n落", 1, 'L', 0, 0);

	$pdf->SetFont('msungstdlight', 'B', 8);
	$tmpX = $pdf->getX();
	$pdf->Cell(1.6,0.5,"段",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,1,'C',0);

	$pdf->setX($tmpX);
	$pdf->Cell(1.6,0.5,"小 段",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,1,'C',0);

	$pdf->setX($tmpX);
	$pdf->Cell(1.6,0.5,"地 號",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,"",1,1,'C',0);

	$pdf->setX($tmpX2);
	$pdf->MultiCell(0.7, 4.5, "(4)\r\n面\r\n積\r\n(平\r\n方\r\n公\r\n尺)", 1, 'L', 0, 0);
	$tmpX = $pdf->getX();
	
	$propertyobject = getPropertyObject($property[0]['cCertifiedId'],'',1);
	
	for ($j=0; $j < 8; $j++) { 
		
		$Measure0 = ($propertyobject[$j]['cBuildItem'] == 0) ? $propertyobject[$j]['cMeasureTotal'] : '';
		$Measure1 = ($propertyobject[$j]['cBuildItem'] == 1) ? $propertyobject[$j]['cMeasureTotal'] : '';
		$Measure2 = ($propertyobject[$j]['cBuildItem'] == 2) ? $propertyobject[$j]['cMeasureTotal'] : '';
		$Measure3 = ($propertyobject[$j]['cBuildItem'] == 3) ? $propertyobject[$j]['cMeasureTotal'] : '';
	
		if ($j == 0 ) {
			$tmpY = $pdf->getY();
	
			$pdf->setY($tmpY);
			$pdf->setX($tmpX);
			$pdf->Cell(1.6,0.5,$propertyobject[$j]['cLevelUse'],1,0,'C',0);
			$pdf->Cell(3.75,0.5,$Measure0,1,0,'C',0); 
			$pdf->Cell(3.75,0.5,$Measure1,1,0,'C',0);
			$pdf->Cell(3.75,0.5,$Measure2,1,0,'C',0);
			$pdf->Cell(3.75,0.5,$Measure3,1,1,'C',0);
			
		}else{

			$pdf->setX($tmpX);
			$pdf->Cell(1.6,0.5,$propertyobject[$j]['cLevelUse'],1,0,'C',0); 
			
			$pdf->Cell(3.75,0.5,$Measure0,1,0,'C',0); 
			$pdf->Cell(3.75,0.5,$Measure1,1,0,'C',0);
			$pdf->Cell(3.75,0.5,$Measure2,1,0,'C',0);
			$pdf->Cell(3.75,0.5,$Measure3,1,1,'C',0);
			 
		}

		$MeasureTotal0 += $Measure0;
		$MeasureTotal1 += $Measure1;
		$MeasureTotal2 += $Measure2;
		$MeasureTotal3 += $Measure3;
	}
	unset($propertyobject);
	$MeasureTotal0 = ($MeasureTotal0 == 0)? '' : $MeasureTotal0;
	$MeasureTotal1 = ($MeasureTotal1 == 0)? '' : $MeasureTotal1;
	$MeasureTotal2 = ($MeasureTotal2 == 0)? '' : $MeasureTotal2;
	$MeasureTotal3 = ($MeasureTotal3 == 0)? '' : $MeasureTotal3;

	
	$pdf->setX($tmpX);
	$pdf->Cell(1.6,0.5,"共 計",1,0,'C',0); 
	$pdf->Cell(3.75,0.5,$MeasureTotal0,1,0,'C',0); 
	$pdf->Cell(3.75,0.5,$MeasureTotal1,1,0,'C',0); 
	$pdf->Cell(3.75,0.5,$MeasureTotal2,1,0,'C',0); 
	$pdf->Cell(3.75,0.5,$MeasureTotal3,1,1,'C',0);
	unset($MeasureTotal0);unset($MeasureTotal1);unset($MeasureTotal2);unset($MeasureTotal3);

	$propertyobject = getPropertyObject($property[0]['cCertifiedId'],'',2);
	

	for ($j=0; $j < count($propertyobject); $j++) { 
		$LevelUse0[] = ($propertyobject[$j]['cBuildItem'] == 0) ? $propertyobject[$j]['cLevelUse'] : '';
		$LevelUseMeasure0[] = ($propertyobject[$j]['cBuildItem'] == 0) ? $propertyobject[$j]['cMeasureTotal'] : '';

		$LevelUse1[] = ($propertyobject[$j]['cBuildItem'] == 1) ? $propertyobject[$j]['cLevelUse'] : '';
		$LevelUseMeasure1[] = ($propertyobject[$j]['cBuildItem'] == 1) ? $propertyobject[$j]['cMeasureTotal'] : '';

		$LevelUse2[] = ($propertyobject[$j]['cBuildItem'] == 2) ? $propertyobject[$j]['cLevelUse'] : '';
		$LevelUseMeasure2[] = ($propertyobject[$j]['cBuildItem'] == 2) ? $propertyobject[$j]['cMeasureTotal'] : '';

		$LevelUse3[] = ($propertyobject[$j]['cBuildItem'] == 3) ? $propertyobject[$j]['cLevelUse'] : '';
		$LevelUseMeasure3[] = ($propertyobject[$j]['cBuildItem'] == 3) ? $propertyobject[$j]['cMeasureTotal'] : '';
	}


	$pdf->setX($tmpX2);
	$pdf->MultiCell(0.7, 2, "(5)\r\n附\r\n屬\r\n建\r\n物", 1, 'L', 0, 0);
	$tmpX = $pdf->getX();
	$pdf->Cell(1.6,1,"用 途",1,0,'C',0); 
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUse0),1,0,'J',0); 
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUse1),1,0,'J',0); 
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUse2),1,0,'J',0); 
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUse3),1,1,'J',0);

	$pdf->setX($tmpX);
	$pdf->MultiCell(1.6, 1, "面 積\r\n(平方公尺)", 1, 'C', 0, 0);
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUseMeasure0),1,0,'J',0); 
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUseMeasure1),1,0,'J',0); 
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUseMeasure2),1,0,'J',0); 
	$pdf->Cell(3.75,1,@implode("\r\n", $LevelUseMeasure3),1,1,'J',0);

	unset($LevelUse0);unset($LevelUseMeasure0);unset($LevelUse1);unset($LevelUseMeasure1);
	unset($LevelUse2);unset($LevelUseMeasure2);unset($LevelUse3);unset($LevelUseMeasure3);


	$pdf->setX($tmpX2);
	$pdf->Cell(2.3,0.5,"(6)權利範圍",1,0,'J',0);
	$tmpX = $pdf->getX();

	for ($i=0; $i < 4; $i++) { 
		$Power = ($property[$i]['cPower1'] == 1 && $property[$i]['cPower2'] == 1)? '全部':$property[$i]['cPower1']."/".$property[$i]['cPower2'] ;
		$Power = ($Power == '/')?'':$Power;
		if ($i < 3) {
			$pdf->Cell(3.75,0.5,$Power,1,0,'C',0); 
		}else{
			$pdf->Cell(3.75,0.5,$Power,1,1,'C',0); 
		}
		
	}

	$pdf->setX($tmpX2);
	
	$pdf->Cell(17.3,0.5,"(7)買賣價款總金額:新台幣".NumtoStr($income['cTotalMoney'])."元整",1,1,'L',0);
	##
	$tmpY = $pdf->getY()+0.5;
	$pdf->setY($tmpY);
	$pdf->MultiCell(0.5, 4.5, "(8)\r\n申\r\n請\r\n登\r\n記\r\n以\r\n外\r\n之\r\n約\r\r定\r\n事\r\n項", 1, 'L', 0, 0);
	$tmpX = $pdf->getX();
	$pdf->Cell(8.5,0.5,"1.他項權利情形：",$border,1,'L',0); 
	$pdf->setX($tmpX);
	$pdf->Cell(8.5,4,"",$border3,0,'L',0); 

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

	$pdf->Cell(2.2, 1,"買受人",1,0,'C',0);//買受人或出賣人
	$pdf->Cell(2.2, 1,$buyer['cName'],1,0,'C',0);//姓名
	// $pdf->Cell(1.1, 1,"詳見標示權力範圍",1,0,'C',0);//權利範圍
	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->MultiCell(1.1, 1, "詳見標示\r\n權力範圍", 1, 'C', 0, 0);
	$pdf->Cell(1.1, 1,"",1,0,'C',0);
	$pdf->Cell(1.8, 1,"民國".$buyer['cBirthdayDayYear']."年".$buyer['cBirthdayDayMonth']."月".$buyer['cBirthdayDayMonth']."日",1,0,'C',0);
	$pdf->SetFont('msungstdlight', 'B', 8);
	$pdf->Cell(2, 1,$buyer['cIdentifyId'],1,0,'C',0);
	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->Cell(6, 1,$buyer['cRegistZip'].$buyer['RegistArea'].$buyer['cRegistAddr'],1,1,'L',0);


	$pdf->setX($tmpX2);
	$pdf->SetFont('msungstdlight', 'B', 8);
	$pdf->Cell(2.2, 1,"出賣人",1,0,'C',0);//買受人或出賣人
	$pdf->Cell(2.2, 1,$owner['cName'],1,0,'C',0);//姓名
	// $pdf->Cell(1.1, 1,"詳見標示權力範圍",1,0,'C',0);//權利範圍
	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->MultiCell(1.1, 1, "詳見標示\r\n權力範圍", 1, 'C', 0, 0);
	$pdf->Cell(1.1, 1,"",1,0,'C',0);
	$pdf->Cell(1.8, 1,"民國".$owner['cBirthdayDayYear']."年".$owner['cBirthdayDayMonth']."月".$owner['cBirthdayDayMonth']."日",1,0,'C',0);
	$pdf->SetFont('msungstdlight', 'B', 8);
	$pdf->Cell(2, 1,$owner['cIdentifyId'],1,0,'C',0);
	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->Cell(6, 1,$owner['cRegistZip'].$owner['RegistArea'].$owner['cRegistAddr'],1,1,'L',0);


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
}
##


##


// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')

// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
?>