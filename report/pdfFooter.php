<?php
//print_r($footer) ; exit ;

$pdf->SetFont('uni','',12) ;

//簽章
$Y = $pdf->GetY() ;
$Y += 20 ;
$pdf->SetXY(10,$Y) ;
$pdf->Cell(0,5,$footer[0]['B'],0,1,'L',0) ;
##

//第四點
$Y += 20 ;
$pdf->SetXY(10,$Y) ;
$pdf->Cell(12,5,$footer[1]['B'],0,0,'L',0) ;
$pdf->MultiCell(0,5,$footer[1]['C'],0,'L') ;
##

//辦法一
$Y = $pdf->GetY() + 6 ;
$pdf->SetXY(10,$Y) ;
$pdf->Cell(54,7,$footer[2]['B'],1,0,'C',0) ;
$pdf->MultiCell(0,7,$footer[2]['D'],0,'L') ;

//$Y = $pdf->GetY() ;
//$pdf->SetXY(10,$Y) ;
$pdf->MultiCell(0,5,$footer[3]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[4]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[5]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[6]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[7]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[8]['B'],0,'L') ;
##

//辦法二
$Y = $pdf->GetY() + 6 ;
$pdf->SetXY(10,$Y) ;
$pdf->Cell(54,7,$footer[9]['B'],1,0,'C',0) ;
$pdf->MultiCell(0,7,$footer[9]['D'],0,'L') ;

//$Y = $pdf->GetY() ;
//$pdf->SetXY(10,$Y) ;
$pdf->MultiCell(0,5,$footer[10]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[11]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[12]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[13]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[14]['B'],0,'L') ;
$pdf->MultiCell(0,5,$footer[15]['B'],0,'L') ;
##

/*
$pdf->Cell(12,5,$header['B'][1],0,0,'L',0) ;
$pdf->MultiCell(178,5,$header['C'][1],0,'L',0) ;
$pdf->Ln() ;

$header['C'][2] = preg_replace("/新台幣.*元整/isu",'新台幣 '.number_format($v['total']).' 元整',$header['C'][2]) ;

$pdf->Cell(12,5,$header['B'][2],0,0,'L',0) ;
$pdf->MultiCell(178,5,$header['C'][2],0,'L',0) ;
$pdf->Ln() ;

$pdf->Cell(12,5,$header['B'][3],0,0,'L',0) ;
$pdf->MultiCell(178,5,$header['C'][3],0,'L',0) ;
$pdf->Ln() ;
*/
?>