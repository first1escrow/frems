<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;

header("Content-Type:text/html; charset=utf-8"); 
##
$data = array();

foreach ($list as $val) {
	$sql = "SELECT
				cr.cCertifyId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cr.cBranchNum3,
				(SELECT bCode FROM tBrand WHERE bId = cr.cBrand) AS brandCode,
				(SELECT bCode FROM tBrand WHERE bId = cr.cBrand1) AS brandCode1,
				(SELECT bCode FROM tBrand WHERE bId = cr.cBrand2) AS brandCode2,
				(SELECT bCode FROM tBrand WHERE bId = cr.cBrand3) AS brandCode3,
				(SELECT bName FROM tBrand WHERE bId =cr.cBrand) AS brandName,
				(SELECT bName FROM tBrand WHERE bId =cr.cBrand1) AS brandName1,
				(SELECT bName FROM tBrand WHERE bId =cr.cBrand2) AS brandName2,
				(SELECT bName FROM tBrand WHERE bId =cr.cBrand3) AS brandName3,
				(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS branchName,
				(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS branchName1,
				(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS branchName2,
				(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum3) AS branchName3,
				s.sName,
				cs.cScrivener
			FROM
				tContractRealestate AS cr 
			LEFT JOIN
				tContractScrivener AS cs ON cs.cCertifiedId=cr.cCertifyId
			LEFT JOIN
				tScrivener AS s ON s.sId = cs.cScrivener
			WHERE
				cr.cCertifyId = '".$val['lCertifiedId']."'";

	$rs = $conn->Execute($sql);


	while (!$rs->EOF) {
		$temp = array();

		$temp['certifiedId'] = $rs->fields['cCertifyId'];
		$temp['scrivener'] = 'SC'.str_pad($rs->fields['cScrivener'], 4,0,STR_PAD_LEFT).$rs->fields['sName'];
		
		$temp['store'] = array();
		$temp['sales'] = getSales($rs->fields['cScrivener'],1);
		

		

		if ($rs->fields['cBranchNum'] > 0) {
			array_push($temp['store'], $rs->fields['brandCode'].str_pad($rs->fields['cBranchNum'], 5,0,STR_PAD_LEFT).$rs->fields['brandName'].$rs->fields['branchName']);
			$temp_sales = getSales($rs->fields['cBranchNum'],2);
			
			foreach ($temp_sales as $sales) {
				if (!in_array($sales, $temp['sales'])) {
					if (!empty($sales)) {
						array_push($temp['sales'], $sales);
					}
					
				}	
			}
			unset($temp_sales);
			
		}

		
		if ($rs->fields['cBranchNum1'] > 0) {
			array_push($temp['store'], $rs->fields['brandCode1'].str_pad($rs->fields['cBranchNum1'], 5,0,STR_PAD_LEFT).$rs->fields['brandName1'].$rs->fields['branchName1']);
			$temp_sales = getSales($rs->fields['cBranchNum1'],2);
			foreach ($temp_sales as $sales) {
				if (!empty($sales)) {
						array_push($temp['sales'], $sales);
				}	
			}
			
			unset($temp_sales);
			// print_r($temp['sales']);
		}

		if ($rs->fields['cBranchNum2'] > 0) {
			array_push($temp['store'], $rs->fields['brandCode2'].str_pad($rs->fields['cBranchNum2'], 5,0,STR_PAD_LEFT).$rs->fields['brandName2'].$rs->fields['branchName2']);
			$temp_sales = getSales($rs->fields['cBranchNum2'],2);
			foreach ($temp_sales as $sales) {
				if (!empty($sales)) {
					array_push($temp['sales'], $sales);
				}	
			}

			unset($temp_sales);
			// print_r($temp['sales']);
		}

		if ($rs->fields['cBranchNum3'] > 0) {
			array_push($temp['store'], $rs->fields['brandCode3'].str_pad($rs->fields['cBranchNum3'], 5,0,STR_PAD_LEFT).$rs->fields['brandName3'].$rs->fields['branchName3']);
			$temp_sales = getSales($rs->fields['cBranchNum3'],2);
			foreach ($temp_sales as $sales) {
				if (!empty($sales)) {
					array_push($temp['sales'], $sales);
				}	
			}

			unset($temp_sales);
			// 
		}


		array_push($data, $temp);

		$rs->MoveNext();
	}
}

##
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("法務列管案件");
$objPHPExcel->getProperties()->setDescription("法務列管案件");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('仲介');

//寫入表頭資料
// $objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
// //仲介、地政士、業務、保證號碼
$objPHPExcel->getActiveSheet()->setCellValue('A1','仲介');
$objPHPExcel->getActiveSheet()->setCellValue('B1','地政士');
$objPHPExcel->getActiveSheet()->setCellValue('C1','業務');
$objPHPExcel->getActiveSheet()->setCellValue('D1','保證號碼');

##
$row = 2;


foreach ($data as $value) {
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,@implode(',', $value['store']));
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$value['scrivener']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,@implode(',', $value['sales']));
	// $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$value['certifiedId']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$row, $value['certifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 

	$row++;
}


$objPHPExcel->setActiveSheetIndex(0);
$_file = 'legalCaseList.xlsx' ;

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

function getSales($id,$type=1){
	global $conn;

	$sales = array();

	if ($id == 505 && $type == 2) {
		return $sales;
	}
	
	if ($type == 1) { // 1:地政士 2:仲介
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS name FROM tScrivenerSales WHERE sScrivener = '".$id."'";
	}else{
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS name FROM tBranch WHERE bId = '".$id."'";
	}

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($sales, $rs->fields['name']);
		$rs->MoveNext();
	}


	return $sales;
	
}
?>