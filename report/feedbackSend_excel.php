<?php

require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;
include_once '../includes/maintain/feedBackData.php' ;
// include_once 'feedBackData.php' ;
include_once '../class/getAddress.php' ;
include_once '../class/getBank.php' ;



$bId = $_POST['branch'];
$sId = $_POST['scrivener'];
$sales_year = $_POST['sales_year'];
$sales_season = $_POST['sales_season'];
$invert_result = $_POST['invert_result'];

##

if ($bId) {
		$str .= ' AND a.bId="'.$bId.'" ' ;
}

$sql = '
	SELECT 
		bId,
		(SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
		bStore,
		bCategory,
		bStoreClass,
		bClassBranch
	FROM
		tBranch AS a
	WHERE
		a.bId <> 0 
		'.$str.'
	ORDER BY
		bId
	ASC;
	' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$branch[] = $rs->fields;

	$rs->MoveNext();
}
unset($str);
##
$date_range = '' ;
$contractDate = '' ;
$str = ' AND cas.cCaseStatus IN ("3","4")' ;
if ($sales_year && $sales_season) {	
	switch ($sales_season) {
		case 'S1' : 
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-01-01" AND tra.tBankLoansDate<="'.$sales_year.'-03-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-01-01" AND cBankList<="'.$sales_year.'-03-31"' ;
				$sales_season = ($sales_year-1911).'年度第一季' ;
				break ;
		case 'S2' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-04-01" AND tra.tBankLoansDate<="'.$sales_year.'-06-30"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-04-01" AND cBankList<="'.$sales_year.'-06-30"' ;
				$sales_season = ($sales_year-1911).'年度第二季' ;
				break ;
		case 'S3' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-07-01" AND tra.tBankLoansDate<="'.$sales_year.'-09-30"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-07-01" AND cBankList<="'.$sales_year.'-09-30"' ;
				$sales_season = ($sales_year-1911).'年度第三季' ;
				break ;
		case 'S4' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-10-01" AND tra.tBankLoansDate<="'.$sales_year.'-12-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-10-01" AND cBankList<="'.$sales_year.'-12-31"' ;
				$sales_season = ($sales_year-1911).'年度第四季' ;
				break ;
		default :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-'.$sales_season.'-01" AND tra.tBankLoansDate<="'.$sales_year.'-'.$sales_season.'-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-'.$sales_season.'-01" AND cBankList<="'.$sales_year.'-'.$sales_season.'-31"' ;
				$sales_season = ($sales_year-1911).'年度'.preg_replace("/^0/","",$sales_season).'月份' ;
				break ;
	}
	$str .= ' AND '.$date_range ;
}

if ($sId) {
	$str .= ' AND cs.cScrivener ="'.$sId.'"' ;
}

//取得所有合約銀行活儲帳號
$contractBank = '' ;
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$conBank[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}
$contractBank = implode('","',$conBank) ;
unset($conBank);
##
$sql = '
	SELECT 
		DISTINCT tra.tMemo as cCertifiedId
	FROM
		tBankTrans AS tra
	JOIN
		tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=tra.tMemo
	WHERE
		tra.tObjKind IN ("點交(結案)","解除契約")
		AND tra.tAccount IN ("'.$contractBank.'")
		'.$str.'
	ORDER BY
		tra.tExport_time
	ASC ;
' ;
// echo $sql ; die ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	
	$list[] = $rs->fields;

	$rs->MoveNext();
}

