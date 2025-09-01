<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;
##

##
$cat = $_POST['cat'];
$city = $_POST['city'];
$area = $_POST['area'];
$dateStart = ($_POST['Af_year']+1911)."-".$_POST['Af_month']."-01 00:00:00";
$dateEnd = ($_POST['At_year']+1911)."-".$_POST['At_month']."-31 00:00:00";
$twhgBranch = $_POST['twhgBranch'];
$twhgBranch2 = $_POST['twhgBranch2'];
$other = $_POST['other'];
$statusOff = $_POST['statusOff'];
//取得區域資訊
if ($city) {
	$sql = 'SELECT * FROM tZipArea WHERE zCity="'.$city.'"' ;
	if ($area) {
		$sql .= ' AND zArea="'.$area.'"' ;	
	}
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$zip[] = $rs->fields['zZip'] ;

		$rs->MoveNext();
	}
	
	$zip_str = '' ;
	if ($zip) {
		$zip_str = implode('","',$zip) ;
		$zip_str = '"'.$zip_str.'"' ;
	}
}

//取得區域郵遞區號字串
$zip_sql = '' ;

if ($zip_str) {
	if ($cat =='scrivener') {
		$zip_sql = ' AND sCpZip1 IN ('.$zip_str.')' ;
	}else{
		$zip_sql = ' AND bZip IN ('.$zip_str.')' ;
	}
	
}
###


$sql = "SELECT
				cc.cCertifiedId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cs.cScrivener
			FROM
				tContractCase AS cc
			LEFT JOIN
				tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId 
			LEFT JOIN 
				tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
			WHERE
				cc.cCertifiedId<>'' AND cc.cCertifiedId !='005030342' AND cc.cCaseStatus<>'8'
				AND cc.cSignDate >='".$dateStart."' AND cc.cSignDate <= '".$dateEnd."'
				".$str."";

$rs = $conn->Execute($sql);
while (!$rs->EOF) {

	if ($cat == 'branch') {
		$dataCount[$rs->fields['cBranchNum']]++;

		if ($rs->fields['cBranchNum1'] > 0) {
			$dataCount[$rs->fields['cBranchNum1']]++;
		}
		if ($rs->fields['cBranchNum2'] > 0) {
			$dataCount[$rs->fields['cBranchNum2']]++;
		}
		
	}else{
		$dataCount[$rs->fields['cScrivener']]++;
	}
	


	$rs->MoveNext();
}

##


#########
if ($cat == 'branch') {
	if ($statusOff == '1') $statusOff = ' AND bStatus="1" ' ; //是否過濾關掉
	$time_str =' AND bCreat_time <="'.$dateEnd.'"'; //時間點
	//仲介類型
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

	

	$sql = "SELECT
				bId AS id,
				CONCAT((SELECT bCode FROM `tBrand` c WHERE c.bId = bBrand ),LPAD(bId,5,'0')) as Code,
				CONCAT((SELECT bName FROM `tBrand` c WHERE c.bId = bBrand),bStore) as Store				
			FROM
				tBranch WHERE bId<>'0' ".$zip_sql.$statusOff.$time_str.$str." ORDER BY bId ASC";
}else{
	if ($statusOff == '1') $statusOff = ' AND sStatus="1" ' ;
	$time_str =' AND sCreat_time <="'.$dateEnd.'"';
	$exceptbId = array(632, 575,552,620,411,224) ;//地政士(排除奇怪的:632=業務專用 575=陳政祺 552=王泰翔 620=吳效承 411=吳)
	$sql = "SELECT
				sId AS id,
				CONCAT('SC',LPAD(sId,4,'0')) as Code,
				sOffice as Store,
				sName,
				sCategory,
				sBrand as sBrand
			FROM 
				tScrivener WHERE sId<>'0' ".$zip_sql.$statusOff.$time_str." AND sId NOT IN(".implode(',', $exceptbId).") ORDER BY sId ASC";
}


$rs = $conn->Execute($sql);
//合作仲介品牌

