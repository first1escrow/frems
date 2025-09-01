<?php
require_once 'fpdf/chinese-unicode.php' ;

//設定行高
$cell_y1 = 4.5 ;			// 內容用
$cell_y2 = 5 ;				// 標題用
$cell_y3 = 1 ;				// 手動跳行調行距用
$cell_y4 = 5 ;				// 內容用
$cell_y5 = 8 ;				// 銀行框框加大
$cell_y6 = 4 ;				// 注意事項用
$cell_gap = 2 ;				// 單元分隔用
$line_gap = 0.4 ;			// 雙線條畫線用
##

//設定線條為實、虛線
class PDF1 extends PDF_Unicode
{
    function SetDash($black=false, $white=false)
    {
        if($black and $white)
            $s=sprintf('[%.3f %.3f] 0 d', $black*$this->k, $white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }
}
################

//$pdf = new PDF_Unicode() ;												// 建立 FPDF
$pdf = new PDF1() ;															// 建立 FPDF
// $pdf = new FPDF() ;															// 建立 FPDF

$pdf->Open() ;																// 開啟建立新的 PDF 檔案
$pdf->SetAuthor('Jason Chen') ; 											// 設定作者
$pdf->SetAutoPageBreak(1,2) ;												// 設定自動分頁並指定距下方邊界1mm
$pdf->SetMargins(10,3,10) ;													// 設定顯示邊界 (左、上、右)
$pdf->AddPage() ;															// 新增一頁
// $pdf->AddUniCNShwFont('uni'); 												// 設定為 UTF-8 顯示輸出
// $pdf->AddUniCNSFont('uni'); 												// 設定為 UTF-8 顯示輸出
$pdf->AddUniGBhwFont('uni');

//////////////////////// 買方 ///////////////////////////

$pdf->SetFont('uni','',16) ;
// $pdf->SetFont('Times','',16) ;


// $pdf->SetFont('uni','',14); 
// $str = iconv("UTF-8","BIG5",'第一建築經理(股)公司') ;					// 寫入文字
// $str = '第一建築經理(股)公司' ;					// 寫入文字
// $str = 'aaaaaaaa' ;					// 寫入文字
// $pdf->Cell(190,$cell_y1,$str,0,1,'C') ;					// 寫入文字

// $pdf->Cell(190,$cell_y1,iconv("UTF-8","BIG5",'第一建築經理(股)公司'),0,1,'C') ;					// 寫入文字
// $pdf->Cell(190,$cell_y1,'AAAAAAAAAAAAAAAAAAAAAAA',0,1,'C') ;					// 寫入文字
	
// $pdf->SetFontSize(8) ;														// 設定字體大小
$pdf->Cell(190,$cell_y1,'履保專戶收支明細表暨點交確認單(買方)',0,1,'C') ;

// $pdf->Cell(190,$cell_y3,'',0,1) ;											// 手動換行

// $pdf->SetFontSize(12) ;	
// $pdf->Cell(190,$cell_y2,'案件基本資料',0,1) ;
// $pdf->SetFontSize(12) ;

//畫線(雙線條)
$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy += ($line_gap / 2) ;
$pdf->Line(10,$xy,200,$xy) ;	

$pdf->SetFontSize(12) ;
$xy = $pdf->GetY() ;
$xy -= $line_gap ;
$pdf->Line(10,$xy,200,$xy) ;
##

/* */
// 產生輸出
// $pdf->Output('xyz.pdf','F') ;
// $pdf->Output() ;
$pdf->Output() ;
?>
