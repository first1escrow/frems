<?php

$max = count($arr) ;

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("回饋案件表");
$objPHPExcel->getProperties()->setDescription("第一建經案件統計表");

$index = 0;
if ($cat == 1 || $cat == 2 || $cat == 3) {
	
	//指定目前工作頁
	$objPHPExcel->setActiveSheetIndex($index);
	//命名工作表標籤
	$objPHPExcel->getActiveSheet()->setTitle('回饋案件表');

	//寫入清單標題列資料
	$col = 65;
	$row = 1;
	//序號	保證號碼	仲介店名	賣方	買方	地政士	總價金	合約保證費	仲介回饋金額	地政士回饋金額	地政士特殊回饋金額	其他回饋對象	其他回饋金	業績歸屬	狀態
		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約保證費');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介回饋金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士回饋金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士特殊回饋金額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'其他回饋金');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'業績歸屬');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'收件人(地政士)');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'寄送地址(地政士)');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'收件人(仲介)');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'寄送地址(仲介)');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'其他回饋對象');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'寄送地址(其他)');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總回饋比例');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'履保費出款日');
	$row++;

	$no = 1;
	for ($i = 0 ; $i < $max ; $i ++) {
		$col = 65;
		
		$color = ($arr[$i]['cBranchNum1'] > 0 || $arr[$i]['cBranchNum2'] > 0 || $arr[$i]['ScrivenerSPFeedMoney'] > 0 || count($arr[$i]['otherFeed']) > 0) ? "FFB630": "FFFFFF";

		$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFill()->getStartColor()->setARGB($color);

		if ($arr[$i]['cBranchNum'] > 0) {
			$feedData['storeName'] = $arr[$i]['bCode'].str_pad($arr[$i]['cBranchNum'], 5,0,STR_PAD_LEFT).$arr[$i]['BrandName'].$arr[$i]['BranchName'];
			$feedData['storeId'] = ($arr[$i]['cBranchNum'] == 505 || $arr[$i]['cFeedbackTarget'] == 2)? $arr[$i]['cScrivener']:$arr[$i]['cBranchNum'];
			$feedData['sales'] = getSales($arr[$i]['cCertifiedId'],$arr[$i]['cBranchNum'],$arr[$i]['cFeedbackTarget'],$arr[$i]['cScrivener']);

			if ($arr[$i]['cCaseFeedback'] == 0) {
				if ($arr[$i]['cFeedbackTarget'] == 1) {
					$feedData['BranchFeedMoney'] = $arr[$i]['cCaseFeedBackMoney'];	

				}else{
					$feedData['ScrivenerFeedMoney'] = $arr[$i]['cCaseFeedBackMoney'];
				}


				$feedData['FeedbackTargetMark'] = ($arr[$i]['cFeedbackTarget'] == 2 && $arr[$i]['cBranchNum'] != 505)?2:1;
			}

			SameRowAdd($arr[$i],$row,$no,$color,$feedData);
			$row++; $no++;

		}
		unset($feedData);
		
		if ($arr[$i]['cBranchNum1'] > 0 ) {
			$feedData['storeName'] = $arr[$i]['bCode1'].str_pad($arr[$i]['cBranchNum1'], 5,0,STR_PAD_LEFT).$arr[$i]['BrandName1'].$arr[$i]['BranchName1'];
			$feedData['storeId'] = ($arr[$i]['cBranchNum1'] == 505 || $arr[$i]['cFeedbackTarget1'] == 2)? $arr[$i]['cScrivener']:$arr[$i]['cBranchNum1'];
			$feedData['sales'] = getSales($arr[$i]['cCertifiedId'],$arr[$i]['cBranchNum1'],$arr[$i]['cFeedbackTarget1'],$arr[$i]['cScrivener']);

			if ($arr[$i]['cCaseFeedback1'] == 0) {
				if ($arr[$i]['cFeedbackTarget1'] == 1) {
					$feedData['BranchFeedMoney'] = $arr[$i]['cCaseFeedBackMoney1'];	
				}else{
					$feedData['ScrivenerFeedMoney'] = $arr[$i]['cCaseFeedBackMoney1'];
				}

				$feedData['FeedbackTargetMark'] = ($arr[$i]['cFeedbackTarget1'] == 2 && $arr[$i]['cBranchNum1'] != 505)?2:1;
			}

			SameRowAdd($arr[$i],$row,$no,$color,$feedData);
			$row++; $no++;
		
		}
		unset($feedData);

		if ($arr[$i]['cBranchNum2'] > 0 ) {
			$feedData['storeName'] = $arr[$i]['bCode2'].str_pad($arr[$i]['cBranchNum2'], 5,0,STR_PAD_LEFT).$arr[$i]['BrandName2'].$arr[$i]['BranchName2'];
			$feedData['storeId'] = ($arr[$i]['cBranchNum2'] == 505  || $arr[$i]['cFeedbackTarget2'] == 2)? $arr[$i]['cScrivener']:$arr[$i]['cBranchNum2'];
			$feedData['sales'] = getSales($arr[$i]['cCertifiedId'],$arr[$i]['cBranchNum2'],$arr[$i]['cFeedbackTarget2'],$arr[$i]['cScrivener']);

			if ($arr[$i]['cCaseFeedback2'] == 0) {
				if ($arr[$i]['cFeedbackTarget2'] == 1) {
					$feedData['BranchFeedMoney'] = $arr[$i]['cCaseFeedBackMoney2'];	
				}else{
					$feedData['ScrivenerFeedMoney'] = $arr[$i]['cCaseFeedBackMoney2'];
				}

				$feedData['FeedbackTargetMark'] = ($arr[$i]['cFeedbackTarget2'] == 2 && $arr[$i]['cBranchNum2'] != 505)?2:1;
			}

			
			SameRowAdd($arr[$i],$row,$no,$color,$feedData);
			$row++; $no++;
		
		}
		unset($feedData);

		// //特殊回饋
		if ($arr[$i]['ScrivenerSPFeedMoney'] > 0) {
			$feedData['storeName'] = '地政士特殊回饋';
			$feedData['storeId'] = $arr[$i]['cScrivener'];
			$feedData['sales'] = getSales($arr[$i]['cCertifiedId'],0,2,$arr[$i]['cScrivener']); //getSales($cid,$b,$target,$sp=0)
			$feedData['ScrivenerSPFeedMoney'] = $arr[$i]['ScrivenerSPFeedMoney'];
			SameRowAdd($arr[$i],$row,$no,$color,$feedData);
			$row++; $no++;
			// $no++;
		}
		unset($feedData);

		##其他回饋##
		if (is_array($arr[$i]['otherFeed'])) {
		
			foreach ($arr[$i]['otherFeed'] as $k => $v) {
				

				$feedData['storeName'] = $v['store'];
				$feedData['fType'] = $v['fType'];
				$feedData['storeId'] = $v['fStoreId'];
				$feedData['otherFeed'] = $v['fMoney'];
				$feedData['sales'] = $v['sales'];

				SameRowAdd($arr[$i],$row,$no,$color,$feedData);
				$row++; $no++;
			
				
			}
			
		}
		
		unset($feedData);
		
	}

	##
	$index++;
}


