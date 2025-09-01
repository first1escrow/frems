<?php

require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php';
##
header("Content-Type:text/html; charset=utf-8"); 
$zip = array();
$zipGov = array();
$sql = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	// $zip[$rs->fields['zCity']] = array();
	// $zipGov[$rs->fields['zCity']] = array();

	// 標題欄位
	for ($i=$startYear; $i <=$endYear ; $i++) { 
		 
		$m = ($startYear == $i)?(int)$startMonth:1;//開始月份
		$m2 = ($endYear == $i)?(int)$endMonth:12;//結束月份
		
		for ($j=$m; $j <=$m2; $j++) { 
			$zip[$rs->fields['zCity']][str_pad($j, 2,0,STR_PAD_LEFT)][($i-1911)] = 0;
			$zipGov[$rs->fields['zCity']][str_pad($j, 2,0,STR_PAD_LEFT)][($i-1911)] = 0;
		}
		
	}

	$rs->MoveNext();
}

// 標題欄位
for ($i=$startYear; $i <=$endYear ; $i++) { 
		 
	$m = ($startYear == $i)?(int)$startMonth:1;//開始月份
	$m2 = ($endYear == $i)?(int)$endMonth:12;//結束月份
		
	for ($j=$m; $j <=$m2; $j++) { 
		// $zip[$rs->fields['zCity']]['data'][($i-1911).str_pad($j, 2,0,STR_PAD_LEFT)] = 0;
		$title[str_pad($j, 2,0,STR_PAD_LEFT)][($i-1911)] = ($i-1911)."年".$j."月";
	}
		
}
// echo "<pre>";
// print_r($title);
// die;


// $zip = array();



##
//建經
$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子


$query .= " AND cas.cSignDate >='".$sDate."' AND cas.cSignDate <= '".$eDate."' AND cas.cCaseStatus != 8";

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
// echo $query."<br>";
// die;
$rs = $conn->Execute($query);
$total = 0;
while (!$rs->EOF) {
	$checkKeyYear = (substr($rs->fields['cSignDate'], 0,4)-1911);
	$checkKeyMonth = substr($rs->fields['cSignDate'], 5,2);

	
	$brachCount = 1;
	
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
				

			if ($rs->fields['bCity'] == '') {//以防店家沒有地區
				if (empty($zip[$rs->fields['sCity']][$checkKey])) {
					$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear] = 0;
				}
				$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear]+=$part;
			}else{
				
				if (empty($zip[$rs->fields['bCity']][$checkKeyMonth][$checkKeyYear])) {
					$zip[$rs->fields['bCity']][$checkKeyMonth][$checkKeyYear] = 0;
				}

				$zip[$rs->fields['bCity']][$checkKeyMonth][$checkKeyYear]+=$part;
			}
		}

		if ($rs->fields['branch1'] != 0) {
			if ($rs->fields['bCity1'] == '') {
				if (empty($zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear])) {
					$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear] = 0;
				}
				$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear]+=$part;
					// $zip[$rs->fields['sCity'].$brand1][$checkKeyMonth][$checkKeyYear]+=$part;
			}else{
				
				if (empty($zip[$rs->fields['bCity1']][$checkKeyMonth][$checkKeyYear])) {
					$zip[$rs->fields['bCity1']][$checkKeyMonth][$checkKeyYear] = 0;
				}

				$zip[$rs->fields['bCity1']][$checkKeyMonth][$checkKeyYear]+=$part;
				
					
					// $zip[$rs->fields['bCity1'].$brand1][$checkKeyMonth][$checkKeyYear]+=$part;
			}

		}

			if ($rs->fields['branch2'] != 0) {
				if ($rs->fields['bCity2'] == '') {
					if (empty($zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear])) {
						$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear] = 0;
					}
					$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear]+=$part;
					// $zip[$rs->fields['sCity'].$brand2][$checkKeyMonth][$checkKeyYear]+=$part;
				}else{
					
					if (empty($zip[$rs->fields['bCity2']][$checkKeyMonth][$checkKeyYear])) {
						$zip[$rs->fields['bCity2']][$checkKeyMonth][$checkKeyYear] = 0;
					}
					$zip[$rs->fields['bCity2']][$checkKeyMonth][$checkKeyYear]+=$part;
				
				}
			}

			if ($rs->fields['branch3'] != 0) {
				if ($rs->fields['bCity3'] == '') {
					if (empty($zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear])) {
						$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear] = 0;
					}
					$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear]+=$part;
					// $zip[$rs->fields['sCity'].$brand3][$checkKeyMonth][$checkKeyYear]+=$part;
				}else{
					
					if (empty($zip[$rs->fields['bCity3']][$checkKeyMonth][$checkKeyYear])) {
						$zip[$rs->fields['bCity3']][$checkKeyMonth][$checkKeyYear] = 0;
					}
					$zip[$rs->fields['bCity3']][$checkKeyMonth][$checkKeyYear]+=$part;
						// $zip[$rs->fields['bCity'].$brand3][$checkKeyMonth][$checkKeyYear]+=$part;
					
				}
			}

	}else{

		if ($rs->fields['branch'] == 505) { //非仲介成交

			
			if (empty($zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear])) {
				$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear] = 0;
			}
			$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear]++;
			
		}else{
				

			if ($rs->fields['bCity'] == '') {
				if (empty($zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear])) {
					$zip[$rs->fields['sCity']][$checkKeyMonth][$checkKeyYear] = 0;
				}
					

			}else{
					// echo $rs->fields['bCity'];
					
				if (empty($zip[$rs->fields['bCity']][$checkKeyMonth][$checkKeyYear])) {
					$zip[$rs->fields['bCity']][$checkKeyMonth][$checkKeyYear] = 0;
				}
				$zip[$rs->fields['bCity']][$checkKeyMonth][$checkKeyYear]++;
					// if ($rs->fields['bCity'] == '嘉義縣') {
					// 	echo $rs->fields['cCertifiedId']."_";
					// 	print_r($zip[$rs->fields['bCity']][$checkKeyMonth][$checkKeyYear]);
					// }

				
					
			}
		}
			
	}

	$rs->MoveNext();
}

