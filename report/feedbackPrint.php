<?php
include_once dirname(dirname(__FILE__)).'/libs/PHPExcel/Classes/PHPExcel.php' ;
include_once dirname(dirname(__FILE__)).'/checklist/fpdf/chinese-unicode.php' ;

$objExcel = PHPExcel_IOFactory::load('excel/'.$xlsName) ;

shell_exec('rm -rf excel/'.$xlsName) ;

$reader = $objExcel->getSheet(0) ;
$maxRow = $reader->getHighestRow() ;

$header = array() ;
$fotter = array() ;
$branch = array() ;
$readFG = true ;

$hd = 0 ;
$bd = 0 ;
$ft = 0 ;
for ($i = 1 ; $i <= $maxRow ; $i ++) {
	$cell = $reader->getCell('A'.$i)->getFormattedValue() ;
	$cellB = $reader->getCell('B'.$i)->getFormattedValue() ;
	
	if ($i < 9) {			//頁首
		if ($cellB)	{
			$header['B'][$hd] = $reader->getCell('B'.$i)->getFormattedValue() ;
			$header['C'][$hd] = $reader->getCell('C'.$i)->getFormattedValue() ;
			$hd ++ ;
		}
	}
	else if (preg_match("/\d+/",$cell)) {			//列表
		
		if ($readFG) {
			$branch[$bd]['store'] = $reader->getCell('D'.$i)->getFormattedValue() ;
			
			$b = $reader->getCell('B'.$i)->getFormattedValue() ;
			$c = $reader->getCell('C'.$i)->getFormattedValue() ;
			$e = $reader->getCell('E'.$i)->getFormattedValue() ;
			$f = $reader->getCell('F'.$i)->getFormattedValue() ;
			$g = $reader->getCell('G'.$i)->getFormattedValue() ;
			$h = $reader->getCell('H'.$i)->getFormattedValue() ;
			$ii = $reader->getCell('I'.$i)->getFormattedValue() ;
			
			$data = array('date' => $b, 'cid' => $c, 'buyer' => $e, 'owner' => $f, 'tmoney' => $g, 'cmoney' => $h, 'bmoney' => $ii) ;
			$branch[$bd]['detail'][] = $data ;
			
			unset($b, $c, $e, $f, $g, $h, $ii, $data) ;
			
			$readFG = false ;
		}
		else {
			if ($reader->getCell('B'.$i)->getFormattedValue() == '') {
				$branch[$bd]['total'] = $reader->getCell('I'.$i)->getFormattedValue() ;
				
				$bd ++ ;
				$readFG = true ;
			}
			else {
				$b = $reader->getCell('B'.$i)->getFormattedValue() ;
				$c = $reader->getCell('C'.$i)->getFormattedValue() ;
				$e = $reader->getCell('E'.$i)->getFormattedValue() ;
				$f = $reader->getCell('F'.$i)->getFormattedValue() ;
				$g = $reader->getCell('G'.$i)->getFormattedValue() ;
				$h = $reader->getCell('H'.$i)->getFormattedValue() ;
				$ii = $reader->getCell('I'.$i)->getFormattedValue() ;
				
				$data = array('date' => $b, 'cid' => $c, 'buyer' => $e, 'owner' => $f, 'tmoney' => $g, 'cmoney' => $h, 'bmoney' => $ii) ;
				$branch[$bd]['detail'][] = $data ;
				
				unset($b, $c, $e, $f, $g, $h, $ii, $data) ;
			}
		}
	}
	else {			//頁尾
		if ($cellB && ($i > 9)) {
			$footer[$ft]['B'] = $reader->getCell('B'.$i)->getFormattedValue() ;
			$footer[$ft]['C'] = $reader->getCell('C'.$i)->getFormattedValue() ;
			$footer[$ft]['D'] = $reader->getCell('D'.$i)->getFormattedValue() ;
			
			$ft ++ ;
		}
	}
}
unset($objExcel, $reader) ;

//print_r($header) ;
//print_r($branch) ;
//print_r($footer) ;
include_once dirname(__FILE__).'/pdfPrint.php' ;
?>