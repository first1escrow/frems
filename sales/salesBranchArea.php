<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../includes/first1Sales.php';
include_once '../includes/sales/getSalesArea.php';
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$ok = trim($_POST['ok']);
$sales = trim($_POST['sales']);
$xls = trim($_POST['xls']);

$sql = "SELECT 
			bId,
			bStore,
			(SELECT bName FROM tBrand AS c WHERE c.bId = bBrand) AS brand,
			CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as bCode
		FROM
			tBranch WHERE bStatus = 1 ORDER BY bId ASC";

$rs = $conn->Execute($sql);
$menu_Store[0] = '';
while (!$rs->EOF) {
	$menu_Store[$rs->fields['bId']] = $rs->fields['bCode'].$rs->fields['brand'].$rs->fields['bStore'];

	$rs->MoveNext();
}
##
$areaSales = array() ;
$menu_City = array();
$menu_City[0] = '請選擇';
$sql = 'SELECT zCity as city, zSales as salesId, zSalesTwhg FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menu_City[$rs->fields['city']] = $rs->fields['city'];

	$tmp = @explode(',', $rs->fields['salesId']);
		
	foreach ($tmp as $key => $value) {
		$areaSales[$rs->fields['city']]['sales'][$value] = getSalesName($value);
	}

	unset($tmp);

	$tmp = @explode(',', $rs->fields['zSalesTwhg']);
		
	foreach ($tmp as $key => $value) {
		$areaSales[$rs->fields['city']]['salesTW'][$value] = getSalesName($value);
	}

	unset($tmp);

	
	$rs->MoveNext();
}

// echo "<pre>";
// print_r($areaSales);

if ($_POST['default_city']) {
	$list_Area = array();
	$sql = "SELECT zZip,zArea,zSales,zSalesTwhg FROM tZipArea WHERE zCity = '".$_POST['default_city']."'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$list_Area[$rs->fields['zZip']]['area'] = $rs->fields['zArea'];

		$tmp = @explode(',', $rs->fields['zSales']);
		// print_r($tmp);
		// die;
		foreach ($tmp as $key => $value) {
			$list_Area[$rs->fields['zZip']]['sales'][$value] = getSalesName($value);

		}
		unset($tmp);

		$tmp = @explode(',', $rs->fields['zSalesTwhg']);
		foreach ($tmp as $key => $value) {
			$list_Area[$rs->fields['zZip']]['salesTw'][$value] = getSalesName($value);
		}
		unset($tmp);
		
		
		$rs->MoveNext();
	}
}
// unset($menu_sales);
$menu_sales2 = array();

$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep = '7' AND pJob = 1";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menu_sales2[$rs->fields['pId']] = $rs->fields['pName'];


	$rs->MoveNext();
}
##
#業務分組
$sql = "SELECT * FROM tSalesGroup WHERE sManager != ''";
$rs = $conn->Execute($sql);
$i = 0;
// header("Content-Type:text/html; charset=utf-8"); 
while (!$rs->EOF) {
	$groupList[$i] = $rs->fields;

	$groupList[$i]['sManager'] = $menu_sales2[$rs->fields['sManager']];
	$groupList[$i]['sSalesReport'] = ($rs->fields['sSalesReport'] == 1)?'是':'否';
	$groupList[$i]['sCalendarNotify'] = ($rs->fields['sCalendarNotify'] == 1)?'是':'否';


	$exp = array();
	$member = array();

	$exp = explode(',', $groupList[$i]['sMember']);
	// echo $groupList[$i]['sMemeber']."<br>";
	// print_r($exp);
	foreach ($exp as $k => $v) {
		$member[] = $menu_sales2[$v];
	}
	$groupList[$i]['sMember'] = implode(',', $member);
	
	// print_r($member);
	// 
	// echo "<prE>";
	// print_r($groupList[$i]);

	$i++;
	$rs->MoveNext();
}


##
function getSalesName($pId){
	global $conn;

	$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$pId."'";
	$rs = $conn->Execute($sql);


	return $rs->fields['pName'];
}
##
$smarty->assign('menu_city',$menu_city);
$smarty->assign('menu_sales',$menu_sales);
$smarty->assign('menu_sales2',$menu_sales2);
$smarty->assign('areaSales',$areaSales);
$smarty->assign('menu_Store',$menu_Store);
$smarty->assign('menu_City',$menu_City);
$smarty->assign('list_Area',$list_Area);
$smarty->assign('default_city',$_POST['default_city']);
$smarty->assign('groupList',$groupList);
$smarty->display('salesBranchArea.inc.tpl', '', 'sales');
?>