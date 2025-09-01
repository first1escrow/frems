<?php

include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;

##

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
$objPHPExcel->getActiveSheet()->setTitle('案件統計報表');

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
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋金[符合查詢條件]');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'銀行出款日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士業務');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'數量占比');
// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋數量');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋數量占比');
$row++;

// echo $max."<br>";

for ($i = 0 ; $i < count($caseAna[$realKey]['data']) ; $i ++) {

	//取得實際出款日
		
		

		if ($caseAna[$realKey]['data'][$i]['tBankLoansDate'] != '') {
			$caseAna[$realKey]['data'][$i]['tBankLoansDate'] = dateCg($caseAna[$realKey]['data'][$i]['tBankLoansDate']) ;
		}else{
			if ($caseAna[$realKey]['data'][$i]['cBankList'] != '') {
				
				$caseAna[$realKey]['data'][$i]['tBankLoansDate'] = dateCg($caseAna[$realKey]['data'][$i]['cBankList']) ;
				unset($tmp_d) ;
			}
		}
	##
	
		//取得各仲介店姓名與編號
		$bStore = getRealtyName($caseAna[$realKey]['data'][$i]['branch']) ;
		// $bNo = getRealtyNo($link,$caseAna[$realKey]['data'][$i]['cBranchNum']) ;
		$bNo = $caseAna[$realKey]['data'][$i]['bCode'];
		
		if ($caseAna[$realKey]['data'][$i]['branch1'] > 0) {
			$bStore .= ' '.getRealtyName($caseAna[$realKey]['data'][$i]['branch1']) ;
			// $bNo .= ' '.getRealtyNo($link,$caseAna[$realKey]['data'][$i]['cBranchNum1']) ;
			$bNo .= ' '.$caseAna[$realKey]['data'][$i]['bCode1'] ;
		}
		
		if ($caseAna[$realKey]['data'][$i]['branch2'] > 0) {
			$bStore .= ' '.getRealtyName($caseAna[$realKey]['data'][$i]['branch2']) ;
			// $bNo .= ' '.getRealtyNo($link,$caseAna[$realKey]['data'][$i]['cBranchNum2']) ;
			$bNo .= ' '.$caseAna[$realKey]['data'][$i]['bCode2'] ;
		}
		$caseAna[$realKey]['data'][$i]['bStore'] = $bStore ;
		$caseAna[$realKey]['data'][$i]['bId'] = $bNo ;

	$col = 65;

	unset($tmp);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i+1));
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $caseAna[$realKey]['data'][$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['bId']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['bStore']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['owner']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['buyer']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['cTotalMoney']);
	
	$tmp = round(($caseAna[$realKey]['data'][$i]['cTotalMoney']-$caseAna[$realKey]['data'][$i]['cFirstMoney'])*0.0006); //萬分之六
			
	if ($tmp > ($caseAna[$realKey]['data'][$i]['cCertifiedMoney']+10)) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['cCertifiedMoney']);
	}else
	{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['cCertifiedMoney']);
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['tBankLoansMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['showcCaseFeedBackMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$certifiedData[$caseAna[$realKey]['data'][$i]['cCertifiedId']][$realKey]['CaseFeedBackMoneyPart']);
	

	if ($caseAna[$realKey]['data'][$i]['cCaseStatus'] =='3') { //
		$date= $caseAna[$realKey]['data'][$i]['cEndDate'] ;
	}
	else {
		$date= $caseAna[$realKey]['data'][$i]['cSignDate'] ;
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);


	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['cApplyDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['cEndDate']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['tBankLoansDate']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['scrivener']);

	$zc = $caseAna[$realKey]['data'][$i]['zCity'] ;
	$caseAna[$realKey]['data'][$i]['cAddr'] = preg_replace("/$zc/","",$caseAna[$realKey]['data'][$i]['cAddr']) ;
	$zc = $caseAna[$realKey]['data'][$i]['zArea'] ;
	$caseAna[$realKey]['data'][$i]['cAddr'] = preg_replace("/$zc/","",$caseAna[$realKey]['data'][$i]['cAddr']) ;

	$caseAna[$realKey]['data'][$i]['cAddr'] = $caseAna[$realKey]['data'][$i]['zCity'].$caseAna[$realKey]['data'][$i]['zArea'].$caseAna[$realKey]['data'][$i]['cAddr'] ;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['cAddr']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['status']);

	//color

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['salesName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$caseAna[$realKey]['data'][$i]['Scrsales']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$certifiedData[$caseAna[$realKey]['data'][$i]['cCertifiedId']][$realKey]['part']);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$certifiedData[$caseAna[$realKey]['data'][$i]['cCertifiedId']]['FeedCount']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$certifiedData[$caseAna[$realKey]['data'][$i]['cCertifiedId']][$realKey]['part2']);
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
$objPHPExcel->getActiveSheet()->setCellValue('F1','合約保證費金額');
$objPHPExcel->getActiveSheet()->setCellValue('I1','回饋金額');
$objPHPExcel->getActiveSheet()->setCellValue('K1','收入');
// $objPHPExcel->getActiveSheet()->setCellValue('I1','出款總保證費金額');



$objPHPExcel->getActiveSheet()->setCellValue('A2',$caseAna[$realKey]['count']);
$objPHPExcel->getActiveSheet()->setCellValue('C2',$caseAna[$realKey]['total']);


$objPHPExcel->getActiveSheet()->setCellValue('F2',$caseAna[$realKey]['certifiedMoney']);



$objPHPExcel->getActiveSheet()->setCellValue('I2',$caseAna[$realKey]['feedbackmoney']);
$objPHPExcel->getActiveSheet()->setCellValue('K2',$caseAna[$realKey]['money']);
// $objPHPExcel->getActiveSheet()->setCellValue('I2',$transMoney);



$_file = iconv('UTF-8', 'BIG5', '案件統計表') ;
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
