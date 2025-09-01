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


// $sql = "
// 		SELECT 
// 			za.zZip,
// 			za.zCity,
// 			za.zArea,
			
// 			za.zSales AS AreaSales
// 		FROM
// 			tZipArea AS za
		
		
// 		ORDER BY zZip ASC";

// $rs = $conn->Execute($sql);

// while (!$rs->EOF) {
	
// 	// $menu_city[$rs->fields['zCity']]=$rs->fields['zCity'];

// 	$tmp[$rs->fields['zCity']] = $rs->fields['zCity'];
	
	

	
// 	$rs->MoveNext();
// }

// $menu_city[0] = '';
// $i = 1;
// foreach ($tmp as $k => $v) {
	
// 	$menu_city[$i] = "<input type=\"button\" name=\"city_".$v."\" value=\"".$v."\" class=\"btnC\" onclick=\"getMenuArea('".$v."')\">";

// 	if ($i != 1 && $i % 5 ==0) {
// 		$menu_city[$i] .= "<br>";
// 	}

// 	$i++;
// }

// unset($tmp);
//
$sql = 'SELECT zCity as city, zSales as salesId, zSalesTwhg FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;' ;
$areaSales = array() ;
$rs = $conn->Execute($sql) ;

while (!$rs->EOF) {
	$areaSales[$rs->fields['city']][] = $rs->field ;
	$va = array() ;
	
	$salesArea = '' ;
	foreach ($menu_sales as $k => $v) {
		$salesArea .= '<option value="'.$k.'"' ;
		if ($rs->fields['salesId'] == $k) $salesArea .= ' selected="seledted"' ;
		$salesArea .= '>'.$v."</option>\n" ;
	}
	$areaSales[$rs->fields['city']]['menu'] = $salesArea ;
	
	$salesArea = '' ;
	foreach ($menu_sales as $k => $v) {
		$salesArea .= '<option value="'.$k.'"' ;
		if ($rs->fields['zSalesTwhg'] == $k) $salesArea .= ' selected="seledted"' ;
		$salesArea .= '>'.$v."</option>\n" ;
	}
	$areaSales[$rs->fields['city']]['menuTwhg'] = $salesArea ;
	
	$rs->MoveNext() ;
}
//print_r($areaSales) ; exit ;
##

##
$smarty->assign('menu_city',$menu_city);
$smarty->assign('menu_sales',$menu_sales);
$smarty->assign('areaSales',$areaSales);
$smarty->assign('menu_Store',$menu_Store);
//$smarty->display('salesBranchArea.inc.tpl', '', 'sales');
$smarty->display('salesArea.inc.tpl', '', 'sales');
?>