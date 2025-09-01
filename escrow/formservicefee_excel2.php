<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$cid = $_POST['cid'];
$type = $_POST['type']; //tBankCode.bApplication

// echo $type;
// die;
// $cid = '011020348';//build
// // $cid = '010939678';//land
// $type = 2;
$sql = "
		SELECT
			cc.cSignDate AS cSignDate,
			cb.cName AS buyer,
			cb.cIdentifyId AS buyerId,
			cb.cMobileNum AS buyerphone,
			cb.sAgentName1 AS buyersale,
			cb.sAgentMobile1 AS buyersalephone,
			co.cName AS owner,
			co.cIdentifyId AS ownerId,
			co.cMobileNum AS ownerphone,
			co.sAgentName1 AS ownersale,
			co.sAgentMobile1 AS ownersalephone,
			(SELECT sName FROM tScrivener AS s WHERE s.sId=cs.cScrivener) AS Scrivener,
			(SELECT sName FROM tScrivenerSms AS s WHERE s.sId=cs.cManage2) AS Scrivener2,
			ci.cTotalMoney AS cTotalMoney,
			ci.cSignMoney AS cSignMoney,
			ci.cAffixMoney AS cAffixMoney,
			ci.cDutyMoney AS cDutyMoney,
			ci.cEstimatedMoney AS cEstimatedMoney,
			cr.cServiceTarget,
			cr.cServiceTarget1,
			cr.cServiceTarget2,
			cr.cServiceTarget3,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cr.cBranchNum3,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS Brand,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS Brand1,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS Brand2,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand3) AS Brand3,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS Store,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS Store1,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS Store2,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum3) AS Store3,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum) AS StoreName,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum1) AS StoreName1,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum2) AS StoreName2,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum3) AS StoreName3
		FROM 
			tContractCase AS cc
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractBuyer AS cb ON cb.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractOwner AS co ON co.cCertifiedId=cc.cCertifiedId
		LEFT JOIN 
			tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
		LEFT JOIN 
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId		
		WHERE
			cc.cCertifiedId = '".$cid."'
	   ";

$rs = $conn->Execute($sql);



$list[] = $rs->fields;

if ($list[0]["cSignDate"] !='') {
	$tmp = explode('-', substr($list[0]["cSignDate"],0,10));
	$tmp[0] = $tmp[0]-1911;
	$list[0]['cSignDate']=$tmp[0]."年".$tmp[1]."月".$tmp[2]."日";
	unset($tmp);
}

//仲介(一)服務對象：1.買賣方、2.賣方、3.買方
if ($list[0]['cBranchNum'] > 0) {
    $buyerBrand[] = $list[0]["Brand"];
    $buyerBranch[] = $list[0]["Store"];
    $buyerBranchName[] = $list[0]["StoreName"];
    $ownerBrand[] = $list[0]["Brand"];
    $ownerBranch[] = $list[0]["Store"];
    $ownerBranchName[] = $list[0]["StoreName"];
}
//仲介(二)
if ($list[0]['cBranchNum1'] > 0) {
    $buyerBrand[] = $list[0]["Brand1"];
    $buyerBranch[] = $list[0]["Store1"];
    $buyerBranchName[] = $list[0]["StoreName1"];
    $ownerBrand[] = $list[0]["Brand1"];
    $ownerBranch[] = $list[0]["Store1"];
    $ownerBranchName[] = $list[0]["StoreName1"];
}
//仲介(三)
if ($list[0]['cBranchNum2'] > 0) {
    $buyerBrand[] = $list[0]["Brand2"];
    $buyerBranch[] = $list[0]["Store2"];
    $buyerBranchName[] = $list[0]["StoreName2"];
    $ownerBrand[] = $list[0]["Brand2"];
    $ownerBranch[] = $list[0]["Store2"];
    $ownerBranchName[] = $list[0]["StoreName2"];
}

