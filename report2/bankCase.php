<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;

$_POST = escapeStr($_POST) ;


if ($_POST['ok']) {
	$list = array();

	$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus = 2' ; //005030342 電子合約書測試用沒有刪的樣子

	if ($_POST['bank']) {
		$query .= " AND  cas.cBank = '".$_POST['bank']."'";
	}

	if ($_POST['sDate'] && $_POST['eDate']) {
		$sDate = (substr($_POST['sDate'], 0,3)+1911).substr($_POST['sDate'], 3);
		$eDate = (substr($_POST['eDate'], 0,3)+1911).substr($_POST['eDate'], 3);

		$query .= " AND  (cas.cSignDate >= '".$sDate."' AND cas.cSignDate <= '".$eDate."')";
	}

	$sql ='
		SELECT 
			cas.cCertifiedId as cCertifiedId, 
			cas.cEscrowBankAccount AS cEscrowBankAccount,
			(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener, 
			csc.cScrivener AS scrivenerID,
			cas.cSignDate as cSignDate, 
			cas.cFinishDate2 as cFinishDate2,
			buy.cName as buyer, 
			own.cName as owner, 
			inc.cTotalMoney as cTotalMoney, 
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,	
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brandname3,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand) AS brandCode,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand1) AS brandCode1,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand2) AS brandCode2,	
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand3) AS brandCode3,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			rea.cBrand2 as brand3,
			rea.cBranchNum as branch,
			rea.cBranchNum1 as branch1,
			rea.cBranchNum2 as branch2,
			rea.cBranchNum3 as branch3,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS branchName,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS branchName1,	
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS branchName2,
			(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum3) AS branchName3,
			cas.cCaseMoney
		FROM 
			tContractCase AS cas 
		LEFT JOIN 
			tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
		LEFT JOIN 
			tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tScrivener AS scr ON scr.sId = csc.cScrivener
		WHERE
		'.$query.' 
		GROUP BY
			cas.cCertifiedId
		ORDER BY 
			cas.cApplyDate,cas.cId,cas.cSignDate ASC;
		' ;
	

	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$store = array();
		$buyers = array();
		$owners = array();
		$list[$i] = $rs->fields;

		//仲介店家
		array_push($store, $rs->fields['brandname'].$rs->fields['branchName']);
		
		if ($rs->fields['branch1'] > 0) {
			array_push($store, $rs->fields['brandname1'].$rs->fields['branchName1']);
		}

		if ($rs->fields['branch2'] > 0) {
			array_push($store, $rs->fields['brandname2'].$rs->fields['branchName2']);
		}

		if ($rs->fields['branch3'] > 0) {
			array_push($store, $rs->fields['brandname3'].$rs->fields['branchName3']);
		}

		$list[$i]['store'] = @implode(';', $store);
		//

		$list[$i]['cTotalMoney'] = $list[$i]['cTotalMoney'];
		$list[$i]['cSignDate'] = substr($list[$i]['cSignDate'], 0,10);
		$list[$i]['cEndDate'] = substr($list[$i]['cEndDate'], 0,10);
		$list[$i]['cFinishDate2'] = substr($list[$i]['cFinishDate2'], 0,10);
		//其他買方
		$buyers = getOthers(1,$list[$i]['cCertifiedId']);
		
		if ($buyers) {
			$list[$i]['buyer'] .= ';'.implode(';', $buyers) ;
			
		}

		//其他賣方
		$owners = getOthers(1,$list[$i]['cCertifiedId']);
		
		if ($buyers) {
			$list[$i]['owner'] .= ';'.implode(';', $owners) ;
			
		}

		$list[$i]['undertaker'] = getUndertaker($rs->fields['scrivenerID']);

		$i++;

		$rs->MoveNext();
	}

	unset($store);
	unset($buyers);
	unset($owners);

	
	

	$objPHPExcel = new PHPExcel();
	// // 	//Set properties 設置文件屬性
	$objPHPExcel->getProperties()->setCreator("第一建經");
	$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
	$objPHPExcel->getProperties()->setTitle("第一建經");
	$objPHPExcel->getProperties()->setSubject("第一建經 銀行未結案表");
	$objPHPExcel->getProperties()->setDescription("第一建經 銀行未結案表");

	// // 	//指定目前工作頁
	$objPHPExcel->setActiveSheetIndex(0);


		//寫入表頭資料
	
	$row = 1;
	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':K'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'第一建築經理(股)公司');
	$row++;

	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':K'.$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'未結案案件');
	$row++;

	$col = 65;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':K'.$row.'');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'成交日:'.str_replace('-', '/', $sDate)."~".str_replace('-', '/', $eDate));
	$row++;

	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'合約書編號');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'代書姓名');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介單位');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'賣方姓名');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'買方姓名');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證金額(買賣價金)');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'簽約日期');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'結案日期(預計點交日)');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'專戶餘額');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'逾六個月未結案原因');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'經辦');
	$row++;

	
	foreach ($list as $k => $v) {
		$col = 65;
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($k+1));
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cEscrowBankAccount']);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $v['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 

		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['scrivener']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['store']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['owner']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['buyer']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cTotalMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cSignDate']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cFinishDate2']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['cCaseMoney']);
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'');
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$v['undertaker']);
		$row++;
	}

	unset($list);


	// die;

	$_file = 'bankCase.xlsx' ;

	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header('Content-type:application/force-download');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename='.$_file);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("php://output");
	exit;

}


	// 	// die;
	
	


$sql = "SELECT cBankCode,cBankName,cBranchName FROM tContractBank WHERE cShow = 1 AND cId = 5";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuBank[$rs->fields['cBankCode']] = $rs->fields['cBankName']."".$rs->fields['cBranchName'];

	$rs->MoveNext();
}

// echo "<pre>";
// print_r($menuBank);

// $sql = "SELECT cBankName,cBranchName,cId FROM tContractBank WHERE cShow = 1";
// $rs = $conn->Execute($sql);
// while (!$rs->EOF) {
// 	$menuBank[$rs->fields['cId']] = $rs->fields['cBankName']."_".$rs->fields['cBranchName'];


// 	$rs->MoveNext();
// }
##
function getOthers($iden,$cId){
	global $conn;

	$name = array();

	$sql = "SELECT cName FROM tContractOthers WHERE cIdentity = '".$iden."' AND cCertifiedId = '".$cId."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		array_push($name, $rs->fields['cName']);

		$rs->MoveNext();
	}

	return $name;
}

function getUndertaker($sId){
		global $conn;

		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sUndertaker1) AS undertaker FROM tScrivener WHERE sId = '".$sId."'";
		$rs = $conn->Execute($sql);

		return $rs->fields['undertaker'];
	}
##
$smarty->assign('sDate',$sDate);
$smarty->assign('eDate',$eDate);
$smarty->assign('bank',$bank);
$smarty->assign('menuBank',$menuBank);
$smarty->display('bankCase.inc.tpl', '', 'report2');
?>