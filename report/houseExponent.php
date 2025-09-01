<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;

$brand = '' ;
$status = '' ;
$category = '' ;
$contract_bank = '' ;

//產出 excel 檔案
if ($_POST['go'] == 'excel') {
	$bank = trim($_POST['bank']) ;
	$cSignDateFrom = trim($_POST['cSignDateFrom']) ;
	$cSignDateTo = trim($_POST['cSignDateTo']) ;
	$brand = trim($_POST['brand']) ;
	$realestate = trim($_POST['realestate']) ;
	$city = trim($_POST['city']) ;
	$area = trim($_POST['area']) ;
	$zip = trim($_POST['zip']) ;
	
	$tlog->exportWrite($_SESSION['member_id'], json_encode($_POST), '匯出房價指數統計表Excel') ;
	
	include_once 'houseExpExcel.php' ;
	exit ;
}
##

//設定日期
if ($cSignDateFrom) $sSingDate = $cSignDateFrom ;
else {
	$sSingDate = date("Y-m-01",strtotime('last month')) ;
	$tmp = explode("-",$sSingDate) ;
	$tmp[0] -= 1911 ;
	$sSingDate = implode('-',$tmp) ;
	unset($tmp) ;
}

if ($cSignDateTo) $eSignDate = $cSignDateTo ;
else {
	$eSignDate = date("Y-m-t",strtotime('last month')) ;
	$tmp = explode("-",$eSignDate) ;
	$tmp[0] -= 1911 ;
	$eSignDate = implode('-',$tmp) ;
	unset($tmp) ;
}
##

//取得房仲品牌列表
$sql = 'SELECT bId,bName FROM tBrand ORDER BY bId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$brand .= "<option value='".$rs->fields['bId']."'>".$rs->fields['bName']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//取得案件狀態列表
$sql = 'SELECT sId,sName FROM tStatusCase WHERE sId<>"0" ORDER BY sId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$status .= "<option value='".$rs->fields['sId']."'>".$rs->fields['sName']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//仲介商類型列表
$category = "<option value='11'>加盟(其他品牌)</option>\n" ;
$category .= "<option value='12'>加盟(台灣房屋)</option>\n" ;
$category .= "<option value='13'>加盟(優美地產)</option>\n" ;
$category .= "<option value='1'>加盟</option>\n" ;
$category .= "<option value='2'>直營</option>\n" ;
$category .= "<option value='3'>非仲介成交</option>\n" ;
$category .= "<option value='4'>其他(未指定)</option>\n" ;
##

//簽約銀行
$sql = 'SELECT cBankCode,(SELECT cBankName FROM tCategoryBank WHERE cId=cbk.cBankCode) cBankName FROM tContractBank AS cbk WHERE cbk.cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$contract_bank .= "<option value='".$rs->fields['cBankCode']."'>".$rs->fields['cBankName']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//縣市
$sql = 'SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY zZip,zCity ASC;' ;
$rs = $conn->Execute($sql) ;
$citys = '<option selected="selected" value="">縣市</option>'."\n" ;
while (!$rs->EOF) {
	$citys .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//
$smarty->assign('brand', $brand) ;
$smarty->assign('status', $status) ;
$smarty->assign('category', $category) ;
$smarty->assign('contract_bank', $contract_bank) ;
$smarty->assign('citys', $citys) ;
$smarty->assign('sSingDate', $sSingDate) ;
$smarty->assign('eSignDate', $eSignDate) ;

$smarty->display('houseExponent.inc.tpl', '', 'report') ;
##
?> 
