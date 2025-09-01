<?php

$objPHPExcel = new PHPExcel() ;

//設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經") ;
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經") ;
$objPHPExcel->getProperties()->setTitle("第一建經仲介地政士統計") ;
$objPHPExcel->getProperties()->setSubject("仲介地政士統計報表") ;
$objPHPExcel->getProperties()->setDescription("第一建經仲介開關店統計") ;
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("總店數") ;
##

//資料內容
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(60) ;

$msg = '註：第一建經成立至今所有店家' ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$msg) ;

$objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true)->setSize(16) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','序號') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','店編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','地址') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','目前狀態') ;
$objPHPExcel->getActiveSheet()->setCellValue('G3','狀態時間') ;
$objPHPExcel->getActiveSheet()->setCellValue('H3','負責業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','備註') ;

$cell = 4 ;
$cnt = 1 ;
for ($i = 0 ; $i < count($realty) ; $i ++) {
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell,($cnt++)) ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell,$realty[$i]['code'].str_pad($realty[$i]['id'],5,'0',STR_PAD_LEFT)) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell,$realty[$i]['name']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell,$realty[$i]['store']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell,$realty[$i]['addr']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell,$realty[$i]['status']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell,$realty[$i]['statusTime']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell,$realty[$i]['sales']) ;	
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'') ;
	
	$cell ++ ;
}
##

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("營業中") ;
##

//資料內容
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(60) ;

$msg = '註：'.$fromDate.'起算店家數量' ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$msg) ;

$objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true)->setSize(16) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','序號') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','店編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','地址') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','目前狀態') ;
$objPHPExcel->getActiveSheet()->setCellValue('G3','狀態時間') ;
$objPHPExcel->getActiveSheet()->setCellValue('H3','負責業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','備註') ;

$cell = 4 ;
$cnt = 1 ;
for ($i = 0 ; $i < count($aliveStore) ; $i ++) {
	if ($aliveStore[$i]['status'] == '營業中') {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell,($cnt++)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell,$aliveStore[$i]['code'].str_pad($aliveStore[$i]['id'],5,'0',STR_PAD_LEFT)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell,$aliveStore[$i]['name']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell,$aliveStore[$i]['store']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell,$aliveStore[$i]['addr']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell,$aliveStore[$i]['status']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell,$aliveStore[$i]['statusTime']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell,$aliveStore[$i]['sales']) ;	
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'') ;
		
		$cell ++ ;
	}
}
##

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(2) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("退店") ;
##

//資料內容
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(60) ;
$msg = '註：'.$fromDate.'起算店家數量' ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$msg) ;

$objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true)->setSize(16) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','序號') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','店編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','地址') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','目前狀態') ;
$objPHPExcel->getActiveSheet()->setCellValue('G3','狀態時間') ;
$objPHPExcel->getActiveSheet()->setCellValue('H3','負責業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','備註') ;

$cell = 4 ;
$cnt = 1 ;
for ($i = 0 ; $i < count($closeStore) ; $i ++) {
	if ($closeStore[$i]['status'] == '退店') {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell,($cnt++)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell,$closeStore[$i]['code'].str_pad($closeStore[$i]['id'],5,'0',STR_PAD_LEFT)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell,$closeStore[$i]['name']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell,$closeStore[$i]['store']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell,$closeStore[$i]['addr']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell,$closeStore[$i]['status']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell,$closeStore[$i]['statusTime']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell,$closeStore[$i]['sales']) ;	
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'') ;
		
		$cell ++ ;
	}
}
##

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(3) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("暫停") ;
##

//資料內容
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(60) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(60) ;

$msg = '註：'.$fromDate.'起算店家數量' ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$msg) ;

$objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true)->setSize(16) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','序號') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','店編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','店名') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','地址') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','目前狀態') ;
$objPHPExcel->getActiveSheet()->setCellValue('G3','狀態時間') ;
$objPHPExcel->getActiveSheet()->setCellValue('H3','負責業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','備註') ;

$cell = 4 ;
$cnt = 1 ;
for ($i = 0 ; $i < count($pauseStore) ; $i ++) {
	if ($pauseStore[$i]['status'] == '暫停') {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell,($cnt++)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell,$pauseStore[$i]['code'].str_pad($pauseStore[$i]['id'],5,'0',STR_PAD_LEFT)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell,$pauseStore[$i]['name']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell,$pauseStore[$i]['store']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell,$pauseStore[$i]['addr']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell,$pauseStore[$i]['status']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell,$pauseStore[$i]['statusTime']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell,$pauseStore[$i]['sales']) ;	
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'') ;
		
		$cell ++ ;
	}
}
##

$objPHPExcel->setActiveSheetIndex(0) ;

?>