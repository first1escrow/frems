<?php
// include_once '../openadodb.php' ;
//資料庫鏈結
// include_once '../opendb.php' ;
require_once dirname(__DIR__).'/bank/Classes/PHPExcel.php' ;
require_once dirname(__DIR__).'/bank/Classes/PHPExcel/Writer/Excel2007.php' ;
require_once dirname(__DIR__).'/first1DB.php';

if(!empty($_POST['charge_sales'])) {
	$sales = trim(addslashes(implode(',', $_POST['charge_sales']))); //業務
} else {
	$sales = '0';
}

$start_y=trim(addslashes($_POST['start_y'])); //時間(起)
$start_m=trim(addslashes($_POST['start_m']));
$end_y=trim(addslashes($_POST['end_y']));//時間(迄)
$end_m=trim(addslashes($_POST['end_m']));

$query='';
##

$conn = new first1DB;

//取得合約銀行
$Savings = array() ;
$savingAccount = '' ;

$sql = 'SELECT cBankAccount FROM tContractBank GROUP BY cBankAccount ORDER BY cId ASC;' ;
$Savings = $conn->all($sql);

$savingAccount = implode('","',$Savings) ;
unset($Savings) ;
##

// 取得所有出款保證費紀錄
$sql = '
	SELECT 
		DISTINCT tMemo, 
		tMoney 
	FROM 
		tBankTrans 
	WHERE 
		tAccount IN ("'.$savingAccount.'") 
		AND tPayOk="1" 
	ORDER BY 
		tMemo 
	ASC;';
$rs = $conn->all($sql);

foreach ($rs as $v) {
	$export_data[$v['tMemo']] = $v['tMoney'];
}
##

//進案時間
$from_date = ($start_y + 1911).'-'.str_pad($start_m,2,'0',STR_PAD_LEFT).'-01 00:00:00' ;
$to_date = ($end_y + 1911).'-'.str_pad($end_m,2,'0',STR_PAD_LEFT).'-31 23:59:59' ;

$query .= " cas.cApplyDate>='".$from_date."'";
$query .= ' AND cas.cApplyDate<="'.$to_date.'"';
$query .= " AND csales.cSalesId IN (".$sales.")";


$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("業績統計");
$objPHPExcel->getProperties()->setDescription("第一建經業績統計");

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('C1:E1');

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1','範圍');
$objPHPExcel->getActiveSheet()->setCellValue('B1','時間');
$objPHPExcel->getActiveSheet()->setCellValue('C1','民國'.$start_y.'年'.$start_m.'月 ~ 民國'.$end_y.'年'.$end_m.'月');
$objPHPExcel->getActiveSheet()->setCellValue('A2','業務人員');



