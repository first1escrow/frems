<?php
include_once dirname(__DIR__) . '/configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/openadodb.php' ;
include_once dirname(__DIR__) . '/session_check.php' ;


$year = $_POST['year'] ;				//查詢回饋年度
$season = $_POST['season'] ;			//查詢回饋季
##
// print_r($_POST);
if ($_POST['allForm']) {
	include_once 'storeFeedBack_excel.php';
}

// echo "<pre>";
// print_r($list);
#
$this_year = date("Y") - 1911 ;
$this_month = date("m") ;

// 年度
$year = '' ;
for ($i = $this_year + 2 ; $i > $this_year - 100 ; $i --) {
	$year .= '<option value="'.($i + 1911).'"' ;
	if ($i == $this_year) { $year .= ' selected="selected"' ; }
	$year .= '>'.$i."</option>\n" ;
}

//// 店名選單
$sql = 'SELECT bId,bStore,(SELECT bCode FROM tBrand  WHERE bId=bBrand) bCode FROM tBranch  WHERE bId <> "0" ORDER BY bId ASC;' ;
$rs = $conn->Execute($sql);
$menu_branch = array();
$menu_branch[0] = '';
while (!$rs->EOF) {

	$menu_branch[$rs->fields['bId']] = $rs->fields['bCode'].str_pad($rs->fields['bId'],5,"0",STR_PAD_LEFT).'/'.$rs->fields['bStore'];	
	$rs->MoveNext();
}


//地政士
$sql = "SELECT sId,sName,sOffice FROM tScrivener ORDER BY sId";
$rs = $conn->Execute($sql);
$menu_scrivener[0] = '';
while (!$rs->EOF) {
	
	$menu_scrivener[$rs->fields['sId']] = 'SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT).$rs->fields['sName'].'('.$rs->fields['sOffice'].')';
	$rs->MoveNext();
}
##
//匯出批次
$menu_exp = array(0=>'全部');
$sql = "SELECT sExportTime FROM tStoreFeedBackMoneyFrom WHERE sExportTime != '' GROUP BY sExportTime ORDER BY sExportTime DESC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menu_exp[$rs->fields['sExportTime']] = $rs->fields['sExportTime'];
	
	$rs->MoveNext();
}
// echo "<pre>";
// print_r($menu_scrivener);

##
$smarty->assign('menu_branch',$menu_branch);
$smarty->assign('menu_scrivener',$menu_scrivener);
$smarty->assign('menu_exp',$menu_exp);
##
$smarty->assign('menu_year',$year);
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
$smarty->assign('list',$list);
$smarty->display('storeFeedBack.inc.tpl', '', 'accounting');
?>