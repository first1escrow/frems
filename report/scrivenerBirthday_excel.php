<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;

// $Datalevel[0]



$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("");
$objPHPExcel->getProperties()->setDescription("第一建經");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('等級1');



$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'生日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'收入');
$row++;

foreach ($Datalevel[1]['data'] as $k => $v) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Code']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sBirthday']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['caseFeedBackMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['income']);
	// print_r($v);
	// die;
	// preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);
	$row++;
}


$objPHPExcel->createSheet(1) ;
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('等級2');

$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'生日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'收入');
$row++;


foreach ($Datalevel[2]['data'] as $k => $v) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Code']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sBirthday']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['caseFeedBackMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['income']);
	// print_r($v);
	// die;
	// preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);
	$row++;
}


$objPHPExcel->createSheet(2) ;
$objPHPExcel->setActiveSheetIndex(2);
$objPHPExcel->getActiveSheet()->setTitle('等級3');

$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'生日');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'收入');
$row++;


foreach ($Datalevel[3]['data'] as $k => $v) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['Code']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['sBirthday']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['certifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['caseFeedBackMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['income']);
	// print_r($v);
	// die;
	// preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);
	$row++;
}

$objPHPExcel->setActiveSheetIndex(0);
// for ($i = 0 ; $i < count($data) ; $i ++) {
// 	$col = 65;

// 	$tmp = explode(' ', $data[$i]['bCreateDate']);
// 	$tmp2 = explode('-', $tmp[0]);
// 	$data[$i]['bCreateDate'] = ($tmp2[0]-1911)."-".$tmp2[1]."-".$tmp2[2];
// 	$code = 'SC'.str_pad($data[$i]['bSID'], 4,0,STR_PAD_LEFT);

// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$code);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['Name']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['total']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['bCreateDate']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['sales']);
// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['Undertaker']);

// 	unset($tmp);
// 	unset($tmp2);
// 	$row++;
// }



$_file = iconv('UTF-8', 'BIG5', '地政士級別') ;
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