//仲介(四)
if ($list[0]['cBranchNum3'] > 0) {
    $buyerBrand[] = $list[0]["Brand3"];
    $buyerBranch[] = $list[0]["Store3"];
    $buyerBranchName[] = $list[0]["StoreName3"];
    $ownerBrand[] = $list[0]["Brand3"];
    $ownerBranch[] = $list[0]["Store3"];
    $ownerBranchName[] = $list[0]["StoreName3"];
}


##買賣標的地址	(根據tBankCode.bApplication來判別[用途(1土地2建物)])

// if ($type==1) {
// 	$sql = "
// 			SELECT
// 				cLand1,
// 				cLand2,
// 				cLand3,
// 				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=cZip) AS city,
// 				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=cZip) AS area
// 			FROM 
// 				tContractLand 
// 			WHERE 
// 				cCertifiedId = '".$cid."'
// 				 ORDER BY cItem ASC";
// 	// echo $sql;
// 	// die;
// 	$rs = $conn->Execute($sql);

// 	while (!$rs->EOF) {

// 		$tmp[$rs->fields['city'].$rs->fields['area']][$rs->fields['cLand1'].$rs->fields['cLand2']][]= $rs->fields['cLand3']; //根據地段分類(怕會有其他段)
// 		// $land[] = $rs->fields;
// 		$rs->MoveNext();
// 		# code...
// 	}

// 	foreach ($tmp as $k => $v) {

// 		foreach ($v as $a => $b) {
			
// 			$tmp2[] = $k.$a.'段'.implode(',', $b)."地號"; //EX:台南市新市區大營段2846,2847,2848,2852,2853,2854,2855,2856

// 		}

// 	}
// unset($tmp);
// }else{
// 	$sql = "
// 			SELECT
// 				cAddr,
// 				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=cZip) AS city,
// 				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=cZip) AS area
// 			FROM 
// 				 tContractProperty 
// 			WHERE 
// 				cCertifiedId = '".$cid."'
// 				 ORDER BY cItem ASC";
// 	$rs = $conn->Execute($sql);

// 	while (!$rs->EOF) {

// 		$tmp2[] = $rs->fields['city'].$rs->fields['area'].$rs->fields['cAddr'];

// 		$rs->MoveNext();
// 	}

	
// }
$sql = "
			SELECT
				cAddr,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=cZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=cZip) AS area
			FROM 
				 tContractProperty 
			WHERE 
				cCertifiedId = '".$cid."'
				 ORDER BY cItem ASC";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {

		$tmp2[] = $rs->fields['city'].$rs->fields['area'].$rs->fields['cAddr'];

		$rs->MoveNext();
	}
$list[0]['addr'] = implode(';', $tmp2);

unset($tmp2);
##
$sql = "SELECT cName,cMobileNum FROM  tContractPhone WHERE  cIdentity = 3 AND cCertifiedId = '".$cid."' ORDER BY cId ASC LIMIT 1";
$rs = $conn->Execute($sql);
$list[0]['buyersale'] = $rs->fields['cName'];
$list[0]['buyersalephone'] = $rs->fields['cMobileNum'];

$sql = "SELECT cName,cMobileNum FROM  tContractPhone WHERE  cIdentity = 4 AND cCertifiedId = '".$cid."' ORDER BY cId ASC LIMIT 1";
$rs = $conn->Execute($sql);
$list[0]['ownersale'] = $rs->fields['cName'];
$list[0]['ownersalephone'] = $rs->fields['cMobileNum'];

#################################

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("成交資料暨仲介服務費出款申請單");
$objPHPExcel->getProperties()->setDescription("成交資料暨仲介服務費出款申請單");





//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//設定邊界
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0);

##字體大小
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A3:F45')->getFont()->setSize(12);
##對齊
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("A2:E2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
##文字樣式
$objPHPExcel->getActiveSheet()->getStyle('A1:F100')->getFont()->setName('新細明體');
$objPHPExcel->getActiveSheet()->getStyle('A1:A38')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D4:D38')->getFont()->setBold(true);
##
##框
//全部
$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000'),),),);
$objPHPExcel->getActiveSheet()->getStyle('A3:F10')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('A15:F15')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('A26:F38')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('A41:F44')->applyFromArray($styleArray);
unset($styleArray);

//表頭
$styleArray = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),),);
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);				
unset($styleArray);