//取出範圍內未收履保費但仍要回饋(有利息)的案件
if ($contractDate){
	$sql = 'SELECT cCertifiedId FROM tContractCase WHERE '.$contractDate ;
}else{
	$sql = 'SELECT cCertifiedId FROM tContractCase WHERE cBankList<>"" ORDER cEndDate ASC ;' ;
}
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	# code...
	$list[] = $rs->fields;
	$rs->MoveNext();
}
$data = $data2 = array();
for ($i=0; $i < count($list); $i++) { 
		$sql = '
		SELECT
			rea.cCertifyId as cCertifiedId,
			rea.cBranchNum as cBranchNum,
			rea.cBranchNum1 as cBranchNum1,
			rea.cBranchNum2 as cBranchNum2,
			cas.cSpCaseFeedBackMoney as cSpCaseFeedBackMoney,
			cas.cCaseFeedBackMoney as cCaseFeedBackMoney,
			cas.cCaseFeedBackMoney1 as cCaseFeedBackMoney1,
			cas.cCaseFeedBackMoney2 as cCaseFeedBackMoney2,
			cas.cFeedbackTarget as cFeedbackTarget,
			cas.cFeedbackTarget1 as cFeedbackTarget1,
			cas.cFeedbackTarget2 as cFeedbackTarget2,
			cas.cCaseFeedback as cCaseFeedback,
			cas.cCaseFeedback1 as cCaseFeedback1,
			cas.cCaseFeedback2 as cCaseFeedback2,
			cs.cScrivener as cScrivener,
			CONCAT("SC", LPAD(cs.cScrivener,4,0)) AS sCode,
			CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
			CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
			CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2
		FROM
			tContractRealestate AS rea
		JOIN 
			tContractScrivener AS cs ON cs.cCertifiedId = rea.cCertifyId
		JOIN
			tContractCase AS cas ON cas.cCertifiedId=rea.cCertifyId
		WHERE
			rea.cCertifyId="'.$list[$i]['cCertifiedId'].'"
	' ;
	
	$rs = $conn->Execute($sql);

	$tmp = getFeedData($rs->fields);
	$data = array_merge($data,$tmp);
	//撈取其他回饋對象
	$tmp2 = getFeedBackMoney($list[$i]['cCertifiedId']);
	if (is_array($tmp2)) {
		$data2 = array_merge($data2,$tmp2);
	}
	##
	
	


	
	// echo $list[$i]['cCertifiedId']."<bR>";
	
	unset($tmp);unset($tmp2);

}

unset($list);


$index = 0;
for ($i = 0 ; $i < count($data) ; $i ++) {

	if ($invert_result=='1') {		//顯示剔除資料
		if ($data[$i]['cCaseFeedback']=='1') {			//不要回饋
			$list[$index] = $data[$i];
			$list[$index]['cat'] = '不回饋';
			$index ++ ;

		}
	}else if ($invert_result=='2'){	//顯示所有資料
				
		$list[$index] = $data[$i];
			
		//顯示 "正常/剔除" title
		if ($data[$i]['cCaseFeedback']=='1') {
			$list[$index]['cat'] = '不回饋';
		}
		else {
			$list[$index]['cat'] = '回饋';
		}
		

		$index ++ ;
	}else {							//顯示正常資料
		if ($data[$i]['cCaseFeedback']=='0') {			//要回饋	
			$list[$index] = $data[$i];	
			$list[$index]['cat'] = '回饋';

			$index ++ ;
		}
	}

	
}
// 
unset($data);
//將其他回饋對象，加進原本的資料陣列
$list2 = array();
for ($i=0; $i < count($data2); $i++) { 


	if ($data2[$i]['fType'] == 1) {
		$tmp =	getScrivenerData($data2[$i]['fStoreId']);
		$tmp[0]['type'] = 'S';
	}elseif ($data2[$i]['fType'] == 2) {
		$tmp =	getBranchData($data2[$i]['fStoreId']);
		$tmp[0]['type'] = 'B';
	}

	$tmp[0]['cCertifiedId'] = $data2[$i]['fCertifiedId'];
	$tmp[0]['cCaseFeedback'] = 0;
	$tmp[0]['cat'] = '回饋';

	$list2 = array_merge($list2,$tmp);




	unset($tmp);
}

unset($data2);
if (is_array($list2)) {
	$list = array_merge($list,$list2);
}
###