$objPHPExcel->getActiveSheet()->setCellValue('A4','序號');
$objPHPExcel->getActiveSheet()->setCellValue('B4','保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('C4','仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue('D4','仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue('E4','賣方');
$objPHPExcel->getActiveSheet()->setCellValue('F4','買方');
$objPHPExcel->getActiveSheet()->setCellValue('G4','總價金');
$objPHPExcel->getActiveSheet()->setCellValue('H4','合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue('I4','出款保證費');
$objPHPExcel->getActiveSheet()->setCellValue('J4','回饋金');
$objPHPExcel->getActiveSheet()->setCellValue('K4','業績分配');
$objPHPExcel->getActiveSheet()->setCellValue('L4','業績');
$objPHPExcel->getActiveSheet()->setCellValue('M4','業務人員');
$objPHPExcel->getActiveSheet()->setCellValue('N4','案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue('O4','進案日期');
$objPHPExcel->getActiveSheet()->setCellValue('P4','實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue('Q4','地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue('R4','標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue('S4','狀態');

// //下面
//出款保證費



$sql ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate, 
	cas.cEndDate as cEndDate, 
	buy.cName as buyer, 
	own.cName as owner, 
	inc.cTotalMoney as cTotalMoney, 
	inc.cCertifiedMoney as cCertifiedMoney, 
	csc.cScrivener as cScrivener, 
	(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener, 
	pro.cAddr as cAddr, 
	pro.cZip as cZip, 
	zip.zCity as zCity, 
	zip.zArea as zArea, 
	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status, 
	csales.cSalesId AS sid,
	(SELECT pName FROM tPeopleInfo WHERE pId=csales.cSalesId) as SalesName,
	csales.cBranch AS bid,
	(SELECT branch.bStore FROM tBranch AS branch WHERE branch.bId=csales.cBranch) as branch,
	cas.cCaseFeedBackMoney AS cCaseFeedBackMoney,
	rea.cBranchNum AS cBranchNum,
	cas.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
	rea.cBranchNum1 AS cBranchNum1,
	cas.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
	rea.cBranchNum2 AS  cBranchNum2
FROM 
	tContractSales AS csales
LEFT JOIN tContractBuyer AS buy ON buy.cCertifiedId=csales.cCertifiedId 
LEFT JOIN tContractOwner AS own ON own.cCertifiedId=csales.cCertifiedId
LEFT JOIN tContractRealestate AS rea ON rea.cCertifyId=csales.cCertifiedId
LEFT JOIN tContractScrivener AS csc ON csc.cCertifiedId=csales.cCertifiedId
LEFT JOIN tContractProperty AS pro ON pro.cCertifiedId=csales.cCertifiedId
LEFT JOIN tContractIncome AS inc ON inc.cCertifiedId=csales.cCertifiedId
LEFT JOIN tZipArea AS zip ON zip.zZip=pro.cZip 
LEFT JOIN tContractCase AS cas  ON  cas.cCertifiedId=csales.cCertifiedId 
WHERE 
'.$query.'
ORDER BY cas.cApplyDate,csales.cCertifiedId,csales.cBranch,csales.cSalesId ASC
' ; 
$rs = $conn->all($sql);

$r = 5;
$i = 1;

foreach ($rs as $row) {
	if($row['cBranchNum'] == $row['bid']) {
		$CaseFeedBackMoney=$row['cCaseFeedBackMoney'];//回饋金
		$bid=getRealtyNo($conn, $row['cBranchNum']); //仲介店編號
	} else if ($row['cBranchNum1'] == $row['bid']) {
		$CaseFeedBackMoney=$row['cCaseFeedBackMoney1'];//回饋金
		$bid=getRealtyNo($conn, $row['cBranchNum1']);//仲介店編號
	} else if ($row['cBranchNum2']==$row['bid']) {
		$CaseFeedBackMoney=$row['cCaseFeedBackMoney2'];//回饋金
		$bid=getRealtyNo($conn, $row['cBranchNum2']);//仲介店編號
	}
	##

	//業績
	$sql='SELECT cSalesId FROM  tContractSales WHERE cBranch ='.$row['bid'].' AND cCertifiedId ='.$row['cCertifiedId'];
	$rel_sales = $conn->all($sql);
	$count = count($rel_sales);
	##

	// 出款保證費
	$mm = 0;
	$mm += $export_data[$row['cCertifiedId']];
	$tmoney = $mm;
	unset($mm);
	##

	//進案日期
	$row['cApplyDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$row['cApplyDate'])) ;
	$tmp = explode('-',$row['cApplyDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
	
	$row['cApplyDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	##

	//實際點交日期
	$row['cFinishDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$row['cFinishDate'])) ;
	$tmp = explode('-',$row['cFinishDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) {
		$tmp[0] = '000';
	} else {
		$tmp[0] -= 1911;
	}
	
	$row['cFinishDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	##

	// 簽約日期
	$row['cSignDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$row['cSignDate'])) ;
	$tmp = explode('-',$row['cSignDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) {
		$tmp[0] = '000';
	} else {
		$tmp[0] -= 1911;
	}
	
	$row['cSignDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	##
	
	// 結案日期
	$row['cEndDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$row['cEndDate'])) ;
	$tmp = explode('-',$row['cEndDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) {
		$tmp[0] = '000';
	} else {
		$tmp[0] -= 1911;
	}
	
	$row['cEndDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	##

	//狀態日期
	if ($row['status']=='3') {
		$staus_date= $row['cEndDate'];
	} else {
		$staus_date= $row['cSignDate'];
	}
	##

	//標的物坐落
	$zc = $row['zCity'] ;
	$row['cAddr'] = preg_replace("/$zc/","",$row['cAddr']) ;
	$zc = $row['zArea'] ;
	$row['cAddr'] = preg_replace("/$zc/","",$row['cAddr']) ;
	$row['cAddr'] = $row['zCity'].$row['zArea'].$row['cAddr'] ;
	##

	//業績
	$sales_money = round(($row['cCertifiedMoney'] - $CaseFeedBackMoney) / $count);

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$r,$i);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$r,'_'.$row['cCertifiedId']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$r,$bid); 
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$r,$row['branch']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$r,$row['owner']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$r,$row['buyer']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$r,$row['cTotalMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$r,$row['cCertifiedMoney']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$r,$tmoney);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$r,$CaseFeedBackMoney);


	$objPHPExcel->getActiveSheet()->setCellValue('K'.$r,$count);
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$r,$sales_money);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$r,$row['SalesName']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$r,$staus_date);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$r,$row['cApplyDate']);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$r,$row['cFinishDate']);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$r,$row['scrivener']);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$r,$row['cAddr']);
	$objPHPExcel->getActiveSheet()->setCellValue('S'.$r,$row['status']);

	$sales_total[$row['sid']] =$sales_total[$row['sid']]+$sales_money;

	$r ++;
	$i ++;
}

##

//上面
$sql='SELECT pName,pId FROM tPeopleInfo WHERE pId IN ('.$sales.')';
$rs = $conn->all($sql);
$i = 66;

//ascii A=65
foreach ($rs as $row) {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($i++).'2',$row['pName']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($i++).'2','總業績');
	//業務總業績計算
	if(!$sales_total[$row['pId']]) {
		$sales_total[$row['pId']] = 0;
	}
	$objPHPExcel->getActiveSheet()->setCellValue(chr($i++).'2',$sales_total[$row['pId']]);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($i++).'2','');
}
##

$_file = '業績統計.xlsx' ;
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

//取得仲介店編號
Function getRealtyNo(&$conn, $no=0) {
	unset($tmp) ;

	if ($no > 0) {
		$sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="'.$no.'";';
		$tmp = $conn->one($sql);
		
		return strtoupper($tmp[bCode]).str_pad($tmp['bId'], 5, '0', STR_PAD_LEFT);
	} else {
		return false;
	}
}
?>