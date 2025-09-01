<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
include_once '../openadodb.php' ;
include_once 'includes/maintain/feedBackData.php';

$tlog = new TraceLog() ;

//預載log物件
$logs = new Intolog() ;
##

 $_POST = escapeStr($_POST) ;
##

// echo 'AAA';
// $_POST['sApplyDate'] = '104-10-01 00:00:00';
// $_POST['eApplyDate'] = '104-10-20 23:59:59';
// $_POST['current_page'] = 2;

$xls = $_POST['xls'] ;

$bank = $_POST['bank'] ;
$sApplyDate = $_POST['sApplyDate'] ;
$eApplyDate = $_POST['eApplyDate'] ;
$sEndDate = $_POST['sEndDate'] ;
$eEndDate = $_POST['eEndDate'] ;
$sSignDate = $_POST['sSignDate'] ;
$eSignDate = $_POST['eSignDate'] ;
$branch = $_POST['branch'] ;
$scrivener = $_POST['scrivener'] ;
$zip = $_POST['zip'] ;
$citys = $_POST['citys'] ;
$brand = $_POST['brand'] ;
$undertaker = $_POST['undertaker'] ;
$status = $_POST['status'] ;
$realestate = $_POST['realestate'] ;
$cCertifiedId = $_POST['cCertifiedId'] ;
$buyer = $_POST['buyer'] ;
$owner = $_POST['owner'] ;
$show_hide = $_POST['show_hide'] ;
$scrivener_category = $_POST['scrivener_category'];
$sales = $_POST['sales'];

$total_page = $_POST['total_page'] + 1 - 1 ;
$current_page = $_POST['current_page'] + 1 - 1 ;
$record_limit = $_POST['record_limit'] + 1 - 1 ;

if (!$record_limit) { $record_limit = 10 ; }
##

$query = '' ; 
$functions = '' ;

$sad = $sApplyDate ;
$ead = $eApplyDate ;
$sed = $sEndDate ;
$eed = $eEndDate ;
$ssd = $sSignDate ;
$esd = $eSignDate ;
$br = $branch ;
$sc = $scrivener ;
$byr = $buyer ;
$owr = $owner ;


##

//取得所有出款保證費紀錄("27110351738","10401810001889","20680100135997")

##

$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子
$queryO = ' AND fb.fDelete = 0 ';
// 搜尋條件-銀行別
if ($bank) {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cBank="'.$bank.'" ' ;
}

// 搜尋條件-進案日期
if ($sApplyDate) {
	$tmp = explode('-',$sApplyDate) ;
	$sApplyDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate>="'.$sApplyDate.' 00:00:00" ' ;
}
if ($eApplyDate) {
	$tmp = explode('-',$eApplyDate) ;
	$eApplyDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate<="'.$eApplyDate.' 23:59:59" ' ;
}
##

##

// 搜尋條件-結案日期
if ($sEndDate) {
	$tmp = explode('-',$sEndDate) ;
	$sEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate>="'.$sEndDate.' 00:00:00" ' ;

}
if ($eEndDate) {
	$tmp = explode('-',$eEndDate) ;
	$eEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate<="'.$eEndDate.' 23:59:59" ' ;
}
##

// 搜尋條件-簽約日期
if ($sSignDate) {
	$tmp = explode('-',$sSignDate) ;
	$sSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
}
if ($eSignDate) {
	$tmp = explode('-',$eSignDate) ;
	$eSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;
}
##

// 搜尋條件-仲介店
if ($branch) {
	if ($query) { $query .= " AND " ; }
	$query .= ' (rea.cBranchNum="'.$branch.'" OR rea.cBranchNum1="'.$branch.'" OR rea.cBranchNum2="'.$branch.'" OR rea.cBranchNum3="'.$branch.'") ' ;
	if ($queryO) { $queryO .= " AND " ; }
	$queryO .= "fb.fStoreId = '".$branch."'";
}

// 搜尋條件-地政士
if ($scrivener) {
	if ($query) { $query .= " AND " ; }
	$query .= ' csc.cScrivener="'.$scrivener.'" ' ;
}

// 搜尋條件-買方姓名
if ($buyer) {
	if ($query) { $query .= " AND " ; }
	$query .= ' buy.cId="'.$buyer.'" ' ;
}

// 搜尋條件-賣方姓名
if ($owner) {
	if ($query) { $query .= " AND " ; }
	$query .= ' own.cId="'.$owner.'" ' ;
}

// 搜尋條件-保證號碼
if ($cCertifiedId) {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCertifiedId="'.$cCertifiedId.'" ' ;
}

