<?php
include_once '../../../openadodb.php' ;

/* 活動日期範圍 */
##測試用##
// $fromDate = '2016-10-01 00:00:00' ;
// $toDate = '2016-10-31 23:59:59' ;
##########

##########
// 第1次日期(2016-11-02)
// $fromDate = '2016-10-01 00:00:00' ;
// $toDate = '2016-10-31 23:59:59' ;
// #############
// // 第2次日期(2016-12-02)
// $fromDate = '2016-10-01 00:00:00' ;
// $toDate = '2016-11-30 23:59:59' ;
// #############
// // 第3次日期(2016-01-04)
// $fromDate = '2016-10-01 00:00:00' ;
// $toDate = '2016-12-31 23:59:59' ;
// #############
// // 第4次日期(2017-02-08)
$fromDate = '2016-10-01 00:00:00' ;
$toDate = '2017-02-05 23:59:59' ;
// #############



/*****************/

//確認加盟店類型
Function checkCategory($bId,$con) {
	$ct = false ;
	
	if ($bId > 0) {
		$sql = 'SELECT * FROM tBranch WHERE bId="'.$bId.'";' ;
		$rel = $con->Execute($sql) ;
		
		//確認案件為台灣房屋加盟店
		if ($rel->fields['bBrand'] == '1') {		//台灣房屋
		//if (($rel->fields['bBrand'] == '49') && ($rel->fields['bCategory'] == '1')) {		//優美地產
			$ct = true ;
		}
		##
	}
	
	return $ct ;
}
##

//$sql = 'SELECT cDealId,cSignDate FROM tContractCase WHERE cDealId<>"" AND cSignDate>="'.$fromDate.'" AND cSignDate<="'.$toDate.'" ORDER BY cSignDate,cDealId ASC;' ;

//
$sql = '
	SELECT 
		cas.cCertifiedId as cCertifiedId,
		cas.cDealId as cDealId,
		cas.cSignDate as cSignDate,
		rea.cBrand,
		rea.cBrand1,
		rea.cBrand2,
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		rea.cServiceTarget as cServiceTarget,
		rea.cServiceTarget1 as cServiceTarget1,
		rea.cServiceTarget2 as cServiceTarget2,
		own.cIdentifyId as ownerId,
		own.cName as ownerName,
		buy.cIdentifyId as buyerId,
		buy.cName as buyerName
	FROM 
		tContractCase AS cas
	JOIN
		tContractRealestate AS rea ON rea.cCertifyId = cas.cCertifiedId
	LEFT JOIN
		tContractOwner AS own ON own.cCertifiedId = cas.cCertifiedId
	LEFT JOIN
		tContractBuyer AS buy ON buy.cCertifiedId = cas.cCertifiedId
	
	WHERE 
		cas.cSignDate >= "'.$fromDate.'" 
		AND cas.cSignDate <= "'.$toDate.'" 
		AND cas.cCaseStatus IN ("2","3")
		AND (rea.cBrand = 1 OR rea.cBrand1 = 1 OR rea.cBrand2 = 1)
	GROUP BY cas.cCertifiedId
	ORDER BY 
		cas.cSignDate,cas.cDealId

	ASC;
' ;
##
// die($sql);
//取得主要資料

$rs = $conn->Execute($sql) ;
$i = 0 ;
function DateChange($val)
{
	$tmp = explode('-', $val);

	$val = $tmp[0]."/".(int)$tmp[1]."/".$tmp[2];
	

	
	return $val;
}
while (!$rs->EOF) {

	$rs->fields['cSignDate'] = substr($rs->fields['cSignDate'],0,10) ;
	$rs->fields['cSignDate'] = DateChange($rs->fields['cSignDate']);
	


	$rs->fields['ownerId'] = strtoupper($rs->fields['ownerId']) ;
	$rs->fields['ownerName'] = strtoupper($rs->fields['ownerName']) ;
	$rs->fields['buyerId'] = strtoupper($rs->fields['buyerId']) ;
	$rs->fields['buyerName'] = strtoupper($rs->fields['buyerName']) ;

	// $city = $rs->fields['cCity'] ;
	// $area = $rs->fields['cArea'] ;
	// $addr = str_replace(',','，',$rs->fields['cAddr']) ;
	// $addr = preg_replace("/$city/","",$addr) ;
	// $addr = str_replace($area,"",$addr) ;

	// $tmpAddr[0] = $city.$area.$addr;


	// $rs->fields['cAddr'] =  implode('_', $tmpAddr);
	// unset($city,$area,$addr) ;
	$rs->fields['cAddr'] = getAddr($rs->fields['cCertifiedId']);

	/*$branch = array($rs->fields['cBranchNum'], $rs->fields['cBranchNum1'], $rs->fields['cBranchNum2']) ;
	
	$fg = 0 ;
	foreach ($branch as $k => $v) {
		if (checkCategory($v,$conn)) {
			$fg ++ ;

		}
	}*/
	$fg = 0 ;
	if ($rs->fields['cBrand'] == '1' ) {$fg ++ ;}
		
	if ($rs->fields['cBrand1'] == '1') {$fg ++ ;}

	if ($rs->fields['cBrand2'] == '1') {$fg ++ ;}


	if ($fg > 0) {
		
		$list[$i] = $rs->fields;
		$list[$i]['type'] = checkType($conn,$rs->fields);
		$list[$i]['fg'] = $fg;
		$i++;
	}
	
	
	
	$rs->MoveNext() ;
}

