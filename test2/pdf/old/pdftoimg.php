<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);


// require_once dirname(dirname(dirname(__FILE__))).'/tcpdf/tcpdf.php' ;
// require_once('fpdi/fpdi.php'); // the addon
// $fullPathToFile = 'checklist.pdf';

// class PDF extends FPDI {

//     var $_tplIdx;

//     function Header() {

//         global $fullPathToFile;

//         if (is_null($this->_tplIdx)) {

//             // THIS IS WHERE YOU GET THE NUMBER OF PAGES
//             $this->numPages = $this->setSourceFile($fullPathToFile);
//             $this->_tplIdx = $this->importPage(1);

//         }
//         $this->useTemplate($this->_tplIdx);

//     }

//     function Footer() {}

// }

// $pdf = new PDF();
// $pdf->setFontSubsetting(true);

// $pdf->AddPage();


// if($pdf->numPages>1) {
//     for($i=2;$i<=$pdf->numPages;$i++) {
//         $pdf->endPage();
//         $pdf->_tplIdx = $pdf->importPage($i);
//         $pdf->AddPage();
//     }
// }


// $pdf->Output('/home/httpd/html/first.twhg.com.tw/test2/pdf/test.pdf', 'F');

$img = new Imagick(); 
$img -> readImage('http://first.twhg.com.tw/inquire/buyerownerinquery.php');
$img -> resetIterator();
$img -> setImageFormat('jpg');
$img->writeImage('output.jpg');

?>