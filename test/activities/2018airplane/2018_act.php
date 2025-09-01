<?php
include_once '../../../openadodb.php' ;

/* 活動日期範圍 */
##測試用##
// $fromDate = '2017-04-01 00:00:00' ;


// $toDate = '2017-05-31 23:59:59' ;
$fromDate = '2018-03-01 00:00:00' ;
$toDate = date('Y-m-d').' 23:59:59';
// $toDate = date('Y-m-d H:i:s');
// $toDate = date('Y-m-d').' 23:59:59';
##########

##########

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
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brand,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brand1,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brand2,

		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS branch,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS branch1,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS branch2,
		(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum) AS bCategory,
		(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum1) AS bCategory1,
		(SELECT bCategory FROM tBranch WHERE bId = rea.cBranchNum2) AS bCategory2,
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		rea.cServiceTarget as cServiceTarget,
		rea.cServiceTarget1 as cServiceTarget1,
		rea.cServiceTarget2 as cServiceTarget2,
		own.cIdentifyId as ownerId,
		own.cName as ownerName,
		buy.cIdentifyId as buyerId,
		buy.cName as buyerName,
		buy.cMobileNum AS buymobile,
		own.cMobileNum AS ownmobile
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
while (!$rs->EOF) {
	$bname = '';
	$bID = '';
	$oname = '';
	$oID = '';
	
	$rs->fields['cSignDate'] = substr($rs->fields['cSignDate'],0,10) ;
	$rs->fields['cSignDate'] = DateChange($rs->fields['cSignDate']);
	//sell 2 buy 1
	if ($rs->fields['bCategory'] == 2) { //1:加盟、2:直營
		$rs->fields['bCategory'] = '直營';
	}else{
		$rs->fields['bCategory'] = '加盟';
	}

	if ($rs->fields['bCategory1'] == 2) { //1:加盟、2:直營
		$rs->fields['bCategory1'] = '直營';
	}else{
		$rs->fields['bCategory1'] = '加盟';
	}

	if ($rs->fields['bCategory2'] == 2) { //1:加盟、2:直營
		$rs->fields['bCategory2'] = '直營';
	}else{
		$rs->fields['bCategory2'] = '加盟';
	}

	$tmp = getOther2($rs->fields['cCertifiedId'],$rs->fields);
			
					for ($j=0; $j < count($tmp); $j++) { 
						if ($tmp[$j]['type'] == '買方') {
							$bname .= ','.$tmp[$j]['name'];
							$bID .= ','.$tmp[$j]['ID'];
						}elseif ($tmp[$j]['type'] == '賣方') {
							$oname .= ','.$tmp[$j]['name'];
							$oID .= ','.$tmp[$j]['ID'];
						}
						
					}
					unset($tmp);


	if ($rs->fields['cBranchNum1'] > 0) { //>一間店
		

		if ($rs->fields['cBrand'] == 1) {
			if ($rs->fields['cServiceTarget'] == 1) {
				


				$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
				$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
				$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);

				$arr[$i]['type'] ='買方';
				$arr[$i]['name'] = $rs->fields['buyerName'].$bname;
				$arr[$i]['ID'] = $rs->fields['buyerId'].$bID;
				$arr[$i]['brand'] = $rs->fields['brand'];
				$arr[$i]['branch'] = $rs->fields['branch'];
				$arr[$i]['category'] = $rs->fields['bCategory'];
				$arr[$i]['mobile'] = $rs->fields['buymobile'];
				$arr2[] = $arr[$i];

				$i++;
				

				
				$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
				$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
				$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);
				$arr[$i]['type'] ='賣方';
				$arr[$i]['name'] = $rs->fields['ownerName'].$oname;
				$arr[$i]['ID'] = $rs->fields['ownerId'].$oID;
				$arr[$i]['brand'] = $rs->fields['brand'];
				$arr[$i]['branch'] = $rs->fields['branch'];
				$arr[$i]['category'] = $rs->fields['bCategory'];
				$arr[$i]['mobile'] = $rs->fields['ownmobile'];
				$arr2[] = $arr[$i];
				$i++;
				

			}elseif ($rs->fields['cServiceTarget'] == 2) { //賣方
				$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
				$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
				$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);
				$arr[$i]['type'] ='賣方';
				$arr[$i]['name'] = $rs->fields['ownerName'].$oname;
				$arr[$i]['ID'] = $rs->fields['ownerId'].$oID;
				$arr[$i]['brand'] = $rs->fields['brand'];
				$arr[$i]['branch'] = $rs->fields['branch'];
				$arr[$i]['category'] = $rs->fields['bCategory'];
				$arr[$i]['mobile'] = $rs->fields['ownmobile'];
				$arr2[] = $arr[$i];
				$i++;
				
			}elseif ($rs->fields['cServiceTarget'] == 3) {
				$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
				$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
				$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);

				$arr[$i]['type'] ='買方';
				$arr[$i]['name'] = $rs->fields['buyerName'].$bname;
				$arr[$i]['ID'] = $rs->fields['buyerId'].$bID;
				$arr[$i]['brand'] = $rs->fields['brand'];
				$arr[$i]['branch'] = $rs->fields['branch'];
				$arr[$i]['category'] = $rs->fields['bCategory'];
				$arr[$i]['mobile'] = $rs->fields['buymobile'];
				$arr2[] = $arr[$i];
				$i++;
				// $tmp = getOther($rs->fields['cCertifiedId'],$rs->fields,1);
				
			}
		}


		if ($rs->fields['cBrand1'] == 1) {
			if ($rs->fields['cServiceTarget1'] == 2) { //賣方
				$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
				$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
				$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);
				$arr[$i]['type'] ='賣方';
				$arr[$i]['name'] = $rs->fields['ownerName'].$oname;
				$arr[$i]['ID'] = $rs->fields['ownerId'].$oID;
				$arr[$i]['brand'] = $rs->fields['brand1'];
				$arr[$i]['branch'] = $rs->fields['branch1'];
				$arr[$i]['category'] = $rs->fields['bCategory1'];
				$arr[$i]['mobile'] = $rs->fields['ownmobile'];
				$arr2[] = $arr[$i];
				$i++;
				
			}elseif ($rs->fields['cServiceTarget1'] == 3) {
				$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
				$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
				$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);

				$arr[$i]['type'] ='買方';
				$arr[$i]['name'] = $rs->fields['buyerName'].$bname;
				$arr[$i]['ID'] = $rs->fields['buyerId'].$bID;
				$arr[$i]['brand'] = $rs->fields['brand1'];
				$arr[$i]['branch'] = $rs->fields['branch1'];
				$arr[$i]['category'] = $rs->fields['bCategory1'];
				$arr[$i]['mobile'] = $rs->fields['buymobile'];
				$arr2[] = $arr[$i];
				$i++;
				
			}
		}

		// if ($rs->fields['cBrand2'] == 1) {
		// 	if ($rs->fields['cServiceTarget2'] == 2) { //賣方
		// 		$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
		// 		$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
		// 		$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);
		// 		$arr[$i]['type'] ='賣方';
		// 		$arr[$i]['name'] = $rs->fields['ownerName'].$oname;
		// 		$arr[$i]['ID'] = $rs->fields['ownerId'].$oID;
		// 		$arr[$i]['brand'] = $rs->fields['brand2'];
		// 		$arr[$i]['branch'] = $rs->fields['branch2'];
		// 		$arr[$i]['category'] = $rs->fields['bCategory2'];
		// 		$arr[$i]['mobile'] = $rs->fields['ownmobile'];
		// 		$arr2[] = $arr[$i];
		// 		$i++;
				
		// 	}elseif ($rs->fields['cServiceTarget2'] == 3) {
		// 		$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
		// 		$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
		// 		$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);

		// 		$arr[$i]['type'] ='買方';
		// 		$arr[$i]['name'] = $rs->fields['buyerName'].$bname;
		// 		$arr[$i]['ID'] = $rs->fields['buyerId'].$bID;
		// 		$arr[$i]['brand'] = $rs->fields['brand2'];
		// 		$arr[$i]['branch'] = $rs->fields['branch2'];
		// 		$arr[$i]['category'] = $rs->fields['bCategory2'];
		// 		$arr[$i]['mobile'] = $rs->fields['buymobile'];
		// 		$arr2[] = $arr[$i];
		// 		$i++;
				
		// 	}
		// }
		
		# code...
	}else{ //單店
		if ($rs->fields['cBrand'] == 1) {
			# $arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
			$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
			$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
			$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);

			$arr[$i]['type'] ='買方';
			$arr[$i]['name'] = $rs->fields['buyerName'].$bname;
			$arr[$i]['ID'] = $rs->fields['buyerId'].$bID;
			$arr[$i]['brand'] = $rs->fields['brand'];
			$arr[$i]['branch'] = $rs->fields['branch'];
			$arr[$i]['category'] = $rs->fields['bCategory'];
			$arr[$i]['mobile'] = $rs->fields['ownmobile'];
			$i++;


			$arr[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'];
			$arr[$i]['cSignDate'] = $rs->fields['cSignDate'];
			$arr[$i]['cAddr'] =getAddr($rs->fields['cCertifiedId']);
			$arr[$i]['type'] ='賣方';
			$arr[$i]['name'] = $rs->fields['ownerName'].$oname;
			$arr[$i]['ID'] = $rs->fields['ownerId'].$oID;
			$arr[$i]['brand'] = $rs->fields['brand'];
			$arr[$i]['branch'] = $rs->fields['branch'];
			$arr[$i]['category'] = $rs->fields['bCategory'];
			$arr[$i]['mobile'] = $rs->fields['buymobile'];

			$i++;
			
		}


		
	}
	
	
	
	$rs->MoveNext();
}