// 搜尋條件-仲介品牌
//if ($brand) {
if (($brand != '') ) {
	if ($query) { $query .= " AND " ; }
	$query .= ' (rea.cBrand="'.$brand.'" OR rea.cBrand1="'.$brand.'" OR rea.cBrand2="'.$brand.'" OR rea.cBrand3="'.$brand.'") ' ;

	if ($queryO) { $queryO .= " AND " ; }
	$queryO .= "b.bBrand = '".$brand."'";
}

//仲介商類型
if ($realestate) {
	
	if ($queryO) { $queryO .= " AND " ; }

	if ($realestate == 11) {//加盟(其他品牌)
		
		$queryO .= "(b.bBrand != '1' AND b.bBrand != '49' AND b.bBrand != '56' AND b.bBrand != '2' AND b.bCategory = 1)";
		
	}else if ($realestate == 12) { //加盟(台灣房屋)
		$queryO .= "(b.bBrand = '1' AND b.bCategory = 1)";
	
	}elseif ($realestate == 13) { //加盟(優美地產)
		$queryO .= "(b.bBrand = '49' AND b.bCategory = 1)";
	}elseif ($realestate == 14) { //加盟(永春不動產)
		$queryO .= "(b.bBrand = '56' AND b.bCategory = 1)";
	}elseif ($realestate == 1) { //加盟
		$queryO .= "b.bCategory = 1";
	}elseif ($realestate == 2) { //直營
		$queryO .= "b.bCategory = 2";
	}elseif ($realestate == 3) { //非仲介成交
		$queryO .= "b.bBrand = 2";
	}elseif ($realestate == 5) { //台屋集團
		$queryO .= "(b.bBrand = 1 AND b.bBrand = 49)";
	}elseif ($realestate == 6) { //他牌+非仲
		$queryO .= "(b.bBrand != 1 AND b.bBrand != 49)";
	}else{

	}

	
}


// 搜尋條件-地區
if ($zip) {
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip="'.$zip.'" ' ;
}
else if ($citys) {
	$zipArr = array() ;
	$zipStr = '' ;
	$sql = 'SELECT zZip FROM tZipArea WHERE zCity="'.$citys.'" ORDER BY zCity,zZip ASC;' ;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$zipArr[] = $rs->fields['zZip'] ;

		$rs->MoveNext();
	}

	
	$zipStr = implode('","',$zipArr) ;
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip IN ("'.$zipStr.'") ' ;
	unset($zipArr) ;
	unset($zipStr) ;
}

##

// 搜尋條件-承辦人
if ($undertaker) {
	if ($query) { $query .= " AND " ; }
	$query .= ' scr.sUndertaker1="'.$undertaker.'" ' ;
}

// 搜尋條件-案件狀態
if ($status) {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus="'.$status.'" ' ;
}
else {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus<>"8" ' ;
}

if ($status=='3') {
	$t_day = '結案日期' ;
}
else {
	$t_day = '簽約日期' ;
}
##
//地政士類別
if ($scrivener_category) {
	if ($query) { $query .= " AND "  ; }
	$query .= "scr.sBrand ='2' AND scr.sCategory='1'";
}

if ($query) { $query = ' WHERE '.$query ; }
//LEFT JOIN
//	tBankTrans AS tra ON tra.tMemo=cas.cCertifiedId AND tra.tAccount="27110351738"
//	tra.tMoney tMoney,

