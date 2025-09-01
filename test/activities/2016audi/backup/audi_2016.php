<?php
include_once '../openadodb.php' ;

/* 活動日期範圍 */
// 2/7 ~ 2/28(二月份)
$fromDate = '2014-02-07 00:00:00' ;
//$toDate = '2014-02-28 23:59:59' ;
##

// 3/1 ~ 3/31(三月份)
//$fromDate = '2014-03-01 00:00:00' ;
//$toDate = '2014-03-31 23:59:59' ;
##

//	4/1 ~ 4/30(四月份)
//$fromDate = '2014-04-01 00:00:00' ;
//$toDate = '2014-04-25 23:59:59' ;
//$toDate = '2014-04-30 23:59:59' ;
##

// 5/1 ~ 5/11(五月份)
//$fromDate = '2014-05-01 00:00:00' ;
$toDate = '2014-05-11 23:59:59' ;
##
/*****************/

//確認加盟店類型
Function checkCategory($bId,$con) {
	$ct = false ;
	
	if ($bId > 0) {
		$sql = 'SELECT * FROM tBranch WHERE bId="'.$bId.'";' ;
		$rel = $con->Execute($sql) ;
		
		//確認案件為台灣房屋加盟店
		if (($rel->fields['bBrand'] == '1') && ($rel->fields['bCategory'] == '1')) {		//台灣房屋
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
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		own.cIdentifyId as ownerId,
		own.cName as ownerName,
		buy.cIdentifyId as buyerId,
		buy.cName as buyerName,
		pro.cZip as cZip,
		(SELECT zCity FROM tZipArea WHERE zZip=pro.cZip) as cCity,
		(SELECT zArea FROM tZipArea WHERE zZip=pro.cZip) as cArea,
		pro.cAddr as cAddr
	FROM 
		tContractCase AS cas
	JOIN
		tContractRealestate AS rea ON rea.cCertifyId = cas.cCertifiedId
	LEFT JOIN
		tContractOwner AS own ON own.cCertifiedId = cas.cCertifiedId
	LEFT JOIN
		tContractBuyer AS buy ON buy.cCertifiedId = cas.cCertifiedId
	LEFT JOIN
		tContractProperty AS pro ON pro.cCertifiedId = cas.cCertifiedId
	WHERE 
		cas.cSignDate >= "'.$fromDate.'" 
		AND cas.cSignDate <= "'.$toDate.'" 
		AND cas.cCaseStatus NOT IN ("2","3")
	ORDER BY 
		cas.cSignDate,cas.cDealId
	ASC;
' ;
##

//取得主要資料
//echo $sql ;
$rs = $conn->Execute($sql) ;
$i = 0 ;
while (!$rs->EOF) {
	$branch = array($rs->fields['cBranchNum'], $rs->fields['cBranchNum1'], $rs->fields['cBranchNum2']) ;
	
	$fg = 0 ;
	foreach ($branch as $k => $v) {
		if (checkCategory($v,$conn)) {
			$fg ++ ;
		}
	}
	
	if ($fg > 0) {
		$tmp = array() ;
		$tmp = explode(",",$rs->fields['cDealId']) ;
		
		//若有多組"委託書編號"時，切開增加
		foreach ($tmp as $k => $v) {
			$list[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'] ;
			$list[$i]['cSignDate'] = substr($rs->fields['cSignDate'],0,10) ;
			$list[$i]['cDealId'] = $v ;
			$list[$i]['ownerId'] = strtoupper($rs->fields['ownerId']) ;
			//$list[$i]['ownerName'] = strtoupper(iconv('utf-8','big5',$rs->fields['ownerName'])) ;
			$list[$i]['ownerName'] = strtoupper($rs->fields['ownerName']) ;
			$list[$i]['buyerId'] = strtoupper($rs->fields['buyerId']) ;
			$list[$i]['buyerName'] = strtoupper($rs->fields['buyerName']) ;
			$list[$i]['cZip'] = $rs->fields['cZip'] ;
			$city = $rs->fields['cCity'] ;
			$area = $rs->fields['cArea'] ;
			$addr = str_replace(',','，',$rs->fields['cAddr']) ;
			
			$addr = preg_replace("/$city/","",$addr) ;
			//$addr = preg_replace("/$area/","",$addr) ;
			$addr = str_replace($area,"",$addr) ;
			$list[$i]['cAddr'] = $city.$area.$addr ;
			
			unset($city,$area,$addr) ;
			$i ++ ;
		}
		##
		
		unset($tmp) ;
	}
	
	$rs->MoveNext() ;
}
##
//print_r($list) ; exit ;
//查詢多組買賣方身分證字號
for ($i = 0 ; $i < count($list) ; $i ++) {
	$sql = 'SELECT cIdentity,cIdentifyId,cName FROM tContractOthers WHERE cCertifiedId="'.$list[$i]['cCertifiedId'].'" ORDER BY cId ASC;' ;
	//echo 'sql='.$sql."<br>\n" ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		if ($rs->fields['cIdentity'] == '1') {
			$list[$i]['buyerId'] .= '_'.strtoupper($rs->fields['cIdentifyId']) ;
			$list[$i]['buyerName'] .= '_'.$rs->fields['cName'] ;
		}
		else if ($rs->fields['cIdentity'] == '2') {
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