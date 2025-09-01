<?php

include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../report/getBranchType.php';
$sSignDate = $_POST['date_start_y']."-".$_POST['date_start_m']."-01";
$eSignDate = $_POST['date_end_y']."-".$_POST['date_end_m']."-31";
$citys = $_POST['country'];
$realestate = $_POST['realestate'];
$citys_b = $_POST['country_b'];
$brand = $_POST['brand'];

##
//計算區間
$m1 = $_POST['date_start_m'];
$m2 = $_POST['date_end_m'];
$y1 = $_POST['date_start_y'];
$y2 = $_POST['date_end_y'];
##時間##
for ($i=$y1; $i <=$y2 ; $i++) { 

	for ($j=1; $j <= 12; $j++) { 

		if ($j<10) {
			$tmp = '0'.$j;
		}else{
			$tmp =$j;
		}

		if (($i==$y1 && $j >=$m1 ) || ($i<=$y2 && $i!=$y1 && $j<=$m2 )) {
			$col_date[$i."-".$tmp] = ($i-1911)."-".$tmp;
			
		}
			
		unset($tmp);
		if ($i==$y2 && $j ==$m2) {
			break;
		}

	}
		# code...
}

##


// ##
$query = 'cas.cCaseStatus<>"8" AND cas.cCertifiedId<>"" ';
$branchA_sql = ' bId<>"0"';

// 搜尋條件-簽約日期
if ($sSignDate) {
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
	//$query .= ' tra.tExport_time>="'.$sSignDate.' 00:00:00" ' ;
}
if ($eSignDate) {
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;

	$branchA_sql .= " AND bCreat_time <='".$eSignDate."'";
	// $time_strA =' AND bCreat_time <="'.$eSignDate.'"';
	//$query .= ' tra.tExport_time<="'.$eSignDate.' 23:59:59" ' ;
}

if ($brand > 0) {
	if ($query) { $query .= " AND " ; }
	$query .= '(rea.cBrand = "'.$brand.'" OR rea.cBrand1 = "'.$brand.'" OR rea.cBrand2 = "'.$brand.'")';
	// $query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;

	if ($branchA_sql) { $branchA_sql .= " AND "; }
		$branchA_sql .= "bBrand ='".$brand."'";
		
}

if ($realestate) {

	if ($realestate == 12) {

		if ($branchA_sql) { $branchA_sql .= " AND "; }
		$branchA_sql .= "bCategory ='1' AND bBrand = 1";

	}elseif ($realestate == 13) {
		if ($branchA_sql) { $branchA_sql .= " AND "; }
		$branchA_sql .= "bBrand = 49";
	}elseif ($realestate == 14) {
		if ($branchA_sql) { $branchA_sql .= " AND "; }
		$branchA_sql .= "bBrand = 56";
	}elseif($realestate == 1){
		if ($branchA_sql) { $branchA_sql .= " AND "; }
		$branchA_sql .= "bCategory ='1'";
	}elseif ($realestate == 2) { //直營

		if ($branchA_sql) { $branchA_sql .= " AND "; }
		$branchA_sql .= "bCategory ='2'";

	}
	
}

##
$query2 = $query;
##
//查詢條件&&縣市資料
if ($citys) {
	$zipArr = array() ;
	$zipStr = '' ;
	$sql = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="'.$citys.'" ORDER BY nid ASC;' ;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$zipArr[] = $rs->fields['zZip'] ;
		$Title[] = $rs->fields['zArea'];
		$rs->MoveNext($sql);
	}
	
	
	$zipStr = implode('","',$zipArr) ;
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip IN ("'.$zipStr.'") ' ;

	$branchA_sql .= ' AND bZip IN ("'.$zipStr.'")' ;

	unset($zipArr) ;
	unset($zipStr) ;
}else{
	$sql = 'SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC;' ;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$Title[] = $rs->fields['zCity'] ;
		$rs->MoveNext($sql);
	}
}

