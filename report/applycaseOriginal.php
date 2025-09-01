<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$brand = '' ;
$status = '' ;
$category = '' ;
$contract_bank = '' ;

//取得房仲品牌列表
$sql = 'SELECT bId,bName FROM tBrand ORDER BY bId ASC;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$brand .= "<option value='".$rs->fields['bId']."'>".$rs->fields['bName']."</option>\n" ; 

	$rs->MoveNext();
}

##

//取得案件狀態列表
$sql = 'SELECT sId,sName FROM tStatusCase WHERE sId<>"0" ORDER BY sId ASC;' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$status .= "<option value='".$rs->fields['sId']."'>".$rs->fields['sName']."</option>\n" ;

	$rs->MoveNext();
}

##


$category = "<option value='11'>加盟(其他品牌)</option>\n" ;
$category .= "<option value='12'>加盟(台灣房屋)</option>\n" ;
$category .= "<option value='13'>加盟(優美地產)</option>\n" ;
$category .= "<option value='14'>加盟(永春不動產)</option>\n" ;
$category .= "<option value='1'>加盟</option>\n" ;
$category .= "<option value='2'>直營</option>\n" ;
$category .= "<option value='3'>非仲介成交</option>\n" ;
$category .= "<option value='4'>其他(未指定)</option>\n" ;
##

//簽約銀行
$query = 'SELECT cBankCode,(SELECT cBankName FROM tCategoryBank WHERE cId=cbk.cBankCode) cBankName FROM tContractBank AS cbk WHERE cbk.cShow="1"  ORDER BY cId ASC;' ;
$rs = $conn->Execute($query);

while (!$rs->EOF) {
	$contract_bank .= "<option value='".$rs->fields['cBankCode']."'" ;
	$contract_bank .= ">".$rs->fields['cBankName']."</option>\n" ;
	$rs->MoveNext();
}



//承辦人
$sql = '
	SELECT 
		b.pName as undertaker,
		b.pId as cUndertakerId 
	FROM 
		tContractCase AS a
	JOIN
		tPeopleInfo AS b ON b.pId=a.cUndertakerId
	WHERE
		b.pJob="1" 
		AND b.pId<>"6"
		AND pDep IN("5","6")
	GROUP BY
		b.pId
;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$undertaker .= "<option value='".$rs->fields['cUndertakerId']."'>".$rs->fields['undertaker']."</option>\n" ;
	$rs->MoveNext();
}

##

if ($_SESSION['member_id'] == 32) {
	$z_str = ' AND zCity IN ("高雄市","屏東縣")';
}elseif ($_SESSION['member_id'] == 34) {
	$z_str = ' AND zCity IN ("嘉義縣","嘉義市","雲林縣","台南市")';
}elseif ($_SESSION['member_id'] == 25) {
	$z_str = ' AND zSales =25';
}elseif ($_SESSION['member_id'] == 42) {
	$z_str = ' AND zCity IN ("台中市","彰化縣","南投縣")';
}elseif ($_SESSION['member_id'] == 41) {
	$z_str = ' AND zCity IN ("新北市","台北市","新竹縣","新竹市","桃園市")';
}

//縣市
$citys = '<option selected="selected" value="">全部</option>'."\n" ;
$sql = 'SELECT zCity FROM tZipArea WHERE 1=1  GROUP BY zCity ORDER BY zZip,zCity ASC;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$citys .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;

	$rs->MoveNext();
}


##
//業務區域
$sql = 'SELECT zZip FROM tZipArea WHERE 1=1 '.$z_str.'  ORDER BY zZip,zCity ASC;' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$zip[] = "'".$rs->fields['zZip']."'";

	$rs->MoveNext();
}



$tmp_z = implode(',', $zip);
$s_str = "1=1";
if ($_SESSION['member_id'] == 32 || $_SESSION['member_id'] == 34||$_SESSION['member_id']==25) {		
		$b_str = ' AND b.bZip IN ('.$tmp_z.')';
		$s_str = ' sZip1 IN('.$tmp_z.')';
}

##
//仲介商

$sql = '
SELECT 
	bId,
	bName,
	bStore,
	(SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand) bCode,
	(SELECT bName FROM tBrand AS c WHERE c.bId=b.bBrand) bBrand,
	bStatus
FROM 
	tBranch b
WHERE
	b.bId NOT IN (0,980)
	'.$b_str.'
;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	// $status = '';
	if (preg_match("/自有品牌/",$rs->fields['bBrand'])) {
		$rs->fields['bBrand'] = '自有品牌' ;
	}

	if ($rs->fields['bStatus'] == 2) {
		$rs->fields['bStatus'] = "[關店]";
	}elseif ($rs->fields['bStatus'] == 3) {
		$rs->fields['bStatus'] = "[暫停]";
	}else {
		$rs->fields['bStatus'] = "";
	}

	// $branch_search .= "<option value='".$rs->fields['bId']."'>".$rs->fields['bBrand'].$rs->fields['bStore']."</option>\n" ;
	$branch_search .= "<option value='".$rs->fields['bId']."'>".$rs->fields['bCode'].str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT).$rs->fields['bBrand'].$rs->fields['bStore'].$rs->fields['bStatus']."</option>\n" ;

	$rs->MoveNext();
}

##

//地政士
$sql = '
SELECT 
	sId,
	sName 
FROM 
	tScrivener 
WHERE 
'.$s_str.'
GROUP BY 
	sName 
ASC;
' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	# code...
	$scrivener_search .= "<option value='".$rs->fields['sId']."'>".$rs->fields['sName']."</option>\n" ;
	$rs->MoveNext();
}

##


$smarty->assign('brand', $brand) ;
$smarty->assign('status', $status) ;
$smarty->assign('category', $category) ;
$smarty->assign('contract_bank', $contract_bank) ;
$smarty->assign('branch_search', $branch_search) ;
$smarty->assign('scrivener_search', $scrivener_search) ;
$smarty->assign('undertaker', $undertaker) ;
$smarty->assign('citys', $citys) ;
$smarty->display('applycaseOriginal.inc.tpl', '', 'report') ;
?> 
