<?php
/**
 * 2024/07/16 點交單最後附上 不動產交易防制洗錢聲明書
 */
$image1 = dirname(dirname(__FILE__)) . '/images/money_laundering_statement.jpg';
$pdf->Cell( 40, 40, $pdf->Image($image1, $pdf->GetX(), $pdf->GetY(), 190), 0, 0, 'L', false );
