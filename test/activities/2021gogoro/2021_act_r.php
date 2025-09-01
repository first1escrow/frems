<?php
// include_once '../db/openadodb.php' ;
include_once '../../../openadodb.php' ;

/* 活動日期範圍 */

$fromDate = '2021-12-01 00:00:00' ;
$toDate = '2022-02-28 18:00:00' ;



/*****************/

##


$sql = 'SELECT 
		cas.cCertifiedId as cCertifiedId,
		cas.cSignDate as cSignDate,
		rea.cBrand,
		rea.cBrand1,
		rea.cBrand2,
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) AS bCategory,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) AS bCategory1,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) AS bCategory2,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brand,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brand1,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brand2,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS branch,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS branch1,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS branch2,
		rea.cServiceTarget as cServiceTarget,
		rea.cServiceTarget1 as cServiceTarget1,
		rea.cServiceTarget2 as cServiceTarget2,
		own.cIdentifyId as ownerId,
		own.cName as ownerName,
		buy.cIdentifyId as buyerId,
		buy.cMobileNum AS buymobile,
		own.cMobileNum AS ownmobile,
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

	ASC;';

	echo $sql;
$rs = $conn->Execute($sql);
$i = 0;
//保證號碼+服務費>0+買方or賣方
//服務對象：1.買賣方、2.賣方、3.買方
while (!$rs->EOF) {
		//

		//第一間店
		if ($rs->fields['cBrand'] == 1) {
			
			if ($rs->fields['cServiceTarget'] == 1) {

					if (checkServiecFee($rs->fields['cBranchNum'],$rs->fields['cCertifiedId'],"買方") && checkCategory($rs->fields['cBranchNum'])) { // 是否有付服務費//判斷是否為加盟
						
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '買方'; 
						$i++;

					}

					if (checkServiecFee($rs->fields['cBranchNum'],$rs->fields['cCertifiedId'],"賣方") && checkCategory($rs->fields['cBranchNum'])) {
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '賣方'; 
						$i++;
					}
				
				
			}elseif ($rs->fields['cServiceTarget'] == 2) {
				
					if (checkServiecFee($rs->fields['cBranchNum'],$rs->fields['cCertifiedId'],"賣方") && checkCategory($rs->fields['cBranchNum'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '賣方'; 
						$i++;
					}
				
				# code...
			}elseif ($rs->fields['cServiceTarget'] == 3) {
				if (checkServiecFee($rs->fields['cBranchNum'],$rs->fields['cCertifiedId'],"買方") && checkCategory($rs->fields['cBranchNum'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '買方'; 
						$i++;
					}
			}
		}
		//第二間店
		if ($rs->fields['cBrand1'] == 1) {
			if ($rs->fields['cServiceTarget1'] == 1) {
			
					if (checkServiecFee($rs->fields['cBranchNum1'],$rs->fields['cCertifiedId'],"買方") && checkCategory($rs->fields['cBranchNum1'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '買方'; 
						$i++;
						
					}

					if (checkServiecFee($rs->fields['cBranchNum1'],$rs->fields['cCertifiedId'],"賣方") && checkCategory($rs->fields['cBranchNum1'])) {
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '賣方'; 
						$i++;
					}
				
				
			}elseif ($rs->fields['cServiceTarget1'] == 2) {
				
					if (checkServiecFee($rs->fields['cBranchNum1'],$rs->fields['cCertifiedId'],"賣方") && checkCategory($rs->fields['cBranchNum1'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '賣方'; 
						$i++;
					}
				
				# code...
			}elseif ($rs->fields['cServiceTarget1'] == 3) {
				if (checkServiecFee($rs->fields['cBranchNum1'],$rs->fields['cCertifiedId'],"買方") && checkCategory($rs->fields['cBranchNum1'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '買方'; 
						$i++;
					}
			}
		}
		
		//第三間店
		if ($rs->fields['cBrand2'] == 1) {
			
			if ($rs->fields['cServiceTarget2'] == 1) {

					if (checkServiecFee($rs->fields['cBranchNum2'],$rs->fields['cCertifiedId'],"買方") && checkCategory($rs->fields['cBranchNum2'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '買方'; 
						$i++;
						
					}

					if (checkServiecFee($rs->fields['cBranchNum2'],$rs->fields['cCertifiedId'],"賣方") && checkCategory($rs->fields['cBranchNum2'])) {
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '賣方'; 
						$i++;
					}
				
				
			}elseif ($rs->fields['cServiceTarget'] == 2) {
				
					if (checkServiecFee($rs->fields['cBranchNum2'],$rs->fields['cCertifiedId'],"賣方") && checkCategory($rs->fields['cBranchNum2'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '賣方'; 
						$i++;
					}
				
				# code...
			}elseif ($rs->fields['cServiceTarget'] == 3) {
				if (checkServiecFee($rs->fields['cBranchNum2'],$rs->fields['cCertifiedId'],"買方") && checkCategory($rs->fields['cBranchNum2'])) { // 是否有付服務費
						$data[$i]=$rs->fields;
						$data[$i]['Target'] = '買方'; 
						$i++;
					}
			}
		}
	

	$rs->MoveNext();
}

$fh = fopen('r'.date("Ymd").'.csv','w') ;
fwrite($fh,'保證號碼,是否有服務費,身分'."\r\n") ;
for ($i = 0 ; $i < count($data) ; $i ++) {



	$txt = $data[$i]['cCertifiedId'].'_,1,'.$data[$i]['Target'];
	fwrite($fh,$txt."\r\n") ;

	// fwrite($fh,$list[$i]['cCertifiedId'].'_,'.$list[$i]['cSignDate'].','.$list[$i]['cDealId'].','.$list[$i]['buyerId'].','.$list[$i]['ownerId'].','.$list[$i]['cAddr'].','.$list[$i]['cate'].','.$list[$i]['buyerName'].','.$list[$i]['ownerName'].','.$list[$i]['buymobile'].','.$list[$i]['ownmobile'].','.$list[$i]['buyStore'].','.$list[$i]['ownStore']."\r\n") ;
}
fclose($fh) ;

// print_r($data);
die;
//確認加盟店類型
Function checkCategory($bId) {
	global $conn;
	$ct = false ;
	
	if ($bId > 0) {
		$sql = 'SELECT * FROM tBranch WHERE bId="'.$bId.'";' ;
		$rel = $conn->Execute($sql) ;
		
		//確認案件為台灣房屋加盟店
		if ($rel->fields['bBrand'] == '1' && $rel->fields['bCategory'] == 1) {		//台灣房屋
		//if (($rel->fields['bBrand'] == '49') && ($rel->fields['bCategory'] == '1')) {		//優美地產
			$ct = true ;
		}
		##
	}
	
	return $ct ;
}
function checkServiecFee($branch,$cId,$target){ //
	global $conn;
	$check = false;
	$sql = "SELECT tTxt FROM tBankTrans WHERE tMemo = '".$cId."' AND tStoreId = '".$branch."'";
	// echo $sql;
	// die;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		
		if (preg_match("/服務費/", $rs->fields['tTxt']) && preg_match("/".$target."/", $rs->fields['tTxt'])) {
			


			$check = true;
		}


		$rs->MoveNext();
	}

	return $check;
}

function DateChange($val){
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
		return implode('_', $arr);
	}else{
		return false;
	}
}


##
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;
//print_r($list) ; exit ;
//查詢多組買賣方身分證字號
// $max = count($list);

// for ($i = 0 ; $i < $max ; $i ++) {

	

// 	$sql = 'SELECT cIdentity,cIdentifyId,cName,cMobileNum FROM tContractOthers WHERE cCertifiedId="'.$list[$i]['cCertifiedId'].'" ORDER BY cId ASC;' ;
// 	//echo 'sql='.$sql."<br>\n" ;
// 	$rs = $conn->Execute($sql) ;
// 	while (!$rs->EOF) {
// 		if ($rs->fields['cIdentity'] == '1' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 3)) {
// 			$list[$i]['buyerId'] .= '_'.strtoupper($rs->fields['cIdentifyId']) ;
// 			$list[$i]['buyerName'] .= '_'.$rs->fields['cName'] ;
// 			$list[$i]['buymobile'] .= '_'.$rs->fields['cMobileNum'];
// 		}
// 		else if ($rs->fields['cIdentity'] == '2' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 2)) {
// 			$list[$i]['ownerId'] .= '_'.strtoupper($rs->fields['cIdentifyId']) ;
// 			$list[$i]['ownerName'] .= '_'.$rs->fields['cName'] ;
// 			$list[$i]['ownmobile'] .= '_'.$rs->fields['cMobileNum'];
// 		}
// 		$rs->MoveNext() ;
// 	}


// 	$sql = "SELECT * FROM tBankTrans WHERE tMemo = '".$list[$i]['cCertifiedId']."' AND tTxt LIKE '%服務費%'";

// 	$rs = $conn->Execute($sql);
// 	$total=$rs->RecordCount();
	
	
// 	// echo $dd."\r\n";
// 	if ($total > 0) {
		
// 	}
// 	$arr[] = $list[$i];
// }
##
// unset($list);
// $list = $arr;

// print_r($list) ; exit ;
// $fh = fopen('r'.date("Ymd").'.csv','w') ;
// fwrite($fh,'保證號碼,簽約日期,委託書編號,買方身份證字號,賣方身份證字號,物件地址,類型,買方姓名,賣方姓名,買方電話,賣方電話,買方仲介店,賣方仲介店'."\r\n") ;
// for ($i = 0 ; $i < count($list) ; $i ++) {
// 	fwrite($fh,$list[$i]['cCertifiedId'].'_,'.$list[$i]['cSignDate'].','.$list[$i]['cDealId'].','.$list[$i]['buyerId'].','.$list[$i]['ownerId'].','.$list[$i]['cAddr'].','.$list[$i]['cate'].','.$list[$i]['buyerName'].','.$list[$i]['ownerName'].','.$list[$i]['buymobile'].','.$list[$i]['ownmobile'].','.$list[$i]['buyStore'].','.$list[$i]['ownStore']."\r\n") ;
// }
// fclose($fh) ;

echo "Done!!<br>\n(".date("Y-m-d G:i:s").')' ;
?>