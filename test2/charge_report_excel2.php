<?php
if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../includes/maintain/feedBackData.php';
// include_once 'feedBackData.php';
 $_POST = escapeStr($_POST) ;

$start_y=$_POST['start_y']; //時間(起)
$start_m=$_POST['start_m'];
$end_y=$_POST['end_y'];//時間(迄)
$end_m=$_POST['end_m'];
$cas_status =$_POST['cas_status'];//案件狀態
$brand =$_POST['brand'];//仲介類別
$date_type = $_POST['date_type'];//時間類別
$report_type = $_POST['report_type'];//報表類別
$branch = $_POST['branch'];//仲介類別
$scrivener = $_POST['scrivener'];//地政士
$categoryArray = $_POST['category'];
// echo $_POST['sales'];

// print_r($_POST['charge_sales']);

if ($_SESSION['member_pDep'] == 7) {
	// $sales = $_SESSION['member_id'];
	$sales_arr[] = $_SESSION['member_id'];
}else{
	if(!empty($_POST['charge_sales'])){//業務(多)
		// $sales=implode(',', $_POST['charge_sales']); 
		$sales_arr = $_POST['charge_sales'];

	}elseif (empty($_POST['charge_sales'])&&!empty($_POST['sales'])) {//業務(單)
		// $sales=$_POST['sales'];
		$sales_arr[] = $_POST['sales'];

	}elseif (empty($_POST['charge_sales'])&&empty($_POST['sales'])){
		$sql = 'SELECT pId FROM tPeopleInfo WHERE pJob =1 AND pDep IN("4","7")';
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			# code...
			$sales_arr[] = $rs->fields['pId'] ;
			$rs->MoveNext();
		}
		
		// $sales = implode(',',$sales_arr) ;
	}
}

if (!is_array($sales_arr)) {
	echo "<script>loaction.href='charge_report2.php';</script>";
}
//時間

$from_date = ($start_y + 1911).'-'.str_pad($start_m,2,'0',STR_PAD_LEFT).'-01 00:00:00' ;
$to_date = ($end_y + 1911).'-'.str_pad($end_m,2,'0',STR_PAD_LEFT).'-31 23:59:59' ;

$from_date2 = ($start_y + 1911).'-'.str_pad($start_m,2,'0',STR_PAD_LEFT).'-01' ;
$to_date2 = ($end_y + 1911).'-'.str_pad($end_m,2,'0',STR_PAD_LEFT).'-31' ;

##
//取得合約銀行
$Savings = array() ;
$savingAccount = '' ;
$sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow = 1 GROUP BY cBankAccount ORDER BY cId ASC;' ;

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$Savings[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}
$savingAccount = implode('","',$Savings) ;
unset($Savings) ;

// 取得所有出款保證費紀錄
//090025288這件回存履保費 8/18 回饋先不計算
$sql = '
	SELECT 
		DISTINCT tMemo, 
		tMoney ,
		tBankLoansDate
	FROM 
		tBankTrans 
	WHERE 
		tAccount IN ("'.$savingAccount.'") 
		AND tPayOk="1" 
		AND tKind = "保證費" AND tId != "451978" AND tId != "664562"
		AND tBankLoansDate>="'.$from_date2.'" AND tBankLoansDate<="'.$to_date2.'"
		AND tMemo != "000000000"
	' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$check = 1;
	if ($from_date2 >= '2017-12-01' && $to_date2 <= '2017-12-31' && $rs->fields['tMemo'] == '005078636') {
		$check = 0;
	}

	if ($from_date2 >= '2017-11-01' && $to_date2 <= '2017-11-31' && $rs->fields['tMemo'] == '004070501') {
		$check = 0;
	}

	if ($rs->fields['tMemo'] == '007106507') {
		$check = 0;
	}

	if ($check == 1) {

		// $export_date[$rs->fields['tMemo']] = $rs->fields['tBankLoansDate'] ;
		$cCertifiedId[$rs->fields['tMemo']]['cCertifiedId'] = $rs->fields['tMemo'];
		$cCertifiedId[$rs->fields['tMemo']]['tBankLoansDate'] = $rs->fields['tBankLoansDate'];
	}
	
	$rs->MoveNext();
}