$query ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate,
	cas.cEndDate as cEndDate, 
	cas.cBankList,
	buy.cName as buyer, 
	own.cName as owner, 
	inc.cTotalMoney as cTotalMoney, 
	inc.cCertifiedMoney as cCertifiedMoney, 
	csc.cScrivener as cScrivener, 
	(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener, 
	pro.cAddr as cAddr, 
	pro.cZip as cZip, 
	zip.zCity as zCity, 
	zip.zArea as zArea, 
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2,
	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
	cas.cCaseStatus as cCaseStatus,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,	
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brandname3,
	rea.cBrand as brand,
	rea.cBrand1 as brand1,
	rea.cBrand2 as brand2,
	rea.cBrand2 as brand3,
	rea.cBranchNum as branch,
	rea.cBranchNum1 as branch1,
	rea.cBranchNum2 as branch2,
	rea.cBranchNum3 as branch3,
	(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) category,
	(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) category1,
	(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) category2,
	scr.sBrand as scr_brand,
	scr.sCategory as scr_cat,
	cas.cCaseFeedBackMoney,
	cas.cCaseFeedBackMoney1,
	cas.cCaseFeedBackMoney2,
	cas.cCaseFeedBackMoney3,
	cas.cSpCaseFeedBackMoney,
	cas.cCaseFeedback,
	cas.cCaseFeedback1,
	cas.cCaseFeedback2,
	cas.cCaseFeedback3
FROM 
	tContractCase AS cas 
LEFT JOIN 
	tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
LEFT JOIN 
	tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN
	tZipArea AS zip ON zip.zZip=pro.cZip
LEFT JOIN 
	tScrivener AS scr ON scr.sId = csc.cScrivener
'.$query.' 
GROUP BY
	cas.cCertifiedId
ORDER BY 
	cas.cApplyDate,cas.cId,cas.cSignDate ASC;
' ;

$tlog->selectWrite($_SESSION['member_id'], $query, '2019案件統計表搜尋') ;
$logs->writelog('applycaseWeb') ;

$rs = $conn->Execute($query);
$max = $rs->RecordCount();
$check = false;
while (!$rs->EOF) {
	if ($realestate) {
		if ($realestate == 11) {//加盟(其他品牌)
			if (($rs->fields['brand'] != 1 AND $rs->fields['brand'] != 49 AND $rs->fields['brand'] != 56) AND $rs->fields['category'] == 1) {
				$check = true;
			}

			if (($rs->fields['brand1'] != 1 AND $rs->fields['brand1'] != 49 AND $rs->fields['brand1'] != 56) AND $rs->fields['category1'] == 1) {
				$check = true;
			}

			if (($rs->fields['brand2'] != 1 AND $rs->fields['brand2'] != 49 AND $rs->fields['brand2'] != 56) AND $rs->fields['category2'] == 1) {
				$check = true;
			}
			
		}else if ($realestate == 12) { //加盟(台灣房屋)
			if ($rs->fields['brand'] == 1 AND $rs->fields['category'] == 1) {
				$check = true;
			}

			if ($rs->fields['brand1'] == 1 AND $rs->fields['category1'] == 1) {
				$check = true;
			}

			if ($rs->fields['brand2'] == 1 AND $rs->fields['category2'] == 1) {
				$check = true;
			}
		
		}elseif ($realestate == 13) { //加盟(優美地產)
			if ($rs->fields['brand'] == 49 AND $rs->fields['category'] == 1) {
				$check = true;
			}

			if ($rs->fields['brand1'] == 49 AND $rs->fields['category1'] == 1) {
				$check = true;
			}

			if ($rs->fields['brand2'] == 49 AND $rs->fields['category2'] == 1) {
				$check = true;
			}
		}elseif ($realestate == 14) { //加盟(永春不動產)
			if ($rs->fields['brand'] == 56 AND $rs->fields['category'] == 1) {
				$check = true;
			}

			if ($rs->fields['brand1'] == 56 AND $rs->fields['category1'] == 1) {
				$check = true;
			}

			if ($rs->fields['brand2'] == 56 AND $rs->fields['category2'] == 1) {
				$check = true;
			}
		}elseif ($realestate == 1) { //加盟
			if ($rs->fields['category'] == 1) {
				$check = true;
			}

			if ($rs->fields['category1'] == 1) {
				$check = true;
			}

			if ($rs->fields['category2'] == 1) {
				$check = true;
			}
		}elseif ($realestate == 2) { //直營
			if ($rs->fields['category'] == 2) {
				$check = true;
			}

			if ($rs->fields['category1'] == 2) {
				$check = true;
			}

			if ($rs->fields['category2'] == 2) {
				$check = true;
			}
		}elseif ($realestate == 3) { //非仲介成交
			if ($rs->fields['brand'] == 2) {
				$check = true;
			}

			if ($rs->fields['brand1'] == 2) {
				$check = true;
			}

			if ($rs->fields['brand2'] == 2) {
				$check = true;
			}
		}elseif ($realestate == 5) { //台屋集團
			if ($rs->fields['brand'] == 1 AND $rs->fields['brand'] == 49) {
				$check = true;
			}

			if ($rs->fields['brand1'] == 1 AND $rs->fields['brand1'] == 49) {
				$check = true;
			}

			if ($rs->fields['brand2'] == 1 AND $rs->fields['brand2'] == 49) {
				$check = true;
			}
		}elseif ($realestate == 6) { //他牌+非仲
			if ($rs->fields['brand'] != 1 AND $rs->fields['brand'] != 49) {
				$check = true;
			}

			if ($rs->fields['brand1'] != 1 AND $rs->fields['brand1'] != 49) {
				$check = true;
			}

			if ($rs->fields['brand2'] != 1 AND $rs->fields['brand2'] != 49) {
				$check = true;
			}
		}
	}else{
		$check = true;
	}

	if ($check == true) {
		$arr[] = $rs->fields;
	}
	

	$rs->MoveNext();
}
unset($check);
$tbl = '' ;

# 取得所有資料
$totalMoney = 0 ;
$certifiedMoney = 0 ;
$transMoney = 0 ;
$j = 0;



for ($i = 0 ; $i < $max ; $i ++) {
	$cCaseFeedBackMoney = 0;
	// 簽約日期
	$arr[$i]['cSignDate'] = dateCg($arr[$i]['cSignDate']) ;
	// 結案日期
	$arr[$i]['cEndDate'] = dateCg($arr[$i]['cEndDate']) ;
	// 進案日期
	$arr[$i]['cApplyDate'] = dateCg($arr[$i]['cApplyDate']) ;
	$arr[$i]['cFinishDate'] = dateCg($arr[$i]['cFinishDate']) ;

	//getTransDate
	$arr[$i]['tBankLoansDate'] = getTransDate($arr[$i]['cCertifiedId']);
	$arr[$i]['tBankLoansMoney'] = getTrans($arr[$i]['cCertifiedId']);

	$arr[$i]['showBrandname'] = $arr[$i]['brandname'];
	$arr[$i]['showBranchname'] = getRealtyName($arr[$i]['branch']);

	$CaseCount = 0; //案件數量平分
	$FeedCount = 0;//回饋數量
	$cat = '';
	$cat1 ='';
	$cat2 = '';

	if ($arr[$i]['branch'] > 0) {
		
		if ($arr[$i]['cCaseFeedback'] == 0) {
			$cCaseFeedBackMoney += $arr[$i]['cCaseFeedBackMoney'];
			$arr[$i]['showcCaseFeedBackMoney'] += $arr[$i]['cCaseFeedBackMoney'];
		}
		$tmp_sales[] = getBranchSales($arr[$i]['branch']);
		##案件比例分配##
		$CaseCount++;
		$FeedCount++;
		$cat = checkCat($arr[$i]['branch'],$arr[$i]['brand']);
	}

	if ($arr[$i]['branch1'] > 0) {
		$arr[$i]['showBrandname'] .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>';
		$arr[$i]['showBrandname'] .= $arr[$i]['brandname1'];

		$arr[$i]['showBranchname'] .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>';
		$arr[$i]['showBranchname'] .= getRealtyName($arr[$i]['branch1']);

		

		if ($arr[$i]['cCaseFeedback1'] == 0) {
			$cCaseFeedBackMoney += $arr[$i]['cCaseFeedBackMoney1'];
			$arr[$i]['showcCaseFeedBackMoney'] += $arr[$i]['cCaseFeedBackMoney1'];
		}
		$tmp_sales[] = getBranchSales($arr[$i]['branch1']);
		##案件比例分配##
		$CaseCount++;
		$FeedCount++;
		$cat1 = checkCat($arr[$i]['branch1'],$arr[$i]['brand1']);
	}

	if ($arr[$i]['branch2'] > 0) {
		$arr[$i]['showBrandname'] .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>';
		$arr[$i]['showBrandname'] .= $arr[$i]['brandname2'];
		$arr[$i]['showBranchname'] .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>';
		
		$arr[$i]['showBranchname'] .= getRealtyName($arr[$i]['branch2']);

		$tmp_sales[] = getBranchSales($arr[$i]['branch2']);

		if ($arr[$i]['cCaseFeedback2'] == 0) {
			$cCaseFeedBackMoney += $arr[$i]['cCaseFeedBackMoney2'];
			$arr[$i]['showcCaseFeedBackMoney'] += $arr[$i]['cCaseFeedBackMoney2'];
		}

		##案件比例分配##
		$CaseCount++;
		$FeedCount++;
		$cat2 = checkCat($arr[$i]['branch2'],$arr[$i]['brand2']);
	}
	//地政士特殊回饋
	if ($arr[$i]['cSpCaseFeedBackMoney'] > 0 ) {
		$FeedCount++;

		$cCaseFeedBackMoney +=$arr[$i]['cSpCaseFeedBackMoney'];
		$arr[$i]['showcCaseFeedBackMoney'] += $arr[$i]['cSpCaseFeedBackMoney'];
	}

	###其他回饋

	$tmp = getOtherFeedMoney($arr[$i]['cCertifiedId']);

	$FeedCount += $tmp['fCount'];
	$cCaseFeedBackMoney += $tmp['fMoney'];
	$certifiedData[$arr[$i]['cCertifiedId']]['FeedCount'] = $FeedCount;
	$arr[$i]['showcCaseFeedBackMoney'] += $tmp['fMoney'];

	$arr[$i]['salesName'] = @implode(',', $tmp_sales);
	
	unset($tmp_sales);unset($tmp);
	$arr[$i]['Scrsales'] = getScrivenerSales($arr[$i]['cScrivener']);
	
	
	##※配件狀況，數量平分(不含其他回饋對象)；保證費平分(以回饋金對象去分) ;回饋金按照後台分的去算 ;總價金不平分
	if ($realestate || $branch || $brand || $sales) {
		$realKey = '';
		if ($realestate) {
			if ($realKey) {	$realKey .= '_';}
			$realKey .= $realestate;
		}

		if ($branch) {
			if ($realKey) {	$realKey .= '_';}
			$realKey .= $branch;
		}

		if ($brand) {
			if ($realKey) {	$realKey .= '_';}
			$realKey .= $brand;
		}

		if ($sales) {
			if ($realKey) {	$realKey .= '_';}
			$realKey .= $sales;
		}
		// echo $realKey."<br>";
		$caseAna[$realKey]['total'] += $arr[$i]['cTotalMoney'];
		if ($CaseCount == 0) {
			$CaseCount = 1;
		}

		if ($FeedCount == 0) {
			$FeedCount = 1;
		}

		$part = round((1/$CaseCount),1);
		$part2 = round((1/$FeedCount),1);
		
		//回饋金只算給仲介的部分
		$check = false;
		$check1 = false;
		$check2 = false;
		$key = '';
		$key1 = '';
		$key2 = '';
		if ($realestate) { 
			if ($key) {$key.='_'; }
			$key .= $cat;

			if ($key1) {$key1.='_'; }
			$key1 .= $cat1;

			if ($key2) {$key2.='_'; }
			$key2 .= $cat2;
		}

		if ($branch) { 
			if ($key) {$key.='_'; }
			$key .= $arr[$i]['branch'];

			if ($key1) {$key1.='_'; }
			$key1 .= $arr[$i]['branch1'];

			if ($key2) {$key1.='_'; }
			$key2 .= $arr[$i]['branch2'];
		}
		
		if ($brand) { 
			if ($key) {$key.='_'; }
			$key .= $arr[$i]['brand'];

			if ($key1) {$key1.='_'; }
			$key1 .= $arr[$i]['brand1'];

			if ($key2) {$key2.='_'; }
			$key2 .= $arr[$i]['brand2'];
		}
		
		if ($sales) { 
			if ($key) {$key.='_'; }
			$key .= getSalse($arr[$i]['cCertifiedId'],$arr[$i]['branch']);

			if ($key1) {$key1.='_'; }
			$key1 .= getSalse($arr[$i]['cCertifiedId'],$arr[$i]['branch1']);

			if ($key2) {$key2.='_'; }
			$key2 .= getSalse($arr[$i]['cCertifiedId'],$arr[$i]['branch2']);
		}
		
		// if ($arr[$i]['cCertifiedId'] == '004105201') {
			// echo $realKey."<br>";
			// echo $key."<bR>";
			// echo $key1."<br>";
			// echo $key2."<br><br>";
		// }

		if ($arr[$i]['branch'] > 0) {
			$caseAna[$key]['count'] += $part;
			// $caseAna[$key]['total'] += $arr[$i]['cTotalMoney'];
			$caseAna[$key]['certifiedMoney'] += $arr[$i]['cCertifiedMoney']*$part2;
			$caseAna[$key]['feedbackmoney'] += $arr[$i]['cCaseFeedBackMoney'];

			//案件的符合條件的比例
			$certifiedData[$arr[$i]['cCertifiedId']][$key]['part'] += $part;
			$certifiedData[$arr[$i]['cCertifiedId']][$key]['part2'] += $part2;
			$certifiedData[$arr[$i]['cCertifiedId']][$key]['CertifiedMoneypart'] +=$arr[$i]['cCertifiedMoney']*$part2;
			$certifiedData[$arr[$i]['cCertifiedId']][$key]['CaseFeedBackMoneyPart'] +=$arr[$i]['cCaseFeedBackMoney'];

		}

		if ($arr[$i]['branch1'] > 0) {
			$caseAna[$key1]['count'] += $part;
			// $caseAna[$key1]['total'] += $arr[$i]['cTotalMoney'];
			$caseAna[$key1]['certifiedMoney'] += $arr[$i]['cCertifiedMoney']*$part2;
			$caseAna[$key1]['feedbackmoney'] += $arr[$i]['cCaseFeedBackMoney1'];

			//案件的符合條件的比例
			$certifiedData[$arr[$i]['cCertifiedId']][$key1]['part'] += $part;
			$certifiedData[$arr[$i]['cCertifiedId']][$key1]['part2'] += $part2;
			$certifiedData[$arr[$i]['cCertifiedId']][$key1]['CertifiedMoneypart'] +=$arr[$i]['cCertifiedMoney']*$part2;
			$certifiedData[$arr[$i]['cCertifiedId']][$key1]['CaseFeedBackMoneyPart'] +=$arr[$i]['cCaseFeedBackMoney1'];

			
		}

		if ($arr[$i]['branch2'] > 0) {
			$caseAna[$key2]['count'] += ($part+0.1); //補0.1才會滿1 1/3=0.3333
			// $caseAna[$key2]['total'] += $arr[$i]['cTotalMoney'];
			$caseAna[$key2]['certifiedMoney'] += $arr[$i]['cCertifiedMoney']*$part2;
			$caseAna[$key2]['feedbackmoney'] += $arr[$i]['cCaseFeedBackMoney2'];

			//案件的符合條件的比例
			$certifiedData[$arr[$i]['cCertifiedId']][$key2]['part'] += ($part+0.1);
			$certifiedData[$arr[$i]['cCertifiedId']][$key2]['part2'] += $part2;
			$certifiedData[$arr[$i]['cCertifiedId']][$key2]['CertifiedMoneypart'] +=$arr[$i]['cCertifiedMoney']*$part2;
			$certifiedData[$arr[$i]['cCertifiedId']][$key2]['CaseFeedBackMoneyPart'] +=$arr[$i]['cCaseFeedBackMoney2'];

		}

		

		

		if ($key == $realKey) {
			$caseAna[$key]['data'][] = $arr[$i];
		}elseif ($key1 == $realKey) {
			$caseAna[$key1]['data'][] = $arr[$i];
		}elseif ($key2 == $realKey) {
			$caseAna[$key2]['data'][] = $arr[$i];
		}
	
				
	}else{
		$realKey = 0;
		$arr[$i]['part'] = 1; //數量占比
		$arr[$i]['part2'] = 1;//回饋占比
		$caseAna[$realKey]['count'] ++;
		$caseAna[$realKey]['total'] += $arr[$i]['cTotalMoney'];
		$caseAna[$realKey]['certifiedMoney'] += $arr[$i]['cCertifiedMoney'];
		$caseAna[$realKey]['feedbackmoney'] += $cCaseFeedBackMoney;
		$caseAna[$realKey]['data'][] = $arr[$i];

		//案件的符合條件的比例
		$certifiedData[$arr[$i]['cCertifiedId']][$realKey]['part'] += $arr[$i]['part'];
		$certifiedData[$arr[$i]['cCertifiedId']][$realKey]['part2'] += $arr[$i]['part2'];
		$certifiedData[$arr[$i]['cCertifiedId']][$realKey]['CertifiedMoneypart'] += $arr[$i]['cCertifiedMoney'];
		$certifiedData[$arr[$i]['cCertifiedId']][$realKey]['CaseFeedBackMoneyPart'] += $cCaseFeedBackMoney;
		
	}
	//<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>
}

if ($realestate || $branch || $brand || $sales) {
	//其他回饋
	$sql = "SELECT 
				fb.*,
				b.bBrand,
				(SELECT cCertifiedMoney FROM tContractIncome WHERE cCertifiedId = fb.fCertifiedId) AS cCertifiedMoney
			FROM
				tFeedBackMoney AS fb
			LEFT JOIN
					tBranch AS b ON b.bId=fb.fStoreId
			WHERE
				fb.fType =2 ".$queryO;
				// echo $sql."<br>";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$keyO = checkCat($rs->fields['fStoreId'],$rs->fields['brand'])."_".$rs->fields['fStoreId']."_".$rs->fields['brand']."_".$rs->fields['fSales'];
			
			$caseAna[$keyO]['certifiedMoney'] += $rs->fields['cCertifiedMoney']*$part2;
			$caseAna[$keyO]['feedbackmoney'] += $arr[$i]['fMoney'];
			$certifiedData[$rs->fields['fCertifiedId']][$keyO]['part2'] += $part2;
			$certifiedData[$rs->fields['cCertifiedId']][$keyO]['CertifiedMoneypart'] +=$rs->fields['cCertifiedMoney']*$part2;

			$rs->MoveNext();
		}
}
//四捨五入
$caseAna[$realKey]['money'] =number_format($caseAna[$realKey]['certifiedMoney']-$caseAna[$realKey]['feedbackmoney']);
$caseAna[$realKey]['count'] = number_format($caseAna[$realKey]['count']);
$caseAna[$realKey]['total'] = number_format($caseAna[$realKey]['total']);
$caseAna[$realKey]['certifiedMoney'] = number_format($caseAna[$realKey]['certifiedMoney']);
$caseAna[$realKey]['feedbackmoney'] = number_format($caseAna[$realKey]['feedbackmoney']);