function getAddr($cId){

	global $conn;

	$sql = "SELECT 
				(SELECT CONCAT(zCity,zArea) AS country FROM tZipArea WHERE zZip =cZip) AS country,
				cAddr,
				cLevelNow
			FROM
				tContractProperty
			WHERE
				cCertifiedId ='".$cId."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$floor = '';
		if ($cId == '005077128') {
			// echo $cId;
			$floor = $rs->fields['cLevelNow']."樓";
		}

		$arr[] = $rs->fields['country'].$rs->fields['cAddr'].$floor;

		$rs->MoveNext();
	}

	if (is_array($arr)) {
		return implode('_', $arr);
	}else{
		return false;
	}
}

function checkType($conn,$arr)
{
	$type = 0;
	if ($arr['cBrand'] == 1 && $arr['cBranchNum'] > 0) {

		if ($arr['cServiceTarget'] == 1) {
			
			return 5; //5 2+3(賣+買)
		}else {
			$type = $arr['cServiceTarget'];
		}
	}

	if ($arr['cBrand1'] == 1 && $arr['cBranchNum1'] > 0) {

		if ($arr['cServiceTarget1'] == 1) {			
			return 5; //5 2+3(賣+買)
		}else {
			$type = $arr['cServiceTarget1'];
		}
	}

	$type = $arr['cServiceTarget']+$arr['cServiceTarget1'];

	
	return $type;

	//
}
##
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;
//print_r($list) ; exit ;
//查詢多組買賣方身分證字號
for ($i = 0 ; $i < count($list) ; $i ++) {

	

	$sql = 'SELECT cIdentity,cIdentifyId,cName FROM tContractOthers WHERE cCertifiedId="'.$list[$i]['cCertifiedId'].'" ORDER BY cId ASC;' ;
	//echo 'sql='.$sql."<br>\n" ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		if ($rs->fields['cIdentity'] == '1' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 3)) {
			$list[$i]['buyerId'] .= '_'.strtoupper($rs->fields['cIdentifyId']) ;
			$list[$i]['buyerName'] .= '_'.$rs->fields['cName'] ;
		}
		else if ($rs->fields['cIdentity'] == '2' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 2)) {
			$list[$i]['ownerId'] .= '_'.strtoupper($rs->fields['cIdentifyId']) ;
			$list[$i]['ownerName'] .= '_'.$rs->fields['cName'] ;
		}
		$rs->MoveNext() ;
	}
}
##

//print_r($list) ; exit ;
$fh = fopen('audi_'.date("Ymd").'.csv','w') ;
fwrite($fh,'保證號碼,簽約日期,委託書編號,買方身份證字號,賣方身份證字號,物件地址,買方姓名,賣方姓名'."\r\n") ;
for ($i = 0 ; $i < count($list) ; $i ++) {
	fwrite($fh,$list[$i]['cCertifiedId'].'_,'.$list[$i]['cSignDate'].','.$list[$i]['cDealId'].','.$list[$i]['buyerId'].','.$list[$i]['ownerId'].','.$list[$i]['cAddr'].','.$list[$i]['buyerName'].','.$list[$i]['ownerName']."\r\n") ;
}
fclose($fh) ;

echo "Done!!<br>\n(".date("Y-m-d G:i:s").')' ;
?>