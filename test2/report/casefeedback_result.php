<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
 header("Content-Type:text/html; charset=utf-8"); 
include_once '../../includes/maintain/feedBackData.php';
include_once '../../openadodb.php' ;
function getOtherFeed_case22($cid,$arr,$bId='',$sId='',$brand=''){ //回饋案件表
	global $conn;

	if (($sId != 0 && $sId != '') && $bId) { //兩個都查
		$str = "AND (fb.fType = 2 AND fStoreId IN(".$bId.") OR fb.fType = 1 AND fStoreId IN(".$sId."))";
		
	}else if ($bId) {
		$str = "AND fb.fType = 2 AND fStoreId IN(".$bId.")";
	}elseif ($sId != 0 && $sId != '') {
		$str .= "AND fb.fType = 1 AND fStoreId IN(".$sId.")";
	}





	$sql = "SELECT *,(SELECT bCategory FROM tBranch WHERE bId =cr.cBranchNum) AS bCategory FROM tFeedBackMoney AS fb,tContractRealestate AS cr  WHERE fb.fCertifiedId=cr.cCertifyId  AND fb.fDelete = 0 AND (fType = 1 OR fType = 2) AND fb.fCertifiedId ='".$cid."'".$str;
	
	// if ($cid == '020057746') {
	// 	echo $sql;

	// 	die;
	// }
	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
			
		if ($rs->fields['fType'] == 1) { //地政士先以第一間店為主..
				$data[$i]['buyer'] = $arr['buyer'] ;
				$data[$i]['owner'] = $arr['owner'] ;
				$data[$i]['cBranchNum'] = '地政士' ;
				$data[$i]['cTotalMoney'] = $arr['cTotalMoney'] ;
				$data[$i]['cCaseFeedback'] = 0 ;
				$data[$i]['cCaseFeedBackMoney'] = $rs->fields['fMoney'] ;
					
				$data[$i]['cEndDate'] = $arr['cEndDate'] ;
				$data[$i]['cSignDate'] = $arr['cSignDate'] ;
				$data[$i]['cFeedbackTarget'] = '地政士'; 
				$data[$i]['cBank'] = $arr['cBank'] ;
				$data[$i]['cCertifiedId'] = $cid ;
				$data[$i]['bApplication'] = $arr['bApplication'];
				$data[$i]['cCertifiedMoney'] = $arr['cCertifiedMoney'];
				$tmp = getFeedBackStore($rs->fields['fType'],$rs->fields['fStoreId']);
				
				$data[$i]['bBrand'] = 'SC';
				$data[$i]['bId'] = $rs->fields['fStoreId'];
				$data[$i]['bCategory2'] = '特殊回饋(地政士)'; 
				$data[$i]['bCategory'] = $data[$i]['bCategory2'].'(回饋)'; 
				$data[$i]['bFBTarget'] = $tmp['Code'];//
				$data[$i]['cScrivener'] = $tmp['Name'];
				$data[$i]['bFeedback'] = '回饋';
				
				$data[$i]['bStoreClass'] = '單店';
				$data[$i]['bStore'] = $tmp['Store'];
				
				$data[$i]['bFeedDateCat'] = $tmp['FeedDateCat'];
				

				
		}else{
				$data[$i]['buyer'] = $arr['buyer'] ;
				$data[$i]['owner'] = $arr['owner'] ;
				$data[$i]['cBranchNum'] = $rs->fields['fStoreId'] ;
				$data[$i]['cTotalMoney'] = $arr['cTotalMoney'] ;
				$data[$i]['cCaseFeedback'] = 0 ;
				$data[$i]['cCaseFeedBackMoney'] = $rs->fields['fMoney'];
				$data[$i]['cEndDate'] = $arr['cEndDate'] ;
				$data[$i]['cSignDate'] = $arr['cSignDate'] ;
				$data[$i]['cFeedbackTarget'] = '' ;
				$data[$i]['cBank'] = $arr['cBank'] ;
				$data[$i]['cCertifiedId'] = $cid ;
				$data[$i]['cCertifiedMoney'] = $arr['cCertifiedMoney'];

				$data[$i]['cScrivener'] = '' ;
				$data[$i]['bId'] = $rs->fields['fStoreId'];
				$data[$i]['bApplication'] = $arr['bApplication'];

				$tmp = getFeedBackStore($rs->fields['fType'],$rs->fields['fStoreId']);
			
				$data[$i]['bBrand'] = $tmp['brand'];
				$data[$i]['bFeedback'] = '回饋';
				$data[$i]['bCategory2'] = category_convert2($tmp['bCategory'],$tmp['brand']); 

				$data[$i]['bFBTarget'] = $tmp['Code'];

				$data[$i]['bCategory'] = $data[$i]['bCategory2'].'(回饋)'; 
				$data[$i]['bStoreClass'] = $tmp['bStoreClass'];
				$data[$i]['bStore'] = $tmp['Store'];
				$data[$i]['bFeedDateCat'] = $tmp['FeedDateCat'];
				$data[$i]['bFeedbackMark2'] = $tmp['FeedbackMark2'];
				 


				
				 
	        
				
			}

			if ($brand) {
				// echo $tmp['brandId']."_".$brand."<bR>";
				// print_r($tmp);

				if ($tmp['brandId'] == $brand) {
					$i++ ;
				}else{
					unset($data[$i]);
				}
			}else{
				$i++ ;
			}
			

			
			unset($tmp);
		$rs->MoveNext();
	}

	// echo "<pre>";
	
	return $data;
}
$invert_result = 2 ;

