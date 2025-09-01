<?php
include_once dirname(dirname(__FILE__)).'/openadodb.php';
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
##

$conn->Execute("use www_first1_report;");

$list = array();
$sql = "SELECT * FROM tContractCaseReport WHERE cSignDate >= '2021-01-01 00:00:00' AND cSignDate <= '2021-12-31 12:59:59' AND cCaseStatus != '作廢' AND cCaseStatus != '異常'";

$rs = $conn->Execute($sql);
$bank = array('一銀'=>array(),'永豐'=>array(),'台新'=>array());
while (!$rs->EOF) {
	array_push($list, $rs->fields);
	
	array_push($bank[$rs->fields['cBank']], $rs->fields);
	$rs->MoveNext();
}





##

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("");
$objPHPExcel->getProperties()->setDescription("");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('全部');

//寫入清單標題列資料

$col = 65;
$row = 1;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'銀行');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');

$row++;

// echo $max."<br>";

foreach ($list as $key => $v) {
	print_r($v);
	$col = 65;
	$v['cEndDate'] = dateformate($v['cEndDate']) ;
	$v['cSignDate'] = dateformate($v['cSignDate']);

	if ($v['cCaseStatus'] =='已結案' || $v['cCaseStatus'] == '已結案有保留款' || $v['cCaseStatus'] == '解約/終止履保') {
		$date = $v['cEndDate'] ;
	}else {
		$date = $v['cSignDate'] ;
	}

	$exp = explode(',', $v['cStoreBrand']);
	$exp1 = explode(',', $v['cStoreBranch']);
	$store = array();
	for ($i=0; $i < count($exp); $i++) { 
		if ($exp[$i]) {
			array_push($store, $exp[$i].$exp1[$i]);
		}
	}
	

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($key+1));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cBank']);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cCertifiedId']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cStoreCode']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,@implode(',', $store));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cEndDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cScrivenerName']);

	$row++;
}
unset($list);

//命名工作表標籤
$objPHPExcel->createSheet(1) ;
$objPHPExcel->setActiveSheetIndex(1);


$objPHPExcel->getActiveSheet()->setTitle('一銀');
$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'銀行');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$row++;
foreach ($bank['一銀'] as $key => $v) {
	print_r($v);
	$col = 65;

	$v['cEndDate'] = dateformate($v['cEndDate']) ;
	$v['cSignDate'] = dateformate($v['cSignDate']);

	if ($v['cCaseStatus'] =='已結案' || $v['cCaseStatus'] == '已結案有保留款' || $v['cCaseStatus'] == '解約/終止履保') {
		$date = $v['cEndDate'] ;
	}else {
		$date = $v['cSignDate'] ;
	}

	$exp = explode(',', $v['cStoreBrand']);
	$exp1 = explode(',', $v['cStoreBranch']);
	$store = array();
	for ($i=0; $i < count($exp); $i++) { 
		if ($exp[$i]) {
			array_push($store, $exp[$i].$exp1[$i]);
		}
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($key+1));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cBank']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cStoreCode']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,@implode(',', $store));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cScrivenerName']);
	
	

	$row++;
	
}


//命名工作表標籤
$objPHPExcel->createSheet(2) ;
$objPHPExcel->setActiveSheetIndex(2);


$objPHPExcel->getActiveSheet()->setTitle('台新');
$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'銀行');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$row++;
foreach ($bank['台新'] as $key => $v) {
	print_r($v);
	$col = 65;

	$v['cEndDate'] = dateformate($v['cEndDate']) ;
	$v['cSignDate'] = dateformate($v['cSignDate']);

	if ($v['cCaseStatus'] =='已結案' || $v['cCaseStatus'] == '已結案有保留款' || $v['cCaseStatus'] == '解約/終止履保') {
		$date = $v['cEndDate'] ;
	}else {
		$date = $v['cSignDate'] ;
	}

	$exp = explode(',', $v['cStoreBrand']);
	$exp1 = explode(',', $v['cStoreBranch']);
	$store = array();
	for ($i=0; $i < count($exp); $i++) { 
		if ($exp[$i]) {
			array_push($store, $exp[$i].$exp1[$i]);
		}
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($key+1));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cBank']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cStoreCode']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,@implode(',', $store));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cScrivenerName']);
	
	

	$row++;
	
}


//命名工作表標籤
$objPHPExcel->createSheet(2) ;
$objPHPExcel->setActiveSheetIndex(2);


$objPHPExcel->getActiveSheet()->setTitle('永豐');
$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'銀行');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$row++;
foreach ($bank['永豐'] as $key => $v) {
	print_r($v);
	$col = 65;

	$v['cEndDate'] = dateformate($v['cEndDate']) ;
	$v['cSignDate'] = dateformate($v['cSignDate']);

	if ($v['cCaseStatus'] =='已結案' || $v['cCaseStatus'] == '已結案有保留款' || $v['cCaseStatus'] == '解約/終止履保') {
		$date = $v['cEndDate'] ;
	}else {
		$date = $v['cSignDate'] ;
	}

	$exp = explode(',', $v['cStoreBrand']);
	$exp1 = explode(',', $v['cStoreBranch']);
	$store = array();
	for ($i=0; $i < count($exp); $i++) { 
		if ($exp[$i]) {
			array_push($store, $exp[$i].$exp1[$i]);
		}
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($key+1));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cBank']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cStoreCode']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,@implode(',', $store));
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cScrivenerName']);
	
	

	$row++;
	
}			
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("/var/www/html/first.twhg.com.tw/test2/log/100.xlsx");
	
exit ;

function dateformate($date){

	if ($date != '0000-00-00') {
		$date = (substr($date, 0,4)-1911)."/".substr($date, 5,2)."/".substr($date, 8,2);
	}else{
		$date = '0000-00-00';
	}

	return $date;
}
?>
