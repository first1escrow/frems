<?php
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
$query = '' ; 
$functions = '' ;


$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件統計表");
$objPHPExcel->getProperties()->setDescription("第一建經案件統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('業務案件統計表2');

//寫入清單標題列資料
// $con = '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,案件狀態日期,進案日期,實際點交日期,銀行出款日期,地政士姓名,標的物座落,狀態'."\n" ;
$col = 65;
$row = 4;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'出款保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'銀行出款日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
$row++;

for ($i = 0 ; $i < $max ; $i ++) {
	$col = 65;

	$totalMoney += $arr[$i]['cTotalMoney'] ;
	$certifiedMoney += $arr[$i]['cCertifiedMoney'] ;
	$transMoney += $arr[$i]['tMoney'] ;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i+1));
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $arr[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['exbCode']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['exbStore']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['owner']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['buyer']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['cTotalMoney']);
	
	$tmp = round($arr[$i]['cTotalMoney']*0.0006); //萬分之六
	$tmp2 = round($arr[$i]['cTotalMoney']*0.0006)*0.1;

	if(($tmp-$tmp2)>$arr[$i]['cCertifiedMoney']) //合約保證費 如果未達6/10000的合約保證費  在合約保證費的金額位置 加註星星 
	{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'*'.$arr[$i]['cCertifiedMoney']);
	}else
	{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['cCertifiedMoney']);
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['tMoney']);

	if ($status=='3') {
		$date= $arr[$i]['cEndDate'] ;
	}
	else {
		$date= $arr[$i]['cSignDate'] ;
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);


	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['cApplyDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['cEndDate']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['tBankLoansDate']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['scrivener']);

	$zc = $arr[$i]['zCity'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/","",$arr[$i]['cAddr']) ;
	$zc = $arr[$i]['zArea'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/","",$arr[$i]['cAddr']) ;

	$arr[$i]['cAddr'] = $arr[$i]['zCity'].$arr[$i]['zArea'].$arr[$i]['cAddr'] ;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['cAddr']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$i]['status']);

	//color
	#顏色
	if ($arr[$i]['branch1'] > 0 && ($arr[$i]['bScrRecall'] != 0 || $arr[$i]['bScrRecall1'] != 0 || $arr[$i]['bScrRecall2'] != 0) ) {
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':P'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':P'.$row)->getFill()->getStartColor()->setARGB('FF7878');
	}
	

	unset($tmp);unset($tmp2);unset($date);

	
	$row++;
}

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:B2');
$objPHPExcel->getActiveSheet()->mergeCells('C1:E1');
$objPHPExcel->getActiveSheet()->mergeCells('C2:E2');
$objPHPExcel->getActiveSheet()->mergeCells('F1:H1');
$objPHPExcel->getActiveSheet()->mergeCells('F2:H2');
$objPHPExcel->getActiveSheet()->mergeCells('I1:J1');
$objPHPExcel->getActiveSheet()->mergeCells('I2:J2');


$objPHPExcel->getActiveSheet()->setCellValue('A1','案件總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('C1','買賣總價金額');
$objPHPExcel->getActiveSheet()->setCellValue('F1','合約總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('I1','出款總保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('A2',$max);
$objPHPExcel->getActiveSheet()->setCellValue('C2',$totalMoney);
$objPHPExcel->getActiveSheet()->setCellValue('F2',$certifiedMoney);
$objPHPExcel->getActiveSheet()->setCellValue('I2',$transMoney);



$_file = iconv('UTF-8', 'BIG5', '業務案件統計表2') ;
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
