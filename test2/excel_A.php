<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php';
##

$sql = "SELECT zCity FROM tZipArea GROUP BY zCity";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$zip[$rs->fields['zCity']] = array();

	$rs->MoveNext();
}

$zip = array();
// $zip['新北市'] = array();
// $zip['台北市'] = array();
// $zip['桃園市'] = array();
// $zip['台中市'] = array();
// $zip['台南市'] = array();
// $zip['高雄市'] = array();
// $zip['宜蘭縣'] = array();
// $zip['新竹縣'] = array();
// $zip['苗栗縣'] = array();
// $zip['彰化縣'] = array();
// $zip['南投縣'] = array();
// $zip['雲林縣'] = array();
// $zip['嘉義縣'] = array();
// $zip['屏東縣'] = array();
// $zip['台東縣'] = array();
// $zip['花蓮縣'] = array();
// $zip['澎湖縣'] = array();
// $zip['基隆市'] = array();
// $zip['新竹市'] = array();
// $zip['嘉義市'] = array();
// $zip['金門縣'] = array();
// $zip['連江縣'] = array();


##
$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子
$sSignDate = '2019-01-01' ;
$eSignDate = '2020-10-31' ;

$query .= " AND cas.cSignDate >='".$sSignDate."' AND cas.cSignDate <= '".$eSignDate."' AND cas.cCaseStatus != 8";

$query ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cSignDate as cSignDate, 
	rea.cBrand as brand,
	rea.cBrand1 as brand1,
	rea.cBrand2 as brand2,
	rea.cBrand2 as brand3,
	rea.cBranchNum as branch,
	rea.cBranchNum1 as branch1,
	rea.cBranchNum2 as branch2,
	rea.cBranchNum3 as branch3,
	(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum) bCategory,
	(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum1) bCategory1,
	(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum2) bCategory2,
	(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum3) bCategory3,
	(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) FROM tBranch WHERE bId=rea.cBranchNum) bCity,
	(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) FROM tBranch WHERE bId=rea.cBranchNum1) bCity1,
	(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) FROM tBranch WHERE bId=rea.cBranchNum2) bCity2,
	(SELECT (SELECT zCity FROM tZipArea WHERE zZip=bZip) FROM tBranch WHERE bId=rea.cBranchNum3) bCity3,
	(SELECT zCity FROM tZipArea WHERE zZip = sCpZip1) AS sCity
FROM 
	tContractCase AS cas 
LEFT JOIN 
	tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
LEFT JOIN 
	tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tScrivener AS scr ON scr.sId = csc.cScrivener
WHERE
'.$query.' 
GROUP BY
	cas.cCertifiedId
ORDER BY 
	cas.cApplyDate,cas.cId,cas.cSignDate ASC;
' ;

