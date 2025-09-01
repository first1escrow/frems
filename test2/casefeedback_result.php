<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
 header("Content-Type:text/html; charset=utf-8"); 
include_once '../includes/maintain/feedBackData.php';
include_once '../openadodb.php' ;

$invert_result = 2 ;

$bb  = array();$ss = array();
$sql = "SELECT zZip FROM tZipArea WHERE ZCity IN('台中市','彰化縣','南投縣')";
$rs = $conn->Execute($sql);
$listZip = array();
while (!$rs->EOF) {
	array_push($listZip, $rs->fields['zZip']);
	$rs->MoveNext();
}

				//搜尋分店
	// 店名
	$_cond = ' AND (bZip IN('.implode(',', $listZip).') OR bId = 505)' ;
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

// $_cond = 'AND tra.tBankLoansDate>="2020-01-01" AND tra.tBankLoansDate<="2021-10-31"' ;
// $_cond2 = 'AND cBankList>="2020-01-01" AND cBankList<="2020-10-31"' ;


$_cond = 'AND tra.tBankLoansDate>="2020-06-01" AND tra.tBankLoansDate<="2020-12-31"' ;
$_cond2 = 'AND cBankList>="2020-06-01" AND cBankList<="2020-12-31"' ;

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
if ($contractDate) $_sql = 'SELECT cCertifiedId FROM tContractCase WHERE '.$contractDate.$_cond2;
else $_sql = 'SELECT cCertifiedId FROM tContractCase WHERE cBankList<>"" '.$_cond2.'  ;' ;

$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$trans[$rs->fields['cCertifiedId']] = $rs->fields['tBankLoansDate'];
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
	$tmp = getOtherFeed_case($cid_arr[$i]['cCertifiedId'],$cid_arr[$i],$branch,$scrivener,$brand);


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
// print_r($cid_arr) ; exit ;
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




foreach ($otherFeed as $key => $value) {

	$date = substr($trans[$value['cCertifiedId']], 0,7);



	$tmp_scr = explode(',', $scrivener);
	$tmp_br = explode(',', $branch);
	if ($value['bFBTarget'] != '' ) {
		$sId = substr($value['bFBTarget'], 2);
			
		if (in_array($sId, $tmp_scr)) {
			$monthData[$value['cCertifiedId']]['date'] = $date;
			$monthData[$value['cCertifiedId']]['cCertifiedMoney'] = $value['cCertifiedMoney'];
			$monthData[$value['cCertifiedId']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];

			
		}
		
		


			
	}elseif (in_array($value['cBranchNum'], $tmp_br)) {
		$monthData[$value['cCertifiedId']]['date'] = $date;
		$monthData[$value['cCertifiedId']]['cCertifiedMoney'] = $value['cCertifiedMoney'];
		$monthData[$value['cCertifiedId']]['cCaseFeedBackMoney'] += $value['cCaseFeedBackMoney'];
		

	}


}



$max = count($list) ;

##
//
// echo "<pre>";
// print_r($list);
// // // // // echo "</pre>";
// die;
// print_r($trans);
// die;
$total = array();
$monthData = array();
foreach ($list as $key => $value) {

	$date = substr($trans[$value['cCertifiedId']], 0,7);

	

	if ($value['cBranchNum'] == '地政士') {
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
	
	

	// if ($value['cCertifiedId'] == '	090101845') {
	// 	print_r($value)
	// }

	// print_r($monthData);
	// die;

}
unset($list);
// print_r($value);
// die;

foreach ($monthData as $key => $value) {

	// echo $value['date'];
	// die;
	

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
	}
	
}
// unset($list);
print_r($total);
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