<?php
$_POST["s"] = $_POST['sales'] ;
$dateYear = $_POST['dateYear'] ;
$dateMonth = $_POST['dateMonth'] ;
//print_r($_POST) ;

$_scr = array() ;
$_realty = array() ;

$_scr1 = array() ;
$_realty1 = array() ;

require_once dirname(dirname(__FILE__)).'/sales/getTrackingList.php' ;
require_once dirname(dirname(__FILE__)).'/sales/getNewSignList.php' ;

require_once dirname(dirname(__FILE__)).'/bank/Classes/PHPExcel.php' ;
require_once dirname(dirname(__FILE__)).'/bank/Classes/PHPExcel/Writer/Excel2007.php' ;

//print_r($_scr1) ;
//print_r($_realty1) ;

//編輯 Excel
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性

$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("未進案追蹤統計");
$objPHPExcel->getProperties()->setDescription("第一建經未進案追蹤統計");

/* 新增第 1 頁 */

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0) ;

$objPHPExcel->getActiveSheet()->setCellValue('A1','業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('B1',$menu_sales[$salesId]) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','地政士') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','未進案天數') ;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(38) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15) ;

$row = 4 ;
foreach ($_scr1 as $k => $v) {
	if ($v['diff'] > 0) {
		if ($v['office']) $v['office'] = '('.$v['office'].')' ;
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$v['name'].$v['office']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,number_format($v['diff'])) ;
		
		$row ++ ;
	}
}
##


//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("新簽地政士未進案天數") ;
##

/* 新增第 2 頁 */

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;

$objPHPExcel->getActiveSheet()->setCellValue('A1','品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('B1','店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('C1','未進案天數') ;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(38) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(38) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15) ;

$row = 2 ;
foreach ($_realty1 as $k => $v) {
	if ($v['diff'] > 0) {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$v['brand']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$v['store']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,number_format($v['diff'])) ;
		$row ++ ;
	}
}
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("新簽仲介店未進案天數") ;
##

/* 新增第 3 頁 */

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(2) ;

$objPHPExcel->getActiveSheet()->setCellValue('A1','地政士') ;
$objPHPExcel->getActiveSheet()->setCellValue('B1',$_last2Month.'月件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('C1',$_lastMonth.'月件數') ;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(38) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15) ;

$row = 2 ;
foreach ($_scr as $k => $v) {
	if ($v['office']) $v['office'] = '('.$v['office'].')' ;
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$v['name'].$v['office']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,number_format($v['lastTotal'])) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,number_format($v['thisTotal'])) ;
	$row ++ ;
}
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("前二月未進案地政士比較") ;
##

/* 新增第 4 頁 */

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(3) ;

$objPHPExcel->getActiveSheet()->setCellValue('A1','品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('B1','店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('C1',$_last2Month.'月件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('D1',$_lastMonth.'月件數') ;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(38) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(38) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15) ;

$row = 2 ;
foreach ($_realty as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$v['brand']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$v['store']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,number_format($v['lastTotal'])) ;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,number_format($v['thisTotal'])) ;
	$row ++ ;
}
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("前二月未進案仲介店比較") ;
##

//產出檔案
$objPHPExcel->setActiveSheetIndex(0) ;

$_file = 'salesTrackData.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
##

exit ;
?>