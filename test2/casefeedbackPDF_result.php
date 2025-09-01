<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../includes/maintain/feedBackData.php';
// include_once 'feedBackData.php';
include_once '../session_check.php' ;



//預載log物件
$logs = new Intolog() ;
##
$_POST = escapeStr($_POST) ;
$bank = $_POST['bank'] ;//查詢銀行系統
$bStoreClass = $_POST['bStoreClass'] ;				//查詢店身份 (總店:1、單店:2)
$sales_year = $_POST['sales_year'] ;				//查詢回饋年度
$sales_season = $_POST['sales_season'] ;			//查詢回饋季
$certifiedid = $_POST['certifiedid'] ;				//查詢保證號碼
$bCategory = $_POST['bCategory'] ;					//查詢仲介商類型 (加盟:1、直營:2)
$branch = $_POST['branch'];
$scrivener = $_POST['scrivener'];
$storeSearch = $_POST['bck'];
$filetype =$_POST['filetype'];
##類別##
$CatArr = explode(',', $bCategory);

for ($i=0; $i < count($CatArr); $i++) { 
	if ($CatArr[$i] == 1) {
		$CatArr[] = "加盟";
	}elseif ($CatArr[$i] == 2) {
		$CatArr[] = "直營";
	}elseif ($CatArr[$i] == 3) {
		$CatArr[] = "非仲介成交";
		$CatArr[] = "特殊回饋(地政士)(回饋)";
		$CatArr[] = "地政士";

	}

	if ($CatArr[$i] == 1 || $CatArr[$i] == 2) {
		$CatArr[] = "特殊回饋(其他)(回饋)";
	}
}
##


