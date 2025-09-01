<?php
if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/advance.class.php';

include_once '../tracelog.php' ;
include_once '../openadodb.php' ;
include_once '../report/getBranchType.php';
// include_once 'getBranchType.php';
include_once 'includes/maintain/feedBackData.php';



##載入class
$advance = new Advance();
$tlog = new TraceLog() ;
##

 $_POST = escapeStr($_POST) ;
##
// 
//取得仲介店名
function getRealtyName($no=0) {
	global $conn;
	if ($no > 0) {
		$sql = 'SELECT bStore FROM tBranch WHERE bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);
		
		return $rs->fields['bStore'] ;
	}else {
		return false ;
	}
}
##

//取得仲介店編號
function getRealtyNo($lnk,$no=0) {//找舊有的品牌(20150908)
	global $conn;
	
	if ($no > 0) {
		$sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);
		
		return strtoupper($rs->fields['bCode']).str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT) ;
	}else {
		return false ;
	}
}

function dateCg($val){
	$val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
		
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'/'.$tmp[1].'/'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}

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

$_POST['sSignDate'] = '110-01-01 00:00:00';
$_POST['eSignDate'] = '110-12-31 23:59:59';
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
$scrivenerBrand = $_POST['scrivenerBrand'];
$report = $_POST['report'];
// $branchGroup = $_POST
// echo $report;
$sales = $_POST['sales'];
// echo "<pre>";
// print_r($_POST);
if ($sales == '') {

	$sales = $_SESSION['member_id'];
}
// echo $sales;	


$total_page = $_POST['total_page'] + 1 - 1 ;
$current_page = $_POST['current_page'] + 1 - 1 ;
$record_limit = $_POST['record_limit'] + 1 - 1 ;

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// die;
// echo $current_page;
if (!$record_limit) { $record_limit = 10 ; }

// 


##

$query = '' ; 
$functions = '' ;

// $sad = $sApplyDate ;
// $ead = $eApplyDate ;
// $sed = $sEndDate ;
// $eed = $eEndDate ;
// $ssd = $sSignDate ;
// $esd = $eSignDate ;
// $br = $branch ;
// $sc = $scrivener ;
// $byr = $buyer ;
// $owr = $owner ;


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
##
//取得所有出款保證費紀錄("27110351738","10401810001889","20680100135997")
$sql = '
	SELECT 
		DISTINCT tMemo, 
		tMoney,
		tKind,
		tBankLoansDate 
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
	$export_data[$rs->fields['tMemo']]['money'] = $rs->fields['tMoney'] ;
	$export_data[$rs->fields['tMemo']]['date'] = ($rs->fields['tKind'] == '保證費')?$rs->fields['tBankLoansDate']:'';
	$rs->MoveNext();
}

##

$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子

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
	//$query .= ' tra.tExport_time<="'.$eSignDate.' 23:59:59" ' ;
}
##
if ($_POST['branchGroup']) {
	$branchGroupData = array();
	$sql = "SELECT bId FROM tBranch WHERE bGroup = '".$_POST['branchGroup']."'";
	// echo $sql;
	// echo $_POST['branchGroup']."<br>";
	// die;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($branchGroupData, $rs->fields['bId']);

		$rs->MoveNext();
	}
	if ($query) { $query .= " AND " ; }
	$query .= '(rea.cBranchNum IN ('.@implode(',', $branchGroupData).') OR rea.cBranchNum1 IN ('.@implode(',', $branchGroupData).') OR rea.cBranchNum2 IN ('.@implode(',', $branchGroupData).') OR rea.cBranchNum3 IN ('.@implode(',', $branchGroupData).'))';
	
}


// 搜尋條件-仲介店
if ($branch) {
	// echo 'CCCCCCCC';
	// print_r($branch);
	for ($i=0; $i < count($branch); $i++) { 
		$branch[$i] = str_replace('b', '', $branch[$i]);
		
	}

	
	if ($query) { $query .= " AND " ; }
	// $query .= ' (rea.cBranchNum="'.$branch.'" OR rea.cBranchNum1="'.$branch.'" OR rea.cBranchNum2="'.$branch.'" OR rea.cBranchNum3="'.$branch.'") ' ;

	$query .= '(rea.cBranchNum IN ('.@implode(',', $branch).') OR rea.cBranchNum1 IN ('.@implode(',', $branch).') OR rea.cBranchNum2 IN ('.@implode(',', $branch).') OR rea.cBranchNum3 IN ('.@implode(',', $branch).'))';
}