$bb  = array();$ss = array();
$sql = "SELECT zZip FROM tZipArea WHERE ZCity IN('台中市','彰化縣','南投縣')";
// 
// $sql = "SELECT zZip FROM tZipArea WHERE ZCity IN('台南市','雲林縣','嘉義縣','嘉義市')";
$rs = $conn->Execute($sql);
$listZip = array();
while (!$rs->EOF) {
	array_push($listZip, '"'.$rs->fields['zZip'].'"');
	$rs->MoveNext();
}

$spStore = array();
$sql = "SELECT bBranch FROM tBrand WHERE bBranch != ''";
$rs=  $conn->Execute($sql);
while (!$rs->EOF) {
	array_push($spStore , $rs->fields['bBranch']);

	$rs->MoveNext();
}


$sql = "SELECT bBranch FROM tBranchGroup WHERE bBranch != ''";
$rs=  $conn->Execute($sql);
while (!$rs->EOF) {
	array_push($spStore , $rs->fields['bBranch']);

	$rs->MoveNext();
}


				//搜尋分店
	// 店名
	$_cond = ' AND (bZip IN('.implode(',', $listZip).') OR bId = 505 OR bId IN ('.@implode(',',$spStore).'))' ;
	// if ($branch) {
	// 	$_cond .= ' AND a.bId="'.$branch.'" ' ;
	// }
	
	
	$bsql = '
	SELECT 
		bId,
		(SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
		bStore,
		bCategory,
		bStoreClass,
		bClassBranch,
		bFeedDateCat
	FROM
		tBranch AS a
	WHERE
		a.bId <> 0 
		'.$_cond.'
	ORDER BY
		bId
	ASC;
	' ;
	

	$rs = $conn->Execute($bsql);
	while (!$rs->EOF) {
		$realty[] = $rs->fields ;
		$bb[] = $rs->fields['bId'];
		$rs->MoveNext();
	}
	
	
$sql = "SELECT sId FROM tScrivener WHERE sCpZip1 IN(".implode(',', $listZip).")";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	// $scrivener .= $rs->fields['sId'].',';
	$ss[] = $rs->fields['sId'];

	$rs->MoveNext();
}
##
unset($_cond);

unset($listZip);
##
//
$branch = implode(',', $bb);
$brand = '';
$scrivener = implode(',', $ss);

$_cond = 'AND tra.tBankLoansDate>="2021-01-01" AND tra.tBankLoansDate<="2021-12-31"' ;
$_cond2 = 'AND cBankList>="2021-01-01" AND cBankList<="2021-12-31"' ;

