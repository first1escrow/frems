<?php
//資料庫鏈結
include_once '../openadodb.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;
include_once '../includes/maintain/feedBackData.php';

$_POST = escapeStr($_POST) ;

$start_y= $_POST['start_y']; //時間(起)
$start_m= $_POST['start_m'];
$end_y= $_POST['end_y'];//時間(迄)
$end_m= $_POST['end_m'];
$cas_status = $_POST['cas_status'];//案件狀態
$branch = $_POST['branch'];//仲介類別
$scrivener = $_POST['scrivener'];//地政士
$date_type =  $_POST['date_type'];//時間類別
$sales = addslashes($_SESSION['member_id']);

if ($_SESSION['member_id'] == 6) {
	$sales = 38;
}

$query='';
##
//取得合約銀行
$Savings = array() ;
$savingAccount = '' ;
$i = 0 ;
$sql = 'SELECT cBankAccount FROM tContractBank GROUP BY cBankAccount ORDER BY cId ASC;' ;
$rs= $conn->Execute($sql);

while (!$rs->EOF) {
	$Savings[$i++] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}

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
	ASC' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$export_data[$rs->fields['tMemo']] = $rs->fields['tMoney'] ;

	$rs->MoveNext();
}
##
##

//進案時間

$from_date = ($start_y + 1911).'-'.str_pad($start_m,2,'0',STR_PAD_LEFT).'-01 00:00:00' ;
$to_date = ($end_y + 1911).'-'.str_pad($end_m,2,'0',STR_PAD_LEFT).'-31 23:59:59' ;
//時間類別
if($date_type==1)
{
	$query .= " cas.cApplyDate>='".$from_date."'";
	$query .= "AND cas.cApplyDate<='".$to_date."'";
	$query2 .= " cas.cApplyDate>='".$from_date."' AND cas.cApplyDate<='".$to_date."'";
}elseif ($date_type==2) {
	$query .= " cas.cEndDate>='".$from_date."'";
	$query .= " AND cas.cEndDate<='".$to_date."'";
	$query2 .= " cas.cEndDate>='".$from_date."' AND cas.cEndDate<='".$to_date."'";
}
//案件狀態
if($cas_status != '0')
{
	$query .=" AND cas.cCaseStatus = '".$cas_status."'";
	$query2 .=" AND cas.cCaseStatus = '".$cas_status."'";
}

	
//業務
if($sales!='')
{
	$query .= " AND csales.cSalesId IN (".$sales.")";
	
}


if ($branch) {
	$query .= " AND (rea.cBranchNum ='".$branch."' OR rea.cBranchNum1 = '".$branch."' OR rea.cBranchNum2 = '".$branch."')";
}

if ($scrivener) {
	$query .=' AND csc.cScrivener = "'.$scrivener.'"';
}
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

	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status, 
	csales.cSalesId AS sid,
	(SELECT pName FROM tPeopleInfo WHERE pId=csales.cSalesId) as SalesName,
	csales.cBranch AS bid,
	b.bName AS branch,
	(SELECT bName FROM  tBrand AS brand WHERE brand.bId=rea.cBrand) as brand,
	rea.cBranchNum AS cBranchNum,
	rea.cBranchNum1 AS cBranchNum1,
	rea.cBranchNum2 AS  cBranchNum2,
	rea.cBrand AS cBrand,
	rea.cBrand1 AS cBrand1,
	rea.cBrand2 AS  cBrand2,
	b.bCategory AS bCategory,
	b.bBrand AS bBrand,
	b.bStore AS store
FROM 
	tContractSales AS csales
LEFT JOIN tContractBuyer AS buy ON buy.cCertifiedId=csales.cCertifiedId 
LEFT JOIN tContractOwner AS own ON own.cCertifiedId=csales.cCertifiedId
LEFT JOIN tContractRealestate AS rea ON rea.cCertifyId=csales.cCertifiedId
LEFT JOIN tContractScrivener AS csc ON csc.cCertifiedId=csales.cCertifiedId
LEFT JOIN tContractIncome AS inc ON inc.cCertifiedId=csales.cCertifiedId
LEFT JOIN tContractCase AS cas  ON  cas.cCertifiedId=csales.cCertifiedId 
LEFT JOIN tBranch AS b ON b.bId=csales.cBranch
WHERE 
'.$query.'
ORDER BY b.bCategory,b.bBrand,csales.cCertifiedId,csales.cSalesId ASC
' ;