// 搜尋條件-地政士
if ($scrivener) {
	// echo 'BBBBBB';
	for ($i=0; $i < count($scrivener); $i++) { 
		$scrivener[$i] = str_replace('s', '', $scrivener[$i]);
	}
	if ($query) { $query .= " AND " ; }
	// $query .= ' csc.cScrivener="'.$scrivener.'" ' ;
	$query .= ' csc.cScrivener IN ('.@implode(',', $scrivener).') ' ;
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
if (($brand != '') && ($realestate != '11') && ($realestate != '12') && ($realestate != '13') && ($realestate != '14') && ($realestate != '5')) {
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
		$zipArr[] = $rs->fields['zZip'] ;

		$rs->MoveNext();
	}
	
	$zipStr = implode('","',$zipArr) ;
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip IN ("'.$zipStr.'") ' ;
	unset($zipArr) ;
	unset($zipStr) ;
}elseif ($_SESSION['member_test'] != 0) {
	if ($sn == '') {
		if ($query) { $query .= " AND " ; }
		$sql = "SELECT
					b.bId
				FROM
					`tZipArea` AS za
				JOIN
					tBranch AS b ON b.bZip = za.zZip
				WHERE
					za.zTrainee = '".$_SESSION['member_test']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$test_tmp[] = "'".$rs->fields['bId']."'";

			$rs->MoveNext();
		}

		$sql = "SELECT
					s.sId
				FROM
					`tZipArea` AS za
				JOIN
					tScrivener AS s ON s.sCpZip1 = za.zZip
				WHERE
					za.zTrainee = '".$_SESSION['member_test']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$test_tmp2[] = "'".$rs->fields['sId']."'";

			$rs->MoveNext();
		}

		$query .= "(rea.cBranchNum IN(".implode(',', $test_tmp).") OR rea.cBranchNum1 IN(".implode(',', $test_tmp).") OR rea.cBranchNum2 IN(".implode(',', $test_tmp).") OR csc.cScrivener IN (".implode(',', $test_tmp2)."))";
		unset($test_tmp);unset($test_tmp2);
	}
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
}else {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus<>"8" ' ;
}

if ($status=='3') {
	$t_day = '結案日期' ;
}else {
	$t_day = '簽約日期' ;
}
##
//地政士類別
if ($scrivener_category) {
	if ($query) { $query .= " AND "  ; }
	// $query .= "scr.sBrand ='2' AND scr.sCategory='1'";
	$query .= ' (find_in_set(2,scr.sBrand) AND scr.sCategory=1)';
}
##
// if ($_SESSION['member_id'] == 6) {
// 	print_r($scrivenerBrand);
// }
//地政是合作仲介品牌
if ($scrivenerBrand) {
	if (is_array($scrivenerBrand)) {
		$scrivenerBrandTxt = @implode(',', $scrivenerBrand);
	}else{
		$scrivenerBrandTxt = $scrivenerBrand;

		$scrivenerBrandArr = explode(',', $scrivenerBrand);

		unset($scrivenerBrand);
		$scrivenerBrand = $scrivenerBrandArr;
		unset($scrivenerBrandArr);

	}
	// $txt =implode(',', $scrivenerBrand);
	$scrivenerStr = '';
	foreach ($scrivenerBrand as $k => $v) {
		if ($scrivenerStr) {
			$scrivenerStr .= ' OR ';
		}
		$scrivenerStr .= ' find_in_set('.$v.',sBrand)';
		
	}

	$sql = "SELECT sId FROM tScrivener WHERE (".$scrivenerStr.") AND sCategory = 1 ";
	
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$tmp[] = 'csc.cScrivener ='.$rs->fields['sId'];

		$rs->MoveNext();
	}
	if ($query) { $query .= " AND "  ; }
	$query .= "(".@implode(' OR ', $tmp).")"; //cs.cScrivener IN(".@implode(',', $tmp).")
	unset($scrivenerStr);unset($tmp);


}

