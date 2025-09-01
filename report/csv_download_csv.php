<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
// header("Content-Type:text/html; charset=utf-8");
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$str = '';
foreach ($title as $key => $value) {
	$str .= $value.',';
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value);
}

$str .= "\n" ;	


foreach ($list as $key => $value) {
	$col = 65;
	foreach ($fieldArray as $field) {
		$str .= $value[$field].',';
		
	}

	$str .= "\n" ;
	
}

header('Content-type:application/force-download');
header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=csv_export.csv");

echo $str ;


exit;


?>