// echo "<pre>";
// print_r($caseAna);
// echo "</pre>";
// // echo "AAAAAA";
// die;
$max = count($caseAna[$realKey]['data']);
##
//產出excel檔
if ($xls == 'ok') {

	// $logs->writelog('applycase2019_excel') ;
	// $tlog->exportWrite($_SESSION['member_id'], json_encode($_POST), '2019案件統計表excel匯出') ;
	// echo $_SESSION['member_id'];
	if ($_SESSION['member_id'] == 6) {
		// echo '66';
		include_once 'applycase2019_csv.php' ;

		// include_once 'applycase2019_excel.php' ;
	}else{
		
		include_once 'applycase2019_excel.php' ;
	}
	
}

# 計算總頁數
if (($max % $record_limit) == 0) {
	$total_page = $max / $record_limit ;
}
else {
	$total_page = floor($max / $record_limit) + 1 ;
}
##

# 設定目前頁數顯示範圍
if ($current_page) {
	if ($current_page >= ($max / $record_limit)) {
		if ($max % $record_limit == 0) {
			$current_page = floor($max / $record_limit) ;
		}
		else {
			$current_page = floor($max / $record_limit) + 1 ;
		}
	}
	$i_end = $current_page * $record_limit ;
	$i_begin = $i_end - $record_limit ;
	if ($i_end > $max) {
		$i_end = $max ;
	}
	if($i_end > $max) { $i_end = $max ; }
}
else {
	$i_end = $record_limit ;
	if($i_end > $max) { $i_end = $max ; }
	$i_begin = 0 ;
	$current_page = 1 ;
}

