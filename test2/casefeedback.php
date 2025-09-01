<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
// include_once '../opendb.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;
$category = '' ;
$contract_bank = '' ;

// 店名選單
$sql = 'SELECT bId,bStore,(SELECT bCode FROM tBrand  WHERE bId=bBrand) bCode FROM tBranch  WHERE bId <> "0" ORDER BY bId ASC;' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$menu_branch .= '<option value="'.$rs->fields['bId'].'">'.$rs->fields['bCode'].str_pad($rs->fields['bId'],5,"0",STR_PAD_LEFT).'/'.$rs->fields['bStore']."</option>\n" ;


	$rs->MoveNext();
}
##


// 簽約銀行
$query = 'SELECT cBankCode,(SELECT cBankName FROM tCategoryBank WHERE cId=cbk.cBankCode) cBankName FROM tContractBank AS cbk WHERE cbk.cShow="1"  ORDER BY cId ASC;' ;
$rs = $conn->Execute($query);
$contract_bank .= '<option value="" selected="selected">全部</option>'."\n" ;
while (!$rs->EOF) {
	$contract_bank .= "<option value='".$tmp['cBankCode']."'" ;
	$contract_bank .= ">".$tmp['cBankName']."</option>\n" ;

	$rs->MoveNext();
}



$this_year = date("Y") - 1911 ;
$this_month = date("m") ;

// 年度
$sales_year = '' ;
for ($i = $this_year + 2 ; $i > $this_year - 100 ; $i --) {
	$sales_year .= '<option value="'.($i + 1911).'"' ;
	if ($i == $this_year) { $sales_year .= ' selected="selected"' ; }
	$sales_year .= '>'.$i."</option>\n" ;
}
##
	//echo $sales_year."<br>\n" ;

// 季
if ($this_month >= 1 && $this_month <= 3) {
	$seasons = 'S1' ;
}
else if ($this_month >= 4 && $this_month <= 6) {
	$seasons = 'S2' ;
}
else if ($this_month >= 7 && $this_month <= 9) {
	$seasons = 'S3' ;
}
else {
	$seasons = 'S4' ;
}
##
//地政士
$sql = "SELECT sId,sName,sOffice FROM tScrivener ORDER BY sId";

$rs = $conn->Execute($sql);
$menu_scr[0] = '';
while (!$rs->EOF) {
	
	$menu_scr[$rs->fields['sId']] = 'SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT).$rs->fields['sName'].'('.$rs->fields['sOffice'].')';
	$rs->MoveNext();
}
##
//
$menu_brand = array();
$sql = "SELECT bId,bName FROM tBrand ORDER BY bId ASC";
$rs = $conn->Execute($sql);
$menu_brand[0] = '請選擇';
while (!$rs->EOF) {
	$menu_brand[$rs->fields['bId']] = $rs->fields['bName'];

	$rs->MoveNext();
}

##
$smarty->assign('menu_brand',$menu_brand);
$smarty->assign('menu_year',$sales_year);
$smarty->assign('menu_season', array(
									'S1' => '第一季', 
									'S2' => '第二季',
									'S3' => '第三季',
									'S4' => '第四季',
									'01' => '1月份',
									'02' => '2月份',
									'03' => '3月份',
									'04' => '4月份',
									'05' => '5月份',
									'06' => '6月份',
									'07' => '7月份',
									'08' => '8月份',
									'09' => '9月份',
									'10' => '10月份',
									'11' => '11月份',
									'12' => '12月份')
									);

$smarty->assign('seasons', $seasons);
$smarty->assign('menu_branch', $menu_branch);
$smarty->assign('menu_scr',$menu_scr);
$smarty->assign('contract_bank', $contract_bank);
$smarty->display('casefeedback.inc.tpl', '', 'report');
?> 