// $_cond = 'AND tra.tBankLoansDate>="2020-01-01" AND tra.tBankLoansDate<="2020-12-31"' ;
// $_cond2 = 'AND cBankList>="2020-01-01" AND cBankList<="2020-12-31"' ;

//取得合約銀行帳號
$_sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" GROUP BY cBankAccount ORDER BY cId ASC;' ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$conBank[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}

$conBank_sql = implode('","',$conBank) ;
##

$_sql = '
	SELECT 
		tra.tMemo as cCertifiedId,
		tBankLoansDate
	FROM
		tBankTrans AS tra
	JOIN
		tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=tra.tMemo
	WHERE
		
		tra.tAccount IN ("'.$conBank_sql.'")
		AND tra.tKind = "保證費"
		AND tra.tId != "451978" AND tra.tId != "664562"
		'.$_cond.$_cond1.' 
	GROUP BY
		tra.tMemo
	ORDER BY
		tra.tExport_time
	ASC ;
' ;

// echo "_sql=".$_sql ; exit ;
$trans = array();
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$cid_arr[] = $rs->fields ;
	$trans[$rs->fields['cCertifiedId']] = $rs->fields['tBankLoansDate'];
	$rs->MoveNext();
}

##
// print_r($cid_arr);
//取出範圍內未收履保費但仍要回饋(有利息)的案件
if ($contractDate) $_sql = 'SELECT cCertifiedId,cBankList FROM tContractCase WHERE '.$contractDate.$_cond2;
else $_sql = 'SELECT cCertifiedId,cBankList FROM tContractCase WHERE cBankList<>"" '.$_cond2.'  ;' ;

$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$trans[$rs->fields['cCertifiedId']] = $rs->fields['cBankList'];
	$cid_arr[] = $rs->fields ;

	$rs->MoveNext();
}

##