$j = 0 ; 
for ($i = $i_begin ; $i < $i_end ; $i ++) {
	
	$list[$j] = $caseAna[$realKey]['data'][$i];
	
	$list[$j]['color'] = ($i % 2 == 0)? "#FFFFFF":"#F8ECE9";
	$j++;
}

unset($j);

if ($record_limit==10) { $records_limit .= '<option value="10" selected="selected">10</option>'."\n" ; }
else { $records_limit .= '<option value="10">10</option>'."\n" ; }
if ($record_limit==50) { $records_limit .= '<option value="50" selected="selected">50</option>'."\n" ; }
else { $records_limit .= '<option value="50">50</option>'."\n" ; }
if ($record_limit==100) { $records_limit .= '<option value="100" selected="selected">100</option>'."\n" ; }
else { $records_limit .= '<option value="100">100</option>'."\n" ; }
if ($record_limit==150) { $records_limit .= '<option value="150" selected="selected">150</option>'."\n" ; }
else { $records_limit .= '<option value="150">150</option>'."\n" ; }
if ($record_limit==200) { $records_limit .= '<option value="200" selected="selected">200</option>'."\n" ; }
else { $records_limit .= '<option value="200">200</option>'."\n" ; }



if($max > 0) {
	$functions = '<span id="a_tag"><a href=# onclick="list()">檢視明細</a></span>' ;
}
else {
	$functions = '－' ;
}


