<?php

include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;

// $_POST = escapeStr($_POST) ;

$nu = $_GET['sn'];

// $sql = "SELECT tPayOk,tExport_nu,SUM(tMoney) as M,tExport_time,tVR_Code,tBank_kind,tObjKind2 FROM tBankTrans WHERE tExport_nu = '".$nu."'  ";
//查詢返還的資料
$sql = "SELECT tId,tObjKind2Date,tBankLoansDate FROM tBankTrans WHERE tExport_nu = '".$nu."'";
// echo $sql;
$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$id = getTaxData($rs->fields['tId']); //反查申請時出款的ID
	$list[$i]['tId'] = $id;
	$list[$i]['tObjKind2Date'] = $rs->fields['tObjKind2Date'];
	$list[$i]['tBankLoansDate'] = $rs->fields['tBankLoansDate'];
	$i++;

	$rs->MoveNext();
}

for ($i=0; $i < count($list); $i++) { 
	$sql = "SELECT eMoney,eCertifiedId,eItem,(SELECT cName FROM tCategoryExpense WHERE cId =eItem) AS ItmeName FROM  tExpenseDetail WHERE eOK = '".$list[$i]['tId']."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		

		$data[$rs->fields['eCertifiedId']][$rs->fields['ItmeName']] =  $rs->fields['eMoney'];
		$data[$rs->fields['eCertifiedId']]['tObjKind2Date'] = $list[$i]['tObjKind2Date'];
		$data[$rs->fields['eCertifiedId']]['totalMoney'] += $rs->fields['eMoney'];
		$data[$rs->fields['eCertifiedId']]['tBankLoansDate'] = $list[$i]['tBankLoansDate'];
		$rs->MoveNext();
	}
}


function getTaxData($id){
	global $conn;
	$sql = "SELECT tId FROM tBankTrans WHERE tObjKind2Item = '".$id."'";
	$rs = $conn->Execute($sql);


	return $rs->fields['tId'];
}


##

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("第一建經代墊稅請款明細總表");
$objPHPExcel->getProperties()->setDescription("第一建經代墊稅請款明細總表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('第一建經代墊稅請款明細總表');

//寫入清單標題列資料
// $con = '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,案件狀態日期,進案日期,實際點交日期,銀行出款日期,地政士姓名,標的物座落,狀態'."\n" ;
$col = 65;
$row = 1;
$objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$title = (date('Y')-1911).date('m').date('d').'第一建經代墊稅請款明細總表';
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$title);


$col = 65;
$row = 3;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'履保號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'繳稅日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'請款日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'土地增值稅');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'房屋稅');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地價稅');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'契稅');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'印花稅');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方預收款項');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'工程受益費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'其他');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合計金額');
$objPHPExcel->getActiveSheet()->getStyle("A3:N3")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$row++;
$num = 1;
$totalMoney = 0;
if (is_array($data)) {
	foreach ($data as $k => $v) {
		$col = 65;
		$objPHPExcel->getActiveSheet()->getStyle("A".$row.":N".$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($num++));
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $k,PHPExcel_Cell_DataType::TYPE_STRING); 
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['tBankLoansDate']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['tObjKind2Date']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['土地增值稅']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['房屋稅']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['地價稅']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['契稅']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['印花稅']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['買方預收款項']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['工程受益費']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['其他']));
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,getZero($v['totalMoney']));
		$totalMoney += $v['totalMoney'];
		$row++;
	}
}
$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總筆數:');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,count($data));
$row++;
$col = 65;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總金額:');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$totalMoney);
$row++;
$col = 65;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':N'.$row);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'此至');
$row++;
$col = 65;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':N'.$row);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'台新國際商業銀行');
$row++;
$col = 65;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':N'.$row);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'委託人簽章');

$row++;

function getZero($val){
	if ($val == '') {
		$val = 0;
	}

	return $val;
}

$_file = iconv('UTF-8', 'BIG5', '第一建經代墊稅請款明細總表') ;
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