//fail
if ($from_date2 >= '2020-01-01' && $to_date2 < '2020-04-01') {
	foreach ($cCertifiedId as $key => $value) {
		$sql = "SELECT * FROM tContractSales WHERE cLastModifyDate >= '2020-04-02 00:00:00' AND cCertifiedId = '".$key."' ";
		$rs = $conn->Execute($sql);

		if (!$rs->EOF) {
			$failCase[] = $key;
		}
		
	}
}


// while (!$rs->EOF) {
	

// 	$rs->MoveNext();
// }

unset($check);
##
if ($from_date2 == '2018-01-01' && ($sales == 34 || in_array($sales, $sales_arr))) { //20180103忘記調整到這筆導致下個月才能計算
	$cCertifiedId['005078636']['tBankLoansDate'] = '2018-01-00' ;
	$cCertifiedId['005078636']['cCertifiedId']= '005078636';
}

if ($from_date2 == '2018-01-01') {
	# code...
	$cCertifiedId['004070501']['tBankLoansDate'] = '2018-01-00' ;
	$cCertifiedId['004070501']['cCertifiedId']= '005078636';

}

if ($from_date2 <= '2018-12-01' && $to_date2 <= '2018-12-31') {
	$cCertifiedId['007106507']['tBankLoansDate'] = '2018-12-01' ;
	$cCertifiedId['007106507']['cCertifiedId']= '005078636';
}
##
$_sql = 'SELECT cc.cCertifiedId,cc.cBankList,(SELECT cCertifiedMoney FROM tContractIncome AS ci WHERE ci.cCertifiedId=cc.cCertifiedId) AS cCertifiedMoney FROM tContractCase AS cc WHERE cc.cBankList >= "'.$from_date2.'" AND cc.cBankList<= "'.$to_date2.'"';
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	if ($rs->fields['cCertifiedId'] != '007106507') {

		$cCertifiedId[$rs->fields['cCertifiedId']]['tBankLoansDate'] = $rs->fields['cBankList'] ;
		$cCertifiedId[$rs->fields['cCertifiedId']]['cCertifiedId']= $rs->fields['cCertifiedId'];
	}
	
	$rs->MoveNext();
}
##
//案件狀態
$query ='';

if($cas_status != '0'){
	if ($query) { $query .= ' AND ';}
	$query .=" cas.cCaseStatus = '".$cas_status."'";

}


//時間類別
if($date_type == 1){
	if ($query) { $query .= ' AND ';}
	$query .= " cas.cSignDate>='".$from_date."' AND cas.cSignDate<='".$to_date."'";

	

	$sql = "SELECT cCertifiedId FROM tContractCase AS cas WHERE ".$query;
	$rs = $conn->Execute($sql);
	unset($cCertifiedId);
	$i = 0;
	while (!$rs->EOF) {
		$cCertifiedId[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
		$i++;
		$rs->MoveNext();
	}
	unset($i);
}elseif ($date_type == 2) {

	if ($query) { $query .= ' AND ';}
	$query .= ' 1=1';
	
}

##
$data2 = array();
$sales_month = array();
// $AA = array();
//ORDER BY b.bCategory,b.bBrand,csales.cCertifiedId,csales.cSalesId ASC


if ($from_date2 >= "2021-07-01") {
	require_once 'charge_report_excel2_2021.php';
}else{
	require_once 'charge_report_excel2_old.php';
}


// echo "<pre>";
// header("Content-Type:text/html; charset=utf-8"); 
// echo "<pre>";
// print_r($list);

// die;

$r = 3;//起始
//上面

if ($report_type==2) { //N個業務比較

	$objPHPExcel->getActiveSheet()->setCellValue('A2','多個業務比較');
	$sql='SELECT pName,pId FROM tPeopleInfo WHERE pId IN ('.@implode(',', $sales_arr).')';
	$rs = $conn->Execute($sql);


	$colT = 65;
	$colTC = 0;
	
	$col=66;
	while (!$rs->EOF) {

		$colname = checkCol();
		$objPHPExcel->getActiveSheet()->setCellValue($colname.'3',$rs->fields['pName']);
		$col++;
		#
		$colname = checkCol();
		$objPHPExcel->getActiveSheet()->setCellValue($colname.'3','總業績');
		$col++;
		//業務總業績計算
		if(!$sales_total[$rs->fields['pId']]){
			$sales_total[$rs->fields['pId']] = 0;
		}
		
		$colname = checkCol();
		$objPHPExcel->getActiveSheet()->setCellValue($colname.'3',$sales_total[$rs->fields['pId']]);
		$col++;

		$colname = checkCol();
		$objPHPExcel->getActiveSheet()->setCellValue($colname.'3','');
		$col++;
	

		$rs->MoveNext();
	}
	
	


	// print_r($sales_total);

	// $objPHPExcel->getActiveSheet()->getStyle('A3:'.chr($c).'3')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'CCCCCC')),));
}else{
	$objPHPExcel->getActiveSheet()->setCellValue('A2','單一業務比較');
	$c=66;

	ksort($sales_month);

	foreach ($sales_month as $key =>$v) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$key);
	}

	$r++;

	$sql='SELECT pName,pId FROM tPeopleInfo WHERE pId IN ('.@implode(',', $sales_arr).')';
	$rs = $conn->Execute($sql);
	
	$c=65;
	while (!$rs->EOF) {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$rs->fields['pName']);
		foreach ($sales_month as $key =>$v) {
			$objPHPExcel->getActiveSheet()->setCellValue(chr($c++).$r,$sales_month[$key]);
		}

		$rs->MoveNext();
	}
	
	$objPHPExcel->getActiveSheet()->getStyle('A3:'.chr($c).$r)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'CCCCCC')),));
}
##