if ($_POST['close']) {
	$branchA_sql.=' AND bStatus = 1';
}
// if ($citys_b) {
// 	$zipArr = array() ;
// 	$zipStr = '' ;
// 	$sql = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="'.$citys_b.'" ORDER BY nid ASC;' ;
// 	$rs = $conn->Execute($sql);
// 	while (!$rs->EOF) {
// 		$zipArr[] = $rs->fields['zZip'] ;
		
// 		$rs->MoveNext($sql);
// 	}
	
	
// 	$zipStr = implode('","',$zipArr) ;
// 	$branchA_sql .= ' AND bZip IN ("'.$zipStr.'")' ;

// 	unset($zipArr) ;
// 	unset($zipStr) ;
// }
####



$sql = 'SELECT 
			bId,
			(SELECT bName FROM tBrand WHERE bId=a.bBrand) as brand,
			bStore,
			(SELECT zCity FROM tZipArea WHERE zZip = bZip) AS city,
			(SELECT zArea FROM tZipArea WHERE zZip = bZip) AS area
		FROM
			tBranch AS a WHERE '.$branchA_sql.' ORDER BY bId ASC;' ;

$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$branchA[$rs->fields['bId']] = $rs->fields;
	$branchA[$rs->fields['bId']]['caseMax'] = 0 ;
	$branchA[$rs->fields['bId']]['caseMoney'] = 0 ; 
	$branchA[$rs->fields['bId']]['HQ'] = 0 ;
	$branchA[$rs->fields['bId']]['sales'] = getSalesName('b',$rs->fields['bId']);

	$key_city = ($citys == '')? $rs->fields['city']:$rs->fields['area'];

	$branchB[$key_city][$rs->fields['bId']] = $rs->fields;

	$branch_check[] = $rs->fields['bId'];
	$rs->MoveNext();
}


#####
if ($query) { $query = ' WHERE '.$query ; }
if ($query2) { $query2 = ' WHERE '.$query2 ; }

function getCaseData($query=''){
	global $conn;
	$sql ='
		SELECT 
			cas.cCertifiedId as cCertifiedId, 
			cas.cSignDate as cSignDate, 
			inc.cTotalMoney as cTotalMoney, 
			inc.cCertifiedMoney as cCertifiedMoney, 
			pro.cAddr as cAddr, 
			pro.cZip as cZip, 
			zip.zCity as zCity, 
			zip.zArea as zArea,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			rea.cBranchNum as branch,
			rea.cBranchNum1 as branch1,
			rea.cBranchNum2 as branch2
		FROM 
			tContractCase AS cas 
		LEFT JOIN 
			tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
		LEFT JOIN
			tZipArea AS zip ON zip.zZip=pro.cZip

		'.$query.' 
		GROUP BY
			cas.cCertifiedId
		ORDER BY 
			cas.cSignDate ASC;
		' ;
		// echo $sql."<br>";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$list[] = $rs->fields;

			$rs->MoveNext();
		}

		return $list;
}


$list = getCaseData($query);
$list2 = getCaseData($query2);

if ($realestate) {	
	$tmp = array() ;
	$j = 0 ;
		for ($i = 0 ; $i < count($list) ; $i ++) {
		
		$type = branch_type($conn,$list[$i]);

		

		if ($realestate == '11' && $type == 'O') {
			//$cat = '加盟其他品牌' ;
			$tmp[$j++] = $list[$i] ;
		}
		else if ($realestate == '12' && $type == 'T') {
			//$cat = '加盟台灣房屋' ;
				$tmp[$j++] = $list[$i] ;
	
		}
		else if ($realestate == '13' && $type == 'U') {
			//$cat = '加盟優美地產' ;
			$tmp[$j++] = $list[$i] ;
		}
		else if ($realestate == '14' && $type == 'F') {
			//$cat = '加盟永春不動產' ;
			$tmp[$j++] = $list[$i] ;
		}
		else if ($realestate == '1' && ($type == 'O' || $type == 'T' || $type == 'U' || $type == 'F')) {
			//$cat = '所有加盟(其他品牌、台灣房屋、優美地產)' ;
			
			$tmp[$j++] = $list[$i] ;
		}
		else if ($realestate == '2' && $type == '2') {
			//$cat = '直營' ;
			//$tmp[$j++] = $arr[$i] ;
			$tmp[$j++] = $list[$i] ;
		}
		else if ($realestate == '3' && $type == '3') {
			//$cat = '非仲介成交' ;
			$tmp[$j++] = $list[$i] ;
		}
		else if ($realestate == '4' && $type == 'N' ) {
			$tmp[$j++] = $list[$i] ;
		}
	}
	unset($list) ;
	$list = array() ;
	
	$list = array_merge($tmp) ;

	unset($tmp);
}



