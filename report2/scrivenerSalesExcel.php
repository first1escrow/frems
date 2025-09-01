<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
##
$sort = $_POST['sort'];
$realestate = $_POST['realestate'];

// echo $sort;

if ($_POST['city']) {
	if ($_POST['city'][0] != "0") {
		for ($i=0; $i < count($_POST['city']); $i++) { 
			$tmp[] = '"'.$_POST['city'][$i].'"';
		}
		// print_r($_POST['city']);
		$sql = "SELECT zZip FROM  tZipArea WHERE zCity IN (".@implode(',', $tmp).")";
		// echo $sql;
		// die;
		unset($tmp);
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$tmp[] = '"'.$rs->fields['zZip'].'"';

			$rs->MoveNext();
		}

		$str = ' AND sCpZip1 IN('.@implode(',', $tmp).')';
		unset($tmp);
	}
	
}

if ($_POST['StartDate']) {
	$tmp = explode('-', $_POST['StartDate']);
	$_POST['StartDate'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
	unset($tmp);
}

if ($_POST['EndDate']) {
	$tmp = explode('-', $_POST['EndDate']);
	$_POST['EndDate'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
	unset($tmp);
}

$date_start = $_POST['StartDate']." 00:00:00";
$date_end = $_POST['EndDate']." 23:59:59";

if ($_POST['cat'] == 1) { //進案日期
	$sql = " AND cApplyDate >='".$date_start."' AND cApplyDate <='".$date_end."'";
}elseif ($_POST['cat'] == 2) {//簽約日期
	$sql = " AND cSignDate >='".$date_start."' AND cSignDate <='".$date_end."'";
}elseif ($_POST['cat'] == 3) {//結案日期
	$sql = " AND cEndDate >='".$date_start."' AND cEndDate <='".$date_end."'";
}
##
##地政士
$sql_s = "SELECT CONCAT('SC',LPAD(sId,4,'0')) as sCode,sName,sOffice,sId,(SELECT pName FROM tPeopleInfo WHERE pId = sUndertaker1) as undertaker FROM tScrivener WHERE 1=1".$str ;
// echo $sql_b."<br>";
// die;
$rs = $conn->Execute($sql_s);

while (!$rs->EOF) {
	$scrivener[$rs->fields['sId']]['name'] = $rs->fields['sName'].'('.$rs->fields['sOffice'].')';
	$scrivener[$rs->fields['sId']]['code'] = $rs->fields['sCode'];
	$scrivener[$rs->fields['sId']]['undertaker'] = $rs->fields['undertaker'];
	$scrivener[$rs->fields['sId']]['count'] = 0;
	$scrivener[$rs->fields['sId']]['money'] = 0;
	$scrivener2[$rs->fields['sId']] = 0;
	$rs->MoveNext();
}

##

$sql .= ' AND cas.cCertifiedId !="005030342"' ;

$sql ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate,
	cas.cEndDate as cEndDate, 
	inc.cTotalMoney as cTotalMoney, 
	inc.cCertifiedMoney as cCertifiedMoney,
	rea.cBrand,
	rea.cBrand1,
	rea.cBrand2, 
	rea.cBranchNum,
	rea.cBranchNum1,
	rea.cBranchNum2,
	cs.cScrivener
FROM 
	tContractCase AS cas 
LEFT JOIN 
	tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN
	tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
LEFT JOIN 
	tContractScrivener AS cs ON cs.cCertifiedId=cas.cCertifiedId
WHERE
cas.cCertifiedId<>""
'.$sql.' AND cas.cCaseStatus<>"8"
GROUP BY
	cas.cCertifiedId
' ;
// echo $sql;
$rs = $conn->Execute($sql);

$i = 0;
while (!$rs->EOF) {
	// $arr[$i] = $rs->fields;

	$scrivener[$rs->fields['cScrivener']]['count']++;
	$scrivener[$rs->fields['cScrivener']]['money'] += $rs->fields['cTotalMoney'];
	$branchCount = 0;
	if ($realestate) {

		if ($rs->fields['cBrand'] > 0) {
			$branchCount++;
			$cat = checkCat($rs->fields['cBranchNum'],$rs->fields['cBrand']);
		}

		if ($rs->fields['cBrand1'] > 0) {
			$branchCount++;
			$cat1 = checkCat($rs->fields['cBranchNum1'],$rs->fields['cBrand1']);
		}

		if ($rs->fields['cBrand2'] > 0) {
			$branchCount++;
			$cat2 = checkCat($rs->fields['cBranchNum2'],$rs->fields['cBrand2']);
		}
		$part = round((1/$branchCount),1);

		//$scrivener[$rs->fields['cScrivener']][]
		if ($sort == 1) {
			if ($realestate == 5) {
				if ($cat == 12 || $cat == 2) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
				if ($cat1 == 12 || $cat1 == 2) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
				if ($cat2 == 12 || $cat2 == 2) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
			}elseif ($realestate == 6) {
				if ($cat == 3 || $cat == 11) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
				if ($cat1 == 3 || $cat1 == 11) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
				if ($cat2 == 3 || $cat2 == 11) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
			}else{
				if ($cat == $realestate) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
				if ($cat1 == $realestate) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
				if ($cat2 == $realestate) {
					$scrivener2[$rs->fields['cScrivener']] += $part;
				}
			}
		}else{
			if ($realestate == 5) {
				if ($cat == 12 || $cat == 2) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
				if ($cat1 == 12 || $cat1 == 2) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
				if ($cat2 == 12 || $cat2 == 2) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
			}elseif ($realestate == 6) {
				if ($cat == 3 || $cat == 11) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
				if ($cat1 == 3 || $cat1 == 11) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
				if ($cat2 == 3 || $cat2 == 11) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
			}else{
				if ($cat == $realestate) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
				if ($cat1 == $realestate) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
				if ($cat2 == $realestate) {
					$scrivener2[$rs->fields['cScrivener']] += ($rs->fields['cTotalMoney']*$part);
				}
			}
		}
		

		unset($cat);
		unset($cat1);
		unset($cat2);
		unset($part);
	}else{
		if ($sort == 1) {
			$scrivener2[$rs->fields['cScrivener']]++;
		}else{
			$scrivener2[$rs->fields['cScrivener']] += $rs->fields['cTotalMoney'];
		}
	}

	
	


	
	$rs->MoveNext();
}

##排序
// echo "<pre>";
// print_r($scrivener2);
// echo "</pre>";
// die;
arsort($scrivener2);
##################################################
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件報表");
$objPHPExcel->getProperties()->setDescription("案件統計報表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('案件統計報表');
//寫入表頭資料
##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('FDFF37');

##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A:E')->setWidth(20);
##

##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','排名');
$objPHPExcel->getActiveSheet()->setCellValue('B1','地政士編號');
$objPHPExcel->getActiveSheet()->setCellValue('C1','地政士');
if ($sort == 1) {
	$objPHPExcel->getActiveSheet()->setCellValue('D1','案件數量');
}else{
	$objPHPExcel->getActiveSheet()->setCellValue('D1','業績(總價金)');
}
$objPHPExcel->getActiveSheet()->setCellValue('E1','經辦');


$row = 2;
$rank = 0;
$j= 1;

// $tttt = 1;

foreach ($scrivener2 as $key => $value) {
	$col = 65;
	if ($scrivener[$key]['name'] != '') {
		$ck = true;
		if ($tmp == $value) {
			
			$ck = false;
		}

		if ($ck == true) {
			$rank+=$j;
			// $j = 1;
		}else{
			// $j++;
		}

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rank);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$scrivener[$key]['code']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$scrivener[$key]['name']);

		if ($sort == 2) {
			$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getNumberFormat()->setFormatCode('#,##0');
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value);
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$scrivener[$key]['undertaker']);

		$row++;
		$tmp = $value;
	}
	
	
}


$objPHPExcel->setActiveSheetIndex(0);

// echo "<pre>";
// print_r($list2);
// echo "</pre>";
// die;

##
$_file = 'case.xlsx' ;

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
##


?>