// echo $bId."_".$sId;
//同樣地址歸類
for ($i=0; $i < count($list); $i++) { 

	if ($bId != '' ) {
		$code = (int) mb_substr($list[$i]['code'], 2);
		if ($code != $bId) {
						
			 continue;
		}

	}elseif ($sId != '0') {
		$code = (int) mb_substr($list[$i]['code'], 2);
		if ($code != $sId) {
						
			 continue;
		}
	}

	
	
	if ($list[$i]['zip'] && $list[$i]['addr']) {


		$check = checkRepeat($data[$list[$i]['zip'].$list[$i]['addr']],$list[$i]['code'],$list[$i]['type']); //1未建立資料 2 重複 3店編++
		// echo "<bR>".$check."<bR>";
		//有仲介跟地政同個地址，所以拆開來算
		if ($list[$i]['type'] == 'B') { //
			if ($check == 1) {
				$data[$list[$i]['zip'].$list[$i]['addr']]['data'.$list[$i]['type']] = $list[$i];
				$data[$list[$i]['zip'].$list[$i]['addr']]['data'.$list[$i]['type']]['realcode'][(int)substr($list[$i]['code'], 2)] = $list[$i]['code'];
				
				
			}elseif($check == 3){
				
					$data[$list[$i]['zip'].$list[$i]['addr']]['data'.$list[$i]['type']]['realcode'][(int)substr($list[$i]['code'], 2)] = $list[$i]['code'];
			
			}
		}else{
			if ($check == 1) {
				$data[$list[$i]['zip'].$list[$i]['addr']]['data'.$list[$i]['type']] = $list[$i];
				$data[$list[$i]['zip'].$list[$i]['addr']]['data'.$list[$i]['type']]['realcode'][(int)substr($list[$i]['code'], 2)] = $list[$i]['code'];
				
				
			}elseif($check == 3){
				
					$data[$list[$i]['zip'].$list[$i]['addr']]['data'.$list[$i]['type']]['realcode'][(int)substr($list[$i]['code'], 2)] = $list[$i]['code'];
			
				
			}
		}
		


	
	}else{
		$data2[$list[$i]['code']] = $list[$i];
	}


	
}

unset($list);

////為了排序 仲介>地政 編號小到大
if (is_array($data)) {
	foreach ($data as $key => $value) {
	# code...
	
		foreach ($value as $k => $v) {

			ksort($v['realcode']);
			$storeId = implode(';', $v['realcode']);

			$mainStore = current($v['realcode']); //取得第一個值
			$mainid = (int)substr($mainStore, 2);
			$addr = getAddr($v['zip']);
			

			if ($v['type'] =='B') {
				$list[$mainid]['zip']=$v['zip'];
				$list[$mainid]['addr']=$addr.$v['addr'];
				$list[$mainid]['title']=$v['title'];
				$list[$mainid]['storeId']=$storeId;
				$list[$mainid]['note']=$v['note'];

			}else{
				$list2[$mainid]['zip']=$v['zip'];
				$list2[$mainid]['addr']=$addr.$v['addr'];
				$list2[$mainid]['title']=$v['title'];
				$list2[$mainid]['storeId']=$storeId;
				$list2[$mainid]['note']=$v['note'];
			}
			
			
			unset($storId);
			unset($mainid);
			unset($addr);
		}
	}
}



if (is_array($data2)) {
	foreach ($data2 as $key => $value) {
		

		$storeId = $key;
		$mainid = (int)substr($storeId, 2);
		
		$addr = getAddr($value['zip']);

		if ($value['type'] =='B') {
				$list[$mainid]['zip']=$value['zip'];
				$list[$mainid]['addr']=$addr.$value['addr'];
				$list[$mainid]['title']=$value['title'];
				$list[$mainid]['storeId']=$storeId;
				$list[$mainid]['note']=$value['note'];
			
		}else{
				$list2[$mainid]['zip']=$value['zip'];
				$list2[$mainid]['addr']=$addr.$value['addr'];
				$list2[$mainid]['title']=$value['title'];
				$list2[$mainid]['storeId']=$storeId;
				$list2[$mainid]['note']=$value['note'];
		}

		unset($storId);
		unset($mainid);
		unset($addr);
		
	}
}


ksort($list);
ksort($list2);
##

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("回饋金寄送名單");
$objPHPExcel->getProperties()->setDescription("回饋金寄送名單");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
//寫入清單標題列資料
// $objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':B'.$row);
// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':N'.$row)->getFont()->setSize(10);
// $objPHPExcel->getActiveSheet(0)->getStyle('A'.$row.':K'.$row)->getFont()->getColor()->setARGB('00FFFFFF'); 
// $objPHPExcel->getActiveSheet(0)->getStyle('A'.$row.':K'.$row)->getFont()->setBold(true);
$row = 1;
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->getFill()->getStartColor()->setARGB('FCEC6E');
$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'聯絡') ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'地址') ;
$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'收件人') ;
$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'店編') ;
$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'') ;
$row++;

