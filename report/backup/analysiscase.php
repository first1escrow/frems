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
$query = 'SELECT bId,bName FROM tBrand ORDER BY bId ASC;' ;

$rs = $conn->Execute($query);

while (!$rs->EOF) {
	
	$brand .= "<option value='".$rs->fields['bId']."'>".$rs->fields['bName']."</option>\n" ; ;

	$rs->MoveNext();
}

##

//取得案件狀態列表
$query = 'SELECT sId,sName FROM tStatusCase WHERE sId<>"0" ORDER BY sId ASC;' ;

$rs = $conn->Execute($query);


while (!$rs->EOF) {
	
		$status .= "<option value='".$rs->fields['sId']."'>".$rs->fields['sName']."</option>\n" ;
	$rs->MoveNext(); 

}

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

##

//仲介商
$sql = '
SELECT 
	bId,
	bName,
	bStore,
	(SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand) bCode,
	(SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) brand 
FROM 
	tBranch b
;
' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$rs->fields['bCode'] = $rs->fields['bCode'].str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT);
	$branch_search .= "<option value='".$rs->fields['bId']."'>".$rs->fields['bCode'].$rs->fields['brand'].$rs->fields['bStore']."</option>\n" ;

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
GROUP BY 
	sName 
ASC;
' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$scrivener_search .= "<option value='".$rs->fields['sId']."'>".$rs->fields['sName']."</option>\n" ;

	$rs->MoveNext();
}

##
//縣市
$sql = 'SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY zZip,zCity ASC;' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$citys .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;

	$rs->MoveNext();
}
##
//業務下拉

$sql="SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(4,7) AND pJob=1";
$rs=$conn->Execute($sql);
$sales_option[0]='全部';
while (!$rs->EOF) {
	$sales_option[$rs->fields['pId']] = $rs->fields['pName'];
	$rs->MoveNext();
}
##
##
//店東下拉
$sql = "SELECT  `bName` ,  `bManager` FROM  `tBranch` WHERE bManager !='' GROUP BY bManager ORDER BY `bId` DESC";
$rs = $conn->Execute($sql);
$manager_option[0] = '請選擇';
while (!$rs->EOF) {
	$manager_option[$rs->fields['bManager']] = $rs->fields['bManager'];
	$rs->MoveNext();
}
##

##

##
//群組
$sql = "SELECT bId,bName FROM tBranchGroup ORDER BY bId ASC";
$rs = $conn->Execute($sql);
$group_option[0] = '請選擇';
while (!$rs->EOF) {
	$group_option[$rs->fields['bId']] = $rs->fields['bName'];
	$rs->MoveNext();
}

##
//年


$now_y = date('Y')-1911;

$year_option[0] = '請選擇';
for ($i=$y; $i <= $now_y; $i++) { 
	
	$year_option[$i]=$i;

}

##
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN('5','6') AND pJob=1";

$rs = $conn->Execute($sql);
$undertaker[0] = '請選擇';
while (!$rs->EOF) {
	
	$undertaker[$rs->fields['pId']] = $rs->fields['pName'];

	$rs->MoveNext();
}

##
$smarty->assign('brand', $brand) ;
$smarty->assign('status', $status) ;
$smarty->assign('category', $category) ;
$smarty->assign('contract_bank', $contract_bank) ;
$smarty->assign('branch_search', $branch_search) ;
$smarty->assign('scrivener_search', $scrivener_search) ;
$smarty->assign('sales_option', $sales_option) ;
$smarty->assign('manager_option', $manager_option) ;
$smarty->assign('group_option', $group_option) ;
$smarty->assign('year_option', $year_option) ;
$smarty->assign('now_y',$now_y);
$smarty->assign('undertaker', $undertaker) ;
$smarty->assign('citys', $citys) ;

$smarty->display('analysiscase.inc.tpl', '', 'report') ;
?> 