##政府
$sql = "SELECT * FROM tReportTransCase WHERE rDate >='".$sDate1."' AND rDate <= '".$eDate1."'";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	if ($rs->fields['rCity'] != '台灣省') {
		$zipGov[$rs->fields['rCity']][substr($rs->fields['rDate'], 4,2)][(substr($rs->fields['rDate'], 0,4)-1911)] = $rs->fields['rTransaction_buildings'];
	}
	

	$rs->MoveNext();
}


// echo "<pre>";
// print_r($zipGov);
// die;


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
$objPHPExcel->getActiveSheet()->setTitle('建經');

//寫入清單標題列資料
$col = 66;
$row =1;


$colorArray = array('FFECC9','FFC9C9','BFFFFF','CCCCCC','BFFFBF');
foreach ($title as $k => $v) {
	$no = 0;
	$color = '';
	foreach ($v as $key => $value) {
		$color = ($no%count($v));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB($colorArray[$color]);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value);

		$no++;
	}
}
unset($color);

$row++;

foreach ($zip as $k => $v) {
	$col = 65;

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k." / 移轉登記");
	foreach ($v as $k => $v) {
		$no = 0;
		$color = '';

		foreach ($v as $key => $value) {
			
			$color = ($no%count($v));
			
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB($colorArray[$color]);
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value);
			// echo $value2."_";
			$no++;
		}
		// print_r($value);
		// die;
		
	}

	$row++;
}


##政府
$row = 0;
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
$objPHPExcel->getActiveSheet()->setTitle('政府資料');

$col = 66;
$row =1;


foreach ($title as $k => $v) {
	$no = 0;
	$color = '';
	foreach ($v as $key => $value) {
		$color = ($no%count($v));
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB($colorArray[$color]);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value);

		$no++;
	}
}
$row++;

foreach ($zipGov as $k => $v) {
	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$k." / 移轉登記");
	foreach ($v as $key => $data) {
		$no = 0;
		$color = '';
		foreach ($data as $key2 => $value2) {
			$color = ($no%count($v));
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB($colorArray[$color]);
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value2);
			$no++;
		}
		// print_r($value);
		// die;
		
	}

	$row++;
}

$_file = 'caseTransExcel.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save("/var/www/html/first.twhg.com.tw/test2/log/count".date(YmdHis).".xlsx");
	
exit ;


?>
