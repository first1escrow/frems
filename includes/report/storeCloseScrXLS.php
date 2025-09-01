<?php

$objPHPExcel = new PHPExcel() ;

//設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經") ;
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經") ;
$objPHPExcel->getProperties()->setTitle("第一建經仲介地政士統計") ;
$objPHPExcel->getProperties()->setSubject("仲介地政士統計報表") ;
$objPHPExcel->getProperties()->setDescription("第一建經地政士開關店統計") ;
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("總店數") ;
##

##

//資料內容
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(60) ;

$msg = '註：第一建經成立至今所有地政士' ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$msg) ;

$objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true)->setSize(16) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','序號') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','地政士編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','姓名') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','事務所名稱') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','目前狀態') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','狀態時間') ;
$objPHPExcel->getActiveSheet()->setCellValue('G3','合作仲介品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('H3','負責業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','備註') ;

$cell = 4 ;
$cnt = 1 ;
for ($i = 0 ; $i < count($scr) ; $i ++) {
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell,($cnt++)) ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell,'SC'.str_pad($scr[$i]['id'],4,'0',STR_PAD_LEFT)) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell,$scr[$i]['name']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell,$scr[$i]['store']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell,$scr[$i]['status']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell,$scr[$i]['statusTime']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell,$scr[$i]['brand']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell,$scr[$i]['sales']) ;

	if ($scr[$i]['category'] == '直營') {
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'直營店') ;
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'') ;
	}
	
	
	$cell ++ ;
}
##

##

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("合作") ;
##

//資料內容
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(60) ;

$msg = '註：'.$fromDate.'起算店家數量' ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$msg) ;

$objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true)->setSize(16) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','序號') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','地政士編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','姓名') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','事務所名稱') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','目前狀態') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','狀態時間') ;
$objPHPExcel->getActiveSheet()->setCellValue('G3','合作仲介品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('H3','負責業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','備註') ;

$cell = 4 ;
$cnt = 1 ;
for ($i = 0 ; $i < count($aliveScr) ; $i ++) {
	if ($aliveScr[$i]['status'] == '合作') {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell,($cnt++)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell,'SC'.str_pad($aliveScr[$i]['id'],4,'0',STR_PAD_LEFT)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell,$aliveScr[$i]['name']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell,$aliveScr[$i]['store']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell,$aliveScr[$i]['status']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell,$aliveScr[$i]['statusTime']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell,$aliveScr[$i]['brand']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell,$aliveScr[$i]['sales']) ;
		if ($aliveScr[$i]['category'] == '直營') {
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'直營店') ;
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'') ;
		}
		
		$cell ++ ;
	}
}
##

//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(2) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("終止") ;
##

//資料內容
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(60) ;

$msg = '註：'.$fromDate.'起算店家數量' ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$msg) ;

$objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true)->setSize(16) ;

$objPHPExcel->getActiveSheet()->setCellValue('A3','序號') ;
$objPHPExcel->getActiveSheet()->setCellValue('B3','地政士編號') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','姓名') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','事務所名稱') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','目前狀態') ;
$objPHPExcel->getActiveSheet()->setCellValue('F3','狀態時間') ;
$objPHPExcel->getActiveSheet()->setCellValue('G3','合作仲介品牌') ;
$objPHPExcel->getActiveSheet()->setCellValue('H3','負責業務') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','備註') ;

$cell = 4 ;
$cnt = 1 ;
for ($i = 0 ; $i < count($closeScr) ; $i ++) {
	if ($closeScr[$i]['status'] != '合作' ) {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell,($cnt++)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell,'SC'.str_pad($closeScr[$i]['id'],4,'0',STR_PAD_LEFT)) ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell,$closeScr[$i]['name']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell,$closeScr[$i]['store']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell,$closeScr[$i]['status']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell,$closeScr[$i]['statusTime']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell,$closeScr[$i]['brand']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell,$closeScr[$i]['sales']) ;
		if ($closeScr[$i]['category'] == '直營') {
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'直營店') ;
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell,'') ;
		}
		
		$cell ++ ;
	}
}
##
/*
//建立並指定新的工作頁
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(3) ;
##

//更改頁籤名稱 
$objPHPExcel->getActiveSheet()->setTitle("暫停") ;
##

//資料內容

##*/

$objPHPExcel->setActiveSheetIndex(0) ;

?>