if ($max==0) {
	$i_begin = 0 ;
	$i_end = 0 ;
}
else {
	$i_begin += 1 ;
}
##
//取得仲介店名
function getRealtyName($no=0) {
	global $conn;
	unset($tmp) ;
	if ($no > 0) {
		$sql = 'SELECT bStore FROM tBranch WHERE bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);

		return $rs->fields['bStore'] ;
	}
	else {
		return false ;
	}
}
##

//取得仲介店編號
function getRealtyNo($lnk,$no=0) {//找舊有的品牌(20150908)
	global $conn;
	unset($tmp) ;
	if ($no > 0) {
		$sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);
		
		return strtoupper($rs->fields['bCode']).str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT) ;
	}
	else {
		return false ;
	}
}

function dateCg($val){

	$val = substr($val, 0,10);


	// $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
		
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'/'.$tmp[1].'/'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}

function checkCat($no,$brand) {
	global $conn;
	$val = '' ;
	
	if ($no) {
		$sql = 'SELECT
					(SELECT bId FROM tBrand AS br WHERE br.bId = '.$brand.') AS bBrand,
					bCategory
					
				FROM
					tBranch
				WHERE
					bId="'.$no.'" AND bId<>"0";' ;
		
		$rs = $conn->Execute($sql);

		
		if ($rs->fields['bCategory'] == '1') {
			
			if ($rs->fields['bBrand'] == '1') {
				$val = 12 ;
			}
			else if ($rs->fields['bBrand'] == '2') {
				$val = 3 ;
			}
			else if ($rs->fields['bBrand'] == '49') {
				$val = 13 ;
			}
			else if ($rs->fields['bBrand'] == '56') {
				$val = 14 ;
			}
			else {
				$val = 11 ;
			}
		}
		else if ($rs->fields['bCategory'] == '2') {
			$val = 2;
		}
		else if ($rs->fields['bCategory'] == '3') {
			$val = 3 ;
		}
		
	}
	// echo $val."-----";
	return $val ;
}