unset($branchGroupData);
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
	cas.cEscrowBankAccount as cEscrowBankAccount,
	buy.cName as buyer, 
	own.cName as owner, 
	inc.cTotalMoney as cTotalMoney, 
	inc.cCertifiedMoney as cCertifiedMoney, 
	csc.cScrivener as cScrivener, 
	(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener, 
	(SELECT b.sOffice FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as sOffice,
	(SELECT b.sCategory FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivenerCategory, 
	pro.cAddr as cAddr, 
	pro.cZip as cZip, 
	zip.zCity as zCity, 
	zip.zArea as zArea, 
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2,
	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
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
	(SELECT bName FROM tBranch WHERE bId=rea.cBranchNum) branchName,
	(SELECT bName FROM tBranch WHERE bId=rea.cBranchNum1) branchName1,
	(SELECT bName FROM tBranch WHERE bId=rea.cBranchNum2) branchName2,
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
	cas.cCaseFeedback3,
	cas.cCaseMoney
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


// die;
$tlog->selectWrite($_SESSION['member_id'], $query, '案件統計表搜尋') ;
$data = array();
$rs = $conn->Execute($query);
while (!$rs->EOF) {
	if (checkSales($rs->fields,$sales)) {
		array_push($data, $rs->fields);
	}

	$rs->MoveNext();
}

$tbl = '' ;

# 取得所有資料
$totalMoney = 0 ;
$certifiedMoney = 0 ;
$transMoney = 0 ;
$j = 0;
$max = count($data);

$cCaseFeedBackMoney = 0;

for ($i = 0 ; $i < $max ; $i ++) {

	
	//取得仲介品牌
		$brand_111 = $data[$j]['brandname'];
		if ($data[$j]['branch1'] > 0) {
			$brand_111 = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$brand_111 ;
			$brand_111.= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$data[$j]['brandname1'] ;
		}

		if ($data[$j]['branch2'] > 0) {
			$brand_111 .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$data[$j]['brandname2']  ;
		}
		$data[$j]['bBrand'] = $brand_111;
		//取得各仲介店姓名
		$bStore = getRealtyName($data[$j]['branch']) ;
		if ($data[$j]['branch1'] > 0) {
			$bStore = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.$bStore ;
			$bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.getRealtyName($data[$j]['branch1']) ;
		}
		if ($data[$j]['branch2'] > 0) {
			$bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.getRealtyName($data[$j]['branch2']) ;
		}

		if ($data[$j]['branch3'] > 0) {
			$bStore .= '<br><span style="font-size:9pt;color:blue;font-weight:bold;">*</span>'.getRealtyName($data[$j]['branch3']) ;
		}

		$data[$j]['bStore'] = $bStore ;
		##
		
		// 簽約日期
		$data[$j]['cSignDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($data[$j]['cSignDate'],Base::REPORT_DATE_FORMAT_NUM_DATE));
		
		##
		
		// 取得匯款金額
		$data[$j]['tMoney'] = $export_data[$data[$j]['cCertifiedId']]['money'] ;
		
		//取得實際出款日
		$data[$j]['tBankLoansDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($export_data[$data[$j]['cCertifiedId']]['date'],Base::REPORT_DATE_FORMAT_NUM_DATE));
		##
		
		// 進案日期
		$data[$j]['cApplyDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($data[$j]['cApplyDate'],Base::REPORT_DATE_FORMAT_NUM_DATE));
		
		##
		
		// 結案日期
		$data[$j]['cEndDate'] = str_replace('-', '/', $advance->ConvertDateToRoc($data[$j]['cEndDate'],Base::REPORT_DATE_FORMAT_NUM_DATE));


		
		if ($data[$j]['branch'] > 0) {
			if ($data[$j]['branch'] != 505) {
				$tmp_sales[] = $data[$j]['sales'];
			}else{
				$tmp_sales[] = $data[$j]['Scrsales'];
			}
			
		}

		if ($data[$j]['branch1'] > 0) {
			$tmp_sales[] = $data[$j]['sales1'];
		}

		if ($data[$j]['branch2'] > 0) {
			$tmp_sales[] = $data[$j]['sales2'];
		}



		$data[$j]['salesName'] = @implode(',', $tmp_sales);
			// echo 'GOGO';
		// print_r($arr[$j]);
		// die;
		unset($tmp_sales);
			$j++;
	
	
	
}
unset($export_data);

##

//決定是否剔除過濾仲介類型

if ($realestate) {	
	$max = count($data) ;
	$list = array() ;
	$j = 0 ;
	for ($i = 0 ; $i < $max ; $i ++) {
		
		$type = branch_type($conn,$data[$i]);
		if ($realestate == '11' && $type == 'O') {
			//$cat = '加盟其他品牌' ;
			$list[$j++] = $data[$i] ;
		}elseif($realestate == '6' && ($type == 'O' || $type == '3')){ //他牌+非仲
			$list[$j++] = $data[$i] ;

		}elseif($realestate == '5' && ($type == 'T' || $type == 'U' || $type == '2')){ //台屋集團
			//
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '12' && $type == 'T') {
			//$cat = '加盟台灣房屋' ;
				$list[$j++] = $data[$i] ;
	
		}
		else if ($realestate == '13' && $type == 'U') {
			//$cat = '加盟優美地產' ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '14' && $type == 'F') {
			//$cat = '加盟永春不動產' ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '1' && ($type == 'O' || $type == 'T' || $type == 'U' || $type == 'F')) {
			//$cat = '所有加盟(其他品牌、台灣房屋、優美地產)' ;
			
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '2' && $type == '2') {
			//$cat = '直營' ;
			//$list[$j++] = $data[$i] ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '3' && $type == '3') {
			//$cat = '非仲介成交' ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '4' && $type == 'N' ) {
			$list[$j++] = $data[$i] ;
		}
	}
	unset($data) ;
	$data = array() ;
	
	$data = array_merge($list) ;

	unset($list);
}



##

// //為了讓總數量跟個別數量加總隊起來，依照他排>台屋
// if ($branch && $report == 2) {
// 	$max = count($data) ;
// 	for ($i=0; $i < $max ; $i++) { 
// 		$type = branch_type2($conn,$data[$i]);

// 		if (in_array($type['bid'], $branch)) {
// 			$list[] = $data[$i];
// 		}
// 		unset($type);
// 	}

// 	unset($data) ;
// 	$data = array() ;	
// 	$data = array_merge($list) ;

// 	unset($list);
// }


//計算總額
$max = count($data) ;
// echo $max;
for ($i = 0 ; $i < $max ; $i ++) {
	$totalMoney += $data[$i]['cTotalMoney'] ;
	$certifiedMoney += $data[$i]['cCertifiedMoney'] ;
	$transMoney += $data[$i]['tMoney'] ;


	if ($branch || $brand) {
				
		if ($data[$i]['branch'] > 0) {
			$tmp_Store['b'.$data[$i]['branch']]['cat'] = $data[$i]['branch'];
		}

		if ($data[$i]['branch1'] > 0) {
			$tmp_Store['b'.$data[$i]['branch1']]['cat'] = $data[$i]['branch1'];
		}

		if ($data[$i]['branch2'] > 0) {
			$tmp_Store['b'.$data[$i]['branch2']]['cat'] = $data[$i]['branch2'];
		}

		if ($data[$i]['branch3'] > 0) {
			$tmp_Store['b'.$data[$i]['branch3']]['cat'] = $data[$i]['branch3'];
		}
		// print_r($branch);
		if ($branch) { //複選
			if (in_array($data[$i]['branch'],$branch)) {
				if ($data[$i]['cCaseFeedback'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney'];
					// echo 'A'.$data[$i]['cCaseFeedBackMoney']."_";
				}
						
			}

			if (in_array($data[$i]['branch1'],$branch)) {
				if ($data[$i]['cCaseFeedback1'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney1'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney1'];

					// echo 'B'.$data[$i]['cCaseFeedBackMoney1']."_";
				}
						
			}

			if (in_array($data[$i]['branch2'],$branch)) {
				if ($data[$i]['cCaseFeedback2'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney2'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney2'];
					// echo 'C'.$data[$i]['cCaseFeedBackMoney2']."_";
				}
						
			}

			if (in_array($data[$i]['branch3'],$branch)) {
				if ($data[$i]['cCaseFeedback3'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney3'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney3'];

					// echo 'D'.$data[$i]['cCaseFeedBackMoney3']."_";
				}
						
			}
		}elseif ($brand) {
			if ($brand == $data[$i]['brand'] ) {
				if ($data[$i]['cCaseFeedback'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney'];
				}
							
			}
			if ($brand == $data[$i]['brand1']) {
				if ($data[$i]['cCaseFeedback1'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney1'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney1'];
				}
							
			}
			if ($brand == $data[$i]['brand2']) {
				if ($data[$i]['cCaseFeedback2'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney2'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney2'];
				}
							
			}
			if ($brand == $data[$i]['brand3']) {
				if ($data[$i]['cCaseFeedback3'] == 0) {
					$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney3'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney3'];
				}
							
			}
		}
				

		if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
			$cCaseFeedBackMoney += $data[$i]['cSpCaseFeedBackMoney'];
			$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cSpCaseFeedBackMoney'];
			// echo 'E'.$data[$i]['cSpCaseFeedBackMoney'];
		}


				//總回饋金額
				// $tmp = getOtherFeedMoney($data[$i]['cCertifiedId']);

				$tmp = getOtherFeed3($data[$i]['cCertifiedId']);
				if (is_array($tmp)) {

					foreach ($tmp as $k => $v) {
						if ($v['fType'] == 2) { //仲介
							if ($branch) {
								if (in_array($v['fStoreId'], $branch)) {
									$cCaseFeedBackMoney += $v['fMoney'];
									$arr[$i]['showcCaseFeedBackMoney'] +=  $v['fMoney'];
								}
							}elseif ($brand) {
								if ($v['storeType'] == $brand) {
									
									$cCaseFeedBackMoney += $v['fMoney'];
									$arr[$i]['showcCaseFeedBackMoney'] +=  $v['fMoney'];
								}
							}
							
						
						}
						
					}
					
					
				}
				unset($tmp);

				//計算總保證費
				// 保證費 要依回饋對像來看
				// 如果AB店配
				// 1.回饋給A或B 那麼保證費就算給A或B
				// 2.回饋給AB 那麼保證費就除以2各半
				// print_r($tmp_Store);
				
				$tmp = getcCertifiedMoney($data[$i]['cCertifiedMoney'],$tmp_Store);
				if (is_array($tmp)) {
					foreach ($tmp as $k => $v) {
						if ($branch) {
							if (in_array($v['cat'], $branch)) {
								$cCertifiedMoney += $v['money'];
							}
						}elseif ($v['cat'] == $brand) {
							$cCertifiedMoney += $v['money'];
						}
						
						

					}
				
				}else{
					//正常情況應該是不會發生沒有店家的問題
					$cCertifiedMoney += $data[$i]['cCertifiedMoney'];

				}
				// echo $cCertifiedMoney;
				
				unset($tmp);
				unset($tmp_Store);



	}else{

				//總回饋金額
				$tmp = getOtherFeedMoney($data[$i]['cCertifiedId']);
				

				if ($data[$i]['brand'] > 0 ) {
					if ($data[$i]['cCaseFeedback'] == 0) {
						$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney'];
						$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney'];
					}
				}

				if ($data[$i]['brand1'] > 0) {
					if ($data[$i]['cCaseFeedback1'] == 0) {
						$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney1'];
						$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney1'];
					}
				}

				if ($data[$i]['brand2'] > 0) {
					if ($data[$i]['cCaseFeedback2'] == 0) {
						$cCaseFeedBackMoney += $data[$i]['cCaseFeedBackMoney2'];
						$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cCaseFeedBackMoney2'];
					}
				}

				

				if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
					$cCaseFeedBackMoney += $data[$i]['cSpCaseFeedBackMoney'];
					$data[$i]['showcCaseFeedBackMoney'] += $data[$i]['cSpCaseFeedBackMoney'];
				}


				if ($tmp['fMoney'] > 0) {
					
					$cCaseFeedBackMoney += $tmp['fMoney'];
					$data[$i]['showcCaseFeedBackMoney'] += $tmp['fMoney'];
				}


				
				unset($tmp);
	}
}
##
//產出excel檔
include_once 'excel.php' ;
die;

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
	$zc = $data[$i]['zCity'] ;
	$data[$i]['cAddr'] = preg_replace("/$zc/","",$data[$i]['cAddr']) ;
	$zc = $data[$i]['zArea'] ;
	$data[$i]['cAddr'] = preg_replace("/$zc/",'',$data[$i]['cAddr']) ;
	$data[$i]['cAddr'] = $data[$i]['zCity'].$data[$i]['zArea'].$data[$i]['cAddr'] ;

##
	$data[$i]['cCertifiedMoney']=$data[$i]['cCertifiedMoney'];
	$tmp = round(($data[$i]['cTotalMoney']-$data[$i]['cFirstMoney'])*0.0006); //萬分之六
	$tmp2 = round(($data[$i]['cTotalMoney']-$data[$i]['cFirstMoney'])*0.0006)*0.1;
  	


	if(($tmp-$tmp2)>$data[$i]['cCertifiedMoney']){ //合約保證費 如果未達6/10000的合約保證費  在合約保證費的金額位置 加註星星 
	
		$data[$i]['cCertifiedMoney']= '*'.$data[$i]['cCertifiedMoney'] ;
	}else
	{
		$data[$i]['cCertifiedMoney']= $data[$i]['cCertifiedMoney'] ;
	}
##	
	$tbl .= '
	<tr style="text-align:center;background-color:'.$color_index.'">
		<td>'.($j++).'</td>
		<td><a href="#" onclick=contract("'.$data[$i]['cCertifiedId'].'")>'.$data[$i]['cCertifiedId'].'</a>&nbsp;</td>
		<td>'.$data[$i]['bBrand'].'</td>
		<td>'.$data[$i]['bStore'].'&nbsp;</td>
		<td>'.$data[$i]['owner'].'&nbsp;</td>
		<td>'.$data[$i]['buyer'].'&nbsp;</td>
		<td style="text-align:right;">'.number_format($data[$i]['cTotalMoney']).'&nbsp;</td>
		<td style="text-align:right;">'.$data[$i]['cCertifiedMoney'].'&nbsp;</td>
		' ;

		if ($status=='3') { 
			$tbl .= '<td>'.$data[$i]['cEndDate'].'&nbsp;</td>' ; 
		}
		else {
			$tbl .= '<td>'.$data[$i]['cSignDate'].'&nbsp;</td>' ; 
		}
	
	$tbl .= '
		<td>'.$data[$i]['cApplyDate'].'&nbsp;</td>
		
		<td>'.$data[$i]['scrivener'].'&nbsp;</td>
		<td>'.$data[$i]['status'].'&nbsp;</td>
	</tr>
	' ;
	/*<td>'.$arr[$i]['cEndDate'].'&nbsp;</td>
	if (!preg_match("/^0000-00-00 00:00:00$/",$arr[$i]['cFinishDate'])) {
		$cFinishDate = preg_replace("/ \d+:\d+:\d+$/","",$arr[$i]['cFinishDate']) ;
		$tmp = explode('-',$cFinishDate) ;
		$cFinishDate = ($tmp[0] - 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
		
		$tbl .= '
			<td>'.$cFinishDate.'&nbsp;</td>
			<td>'.$arr[$i]['scrivener'].'&nbsp;</td>
			<td>'.$arr[$i]['status'].'&nbsp;</td>
		</tr>
		' ;
	}
	*/
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

// include('closedb.php') ;

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
$conn->close();
# 頁面資料
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',number_format($max)) ;

# 搜尋資訊
$smarty->assign('bank',$bank) ;
$smarty->assign('sApplyDate',$_POST['sApplyDate']) ;
$smarty->assign('eApplyDate',$_POST['eApplyDate']) ;
$smarty->assign('sEndDate',$_POST['sEndDate']) ;
$smarty->assign('eEndDate',$_POST['eEndDate']) ;
$smarty->assign('sSignDate',$_POST['sSignDate']) ;
$smarty->assign('eSignDate',$_POST['eSignDate']) ;
$smarty->assign('branch',$_POST['branch']) ;
$smarty->assign('scrivener',$_POST['scrivener']) ;
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
$smarty->assign('scrivenerBrand',$scrivenerBrandTxt);
$smarty->assign('report',$report);
$smarty->assign('branchGroup',$_POST['branchGroup']);
# 搜尋結果
$smarty->assign('tbl',$tbl) ;
$smarty->assign('totalMoney',$totalMoney) ;
$smarty->assign('certifiedMoney',$certifiedMoney) ;
$smarty->assign('cCertifiedMoney',$cCertifiedMoney);//只查詢店家跟仲介
$smarty->assign('cCaseFeedBackMoney',$cCaseFeedBackMoney);
// $smarty->assign('income',number_format(($certifiedMoney-$cCaseFeedBackMoney)));
$smarty->assign('transMoney',number_format($transMoney)) ;


$smarty->assign('show_hide',$show_hide) ;
$smarty->assign('realestate',$realestate) ;

# 其他
$smarty->assign('functions',$functions) ;
$smarty->assign('t_day',$t_day) ;
$smarty->display('applycase_result.inc.tpl', '', 'report');
?>