# 取得所有資料

for ($i = 0 ; $i < count($list) ; $i ++) {
	// $arr[$i] = mysql_fetch_array($result) ;
	
	if (checkSales($list[$i],$_SESSION['member_id'])) {
		$month = substr($list[$i]['cSignDate'], 0,7);
		$type = branch_type2($conn,$list[$i]);
	
		##案件區域
		$key_city = ($citys == '')? $list[$i]['zCity']:$list[$i]['zArea'];

		// if ($brand > 0) {
		// 	$cc = 0;
		// 	if (in_array($list[$i]['branch'], $branch_check) && $type['bid'] == $list[$i]['branch'] && $brand == $list[$i]['brand']) {
		// 		$cc = 1;
		// 	}elseif(in_array($list[$i]['branch1'], $branch_check) && $type['bid'] == $list[$i]['branch1'] && $brand == $list[$i]['brand1']){
		// 		$cc = 1;
		// 	}elseif (in_array($list[$i]['branch'], $branch_check) && $type['bid'] == $list[$i]['branch'] && $brand == $list[$i]['brand2']) {
		// 		$cc = 1;
		// 	}

		// 	if ($cc == 1) {
		// 		$arr[$key_city]['count']++;
		// 		$arr[$key_city]['CertifiedMoney'] += $list[$i]['cCertifiedMoney'];
		// 		$arr[$key_city]['TotalMoney'] += $list[$i]['cTotalMoney'];
		// 		$arr[$key_city]['monthcount'][$month]++; 
		// 		$arr[$key_city]['monthCertifiedMoney'][$month]+=$list[$i]['cCertifiedMoney'];

		// 		$arr[$key_city]['monthTotalMoney'][$month]+=$list[$i]['cTotalMoney'];

		// 		//每月數量表
		// 		if ($key_city != '') { //可能會沒填寫地址
		// 			$arr2[$month]['count']++;
		// 			$arr2[$month]['CertifiedMoney'] +=$list[$i]['cCertifiedMoney'];
		// 			$arr2[$month]['TotalMoney'] +=$list[$i]['cTotalMoney'];
		// 		}
		// 	}
		// }else{
				$arr[$key_city]['count']++;
				$arr[$key_city]['CertifiedMoney'] += $list[$i]['cCertifiedMoney'];
				$arr[$key_city]['TotalMoney'] += $list[$i]['cTotalMoney'];
				$arr[$key_city]['monthcount'][$month]++; 
				$arr[$key_city]['monthCertifiedMoney'][$month]+=$list[$i]['cCertifiedMoney'];

				$arr[$key_city]['monthTotalMoney'][$month]+=$list[$i]['cTotalMoney'];

				//每月數量表
				if ($key_city != '') { //可能會沒填寫地址
					$arr2[$month]['count']++;
					$arr2[$month]['CertifiedMoney'] +=$list[$i]['cCertifiedMoney'];
					$arr2[$month]['TotalMoney'] +=$list[$i]['cTotalMoney'];
				}
		// }
		##

		

		##
		unset($type);
	}
	
	
}


unset($list);

