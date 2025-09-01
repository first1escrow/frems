<?php

include_once '../configs/config.class.php' ;
include_once 'class/intolog.php' ;
include_once '../session_check.php' ;
include_once 'getBranchType.php';

//資料庫鏈結
include_once '../openadodb.php';
require_once dirname(__DIR__).'/first1DB.php';
##

//預載log物件
$logs = new Intolog() ;
##

//取得仲介列表權限
$showList = $_SESSION['member_pRealtyCaseList'] ;
##

//取得區域資訊
if ($city) {
	$sql = 'SELECT * FROM tZipArea WHERE zCity="'.$city.'"' ;
	if ($area) {
		$sql .= ' AND zArea="'.$area.'"' ;	
	}
	
	$_conn = new first1DB;
	$zip = $_conn->all($sql);
	$_conn = null; unset($_conn);

	$zip_str = '' ;
	if ($zip) {
		$zip_str = implode('","',$zip) ;
		$zip_str = '"'.$zip_str.'"' ;
	}
}
##


//取得區域郵遞區號字串
$zip_sql = '' ;
if ($zip_str) {
	$zip_sql = ' AND bZip IN ('.$zip_str.')' ;
}
##
	
//取得仲介店名稱
$branch = array() ;
$branchA_sql = '' ;

if ($statusOff == '1') $statusOff = ' AND bStatus="1" ' ;		//過濾已關店頭資料 2015-09-03
//取得"比較"時間範圍之保證號碼
$from_dateA = ($Af_year + 1911).'-'.str_pad($Af_month,2,'0',STR_PAD_LEFT).'-01 00:00:00' ;
$to_dateA = ($At_year + 1911).'-'.str_pad($At_month,2,'0',STR_PAD_LEFT).'-31 23:59:59' ;
$time_strA =' AND bCreat_time <="'.$to_dateA.'"';
if (($brA) && ($brB)) {
	$branchA = array() ;
	$branchB = array() ;
	
	//取得A仲介店名稱
	$branchA_sql = ' AND bId="'.$brA.'"' ;
	
	$sql = 'SELECT bId,(SELECT bName FROM tBrand WHERE bId=a.bBrand) as brand,bStore FROM tBranch AS a WHERE bId<>"0"'.$branchA_sql.$zip_sql.$time_strA.' ORDER BY bId ASC;' ;

	$_conn = new first1DB;
	$rel = $_conn->all($sql);
	$_conn = null; unset($_conn);

	for ($i = 0 ; $i < count($rel) ; $i ++) {
		$branchA[$i] = $rel[$i] ;
		$branchA[$i]['caseMax'] = 0 ;
		$branchA[$i]['caseMoney'] = 0 ; 
		$branchA[$i]['HQ'] = 0 ;
		$branchA[$i]['sales'] = getSalesName('b',$branchA[$i]['bId']);
	}
	##
	
	//取得B仲介店名稱
	$branchB_sql = ' AND bId="'.$brB.'"' ;
	
	$sql = 'SELECT bId,(SELECT bName FROM tBrand WHERE bId=a.bBrand) as brand,bStore FROM tBranch AS a WHERE bId<>"0"'.$branchB_sql.$zip_sql.$time_strA.' ORDER BY bId ASC;' ;
	
	$_conn = new first1DB;
	$rel = $_conn->all($sql);
	$_conn = null; unset($_conn);

	for ($i = 0 ; $i < count($rel) ; $i ++) {
		$branchB[$i] = $rel[$i] ;
		$branchB[$i]['caseMax'] = 0 ;
		$branchB[$i]['caseMoney'] = 0 ; 
		$branchB[$i]['HQ'] = 0 ;
		$branchB[$i]['sales'] = getSalesName('b',$branchB[$i]['bId']);
	}
	##
	
	$branch = array_merge($branchA,$branchB) ;
	unset($branchA) ; 
	unset($branchB) ;
}
else {
	if ($twhgBranch && $twhgBranch2 && $other) {
		$str =	' AND (bCategory="1" OR bCategory="2") ';

	}else if ($twhgBranch && $twhgBranch2) {
		$str =	' AND bBrand="1" AND (bCategory="1" OR bCategory="2") ';

	}elseif(($twhgBranch || $twhgBranch2) && $other){
		if ($twhgBranch) {
			$str = ' AND bCategory="1"';
		}elseif($twhgBranch2){
			$str = ' AND ((bBrand="1" AND bCategory="2") OR (bBrand !="1" AND bCategory="1")) ';
		}


	}else if ($twhgBranch) {
		$str = ' AND bBrand="1" AND bCategory="1" ';
	}elseif($twhgBranch2){
		$str = ' AND bBrand="1" AND bCategory="2" ';
	}elseif ($other) {
		$str = ' AND bBrand !="1" AND bCategory="1"';
	}
	
	$sql = 'SELECT bId,(SELECT bName FROM tBrand WHERE bId=a.bBrand) as brand,bStore FROM tBranch AS a WHERE bId<>"0"'.$str.$zip_sql.$statusOff.$time_strA.' ORDER BY bId ASC;' ;

	$_conn = new first1DB;
	$rel = $_conn->all($sql);
	$_conn = null; unset($_conn);

	for ($i = 0 ; $i < count($rel) ; $i ++) {
		$branch[$i] = $rel[$i] ;
		$branch[$i]['caseMax'] = 0 ;
		$branch[$i]['caseMoney'] = 0 ; 
		$branch[$i]['HQ'] = 0 ;
		$branch[$i]['sales'] = getSalesName('b',$branch[$i]['bId']);
	}

	
}