//print_r($cid_arr) ; exit ;
$otherFeed = array();
$cid_max = count($cid_arr) ;
for ($i = 0 ; $i < $cid_max ; $i ++) {
	//--依據保證號碼找出買賣方、店編號1、店編號2、買賣總價金、是否回饋、回饋金1、回饋金2、結案日期、銀行別--
		$Dsql = '
		SELECT
			rea.cCertifyId as cCertifiedId,
			buy.cName as buyer,
			own.cName as owner,
			rea.cBranchNum as cBranchNum,
			rea.cBranchNum1 as cBranchNum1,
			rea.cBranchNum2 as cBranchNum2,
			rea.cBrand as cBrand,
			rea.cBrand1 as cBrand1,
			rea.cBrand2 as cBrand2,
			inc.cTotalMoney as cTotalMoney,
			inc.cCertifiedMoney as cCertifiedMoney,
			inc.cFirstMoney as cFirstMoney,
			cas.cSpCaseFeedBackMoney as cSpCaseFeedBackMoney,
			cas.cCaseFeedback as cCaseFeedback,
			cas.cCaseFeedback1 as cCaseFeedback1,
			cas.cCaseFeedback2 as cCaseFeedback2,
			cas.cCaseFeedBackMoney as cCaseFeedBackMoney,
			cas.cCaseFeedBackMoney1 as cCaseFeedBackMoney1,
			cas.cCaseFeedBackMoney2 as cCaseFeedBackMoney2,
			cas.cEndDate as cEndDate,
			cas.cSignDate as cSignDate,
			cas.cFeedbackTarget as cFeedbackTarget,
			cas.cFeedbackTarget1 as cFeedbackTarget1,
			cas.cFeedbackTarget2 as cFeedbackTarget2,
			(
				SELECT 
					(
						SELECT
							sName
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as cScrivener,
			
			(SELECT cBankFullName FROM tContractBank WHERE cBankCode=cas.cBank) as cBank,
			(
				SELECT 
					(
						SELECT
							sId
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as sId,
			(
				SELECT 
					(
						SELECT
							sOffice
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as sOffice,
			(
				SELECT 
					(
						SELECT
							sFeedDateCat
						FROM
							tScrivener AS b
						WHERE
							b.sId=a.cScrivener
					)
				FROM
					tContractScrivener AS a
				WHERE
					a.cCertifiedId=cas.cCertifiedId
			) as sFeedDateCat,
			(SELECT CONCAT("SC", LPAD(a.cScrivener,4,"0"))  FROM tContractScrivener AS a WHERE a.cCertifiedId=cas.cCertifiedId) AS sCode2
		FROM
			tContractRealestate AS rea
		JOIN
			tContractBuyer AS buy ON buy.cCertifiedId=rea.cCertifyId
		JOIN
			tContractOwner AS own ON own.cCertifiedId=rea.cCertifyId
		JOIN
			tContractIncome AS inc ON inc.cCertifiedId=rea.cCertifyId
		JOIN
			tContractCase AS cas ON cas.cCertifiedId=rea.cCertifyId
		WHERE
			rea.cCertifyId="'.$cid_arr[$i]['cCertifiedId'].'"
	' ;
	
	$rs = $conn->Execute($Dsql);
	$app = $cid_arr[$i]['bApplication'];
	$cid_arr[$i] = $rs->fields;
	$cid_arr[$i]['bApplication'] =  $app;

	//撈取其他回饋對象
	$tmp = getOtherFeed_case22($cid_arr[$i]['cCertifiedId'],$cid_arr[$i],$branch,$scrivener,$brand);


	// if ($_SESSION['member_id'] == 6) {
	// 	echo 'aAAA';
	// 	header("Content-Type:text/html; charset=utf-8"); 
	// 	echo "<pre>";
	// 	print_r($tmp);
	// 	die;
	// }

	if (is_array($tmp)) {

		$otherFeed = array_merge($otherFeed,$tmp);
	}
	
	unset($app);
}

// echo "<pre>";
// print_r($otherFeed) ; exit ;
//所有店家

for ($i = 0 ; $i < count($realty) ; $i ++) {
	//辨識店家身分
	if ($realty[$i]['bStoreClass'] == '1') { $realty[$i]['bStoreClass'] = '總店' ; }
	else { $realty[$i]['bStoreClass'] = '單店' ; }
	##
	
	//檢核是否有屬於該店之保證號碼
	$index = 0 ;

	for ($j = 0 ; $j < $cid_max ; $j ++) {

		if ($cid_arr[$j]['cFeedbackTarget'] == '1') {					//第一家回饋對象為仲介

			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum'] && $cid_arr[$j]['cBranchNum'] > 0) {			//第一家仲介
				
				//if ($cid_arr[$j]['cCaseFeedBackMoney'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = $cid_arr[$j]['cBranchNum'] ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback'] ;
					//
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = '' ;
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;
					$index ++ ;
				
				//}
				if ($cid_arr[$j]['cSpCaseFeedBackMoney']!=0  && (($cid_arr[$j]['cBrand']!=2 && $cid_arr[$j]['cBrand']!=49 && $cid_arr[$j]['cBrand']!=1)||($cid_arr[$j]['cBrand1']!=2 && $cid_arr[$j]['cBrand1']!=49 && $cid_arr[$j]['cBrand1']!=1)||($cid_arr[$j]['cBrand2']!=2 && $cid_arr[$j]['cBrand2']!=49 && $cid_arr[$j]['cBrand2']!=1))) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = $cid_arr[$j]['cBranchNum'] ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = 0 ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['cSpCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['bcode'] = $cid_arr[$j]['bcode'] ;
					// die($realty[$i]['cId'][$index]['cSpCaseFeedBackMoney']);
					
					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'] ;
					$realty[$i]['cId'][$index]['ck'] = '地政士';
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cScrivener'] = $cid_arr[$j]['cScrivener'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;
					$realty[$i]['cId'][$index]['sOffice'] = $cid_arr[$j]['sOffice'] ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sFeedDateCat'] = $cid_arr[$j]['sFeedDateCat'] ;
					$realty[$i]['cId'][$index]['bCategory2'] = '特殊回饋(地政士)' ;
					$index ++ ;
				}
			}
		}
		else if ($cid_arr[$j]['cFeedbackTarget'] == '2') {	//回饋對象為地政士(一)
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum'] && $cid_arr[$j]['cBranchNum'] > 0) {			//第一家仲介(代表)
				//if ($cid_arr[$j]['cCaseFeedBackMoney'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = '地政士' ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney'] ;
					
					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'] ;
					$realty[$i]['cId'][$index]['ck'] = '地政士';
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cScrivener'] = $cid_arr[$j]['cScrivener'] ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sFeedDateCat'] = $cid_arr[$j]['sFeedDateCat'] ;
					$realty[$i]['cId'][$index]['sOffice'] = $cid_arr[$j]['sOffice'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;
					
					$index ++ ;
				//}
				if ($cid_arr[$j]['cSpCaseFeedBackMoney']!=0 && (($cid_arr[$j]['cBrand']!=2 && $cid_arr[$j]['cBrand']!=49 && $cid_arr[$j]['cBrand']!=1)||($cid_arr[$j]['cBrand1']!=2 && $cid_arr[$j]['cBrand1']!=49 && $cid_arr[$j]['cBrand1']!=1)||($cid_arr[$j]['cBrand2']!=2 && $cid_arr[$j]['cBrand2']!=49 && $cid_arr[$j]['cBrand2']!=1))) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = $cid_arr[$j]['cBranchNum'] ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = 0 ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['cSpCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['bcode'] = $cid_arr[$j]['bcode'] ;
					$realty[$i]['cId'][$index]['ck'] = '地政士';

					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'] ;
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cScrivener'] = $cid_arr[$j]['cScrivener'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;
					$realty[$i]['cId'][$index]['sOffice'] = $cid_arr[$j]['sOffice'] ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sFeedDateCat'] = $cid_arr[$j]['sFeedDateCat'] ;
					$realty[$i]['cId'][$index]['bCategory2'] = '特殊回饋(地政士)' ;
					$index ++ ;
				}
			}
		}	
			
		if ($cid_arr[$j]['cFeedbackTarget1'] == '1') {					//第二家回饋對象為仲介
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum1'] && $cid_arr[$j]['cBranchNum1'] > 0) {		//第二家仲介
				//if ($cid_arr[$j]['cCaseFeedBackMoney1'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = $cid_arr[$j]['cBranchNum1'] ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback1'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney1'] ;
					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = '' ;
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cScrivener'] = '' ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;
					$index ++ ; 
				//}
			}

			
		}	
		else if ($cid_arr[$j]['cFeedbackTarget1'] == '2') {					//回饋對象為地政士(二)
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum1'] && $cid_arr[$j]['cBranchNum1'] > 0) {			//第二家仲介(代表)
				//if ($cid_arr[$j]['cCaseFeedBackMoney1'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = '地政士' ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback1'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney1'] ;
					$realty[$i]['cId'][$index]['ck'] = '地政士';
					
					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'] ;
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cScrivener'] = $cid_arr[$j]['cScrivener'] ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sFeedDateCat'] = $cid_arr[$j]['sFeedDateCat'] ;
					$realty[$i]['cId'][$index]['sOffice'] = $cid_arr[$j]['sOffice'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;



					$index ++ ;
				//}
			}
		}
			
		
		if ($cid_arr[$j]['cFeedbackTarget2'] == '1') {					//第三家回饋對象為仲介
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum2'] && $cid_arr[$j]['cBranchNum2'] > 0) {			//第三家仲介
				//if ($cid_arr[$j]['cCaseFeedBackMoney2'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = $cid_arr[$j]['cBranchNum2'] ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback2'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney2'] ;
					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = '' ;
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cScrivener'] = '' ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;
					$index ++ ;
				//}
			}
		}
		else if ($cid_arr[$j]['cFeedbackTarget2'] == '2') {					//回饋對象為地政士(三)
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum2'] && $cid_arr[$j]['cBranchNum2'] > 0) {			//第三家仲介(代表)
				//if ($cid_arr[$j]['cCaseFeedBackMoney2'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = '地政士' ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback2'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney2'] ;
					$realty[$i]['cId'][$index]['ck'] = '地政士';
					
					$realty[$i]['cId'][$index]['cEndDate'] = $cid_arr[$j]['cEndDate'] ;
					$realty[$i]['cId'][$index]['cSignDate'] = $cid_arr[$j]['cSignDate'] ;
					$realty[$i]['cId'][$index]['cFeedbackTarget'] = $cid_arr[$j]['sCode2'] ;
					$realty[$i]['cId'][$index]['cBank'] = $cid_arr[$j]['cBank'] ;
					$realty[$i]['cId'][$index]['cScrivener'] = $cid_arr[$j]['cScrivener'] ;
					$realty[$i]['cId'][$index]['cCertifiedId'] = $cid_arr[$j]['cCertifiedId'] ;
					$realty[$i]['cId'][$index]['bApplication'] = $cid_arr[$j]['bApplication'] ;
					$realty[$i]['cId'][$index]['sFeedDateCat'] = $cid_arr[$j]['sFeedDateCat'] ;
					$realty[$i]['cId'][$index]['sOffice'] = $cid_arr[$j]['sOffice'] ;
					$realty[$i]['cId'][$index]['sId'] = $cid_arr[$j]['sId'] ;
					$index ++ ;
				//}
			}
		}
	}
	##
	
}
##
// echo "<pre>";
// print_r($realty) ; exit ;
$index = 0 ;
$ct = 0 ;
$total_money = 0 ;
$_arr_index = 0 ;