for ($i=0; $i < count($list2); $i++) { 
	$month = substr($list2[$i]['cSignDate'], 0,7);
	$type = branch_type2($conn,$list2[$i]);
	##仲介比較表
		
		
		if ($brand > 0) {
				if ($brand == $list2[$i]['brand']) {
					if (in_array($list2[$i]['branch'], $branch_check) && ($type['bid'] == $list2[$i]['branch'])) {
						
							$branchA[$list2[$i]['branch']]['caseMax'] ++ ;
							$branchA[$list2[$i]['branch']]['caseMoney'] += $list2[$i]['cCertifiedMoney'];
							$branchA[$list2[$i]['branch']]['caseTotalMoney'] += $list2[$i]['cTotalMoney'];

							$branchA[$list2[$i]['branch']]['monthcount'][$month]++;
							$branchA[$list2[$i]['branch']]['monthCertifiedMoney'][$month] += $list2[$i]['cCertifiedMoney'];
							$branchA[$list2[$i]['branch']]['monthTotalMoney'][$month] += $list2[$i]['cTotalMoney'];
							
							

							$branchA2[$month]['count']++;
							$branchA2[$month]['CertifiedMoney'] +=$list2[$i]['cCertifiedMoney'];
							$branchA2[$month]['TotalMoney'] +=$list2[$i]['cTotalMoney'];

					}
				}


				if ($brand == $list2[$i]['brand1']) {
					if (in_array($list2[$i]['branch1'], $branch_check) && $type['bid'] == $list2[$i]['branch1']) {//第二家仲介
					
					
						$branchA[$list2[$i]['branch1']]['caseMax'] ++ ;
						$branchA[$list2[$i]['branch1']]['caseMoney'] += $list2[$i]['cCertifiedMoney'] ;
						$branchA[$list2[$i]['branch1']]['caseTotalMoney'] += $list2[$i]['cTotalMoney'];

						$branchA[$list2[$i]['branch1']]['monthcount'][$month]++;
						$branchA[$list2[$i]['branch1']]['monthCertifiedMoney'][$month] += $list2[$i]['cCertifiedMoney'];
						$branchA[$list2[$i]['branch1']]['monthTotalMoney'][$month] += $list2[$i]['cTotalMoney'];
							
						$branchA2[$month]['count']++;
						$branchA2[$month]['CertifiedMoney'] +=$list2[$i]['cCertifiedMoney'];
						$branchA2[$month]['TotalMoney'] +=$list2[$i]['cTotalMoney'];
					}
				}

				if ($brand == $list2[$i]['brand2']) {
					if (in_array($list2[$i]['branch2'], $branch_check) && $type['bid'] == $list2[$i]['branch2']) {//第三家仲介
					
							$branchA[$list2[$i]['branch2']]['caseMax'] ++ ;
							$branchA[$list2[$i]['branch2']]['caseMoney'] += $list2[$i]['cCertifiedMoney'] ;
							$branchA[$list2[$i]['branch2']]['caseTotalMoney'] += $list2[$i]['cTotalMoney'];

							$branchA[$list2[$i]['branch2']]['monthcount'][$month]++;
							$branchA[$list2[$i]['branch2']]['monthCertifiedMoney'][$month] += $list2[$i]['cCertifiedMoney'];
							$branchA[$list2[$i]['branch2']]['monthTotalMoney'][$month] += $list2[$i]['cTotalMoney'];
							
							$branchA2[$month]['count']++;
							$branchA2[$month]['CertifiedMoney'] +=$list2[$i]['cCertifiedMoney'];
							$branchA2[$month]['TotalMoney'] +=$list2[$i]['cTotalMoney'];
					}	
				}
		}else{
			if (in_array($list2[$i]['branch'], $branch_check) && ($type['bid'] == $list2[$i]['branch'])) {
					
						$branchA[$list2[$i]['branch']]['caseMax'] ++ ;
						$branchA[$list2[$i]['branch']]['caseMoney'] += $list2[$i]['cCertifiedMoney'];
						$branchA[$list2[$i]['branch']]['caseTotalMoney'] += $list2[$i]['cTotalMoney'];

						$branchA[$list2[$i]['branch']]['monthcount'][$month]++;
						$branchA[$list2[$i]['branch']]['monthCertifiedMoney'][$month] += $list2[$i]['cCertifiedMoney'];
						$branchA[$list2[$i]['branch']]['monthTotalMoney'][$month] += $list2[$i]['cTotalMoney'];
						
						$branchA2[$month]['count']++;
						$branchA2[$month]['CertifiedMoney'] +=$list2[$i]['cCertifiedMoney'];
						$branchA2[$month]['TotalMoney'] +=$list2[$i]['cTotalMoney'];

			}

			if (in_array($list2[$i]['branch1'], $branch_check) && $type['bid'] == $list2[$i]['branch1']) {//第二家仲介
				
				
					$branchA[$list2[$i]['branch1']]['caseMax'] ++ ;
					$branchA[$list2[$i]['branch1']]['caseMoney'] += $list2[$i]['cCertifiedMoney'] ;
					$branchA[$list2[$i]['branch1']]['caseTotalMoney'] += $list2[$i]['cTotalMoney'];

					$branchA[$list2[$i]['branch1']]['monthcount'][$month]++;
					$branchA[$list2[$i]['branch1']]['monthCertifiedMoney'][$month] += $list2[$i]['cCertifiedMoney'];
					$branchA[$list2[$i]['branch1']]['monthTotalMoney'][$month] += $list2[$i]['cTotalMoney'];
						
					$branchA2[$month]['count']++;
					$branchA2[$month]['CertifiedMoney'] +=$list2[$i]['cCertifiedMoney'];
					$branchA2[$month]['TotalMoney'] +=$list2[$i]['cTotalMoney'];
			}

			if (in_array($list2[$i]['branch2'], $branch_check) && $type['bid'] == $list2[$i]['branch2']) {//第三家仲介
				
					$branchA[$list2[$i]['branch2']]['caseMax'] ++ ;
					$branchA[$list2[$i]['branch2']]['caseMoney'] += $list2[$i]['cCertifiedMoney'] ;
					$branchA[$list2[$i]['branch2']]['caseTotalMoney'] += $list2[$i]['cTotalMoney'];

					$branchA[$list2[$i]['branch2']]['monthcount'][$month]++;
					$branchA[$list2[$i]['branch2']]['monthCertifiedMoney'][$month] += $list2[$i]['cCertifiedMoney'];
					$branchA[$list2[$i]['branch2']]['monthTotalMoney'][$month] += $list2[$i]['cTotalMoney'];

					$branchA2[$month]['count']++;
					$branchA2[$month]['CertifiedMoney'] +=$list2[$i]['cCertifiedMoney'];
					$branchA2[$month]['TotalMoney'] +=$list2[$i]['cTotalMoney'];
				
			}
		}
		
		
		unset($type);
			
			
			
		
		##
}



