<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;

//預載log物件
$logs = new Intolog() ;
##

 $_POST = escapeStr($_POST) ;
##
//檢核仲介類型
function checkCat($ct,$bd,$no) {
	global $conn;
	$val = '' ;
	
	if ($no) {
		$sql = 'SELECT bBrand,bCategory FROM tBranch WHERE bId="'.$no.'" AND bId<>"0";' ;
		$rs = $conn->Execute($sql);
		
		
		if ($rs->fields['bCategory'] == '1') {
			$val = '加盟' ;
			if ($rs->fields['bBrand'] == '1') {
				$val .= '台灣房屋' ;
			}
			else if ($rs->fields['bBrand'] == '2') {
				$val .= '非仲介成交' ;
			}
			else if ($rs->fields['bBrand'] == '49') {
				$val .= '優美地產' ;
			}elseif ($rs->fields['bBrand'] == '56') {
				$val .= '永春不動產' ;
			}
			else {
				$val .= '其他品牌' ;
			}
		}
		else if ($rs->fields['bCategory'] == '2') {
			$val = '直營' ;
		}
		else if ($rs->fields['bCategory'] == '3') {
			$val = '非仲介成交' ;
		}
	}
	
	return $val ;
}
//取得仲介店名
function getRealtyName($no=0) {
	global $conn;
	
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
function getRealtyNo($no=0) {
	global $conn;
	if ($no > 0) {
		$sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);

		return  strtoupper($rs->fields['bCode']).str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT);
	}
	else {
		return false ;
	}
}
##
function checkSales($arr,$pId){
    global $conn;

  	
    if ($_SESSION['member_pDep'] != 7) {return true;}
    $twhgCount = 0;//業務不能看直營的案件
    $branch[] = $arr['branch'];
    if ($arr['brand'] == 1 && $arr['category'] == 2) {//仲介台屋直營
        $twhgCount++;
    }

    if ($arr['branch1'] > 0) {
        $branch[] = $arr['branch1'];
        if ($arr['brand1'] == 1 && $arr['category1'] == 2) {//仲介台屋直營
            $twhgCount++;
        }
    }
    if ($arr['branch2'] > 0){
    	$branch[] = $arr['branch2'];
        if ($arr['brand2'] == 1 && $arr['category2'] == 2) {//仲介台屋直營
            $twhgCount++;
        }
    }   

   

    if ($twhgCount == count($branch)) { //直營不可以給業務看
        return false;
    }

    if ($_SESSION['member_test'] != 0) {
  		return true;
  	}
    // if ($arr['scrivenerCategory'] == 2 || $arr['cScrivener'] == 1182 || $arr['cScrivener'] == 632) { //直營代書不可以給業務看
    //     return false;
    // }
    
    // echo "<pre>";
   	// print_r($arr);
    ##
    $salesCount = 0;
    $sql = "SELECT bSales FROM tBranchSales WHERE bBranch IN(".@implode(',', $branch).") AND bSales = '".$pId."'";
   
    $rs = $conn->Execute($sql);
    $salesCount +=$rs->RecordCount();


    $sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener =".$arr['cScrivener']." AND sSales='".$pId."'";
    $rs = $conn->Execute($sql);
    $salesCount +=$rs->RecordCount();

    if ($salesCount > 0) {
        return true;
    }else{
        return false;
    }
    

    
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
##

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
$scrivener_catego = $_POST['scrivener_category'];
$bApplication = $_POST['bApplication'];
$total_page = $_POST['total_page'] + 1 - 1 ;
$current_page = $_POST['current_page'] + 1 - 1 ;
$record_limit = $_POST['record_limit'] + 1 - 1 ;


if (!$record_limit) { $record_limit = 10 ; }

//產出excel檔

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

//取得合約銀行
$Savings = array() ;
$savingAccount = '' ;
$i = 0 ;
$sql = 'SELECT cBankAccount FROM tContractBank GROUP BY cBankAccount ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$Savings[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}

$savingAccount = implode('","',$Savings) ;
unset($Savings) ;
##

//取得所有出款保證費紀錄("27110351738","10401810001889","20680100135997")
$sql = '
	SELECT 
		DISTINCT tMemo, 
		tMoney 
	FROM 
		tBankTrans 
	WHERE 
		tAccount IN ("'.$savingAccount.'") 
		AND tPayOk="1" 
	ORDER BY 
		tMemo 
	ASC' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$export_data[$rs->fields['tMemo']] = $rs->fields['tMoney'] ;

	$rs->MoveNext();
}