$rs = $conn->Execute($query);
$total = 0;
while (!$rs->EOF) {
	$year = (substr($rs->fields['cSignDate'], 0,4)-1911);
	$month = substr($rs->fields['cSignDate'], 5,2);

	if ($month < 11) {//只取1~10月
		$brachCount = 1;
		$brand = ($rs->fields['brand'] == 1)?'T':'O';
		$brand1 = ($rs->fields['brand1'] == 1)?'T':'O';
		$brand2 = ($rs->fields['brand2'] == 1)?'T':'O';
		$brand3 = ($rs->fields['brand3'] == 1)?'T':'O';
			


		if ($rs->fields['branch1'] > 0) {
			$brachCount++;
		}

		if ($rs->fields['branch2'] > 0) {
			$brachCount++;
		}

		if ($rs->fields['branch3'] > 0) {
			$brachCount++;
		}

		

		if ($brachCount > 1) {
			$part = round((1/$brachCount),2);
			//bCategory
			

			if ($rs->fields['branch'] != 0) {
				

				if ($rs->fields['bCity'] == '') {
					if (empty($zip[$rs->fields['sCity'].$brand][$year])) {
						$zip[$rs->fields['sCity'].$brand][$year] = 0;
					}
					$zip[$rs->fields['sCity'].$brand][$year]+=$part;
					// $zip[$rs->fields['sCity'].$brand][$year.$month]+=$part;
				}else{
					if ($rs->fields['bCategory'] != 2) {
						if (empty($zip[$rs->fields['bCity'].$brand][$year])) {
							$zip[$rs->fields['bCity'].$brand][$year] = 0;
						}

						$zip[$rs->fields['bCity'].$brand][$year]+=$part;
					}
					
					// $zip[$rs->fields['bCity'].$brand][$year.$month]+=$part;
				}
			}

			if ($rs->fields['branch1'] != 0) {
				if ($rs->fields['bCity1'] == '') {
					if (empty($zip[$rs->fields['sCity'].$brand1][$year])) {
						$zip[$rs->fields['sCity'].$brand1][$year] = 0;
					}
					$zip[$rs->fields['sCity'].$brand1][$year]+=$part;
					// $zip[$rs->fields['sCity'].$brand1][$year.$month]+=$part;
				}else{
					if ($rs->fields['bCategory1'] != 2) {
						if (empty($zip[$rs->fields['bCity1'].$brand1][$year])) {
							$zip[$rs->fields['bCity1'].$brand1][$year] = 0;
						}

						$zip[$rs->fields['bCity1'].$brand1][$year]+=$part;
					}
					
					
					// $zip[$rs->fields['bCity1'].$brand1][$year.$month]+=$part;
				}

			}

			if ($rs->fields['branch2'] != 0) {
				if ($rs->fields['bCity2'] == '') {
					if (empty($zip[$rs->fields['sCity'].$brand2][$year])) {
						$zip[$rs->fields['sCity'].$brand2][$year] = 0;
					}
					$zip[$rs->fields['sCity'].$brand2][$year]+=$part;
					// $zip[$rs->fields['sCity'].$brand2][$year.$month]+=$part;
				}else{
					if ($rs->fields['bCategory2'] != 2) {
						if (empty($zip[$rs->fields['bCity2'].$brand2][$year])) {
							$zip[$rs->fields['bCity2'].$brand2][$year] = 0;
						}
						$zip[$rs->fields['bCity2'].$brand2][$year]+=$part;
					}
					
					// $zip[$rs->fields['bCity2'].$brand2][$year.$month]+=$part;
				}
			}

			if ($rs->fields['branch3'] != 0) {
				if ($rs->fields['bCity3'] == '') {
					if (empty($zip[$rs->fields['sCity'].$brand3][$year])) {
						$zip[$rs->fields['sCity'].$brand3][$year] = 0;
					}
					$zip[$rs->fields['sCity'].$brand3][$year]+=$part;
					// $zip[$rs->fields['sCity'].$brand3][$year.$month]+=$part;
				}else{
					if ($rs->fields['bCategory3'] != 2) {
						if (empty($zip[$rs->fields['bCity3'].$brand3][$year])) {
							$zip[$rs->fields['bCity3'].$brand3][$year] = 0;
						}
						$zip[$rs->fields['bCity3'].$brand3][$year]+=$part;
						// $zip[$rs->fields['bCity'].$brand3][$year.$month]+=$part;
					}
					
				}
			}



		}else{

			


			if ($rs->fields['branch'] == 505) { //非仲介成交

				if ($rs->fields['sCity'] == '') {
					// $error[] = $rs->fields['cCertifiedId'];
				}else{

					if (empty($zip[$rs->fields['sCity'].$brand][$year])) {
						$zip[$rs->fields['sCity'].$brand][$year] = 0;
					}
					$zip[$rs->fields['sCity'].$brand][$year]++;
					// $zip[$rs->fields['sCity'].$brand][$year.$month]++;
				}

				
			}else{
				

				if ($rs->fields['bCity'] == '') {
					if (empty($zip[$rs->fields['sCity'].$brand][$year])) {
						$zip[$rs->fields['sCity'].$brand][$year] = 0;
					}
					

					if ($rs->fields['cCertifiedId'] == '070496511') {
						$zip[$rs->fields['sCity'].'O'][$year]++;
					}else{
						$zip[$rs->fields['sCity'].$brand][$year]++;
					}
					// $zip[$rs->fields['sCity'].$brand][$year.$month]++;
					// $error[] = $rs->fields['cCertifiedId'];


				}else{
					if ($rs->fields['bCategory'] != 2) {
						if (empty($zip[$rs->fields['bCity'].$brand][$year])) {
							$zip[$rs->fields['bCity'].$brand][$year] = 0;
						}
						$zip[$rs->fields['bCity'].$brand][$year]++;
					}
					
					// $zip[$rs->fields['bCity'].$brand][$year.$month]++;
				}
			}
			
		}

	}
	

	// echo $year;
	// die;
	
	// if ($month < 10 ) { //只取1~9月
	// 	$total++;
	// 	// echo $rs->fields['cSignDate']."_";
	// 	$brachCount = 1;

	// 	if ($rs->fields['branch1'] > 0) {
	// 		$brachCount++;
	// 	}

	// 	if ($rs->fields['branch2'] > 0) {
	// 		$brachCount++;
	// 	}

	// 	if ($rs->fields['branch3'] > 0) {
	// 		$brachCount++;
	// 	}

	// 	//ZZZZZZ
	// 	if ($brachCount > 1) {
	// 		$part = round((1/$brachCount),2);
			


	// 		if ($rs->fields['branch'] != 0) {
				

	// 			if ($rs->fields['bCity'] == '') {
	// 				$zip[$rs->fields['sCity']][$year]+=$part;
	// 				$zip[$rs->fields['sCity']][$year.$month]+=$part;
	// 			}else{
	// 				$zip[$rs->fields['bCity']][$year]+=$part;
	// 				$zip[$rs->fields['bCity']][$year.$month]+=$part;
	// 			}
	// 		}

	// 		if ($rs->fields['branch1'] != 0) {
	// 			if ($rs->fields['bCity1'] == '') {
	// 				$zip[$rs->fields['sCity']][$year]+=$part;
	// 				$zip[$rs->fields['sCity']][$year.$month]+=$part;
	// 			}else{
	// 				$zip[$rs->fields['bCity1']][$year]+=$part;
	// 				$zip[$rs->fields['bCity1']][$year.$month]+=$part;
	// 			}

	// 		}

	// 		if ($rs->fields['branch2'] != 0) {
	// 			if ($rs->fields['bCity2'] == '') {
	// 				$zip[$rs->fields['sCity']][$year]+=$part;
	// 				$zip[$rs->fields['sCity']][$year.$month]+=$part;
	// 			}else{
	// 				$zip[$rs->fields['bCity2']][$year]+=$part;
	// 				$zip[$rs->fields['bCity2']][$year.$month]+=$part;
	// 			}
	// 		}

	// 		if ($rs->fields['branch3'] != 0) {
	// 			if ($rs->fields['bCity3'] == '') {
	// 				$zip[$rs->fields['sCity']][$year]+=$part;
	// 				$zip[$rs->fields['sCity']][$year.$month]+=$part;
	// 			}else{
	// 				$zip[$rs->fields['bCity3']][$year]+=$part;
	// 				$zip[$rs->fields['bCity']][$year.$month]+=$part;
	// 			}
	// 		}



	// 	}else{
	// 		if ($rs->fields['branch'] == 505) { //非仲介成交

	// 			if ($rs->fields['sCity'] == '') {
	// 				// $error[] = $rs->fields['cCertifiedId'];
	// 			}else{
	// 				$zip[$rs->fields['sCity']][$year]++;
	// 				$zip[$rs->fields['sCity']][$year.$month]++;
	// 			}

				
	// 		}else{
				

	// 			if ($rs->fields['bCity'] == '') {
	// 				$zip[$rs->fields['sCity']][$year]++;
	// 				$zip[$rs->fields['sCity']][$year.$month]++;
	// 				// $error[] = $rs->fields['cCertifiedId'];


	// 			}else{
	// 				$zip[$rs->fields['bCity']][$year]++;
	// 				$zip[$rs->fields['bCity']][$year.$month]++;
	// 			}
	// 		}
			
	// 	}


	// }



	$rs->MoveNext();
}