unset($list2);
##

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("區域案件統計表");
$objPHPExcel->getProperties()->setDescription("第一建經案件統計表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('區域案件統計報表');

//標題列資料
$col = 65;
$row = 2;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'縣市區域');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件件數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'履保金額');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
$row++;

if (is_array($Title)) {
	foreach ($Title as $key => $value) {
		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value);
		if ($arr[$value]['count'] == '') {$arr[$value]['count'] = 0;}
		if ($arr[$value]['CertifiedMoney'] == '') {$arr[$value]['CertifiedMoney'] = 0;}	
		if ($arr[$value]['TotalMoney'] == '') {$arr[$value]['TotalMoney'] = 0;}	
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$value]['count']);
		$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);  
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$value]['CertifiedMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$arr[$value]['TotalMoney']);
		$row++;
	}


}

$col++;

$row = 2;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'案件件數');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'履保金額');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'總價金');
$row++;

$col++;

if (is_array($col_date)) {
	foreach ($col_date as $k => $v) {
		$row = 2;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
		
		$row++;

		if ($arr2[$k]['count'] == '') {$arr2[$k]['count'] = 0;}
		if ($arr2[$k]['CertifiedMoney'] == '') {$arr2[$k]['CertifiedMoney'] = 0;}	
		if ($arr2[$k]['TotalMoney'] == '') {$arr2[$k]['TotalMoney'] = 0;}	

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$arr2[$k]['count']);
		$row++;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$arr2[$k]['CertifiedMoney']);
		$row++;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$arr2[$k]['TotalMoney']);
		$row++;

		$col++;
	}
}
$objPHPExcel->getActiveSheet()->mergeCells('A1:'.chr($col-1).'1') ;	
$objPHPExcel->getActiveSheet()->setCellValue('A1','區域案件統計');
$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr($col-1).'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr($col-1).'1')->getFill()->getStartColor()->setARGB('CCCCCC');

