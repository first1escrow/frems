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

getLandApply();
$pdf->Output() ;
function getLandApply(){
	global $pdf;
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

	$pdf->SetFont('msungstdlight', 'B', 8);
	$tmpYT = $pdf->getY();
	$tmpY = $tmpYT;

	$pdf->Cell(0.5,0.8,"收",$border,0,'J',0);

	$tmpX = $pdf->getX();
	$pdf->Cell(0.5,0.4,"日",$border,1,'J',0);

	$pdf->setX($tmpX);
	$pdf->Cell(0.5,0.4,"期",$border3,0,'J',0);


	$pdf->SetY($tmpY);
	$pdf->setX($tmpX+0.5);
	$pdf->Cell(4,0.8,"   年   月   日   時   分",1,0,'J',0);

	$tmpX = $pdf->getX();
	$pdf->Cell(0.5,0.4,"收",$border,1,'J',0);

	$pdf->setX($tmpX);
	$pdf->Cell(0.5,0.4,"件",$border2,0,'J',0);

	$pdf->SetY($tmpY);
	$pdf->setX($tmpX+0.5);
	$pdf->Cell(1,0.8,"",$border,1,'J',0);
	//
	$tmpY = $pdf->getY();
	$pdf->Cell(0.5,0.8,"件",$border3,0,'J',0);
	$tmpX = $pdf->getX();
	$pdf->Cell(0.5,0.4,"字",$border,1,'J',0);

	$pdf->setX($tmpX);
	$pdf->Cell(0.5,0.4,"號",$border3,0,'J',0);

	$pdf->SetY($tmpY);
	$pdf->setX($tmpX+0.5);
	$pdf->Cell(4,0.8,"    字第       號",1,0,'J',0);
	$tmpX = $pdf->getX();
	$pdf->Cell(0.5,0.4,"者",$border2,1,'J',0);

	$pdf->setX($tmpX);
	$pdf->Cell(0.5,0.4,"章",$border3,0,'J',0);
	$pdf->SetY($tmpY);
	$pdf->setX($tmpX+0.5);
	$pdf->Cell(1,0.8,"",$border3,1,'J',0);
	##

	$pdf->SetY($tmpYT);
	$pdf->setX(8.5);
	$pdf->MultiCell(1.3, 1.6, "連件序別(非連件者免填)", 1, 'L', 0, 0);
	$pdf->Cell(1.3,1.6,"共 件",1,0,'J',0);
	$pdf->Cell(1.3,1.6,"第 件",1,0,'J',0);

	###
	$tmpX = $pdf->getX()+0.5;
	$pdf->setX($tmpX);
	$pdf->Cell(1.2,0.53,"登記費",1,0,'J',0);   
	$pdf->Cell(1.8,0.53,"元",1,0,'R',0);
	$pdf->Cell(1.2,0.53,"合計",1,0,'J',0);   
	$pdf->Cell(2.3,0.53,"元",1,1,'R',0);

	$pdf->setX($tmpX);
	$pdf->Cell(1.2,0.53,"書狀費",1,0,'J',0);   
	$pdf->Cell(1.8,0.53,"元",1,0,'R',0);
	$pdf->Cell(1.2,0.53,"收據",1,0,'J',0);   
	$pdf->Cell(2.3,0.53," 字 號",1,1,'J',0);

	$pdf->setX($tmpX);
	$pdf->Cell(1.2,0.53,"罰鍰",1,0,'J',0);   
	$pdf->Cell(1.8,0.53,"元",1,0,'R',0);
	$pdf->Cell(1.2,0.53,"核算者",1,0,'J',0);   
	$pdf->Cell(2.3,0.53,"",1,1,'J',0);

	##
	$tmpY = $pdf->getY()+0.2;
	$pdf->setY($tmpY);
	$pdf->SetFont('msungstdlight', 'B', 14);
	$pdf->Cell(18,0.8,"土    地    登    記    申    請    書",1,1,'C',0); 
	##
	$pdf->SetFont('msungstdlight', 'B', 8);
	$pdf->MultiCell(1.3, 0.8, "(1)受理機關", 1, 'R', 0, 0);
	$pdf->Cell(3,0.8,"",$border4,0,'J',0); 

	$tmpY = $pdf->getY();
	$tmpX = $pdf->getX();
	$pdf->Cell(2,0.4,"",$border9,1,'J',0); 

	$pdf->setX($tmpX);
	$pdf->Cell(2,0.4,"□跨所申請",$border8,0,'R',0); 

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->MultiCell(1.3, 0.8, "資料管轄機關", 1, 'R', 0, 0);
	$pdf->Cell(4,0.8,"",1,0,'J',0); 

	$pdf->MultiCell(1.5, 0.8, "(2)原    因\r\n發生日期", 1, 'J', 0, 0);
	$pdf->Cell(4.9,0.8,"中華民國    年   月   日",1,1,'J',0);
	##
	$pdf->Cell(5,0.4,"(3)申請登記事由(選擇打V一項)",1,0,'J',0); 
	$pdf->Cell(13,0.4,"(4)登記原因(選擇打V一項)",1,1,'J',0); 
	##
	// □

	$pdf->Cell(5,0.4,"□所有權第一次登記",1,0,'L',0); 
	$pdf->Cell(13,0.4,"□第一次登記",1,1,'L',0); 
	##
	$pdf->Cell(5,0.4,"□所有權移轉登記",1,0,'L',0); 
	$pdf->Cell(13,0.4,"□買賣  □贈與  □繼承  □分割繼承  □拍賣 □共有物分割 □",1,1,'L',0); 
	##
	$pdf->Cell(5,0.4,"□抵押權登記",1,0,'L',0); 
	$pdf->Cell(13,0.4,"□設定  □法定  □",1,1,'L',0); 
	##
	$pdf->Cell(5,0.4,"□抵押權塗銷登記",1,0,'L',0); 
	$pdf->Cell(13,0.4,"□清償 □拋棄  □混同  □判決塗銷  □",1,1,'L',0); 
	##
	$pdf->Cell(5,0.4,"□抵押權內容變更登記",1,0,'L',0); 
	$pdf->Cell(13,0.4,"□權利價值變更  □權利內容等變更  □",1,1,'L',0); 
	##
	$pdf->Cell(5,0.4,"□標示變更登記",1,0,'L',0); 
	$pdf->Cell(13,0.4,"□分割  □合併  □地目變更  □",1,1,'L',0); 
	##
	$pdf->Cell(5,0.4,"□",1,0,'L',0); 
	$pdf->Cell(13,0.4,"□",1,1,'L',0); 
	##
	$pdf->Cell(4,0.4,"標示及申請權利內容",1,0,'L',0); 
	$pdf->Cell(14,0.4,"詳如   □契約書  □登記清冊  □複帳結果通知書  □建物測量成果圖  □",1,1,'L',0); 
	##
	$pdf->MultiCell(1, 2, "(6)\r\n附\r\n繳\r\n證\r\n件", 1, 'C', 0, 0); 
	$tmpX = $pdf->getX();
	$pdf->Cell(5.6,0.5,"1.        份",1,0,'J',0); 
	$pdf->Cell(5.6,0.5,"5.        份",1,0,'J',0); 
	$pdf->Cell(5.8,0.5,"9.        份",1,1,'J',0); 
	$pdf->setX($tmpX);
	$pdf->Cell(5.6,0.5,"2.        份",1,0,'J',0); 
	$pdf->Cell(5.6,0.5,"6.        份",1,0,'J',0); 
	$pdf->Cell(5.8,0.5,"10.        份",1,1,'J',0); 
	$pdf->setX($tmpX);
	$pdf->Cell(5.6,0.5,"3.        份",1,0,'J',0); 
	$pdf->Cell(5.6,0.5,"7.        份",1,0,'J',0); 
	$pdf->Cell(5.8,0.5,"11.        份",1,1,'J',0); 
	$pdf->setX($tmpX);
	$pdf->Cell(5.6,0.5,"4.        份",1,0,'J',0); 
	$pdf->Cell(5.6,0.5,"8.        份",1,0,'J',0); 
	$pdf->Cell(5.8,0.5,"12.        份",1,1,'J',0); 
	##
	$tmpY = $pdf->getY();

	$pdf->Cell(2,1.2,"(7)委任關係",1,0,'L',0); 

	$txt = "本土地登記案之申請委託    代理。    複代理。\r\n";
	$txt .= "委託人確為登記標的物之權利人或權利關係人，並經核對身分無誤，如有虛偽不實，本代理人(複代理人)願負法律責任。";
	$pdf->MultiCell(10, 1.2, $txt, 1, 'J', 0, 1); 

	$pdf->Cell(2,2,"(9)備註",1,0,'L',0); //3.2
	$pdf->Cell(10,2,"",1,0,'L',0);


	$tmpX = $pdf->GetX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);

	$pdf->MultiCell(0.5, 3.2, "(8)\r\n聯\r\n絡\r\n方\r\n式", 1, 'J', 0, 0); 

	$tmpX = $pdf->GetX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(2.2,0.4,"權利人電話",1,0,'C',0); 
	$pdf->Cell(3.3,0.4,"",1,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(2.2,0.4,"義務人電話",1,0,'C',0); 
	$pdf->Cell(3.3,0.4,"",1,1,'L',0);
	$pdf->setX($tmpX);
	$pdf->Cell(2.2,0.4,"代理人聯絡電話",1,0,'C',0); 
	$pdf->Cell(3.3,0.4,"",1,1,'L',0);
	$pdf->setX($tmpX);
	$pdf->Cell(2.2,0.4,"傳真電話",1,0,'C',0); 
	$pdf->Cell(3.3,0.4,"",1,1,'L',0);
	$pdf->setX($tmpX);
	$pdf->Cell(2.2,0.4,"電子郵件信箱",1,0,'C',0); 
	$pdf->Cell(3.3,0.4,"",1,1,'L',0);

	$pdf->SetFont('msungstdlight', 'B', 7);
	$pdf->setX($tmpX); 
	$pdf->MultiCell(2.2, 0.8, "不動產經紀業名稱及統一編號", 1, 'C', 0, 0); 
	$pdf->Cell(3.3,0.8,"",1,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->Cell(2.2,0.4,"不動產經紀業電話",1,0,'C',0); 
	$pdf->Cell(3.3,0.4,"",1,1,'L',0);
	##
	$tmpY = $pdf->getY()+0.5;
	$pdf->setY($tmpY);
	$pdf->MultiCell(0.5, 8.7, "(10)\r\n\r\n\r\n申\r\n\r\n\r\n請\r\n\r\n\r\n人", 1, 'J', 0, 0); 

	$tmpX2 = $pdf->GetX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX2);
	$pdf->MultiCell(1.5, 1.5, "(11)\r\n權利人\r\n或\r\n義務人", 1, 'C', 0, 0); 
	$pdf->MultiCell(2, 1.5, "(12)\r\n姓名\r\n或\r\n名稱", 1, 'C', 0, 0); 
	$pdf->MultiCell(1.5, 1.5, "(13)\r\n出生\r\n年月日", 1, 'C', 0, 0); 
	$pdf->MultiCell(2, 1.5, "(14)\r\n\r\n統一編號", 1, 'C', 0, 0); 

	$tmpY = $pdf->getY();
	$tmpX = $pdf->getX();
	$pdf->Cell(9,0.4,"(15)住  所",1,1,'J',0); 

	$tmpY2 = $pdf->getY();
	$pdf->setX($tmpX);
	$pdf->Cell(0.8,1.1,"縣市",1,0,'J',0); 
	$tmpX = $pdf->getX();
	$pdf->Cell(1,0.5,"鄉鎮",$border,1,'C',0); 

	$pdf->setX($tmpX);
	$pdf->Cell(1,0.6,"市區",$border3,0,'C',0); 
	$tmpX = $pdf->getX();
	$pdf->setY($tmpY2);
	$pdf->setX($tmpX);
	// $pdf->MultiCell(1, 1.1, "鄉鎮\r\n市區", 1, 'C', 0, 0);
	$pdf->Cell(0.8,1.1,"村里",1,0,'J',0); 
	$pdf->Cell(0.8,1.1,"鄰",1,0,'J',0); 
	$pdf->Cell(0.8,1.1,"街路",1,0,'J',0); 
	$pdf->Cell(0.8,1.1,"段",1,0,'J',0); 
	$pdf->Cell(0.8,1.1,"巷",1,0,'J',0);
	$pdf->Cell(0.8,1.1,"弄",1,0,'J',0);
	$pdf->Cell(0.8,1.1,"號",1,0,'J',0);
	$pdf->Cell(0.8,1.1,"樓",1,0,'J',0);
	$pdf->Cell(0.8,1.1,"",1,0,'J',0);

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->MultiCell(1.5, 1.5, "(16)\r\n\r\n簽章", 1, 'C', 0, 1); 

	$tmpY2 = $pdf->getY();
	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,1,'J',0);

	$pdf->setX($tmpX2);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(1.5,0.8,"",1,0,'J',0); 
	$pdf->Cell(2,0.8,"",1,0,'J',0);
	$pdf->Cell(9,0.8,"",1,0,'J',0);

	$tmpX = $pdf->getX();
	$pdf->setY($tmpY2);
	$pdf->setX($tmpX);
	$pdf->Cell(1.5,7.2,"",1,1,'J',0);
	##

	$pdf->MultiCell(0.5, 5.5, "本\r\n案\r\n處\r\n理\r\n經\r\n過\r\n情\r\n形\r\n，\r\n以\r\n下\r\n各\r\n欄\r\n申\r\n請\r\n人\r\n請\r\n勿\r\n填\r\n寫\r\n", 1, 'J', 0, 0); 

	$pdf->SetFont('msungstdlight', 'B', 8);
	$tmpX = $pdf->getX();
	$pdf->Cell(3,0.8,"初 審",1,0,'J',0); 
	$pdf->Cell(3,0.8,"複 審",1,0,'J',0); 
	$pdf->Cell(3,0.8,"核 定",1,0,'J',0); 
	$pdf->Cell(1.7,0.8,"登 簿",1,0,'J',0);
	$pdf->Cell(1.7,0.8,"校 簿",1,0,'J',0);
	$pdf->MultiCell(1.7, 0.8, "書狀\r\n列印", 1, 'C', 0, 0); 
	$pdf->Cell(1.7,0.8,"校 狀",1,0,'J',0); 
	$pdf->MultiCell(1.7, 0.8, "書狀\r\n列印", 1, 'C', 0, 0); 

	$tmpY = $pdf->getY()+0.8;
	$pdf->setY($tmpY);
	$pdf->setX($tmpX);
	$pdf->Cell(3,5.39,"",1,0,'L',0);
	$pdf->Cell(3,5.39,"",1,0,'L',0);
	$pdf->Cell(3,5.39,"",1,0,'L',0);

	$tmpX = $pdf->getX();
	$pdf->Cell(1.7,2.3,"",1,0,'L',0);
	$pdf->Cell(1.7,2.3,"",1,0,'L',0);
	$pdf->Cell(1.7,2.3,"",1,0,'L',0);
	$pdf->Cell(1.7,2.3,"",1,0,'L',0);
	$pdf->Cell(1.7,2.3,"",1,1,'L',0);

	$pdf->setX($tmpX);
	$pdf->MultiCell(1.7, 0.8, "地價\r\n異動", 1, 'C', 0, 0); 
	$pdf->MultiCell(1.7, 0.8, "通知\r\n領狀", 1, 'C', 0, 0); 
	$pdf->MultiCell(1.7, 0.8, "異動\r\n通知", 1, 'C', 0, 0); 
	$pdf->MultiCell(1.7, 0.8, "交付\r\n發狀", 1, 'C', 0, 0); 
	$pdf->Cell(1.7,0.8,"歸檔",1,1,'C',0);

	$pdf->setX($tmpX);
	$pdf->Cell(1.7,2.29,"",1,0,'L',0);
	$pdf->Cell(1.7,2.29,"",1,0,'L',0);
	$pdf->Cell(1.7,2.29,"",1,0,'L',0);
	$pdf->Cell(1.7,2.29,"",1,0,'L',0);
	$pdf->Cell(1.7,2.29,"",1,1,'L',0);
}

##


// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')

// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
?>