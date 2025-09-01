<?php
//print_r($branch) ; exit ;

$pdf->SetFont('uni','',12) ;

$pdf->Cell(23,9,'日期',1,0,'L') ;
$pdf->Cell(22,9,'保證號碼',1,0,'L') ;
$pdf->Cell(24,9,'店編號',1,0,'L') ;
$pdf->Cell(28,9,'買方',1,0,'L') ;
$pdf->Cell(28,9,'賣方',1,0,'L') ;
$pdf->Cell(25,9,'買賣總價金',1,0,'L') ;
$pdf->Cell(20,9,'履保費用',1,0,'L') ;
$pdf->Cell(20,9,'回饋金額',1,1,'L') ;

foreach ($v['detail'] as $m => $n) {
	$h = 5 ;
	$n['buyer'] = n2w($n['buyer']) ;
	$n['owner'] = n2w($n['owner']) ;
	
	$hBuyer = checkHeight($n['buyer']) ;
	$hOwner = checkHeight($n['owner']) ;
	
	if ($hBuyer  > $hOwner) {
		$mul = $hBuyer ;
		$hBuyer = 5 ; 
		if ($hOwner <= 1) $hOwner = $h * $mul ;
		else $hOwner = 5 ;
	}
	else {
		$mul = $hOwner ;
		if ($hBuyer <= 1) $hBuyer = $h * $mul ;
		else $hBuyer = 5 ;
		$hOwner = 5 ;
	}
	$h *= $mul ;
	
	$_store = preg_replace("/\W+.*$/","",$v['store']) ;
	$matches = array() ;
	preg_match("/^([A-Za-z]+)(\d+)$/",$_store,$matches) ;
	$_store = $matches[2] ;
	$_sCode = $matches[1] ;
	
	$X = 10 + 23 + 22 + 24 + 28 ;
	
	$pdf->Cell(23,$h,$n['date'],1,0,'L') ;
	$pdf->Cell(22,$h,$n['cid'],1,0,'L') ;
	$pdf->Cell(5,$h,$_sCode,'LTB',0,'L') ;
	$pdf->Cell(19,$h,$_store,'RTB',0,'L') ;
	//$pdf->Cell(24,$h,$_sCode.' '.$_store,1,0,'L') ;
	
	$Y = $pdf->GetY() ;
	$pdf->MultiCell(28,$hBuyer,$n['buyer'],1,'L') ;
	
	$pdf->SetXY($X,$Y) ;
	$pdf->MultiCell(28,$hOwner,$n['owner'],1,'L') ;
	
	$X += 28 ;
	$pdf->SetXY($X,$Y) ;
	
	$pdf->Cell(25,$h,number_format($n['tmoney']),1,0,'R') ;
	$pdf->Cell(20,$h,number_format($n['cmoney']),1,0,'R') ;
	// echo $n['cmoney']."<br>";
	
	$pdf->Cell(20,$h,number_format($n['bmoney']),1,1,'R') ;
}
$pdf->SetFont('uni','B',12) ;
$pdf->Cell(23,6,'',1,0,'L') ;
$pdf->Cell(22,6,'',1,0,'L') ;
$pdf->Cell(5,6,$_sCode,'LTB',0,'L') ;
$pdf->Cell(11,6,$_store,'TB',0,'L') ;
$pdf->Cell(64,6,preg_replace("/^\w+/","",$v['store']).' 合計','RTB',0,'L') ;
//$pdf->Cell(80,6,$v['store'].' 合計',1,0,'L') ;
$pdf->Cell(25,6,'',1,0,'L') ;
$pdf->Cell(20,6,'',1,0,'L') ;
$pdf->Cell(20,6,number_format($v['total']),1,1,'R') ;
$pdf->SetFont('uni','',12) ;
?>