function getOther($id,$arr2,$type=''){
	global $conn;

	if ($type=='') {
		$query = ' AND cIdentity IN(1,2)';
	}else{
		$type = 'AND cIdentity = "'.$type.'"';
	}


	$sql = "SELECT * FROM  tContractOthers WHERE cCertifiedId = '".$id."'".$query;

	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {

		if ($rs->fields['cIdentity'] == 1) { //(1買2賣
			$tmp2[$i]['cCertifiedId'] = $id;
			$tmp2[$i]['cSignDate'] = $arr2['cSignDate'];
			$tmp2[$i]['cAddr'] =getAddr($id);
			$tmp2[$i]['type'] ='買方';
			$tmp2[$i]['name'] = $rs->fields['cName'];
			$tmp2[$i]['ID'] = $rs->fields['cIdentifyId'];
			$tmp2[$i]['brand'] = $arr2['brand'];
			$tmp2[$i]['branch'] = $arr2['branch'];
			$tmp2[$i]['category'] = $arr2['bCategory'];
			$tmp2[$i]['mobile'] = $rs->fields['cMobileNum'];
			$i++;
		}elseif ($rs->fields['cIdentity'] == 2) {
			$tmp2[$i]['cCertifiedId'] = $id;
			$tmp2[$i]['cSignDate'] = $arr2['cSignDate'];
			$tmp2[$i]['cAddr'] =getAddr($id);
			$tmp2[$i]['type'] ='賣方';
			$tmp2[$i]['name'] = $rs->fields['cName'];
			$tmp2[$i]['ID'] = $rs->fields['cIdentifyId'];
			$tmp2[$i]['brand'] = $arr2['brand'];
			$tmp2[$i]['branch'] = $arr2['branch'];
			$tmp2[$i]['category'] = $arr2['bCategory'];
			$tmp2[$i]['mobile'] = $rs->fields['cMobileNum'];
			$i++;
		}
		# code...
		$rs->MoveNext();
	}

	return $tmp2;
}
function getOther2($id,$arr2,$type=''){
	global $conn;

	if ($type=='') {
		$query = ' AND cIdentity IN(1,2)';
	}else{
		$type = 'AND cIdentity = "'.$type.'"';
	}


	$sql = "SELECT * FROM  tContractOthers WHERE cCertifiedId = '".$id."'".$query;

	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {

		if ($rs->fields['cIdentity'] == 1) { //(1買2賣
			
			$tmp2[$i]['type'] ='買方';
			$tmp2[$i]['name'] = $rs->fields['cName'];
			$tmp2[$i]['ID'] = $rs->fields['cIdentifyId'];			
			$tmp2[$i]['mobile'] = $rs->fields['cMobileNum'];
			$i++;
		}elseif ($rs->fields['cIdentity'] == 2) {
			
			$tmp2[$i]['type'] ='賣方';
			$tmp2[$i]['name'] = $rs->fields['cName'];
			$tmp2[$i]['ID'] = $rs->fields['cIdentifyId'];
			$tmp2[$i]['mobile'] = $rs->fields['cMobileNum'];
			$i++;
		}
		# code...
		$rs->MoveNext();
	}

	return $tmp2;
}
//保證號碼 簽約日期 買方/賣方  委託書編號 身份證字號 物件地址 姓名 店名 直營或加盟
// echo '保證號碼_簽約日期_買方/賣方_委託書編號_身份證字號_物件地址_姓名_店名_直營或加盟<br>';

