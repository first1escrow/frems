<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../opendb.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php';
##
 $_POST = escapeStr($_POST) ;
$dateCategory = ($_POST['dateCategory'])?$_POST['dateCategory']:1;
$timeCategory = ($_POST['timeCategory'])?$_POST['timeCategory']:'y';

$endYear = ($_POST['endYear'])?$_POST['endYear']:(date('Y')-1911);
$startYear = ($_POST['startYear'])?$_POST['startYear']:(date('Y')-1911);
$tab = ($_POST['tab'])?$_POST['tab']:'sales';

// echo $tab;
##


##
// 店名選單
$sql = 'SELECT bId,bStore,(SELECT bCode FROM tBrand  WHERE bId=bBrand) AS bCode,(SELECT bName FROM tBrand  WHERE bId=bBrand) AS brand FROM tBranch  WHERE bId <> "0" ORDER BY bId ASC;' ;
$rs = $conn->Execute($sql);
$menu_branch[0] = '';
while (!$rs->EOF) {
	
	// $menu_branch .= '<option value="'.$rs->fields['bId'].'">'.$rs->fields['bCode'].str_pad($rs->fields['bId'],5,"0",STR_PAD_LEFT).'/'.$rs->fields['bStore']."</option>\n" ;
	$menu_branch[$rs->fields['bId']] = $rs->fields['bCode'].str_pad($rs->fields['bId'],5,"0",STR_PAD_LEFT).'/'.$rs->fields['brand'].$rs->fields['bStore'];

	$rs->MoveNext();
}

//地政士
$sql = "SELECT sId,sName,sOffice FROM tScrivener ORDER BY sId";
$rs = $conn->Execute($sql);
$menu_scrivener = array();
$menu_scrivener[0] = '';
while (!$rs->EOF) {
	
	$menu_scrivener[$rs->fields['sId']] = 'SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT).$rs->fields['sName'].'('.$rs->fields['sOffice'].')';
	$rs->MoveNext();
}

//品牌
$sql = "SELECT bId,bName FROM tBrand";
$rs = $conn->Execute($sql);
$menu_brand = array();
$menu_brand[0] = '';
while (!$rs->EOF) {
	$menu_brand[$rs->fields['bId']] = $rs->fields['bName'];

	$rs->MoveNext();
}
//仲介類別
$menu_brandCategory = array('0'=>'全部','11'=>'加盟(其他品牌)','12'=>'加盟(台灣房屋)','14'=>'加盟(永春不動產)','1'=>'加盟','2'=>'直營','3'=>'非仲介成交','4'=>'其他(未指定)');
//地區
$sql = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs = $conn->Execute($sql);
$menu_city[0] = '全部';
while (!$rs->EOF) {
	$menu_city[$rs->fields['zCity']] = $rs->fields['zCity'];

	$rs->MoveNext();
}
//銀行
// 簽約銀行
$sql = 'SELECT cBankCode,(SELECT cBankName FROM tCategoryBank WHERE cId=cbk.cBankCode) cBankName FROM tContractBank AS cbk WHERE cbk.cShow="1"  ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql);
$menu_bank = array();
$menu_bank[0] = "全部" ;
while (!$rs->EOF) {
	$menu_bank[$rs->fields['cBankCode']] = $rs->fields['cBankName'];
	
	$rs->MoveNext();
}

## 
//,'branch'=>'店家'
$menu_dateCategory = array(1=>'進案',2=>'簽約',3=>'結案');
$menu_tab = array('sales'=>'業務','brand'=>'品牌','storearea'=>'店區域(仲介、地政士)','brandCategory'=>'品牌類別','branchGroup'=>'仲介群組');
$menu_timeCategory = array('y'=>'年','s'=>'季','m'=>'月');
$menu_Year = array();
for ($i=100; $i <= (date('Y')-1911); $i++) { 
	$menu_Year[$i] = $i;
}

##
$smarty->assign('menu_dateCategory',$menu_dateCategory);
$smarty->assign('menu_tab',$menu_tab);
$smarty->assign('menu_timeCategory',$menu_timeCategory);
$smarty->assign('menu_Year',$menu_Year);
$smarty->assign('menu_branch',$menu_branch);
$smarty->assign('menu_scrivener',$menu_scrivener);
$smarty->assign('menu_brand',$menu_brand);
$smarty->assign('menu_brandCategory',$menu_brandCategory);
$smarty->assign('menu_city',$menu_city);
$smarty->assign('menu_bank',$menu_bank);
##
$smarty->assign('dateCategory',$dateCategory);
$smarty->assign('timeCategory',$timeCategory);
$smarty->assign('startYear',$startYear);
$smarty->assign('endYear',$endYear);
$smarty->assign('tab',$tab);
##

$smarty->display('analysiscase.inc.tpl', '', 'report') ;
?> 