$branchA = array_merge($branch) ;
$branchB = array_merge($branch) ;
##


	
$sql = '
	SELECT 
		cas.cCertifiedId as cCertifiedId,
		inc.cCertifiedMoney as cCertifiedMoney,
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		rea.cBranchNum as branch,
		rea.cBranchNum1 as branch1,
		rea.cBranchNum2 as branch2,
		rea.cBrand as brand,
		rea.cBrand1 as brand1,
		rea.cBrand2 as brand2
	FROM 
		tContractCase AS cas
	JOIN
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
	JOIN
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
	WHERE 
		cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus<>"8"
		AND cas.cSignDate>="'.$from_dateA.'" 
		AND cas.cSignDate<="'.$to_dateA.'" 
	ORDER BY 
		cas.cSignDate 
	ASC;
' ;
//cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" AND cas.cSignDate>="2017-03-01 00:00:00" AND cas.cSignDate<="2017-03-31 23:59:59" AND cas.cCaseStatus<>"8" 

$_conn = new first1DB;
$listA = $_conn->all($sql);
$_conn = null; unset($_conn);
##
//print_r($listA) ; exit ;
//計算"比較"期間仲介店件數與金額
for ($i = 0 ; $i < count($listA) ; $i ++) {
	//依據仲介店數量，均分履保金額
	$realty_mod = 0 ;
	$realty_base = 0 ;
	// if ($listA[$i]['cBranchNum'] != '0') { $realty_base ++ ; }
	// if ($listA[$i]['cBranchNum1'] != '0') { $realty_base ++ ; }
	// if ($listA[$i]['cBranchNum2'] != '0') { $realty_base ++ ; }
	
	// if (($listA[$i]['cCertifiedMoney'] > 0) && ($realty_base > 0)) {
	// 	$realty_mod = $listA[$i]['cCertifiedMoney'] % $realty_base ;
	// 	if ($realty_base > 1) {
	// 		$listA[$i]['cCertifiedMoney'] = floor($listA[$i]['cCertifiedMoney'] / $realty_base) ;
	// 	}
	// }
	##
	$type = branch_type2($conn,$listA[$i]);
	for ($j = 0 ; $j < count($branchA) ; $j ++) {
		//第一家仲介
		
		if ($listA[$i]['cBranchNum'] != '0') {
			if ($branchA[$j]['bId'] == $listA[$i]['cBranchNum'] && $type['bid'] == $listA[$i]['cBranchNum']) {
				$branchA[$j]['caseMax'] ++ ;
				$branchA[$j]['caseMoney'] += $listA[$i]['cCertifiedMoney'] + $realty_mod ;
				continue ;
			}
		}
		##
		
		//第二家仲介
		if ($listA[$i]['cBranchNum1'] != '0') {
			if ($branchA[$j]['bId'] == $listA[$i]['cBranchNum1'] && $type['bid'] == $listA[$i]['cBranchNum1']) {
				$branchA[$j]['caseMax'] ++ ;
				$branchA[$j]['caseMoney'] += $listA[$i]['cCertifiedMoney'] ;
				continue ;
			}
		}
		##
		
		//第三家仲介
		if ($listA[$i]['cBranchNum2'] != '0') {
			if ($branchA[$j]['bId'] == $listA[$i]['cBranchNum1'] && $type['bid'] == $listA[$i]['cBranchNum2']) {
				$branchA[$j]['caseMax'] ++ ;
				$branchA[$j]['caseMoney'] += $listA[$i]['cCertifiedMoney'] ;
				continue ;
			}
		}
		##

		
	}
	unset($type);
}
##

