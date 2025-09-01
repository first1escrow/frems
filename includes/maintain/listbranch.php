<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/getAddress.php' ;
include_once 'class/brand.class.php';
include_once '../session_check.php' ;
include_once '../openadodb.php';
include_once '../../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_GET), '查詢仲介店列表') ;

if ($_GET['sBrand']) {
	$brand2 = addslashes(trim($_GET['sBrand']));
}

if ($_GET['sZip']) {
	$zip = addslashes(trim($_GET['sZip']));
}

$sales = $_GET['salesman'] ;

$salesman = '<option value="">請選擇業務身分</option>' ;
$sql = 'SELECT * FROM tPeopleInfo WHERE pDep IN (4,7) ORDER BY pId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$salesman .= '<option value="'.$rs->fields['pId'].'"' ;
	if ($sales == $rs->fields['pId']) $salesman .= ' selected="selected"' ;
	$salesman .= '>'.$rs->fields['pName']."</option>\n" ;
	
	$rs->MoveNext() ;
}


##品牌選單
$brand = new Brand();

$list_brand = $brand->GetBrandList(array(8, 77));

$menu_brand = $brand->ConvertOption($list_brand, 'bId', 'bName');
$menu_brand [0] = "請選擇";

ksort($menu_brand);
// array_unshift($menu_brand,'請選擇');
##


$smarty->assign('menu_brand',$menu_brand);
$smarty->assign('country', listCity($conn,$zip)) ;//縣市

$smarty->assign('area', listArea($conn,$zip)) ;//鄉鎮區域

$smarty->assign('salesman',$salesman) ;
				
$smarty->assign('search_brand',$brand2) ;
$smarty->assign('search_zip',$zip);

$smarty->display('listbranch.inc.tpl', '', 'maintain');
?> 