$styleArray = array('borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),),);
$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleArray);		
unset($styleArray);


//中間左BORDER_DOUBLE
$styleArray = array('borders' => array(
										'left' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),
										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'right' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'top' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),
										),
					);
$objPHPExcel->getActiveSheet()->getStyle('A11')->applyFromArray($styleArray);	
unset($styleArray);

$styleArray = array('borders' => array(
										'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'right' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'left' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),
										),
					);
$objPHPExcel->getActiveSheet()->getStyle('A12')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A13')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A14')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getStyle('A15')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A16')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A17')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A18')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A20')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A21')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A22')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A23')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A24')->applyFromArray($styleArray);

//中間直線BORDER_DOUBLE
$objPHPExcel->getActiveSheet()->getStyle('D11')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D12')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D13')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D14')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('D16')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D17')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D18')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D20')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D21')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D22')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D23')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D24')->applyFromArray($styleArray);


unset($styleArray);
//右邊BORDER_DOUBLE
$styleArray = array('borders' => array(
										'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'left' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'right' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),
										),
					);
$objPHPExcel->getActiveSheet()->getStyle('F11')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F12')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F13')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F14')->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('F16')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F17')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F18')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F19')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F20')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F21')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F22')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F23')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('F24')->applyFromArray($styleArray);

unset($styleArray);

//底部35~39BORDER_NONE
//$styleArray = array('borders' => array(
//										'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
//										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
//										'left' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
//										),
//					);
//// $objPHPExcel->getActiveSheet()->getStyle('F35')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('F39')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('F40')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('F41')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('F42')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('F43')->applyFromArray($styleArray);
//unset($styleArray);
//$styleArray = array('borders' => array(
//										'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
//										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
//										),
//					);
//// $objPHPExcel->getActiveSheet()->getStyle('A35')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('A39')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('A40')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('A41')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('A42')->applyFromArray($styleArray);
//$objPHPExcel->getActiveSheet()->getStyle('A43')->applyFromArray($styleArray);
//unset($styleArray);
//畫雙線
$styleArray = array('borders' => array('left' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),),);
$objPHPExcel->getActiveSheet()->getStyle('A15')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D15')->applyFromArray($styleArray);
unset($styleArray);

$styleArray = array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),),);
$objPHPExcel->getActiveSheet()->getStyle('B10:F10')->applyFromArray($styleArray);


$styleArray = array('borders' => array(
										'right' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),
										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
									),
				);
$objPHPExcel->getActiveSheet()->getStyle('F15')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C15')->applyFromArray($styleArray);

unset($styleArray);
//$styleArray = array('borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000'),),),);
//$objPHPExcel->getActiveSheet()->getStyle('A24:F24')->applyFromArray($styleArray);
//unset($styleArray);

#############################
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17); //14
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17); //16
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17.5); //16
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17); //14
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17); //15
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18); //15
##

##
$objPHPExcel->getActiveSheet()->setCellValue('A1','成交資料暨仲介服務費出款申請單');
$objPHPExcel->getActiveSheet()->setCellValue('A2','簽約日期'.$list[0]['cSignDate']);
##合併
$objPHPExcel->getActiveSheet()->mergeCells("A1:F1");
$objPHPExcel->getActiveSheet()->mergeCells("A2:F2");

$row = 3;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'保證編號');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$row, $cid,PHPExcel_Cell_DataType::TYPE_STRING); 

$objPHPExcel->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'地政士');
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$list[0]['Scrivener']);

$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'案件連絡人');
$objPHPExcel->getActiveSheet()->setCellValue('F'.$row,'');
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;
##
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"買方姓名");
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$list[0]['buyer']);
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"賣方姓名");
$objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$list[0]['owner']);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;
##

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"身分證/統一編號");
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row, substr_replace($list[0]['buyerId'], '****', 5,4));     //身分證or統編
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);