#########仲介比較表
//店名稱	履保收入	進案件數
$col++;


$row = 2;
$col2 = $col;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店名稱');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'履保收入');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案件數');


$row++;

$col++;


if (is_array($branchA)) {
	foreach ($branchA as $k => $v) {
		
			if ($citys != '') {
				
						$col = $col2;
						$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['brand'].$v['bStore']);
						$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['caseMoney']);
						$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['caseMax']);

						$row++;
				
			}else{
				$col = $col2;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['brand'].$v['bStore']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['caseMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['caseMax']);

				$row++;
			}
		
	
	}
}

$col = $col+2;

$row = 2;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'案件件數');
$row++;

$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'履保金額');
$row++;



$col++;

if (is_array($col_date)) {
	foreach ($col_date as $k => $v) {
		$row = 2;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
		
		$row++;

		if ($branchA2[$k]['count'] == '') {$branchA2[$k]['count'] = 0;}
		if ($branchA2[$k]['CertifiedMoney'] == '') {$branchA2[$k]['CertifiedMoney'] = 0;}	
		if ($branchA2[$k]['TotalMoney'] == '') {$branchA2[$k]['TotalMoney'] = 0;}	

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$branchA2[$k]['count']);
		$row++;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$branchA2[$k]['CertifiedMoney']);
		$row++;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$branchA2[$k]['TotalMoney']);
		$row++;

		$col++;
	}
}