function getSalse($cId,$bId){
	global $conn;

	$sql = "SELECT cSalesId FROM tContractSales WHERE cCertifiedId = '".$cId."' AND cBranch = '".$bId."'";

	$rs = $conn->Execute($sql);

	return $rs->fields['cSalesId'];
}

function getTransDate($id){
	global $conn;

	//取得實際出款日
		$sql = 'SELECT tBankLoansDate FROM tBankTrans WHERE tMemo="'.$id.'" AND tKind="保證費"  ORDER BY tExport_time DESC LIMIT 1;' ;
		$rs = $conn->Execute($sql);

		return $rs->fields['tBankLoansDate'];
}
function getBranchSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS Name FROM tBranchSales WHERE bBranch = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['Name'];

		$rs->MoveNext();
	}

	return @implode('_', $sales);
}

function getScrivenerSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['Name'];

		$rs->MoveNext();
	}

	return @implode('_', $sales);
}

function getTrans($cId){
	global $conn;

	//取得合約銀行
	$Savings = array() ;
	$savingAccount = '' ;
	$i = 0 ;
	$sql = 'SELECT cBankAccount FROM tContractBank GROUP BY cBankAccount ORDER BY cId ASC;' ;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$Savings[$i++] = $rs->fields['cBankAccount'] ;

		$rs->MoveNext();
	}


	$savingAccount = implode('","',$Savings) ;
	unset($Savings) ;

	$sql = '
	SELECT 
		DISTINCT tMemo, 
		tMoney 
	FROM 
		tBankTrans 
	WHERE 
		tAccount IN ("'.$savingAccount.'") 
		AND tPayOk="1" 
		AND tMemo = "'.$cId.'"
	ORDER BY 
		tMemo 
	ASC' ;

	$rs = $conn->Execute($sql);
	// while (!$rs->EOF) {
		// $export_data[$rs->fields['tMemo']] = $rs->fields['tMoney'] ;

		// $rs->MoveNext();
	// }

	return $rs->fields['tMoney'] ;
}
##