//print_r($branchA) ; exit ;

//取得總部成交案件數
unset($HQ) ;
$sql = '
	SELECT
		*
	FROM
		tHQCase
	WHERE
		SIGN_DATE>="'.substr($from_dateA,0,10).'"
		AND SIGN_DATE<="'.substr($to_dateA,0,10).'"
' ;

$_conn = new first1DB;
$HQ = $_conn->all($sql);
$_conn = null; unset($_conn);
##

//重整"比較"期間仲介店資料
for ($i = 0 ; $i < count($HQ) ; $i ++) {
	$fg = 0 ;
	for ($j = 0 ; $j < count($branchA) ; $j ++) {
		if ($HQ[$i]['DEP_NAM'] == $branchA[$j]['bStore']) {
			$branchA[$j]['HQ'] ++ ;
			$fg ++ ;
			break ;
		}
	}
	if ($fg <= 0) {
		$HQA[] = $HQ[$i] ;
	}
}
##

//取得"對照"時間範圍之保證號碼
$from_dateB = ($Bf_year + 1911).'-'.str_pad($Bf_month,2,'0',STR_PAD_LEFT).'-01 00:00:00' ;
$to_dateB = ($Bt_year + 1911).'-'.str_pad($Bt_month,2,'0',STR_PAD_LEFT).'-31 23:59:59' ;

$sql = '
	SELECT 
		cas.cCertifiedId as cCertifiedId,
		inc.cCertifiedMoney as cCertifiedMoney,
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		rea.cBranchNum as branch,
		rea.cBranchNum1 as branch1,
		rea.cBranchNum2 as branch2,
		rea.cBrand as brand,
		rea.cBrand1 as brand1,
		rea.cBrand2 as brand2
	FROM 
		tContractCase AS cas
	JOIN
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
	JOIN
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
	WHERE 
		cas.cCertifiedId<>""
		AND cas.cSignDate>="'.$from_dateB.'" 
		AND cas.cSignDate<="'.$to_dateB.'" 
		AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus<>"8"
	ORDER BY 
		cas.cSignDate 
	ASC;
' ;

$_conn = new first1DB;
$listB = $_conn->all($sql);
$_conn = null; unset($_conn);
##