// echo chr($col2)."_".chr($col);
// die;
// $objPHPExcel->setActiveSheetIndex()->mergeCells(chr($col2).'1:'.chr($col-1).'1') ;	
$objPHPExcel->getActiveSheet()->mergeCells(chr($col2).'1:'.chr($col-1).'1');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col2).'1','仲介比較表');
$objPHPExcel->getActiveSheet()->getStyle(chr($col2).'1:'.chr($col-1).'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle(chr($col2).'1:'.chr($col-1).'1')->getFill()->getStartColor()->setARGB('CCCCCC');
###########分頁###########
$i = 1;
if (is_array($Title)) {
	foreach ($Title as $key => $value) {
		$objPHPExcel->createSheet($i) ;
		$objPHPExcel->setActiveSheetIndex($i);
		$objPHPExcel->getActiveSheet()->setTitle($value);


		$col = 65;
		$row = 2;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'');
		$row++;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'案件件數');
		$row++;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'履保金額');
		$row++;

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'總價金');
		$row++;

		$col++;
		if (is_array($col_date)) {
			foreach ($col_date as $k => $v) {
				$row = 2;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
				
				$row++;

				if ($arr[$value]['monthcount'][$k] == '') {$arr[$value]['monthcount'][$k] = 0;}
				if ($arr[$value]['monthCertifiedMoney'][$k] == '') {$arr[$value]['monthCertifiedMoney'][$k] = 0;}	
				if ($arr[$value]['monthTotalMoney'][$k] == '') {$arr[$value]['monthTotalMoney'][$k] = 0;}	

				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$arr[$value]['monthcount'][$k]);
				$row++;

				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$arr[$value]['monthCertifiedMoney'][$k]);
				$row++;

				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$arr[$value]['monthTotalMoney'][$k]);
				$row++;

				$col++;
			}
		}
		

		$i++;

		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.chr($col-1).'1') ;	
		$objPHPExcel->getActiveSheet()->setCellValue('A1','區域案件統計');
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr($col-1).'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr($col-1).'1')->getFill()->getStartColor()->setARGB('CCCCCC');

		####
		$col++;
		$col2 = $col;
		$row = 2;

		// echo "<pre>";
		// print_r($branchB[$value]);
		// echo "</pre>";

		if (is_array($branchB[$value])) {
			foreach ($branchB[$value] as $k => $v) {
				
				
				$col = $col2;
				$row3 = $row;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v['brand'].$v['bStore']);
				$row++;

				$row2 = $row;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'');
				$row++;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'履保收入');
				$row++;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'進案件數');
				$row++;

				$col++;

				
				foreach ($col_date as $k2 => $v2) {
					$row = $row2;
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v2);
					$row++;

					if ($branchA[$v['bId']]['monthcount'][$k2] == '') { $branchA[$v['bId']]['monthcount'][$k2] = 0;}
					if ($branchA[$v['bId']]['monthCertifiedMoney'][$k2] == '') { $branchA[$v['bId']]['monthCertifiedMoney'][$k2] = 0;}
						
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$branchA[$v['bId']]['monthCertifiedMoney'][$k2]);
					$row++;

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$branchA[$v['bId']]['monthcount'][$k2]);
					$row++;

					

					$col++;
				}

				
				
				$row = $row+2;
				
				$objPHPExcel->getActiveSheet()->mergeCells(chr($col2).$row3.':'.chr($col-1).$row3) ;	
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col2).$row3,$v['brand'].$v['bStore']);
				// $objPHPExcel->getActiveSheet()->getStyle(chr($col2).'1:'.chr($col-1).'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				// $objPHPExcel->getActiveSheet()->getStyle(chr($col2).'1:'.chr($col-1).'1')->getFill()->getStartColor()->setARGB('CCCCCC');

			}

			$objPHPExcel->getActiveSheet()->mergeCells(chr($col2).'1:'.chr($col).'1') ;	
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col2).'1','仲介比較表');
			$objPHPExcel->getActiveSheet()->getStyle(chr($col2).'1:'.chr($col).'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle(chr($col2).'1:'.chr($col).'1')->getFill()->getStartColor()->setARGB('CCCCCC');

		}

		


	}



}


$objPHPExcel->setActiveSheetIndex(0);


$_file = iconv('UTF-8', 'BIG5', '區域案件統計表') ;
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

function checkSales($arr,$pId)
{
	global $conn;
	$sql = "SELECT * FROM tPeopleInfo WHERE PDep !=7 AND pId ='".$pId."'";

	$rs = $conn->Execute($sql);
	$max=$rs->RecordCount();

	if ($max > 0) {return true;}
	
	$branch[] = '"'.$arr['branch'].'"';
	if ($arr['branch1'] > 0) {$branch[] = $arr['branch1'];}
	if ($arr['branch2'] > 0) {$branch[] = $arr['branch2'];}	

	$sql = "SELECT bSales FROM tBranchSales WHERE bBranch IN(".@implode(',', $branch).")";
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		$sales[] = $rs->fields['bSales'];
		unset($tmp) ;
	}
	
	$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener =".$arr['cScrivener']." AND sSales='".$pId."'";
	
	$rs = $conn->Execute($sql);
	$max=$rs->RecordCount();
	

	if (in_array($pId, $sales) || $max > 0) {
		return true;
	}
	else{
		return false;
	}

	
}

function DateChange($val){
	
	$val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
		
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'/'.$tmp[1].'/'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}

//取得仲介店名
function getRealtyName($no=0) {
	global $conn;
	unset($tmp) ;
	if ($no > 0) {
		$sql = 'SELECT bStore FROM tBranch WHERE bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);
		return $rs->fields['bStore'] ;
	}
	else {
		return false ;
	}
}


function getSalesName($type,$id){
	global $conn;

	if ($type =='s') {
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS sales FROM tScrivenerSales WHERE sScrivener ='".$id."'";
	}else{
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS sales FROM tBranchSales WHERE bBranch ='".$id."'";
	}
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['sales'];

		$rs->MoveNext();
	}
	
	return @implode(',', $tmp);
}
?>