##

$query = ' cas.cCertifiedId<>""  AND cas.cCertifiedId !="005030342"' ;

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



// 搜尋條件-結案日期
if ($sEndDate) {
	$tmp = explode('-',$sEndDate) ;
	$sEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate>="'.$sEndDate.' 00:00:00" ' ;
	//$query .= ' tra.tExport_time>="'.$sEndDate.' 00:00:00" ' ;
}
if ($eEndDate) {
	$tmp = explode('-',$eEndDate) ;
	$eEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate<="'.$eEndDate.' 23:59:59" ' ;
	//$query .= ' tra.tExport_time<="'.$eEndDate.' 23:59:59" ' ;
}
##

// 搜尋條件-簽約日期
if ($sSignDate) {
	$tmp = explode('-',$sSignDate) ;
	$sSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
	//$query .= ' tra.tExport_time>="'.$sSignDate.' 00:00:00" ' ;
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
}

// 搜尋條件-地政士
if ($scrivener) {
	if ($query) { $query .= " AND " ; }
	$query .= ' csc.cScrivener="'.$scrivener.'" ' ;
}


// 搜尋條件-保證號碼
if ($cCertifiedId) {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCertifiedId="'.$cCertifiedId.'" ' ;
}

// 搜尋條件-仲介品牌
//if ($brand) {
if (($brand != '') && ($realestate != '11') && ($realestate != '12') && ($realestate != '13') && ($realestate != '14')) {
	if ($query) { $query .= " AND " ; }
	$query .= ' (rea.cBrand="'.$brand.'" OR rea.cBrand1="'.$brand.'" OR rea.cBrand2="'.$brand.'" OR rea.cBrand3="'.$brand.'") ' ;
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
		# code...
		$zipArr[] = $rs->fields['zZip'] ;
		$rs->MoveNext();
	}

	
	$zipStr = implode('","',$zipArr) ;
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip IN ("'.$zipStr.'") ' ;
	unset($zipArr) ;
	unset($zipStr) ;
}


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
##
// //合約書類別
// if ($bApplication) {
// 	if ($query) { $query .= " AND "  ; }

// 	if ($bApplication == 4) { //很早期的保號沒有記錄是哪依類別
// 		$query .= "bc.bApplication = ''";
// 	}else{
// 		$query .= "bc.bApplication = '".$bApplication."'";
// 	}

	
// }


if ($query) { $query = ' WHERE '.$query ; }


$query ='
SELECT 
	cas.cCertifiedId as cCertifiedId,
	cas.cEscrowBankAccount as cEscrowBankAccount, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate,
	cas.cEndDate as cEndDate, 
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
	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,
	(SELECT bName FROM tBrand WHERE bId = rea.cBrand3) AS brandname3,	
	rea.cBrand as brand,
	rea.cBrand1 as brand1,
	rea.cBrand2 as brand2,
	rea.cBrand3 as brand3,
	rea.cBranchNum as branch,
	rea.cBranchNum1 as branch1,
	rea.cBranchNum2 as branch2,
	rea.cBranchNum3 as branch3,
	(SELECT bScrRecall FROM tBranch AS b WHERE b.bId=rea.cBranchNum) AS bScrRecall,
	(SELECT bScrRecall FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) AS bScrRecall1,
	(SELECT bScrRecall FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) AS bScrRecall2,
	(SELECT bScrRecall FROM tBranch AS b WHERE b.bId=rea.cBranchNum3) AS bScrRecall3,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand3 ),LPAD(rea.cBranchNum3,5,"0")) as bCode3,
	scr.sBrand as scr_brand,
	scr.sCategory as scr_cat
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

// echo $query ; //exit ;
$tlog->selectWrite($_SESSION['member_id'], $query, '案件統計表搜尋') ;
$logs->writelog('applycaseWeb') ;
$rs = $conn->Execute($query);
$max = $rs->RecordCount();
while (!$rs->EOF) {
	$tmp_arr[] = $rs->fields;
	$rs->MoveNext();
}

$tbl = '' ;
if ($bApplication) {

	for ($i=0; $i < count($tmp_arr); $i++) { 

		$sql = "SELECT bApplication FROM tBankCode WHERE bAccount = '".$tmp_arr[$i]['cEscrowBankAccount']."'";
		$rs = $conn->Execute($sql);

		if ($bApplication == 4) {
			

			if ($rs->fields['bApplication'] == '') {
				$tmp_arr2[] = $tmp_arr[$i];
			}

		}else{
			

			if ($bApplication == $rs->fields['bApplication']) {
				$tmp_arr2[] = $tmp_arr[$i];
			}
		}

		
	}
	unset($tmp_arr);

	$tmp_arr = $tmp_arr2;

	unset($tmp_arr2);

	$max = count($tmp_arr);
}