//有勾選就顯示
if (is_array($sp)) {

	foreach ($sp as $k => $v) {
		$FeedBackType = mb_substr($v, 0,1);
		$FeedBackId = (int)mb_substr($v, 1);
		$col = 65;
		$row = 1;
		$no = 1;
	

		$objPHPExcel->createSheet() ;
		$objPHPExcel->setActiveSheetIndex($index) ;
		$objPHPExcel->getActiveSheet()->setTitle($arrayCategory2[$v]['bName']);
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約保證費');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'簽約日期');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋'.$arrayCategory2[$v]['bRecall'].'%');
		$row++;
		if (is_array($dataSp[$FeedBackId])) {
			foreach ($dataSp[$FeedBackId] as $key => $value) {

			

				$col = 65;
				$branchCode = $value['bCode'].str_pad($value['cBranchNum'],5,'0',STR_PAD_LEFT);
				$branchName = $value['BranchName'];
				// $Branchcount = 0;
				$checkCount = 1;// > 1 有別排的仲介

				if ($value['cBranchNum'] > 0) {
					// $Branchcount++;
					if ($FeedBackType  == 'b') {

						if ($value['cBrand'] != $FeedBackId) {
							$checkCount++;
						}
					}elseif ($FeedBackType == 'g') {
						if ($value['BranchGroup'] != $FeedBackId) {
							$checkCount++;
						}
					}
				}

				if ($value['cBranchNum1'] > 0) {
					// $Branchcount++;
					$branchCode .= ','.$value['bCode1'].str_pad($value['cBranchNum1'],5,'0',STR_PAD_LEFT);
					$branchName .= ','.$value['BranchName1'];
					if ($FeedBackType  == 'b') {
						if ($value['cBrand1'] != $FeedBackId) {
							$checkCount++;
						}
					}elseif ($FeedBackType == 'g') {
						if ($value['BranchGroup1'] != $FeedBackId) {
							$checkCount++;
						}
					}
					
				}

				if ($value['cBranchNum2'] > 0) {
					// $Branchcount++;
					$branchCode .= ','.$value['bCode2'].str_pad($value['cBranchNum2'],5,'0',STR_PAD_LEFT);
					$branchName .= ','.$value['BranchName2'];
					if ($FeedBackType  == 'b') {
						if ($value['cBrand2'] != $FeedBackId) {
							$checkCount++;
						}
					}elseif ($FeedBackType == 'g') {
						if ($value['BranchGroup2'] != $FeedBackId) {
							$checkCount++;
						}
					}
				}

				$date = ($value['cCaseStatus'] == '3' || $value['cCaseStatus'] == '4' || $value['cCaseStatus'] == '9')?$value['cEndDate']:$value['cSignDate'];

				$date = substr($date, 0,10);
				$addr = getBuildAddr($value['cCertifiedId']);
				
				if ($checkCount > 1) {
					$recall = ($arrayCategory2[$v]['bRecall']/$checkCount)/100;
				}else{
					$recall = $arrayCategory2[$v]['bRecall']/100;
				}

				
				
				$money =round( $value['cCertifiedMoney']*$recall);

				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$no);
				// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cCertifiedId']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
			
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branchCode);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branchName);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['owner']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['buyer']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cTotalMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cCertifiedMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,substr($value['cSignDate'], 0,10));
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$addr);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$money);
				$row++;
				$no++;
			}
		}
		

		$index++;
	}

	// if (in_array(16, $sp)) {
	// 	$col = 65;
	// 	$row = 1;
	// 	$no = 1;
	// 	$sql = "SELECT bRecall FROM tBranchGroup WHERE bId = 16";
	// 	$rs = $conn->Execute($sql);
	// 	// $objPHPExcel->setActiveSheetIndex($index);
	// 	$objPHPExcel->createSheet() ;
	// 	$objPHPExcel->setActiveSheetIndex($index) ;
	// 	$objPHPExcel->getActiveSheet()->setTitle('飛鷹');

	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約保證費');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'簽約日期');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋'.$rs->fields['bRecall'].'%');
	// 	$row++;
	// 	for ($i=0; $i < count($dataSp16); $i++) { 
	// 		$col = 65;
	// 		$branchCode = $dataSp16[$i]['bCode'].str_pad($dataSp16[$i]['cBranchNum'],5,'0',STR_PAD_LEFT);
	// 		$branchName = $dataSp16[$i]['BranchName'];
	// 		$count = 0;
	// 		if ($dataSp16[$i]['BranchGroup'] != 16) {
	// 				$count++;
	// 		}

	// 		if ($dataSp16[$i]['cBranchNum1'] > 0) {
	// 			$branchCode .= ','.$dataSp16[$i]['bCode1'].str_pad($dataSp16[$i]['cBranchNum1'],5,'0',STR_PAD_LEFT);
	// 			$branchName .= ','.$dataSp16[$i]['BranchName1'];
	// 			if ($dataSp16[$i]['BranchGroup1'] != 16) {
	// 				$count++;
	// 			}
				
	// 		}

	// 		if ($dataSp16[$i]['cBranchNum2'] > 0) {
	// 			$branchCode .= ','.$dataSp16[$i]['bCode2'].str_pad($dataSp16[$i]['cBranchNum2'],5,'0',STR_PAD_LEFT);
	// 			$branchName .= ','.$dataSp16[$i]['BranchName2'];
	// 			if ($dataSp16[$i]['BranchGroup2'] != 16) {
	// 				$count++;
	// 			}
	// 		}

	// 		$date = ($dataSp16[$i]['cCaseStatus'] == '3' || $dataSp16[$i]['cCaseStatus'] == '4' || $dataSp16[$i]['cCaseStatus'] == '9')?$dataSp16[$i]['cEndDate']:$dataSp16[$i]['cSignDate'];

	// 		$date = substr($date, 0,10);
	// 		$addr = getBuildAddr($dataSp16[$i]['cCertifiedId']);
			
	// 		if ($count > 0) {
	// 			$recall = ($rs->fields['bRecall']/2)/100;
	// 		}else{
	// 			$recall = $rs->fields['bRecall']/100;
	// 		}
			
	// 		$money =round( $dataSp16[$i]['cCertifiedMoney']*$recall);

	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$no);
	// 		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp16[$i]['cCertifiedId']);
	// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $dataSp16[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
		
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branchCode);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branchName);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp16[$i]['owner']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp16[$i]['buyer']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp16[$i]['cTotalMoney']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp16[$i]['cCertifiedMoney']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,substr($dataSp16[$i]['cSignDate'], 0,10));
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$addr);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$money);
	// 		$row++;
	// 		$no++;
	// 	}
	// 	// 序號	保證號碼	仲介店編號	仲介店名	賣方	買方	總價金	合約保證費	簽約日期	案件狀態日期	標的物座落	

	// 	$index++;
	// }

	// if (in_array(72, $sp)) {
	// 	$col = 65;
	// 	$row = 1;
	// 	$no = 1;
	// 	$sql = "SELECT bRecall FROM tBrand WHERE bId = 72";
	// 	$rs = $conn->Execute($sql);

	// 	$objPHPExcel->createSheet() ;
	// 	$objPHPExcel->setActiveSheetIndex($index) ;
	// 	$objPHPExcel->getActiveSheet()->setTitle('群義');

	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店編號');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約保證費');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'簽約日期');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'回饋'.$rs->fields['bRecall'].'%');
	// 	$row++;
	// 	for ($i=0; $i < count($dataSp72); $i++) { 
	// 		$col = 65;
	// 		$branchCode = $dataSp72[$i]['bCode'].str_pad($dataSp72[$i]['cBranchNum'],5,'0',STR_PAD_LEFT);
	// 		$branchName = $dataSp72[$i]['BranchName'];
	// 		$count = 0;
	// 		if ($dataSp72[$i]['cBrand'] != 72) {
	// 				$count++;
	// 		}
			
	// 		if ($dataSp72[$i]['cBranchNum1'] > 0) {
	// 			$branchCode .= ','.$dataSp72[$i]['bCode1'].str_pad($dataSp72[$i]['cBranchNum1'],5,'0',STR_PAD_LEFT);
	// 			$branchName .= ','.$dataSp72[$i]['BranchName1'];
	// 			if ($dataSp72[$i]['cBrand1'] != 72) {
	// 				$count++;
	// 			}
				
	// 		}

	// 		if ($dataSp72[$i]['cBranchNum2'] > 0) {
	// 			$branchCode .= ','.$dataSp72[$i]['bCode2'].str_pad($dataSp72[$i]['cBranchNum2'],5,'0',STR_PAD_LEFT);
	// 			$branchName .= ','.$dataSp72[$i]['BranchName2'];
	// 			if ($dataSp72[$i]['cBrand2'] != 72) {
	// 				$count++;
	// 			}
	// 		}

	// 		$date = ($dataSp72[$i]['cCaseStatus'] == '3' || $dataSp72[$i]['cCaseStatus'] == '4' || $dataSp72[$i]['cCaseStatus'] == '9')?$dataSp72[$i]['cEndDate']:$dataSp72[$i]['cSignDate'];

	// 		$date = substr($date, 0,10);
	// 		$addr = getBuildAddr($dataSp72[$i]['cCertifiedId']);
			
	// 		if ($count > 0) {
	// 			$recall = ($rs->fields['bRecall']/2)/100;
	// 		}else{
	// 			$recall = $rs->fields['bRecall']/100;
	// 		}
			
	// 		$money =round( $dataSp72[$i]['cCertifiedMoney']*$recall);

	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$no);
	// 		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp72[$i]['cCertifiedId']);
	// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $dataSp72[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
		
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branchCode);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$branchName);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp72[$i]['owner']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp72[$i]['buyer']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp72[$i]['cTotalMoney']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$dataSp72[$i]['cCertifiedMoney']);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,substr($dataSp72[$i]['cSignDate'], 0,10));
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$date);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$addr);
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$money);
	// 		$row++;$no++;
	// 	}
	// 	// 序號	保證號碼	仲介店編號	仲介店名	賣方	買方	總價金	合約保證費	簽約日期	案件狀態日期	標的物座落	

	// 	$index++;
	// }
}