//仲介類型轉碼
function category_convert($str='0',$code='') {
	switch ($str) {
		case '1' :
			
		
		$str = '加盟';			
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
##

// 找出愈搜尋的店身份(總店:1、單店:2)
if ($bStoreClass=="1") {	//搜尋總店	
	//bStoreClass = 總店/單店、bCategory = 加盟/直營、branch = 店編號
	// 店名
	$_cond = '' ;
	// if ($branch) {	
	// 	$_cond .= ' AND a.bId="'.$branch.'" ' ;
	// }

	// if ($branch && $storeSearch == 1) {	
		
	// 	$_cond .= ' AND a.bId IN ('.$branch.')' ;
	// }
	// ##
	
	// //仲介類型
	// if ($bCategory && $storeSearch == '' && $branch == '') { //只查店
	// 	// $_cond .= ' AND bCategory="'.$bCategory.'" ' ;

	// 	if ($scrivener != '' || $branch != '') {
	// 		$bCategory2 =$bCategory.",3";
	// 	}else{
	// 		$bCategory2 = $bCategory;
	// 	}

	// 	$_cond .= ' AND bCategory IN('.$bCategory2.') ' ;
	// }
	##
	
	//找出所有總店
	$bsql = '
	SELECT 
		bId,
		(SELECT bCode FROM tBrand WHERE bId=a.bBrand) as bBrand,
		bStore,
		bCategory,
		bStoreClass,
		bClassBranch
	FROM
		tBranch AS a
	WHERE
		bStoreClass="1" 
		AND bStatus="1"
		'.$_cond.'
	ORDER BY
		bId
	ASC;
	' ;
	
	$i = 0 ;
	$rs = $conn->Execute($bsql);
	while (!$rs->EOF) {
		$realty[$i] = $rs->fields;
		$realty_arr = explode(';',$realty[$i]['bClassBranch']) ;
		for ($j = 0 ; $j < count($realty_arr) ; $j ++) {
			$realty_arr[$j] = preg_replace("/^[a-zA-Z]+/","",$realty_arr[$j]) ;
			$realty_arr[$j] = $realty_arr[$j] + 1 - 1 ;
		}

		foreach ($realty_arr as $k => $v) {
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
					bId="'.$v.'"
					AND bStatus="1"
				ORDER BY
					bId
				ASC;
			' ;
			$rs = $conn->Execute($bsql);
			while (!$rs->EOF) {
				$realty[++$i] = $rs->fields ;

				$rs->MoveNext();
			}
			
		}

		$i++;
		$rs->MoveNext();
	}


	
}
else {						//搜尋分店
	// 店名
	$_cond = '' ;
	// if ($branch) {
	// 	$_cond .= ' AND a.bId="'.$branch.'" ' ;
	// }
	// if ($branch && ($scrivener == 0)) {	
		
	// 	if ($bck ==1){
	// 		$_cond .= ' AND a.bId IN ('.$branch.')' ;
	// 	}else{
	// 		$_cond .= ' AND a.bId="'.$branch.'" ' ;
	// 	}
	// }
	// print_r($branch);

	// if ($branch && $storeSearch == 1) {	
		
	// 	$_cond .= ' AND a.bId IN ('.$branch.')' ;
	// }
	##

	//仲介類型
	// if ($bCategory) {
	// 	$_cond .= ' AND bCategory="'.$bCategory.'" ' ;
	// }
	// if ($bCategory && $storeSearch == '' && $branch == '') {
	// 	// $_cond .= ' AND bCategory="'.$bCategory.'" ' ;
	// 	if ($scrivener != '') {
	// 		$bCategory2 =$bCategory.",3";
	// 	}else{
	// 		$bCategory2 = $bCategory;
	// 	}

	// 	$_cond .= ' AND bCategory IN('.$bCategory2.') ' ;
	// }

	##
	
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

		$rs->MoveNext();
	}
	
	
}
##
////建立搜尋條件
$_cond = '' ;
// 銀行
if ($bank) {	
	$_cond .= ' AND cas.cBank="'.$bank.'"' ;

}
##

// 保證號碼
if ($certifiedid) {	
	$_cond .= ' AND cas.cCertifiedId="'.$certifiedid.'"' ;
	$_cond2  = ' AND cCertifiedId="'.$certifiedid.'"';
}
##

// 年度季別
$date_range = '' ;
$contractDate = '' ;
if ($sales_year && $sales_season) {	
	switch ($sales_season) {
		case 'S1' : 
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-01-01" AND tra.tBankLoansDate<="'.$sales_year.'-03-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-01-01" AND cBankList<="'.$sales_year.'-03-31"' ;
				$sales_season1 = '第1季' ;
				break ;
		case 'S2' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-04-01" AND tra.tBankLoansDate<="'.$sales_year.'-06-30"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-04-01" AND cBankList<="'.$sales_year.'-06-30"' ;
				$sales_season1 = '第2季' ;
				break ;
		case 'S3' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-07-01" AND tra.tBankLoansDate<="'.$sales_year.'-09-30"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-07-01" AND cBankList<="'.$sales_year.'-09-30"' ;
				$sales_season1 = '第3季' ;
				break ;
		case 'S4' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-10-01" AND tra.tBankLoansDate<="'.$sales_year.'-12-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-10-01" AND cBankList<="'.$sales_year.'-12-31"' ;
				$sales_season1 = '第4季' ;
				break ;
		default :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-'.$sales_season.'-01" AND tra.tBankLoansDate<="'.$sales_year.'-'.$sales_season.'-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-'.$sales_season.'-01" AND cBankList<="'.$sales_year.'-'.$sales_season.'-31"' ;
				$sales_season1 = preg_replace("/^0/","",$sales_season).'月份' ;
				break ;
	}
	$_cond .= ' AND '.$date_range ;
}
##
//


##
// 其他限制條件(案件狀態須為："已結案":3 或"解約/終止履保":4 或"發函終止":9)
// $_cond1 = ' AND cas.cCaseStatus IN ("3","4","9")' ;


##
####

//先選取期間所有保證號碼(因 tBankTrans 問題)與相關資料
//--取出範圍內保證號碼--
//if (!$date_range) {
//	$date_range = '1' ;
//}

//取得合約銀行帳號
$_sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" GROUP BY cBankAccount ORDER BY cId ASC;' ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$conBank[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}

$conBank_sql = implode('","',$conBank) ;
##
//tra.tObjKind IN ("點交(結案)","解除契約","建經發函終止") 不用了
//20180103 改成(保證費+銀行放款時間) 
//20200205 家津
// 請幫我刪一筆081239015
// 這筆109/1/2有出款到公司,但不是履保費,是其他款項
// 我1月的回饋報表裡請直接刪除它
// 金額是$1700
// 我108/12月回饋過了(那時才是收履保費的時點)
//090025288這件回存履保費 8/18 回饋先不計算
$_sql = '
	SELECT 
		tra.tMemo as cCertifiedId,
		(SELECT bApplication FROM tBankCode WHERE bAccount=tra.tVR_Code) AS bApplication
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
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$cid_arr[] = $rs->fields ;

	$rs->MoveNext();
}