$rs = $conn->Execute($sql);
$data2 = array();
while (!$rs->EOF) {
	$list[] = $rs->fields;
	
	$rs->MoveNext();
}
	//撈取其他回饋對象
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

	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status, 
	csales.cSalesId AS sid,
	(SELECT pName FROM tPeopleInfo WHERE pId=csales.cSalesId) as SalesName,
	csales.cBranch AS bid,
	b.bName AS branch,
	(SELECT bName FROM  tBrand AS brand WHERE brand.bId=rea.cBrand) as brand,
	rea.cBranchNum AS cBranchNum,
	rea.cBranchNum1 AS cBranchNum1,
	rea.cBranchNum2 AS  cBranchNum2,
	rea.cBrand AS cBrand,
	rea.cBrand1 AS cBrand1,
	rea.cBrand2 AS  cBrand2,
	b.bCategory AS bCategory,
	b.bBrand AS bBrand,
	b.bStore AS store
FROM 
	 tContractCase AS cas
LEFT JOIN tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId 
LEFT JOIN tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
LEFT JOIN tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
LEFT JOIN tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
LEFT JOIN tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
LEFT JOIN tContractSales AS csales  ON  cas.cCertifiedId=csales.cCertifiedId 
LEFT JOIN tBranch AS b ON b.bId=csales.cBranch
WHERE 
'.$query2.'
GROUP BY cas.cCertifiedId 
ORDER BY b.bCategory,b.bBrand,csales.cCertifiedId,csales.cSalesId ASC' ;
// echo $sql;
// die;
$cer =array();
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
		

	$tmp = getOtherFeed2($rs->fields);
	if (is_array($tmp)) {
				
		$data2 = array_merge($data2,$tmp);
		unset($tmp);
	}
	##

	$rs->MoveNext();
}

if (is_array($data2)) {
	$list = array_merge($list,$data2);
}