function match_data($arr){
	// echo "<pre>";
	// print_r($arr);
	// echo "</pre>";
	for ($j=0; $j < count($arr); $j++) { 
		
		if (!@in_array($arr[$j]['zip'].getAddr($arr[$j]['zip']).$arr[$j]['addr'], $tmp['addr'])) {
			$tmp['name'][] = $arr[$j]['title'];
			$tmp['addr'][] = $arr[$j]['zip'].getAddr($arr[$j]['zip']).$arr[$j]['addr'];
		}
		
	}

	$data['name'] = implode("\r\n",$tmp['name']);
	$data['addr'] = implode("\r\n",$tmp['addr']);

	return $data;
}

function getAddr($zip)
{
	global $conn;

	$sql= "SELECT zCity,zArea FROM tZipArea WHERE zZip ='".$zip."'";

	$rs = $conn->Execute($sql);

	return $rs->fields['zCity'].$rs->fields['zArea'];
}

function SameRowAdd($data,$row,$no,$color,$feedData){
	global $conn;
	global $objPHPExcel;
	global $CaseFeedTotal;
	global $sales;
	global $bankLoansDate;
	

	$scrivenerMark = array();
	$sql = "SELECT sId FROM tScrivener WHERE sFeedbackMark = 1";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($scrivenerMark, $rs->fields['sId']);

		$rs->MoveNext();
	}

	$branchMark = array();

	$sql = "SELECT bId FROM tBranch WHERE bFeedbackMark = 1";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($branchMark, $rs->fields['bId']);

		$rs->MoveNext();
	}

	// $scrivenerMark = array(549,578,537,250,464,679,852,877,984,985,1213,320,123,2021,1575,117);


	$feedData['BranchFeedMoney'] = ($feedData['BranchFeedMoney'] > 0)? $feedData['BranchFeedMoney']:'0';
	$feedData['ScrivenerFeedMoney'] = ($feedData['ScrivenerFeedMoney'] > 0)? $feedData['ScrivenerFeedMoney']:'0';
	$feedData['ScrivenerSPFeedMoney'] = ($feedData['ScrivenerSPFeedMoney'] > 0)? $feedData['ScrivenerSPFeedMoney']:'0';
	$feedData['otherFeed'] = ($feedData['otherFeed'] > 0)? $feedData['otherFeed']:'0';
	
	$col = 65;

	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFill()->getStartColor()->setARGB($color);


	// echo $data['cCertifiedId']."<br>";//&& ($sales == 25 || $_SSSION['member_id'] == 6)
	if ($feedData['FeedbackTargetMark'] == 2) { 
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'*'.$no);
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$no);
	}

	
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $data['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$feedData['storeName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data['sName']."(".$data['sOffice'].")");
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data['cTotalMoney']);
	
	$tmp = round(($data['cTotalMoney']-$data['cFirstMoney'])*0.0006); //萬分之六
	$tmp2 = round(($data['cTotalMoney']-$data['cFirstMoney'])*0.0006)*0.1;

	if(($tmp-$tmp2)>$data['cCertifiedMoney']){ //合約保證費 如果未達6/10000的合約保證費  在合約保證費的金額位置 加註星星 
	
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'*'.$data['cCertifiedMoney']);
	}else{
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data['cCertifiedMoney']);
	}

	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$feedData['BranchFeedMoney']);
	
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$feedData['ScrivenerFeedMoney']);

	if ($feedData['ScrivenerSPFeedMoney'] == 0 && $data['cSpCaseFeedBackMoneyMark'] == 'x') {
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB("FFBFBF");
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$feedData['ScrivenerSPFeedMoney']);

	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$feedData['otherFeed']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$feedData['sales']);

	// echo $data['cScrivener']."_";

	if (in_array($data['cScrivener'], $scrivenerMark)) {
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->getStartColor()->setARGB("96FFFF");
	}

	//仲介標示
	if (in_array($data['cBranchNum'], $branchMark)) {
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->getStartColor()->setARGB("96FF96");
	}

	
	if (in_array($data['cBranchNum1'], $branchMark)) {
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->getStartColor()->setARGB("96FF96");
	}

	if (in_array($data['cBranchNum2'], $branchMark)) {
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row.":Q".$row)->getFill()->getStartColor()->setARGB("96FF96");
	}



	// //特殊回饋
	if ($feedData['ScrivenerSPFeedMoney'] > 0) {
		$tmp3 = match_data(getScrivenerData($data['cScrivener']));

		$objPHPExcel->getActiveSheet()->setCellValue('L'.$row,$tmp3['name']);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$row,$tmp3['addr']);
	}
	
	if ($feedData['BranchFeedMoney'] > 0 ) {
		$tmp3 = match_data(getBranchData($feedData['storeId']));

		$objPHPExcel->getActiveSheet()->setCellValue('N'.$row,$tmp3['name']);
		$objPHPExcel->getActiveSheet()->setCellValue('O'.$row,$tmp3['addr']);
		
		
	}else if ($feedData['ScrivenerFeedMoney'] > 0) {
		$tmp3 = match_data(getScrivenerData($feedData['storeId']));
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$row,$tmp3['name']);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$row,$tmp3['addr']);
	}else if($feedData['fType'] > 0){
		if ($feedData['fType'] == 2) {
			$tmp3 = match_data(getBranchData($feedData['storeId']));
		}elseif ($feedData['fType'] == 1) {
			$tmp3 = match_data(getScrivenerData($feedData['storeId']));
		}
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$row,$tmp3['name']);
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$row,$tmp3['addr']);
	}

	$recall = round(($CaseFeedTotal[$data['cCertifiedId']]/$data['cCertifiedMoney'])*100,2)."%";
	// echo "<pre>";
	// print_r($CaseFeedTotal);
	// die;
	// echo $CaseFeedTotal[$data['cCertifiedId']];
	// die;
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$row,$recall);

	$objPHPExcel->getActiveSheet()->setCellValue('S'.$row,$bankLoansDate[$data['cCertifiedId']]);
	

	unset($tmp);unset($tmp2);unset($tmp3);unset($recall);unset($scrivenerMark);

}


$_file = iconv('UTF-8', 'BIG5', '回饋案件表') ;
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

######################################################################33

function getSales($cid,$b,$target,$sp=0){
	global $conn;

	if ($sp > 0 && $target == 2) {
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '".$sp."'";
	}else{
		$sql = "SELECT
				(SELECT pName FROM tPeopleInfo WHERE pId=cSalesId) AS Name
			FROM
				tContractSales WHERE cCertifiedId = '".$cid."'  AND cBranch = '".$b."'";
	}


	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['Name'];

		$rs->MoveNext();
	}
	
	return @implode(',', $tmp);
}

function getBuildAddr($cId){
	global $conn;

	$sql = "SELECT cZip,cAddr FROM tContractProperty WHERE cCertifiedId = '".$cId."' AND cItem = 0";
	$rs = $conn->Execute($sql);
	$addr = getAddr($rs->fields['cZip']).$rs->fields['cAddr'];
	return $addr;
}
?>
