<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/checklist/fpdf/chinese-unicode.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '地政士委任書');

$_POST = escapeStr($_POST);

$scrivener = new Scrivener();

$data = $scrivener->GetScrivenerInfo($_POST["id"]);
//設定線條為實、虛線
class PDF1 extends PDF_Unicode
{
    public function SetDash($black = false, $white = false)
    {
        if ($black and $white) {
            $s = sprintf('[%.3f %.3f] 0 d', $black * $this->k, $white * $this->k);
        } else {
            $s = '[] 0 d';
        }

        $this->_out($s);
    }
}
$pdf = new PDF1(); // 建立 FPDF

$pdf->Open(); // 開啟建立新的 PDF 檔案
$pdf->SetAuthor('第一建經'); // 設定作者
$pdf->SetAutoPageBreak(1, 2); // 設定自動分頁並指定距下方邊界1mm
$pdf->SetMargins(10, 3, 10); // 設定顯示邊界 (左、上、右)
$pdf->AddPage(); // 新增一頁
$pdf->AddUniCNShwFont('uni'); // 設定為 UTF-8 顯示輸出

$pdf->Image(dirname(__DIR__) . "/images/appoint2.png", 0, 0, '', '');

$pdf->AddUniCNShwFont('uniKai', 'DFKaiShu-SB-Estd-BF'); //標楷體

// $pdf->AddUniCNShwFont('test','UniCNS-UTF16-H');
$pdf->SetFont('uniKai', 'B', 28);
// $pdf->SetFont('uni','',24);
$pdf->Cell(190, 210, $data['sOffice'], 0, 1, 'C');
$pdf->SetFont('uniKai', 'B', 25);
$pdf->Cell(0, 0, $data['sOffice'], 0, 1, 'C');

$year  = date('Y') - 1911;
$month = date('m');
$day   = date('d');
$x     = $pdf->getX() + 75;
$y     = $pdf->getY() + 62;
$pdf->setY($y);
$pdf->setX($x);
$pdf->Cell(0, 12, $year, 0, 1);

$x = $pdf->getX() + 105;
$pdf->setY($y);
$pdf->setX($x);
$pdf->Cell(0, 12, $month, 0, 1);

$x = $pdf->getX() + 130;
$pdf->setY($y);
$pdf->setX($x);
$pdf->Cell(0, 12, $day, 0, 1);
$pdf->Output();
