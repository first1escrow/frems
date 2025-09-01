<?php
include_once 'opendb.php' ;

//
Function getBranchName($sId) {
	global $link ;
	
	$sql = "
		SELECT
			bStore,
			(SELECT bName FROM tBrand WHERE bId=a.bBrand) as bName
		FROM
			tBranch AS a
		WHERE
			bId='".$sId."'
	;" ;
	$rel = mysql_query($sql,$link) ;
	$tmp = mysql_fetch_array($rel) ;
	
	return $tmp['bName'].$tmp['bStore'] ;
}
##

//定義資格條件
$act = '2015bigdata' ;		//大數據領航家抽獎20150520~20150808
$cSignDateF = '2015-05-20 00:00:00' ;
$cSignDateT = '2015-08-08 18:00:00' ;
##

echo date("Y-m-d H:i:s")." 開始撈取資料!!\r\n" ;
echo "==================================================\r\n" ;

//撈出所有符合資格的保證號碼與店編號
$sql = "
	SELECT
		a.cSignDate,
		d.zCity as cCity,
		d.zArea as cArea,
		c.cAddr,
		b.*
	FROM
		tContractCase AS a
	JOIN
		tContractRealestate AS b ON a.cCertifiedId=b.cCertifyId
	JOIN
		tContractProperty AS c ON a.cCertifiedId=c.cCertifiedId
	LEFT JOIN
		tZipArea AS d ON d.zZip=c.cZip
	WHERE
		a.cSignDate >= '".$cSignDateF."'
		AND a.cSignDate <= '".$cSignDateT."'
		AND a.cCaseStatus IN (2,3)
		AND (b.cBrand IN (1) OR b.cBrand1 IN (1) OR b.cBrand2 IN (1))
;" ;
$rel = mysql_query($sql,$link) ;
$list = array() ;
$i = 0 ;
while ($tmp = mysql_fetch_array($rel)) {	
	if ($tmp['cBrand'] != '0') {
		$list[$i]['cCertifiedId'] = $tmp['cCertifyId'] ;
		$list[$i]['cSignDate'] = $tmp['cSignDate'] ;
		$list[$i]['cCity'] = $tmp['cCity'] ;
		$list[$i]['cArea'] = $tmp['cArea'] ;
		$list[$i]['cAddr'] = $tmp['cAddr'] ;
		
		$list[$i]['cBrand'] = $tmp['cBrand'] ;
		$list[$i]['cBranchNum'] = $tmp['cBranchNum'] ;
		$list[$i]['cBranchName'] = getBranchName($list[$i]['cBranchNum']) ;
		$list[$i]['cServiceTarget'] = $tmp['cServiceTarget'] ;
		
		$i ++ ;
	}
	
	if ($tmp['cBrand1'] != '0') {
		$list[$i]['cCertifiedId'] = $tmp['cCertifyId'] ;
		$list[$i]['cSignDate'] = $tmp['cSignDate'] ;
		$list[$i]['cCity'] = $tmp['cCity'] ;
		$list[$i]['cArea'] = $tmp['cArea'] ;
		$list[$i]['cAddr'] = $tmp['cAddr'] ;
		
		$list[$i]['cBrand'] = $tmp['cBrand1'] ;
		$list[$i]['cBranchNum'] = $tmp['cBranchNum1'] ;
		$list[$i]['cBranchName'] = getBranchName($list[$i]['cBranchNum']) ;
		$list[$i]['cServiceTarget'] = $tmp['cServiceTarget1'] ;
		
		$i ++ ;
	}
	
	if ($tmp['cBrand2'] != '0') {
		$list[$i]['cCertifiedId'] = $tmp['cCertifyId'] ;
		$list[$i]['cSignDate'] = $tmp['cSignDate'] ;
		$list[$i]['cCity'] = $tmp['cCity'] ;
		$list[$i]['cArea'] = $tmp['cArea'] ;
		$list[$i]['cAddr'] = $tmp['cAddr'] ;
		
		$list[$i]['cBrand'] = $tmp['cBrand2'] ;
		$list[$i]['cBranchNum'] = $tmp['cBranchNum2'] ;
		$list[$i]['cBranchName'] = getBranchName($list[$i]['cBranchNum']) ;
		$list[$i]['cServiceTarget'] = $tmp['cServiceTarget2'] ;
		
		$i ++ ;
	}
	
	unset($tmp) ;
}
//print_r($list) ; exit ;
##

//蒐集所有買賣方資料
$all = array() ;
$i = 0 ;
foreach ($list as $k => $v) {
	$cId = $v['cCertifiedId'] ;
	$sId = $v['cBranchNum'] ;
	$tg = $v['cServiceTarget'] ;
	$j = 0 ;
	
	switch ($v['cServiceTarget']) {
		case '1':
				include 'contractOwner.php' ;
				include 'contractBuyer.php' ;
				include 'contractOther.php' ;
				
				break ;
		case '2':
				include 'contractOwner.php' ;
				include 'contractOther.php' ;
				
				break ;
		case '3':
				include 'contractBuyer.php' ;
				include 'contractOther.php' ;
				
				break ;
		
	}
}
unset($list) ;
//print_r($all) ; exit ;
##

//查無符合條件資格之買賣方
if (count($all) <= 0) {
	echo "查無符合條件資格之買賣方!!\n" ;
	exit ;
}
##

//產出CSV檔
$str = '流水號,簽約日,標的物地址,仲介店,身分別,身分證字號,姓名,手機號碼,經紀人,經紀人手機,中獎日期,保證號碼'."\r\n" ;
foreach ($all as $k => $v) {
	$str .= ($k+1).','.substr($v['cSignDate'],0,10).','.$v['cCity'].$v['cArea'].$v['cAddr'].','.$v['cBranchName'].','.$v['candidate'].','.$v['cIdentifyId'].','.$v['cName'].','.$v['cMobileNum'].','.$v['cAgentName'].','.$v['cAgentMobile'].','.date("Y-m-d").','.$v['cCertifiedId']."\r\n" ;
}

$fh = fopen ('2015bigdata_'.date("Y-m-d").".csv","w") ;
fwrite($fh,$str) ;
fclose($fh) ;
##

echo "==================================================\r\n" ;
echo date("Y-m-d H:i:s")." 已產出資料!!\r\n" ;

?>