# 取得所有資料
$totalMoney = 0 ;
$certifiedMoney = 0 ;
$transMoney = 0 ;
$j = 0;
for ($i = 0 ; $i < $max ; $i ++) {

	if (checkSales($tmp_arr[$i],$_SESSION['member_id'])) {
		$arr[$j] =  $tmp_arr[$i];

		//取得仲介品牌
		$brand_111 = $arr[$j]['brandname'];
		
		if ($arr[$j]['branch1'] > 0) {
			
			// $brand_excel  =  $arr[$j]['brandname'].$arr[$j]['brandname1'];
			$brand_111 = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$brand_111 ;
			$brand_111.= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$arr[$j]['brandname1'] ;
			
		}

		if ($arr[$j]['branch2'] > 0) {
			$brand_111 .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$arr[$j]['brandname2']  ;
			
		}

		if ($arr[$j]['branch3'] > 0) {
			$brand_111 .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$arr[$j]['brandname3']  ;
			
		}
		
		$arr[$j]['bBrand'] = $brand_111;
		//取得各仲介店姓名
		$bStore = getRealtyName($arr[$j]['branch']) ;
		$store_excel[] = getRealtyName($arr[$j]['branch']) ;
		$code_excel[] = $arr[$j]['bCode'];

		if ($arr[$j]['branch1'] > 0) {
			$bStore = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$bStore ;
			$bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.getRealtyName($arr[$j]['branch1']) ;
			$store_excel[]=getRealtyName($arr[$j]['branch1']);
			$code_excel[]= $arr[$j]['bCode1'];
		}
		if ($arr[$j]['branch2'] > 0) {
			$bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.getRealtyName($arr[$j]['branch2']) ;
			$store_excel[]=getRealtyName($arr[$j]['branch2']);
			$code_excel[]= $arr[$j]['bCode2'];
		}
		if ($arr[$j]['branch3'] > 0) {
			$bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.getRealtyName($arr[$j]['branch3']) ;
			$store_excel[]=getRealtyName($arr[$j]['branch3']);
			$code_excel[]= $arr[$j]['bCode3'];
		}
		$arr[$j]['bStore'] = $bStore ;
		$arr[$j]['exbStore'] = @implode(',', $store_excel);
		$arr[$j]['exbCode'] = @implode(',', $code_excel);

		unset($store_excel); unset($code_excel);

		##
		
		// 簽約日期
		$arr[$j]['cSignDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$j]['cSignDate'])) ;
		$tmp = explode('-',$arr[$j]['cSignDate']) ;
		
		if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
		else { $tmp[0] -= 1911 ; }
		
		$arr[$j]['cSignDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
		##
		
		// 取得匯款金額
		$mm = 0 ;
		$mm += $export_data[$arr[$j]['cCertifiedId']] ;
		$arr[$j]['tMoney'] = $mm ;
		unset($mm) ;
		##
		
		// 進案日期
		$arr[$j]['cApplyDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$j]['cApplyDate'])) ;
		$tmp = explode('-',$arr[$j]['cApplyDate']) ;
		
		if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
		else { $tmp[0] -= 1911 ; }
		
		$arr[$j]['cApplyDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
		##
		
		// 結案日期
		$arr[$j]['cEndDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$j]['cEndDate'])) ;
		$tmp = explode('-',$arr[$j]['cEndDate']) ;
		
		if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
		else { $tmp[0] -= 1911 ; }
		
		$arr[$j]['cEndDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;

		$j++;
	}

	
	##

}
// echo "<pre>";
// print_r($arr);
// echo "</pre>";
// die;
##

//決定是否剔除過濾仲介類型
$max = count($arr) ;
// echo $max;

if ($realestate) {	

	
	$list = array() ;
	$j = 0 ;
	for ($i = 0 ; $i < $max ; $i ++) {
		//加盟分類
		$cat = '' ;
		if (preg_match("/^1/",$realestate)) {
			$cat = substr($realestate,1,1) ;
			$brd = substr($realestate,0,1) ;
		}
		##
		
		$cat1 = checkCat($cat,$brd,$arr[$i]['branch']) ;
		$cat2 = checkCat($cat,$brd,$arr[$i]['branch1']) ;
		$cat3 = checkCat($cat,$brd,$arr[$i]['branch2']) ;
		$cat4 = checkCat($cat,$brd,$arr[$i]['branch3']) ;

		
		if (($realestate == '11') && (($cat1 == '加盟其他品牌') || ($cat2 == '加盟其他品牌') || ($cat3 == '加盟其他品牌') || ($cat4 == '加盟其他品牌'))) {
			//$cat = '加盟其他品牌' ;
			$list[$j++] = $arr[$i] ;
		}
		else if (($realestate == '12') && (($cat1 == '加盟台灣房屋') || ($cat2 == '加盟台灣房屋') || ($cat3 == '加盟台灣房屋') || ($cat4 == '加盟台灣房屋'))) {
			//$cat = '加盟台灣房屋' ;
			if (($cat1 != '加盟其他品牌') && ($cat2 != '加盟其他品牌') && ($cat3 != '加盟其他品牌')) {
				$list[$j++] = $arr[$i] ;
			}
		}
		else if (($realestate == '13') && (($cat1 == '加盟優美地產') || ($cat2 == '加盟優美地產') || ($cat3 == '加盟優美地產') || ($cat4 == '加盟優美地產'))) {
			//$cat = '加盟優美地產' ;
			if (($cat1 != '加盟其他品牌') 
				&& ($cat2 != '加盟其他品牌') 
				&& ($cat3 != '加盟其他品牌') 
				&& ($cat4 != '加盟其他品牌') 
				&& ($cat1 != '加盟台灣房屋') 
				&& ($cat2 != '加盟台灣房屋') 
				&& ($cat3 != '加盟台灣房屋')
				&& ($cat4 != '加盟台灣房屋')) {
					$list[$j++] = $arr[$i] ;
			}
		}else if (($realestate == '14') && (($cat1 == '加盟永春不動產') || ($cat2 == '加盟永春不動產') || ($cat3 == '加盟永春不動產') || ($cat4 == '加盟永春不動產'))) {
			//$cat = '加盟優美地產' ;
			if (($cat1 != '加盟其他品牌') 
				&& ($cat2 != '加盟其他品牌') 
				&& ($cat3 != '加盟其他品牌') 
				&& ($cat4 != '加盟其他品牌')
				&& ($cat1 != '加盟台灣房屋') 
				&& ($cat2 != '加盟台灣房屋') 
				&& ($cat3 != '加盟台灣房屋')
				&& ($cat4 != '加盟台灣房屋')
				&& ($cat1 != '加盟優美地產') 
				&& ($cat2 != '加盟優美地產') 
				&& ($cat3 != '加盟優美地產')
				&& ($cat4 != '加盟優美地產')
				) {
					$list[$j++] = $arr[$i] ;
			}
		}
		else if (($realestate == '1') && (preg_match("/^加盟/",$cat1) || preg_match("/^加盟/",$cat2) || preg_match("/^加盟/",$cat3) || preg_match("/^加盟/",$cat4))) {
			//$cat = '所有加盟(其他品牌、台灣房屋、優美地產)' ;
			$list[$j++] = $arr[$i] ;
		}
		else if (($realestate == '2') && (($cat1 == '直營') || ($cat2 == '直營') || ($cat3 == '直營') || ($cat4 == '直營'))) {
			//$cat = '直營' ;
			//$list[$j++] = $arr[$i] ;
			if (!(preg_match("/^加盟/",$cat1) || preg_match("/^加盟/",$cat2) || preg_match("/^加盟/",$cat3) || preg_match("/^加盟/",$cat4))) {
				$list[$j++] = $arr[$i] ;
			}
		}
		else if (($realestate == '3') && (($cat1 == '非仲介成交') || ($cat2 == '非仲介成交') || ($cat3 == '非仲介成交') || ($cat4 == '非仲介成交'))) {
			//$cat = '非仲介成交' ;
			if ($cat1 == '非仲介成交') {
				$list[$j++] = $arr[$i] ;
			}
		}
		else if ($realestate == '4') {
			if (!(preg_match("/^加盟/",$cat1) 
				|| preg_match("/^加盟/",$cat2) 
				|| preg_match("/^加盟/",$cat3) 
				|| preg_match("/^加盟/",$cat4)
				|| preg_match("/直營/",$cat1)
				|| preg_match("/直營/",$cat2)
				|| preg_match("/直營/",$cat3)
				|| preg_match("/直營/",$cat4)
				|| preg_match("/非仲介成交/",$cat1)
				|| preg_match("/非仲介成交/",$cat2)
				|| preg_match("/非仲介成交/",$cat3)
				|| preg_match("/非仲介成交/",$cat4))) {
				
				$list[$j++] = $arr[$i] ;
			}
		}
	}
	unset($arr) ;
	$arr = array() ;
	
	$arr = array_merge($list) ;
}

##
$max = count($arr) ;



//計算總額
$max = count($arr) ;
// echo $max;
for ($i = 0 ; $i < $max ; $i ++) {
	$totalMoney += $arr[$i]['cTotalMoney'] ;
	$certifiedMoney += $arr[$i]['cCertifiedMoney'] ;
	$transMoney += $arr[$i]['tMoney'] ;
}
##
//EXCEL
if ($xls == 'ok') {

	
	$logs->writelog('applycaseOriginal_excel') ;
	$tlog->exportWrite($_SESSION['member_id'], json_encode($_POST), '案件統計表2excel匯出') ;
	
	include_once 'applycaseOriginal_excel.php' ;
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

$j = 1 ; 
for ($i = $i_begin ; $i < $i_end ; $i ++) {

	if ($i % 2 == 0) { $color_index = "#FFFFFF" ; }
	//else { $color_index = "#E4BeB1" ; }
	else { $color_index = "#F8ECE9" ; }
	$zc = $arr[$i]['zCity'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/","",$arr[$i]['cAddr']) ;
	$zc = $arr[$i]['zArea'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/",'',$arr[$i]['cAddr']) ;
	$arr[$i]['cAddr'] = $arr[$i]['zCity'].$arr[$i]['zArea'].$arr[$i]['cAddr'] ;

##
	$arr[$i]['cCertifiedMoney']=$arr[$i]['cCertifiedMoney'];
	$tmp = round($arr[$i]['cTotalMoney']*0.0006); //萬分之六
	$tmp2 = round($arr[$i]['cTotalMoney']*0.0006)*0.1;
  	


	if(($tmp-$tmp2)>$arr[$i]['cCertifiedMoney']) //合約保證費 如果未達6/10000的合約保證費  在合約保證費的金額位置 加註星星 
	{
		$arr[$i]['cCertifiedMoney']= '*'.$arr[$i]['cCertifiedMoney'] ;
	}else
	{
		$arr[$i]['cCertifiedMoney']= $arr[$i]['cCertifiedMoney'] ;
	}
##	
	$tbl .= '
	<tr style="text-align:center;background-color:'.$color_index.'">
		<td>'.($j++).'</td>
		<td><a href="#" onclick=contract("'.$arr[$i]['cCertifiedId'].'")>'.$arr[$i]['cCertifiedId'].'</a>&nbsp;</td>
		<td>'.$arr[$i]['bBrand'].'</td>
		<td>'.$arr[$i]['bStore'].'&nbsp;</td>
		<td>'.$arr[$i]['owner'].'&nbsp;</td>
		<td>'.$arr[$i]['buyer'].'&nbsp;</td>
		<td style="text-align:right;">'.number_format($arr[$i]['cTotalMoney']).'&nbsp;</td>
		<td style="text-align:right;">'.$arr[$i]['cCertifiedMoney'].'&nbsp;</td>
		' ;

		if ($status=='3') { 
			$tbl .= '<td>'.$arr[$i]['cEndDate'].'&nbsp;</td>' ; 
		}
		else {
			$tbl .= '<td>'.$arr[$i]['cSignDate'].'&nbsp;</td>' ; 
		}
	
	$tbl .= '
		<td>'.$arr[$i]['cApplyDate'].'&nbsp;</td>
		
		<td>'.$arr[$i]['scrivener'].'&nbsp;</td>
		<td>'.$arr[$i]['status'].'&nbsp;</td>
	</tr>
	' ;
	
}
	

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

# 頁面資料
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',number_format($max)) ;

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
$smarty->assign('bApplication',$bApplication);

# 搜尋結果
$smarty->assign('tbl',$tbl) ;
$smarty->assign('totalMoney',number_format($totalMoney)) ;
$smarty->assign('certifiedMoney',number_format($certifiedMoney)) ;
$smarty->assign('transMoney',number_format($transMoney)) ;
$smarty->assign('show_hide',$show_hide) ;
$smarty->assign('realestate',$realestate) ;

# 其他
$smarty->assign('functions',$functions) ;
$smarty->assign('t_day',$t_day) ;

$smarty->display('applycaseOriginalresult.inc.tpl', '', 'report');
?>