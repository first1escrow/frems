<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;
##

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件統計表");
$objPHPExcel->getProperties()->setDescription("第一建經案件統計表");


//寫入title資料
//最後進案日、未進案天數(以查詢當天計算)
$objPHPExcel->getActiveSheet()->setCellValue('A1','未進案的計算天數:最後進案日(未有最後進案日以店家資料建檔的時間為準)-查詢當日') ;



$objPHPExcel->getActiveSheet()->setCellValue('A2','名稱') ;
$objPHPExcel->getActiveSheet()->setCellValue('B2','最後進案日') ;
$objPHPExcel->getActiveSheet()->setCellValue('C2','未進案天數(以查詢當天計算)') ;


//寫入各店家資料
$row =3 ;

foreach ($store as $k => $v) {

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$v['name']) ;


	if (!empty($data[$k]['date'])) {
		sort($data[$k]['date']);
		$lastDate = $data[$k]['date'][count($data[$k]['date'])-1];
		$lastDate = substr($lastDate, 0,4)."-".substr($lastDate, 4,2)."-".substr($lastDate, 6,2);
	}else{
		$lastDate = '0000-00-00';

	}
	// echo $lastDate."<br>";

	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$lastDate) ;

	
	if ($lastDate =='0000-00-00') {
		if (substr($v['creatTime'], 0,10) != '0000-00-00') {
			$lastDate = substr($v['creatTime'], 0,10);
		}else{
			//早期代書沒有建檔日
			$lastDate = '2012-01-01';//建經好像是2012開始有網站的
		}

		
	}

	$today = strtotime(date("Y-m-d"));
	$lastDate = strtotime($lastDate);

	$days =  round(($today-$lastDate)/3600/24) ;

	

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$days) ;


	unset($today);unset($lastDate);unset($days);
	$row++;
}


// die;
##

// $_file = 'SSSSS';
$_file = iconv('UTF-8', 'BIG5', '未進案表') ;
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file.'.xlsx');
			
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
	
exit ;
?>