for ($i=0; $i < count($list); $i++) { 



	$tmp2 = getOtherFeedMoney($list[$i]['cCertifiedId']);

	$sql="SELECT* FROM  tContractCase WHERE cCertifiedId =".$list[$i]['cCertifiedId']."";
	$rs = $conn->Execute($sql);
	$tmp = $rs->fields;

	if ($tmp['cCaseFeedback'] == 1) {$tmp['cCaseFeedBackMoney'] = 0;}
	if ($tmp['cCaseFeedback1'] == 1) {$tmp['cCaseFeedBackMoney1'] = 0;}
	if ($tmp['cCaseFeedback2'] == 1) {$tmp['cCaseFeedBackMoney2'] = 0;}

	$CaseFeedBackMoney=$tmp['cCaseFeedBackMoney']+$tmp['cCaseFeedBackMoney1']+$tmp['cCaseFeedBackMoney2']+$tmp['cSpCaseFeedBackMoney']+$tmp2['fMoney'];
	$list[$i]['CaseFeedBackMoney'] = $CaseFeedBackMoney;
	$bid='';
	if($list[$i]['cBranchNum'] == $list[$i]['bid'])
	{

		$bid=getRealtyNo($list[$i]['cBranchNum']);
		$list[$i]['branch_id'] = $bid;
		
		
	}elseif ($list[$i]['cBranchNum1'] == $list[$i]['bid']) 
	{

		$bid=getRealtyNo($list[$i]['cBranchNum1']);//仲介店編號
		// $brandName = $list[$i]['brand1'];
		$list[$i]['branch_id'] = $bid;
		
		
	}elseif ($list[$i]['cBranchNum2']==$list[$i]['bid']) 
	{
		$bid=getRealtyNo($list[$i]['cBranchNum2']);//仲介店編號
		// $brandName = $list[$i]['brand2'];
		$list[$i]['branch_id'] = $bid;
		
	}else
	{
		continue;
	}



	
	//業績

	// $sql='SELECT cSalesId FROM  tContractSales WHERE cBranch ='.$list[$i]['bid'].' AND cCertifiedId ='.$list[$i]['cCertifiedId'];
	$sql='SELECT cSalesId FROM  tContractSales WHERE  cCertifiedId ='.$list[$i]['cCertifiedId'];
	$rs = $conn->Execute($sql);
	$count = $rs->RecordCount();
	$count = $count + $tmp2['fCount'];

	
	
	$list[$i]['count'] = $count;

	//業績
	$sales_money=round(($list[$i]['cCertifiedMoney']-$CaseFeedBackMoney)/$count);

	$list[$i]['sales_money'] = $sales_money;
	unset($tmp);

	unset($tmp2);


	// 出款保證費
	$mm = 0 ;
	$mm += $export_data[$list[$i]['cCertifiedId']] ;
	$tmoney = $mm ;

	$list[$i]['tmoney'] = $tmoney;

	unset($mm) ;
	##

	//進案日期
	$list[$i]['cApplyDate'] = date_change($list[$i]['cApplyDate']) ;
	//實際點交日期
	$list[$i]['cFinishDate'] = date_change($list[$i]['cFinishDate']) ;
	// 簽約日期
	$list[$i]['cSignDate'] = date_change($list[$i]['cSignDate']) ;
	//結案日期
	$list[$i]['cEndDate'] = date_change($list[$i]['cEndDate']) ;
	##

	//狀態日期

	if ($list[$i]['status']=='已結案') {
		$staus_date= $list[$i]['cEndDate'];
	}
	else {
		$staus_date= $list[$i]['cSignDate'];
	}

	$list[$i]['staus_date'] = $staus_date;

	//標的物坐落
	$sql = "SELECT 
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=pro.cZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=pro.cZip) AS area,
				cAddr
			FROM 
				tContractProperty AS pro 
			WHERE 
				cCertifiedId='".$list[$i]['cCertifiedId']."'";
	// echo $sql;
	// die;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$addr[] =  $rs->fields['city'].$rs->fields['area'].$rs->fields['cAddr'];

		$rs->MoveNext();
	}

	$list[$i]['cAddr'] = implode(';', $addr);

	unset($addr);
	##
	
	##

	// 仲介類別

	if($list[$i]['bCategory']==1 && $list[$i]['cBrand'] !=1 && $list[$i]['cBrand'] !=2 && $list[$i]['cBrand'] !=49)//加盟(其他品牌)
	{
		$cat ='加盟(其他品牌)';
	}elseif ($list[$i]['bCategory']==1 &&$list[$i]['cBrand']==1) {//加盟(台灣房屋)
		$cat ='加盟(台灣房屋)';
	}elseif ($list[$i]['bCategory']==1 && $list[$i]['cBrand']==49) {//加盟(優美地產)
		$cat ='加盟(優美地產)';
	}elseif ($list[$i]['bCategory']==1) {//加盟
		$cat ='加盟';
	}elseif ($list[$i]['bCategory']==2) {//直營
		$cat ='直營';
	}elseif ($list[$i]['bCategory']==3) {//非仲介成交
		$cat ='非仲介成交';
	}else{
		$cat ='';
	}

	$list[$i]['cat'] = $cat;

	//月業績
	##

	


	if ($sales == $list[$i]['sid']) {
		if($date_type==1)
		{	
			preg_match_all("/(.*)-/U",$list[$i]['cApplyDate'] , $tmp);
			
				
		}elseif ($date_type==2) {
				// cEndDate
			preg_match_all("/(.*)-/U",$list[$i]['cEndDate'] , $tmp);
		}

		if ($sales == 3) {
			$month=$tmp[1][0].'-'.$tmp[1][1];
			$sales_month[$month]=$sales_month[$month]+$sales_money;

			unset($tmp);
		
			$arr[] = $list[$i];
		}else{
			if ($list[$i]['brand'] != '台灣房屋' && $list[$i]['brand'] != '優美地產') {
				$month=$tmp[1][0].'-'.$tmp[1][1];
				$sales_month[$month]=$sales_month[$month]+$sales_money;

				unset($tmp);
			
				$arr[] = $list[$i];
			}
		}

		
	}
		
}



unset($list);


$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("業績統計");
$objPHPExcel->getProperties()->setDescription("第一建經業績統計");

//合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells('C1:E1');

//基本資料開始頁數
$r=3;//列
$c=66;//欄

$objPHPExcel->getActiveSheet()->setCellValue('A2','單一業務比較');
	
ksort($sales_month);
	