//計算"對照"期間仲介店件數與金額
for ($i = 0 ; $i < count($listB) ; $i ++) {
	// 依據仲介店數量，均分履保金額(X)
	$realty_mod = 0 ;
	$realty_base = 0 ;
	
	// if ($listB[$i]['cBranchNum'] != '0') { $realty_base ++ ; }
	// if ($listB[$i]['cBranchNum1'] != '0') { $realty_base ++ ; }
	// if ($listB[$i]['cBranchNum2'] != '0') { $realty_base ++ ; }
	
	// if (($listB[$i]['cCertifiedMoney'] > 0) && ($realty_base > 0)) {
	// 	$realty_mod = $listB[$i]['cCertifiedMoney'] % $realty_base ;
	// 	if ($realty_base > 1) {
	// 		$listB[$i]['cCertifiedMoney'] = floor($listB[$i]['cCertifiedMoney'] / $realty_base) ;
	// 	}
	// }
	##
	$type2 = branch_type2($conn,$listB[$i]);
	for ($j = 0 ; $j < count($branchB) ; $j ++) {
		//第一家仲介
		if ($listB[$i]['cBranchNum'] != '0') {
			if ($branchB[$j]['bId'] == $listB[$i]['cBranchNum'] && $type2['bid'] == $listB[$i]['cBranchNum']) {
				$branchB[$j]['caseMax'] ++ ;
				$branchB[$j]['caseMoney'] += $listB[$i]['cCertifiedMoney'] ;
				continue ;
			}
		}
		##
		
		//第二家仲介
		if ($listB[$i]['cBranchNum1'] != '0') {
			if ($branchB[$j]['bId'] == $listB[$i]['cBranchNum1'] && $type2['bid'] == $listB[$i]['cBranchNum1']) {
				$branchB[$j]['caseMax'] ++ ;
				$branchB[$j]['caseMoney'] += $listB[$i]['cCertifiedMoney'] ;
				continue ;
			}
		}
		##
		
		//第三家仲介
		if ($listB[$i]['cBranchNum2'] != '0') {
			if ($branchB[$j]['bId'] == $listB[$i]['cBranchNum2'] && $type2['bid'] == $listB[$i]['cBranchNum2']) {
				$branchB[$j]['caseMax'] ++ ;
				$branchB[$j]['caseMoney'] += $listB[$i]['cCertifiedMoney'] ;
				continue ;
			}
		}
		##

		
	}
	unset($type2);
}	
##

//取得總部成交案件數
unset($HQ) ;
$sql = '
	SELECT
		*
	FROM
		tHQCase
	WHERE
		SIGN_DATE>="'.substr($from_dateB,0,10).'"
		AND SIGN_DATE<="'.substr($to_dateB,0,10).'"
' ;

$_conn = new first1DB;
$HQ = $_conn->all($sql);
$_conn = null; unset($_conn);
##

//重整"對照"期間仲介店資料
for ($i = 0 ; $i < count($HQ) ; $i ++) {
	for ($j = 0 ; $j < count($branchB) ; $j ++) {
		if ($HQ[$i]['DEP_NAM'] == $branchB[$j]['bStore']) {
			$branchB[$j]['HQ'] ++ ;
			break ;
		}
	}
}
##

$logs->writelog('realtyCompExcel') ;

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經業務資料查詢明細結果");
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('比較表');
##

//設定欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20) ;

$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20) ;
##

//設定文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
##

//寫入日期範圍
$objPHPExcel->setActiveSheetIndex()->mergeCells('A1:D1') ;				// A 仲介
$objPHPExcel->setActiveSheetIndex()->mergeCells('A2:D2') ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$Af_year.'年/'.$Af_month.'月 ~ '.$At_year.'年/'.$At_month.'月') ;
$objPHPExcel->getActiveSheet()->setCellValue('A2','比較組') ;

$objPHPExcel->setActiveSheetIndex()->mergeCells('G1:K1') ;				// B 仲介
$objPHPExcel->setActiveSheetIndex()->mergeCells('G2:K2') ;
$objPHPExcel->getActiveSheet()->setCellValue('G1',$Bf_year.'年/'.$Bf_month.'月 ~ '.$Bt_year.'年/'.$Bt_month.'月') ;
$objPHPExcel->getActiveSheet()->setCellValue('G2','對照組') ;
##