##
// print_r($cid_arr);
//取出範圍內未收履保費但仍要回饋(有利息)的案件
if ($contractDate) $_sql = 'SELECT cCertifiedId FROM tContractCase WHERE '.$contractDate.$_cond2;
else $_sql = 'SELECT cCertifiedId FROM tContractCase WHERE cBankList<>"" '.$_cond2.' ORDER cEndDate ASC ;' ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
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
	$tmp = getOtherFeed_case($cid_arr[$i]['cCertifiedId'],$cid_arr[$i],$branch,$scrivener);
	
	if (is_array($tmp)) {



		$otherFeed = array_merge($otherFeed,$tmp);
	}
	
	unset($app);unset($tmp);
}

// exit();
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

			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum']) {			//第一家仲介
				
				//if ($cid_arr[$j]['cCaseFeedBackMoney'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = $cid_arr[$j]['cBranchNum'] ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback'] ;
					$realty[$i]['cId'][$index]['bCategory'] = category_convert($realty[$i]['bCategory'],$cid_arr[$j]['bBrand']) ;
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
				if ($cid_arr[$j]['cSpCaseFeedBackMoney']!=0 && empty($branch) && (($cid_arr[$j]['cBrand']!=2 && $cid_arr[$j]['cBrand']!=49 && $cid_arr[$j]['cBrand']!=1)||($cid_arr[$j]['cBrand1']!=2 && $cid_arr[$j]['cBrand1']!=49 && $cid_arr[$j]['cBrand1']!=1)||($cid_arr[$j]['cBrand2']!=2 && $cid_arr[$j]['cBrand2']!=49 && $cid_arr[$j]['cBrand2']!=1))) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = $cid_arr[$j]['cBranchNum'] ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = 0 ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['cSpCaseFeedBackMoney'] = $cid_arr[$j]['cSpCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['bcode'] = $cid_arr[$j]['bcode'] ;
					$realty[$i]['cId'][$index]['bCategory'] = '特殊回饋(地政士)(回饋)' ;
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
					$index ++ ;
				}
			}
		}
		else if ($cid_arr[$j]['cFeedbackTarget'] == '2') {	//回饋對象為地政士(一)
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum']) {			//第一家仲介(代表)
				//if ($cid_arr[$j]['cCaseFeedBackMoney'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = '地政士' ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney'] ;
					$realty[$i]['cId'][$index]['bCategory'] = '地政士' ;
					
					
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
					$realty[$i]['cId'][$index]['bCategory'] = '特殊回饋(地政士)(回饋)' ;
					

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
					$index ++ ;
				}
			}
		}	
			
		if ($cid_arr[$j]['cFeedbackTarget1'] == '1') {					//第二家回饋對象為仲介
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum1']) {		//第二家仲介
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
					$realty[$i]['cId'][$index]['bCategory'] = category_convert($realty[$i]['bCategory'],$cid_arr[$j]['bBrand1']) ;
					
					$index ++ ; 
				//}
			}
		}
		else if ($cid_arr[$j]['cFeedbackTarget1'] == '2') {					//回饋對象為地政士(二)
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum1']) {			//第二家仲介(代表)
				//if ($cid_arr[$j]['cCaseFeedBackMoney1'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = '地政士' ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback1'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney1'] ;
					$realty[$i]['cId'][$index]['ck'] = '地政士';
					$realty[$i]['cId'][$index]['bCategory'] = '地政士' ;
					
					
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
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum2']) {			//第三家仲介
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
					$realty[$i]['cId'][$index]['bCategory'] = category_convert($realty[$i]['bCategory'],$cid_arr[$j]['bBrand2']) ;
					
					$index ++ ;
				//}
			}
		}
		else if ($cid_arr[$j]['cFeedbackTarget2'] == '2') {					//回饋對象為地政士(三)
			if ($realty[$i]['bId']==$cid_arr[$j]['cBranchNum2']) {			//第三家仲介(代表)
				//if ($cid_arr[$j]['cCaseFeedBackMoney2'] > 0) {
					$realty[$i]['cId'][$index]['buyer'] = $cid_arr[$j]['buyer'] ;
					$realty[$i]['cId'][$index]['owner'] = $cid_arr[$j]['owner'] ;
					$realty[$i]['cId'][$index]['cBranchNum'] = '地政士' ;
					$realty[$i]['cId'][$index]['cTotalMoney'] = $cid_arr[$j]['cTotalMoney'] ;
					$realty[$i]['cId'][$index]['cCertifiedMoney'] = $cid_arr[$j]['cCertifiedMoney'] ;
					$realty[$i]['cId'][$index]['cCaseFeedback'] = $cid_arr[$j]['cCaseFeedback2'] ;
					$realty[$i]['cId'][$index]['cCaseFeedBackMoney'] = $cid_arr[$j]['cCaseFeedBackMoney2'] ;
					$realty[$i]['cId'][$index]['ck'] = '地政士';
					$realty[$i]['cId'][$index]['bCategory'] = '地政士' ;
					
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

//print_r($realty) ; exit ;
$index = 0 ;
$ct = 0 ;
$total_money = 0 ;
$_arr_index = 0 ;



for ($i = 0 ; $i < count($realty) ; $i ++) {
	if (count($realty[$i]['cId'])&&$realty[$i]['bId']) {		
		for ($j = 0 ; $j < count($realty[$i]['cId']) ; $j ++) {
			if ($realty[$i]['cId'][$j]['cCaseFeedback']=='0') {			//要回饋		
					$list[$index] = $realty[$i]['cId'][$j] ;
				
					$list[$index]['bId'] = $realty[$i]['bId'] ;
					$list[$index]['bBrand'] = $realty[$i]['bBrand'] ;
					
					$list[$index]['bcode'] = $realty[$i]['cId'][$j]['bcode'] ;
					// if ($list[$index]['cSpCaseFeedBackMoney'] > 0) {
					// 	$list[$index]['bCategory'] = '特殊回饋(地政士)(回饋)';
					// }else{
					// 	$list[$index]['bCategory'] = category_convert($realty[$i]['bCategory'],$realty[$i]['bBrand']) ;
					
					// }
					$list[$index]['bFeedback'] = '回饋' ;
					$list[$index]['bFBTarget'] = $list[$index]['cFeedbackTarget'] ;

					if ($list[$index]['bFBTarget'] == '') {
						$list[$index]['bFBTarget'] = $realty[$i]['bBrand'].str_pad($realty[$i]['bId'], 5,0,STR_PAD_LEFT);
						$list[$index]['bStore'] = $realty[$i]['bStore'] ;
					}else{
						$list[$index]['bStore'] = $list[$index]['sOffice'] ;
					}


					$list[$index]['bStoreClass'] = $realty[$i]['bStoreClass'] ;
					$list[$index]['bClassBranch'] = $realty[$i]['bClassBranch'] ;
					$list[$index]['bFeedDateCat'] = $realty[$i]['bFeedDateCat'] ;

					##數量
					$count[$list[$index]['cCertifiedId']] = $count[$list[$index]['cCertifiedId']]+1;
					##
				
					$ct ++ ;
					$total_money += $list[$index]['cCaseFeedBackMoney'] + 1 - 1 ;
					
					$index ++ ;
			}


		}

	}
}

//echo "總共：".$ct." 筆, 共計：".$total_money."元" ;
$xx = count($list);

	
//將其他回饋對象，加進原本的資料陣列
for ($i=0; $i < count($otherFeed); $i++) { 
	if (preg_match("/直營/", $otherFeed[$i]['bStore'])) {
		$otherFeed[$i]['bCategory'] = '直營';
	}elseif (preg_match("/加盟/", $otherFeed[$i]['bStore'])) {
		$otherFeed[$i]['bCategory'] = '加盟';
	}
	$list[($xx+$i)] = $otherFeed[$i];
	##數量
		$count[$otherFeed[$i]['cCertifiedId']] = $count[$otherFeed[$i]['cCertifiedId']]+1;
	##
	$total_money = $total_money +$otherFeed[$i]['cCaseFeedBackMoney'];
}

$max = count($list) ;

// 以案件結案日期排序
for ($i = 0 ; $i < $max ; $i ++) {
	for ($j = 0 ; $j < $max - 1 ; $j ++) {
		if ($list[$j]['cEndDate'] > $list[$j+1]['cEndDate']) {
			$tmp = $list[$j] ;
			$list[$j] = $list[$j+1] ;
			$list[$j+1] = $tmp ;
			unset($tmp) ;
		}
	}
}
##
// $bCategory
// if ($_SESSION['member_id'] == 6) {
// 		if ($list[$i]['cCertifiedId'] == '004107118 ') {
// 		echo $bCategory."_".$storeSearch."_".$list[$i]['bCategory'];
// 		print_r($CatArr);
// 		}
// 	}



$max = count($list) ;
if ($scrivener) {
	$scrArr = explode(',', $scrivener);
}

if ($branch) {
	$branchArr = explode(',', $branch);
}
for ($i = 0 ; $i < $max ; $i ++) {


	$code = (substr($list[$i]['bFBTarget'], 0,2) == 'SC')?'s':'b';
	$codeId = substr($list[$i]['bFBTarget'],2);
	$code2 = $code.$codeId;
	$check = false;
	

	// //不查詢地政士且無查詢單一地政士
	// if (!preg_match("/3/", $bCategory)) {
	// 	if ($code == 'SC' && $scrivener == '') {
	// 		continue;
	// 	}
	// }

	// //只查詢單一地政士
	// if ($scrivener != '' && $code == 'SC') { 
	// 	if (!in_array((int)substr($list[$i]['bFBTarget'],2), $scrArr)) {
	// 		continue;
	// 	}
	// }
	//查單一地政是
	if ($scrivener) {
		// print_r($scrArr);
		if (in_array((int)$codeId, $scrArr) && $code == 's') {
			$check = true;
			
		}

		// echo 'GO';

	}

	if ($branch) {
		if (in_array((int)$codeId, $branchArr) && $code != 's') {
			$check = true;
		}
	}

	//有仲介商類型且類別要一致或有查詢額外的店跟地政士
	
	// if ($list[$i]['cCertifiedId'] == '006108323') {
	// 	echo $bCategory."_".$storeSearch."_".$list[$i]['bCategory'];
	// 	print_r($CatArr);
	// }

	

	if (($bCategory && $storeSearch == '') && in_array($list[$i]['bCategory'], $CatArr)) {

		$check = true;
	}


	if ($list[$i]['cCaseFeedBackMoney'] == 0 ) { //回饋金為0濾掉
		$check = false;
	}



	
	if ($check) {


		$checkArr[] =  $list[$i];

		$data[$code2]['data'][] = $list[$i];
		$data[$code2]['feedbackMoney'] += (int)$list[$i]['cCaseFeedBackMoney'];//回饋金
		$data[$code2]['cCertifiedMoney'] += (int)$list[$i]['cCertifiedMoney'];//保證費
		$data[$code2]['total'] += (int)$list[$i]['cTotalMoney'];//總價金
		$data[$code2]['storeId'] = $list[$i]['bFBTarget'];//店編號號
		$data[$code2]['storeName'] = $list[$i]['bStore'];//店名稱
	}else{
		$ERROR[]=  $list[$i];
	}

	
	
	
}
####

// echo "<pre>";
// print_r($data);
// die;
if (is_array($data)) {
	ksort($data);
}


// if ($_SESSION['member_id'] == 6) {
		
// 			// echo $bCategory."_".$storeSearch."_".$list[$i]['bCategory'];
// 			// print_r($CatArr);
// 			echo "<pre>";
// 			print_r($data);
// 			die;
		
// 	}

if (count($data) > 0) {
	$cat = 1;

	if ($filetype == 'excel') {
		include_once dirname(__FILE__).'/pdf/excel.php' ;
	}else{
		set_time_limit(0);
		include_once dirname(__FILE__).'/pdf/pdfPrint.php' ;
		include_once dirname(__FILE__).'/pdf/excel.php' ;
	}
	
}else{
	$cat = 0;
}
// if ($_SESSION['member_id'] == 6) {
// 	echo "<pre>";
// 	print_r($data);
// 	echo 'GO';
// 	die;
// }


// echo "<pre>";
// print_r($data);
// echo "</pre>";
// die;
// echo implode(',', $checkArr);
// print_r($CatArr);


// if ($exports == 'ok') {
// 	$logs->writelog('casefeedbackExcel') ;
// 	if ($branch || $scrivener) {
		
// 		include_once 'casefeedback_excelmonth.php' ;
// 	}else{
// 		include_once 'casefeedback_excel.php' ;
// 	}
	
// 	die;
// }

// echo "<pre>";
// print_r($list);
// echo "</pre>";
##

// echo count($tmp);

//$max = $ct ;
// 計算總頁數

# 搜尋資訊
$smarty->assign('bank',$bank) ;
$smarty->assign('bStoreClass',$bStoreClass) ;
$smarty->assign('branch',$branch) ;
$smarty->assign('sales_year',$sales_year) ;
$smarty->assign('sales_season',$sales_season) ;
$smarty->assign('certifiedid',$certifiedid) ;
$smarty->assign('bCategory',$bCategory) ;
$smarty->assign('scrivener',$scrivener) ;
$smarty->assign('storeSearch',$storeSearch) ;
# 搜尋資訊
$smarty->assign('link',$link) ;
$smarty->assign('cat',$cat) ;
$smarty->assign('link2',$link2) ;
$smarty->assign('link3',$link3) ;
$smarty->display('casefeedbackPDF_result.inc.tpl', '', 'report');
?>