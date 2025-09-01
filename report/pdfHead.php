<?php
//print_r($header) ;

$pdf->SetFont('uni','',20) ;

$pdf->Cell(190,20,$header['B'][0],0,1,'C',0) ;

$pdf->SetFont('uni','',12) ;

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

?>