foreach ($list as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$row, $v['zip'],PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$v['addr']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$v['title']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$v['storeId']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$v['note']) ;

	$row++;
}
foreach ($list2 as $k => $v) {
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$row, $v['zip'],PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$v['addr']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$v['title']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$v['storeId']) ;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$v['note']) ;

	$row++;
}
// if (is_array($data)) {
// 	foreach ($data as $key => $value) {
// 	# code...
	
// 		foreach ($value as $k => $v) {

// 			sort($v['realcode']);

// 			$storeId = implode(';', $v['realcode']);
// 			$addr = getAddr($v['zip']);

// 			$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$row, $v['zip'],PHPExcel_Cell_DataType::TYPE_STRING); 
// 			$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$addr.$v['addr']) ;
// 			$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$v['title']) ;
// 			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$storeId) ;
// 			$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$v['note']) ;
// 			$row++;
// 			unset($storId);
// 			unset($addr);
// 		}
// 	}
// }

// if (is_array($data2)) {
// 	foreach ($data2 as $key => $value) {
	
// 		$storeId = $key;
// 		$addr = getAddr($value['zip']);

// 		$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$row, $value['zip'],PHPExcel_Cell_DataType::TYPE_STRING); 
// 		$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$addr.$value['addr']) ;
// 		$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$value['title']) ;
// 		$objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$storeId) ;
// 		$objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$value['note']) ;
// 		$row++;
// 		unset($storId);
// 		unset($addr);

// 		// echo "<pre>";
// 		// 	print_r($value);
// 		// echo "</pre>";
// 		// die;
// 	}
// }



function getAddr($zip)
{
	global $conn;

	$sql= "SELECT zCity,zArea FROM tZipArea WHERE zZip ='".$zip."'";

	$rs = $conn->Execute($sql);

	return $rs->fields['zCity'].$rs->fields['zArea'];
}


$_file = 'feedbackSend.xlsx' ;

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