$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"身分證/統一編號");
$objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
$objPHPExcel->getActiveSheet()->setCellValue('E'.$row, substr_replace($list[0]['ownerId'], '****', 5, 4));    //身分證or統編
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".($row+1));
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$row, "手機號碼：".substr_replace($list[0]['buyerphone'], '****',5, 4 )."\n(若不願收受簡訊則請勿填寫)",PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".($row+1));
$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$row, "手機號碼：".substr_replace($list[0]['ownerphone'], '****', 5, 4)."\n(若不願收受簡訊則請勿填寫)",PHPExcel_Cell_DataType::TYPE_STRING);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row = $row+2;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"買方經紀人員");
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$list[0]['buyersale']);

$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"賣方經紀人員");
$objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$list[0]['ownersale']);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$row, $list[0]['buyersalephone'],PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"手機號碼");
$objPHPExcel->getActiveSheet()->mergeCells("E".$row.":F".$row);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$row, $list[0]['ownersalephone'],PHPExcel_Cell_DataType::TYPE_STRING);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(11);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"地址");
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":F".$row);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$list[0]['addr']);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"總價：");//$list[0]['cTotalMoney']
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"簽約：".number_format($list[0]['cSignMoney']));//
$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);

$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"□無□有設定扺押權_________________萬元");
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$row2 = $row;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,number_format($list[0]['cTotalMoney']));
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);


$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"用印：".number_format($list[0]['cAffixMoney']));
$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);

$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"□無□有私人設定__________________萬元");
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

//

$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"完稅：".number_format($list[0]['cDutyMoney']));
$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("B".$row.":C".$row);

$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"□無□有解約條款");
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$objPHPExcel->getActiveSheet()->mergeCells("A".$row2.":A".$row);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"尾款：".number_format($list[0]['cEstimatedMoney']));
$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"□無□有限制登記");
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$styleArray = array('borders' => array(
										'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')),
										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'left' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000')),
										'right' => array('style' => PHPExcel_Style_Border::BORDER_NONE,),
										),
					);
$styleArray2 = array('borders' => array(
										'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')),
										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'left' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'right' => array('style' => PHPExcel_Style_Border::BORDER_NONE,),
										),
					);

$styleArray3 = array('borders' => array(
										'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')),
										'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
										'left' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE),
										'right' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,),
										),
					);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':C'.$row)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
//$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"【買方】服務費總額：____________________");//
$objPHPExcel->getActiveSheet()->getStyle('B'.$row)->applyFromArray($styleArray2);
//$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,"仲介：(如配件拆帳 請詳細填寫！)");//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);
//
$objPHPExcel->getActiveSheet()->getStyle('D'.$row.':F'.$row)->applyFromArray($styleArray3);
$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
//$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"【賣方】服務費總額：____________________");
$objPHPExcel->getActiveSheet()->getStyle('E'.$row)->applyFromArray($styleArray2);
//$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,"仲介：(如配件拆帳 請詳細填寫！)");
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;
//仲介(一)品牌+店名
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'(1)'.$buyerBrand[0].$buyerBranch[0]);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'(1)'.$ownerBrand[0].$ownerBranch[0]);
$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(22);

$row++;
//經紀業名稱
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $buyerBranchName[0]);
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $ownerBranchName[0]);
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

$row++;
//分配金額
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, '分配金額：__________________________________');
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, '分配金額：__________________________________');
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

$row++;
$branchCount = 1;

//仲介(二)
if ($buyerBranch[1] != '') {
    //品牌+店名
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'(2)'.$buyerBrand[1].$buyerBranch[1]);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'(2)'.$ownerBrand[1].$ownerBranch[1]);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(22);

    $row++;
    //經紀業名稱
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $buyerBranchName[1]);
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $ownerBranchName[1]);
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

    $row++;
    //分配金額
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

    $row++;
    $branchCount++;
}

