<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
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
$category .= "<option value='5'>台屋集團</option>\n" ;
$category .= "<option value='6'>他牌+非仲</option>\n" ;
##

//簽約銀行
$sql = 'SELECT cBankCode,(SELECT cBankName FROM tCategoryBank WHERE cId=cbk.cBankCode) cBankName FROM tContractBank AS cbk WHERE cbk.cShow="1"  ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$contract_bank .= "<option value='".$rs->fields['cBankCode']."'>".$rs->fields['cBankName']."</option>\n" ; ;

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

//縣市
$sql = 'SELECT zCity FROM tZipArea WHERE 1=1 '.$z_str.'  GROUP BY zCity ORDER BY zZip,zCity ASC;' ;
$rs = $conn->Execute($sql);
$citys = '<option selected="selected" value="">全部</option>'."\n" ;
while (!$rs->EOF) {
	$citys .= '<option value="'.$tmp['zCity'].'">'.$tmp['zCity']."</option>\n" ;
	

	$rs->MoveNext();
}


##

##
//仲介商

$sql = '
SELECT 
	bId,
	bName,
	bStore,
	CONCAT((SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand ),LPAD(bId,5,"0")) as bCode,
	(SELECT bName FROM tBrand AS c WHERE c.bId=b.bBrand) bBrand,
	bStatus
FROM 
	tBranch b
WHERE
	b.bId NOT IN (0,980)
	
	'.$b_str.'
;' ;
$branch_search = '' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	if (preg_match("/自有品牌/",$rs->fields['bBrand'])) {
		$rs->fields['bBrand'] = '自有品牌' ;
	}

	if ($rs->fields['bStatus'] == 2) {
		$rs->fields['bStatus']="[關店]";
	}elseif ($rs->fields['bStatus'] == 3) {
		$rs->fields['bStatus']="[暫停]";
	}else{
		$rs->fields['bStatus']='';
	}
	$branch_search .= "<option value='".$rs->fields['bId']."'>".$rs->fields['bCode'].$rs->fields['bBrand'].$rs->fields['bStore'].$rs->fields['bStatus']."</option>\n" ;


	$rs->MoveNext();
}

##

//地政士
$sql = '
SELECT 
	sId,
	sName,
	CONCAT("SC",LPAD(sId,4,"0")) as Code 
FROM 
	tScrivener 
GROUP BY 
	sId 
ASC;
' ;
$rs = $conn->Execute($sql);
$scrivener_search = '' ;
while (!$rs->EOF) {
	$scrivener_search .= "<option value='".$rs->fields['sId']."'>".$rs->fields['Code'].$rs->fields['sName']."</option>\n" ;
	

	$rs->MoveNext();
}


$sql = "SELECT pName,pId FROM tPeopleInfo WHERE pDep IN(7,4) AND pJob = 1";
$menuSalse = '' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuSalse .= "<option value='".$rs->fields['pId']."'>".$rs->fields['pName']."</option>\n" ;
	

	$rs->MoveNext();
}

##

$smarty->assign('menuSalse',$menuSalse);
$smarty->assign('brand', $brand) ;
$smarty->assign('status', $status) ;
$smarty->assign('category', $category) ;
$smarty->assign('contract_bank', $contract_bank) ;
$smarty->assign('branch_search', $branch_search) ;
$smarty->assign('scrivener_search', $scrivener_search) ;
$smarty->assign('undertaker', $undertaker) ;
$smarty->assign('citys', $citys) ;
$smarty->display('applycase2019.inc.tpl', '', 'report') ;

?> 