// for ($i=0; $i < count($arr); $i++) { 
// 	echo $arr[$i]['cCertifiedId'].'_'.$arr[$i]['cSignDate'].'_'.$arr[$i]['type'].'_-_'.$arr[$i]['ID'].'_'.$arr[$i]['cAddr'].'_'.$arr[$i]['name'].'_'.$arr[$i]['brand'].$arr[$i]['branch'].'_'.$arr[$i]['category'].'<br>';
// }

$fh = fopen('audi_'.date("Ymd").'.csv','w') ;
fwrite($fh,'保證號碼_簽約日期_買方/賣方_委託書編號_身份證字號_物件地址_姓名_店名_直營或加盟_買賣方電話'."\r\n") ;
for ($i = 0 ; $i < count($arr) ; $i ++) {
	$arr[$i]['cAddr'] = str_replace(',','，',$arr[$i]['cAddr']);
	fwrite($fh,$arr[$i]['cCertifiedId'].'_'.$arr[$i]['cSignDate'].'_'.$arr[$i]['type'].'_-_'.$arr[$i]['ID'].'_'.$arr[$i]['cAddr'].'_'.$arr[$i]['name'].'_'.$arr[$i]['brand'].$arr[$i]['branch'].'_'.$arr[$i]['category']."_".$arr[$i]['mobile']."\r\n") ;
}
fclose($fh) ;