function checkRepeat($arr,$id,$type){

	if (is_array($arr['data'.$type]['realcode'])) {
			
			
		if (!in_array($id, $arr['data'.$type]['realcode'])) {

			return 3;

		}else{
			return 2;
		}
			

	}else{
		
		return 1;
	}
		
	
	
}
function getFeedData($arr){
	// global $conn;
	$i = 0;
	//仲介(1)

	if ($arr['cFeedbackTarget'] == '1') {

		if ($arr['cBranchNum'] > 0) {
			$tmp = getBranchData($arr['cBranchNum']);
			
			if (is_array($tmp)) { //有N筆情況
				for ($j=0; $j < count($tmp); $j++) { 
					$tmp_arr[$i] = $tmp[$j];
					$tmp_arr[$i]['cCertifiedId'] = $arr['cCertifiedId'] ;
					$tmp_arr[$i]['cCaseFeedback'] = $arr['cCaseFeedback'];
					$tmp_arr[$i]['type'] = 'B';
					$i ++ ;
					
				}


			}
			unset($tmp);

		}
		
	}elseif ($arr['cFeedbackTarget'] == '2') {

		$tmp = getScrivenerData($arr['cScrivener']);

		if (is_array($tmp)) { 
			for ($j=0; $j < count($tmp); $j++) { 
				$tmp_arr[$i] = $tmp[$j];
				$tmp_arr[$i]['cCertifiedId'] = $arr['cCertifiedId'] ;
				$tmp_arr[$i]['cCaseFeedback'] = $arr['cCaseFeedback'];
				$tmp_arr[$i]['type'] = 'S';				
				$i ++ ;
				
			}
		}
		unset($tmp);
	}

	//特殊回饋
	if ($arr['cSpCaseFeedBackMoney'] > 0 && (($arr['cBrand']!=2 && $arr['cBrand']!=49 && $arr['cBrand']!=1)||($arr['cBrand1']!=2 && $arr['cBrand1']!=49 && $arr['cBrand1']!=1)||($arr['cBrand2']!=2 && $arr['cBrand2']!=49 && $arr['cBrand2']!=1))) {
			$tmp = getScrivenerData($arr['cScrivener']);
			if (is_array($tmp)) { //有N筆情況
				for ($j=0; $j < count($tmp); $j++) { 
					$tmp_arr[$i] = $tmp[$j];

					$tmp_arr[$i]['cCertifiedId'] = $arr['cCertifiedId'] ;
					$tmp_arr[$i]['cCaseFeedback'] = 0;
					$tmp_arr[$i]['type'] = 'S';
					$i ++ ;
					
				}
			}
			unset($tmp);
			
	}
	//第二間回饋
	if ($arr['cFeedbackTarget1'] == '1') {	
		if ($arr['cBranchNum1'] > 0) {	
			$tmp = getBranchData($arr['cBranchNum1']);
			if (is_array($tmp)) { //有N筆情況
				for ($j=0; $j < count($tmp); $j++) { 
					$tmp_arr[$i] = $tmp[$j];

					// $tmp_arr[$i]['storId'] = $arr['cBranchNum1'] ;
					// $tmp_arr[$i]['storCode'] = $arr['bCode1'] ;
					$tmp_arr[$i]['cCertifiedId'] = $arr['cCertifiedId'] ;
					$tmp_arr[$i]['cCaseFeedback'] = $arr['cCaseFeedback1'];
					$tmp_arr[$i]['type'] = 'B';
					$i ++ ;
					
				}
			}
			unset($tmp);
		}
	}elseif ($arr['cFeedbackTarget1'] == '2') {
		$tmp = getScrivenerData($arr['cScrivener']);
		if (is_array($tmp)) { //有N筆情況
			for ($j=0; $j < count($tmp); $j++) { 
				$tmp_arr[$i] = $tmp[$j];

				// $tmp_arr[$i]['storId'] = $arr['cScrivener'] ;
				// $tmp_arr[$i]['storCode'] = $arr['sCode'] ;
				$tmp_arr[$i]['cCertifiedId'] = $arr['cCertifiedId'] ;
				$tmp_arr[$i]['cCaseFeedback'] = $arr['cCaseFeedback1'];
				$tmp_arr[$i]['type'] = 'S';				
				$i ++ ;
				
			}
		}
		unset($tmp);
		
	}

	if ($arr['cFeedbackTarget2'] == '1') {
		if ($arr['cBranchNum2'] > 0) {
			
			$tmp = getBranchData($arr['cBranchNum2']);
			if (is_array($tmp)) {
				for ($j=0; $j < count($tmp); $j++) { 
					$tmp_arr[$i] = $tmp[$j];
					// $tmp_arr[$i]['storId'] = $arr['cBranchNum2'] ;
					// $tmp_arr[$i]['storCode'] = $arr['bCode2'] ;
					$tmp_arr[$i]['cCertifiedId'] = $arr['cCertifiedId'] ;
					$tmp_arr[$i]['cCaseFeedback'] = $arr['cCaseFeedback2'];
					$tmp_arr[$i]['type'] = 'B';
					$i ++ ;
					
				}
			}
			unset($tmp);
		}
	}elseif ($arr['cFeedbackTarget2'] == '2') {
		$tmp = getScrivenerData($arr['cScrivener']);
		if (is_array($tmp)) {
			for ($j=0; $j < count($tmp); $j++) { 
				$tmp_arr[$i] = $tmp[$j];
				// $tmp_arr[$i]['storId'] = $arr['cScrivener'] ;
				// $tmp_arr[$i]['storCode'] = $arr['sCode'] ;
				$tmp_arr[$i]['cCertifiedId'] = $arr['cCertifiedId'] ;
				$tmp_arr[$i]['cCaseFeedback'] = $arr['cCaseFeedback2'];
				$tmp_arr[$i]['type'] = 'S';				
				$i ++ ;
				
			}
		}
		unset($tmp);
		
	}

	return $tmp_arr;
}




?>