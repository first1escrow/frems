<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;

// header("Content-Type:text/html; charset=utf-8"); 
// $undertaker = 5 ;
if ($undertaker) {
	$search_txt = " AND pId = '".$undertaker."'";
}
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN (5,6) AND pJob =1 ".$search_txt;

$rs = $conn->Execute($sql);

$undertakerData = array();
while (!$rs->EOF) {
	$undertakerData[$rs->fields['pId']] =  $rs->fields['pName'];


	$rs->MoveNext();
}

$sql = "SELECT qSend,qDateStart,qContent FROM tQuestionaire WHERE qId = '".$_POST['id']."'";
$rs = $conn->Execute($sql);
$Questionaire = $rs->fields;

$qContent = json_decode(base64_decode($Questionaire['qContent']),true);

// 	echo "<pre>";
// print_r($qContent);
// die;

// die;



$excelData = array();
foreach ($undertakerData as $key => $value) {
	$json = file_get_contents('http://first.twhg.com.tw/includes/report/getQuestionAnalysis.php?sDate='.urlencode($sDate).'&eDate='.urlencode($eDate).'&id='.$_POST['id'].'&undertaker='.$key);
	$arr = json_decode($json,true);

	foreach ($arr['count'] as $k => $v) {


		

		foreach ($v['item'] as $itemkey => $itemValue) {
			$excelData[$key][$k]['item'][$itemkey]['value'] = $itemValue['value'];
			$excelData[$key][$k]['item'][$itemkey]['score'] = $itemValue['score'];
			

			
		}

		// echo $v['score'];

		$excelData[$key][$k]['value'] = $v['value'];
		$excelData[$key][$k]['score'] = $v['score'];
		$excelData[$key][$k]['txt'] = $itemValue['txt'];
		
	}

}



// 	echo "<pre>";
// print_r($excelData);
// die;
##################################################
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("問卷報表");
$objPHPExcel->getProperties()->setDescription("問卷統計報表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('問卷統計報表');
//寫入表頭資料
// ##顏色
// $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFill()->getStartColor()->setARGB('FDFF37');

// ##寬度
// $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
##

##標頭
$colArr[0] = '';
$colArr[1] = 66;//B
$colArr1[0] = '';
$colArr1[1] = 66;//B

$colArr2[0] = '';
$colArr2[1] = 66;//B

$colArr3[0] = '';
$colArr3[1] = 65;//B

foreach ($undertakerData as $key => $value) {
	$row = 1;

	$colname = ($colArr[0] == '')?chr($colArr[1]):chr($colArr[0]).chr($colArr[1]);

	// echo $colArr[0]."_".$colArr[1]."<bR>";
	//colname2
	$colArr1[0] = $colArr[0];
	$colArr1[1] = $colArr[1]+1;
	$colArr1 = setNumber($colArr1);



	$colname2 = ($colArr1[0] == '')?chr($colArr1[1]):chr($colArr1[0]).chr($colArr1[1]);
	//
 
	$objPHPExcel->getActiveSheet()->setCellValue($colname.$row,$value);
	$objPHPExcel->getActiveSheet()->mergeCells($colname.$row.':'.$colname2.$row);
	$colArr[1]++;
	$colArr[1]++;
	
	$colArr = setNumber($colArr,1);

	
	// echo $colname.$row.':'.$colname2.$row."<bR>";
	

	// 

}
// die;
$row = 2;
	// die;
foreach ($qContent as $k => $v) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v['question']);
	$row++;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'選項名稱');
	
	$colArr2[0] = '';//B
	$colArr2[1] = 66;//B
	foreach ($undertakerData as $key => $value) {
		$row2 = $row;

		$colname3 = ($colArr2[0] == '')?chr($colArr2[1]):chr($colArr2[0]).chr($colArr2[1]);
		$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,'選項數');
		$colArr2[1]++;
		$colArr2 = setNumber($colArr2);

		$colname3 = ($colArr2[0] == '')?chr($colArr2[1]):chr($colArr2[0]).chr($colArr2[1]);
		$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,'分數');
		$colArr2[1]++;
		$colArr2 = setNumber($colArr2);
		
		##
		$row2++;
		


	}
	

	// print_r($v['item']);
	// die;

	foreach ($v['item'] as $ikey => $ivalue) {
			$colArr3[0] = '';
			$colArr3[1] = 65;//A

			$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
			$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,$ivalue);
			$colArr3[1]++;
			$colArr3 = setNumber($colArr3);

			foreach ($undertakerData as $key => $value) {
				$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
				$excelData[$key][$k]['item'][($ikey+1)]['value'] = ($excelData[$key][$k]['item'][($ikey+1)]['value'])?$excelData[$key][$k]['item'][($ikey+1)]['value']:0;
				$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,$excelData[$key][$k]['item'][($ikey+1)]['value']);
				$colArr3[1]++;
				$colArr3 = setNumber($colArr3);


				$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
				$excelData[$key][$k]['item'][($ikey+1)]['score'] = ($excelData[$key][$k]['item'][($ikey+1)]['score'])?$excelData[$key][$k]['item'][($ikey+1)]['score']:0;
				$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,$excelData[$key][$k]['item'][($ikey+1)]['score']);
				$colArr3[1]++;
				$colArr3 = setNumber($colArr3);
				


			}

			$row2++;

	}

	//合計
	$colArr3[0] = '';
	$colArr3[1] = 65;//A
	$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
	$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,'合計');
	$colArr3[1]++;
	$colArr3 = setNumber($colArr3);

	
	foreach ($undertakerData as $key => $value) {
		$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
		$excelData[$key][$k]['value'] = ($excelData[$key][$k]['value'])?$excelData[$key][$k]['value']:0;
		$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,$excelData[$key][$k]['value']);
		$colArr3[1]++;
		$colArr3 = setNumber($colArr3);

		$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
		$excelData[$key][$k]['score'] = ($excelData[$key][$k]['score'])?$excelData[$key][$k]['score']:0;
		$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,$excelData[$key][$k]['score']);
		$colArr3[1]++;
		$colArr3 = setNumber($colArr3);


	}
	$row2++;
	//意見回饋
	$colArr3[0] = '';
	$colArr3[1] = 65;//A
	$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
	$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,'意見回饋');
	$colArr3[1]++;
	$colArr3 = setNumber($colArr3);

	
	foreach ($undertakerData as $key => $value) {
		$colname3 = ($colArr3[0] == '')?chr($colArr3[1]):chr($colArr3[0]).chr($colArr3[1]);
		$objPHPExcel->getActiveSheet()->setCellValue($colname3.$row2,$excelData[$key][$k][$ikey]['txt']);
		$colArr3[1]++;
		$colArr3 = setNumber($colArr3);


	}
	$row2++;
	
	$row = $row2;
	$row++;

}

