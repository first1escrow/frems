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
$act = '2015prize' ;		//禮多人歡喜周周抽大獎20150309~20150511
$cSignDateF = '2015-02-26 00:00:00' ;
//$cSignDateT = date("Y-m-d 23:59:59",strtotime("-1 day")) ;
$cSignDateT = '2015-05-10 18:00:00' ;

echo date("Y-m-d H:i:s")." 開始進行 ".$act." [".date("Y-m-d")."] 的抽獎活動!!\n" ;
##

//確認無今日的中獎人
$sql = "SELECT * FROM tIpadPrize WHERE pPrizeDate>='".date("Y-m-d 00:00:00")."' AND pActivity='".$act."';" ;
$rel = mysql_query($sql,$link) ;
if (mysql_num_rows($rel) > 0) {
	echo "今日(".date("Y-m-d").")額度一名已抽過了!!\n" ;
	exit ;
}
##

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

//增加一名例外人選 ""
$i = count($list) ;
$list[$i]['cCertifiedId'] = '003072441' ;
$list[$i]['cSignDate'] = '2015-02-24 00:00:00' ;
$list[$i]['cCity'] = '桃園市' ;
$list[$i]['cArea'] = '龜山區' ;
$list[$i]['cAddr'] = '陸光路61-1號' ;
$list[$i]['cBrand'] = '1' ;
$list[$i]['cBranchNum'] = '42' ;
$list[$i]['cBranchName'] = '台灣房屋龜山直營店' ;
$list[$i]['cServiceTarget'] = '2' ;
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

//抽出一名
$max = count($all) - 1 ;

while (1) {
	$winner = array() ;
	$winner = $all[rand(0,$max)] ;
	//print_r($winner) ; exit ;

	//$sql = "SELECT * FROM tIpadPrize WHERE pCertifiedId = '".$winner['cCertifiedId']."' AND pName ='".$winner['cName']."' AND pIdentifyId ='".$winner['cIdentifyId']."'";
	$sql = "SELECT * FROM tIpadPrize WHERE pActivity = '".$act."' AND pCertifiedId = '".$winner['cCertifiedId']."' AND pName ='".$winner['cName']."' AND pIdentifyId ='".$winner['cIdentifyId']."'";
	// echo $sql."<br>";
	$check = mysql_query($sql,$link) ;
	$check_count = 0 ;
	$check_count = mysql_num_rows($check) ;
	$tf = true ;
	
	//2015-03-10
	/*
	$weight = 10 ;
	$sql = "SELECT COUNT(pId) as realty FROM tIpadPrize WHERE pBranchName LIKE '%直營%';" ;
	$rel = mysql_query($sql,$link) ;
	$tmp = mysql_fetch_array($rel) ;
	$realty1 = (int)$tmp['realty'] ;
	unset($tmp) ;

	$sql = "SELECT COUNT(pId) as realty FROM tIpadPrize WHERE pBranchName LIKE '%加盟%';" ;
	$rel = mysql_query($sql,$link) ;
	$tmp = mysql_fetch_array($rel) ;
	$realty2 = (int)$tmp['realty'] ;
	//echo 'realty 1 ='.$realty1.', realty 2 ='.$realty2 ;
	if ((($realty2 + $weight) > $realty1) && (preg_match("/加盟/",$winner['cBranchName']))) $tf = false ;
	unset($tmp) ;
	*/
	//if (preg_match("/加盟/",$winner['cBranchName'])) $tf = false ;
	##
	
	if (($winner['cName'] != '') && ($check_count == 0) && $tf) {
		$sql = "
			INSERT INTO
				tIpadPrize
			(
				pActivity,
				pCertifiedId,
				pSignDate,
				pAddr,
				pBranchNum,
				pBranchName,
				pIdentity,
				pIdentifyId,
				pName,
				pMobile,
				pTel,
				pAgentName,
				pAgentMobile,
				pPrizeDate
			)
			VALUE
			(
				'".$act."',
				'".$winner['cCertifiedId']."',
				'".$winner['cSignDate']."',
				'".$winner['cCity'].$winner['cArea'].$winner['cAddr']."',
				'".$winner['cBranchNum']."',
				'".$winner['cBranchName']."',
				'".$winner['candidate']."',
				'".$winner['cIdentifyId']."',
				'".$winner['cName']."',
				'".$winner['cMobileNum']."',
				'".$winner['cTelArea1'].$winner['cTelMain1']."',
				'".$winner['cAgentName']."',
				'".$winner['cAgentMobile']."',
				'".date("Y-m-d H:i:s")."'
			)
		;" ;
		//echo "sql = ".$sql."\n" ; exit ;
		mysql_query($sql,$link) ;
		
		echo date("Y-m-d")." 的獎項已經抽出!!\n" ;
		exit ;
	}
	/*
	else {
		$winner = array() ;
		$winner = $all[rand(0,$max)] ;
		//print_r($winner) ; exit ;

		$sql = "SELECT * FROM tIpadPrize WHERE pCertifiedId = '".$winner['cCertifiedId']."' AND pName ='".$winner['cName']."' AND pIdentifyId ='".$winner['cIdentifyId']."'";

		$check = mysql_query($sql,$link) ;
		$check_count=0;
		$check_count= mysql_num_rows($check);

		if ($winner['cName'] != '' && $check_count==0) {
			$sql = "
				INSERT INTO
					tIpadPrize
				(
					pCertifiedId,
					pSignDate,
					pAddr,
					pBranchNum,
					pBranchName,
					pIdentity,
					pIdentifyId,
					pName,
					pMobile,
					pTel,
					pAgentName,
					pAgentMobile,
					pPrizeDate
				)
				VALUE
				(
					'".$winner['cCertifiedId']."',
					'".$winner['cSignDate']."',
					'".$winner['cCity'].$winner['cArea'].$winner['cAddr']."',
					'".$winner['cBranchNum']."',
					'".$winner['cBranchName']."',
					'".$winner['candidate']."',
					'".$winner['cIdentifyId']."',
					'".$winner['cName']."',
					'".$winner['cMobileNum']."',
					'".$winner['cTelArea1'].$winner['cTelMain1']."',
					'".$winner['cAgentName']."',
					'".$winner['cAgentMobile']."',
					'".date("Y-m-d H:i:s")."'
				)
			;" ;
			//echo "sql = ".$sql."\n" ; exit ;
			mysql_query($sql,$link) ;
			
			echo date("Y-m-d")." 的獎項已經抽出!!\n" ;
			exit ;
		}

	}
	*/
}
##
?>