//寫入title資料
$objPHPExcel->getActiveSheet()->setCellValue('A3','店名稱') ;			// A 仲介
$objPHPExcel->getActiveSheet()->setCellValue('B3','履保收入') ;
$objPHPExcel->getActiveSheet()->setCellValue('C3','進案件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('D3','總部成交件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('E3','負責業務') ;
if ($showList < 2) {
	$objPHPExcel->getActiveSheet()->getComment('E3')->getText()->createTextRun('本欄位需經主管授權方能檢視!!') ;
}

$objPHPExcel->getActiveSheet()->setCellValue('G3','店名稱') ;			// B 仲介
$objPHPExcel->getActiveSheet()->setCellValue('H3','履保收入') ;
$objPHPExcel->getActiveSheet()->setCellValue('I3','進案件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('J3','總部成交件數') ;
$objPHPExcel->getActiveSheet()->setCellValue('K3','負責業務') ;
if ($showList < 2) {
	$objPHPExcel->getActiveSheet()->getComment('J3')->getText()->createTextRun('本欄位需經主管授權方能檢視!!') ;
}
##

//寫入各店家資料
$index = 4 ;

for ($i = 0 ; $i < count($branchA) ; $i ++) {							// A 仲介
	$objPHPExcel->getActiveSheet()->setCellValue('A'.($index+$i),$branchA[$i]['brand'].$branchA[$i]['bStore']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($index+$i),$branchA[$i]['caseMoney']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($index+$i),$branchA[$i]['caseMax']) ;
	if ($showList > 1) {		//確認是否擁有檢視總部件數權限
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($index+$i),$branchA[$i]['HQ']) ;
	}
	else {
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($index+$i),'0') ;
	}

	$objPHPExcel->getActiveSheet()->setCellValue('E'.($index+$i),$branchA[$i]['sales']) ;
}

for ($i = 0 ; $i < count($branchB) ; $i ++) {							// B 仲介
	$objPHPExcel->getActiveSheet()->setCellValue('G'.($index+$i),$branchB[$i]['brand'].$branchB[$i]['bStore']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($index+$i),$branchB[$i]['caseMoney']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('I'.($index+$i),$branchB[$i]['caseMax']) ;
	if ($showList > 1) {		//確認是否擁有檢視總部件數權限
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($index+$i),$branchB[$i]['HQ']) ;
	}
	else {
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($index+$i),'0') ;
	}
	$objPHPExcel->getActiveSheet()->setCellValue('K'.($index+$i),$branchB[$i]['sales']) ;
}
##

//若擁有檢視總部件數權限則顯示無法比對之店家案件明細
if ($showList > 1) {
	//新增並指定工作頁
	$objPHPExcel->createSheet() ;
	$objPHPExcel->setActiveSheetIndex(1) ;
	$objPHPExcel->getActiveSheet()->setTitle('無法比對之總部店家');
	##
	
	//寫入title資料
	$objPHPExcel->getActiveSheet()->setCellValue('A1','總部店名稱') ;
	$objPHPExcel->getActiveSheet()->setCellValue('B1','總部店編號') ;
	$objPHPExcel->getActiveSheet()->setCellValue('C1','進案日期') ;
	$objPHPExcel->getActiveSheet()->setCellValue('D1','總部案件編號') ;
	##
	
	//設定文字置中
	$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	##

	//設定欄位寬度
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20) ;
	##
	
	//無法對出之店家資料
	$index = 2 ;
	//for ($i = 0 ; $i < count($branchB) ; $i ++) {
	for ($i = 0 ; $i < count($HQA) ; $i ++) {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($index + $i),$HQA[$i]['DEP_NAM']) ;			// A 仲介
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($index + $i),$HQA[$i]['DEP_ID']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($index + $i),$HQA[$i]['SIGN_DATE']) ;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($index + $i),$HQA[$i]['NOTE_NO']) ;
	}
	##
}
##

//Save Excel 2007 file 保存
$objPHPExcel->setActiveSheetIndex(0) ;
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel) ;
##

//儲存資訊
//$file_name = '業務資訊比較表.xlsx' ;
//$file_path = '/home/httpd/html/'.substr($web_addr,7).'/report/excel/' ;
//$_file = $file_path.$file_name ;
##

//檔案另存
//$objWriter->save($_file);
##

$objWriter->save('php://output');
exit ;


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