//仲介(三)
if ($buyerBranch[2] != '') {
    //品牌+店名
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'(3)'.$buyerBrand[2].$buyerBranch[2]);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'(3)'.$ownerBrand[2].$ownerBranch[2]);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(22);

    $row++;
    //經紀業名稱
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $buyerBranchName[2]);
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $ownerBranchName[2]);
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

    $row++;
    //分配金額
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

    $row++;
    $branchCount++;
}

//仲介(四)
if ($buyerBranch[3] != '') {
    //品牌+店名
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'(4)'.$buyerBrand[3].$buyerBranch[3]);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'(4)'.$ownerBrand[3].$ownerBranch[3]);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(22);

    $row++;
    //經紀業名稱
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $buyerBranchName[3]);
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $ownerBranchName[3]);
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

    $row++;
    //分配金額
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

    $row++;
    $branchCount++;
}

//自己填寫
if ($branchCount < 4) {
    $branchCount++;
    //品牌+店名
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'('.$branchCount.')'.'__________房屋__________________加盟店');
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'('.$branchCount.')'.'__________房屋__________________加盟店');
    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(22);

    $row++;
    //經紀業名稱
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, '法人：______________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, '法人：______________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

    $row++;
    //分配金額
    $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);

    $objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, '分配金額：__________________________________');
    $objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
    //列高
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

    $row++;
}

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"□於簽約後全數撥付 □於交屋時全數撥付");//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"□於簽約後全數撥付 □於交屋時全數撥付");//
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

$row++;


$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"□簽約後先撥付________________________________元，");//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"□簽約後先撥付________________________________元，");//
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"餘款交屋時撥付_______________________________元｡");//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"餘款交屋時撥付_______________________________元｡");//
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(17);

$styleArray = array('borders' => array(
        'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
        'right' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
        'left' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),
    ),
);
for($j = 25; $j<= $row; $j++) {
    $objPHPExcel->getActiveSheet()->getStyle('A'.$j.':A'.$j)->applyFromArray($styleArray);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$j.':D'.$j)->applyFromArray($styleArray);
}

//右邊BORDER_DOUBLE
$styleArray = array('borders' => array(
        'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
        'left' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
        'right' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE,'color' => array('argb' => '000000'),),
    ),
);
for($j = 25; $j<= $row; $j++) {
    $objPHPExcel->getActiveSheet()->getStyle('F'.$j.':F'.$j)->applyFromArray($styleArray);
}

$row++;

$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000'),),),);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':F'.($row + 9))->applyFromArray($styleArray);
unset($styleArray);

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"請蓋仲介公司章");//
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".$row);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,"請蓋仲介公司章");//
$objPHPExcel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".$row);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":C".($row+8));
$objPHPExcel->getActiveSheet()->mergeCells("D".$row.":F".($row+8));
$row = $row+9;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'★請經紀業於簽約完成後，儘速填寫此表並蓋公司大章後回傳至第一建經，以利後續出款作業。');//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":F".$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

$row++;

$tel = ($undertaker['pExt'])?$company['tel']."(".$undertaker['undertaker']."*".$undertaker['pExt'].")":$company['tel'];
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,"◆聯絡電話： ".$tel);//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":F".$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);
$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'◆傳真：'.$undertaker['pFaxNum']);//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":F".$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);

//左右邊線
$styleArray = array('borders' => array(
    'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
    'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000'),),
    'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000'),),
),
);
$objPHPExcel->getActiveSheet()->getStyle('A'.($row-1).':F'.($row-1))->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A'.($row-2).':F'.($row-2))->applyFromArray($styleArray);
unset($styleArray);
//底部邊線
$styleArray = array('borders' => array(
    'top' => array('style' => PHPExcel_Style_Border::BORDER_NONE),
    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000'),),
),
);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':F'.$row)->applyFromArray($styleArray);
unset($styleArray);
$row++;

$space = '　　　　　　';
$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$space.'');//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":F".$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);
$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$space.'');//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":F".$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);
$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$space.'');//
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":F".$row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
//列高
$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(19);
$row++;



###############################


$_file = 'service_'.$cid.'.xlsx' ;

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