while (!$rs->EOF) {
	if ($dataCount[$rs->fields['id']] == '') {
		
		
		$store[$rs->fields['id']]['sales'] = getSales($cat,$rs->fields['id']);
		$store[$rs->fields['id']]['count'] = $dataCount[$rs->fields['id']];
		if ($cat == 'scrivener') {
			$store[$rs->fields['id']]['name'] = $rs->fields['sName']."(".$rs->fields['Store'].")";
			$store[$rs->fields['id']]['sBrand'] = CategoryScrinver($rs->fields['sBrand']);
			$store[$rs->fields['id']]['category'] = CategoryConvert($rs->fields['sCategory']) ;

			unset($tmp);unset($brand);
		}else{
			$store[$rs->fields['id']]['name'] = $rs->fields['Store'];
		}
	}
		
		// $list[$rs->fields['id']]['name'] = "(".$rs->fields['Code'].")".$rs->fields['Store'];
		// $list[$rs->fields['id']]['sales'] = getSales($cat,$rs->fields['id']);
	$rs->MoveNext();
}

function CategoryScrinver($arr){

	$ss = '';
	$tmp = explode(',', $arr);

	for ($i=0; $i < count($tmp); $i++) { 
		if ($tmp[$i] == 2) {
			$ss[]= '非仲介成交';
		}elseif ($tmp[$i] == 1) {
			$ss[]= '台灣房屋';
		}elseif ($tmp[$i] == 49) {
			$ss[]= '優美地產';
		}
	}

	rsort($ss);
	return @implode(',', $ss);
	// $menu_brand = array(2 => '非仲介成交',1=> '台灣房屋',49=>'優美地產');
}

function getBrand($id){
	global $conn;
	$sql = "SELECT bName FROM tBrand WHERE bId ='".$id."'";

	$rs = $conn->Execute($sql);

	return $rs->fields['bName'];

}
function getSales($cat,$id){
	global $conn;

	if ($cat == 'branch') {
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS name FROM tBranchSales WHERE bBranch ='".$id."'";
	}else{
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS name FROM tScrivenerSales WHERE sScrivener ='".$id."'";
	}

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['name'];


		$rs->MoveNext();
	}
	
	return @implode(',', $tmp);
}
//加盟直營
Function CategoryConvert($vv='') {
	if ($vv == '1') $vv = '加盟' ;
	else if ($vv == '2') $vv = '直營' ;
	else if ($vv == '3') $vv = '非仲介成交' ;
	
	return $vv ;
}
##


$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("未進案名單");
$objPHPExcel->getProperties()->setDescription("未進案名單");
##

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('未進案名單');
##

//設定欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20) ;

##

//設定文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
##

//寫入日期範圍
$objPHPExcel->setActiveSheetIndex()->mergeCells('A1:D1') ;				// A 仲介
$objPHPExcel->setActiveSheetIndex()->mergeCells('A2:D2') ;
$objPHPExcel->getActiveSheet()->setCellValue('A1',$_POST['Af_year'].'年/'.$_POST['Af_month'].'月 ~ '.$_POST['At_year'].'年/'.$_POST['At_month'].'月') ;

##

//寫入title資料
$objPHPExcel->getActiveSheet()->setCellValue('A3','店名稱') ;			// A 仲介
$objPHPExcel->getActiveSheet()->setCellValue('B3','負責業務') ;
if ($cat == 'scrivener') {
	$objPHPExcel->getActiveSheet()->setCellValue('C3','合作品牌') ;
	$objPHPExcel->getActiveSheet()->setCellValue('D3','備註') ;
}


//寫入各店家資料
$index = 4 ;

foreach ($store as $k => $v) {

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$index,$v['name']) ;	
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$index,$v['sales']) ;
	if ($cat == 'scrivener') {
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$index,$v['sBrand']) ;
		if ($v['category'] == '直營') {
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$index,$v['category']) ;
		}else{
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$index,'') ;
		}
		
	}

	$index++;
}

##


$_file = iconv('UTF-8', 'BIG5', '未進案統計表') ;
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
?>