for ($i = 0 ; $i < count($realty) ; $i ++) {
	if (count($realty[$i]['cId'])&&$realty[$i]['bId']) {		
		for ($j = 0 ; $j < count($realty[$i]['cId']) ; $j ++) {
				//顯示所有資料
				$list[$index] = $realty[$i]['cId'][$j] ;

				
				
				//顯示 "正常/剔除" title
				if ($realty[$i]['cId'][$j]['cCaseFeedback']=='1') {
					$fb = '不回饋' ;
					$list[$index]['cCaseFeedBackMoney'] = 0 ;
				}
				else {
					$fb = '回饋' ;
					//案件總回饋
					$CaseFeedTotal[$list[$index]['cCertifiedId']] += $realty[$i]['cId'][$j]['cCaseFeedBackMoney'];
				}
				##
				
				$list[$index]['bId'] = $realty[$i]['bId'] ;
				$list[$index]['bBrand'] = $realty[$i]['bBrand'] ;
				$list[$index]['bStore'] = $realty[$i]['bStore'] ;
				$list[$index]['bcode'] = $realty[$i]['cId'][$j]['bcode'] ;
				$list[$index]['bCategory'] = category_convert($realty[$i]['bCategory'],$realty[$i]['bBrand']) ;
				$list[$index]['bFeedback'] = $fb ;
				$list[$index]['bFBTarget'] = $list[$index]['cFeedbackTarget'] ;
				$list[$index]['bStoreClass'] = $realty[$i]['bStoreClass'] ;
				$list[$index]['bClassBranch'] = $realty[$i]['bClassBranch'] ;
				$list[$index]['sId'] = $realty[$i]['cId'][$j]['sId'];
				if (substr($list[$index]['bFBTarget'], 0,2) != 'SC' ) {
						$list[$index]['bFeedDateCat'] = $realty[$i]['bFeedDateCat'] ;
					}

				##數量
					$count[$list[$index]['cCertifiedId']] = $count[$list[$index]['cCertifiedId']]+1;
				##
				
				$ct ++ ;
				$total_money += $list[$index]['cCaseFeedBackMoney'] + 1 - 1 ;

				$index ++ ;
			
			


		}

	}
}