print_r($zip);
die;


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
$objPHPExcel->getActiveSheet()->setTitle('統計報表');

//寫入清單標題列資料
// $con = '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,案件狀態日期,進案日期,實際點交日期,銀行出款日期,地政士姓名,標的物座落,狀態'."\n" ;
$col = 66;
$row =1;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,"108年(1月至9月)");
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,"109年(1月至9月)");

for ($i=108; $i <=109 ; $i++) { 
	for ($j=1; $j <=9; $j++) { 
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,"".$i."年 ".$j."月");
	}
	
}


$row =2;

foreach ($zip as $k => $v) {
	$col = 65;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k." / 移轉登記");

	if ($v['108'] == '') {
		$v['108'] = 0;
	}

	if ($v['109'] == '') {
		$v['109'] = 0;
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['108']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['109']);

	for ($i=108; $i <=109 ; $i++) { 
		for ($j=1; $j <=9; $j++) { 
			if ($v[$i.str_pad($j, 2,0,STR_PAD_LEFT)] == '') {
				$v[$i.str_pad($j, 2,0,STR_PAD_LEFT)] = 0;
			}
			// echo $i.str_pad($j, 2,0,STR_PAD_LEFT);
			// die;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v[$i.str_pad($j, 2,0,STR_PAD_LEFT)]);
		}
		
	}

	$row++;
	
}

			
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("/var/www/html/first.twhg.com.tw/test2/log/count".date(YmdHis).".xlsx");
	
exit ;


?>