##
# 頁面資料
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',number_format($max)) ;
$smarty->assign('caseAna',$caseAna);
# 搜尋資訊
$smarty->assign('bank',$bank) ;
$smarty->assign('sApplyDate',$sad) ;
$smarty->assign('eApplyDate',$ead) ;
$smarty->assign('sEndDate',$sed) ;
$smarty->assign('eEndDate',$eed) ;
$smarty->assign('sSignDate',$ssd) ;
$smarty->assign('eSignDate',$esd) ;
$smarty->assign('branch',$br) ;
$smarty->assign('scrivener',$sc) ;
$smarty->assign('zip',$zip) ;
$smarty->assign('citys',$citys) ;
$smarty->assign('brand',$brand) ;
$smarty->assign('undertaker',$undertaker) ;
$smarty->assign('status',$status) ;
$smarty->assign('cCertifiedId',$cCertifiedId) ;
$smarty->assign('buyer',$byr) ;
$smarty->assign('owner',$owr) ;
$smarty->assign('scrivener_category',$scrivener_category);
$smarty->assign('sales',$sales);
$smarty->assign('list',$list);

# 搜尋結果
$smarty->assign('tbl',$tbl) ;
$smarty->assign('totalMoney',number_format($totalMoney)) ;
$smarty->assign('certifiedMoney',number_format($certifiedMoney)) ;
$smarty->assign('cCertifiedMoney',$cCertifiedMoney);//只查詢店家跟仲介
$smarty->assign('cCaseFeedBackMoney',number_format($cCaseFeedBackMoney));
$smarty->assign('income',number_format(($certifiedMoney-$cCaseFeedBackMoney)));
$smarty->assign('transMoney',number_format($transMoney)) ;


$smarty->assign('show_hide',$show_hide) ;
$smarty->assign('realestate',$realestate) ;

# 其他
$smarty->assign('functions',$functions) ;
$smarty->assign('t_day',$t_day) ;
$smarty->assign('realKey',$realKey);
$smarty->display('applycase2019_result.inc.tpl', '', 'report');


?>