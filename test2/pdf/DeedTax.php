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




for ($i=0; $i < count($property); $i++) { 
	
	$propertyobject = getPropertyObject($property[$i]['cCertifiedId'],$property[$i]['cBuildItem']);
	getDeedTaxForm($cId,$property[$i],$today,$propertyobject);
	$pdf->Output() ;
	die;
}

function getDeedTaxForm($cId,$arr,$today,$propertyobject){
	global $pdf;
	global $owner;
	global $buyer;
	global $scrivener;
	global $land;
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
	##
	$pdf->AddPage();
	$pdf->SetFont('msungstdlight', 'B', 6);
	$txt = "＊檔 案 編 號";
	$pdf->Cell(5.6,'',$txt,1,1,'J',0);


	$txt = "  　年  　月";
	$pdf->Cell(1.6,0.8,$txt,1,0,'R',0);

	$tmpX = $pdf->getX();
	$tmpY = $pdf->getY();

	$txt = "資　料　號　碼";
	$pdf->Cell(4,0.4,$txt,1,0,'C',0);

	$pdf->setY($tmpY+0.4);
	$pdf->setX($tmpX);
	$txt = "服務區";
	$pdf->Cell(0.8,0.4,$txt,1,0,'C',0);

	$txt = "總分區";
	$pdf->Cell(0.8,0.4,$txt,1,0,'C',0);

	$txt = "流水號";
	$pdf->Cell(2.4,0.4,$txt,1,1,'C',0);

	$pdf->Cell(1.6,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);
	$pdf->Cell(0.4,0.2,'',1,0,'C',0);


	$pdf->SetFont('msungstdlight', 'B', 14);
	$pdf->setY($tmpY);
	$pdf->setX(8);
	$pdf->Cell(5,'','契稅申報書',0,0,'C',0);

	$pdf->SetFont('msungstdlight', 'B', 6);
	$tmpX = 15;
	$tmpY2 = $tmpY;
	$pdf->setY($tmpY2);
	$pdf->setX($tmpX);


	$pdf->Cell(0.8,0.25,'＊',$border,1,'C',0);

	$tmpY2+=0.25;
	$pdf->setY($tmpY2);
	$pdf->setX($tmpX);
	$pdf->Cell(0.8,0.25,'總',$border2,1,'C',0);

	$tmpY2+=0.25;
	$pdf->setY($tmpY2);
	$pdf->setX($tmpX);
	$pdf->Cell(0.8,0.25,'收',$border2,1,'C',0);


	$tmpY2+=0.25;
	$pdf->setY($tmpY2);
	$pdf->setX($tmpX);
	$pdf->Cell(0.8,0.35,'文',$border3,1,'C',0);

	$tmpX +=0.8;

	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(0.8,0.5,'日期',1,0,'C',0);
	$pdf->Cell(3,0.5,'  年  月  日',1,1,'J',0);

	$pdf->setY(($tmpY+0.5));
	$pdf->setX($tmpX);

	$pdf->Cell(0.8,0.6,'字號',1,0,'C',0);
	$pdf->Cell(3,0.6,'',1,1,'J',0);
	###

	// $pdf->Cell(3,0.52,'(1)',1,1,'J',0);
	$tmpY = 2.38;
	$pdf->setY($tmpY);
	$pdf->MultiCell(1.6, 0.8, "(1)房屋\r\n稅籍編號", 1, 'C', 0, 0);

	$pdf->SetFont('msungstdlight', 'B', 4);
	$tmpX = $pdf->getX();
	// 鄉鎮市區
	$pdf->MultiCell(0.8, 0.4,"鄉鎮\r\n市區", 1, 'C', 0, 0);
	// 村里
	$pdf->Cell(1, 0.4,'村里',1,0,'C',0);
	// 冊頁(棟)
	$pdf->Cell(1.2, 0.4,'冊頁(棟)',1,0,'C',0);
	//分戶號
	$pdf->Cell(1, 0.4,'分戶號',1,1,'C',0);


	$pdf->setX($tmpX);
	$pdf->Cell(4,0.4,'',1,0,'C',0);
	

	$pdf->SetFont('msungstdlight', 'B', 6);
	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(1.5,0.8,"(2)建號",1,0,'C',0);//(2)建號 段      小段  建號


	$tmpX = $pdf->getX();
	$pdf->Cell(3,0.4,"      段      小段",$border,1,'J',0);    

	$pdf->setX($tmpX);
	$pdf->Cell(3,0.4,$arr['cBuildNo'],$border3,0,'L',0); 

	$tmpX = $pdf->getX();

	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->MultiCell(1.7, 0.8, "(3)移　　轉\r\n房屋坐落", 1, 'C', 0, 0);
	$pdf->SetFont('msungstdlight', 'B', 8);
	$pdf->MultiCell(6.3, 0.8, $arr['cZip'].$arr['Area'].$arr['cAddr'], 1, 'C', 0, 1);

	#######
	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->MultiCell(4.6, 0.6, "(4)立契日期或使用執照核發日期（限\r\n房屋建造完成前取得所有權案件）", 1, 'C', 0, 0);

	$tmpY = $pdf->GetY();

	$pdf->SetFont('msungstdlight', 'B', 4);
	$tmpX = $pdf->getX();
	// 年
	$pdf->Cell(1, 0.3,'年',1,0,'C',0);
	// 月
	$pdf->Cell(1, 0.3,'月',1,0,'C',0);
	// 日
	$pdf->Cell(1, 0.3,'日',1,0,'C',0);
	//□1.一般申報案件
	$pdf->SetFont('msungstdlight', 'B', 6);
	$txt = "□1.一般申報案件";
	$pdf->Cell(5.5, 0.3,$txt,$border,1,'L',0);


	$pdf->setX($tmpX);
	$pdf->Cell(1,0.3,$arr['cBuildDateYear'],1,0,'C',0);// 年
	
	$pdf->Cell(1,0.3,$arr['cBuildDateMonth'],1,0,'C',0);// 月

	$pdf->Cell(1,0.3,$arr['cBuildDateDay'],1,0,'C',0);// 日

	$txt = "□ 2.房屋建造完成前取得所有權案件";
	$pdf->Cell(5.5, 0.3,$txt,$border3,0,'L',0);

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(2,0.6,'(5)申報日期',1,0,'C',0);// 年
	$tmpX = $pdf->getX();
	// 年
	$pdf->Cell(1, 0.3,'年',1,0,'C',0);
	// 月
	$pdf->Cell(1, 0.3,'月',1,0,'C',0);
	// 日
	$pdf->Cell(1, 0.3,'日',1,1,'C',0);

	$pdf->setX($tmpX);
	$pdf->Cell(1,0.3,$today['year'],1,0,'C',0);// 年

	$pdf->Cell(1, 0.3,$today['month'],1,0,'C',0);// 月
	
	$pdf->Cell(1,0.3,$today['day'],1,1,'C',0);// 日
	

	##
	//
	$pdf->MultiCell(2.1, 0.6, "(6)移轉價格\r\n(新臺幣)", 1, 'C', 0, 0);

	
	$pdf->Cell(5.5,0.6,'元',1,0,'C',0);//





	$tmpX = $pdf->getX();
	$txt = "1.□請按照評定標準價格核課契稅。";
	$pdf->Cell(10.5, 0.3,$txt,$border,1,'L',0);


	$pdf->setX($tmpX);

	$txt = "2.□本件係領買標購公產或法院拍賣案件請按照評定標準價格或申報移轉價格從低核課契稅。";
	$pdf->Cell(10.5, 0.3,$txt,$border3,1,'L',0);

	##
	// (7)茲委託　丁小一	先生
	//                      女士	代辦契稅申報、領取契稅繳款書或免稅證明書、前業主應繳未繳之房屋稅繳款書及領回證件等事項。
	$tmpY = $pdf->getY();
	$pdf->Cell(3, 0.6,'(7)茲委託',$border4,0,'L',0);

	$tmpX = $pdf->getX();
	$pdf->Cell(0.6, 0.3,'先生',"T",1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(0.6, 0.3,'小姐',"B",0,'L',0);

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(14.5,0.6,"代辦契稅申報、領取契稅繳款書或免稅證明書、前業主應繳未繳之房屋稅繳款書及領回證件等事項。",$border6,1,'C',0);//(2)建號 段      小段  建號
	##
	//(8)原所有權人
	$pdf->Cell(1.5, 1.8,'(8)原所有權人',1,0,'L',0);

	//
	$tmpY = $pdf->getY();
	$tmpX = $pdf->getX();
	$pdf->Cell(2, 0.6,"姓名或名稱",1,0,'L',0);
	$pdf->Cell(1, 0.6,"蓋章",1,0,'L',0);
	$pdf->MultiCell(2.1, 0.6, "國民身分證或事業\r\n機關團體統一編號", 1, 'C', 0, 0);
	$pdf->MultiCell(0.7, 0.6, "身分\r\n代號", 1, 'C', 0, 0);
	$pdf->MultiCell(0.7, 0.6, "公私\r\n有別", 1, 'C', 0, 0);
	$pdf->Cell(7.1, 0.6,"戶  籍  地  址",1,0,'C',0);
	$pdf->MultiCell(1.5, 0.6, "權利範圍\r\n持分比率", 1, 'C', 0, 0);
	$pdf->MultiCell(1.5, 0.6, "※房屋稅\r\n查欠情形", 1, 'C', 0, 1);

	//
	$pdf->setX($tmpX);
	$pdf->Cell(2, 0.6,$owner['cName'],1,0,'L',0);
	$pdf->Cell(1, 0.6,"",1,0,'L',0);

	$tmpY2 = $pdf->getY();
	$tmpX2 = $pdf->getX();
	$pdf->Cell(2.1, 0.3,$owner['cIdentifyId'],1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2.1, 0.3,"",1,0,'L',0);

	$pdf->setY($tmpY2);
	$pdf->setX($tmpX2+2.1);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(7.1, 0.6,$owner['cRegistZip'].$owner['RegistArea'].$owner['cRegistAddr'],1,0,'L',0);
	$X = $pdf->getX();
	$Y = $pdf->getY();
	$pdf->Cell(1.5, 0.6,"",1,1,'C',0);


	//////

	$pdf->setX($tmpX);
	$pdf->Cell(2, 0.6,"",1,0,'L',0);
	$pdf->Cell(1, 0.6,"",1,0,'L',0);

	$tmpY2 = $pdf->getY();
	$tmpX2 = $pdf->getX();
	$pdf->Cell(2.1, 0.3,"",1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2.1, 0.3,"",1,0,'L',0);


	$pdf->setY($tmpY2);
	$pdf->setX($tmpX2+2.1);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(7.1, 0.6,"",1,0,'C',0);

	$pdf->Cell(1.5, 0.6,"",1,1,'C',0);
	// $pdf->Cell(1.5, 0.6,"",1,1,'C',0);



	##
	$pdf->Cell(1.5, 1.2,'(9)新所有權人',1,0,'L',0);
	//
	$tmpY = $pdf->getY();
	$tmpX = $pdf->getX();

	$pdf->setX($tmpX);
	$pdf->Cell(2, 0.6,$buyer['cName'],1,0,'L',0);
	$pdf->Cell(1, 0.6,"",1,0,'L',0);

	$tmpY2 = $pdf->getY();
	$tmpX2 = $pdf->getX();
	$pdf->Cell(2.1, 0.3,$buyer['cIdentifyId'],1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2.1, 0.3,"",1,0,'L',0);

	$pdf->setY($tmpY2);
	$pdf->setX($tmpX2+2.1);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(7.1, 0.6,$buyer['cRegistZip'].$buyer['RegistArea'].$buyer['cRegistAddr'],1,0,'L',0);
	$pdf->Cell(1.5, 0.6,"",1,1,'C',0);
	// $pdf->Cell(1.5, 0.6,"",1,1,'C',0);

	//////
	$pdf->setX($tmpX);
	$pdf->Cell(2, 0.6,"",1,0,'L',0);
	$pdf->Cell(1, 0.6,"",1,0,'L',0);

	$tmpY2 = $pdf->getY();
	$tmpX2 = $pdf->getX();
	$pdf->Cell(2.1, 0.3,"",1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2.1, 0.3,"",1,0,'L',0);

	$pdf->setY($tmpY2);
	$pdf->setX($tmpX2+2.1);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(7.1, 0.6,"",1,0,'C',0);
	$pdf->Cell(1.5, 0.6,"",1,1,'C',0);

	##
	$pdf->Cell(1.5, 0.6,'(10)契稅代理人',1,0,'L',0);
	//
	$tmpY = $pdf->getY();
	$tmpX = $pdf->getX();

	$pdf->setX($tmpX);
	$pdf->Cell(2, 0.6,$scrivener['sName'],1,0,'L',0);
	$pdf->Cell(1, 0.6,"",1,0,'L',0);

	$tmpY2 = $pdf->getY();
	$tmpX2 = $pdf->getX();
	$pdf->Cell(2.1, 0.3,$scrivener['sIdentifyId'],1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2.1, 0.3,"",1,0,'L',0);

	$pdf->setY($tmpY2);
	$pdf->setX($tmpX2+2.1);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(7.1, 0.6,$scrivener['sCpZip1'].$scrivener['Area'].$scrivener['sCpAddress'],1,0,'L',0);
	// $pdf->Cell(1.5, 0.6,"",1,1,'C',0);
	$pdf->MultiCell(1.5, 0.6, "領回證\r\n件蓋章", 1, 'C', 0, 1);
	// $pdf->Cell(1.5, 0.6,"",1,1,'C',0);

	##
	$pdf->SetFont('msungstdlight', 'B', 4);
	$pdf->MultiCell(1.5, 0.6, "(11)在國內房屋稅\r\n納稅代理人", 1, 'C', 0, 0);
	//
	$tmpY = $pdf->getY();
	$tmpX = $pdf->getX();

	$pdf->setX($tmpX);
	$pdf->Cell(2, 0.6,"",1,0,'L',0);
	$pdf->Cell(1, 0.6,"",1,0,'L',0);

	$tmpY2 = $pdf->getY();
	$tmpX2 = $pdf->getX();
	$pdf->Cell(2.1, 0.3,"",1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2.1, 0.3,"",1,0,'L',0);

	$pdf->setY($tmpY2);
	$pdf->setX($tmpX2+2.1);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(0.7, 0.6,"",1,0,'L',0);
	$pdf->Cell(7.1, 0.6,"",1,0,'C',0);
	$pdf->Cell(1.5, 0.6,"",1,0,'C',0);

	$pdf->setY($Y);
	$pdf->SetX($X+1.5);
	$pdf->Cell(1.5, 3.6,"",1,1,'C',0);
	##
	$pdf->SetFont('msungstdlight', 'B', 6);
	$tmpX = $pdf->getX();
	$tmpX2 = $tmpX;
	$txt = "(12)契約種類:□1.買賣□2.典權□3.交換";
	$pdf->Cell(3.8, 0.9,$txt,$border4,0,'L',0);
	$tmpX = $pdf->getX();
	
	$pdf->setX(($tmpX2+1.23));
	$pdf->Cell(0.3, 0.8, "v", 0,  0,'C', 0);
	

	// $txt = "□4.0贈與\r\n□4.1夫妻贈與";
	// $pdf->MultiCell(1.6, 1,$txt, $border5, 'L', 0, 0);
	$pdf->setX($tmpX);
	$tmpY = $pdf->getY();
	$pdf->Cell(1.5, 0.45,"□4.0贈與","T",1,'L',0);

	$pdf->SetX($tmpX);
	$pdf->Cell(1.5, 0.45,"□4.1夫妻贈與","B",0,'L',0);


	$pdf->setY($tmpY);
	$pdf->setX($tmpX+1.5);
	$txt = "□7.二等親間買賣";
	$pdf->Cell(2, 0.9,$txt,$border5,0,'L',0);
	// $pdf->MultiCell(1.6, 0.9,$txt, $border5, 'R', 0, 0);

	$tmpX = $pdf->getX();
	$tmpY = $pdf->getY();
	$pdf->Cell(1.5, 0.3,"□8.1標購","T",1,'L',0);
	$pdf->SetX($tmpX);
	$pdf->Cell(1.5, 0.3,"□8.2拍賣","0",1,'L',0);
	$pdf->SetX($tmpX);
	$pdf->Cell(1.5, 0.3,"□8.3領買","B",0,'L',0);

	$pdf->setY($tmpY);
	$pdf->setX($tmpX+1.5);
	$pdf->Cell(7.8, 0.9,"",$border6,0,'L',0);

	$pdf->MultiCell(1.5, 0.9, "※查欠人員\r\n蓋　　章", 1, 'C', 0, 1);
	##

	$pdf->Cell(1.5, 1.1,"(13)移轉情形 ",1,0,'L',0);

	//propertyobject
	$tmpY = $pdf->getY();
	$tmpX = $pdf->getX();
	$Y = $tmpY ;

	$pdf->Cell(1.2, 0.2,"層    次",1,0,'C',0);
	for ($i=0; $i < 8; $i++) { 

		$category = ($propertyobject[$i]['cCategory'] == 3)? '':$propertyobject[$i]['Category'];
		$pdf->Cell(1, 0.2,$category,1,0,'L',0);
		unset($category);
	}
	
	// $pdf->Cell(1, 0.2,"",1,0,'L',0);
	// $pdf->Cell(1, 0.2,"",1,0,'L',0);
	// $pdf->Cell(1, 0.2,"",1,0,'L',0);
	// $pdf->Cell(1, 0.2,"",1,0,'L',0);
	// $pdf->Cell(1, 0.2,"",1,0,'L',0);
	// $pdf->Cell(1, 0.2,"",1,0,'L',0);
	// $pdf->Cell(1, 0.2,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.2,"公設建號",1,0,'C',0);
	$pdf->Cell(1, 0.2,"",1,0,'L',0);
	$pdf->Cell(1, 0.2,"",1,0,'L',0);
	$pdf->Cell(1, 0.2,"",1,1,'L',0);


	$pdf->setX($tmpX);
	$pdf->Cell(1.2, 0.2,"構    造",1,0,'C',0);
	// for ($i=0; $i < 8; $i++) { 

	// 	$BudMaterial =  ($propertyobject[$i]['cCategory'] == 3)? '':$arr['BudMaterial'];
		
	// 	$pdf->Cell(1, 0.2,$BudMaterial,1,0,'L',0);
	// }
	$pdf->Cell(8, 0.2,$arr['BudMaterial'],1,0,'L',0);

	$pdf->writeHTMLCell(1.5, 0.2,'', '', "面積（m<sup>2</sup>）", 1, 0, 0, true, C);
	$pdf->Cell(1, 0.2,"",1,0,'L',0);
	$pdf->Cell(1, 0.2,"",1,0,'L',0);
	$pdf->Cell(1, 0.2,"",1,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->MultiCell(1.2, 0.2, "面積\r\n(平方公尺)", 1, 'C', 0, 0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	// $pdf->Cell(1, 0.55,"",1,0,'L',0);
	for ($i=0; $i < 8; $i++) { 

		$cMeasureMain = ($propertyobject[$i]['cCategory'] == 3 || $propertyobject[$i]['cCategory'] == 0)? '':$propertyobject[$i]['cMeasureMain'];
		$pdf->Cell(1, 0.2,$cMeasureMain,1,0,'L',0);
		unset($cMeasureMain);
	}
	$pdf->Cell(1.5, 0.55,"持分比例",1,0,'C',0);
	$pdf->Cell(1, 0.55,"",1,0,'L',0);
	$pdf->Cell(1, 0.55,"",1,0,'L',0);
	$pdf->Cell(1, 0.55,"",1,0,'L',0);

	$tmpX = $pdf->getX();

	$pdf->setY($Y);
	$pdf->setX($tmpX);
	$pdf->MultiCell(1.4, 1.1, "□未辦保存登記部分一併移轉", 1, 'L', 0, 0);
	$pdf->Cell(1.5, 1.1,"",1,1,'L',0);

	##
	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->MultiCell(1.5, 0.6, "(14)申請減免\r\n項目", 1, 'C', 0, 0);

	$tmpX = $pdf->getX();
	$tmpY = $pdf->getY();
	$pdf->Cell(1.2, 0.3,"□ 1.全免",$border,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(1.2, 0.3,"□ 2.減徵",$border3,0,'L',0);

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);

	$pdf->Cell(12.5, 0.3,"合於契稅條例第(　　)條規定。",$border,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(12.5, 0.3,"合於(　　)條例第(　　)條(　　)款規定。",$border3,0,'L',0);

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->MultiCell(1.4, 0.6, "*□准\r\n  □不准", 1, 'L', 0, 0);

	$pdf->MultiCell(1.5, 0.6, "※房屋稅承辦\r\n人員蓋章", 1, 'L', 0, 1);
	##
	// 
	$pdf->Cell(1.5, 0.9,"(15)檢　　附",1,0,'L',0);

	$tmpX = $pdf->getX();
	$tmpY = $pdf->getY();
	$pdf->Cell(15.1, 0.3,"□契約書正本(查驗後退還)、影本（貼印花部分）1份，　□所有權狀影本共(　　)份。",$border,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(15.1, 0.3,"□不動產權利移轉證明書影本1份，□法院判決書及判決確定證明書影本( 　 )份。",$border2,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(15.1, 0.3,"□身分證明文件影本(   　)份，□該移轉房屋已納房屋稅繳款書影本1份　□其他文件(　　　) 份。",$border3,0,'L',0);

	$tmpX = $pdf->getX();
	$tmpY2 = $pdf->getY();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(1.5, 2.1,"",1,1,'L',0);

	##
	$pdf->setY($tmpY2+0.3);
	$pdf->MultiCell(16.6, 0.3, "(16)移轉後房屋稅繳款書寄送地址:", 1, 'L', 0, 1);
	##

	$pdf->Cell(1.5, 0.9,"(17)※怠報日數",1,0,'L',0);
	$pdf->Cell(3, 0.9,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.9,"(18)備註",1,0,'L',0);

	$tmpX = $pdf->GetX();

	$pdf->Cell(10.6, 0.3,"本處已提共契稅申報書案件結案語音回撥服務，您申報之契稅案件於核單完成時，",$border,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(10.6, 0.3,"□需要語音回撥服務",$border2,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(10.6, 0.3,"□不需要語音回撥服務",$border3,1,'L',0);
	##
	$tmpX = $pdf->GetX();
	$pdf->Cell(18.1, 0.3,"茲依照契稅條例第14條、第15條、第16條規定填具契稅申報書，請依法核定應納契稅，並依照房屋稅條例第7條之規定申請變更房屋稅納稅義務人名義。",$border,1,'L',0);

	$pdf->MultiCell(3, 0.9, "此　　致\r\n（稽徵機關全銜）\r\n鄉(鎮、市)公所", $border7, 'C', 0, 0);

	$pdf->Cell(5, 0.9,"","B",0,'L',0);


	$tmpX = $pdf->GetX();
	$pdf->Cell(10.1, 0.3,"※申報人為公司行號或機關團體除蓋印鑑章外，並須由負責人或代表人簽章","R",1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(10.1, 0.3,"(未辦建物所有權第1次登記之房屋移轉，雙方均需簽名或蓋章)","R",1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(10.1, 0.3,"申報人:".$buyer['cName'],$border8,1,'L',0);
	##
	$pdf->Cell(16.5, 0.3,"一、本表有※之欄位請免填。",0,1,'L',0);
	##
	$pdf->MultiCell(16.5, 0.3, "二、申報人取得房屋，請依附聯填報不動產移轉後使用情形，其供作自用住宅用地使用者，並可據以適用自用住宅用地稅率課徵地價稅及適用住家用房屋稅稅率改課房屋稅，請確實填報，以維護您的權益。", 0, 'L', 0, 1);
	##
	$bb = array(
			'T' => array('width' => 0.01, 'color' => array(0,0,0),'dash' => 1),
		);
	$pdf->Cell(18.1, 0.3,"",$bb,1,'L',0);
	unset($bb);
	##
	$bb = array(
			'T' => array('width' => 0.01, 'color' => array(0,0,0),'dash' => 0),
			'L' => array('width' => 0.01, 'color' => array(0,0,0),'dash' => 0),
			'R' => array('width' => 0.01, 'color' => array(0,0,0),'dash' => 0),
			'B' => array('width' => 0.01, 'color' => array(0,0,0),'dash' => 0),
		);
	$pdf->MultiCell(2, 0.6, "□已輔導\r\n□無須申報\r\n契稅申報書附聯", $bb, 'L', 0, 0);

	$pdf->SetFont('msungstdlight', 'B', 8);
	$tmpX = $pdf->getX()+6;
	$tmpX1 = $pdf->getX()+3;
	$tmpX2 = $pdf->getX();
	$pdf->setX($tmpX);
	$pdf->Cell(3, 0.3,"契稅申報書附聯",0,1,'C',0);

	$pdf->SetFont('msungstdlight', 'B', 6);
	$pdf->setX($tmpX);
	$pdf->Cell(3, 0.3,"(不動產移轉後使用情形申報表)",0,1,'L',0);

	$pdf->setX($tmpX1);
	$pdf->Cell(3, 0.3,"一、土地部分     年     月     日",0,1,'L',0);

	$pdf->setX($tmpX1);
	$land[0]['cLand2'] = ($land[0]['cLand2'] =='')?  '     ':$land[0]['cLand2'];
	$land[0]['cLand3'] = ($land[0]['cLand3'] =='')?  '     ':$land[0]['cLand3'];
	$txt = "本申報書所列房屋基地".$land[0]['cLand1']."段".$land[0]['cLand2']."小段".$land[0]['cLand3']."地號土地取得後係供自用住宅用地使用，茲先行提出申請按";
	$txt .= "自用住宅用地稅率課徵地價稅，俟辦妥土地所有權移轉登記並於本年9月22日前辦竣戶籍登記後，再補送有關文件";
	$txt .= "，請准自本年起按自用住宅用地稅率課徵地價稅。";
	$pdf->MultiCell(9, 0.3, $txt, 1, 'L', 0, 1);

	$pdf->setX($tmpX1);
	$pdf->Cell(3, 0.3,"二、房屋部分(稅籍編號:              )(□請依持分比例分單繳納)",0,1,'L',0);

	$pdf->setX($tmpX1);
	$pdf->Cell(2, 0.3,"坐落:".$arr['cZip'].$arr['Area'].$arr['cAddr'],0,0,'L',0);
	$pdf->Cell(5, 0.3,"",0,0,'L',0);
	$pdf->Cell(3, 0.3,"房屋移轉後使用情形如下:",0,1,'L',0);

	$tmpY = $pdf->getY();
	$pdf->setX($tmpX2);

	$pdf->Cell(2, 0.3,"層次",$border,1,'R',0);
	$x = $pdf->getX()+2;

	$pdf->setX($tmpX2);
	$pdf->Cell(2, 0.3,"面積",$border2,1,'C',0);
	$pdf->setX($tmpX2);
	$pdf->Cell(2, 0.3,"使用別",$border3,0,'L',0);

	$pdf->setY($tmpY);
	$pdf->setX($x+2);

	$i = 0;
	for ($i=0; $i < 8; $i++) { 
		$levelUse = ($propertyobject[$i]['cCategory'] == 3)? '':$propertyobject[$i]['cLevelUse'];
		if ($i < 7 ) {
			$pdf->Cell(1.5, 0.9,$levelUse,1,0,'L',0);
			
		}else{
			$pdf->Cell(1.5, 0.9,$levelUse,1,1,'L',0);
		}
	
	}
	// for ($i=0; $i < 8; $i++) { 
	// 	$category = ($propertyobject[$i]['cCategory'] == 3)? '':$propertyobject[$i]['Category'];
	// 	if ($i <= 8) {
	// 		$pdf->Cell(1.5, 0.9,$category,1,0,'L',0);
	// 	}else{
	// 		$pdf->Cell(1.5, 0.9,$category,1,1,'L',0);
	// 	}

	// }



	$pdf->setX($tmpX2);
	$pdf->Cell(2, 0.5,"營業用",1,0,'C',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2, 0.5,"營業用減半",1,0,'C',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,1,'L',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(2, 0.5,"住家用",1,0,'C',0);
	// $pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	// $pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	// $pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	// $pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	// $pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	// $pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	// $pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	// $pdf->Cell(1.5, 0.5,"",1,1,'L',0);
	for ($i=0; $i < 8; $i++) { 
		$measure = ($propertyobject[$i]['cCategory'] == 3)? '':$propertyobject[$i]['cMeasureTotal'];
		if ($i < 7 ) {
			$pdf->Cell(1.5, 0.5,$measure,1,0,'L',0);
			
		}else{
			$pdf->Cell(1.5, 0.5,$measure,1,1,'L',0);
		}
	
	}

	$pdf->setX($tmpX2);
	$pdf->Cell(2, 0.5,"非住家非營業",1,0,'C',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,0,'L',0);
	$pdf->Cell(1.5, 0.5,"",1,1,'L',0);

	$pdf->setX($tmpX1);
	$pdf->Cell(2, 0.3,"三、地下室停車位部分",0,1,'C',0);//
	$pdf->setX(($tmpX1+0.2));
	$pdf->Cell(8, 0.3,"□地下室停車位共    個，係供自用停車且無出租收費情事，請准予免徵房屋稅。",0,1,'C',0);
	$pdf->setX($tmpX1);
	$pdf->Cell(1, 0.3,"申報人:".$buyer['cName'],0,0,'L',0);
	$pdf->Cell(4, 0.3,"",0,0,'L',0);
	$pdf->Cell(1, 0.3,"蓋章",0,0,'L',0);
	$pdf->Cell(2, 0.3,"",0,0,'L',0);
	$pdf->Cell(1, 0.3,"電話:",0,1,'L',0);
	$pdf->setX($tmpX1);
	$pdf->Cell(1, 0.3,"身分證統一編號:",0,1,'L',0);
	$pdf->setX($tmpX1);
	$pdf->Cell(1, 0.3,"收文    年    月    日      號",0,1,'L',0);
}






//層次
//※房屋稅承辦
//人員蓋章







// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')

// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
?>