//檢查是否超過Z欄位
function setNumber($arr,$plus=''){
	//colname colArr
				if ($arr[1] > 90) { //到Z了所以要回到A
					$arr[1] = ($plus)?(65+$plus):65;

					if ($arr[0] != '') {//進位
						$arr[0]++;
					}else{
						$arr[0] = 65;
					}
				}

	return $arr;
}


	// $json = file_get_contents('http://first.twhg.com.tw/includes/report/getQuestionAnalysis.php?sDate='.urlencode($sDate).'&eDate='.urlencode($eDate).'&id='.$_POST['id'].'&undertaker='.$key);
	// if ($json) {
	// 	$arr = json_decode($json,true);

		

	// 	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$value);
	// 	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.':'.chr($col++).$row);
	// 	$col++;
	// 	$row++;
	// 	foreach ($arr['count'] as $k => $v) {
	// 		$col2 = 65;
	// 		$objPHPExcel->getActiveSheet()->setCellValue(chr($col2).$row,$v['title']);
	// 		foreach ($v['item'] as $itemkey => $itemValue) {
	// 			// $excelData[$v['title']][$itemValue['item']][$key]['value'] = $itemValue['value'];
	// 			// $excelData[$v['title']][$itemValue['item']][$key]['score'] = $itemValue['score'];
	// 		}
			
	// 	}
	// }
	
	//

	





// 		echo "<pre>";
// 		print_r($excelData);
// 		echo "</pre>";





// $objPHPExcel->getActiveSheet()->setCellValue('A1','排名');
// $objPHPExcel->getActiveSheet()->setCellValue('B1','店編號');
// $objPHPExcel->getActiveSheet()->setCellValue('C1','店名');
// if ($sort == 1) {
// 	$objPHPExcel->getActiveSheet()->setCellValue('D1','案件數量');
// }else{
// 	$objPHPExcel->getActiveSheet()->setCellValue('D1','業績(總價金)');
// }
// $objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');

// $col = 65;


$objPHPExcel->setActiveSheetIndex(0);

// echo "<pre>";
// print_r($list2);
// echo "</pre>";
// echo 'AAAA';


##
$_file = 'question.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit ;


?>