$_file = iconv('UTF-8', 'BIG5', '業績報表') ;
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


##
function checkSalesResignation($date,$sales,$branchId,$scrivenerId,$target,$salesCount,$cId,$creator){//判斷是否有離職的區間
	global $conn;
	// global $AA;

	if ($creator) {//手動改過
		return $sales;
	}

	if ($target == 1) { //target ->1:仲介、2:代書 3特殊回饋 type->1:代書 2仲介  
		$type = 2;
		$storeId  = $branchId;

	}elseif ($target == 2 || $target == 3) {
		$type = 1;
		$storeId  = $scrivenerId;

	}

	
	//先取得案件簽約日當下店家業務資料最近的一筆資料日期

	$sql = "SELECT sDate FROM tSalesRegionalAttribution WHERE sType = '".$type."' AND sStoreId = '".$storeId."' AND sDate <='".$date."' AND sDelete = 0 ORDER BY sDate DESC LIMIT 1";
	
	$rs = $conn->Execute($sql);

	if (!$rs->EOF) {
		$sDate = $rs->fields['sDate'];
	}else{
		return $sales; //tSalesRegionalAttribution 查不到表示這間店沒有手動調整過店家業務
	}

	
	//在抓取業務分配狀況(同一間店可能會有兩個業務) $checkSales 
	$sql = "SELECT sSales FROM tSalesRegionalAttribution WHERE sDate ='".$sDate."' AND sStoreId = '".$storeId."' AND sType = '".$type."'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$checkSales[] = $rs->fields['sSales'];

		$rs->MoveNext();
	}




	if (in_array($sales, $checkSales)) { // 符合
		
		
		return $sales;
	}else{ //不符合，
			// echo '??';
		if ($salesCount > 1) {
			return $sales;
			// resetSales();
		}else{

			return $checkSales[0];
		}
	
	}

}
function dateformate($val){

	$val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
	
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}

function addr($cid){
	global $conn;
	$sql = "SELECT 
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=pro.cZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=pro.cZip) AS area,
				cAddr
			FROM 
				tContractProperty AS pro 
			WHERE 
				cCertifiedId='".$cid."'";
	$rs = $conn->Execute($sql);

	

	while (!$rs->EOF) {
		$addr .= $rs->fields['city'].$rs->fields['area'].$rs->fields['cAddr'].";";
		$rs->MoveNext();
	}

	return $addr;
}
function checkCol(){
	global $col;
	global $colT;
	global $colTC;
		
	if ($col > 90) { //Z是90
		$tmp = $col-91;	 
		$colT = $colT+$colTC;
		$col = 65+$tmp;
		$colTC++;
	}

	$name = ($colTC == 0)? chr($col): chr($colT).chr($col);

	return $name;
}
	
?>