foreach ($sales_month as $key =>$v) {

	$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$key);

}
$r++;
$sql='SELECT pName,pId FROM tPeopleInfo WHERE pId IN ('.$sales.')';

	$rs = $conn->Execute($sql);

	$c=65;//ascii A=65

	while (!$rs->EOF) {
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$rs->fields['pName']);
		
		foreach ($sales_month as $key =>$v) {
			$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$sales_month[$key]);
		}
		$objPHPExcel->getActiveSheet()->getStyle('A3:'.chr($c).$r)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'CCCCCC')),));
		$r++;

		$rs->MoveNext();
	}


$c=65;//欄
//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1','範圍');
$objPHPExcel->getActiveSheet()->setCellValue('B1','時間');
$objPHPExcel->getActiveSheet()->setCellValue('C1','民國'.$start_y.'年'.$start_m.'月 ~ 民國'.$end_y.'年'.$end_m.'月');

$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'序號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介品牌');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介店編號');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介店名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'公司名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'賣方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'買方');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'總價金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'合約保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'出款保證費');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'回饋金');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'業績分配');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'業績');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'業務人員');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'案件狀態日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'進案日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'實際點交日期');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'地政士姓名');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'標的物座落');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'狀態');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'仲介類別');
$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,'結案日期');

$r++;
$ttt= 0;

for ($i=0; $i < count($arr); $i++) { 


	##
	$c=65;//欄
	##

	if ($sales == 3) {
		if ($bid == 'NG00505' || $arr[$i]['fType'] == 1) {
				$bid = 'SC'.str_pad($arr[$i]['cScrivener'],4,'0',STR_PAD_LEFT); 
				if ($arr[$i]['fType'] == 1) {
					$arr[$i]['branch_id'] = $bid;
				}
			}
			
			if ($arr[$i]['fType']) {$mark = '*';}
		
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$mark.($ttt+1));
				//$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).($r+1),'_'.$arr[$i]['cCertifiedId']);
				$objPHPExcel->getActiveSheet()->getCell(chr($c++).$r)->setValueExplicit($arr[$i]['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);

				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['brand']);///C
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['branch_id']);//D
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['store']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['branch']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['owner']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['buyer']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cTotalMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cCertifiedMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['tmoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['CaseFeedBackMoney']);


				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['count']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['sales_money']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['SalesName']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['staus_date']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cApplyDate']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cFinishDate']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['scrivener']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cAddr']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['status']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cat']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cEndDate']);
				##
				$r++;
				$ttt++;
	}else{

		if ($arr[$i]['brand'] != '台灣房屋' && $arr[$i]['brand'] != '優美地產') {
			if ($bid == 'NG00505' || $arr[$i]['fType'] == 1) {
				$bid = 'SC'.str_pad($arr[$i]['cScrivener'],4,'0',STR_PAD_LEFT); 
				if ($arr[$i]['fType'] == 1) {
					$arr[$i]['branch_id'] = $bid;
				}
			}
			
			if ($arr[$i]['fType']) {$mark = '*';}
		
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$mark.($ttt+1));
				//$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).($r+1),'_'.$arr[$i]['cCertifiedId']);
				$objPHPExcel->getActiveSheet()->getCell(chr($c++).$r)->setValueExplicit($arr[$i]['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING);

				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['brand']);///C
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['branch_id']);//D
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['store']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['branch']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['owner']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['buyer']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cTotalMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cCertifiedMoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['tmoney']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['CaseFeedBackMoney']);


				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['count']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['sales_money']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['SalesName']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['staus_date']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cApplyDate']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cFinishDate']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['scrivener']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cAddr']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['status']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cat']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$arr[$i]['cEndDate']);
				##
				$r++;
				$ttt++;

		}
	}
	

}










##

$_file = 'personal_sales_report' ;
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
//取得仲介店編號
function getRealtyNo($no=0) {
	
	global $conn;
	if ($no > 0) {
		$sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="'.$no.'";' ;
		
		$rs = $conn->Execute($sql);
		
		return strtoupper($rs->fields['bCode']).str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT) ;
	}
	else {
		return false ;
	}
}

function date_change($txt)
{
	$txt = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$txt)) ;
	$tmp = explode('-',$txt) ; 
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }

	$txt = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;

	unset($tmp) ;

	return $txt;
}
?>