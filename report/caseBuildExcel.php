<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../report/getBranchType.php';

$_POST = escapeStr($_POST) ;
$sSignDate = $_POST['date_start_y']."-".$_POST['date_start_m']."-01";
$eSignDate = $_POST['date_end_y']."-".$_POST['date_end_m']."-31";
$city = $_POST['zipC']; //zipC[]
$area = $_POST['zipA'];
$m1 = $_POST['date_start_m'];
$m2 = $_POST['date_end_m'];
$y1 = $_POST['date_start_y'];
$y2 = $_POST['date_end_y'];
##


$ss = $source ;
if (!$ss) $ss = '永慶、信義、台灣' ;
$ss = '（'.$ss.'）' ;
##

//產品分類
$ptype = array('土地/廠辦','大樓/華廈','套房','透天','公寓','店面','其他') ;


//總價分類
$priceList = array('1000萬以下','1000萬-1500萬','1500萬-2000萬','2000萬-2500萬','2500萬-3000萬','3000萬以上') ;


//取得所在縣市的鄉鎮市區
$dist = array() ;
//區域## (有可能會有地區跟縣市同時存在)
if ($city) {
	for ($i=0; $i < count($city); $i++) { 
		
		$dist[] = $city[$i];
	}
	
	if ($area) {
		$sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip IN(".@implode(',', $area).")";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$dist[] = $rs->fields['zCity'].$rs->fields['zArea'];

			$rs->MoveNext();
		}

	}

}else if($area){
	$sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip IN(".@implode(',', $area).")";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$dist[] = $rs->fields['zCity'].$rs->fields['zArea'];

			$rs->MoveNext();
		}


}
##

die;

$Firstdata = getCaseData('objkind',$sSignDate,$eSignDate);
$Firstdata2 = getCaseData('money',$sSignDate,$eSignDate);
$Firstdata4 = getCaseData('count',$sSignDate,$eSignDate);
//$cat == 'money'





function getCaseData($cat,$sSignDate,$eSignDate){

	global $conn;

	$query = 'cas.cCaseStatus<>"8" AND cas.cCertifiedId<>"" ';
	// 搜尋條件-簽約日期
	if ($sSignDate) {
		
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
		//$query .= ' tra.tExport_time>="'.$sSignDate.' 00:00:00" ' ;
	}
	if ($eSignDate) {
		
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;

	}

	if ($city) {
		//縣市
		for ($i=0; $i < count($city); $i++) { 
			$tmp[] = "'".$city[$i]."'";
			$col_Area[$city[$i]] = $city[$i];
		}
		if ($area) { //有區域的話
			

			$query .= " AND (zip.zCity IN(".@implode(',', $tmp).") OR zip.zZip IN(".@implode(',', $area)."))";
		}else{
			$query .= " AND zip.zCity IN(".@implode(',', $tmp).")";
		}
	}else if($area){
		$queyr .= " AND zip.zZip IN(".@implode(',', $area).")";

	}
	unset($tmp);
	if ($query) { $query = ' WHERE '.$query ; }

	$sql ='
		SELECT 
			cas.cCertifiedId as cCertifiedId, 
			cas.cSignDate as cSignDate, 
			inc.cTotalMoney as cTotalMoney, 
			inc.cCertifiedMoney as cCertifiedMoney, 
			pro.cAddr as cAddr, 
			pro.cZip as cZip,
			pro.cTownHouse,
			pro.cLevelHighter, 
			pro.cLevelNow, 
			zip.zCity as zCity,
			zip.zArea as zArea,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			rea.cBranchNum as branch,
			rea.cBranchNum1 as branch1,
			rea.cBranchNum2 as branch2
		FROM 
			tContractCase AS cas 
		LEFT JOIN 
			tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
		LEFT JOIN 
			tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
		LEFT JOIN
			tZipArea AS zip ON zip.zZip=pro.cZip
		'.$query.'  AND pro.cItem = 0 
		GROUP BY
			cas.cCertifiedId
		ORDER BY 
			cas.cSignDate ASC' ;

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$month = substr($rs->fields['cSignDate'], 0,7);  //月份
		$tmpObj = '';

		// $tmpArea = '';
		
		//物件類型
		if ($cat == 'objkind') {
			
			$Totalfloor = checkSp(TxtToNum($rs->fields['cLevelHighter']));
			$floor = checkSp(TxtToNum($rs->fields['cLevelNow']));

			//判斷樓層是否KEY反
			if (is_numeric($Totalfloor) && is_numeric($floor) ) { 
				if ($Totalfloor < $floor) {
					$tmpV = $Totalfloor;
					$Totalfloor = $floor;
					$floor = $tmpV;
					unset($tmpV);
				}
			}elseif (preg_match("/^.*-.*$/isu", $Totalfloor)) {
				$tmpV = $Totalfloor;
				$Totalfloor = $floor;
				$floor = $tmpV;
				unset($tmpV);
			}

			
			##

			if ($Totalfloor == '' && $floor == '') {
				$tmpObj = '土地/廠辦';
			}else if ($Totalfloor >= 11 ) {
				$tmpObj = '大樓/華廈';
			}elseif ($Totalfloor >= 6 && $Totalfloor <= 10) {
				$tmpObj = '大樓/華廈';
			}else {
				if (checkProperty($floor,$Totalfloor,$rs->fields['cTownHouse'])) {
					$tmpObj = '透天';
				}else{
					$tmpObj = '公寓';
				}
			}
		}else if($cat == 'money'){
			if ($rs->fields['cTotalMoney'] > 30000000) {  //3000萬以上
				$tmpObj = '3000萬以上';
			}else if ($rs->fields['cTotalMoney'] > 25000000 && $rs->fields['cTotalMoney'] <= 30000000) { //2500萬-3000萬
				$tmpObj = '2500萬-3000萬';
			}else if ($rs->fields['cTotalMoney'] > 20000000 && $rs->fields['cTotalMoney'] <= 25000000) { // 2000萬-2500萬
				$tmpObj = '2000萬-2500萬';
			}elseif ($rs->fields['cTotalMoney'] >15000000 && $rs->fields['cTotalMoney'] <= 20000000) { // 1500~2000萬(含) 4
				$tmpObj = '1500~2000萬(含)';
			}elseif ($rs->fields['cTotalMoney'] >10000000 && $rs->fields['cTotalMoney'] <= 15000000) { // 1000~1500萬(含) 3
				$tmpObj = '1000~1500萬(含)';
			}else{ //1000萬以下
				$tmpObj = '1000萬以下';
			}
		}else{
			$tmpObj = '數量';
		}
		##地區
		if ($city) {
			$data[$month][$tmpObj][$rs->fields['zCity']]++;
			if ($area) {
				$data[$month][$tmpObj][$rs->fields['zCity'].$rs->fields['zArea']]++;
			}
		}else if($area){
			$data[$month][$tmpObj][$rs->fields['zCity'].$rs->fields['zArea']]++;
		}else{
			$data[$month][$tmpObj]++;
		}

		

		unset($month);unset($tmpObj);
		$rs->MoveNext();
	}
}
?>