//echo "總共：".$ct." 筆, 共計：".$total_money."元" ;
$xx = count($list);


// echo "<pre>";
// print_r($otherFeed);
// die;
$monthData = array();
$monthData_CC = array();
foreach ($otherFeed as $key => $value) {

	$date = substr($trans[$value['cCertifiedId']], 0,7);
	$tmp_scr = explode(',', $scrivener);
	$tmp_br = explode(',', $branch);

	

	if (substr($value['bFBTarget'], 0,2) == 'SC') {
		$sId = substr($value['bFBTarget'], 2);
			
		if (in_array($sId, $tmp_scr)) {
			$monthData_CC[$value['cCertifiedId']]['date'] = $date;
			$monthData_CC[$value['cCertifiedId']]['cCertifiedMoney'] = $value['cCertifiedMoney'];
			$monthData_CC[$value['cCertifiedId']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];

			
		}
		
		// if ($value['cCertifiedId'] == '003048854') {
		// 	echo 'DD';
		// 	print_r($value);
		// 	die;
		// }


			
	}elseif (in_array($value['cBranchNum'], $tmp_br)) {
		$monthData_CC[$value['cCertifiedId']]['date'] = $date;
		$monthData_CC[$value['cCertifiedId']]['cCertifiedMoney'] = $value['cCertifiedMoney'];
		$monthData_CC[$value['cCertifiedId']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];
		
		// if ($value['cCertifiedId'] == '003048854') {
		// 	echo 'AAAA';
		// 	print_r($value);
		// 	// die;
		// }
	}else{

		$monthData_CC[$value['cCertifiedId']]['date'] = $date;
		$monthData_CC[$value['cCertifiedId']]['cCertifiedMoney'] = $value['cCertifiedMoney'];
		$monthData_CC[$value['cCertifiedId']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];
	}

	

}
// echo "<pre>";
// print_r($monthData_CC);

