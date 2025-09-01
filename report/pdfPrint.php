<?php
//半形<=>全形
Function n2w($strs, $types = '0'){  // narrow to wide , or wide to narrow
	$nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
	);
	$wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
	);
 
	if ($types == '0') {		//半形轉全形
		// narrow to wide
		$strtmp = str_replace($nt, $wt, $strs);
	}
	else {						//全形轉半形
		// wide to narrow
		$strtmp = str_replace($wt, $nt, $strs);
	}
	return $strtmp;
}
##

//計算行高
Function checkHeight($str='') {
	$mul = 1 ;
	if ($str) $mul = ceil(mb_strlen($str,'utf-8') / 6) ;
	
	return $mul ;
}
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
//$pdf->SetDash(2,2) ;	//2mm on, 2mm off
//$pdf->SetDash() ;		//restore no dash
##

//print_r($header) ;
//print_r($branch) ;
//print_r($footer) ;

//$pdf = new PDF_Unicode() ;												// 建立 FPDF
$pdf = new PDF1() ;															// 建立 FPDF

$pdf->Open() ;																// 開啟建立新的 PDF 檔案
$pdf->SetAuthor('Jason Chen') ; 											// 設定作者
$pdf->SetAutoPageBreak(1,2) ;												// 設定自動分頁並指定距下方邊界1mm
$pdf->SetMargins(10,3,10) ;													// 設定顯示邊界 (左、上、右)
$pdf->AddUniCNShwFont('uni'); 												// 設定為 UTF-8 顯示輸出
$cnt = 0 ;
foreach ($branch as $k => $v) {
	$pdf->AddPage() ;
	$pdf->SetFont('uni','',12) ;
	include dirname(__FILE__).'/pdfHead.php' ;
	include dirname(__FILE__).'/pdfBody.php' ;
	include dirname(__FILE__).'/pdfFooter.php' ;
	$cnt ++ ;
}

$pdf->Output() ;
?>