echo "Done!!<br>\n(".date("Y-m-d G:i:s").')' ;

die;
// while (!$rs->EOF) {

// 	$rs->fields['cSignDate'] = substr($rs->fields['cSignDate'],0,10) ;
// 	$rs->fields['cSignDate'] = DateChange($rs->fields['cSignDate']);
	


// 	$rs->fields['ownerId'] = strtoupper($rs->fields['ownerId']) ;
// 	$rs->fields['ownerName'] = strtoupper($rs->fields['ownerName']) ;
// 	$rs->fields['buyerId'] = strtoupper($rs->fields['buyerId']) ;
// 	$rs->fields['buyerName'] = strtoupper($rs->fields['buyerName']) ;

// 	// $city = $rs->fields['cCity'] ;
// 	// $area = $rs->fields['cArea'] ;
// 	// $addr = str_replace(',','，',$rs->fields['cAddr']) ;
// 	// $addr = preg_replace("/$city/","",$addr) ;
// 	// $addr = str_replace($area,"",$addr) ;

// 	// $tmpAddr[0] = $city.$area.$addr;


// 	// $rs->fields['cAddr'] =  implode('_', $tmpAddr);
// 	// unset($city,$area,$addr) ;
// 	$rs->fields['cAddr'] = getAddr($rs->fields['cCertifiedId']);

// 	/*$branch = array($rs->fields['cBranchNum'], $rs->fields['cBranchNum1'], $rs->fields['cBranchNum2']) ;
	
// 	$fg = 0 ;
// 	foreach ($branch as $k => $v) {
// 		if (checkCategory($v,$conn)) {
// 			$fg ++ ;

// 		}
// 	}*/
// 	$fg = 0 ;
// 	if ($rs->fields['cBrand'] == '1' ) {$fg ++ ;}
		
// 	if ($rs->fields['cBrand1'] == '1') {$fg ++ ;}

// 	if ($rs->fields['cBrand2'] == '1') {$fg ++ ;}


// 	if ($fg > 0) {
		
// 		$list[$i] = $rs->fields;
// 		$list[$i]['type'] = checkType($conn,$rs->fields);
// 		$list[$i]['fg'] = $fg;
// 		$i++;
// 	}
	
	
	
// 	$rs->MoveNext() ;
// }

##
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;
//print_r($list) ; exit ;
//查詢多組買賣方身分證字號
// for ($i = 0 ; $i < count($list) ; $i ++) {

	

// 	$sql = 'SELECT cIdentity,cIdentifyId,cName FROM tContractOthers WHERE cCertifiedId="'.$list[$i]['cCertifiedId'].'" ORDER BY cId ASC;' ;
// 	//echo 'sql='.$sql."<br>\n" ;
// 	$rs = $conn->Execute($sql) ;
// 	while (!$rs->EOF) {
// 		if ($rs->fields['cIdentity'] == '1' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 3)) {
// 			$list[$i]['buyerId'] .= '_'.strtoupper($rs->fields['cIdentifyId']) ;
// 			$list[$i]['buyerName'] .= '_'.$rs->fields['cName'] ;
// 		}
// 		else if ($rs->fields['cIdentity'] == '2' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 2)) {
// 			$list[$i]['ownerId'] .= '_'.strtoupper($rs->fields['cIdentifyId']) ;
// 			$list[$i]['ownerName'] .= '_'.$rs->fields['cName'] ;
// 		}
// 		$rs->MoveNext() ;
// 	}
// }
##
die;

function DateChange($val)
{
	$tmp = explode('-', $val);

	$val = $tmp[0]."/".(int)$tmp[1]."/".$tmp[2];
	

	
	return $val;
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
		return implode(';', $arr);
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
?>