// die;
$max = count($list) ;

##

$total = array();

foreach ($list as $key => $value) {

	$date = substr($trans[$value['cCertifiedId']], 0,7);

	

	if ($value['cBranchNum'] == '地政士' || $value['bStore'] == '非仲介成交') {
		if (in_array($value['sId'], $tmp_scr)) {
			$monthData[$value['cCertifiedId']]['date'] = $date;
			$monthData[$value['cCertifiedId']]['cCertifiedMoney'] = $value['cCertifiedMoney'];
			$monthData[$value['cCertifiedId']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];

			
			
		}
	}else{
		$monthData[$value['cCertifiedId']]['date'] = $date;
		$monthData[$value['cCertifiedId']]['cCertifiedMoney'] = $value['cCertifiedMoney'];
		$monthData[$value['cCertifiedId']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];

	}
	
	// print_r($value);
	

}
// unset($list);

$fw = fopen('txt/casefeed2021.txt', 'a+');

foreach ($monthData as $key => $value) {

	
	

	if ($value['date'] == '') {
		// echo $key."_".$value['cCertifiedMoney']."_".$value['cCaseFeedBackMoney']."<br>";

		// die;

		if ($key == '003008990') {
			$total['2020-05']['cCertifiedMoney'] += $value['cCertifiedMoney'];
			$total['2020-05']['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];
		}

		$total[$key] = $key."_".$value['cCertifiedMoney']."_".$value['cCaseFeedBackMoney']."<br>";
		// print_r(expression);
		// die;
	}else{
		$total[$value['date']]['cCertifiedMoney'] += $value['cCertifiedMoney'];
		$total[$value['date']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];


		$total[$value['date']]['cCaseFeedBackMoney'] +=$monthData_CC[$value['cCertifiedId']]['cCaseFeedBackMoney'];

		$txt = $key."_".$value['cCertifiedMoney']."_".($value['cCaseFeedBackMoney']+$monthData_CC[$key]['cCaseFeedBackMoney'])."_".$value['date']
	."\r\n";
	fwrite($fw, $txt);
	}

	


	

	
}

fclose($fw);

echo "<pre>";
print_r($total);
// unset($list);

die;

//仲介類型轉碼
Function category_convert($str='0',$code='') {
	switch ($str) {
		case '1' :
			
			if ($code=='TH') {
				$str = '加盟(台屋)';
			}elseif ($code=='UM') {
				$str = '加盟(優美)';
			}else{
				$str = '加盟(其他)';
			}
					
					break ;
		case '2' :
					$str = '直營' ;
					break ;
		case '3' :
					$str = '非仲介成交' ;
					break ;
		default :
					$str = '未知' ;
					break ;
	}
	return $str ;
}
?>