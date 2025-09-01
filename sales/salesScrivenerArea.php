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
			sId,
			sName,
			sOffice,
			CONCAT('SC',LPAD(sId,4,'0')) as Code
		FROM
			tScrivener WHERE sStatus = 1 ORDER BY sId ASC";

$rs = $conn->Execute($sql);
$menu_Store[0] = '';
while (!$rs->EOF) {
	$menu_Store[$rs->fields['sId']] = $rs->fields['Code'].$rs->fields['sName'];

	if ($rs->fields['sOffice'] != '') {
		$menu_Store[$rs->fields['sId']] .= "(".$rs->fields['sOffice'].")";
	}
	$rs->MoveNext();
}
##
$areaSales = array() ;
$menu_City = array();
$menu_City[0] = '請選擇';
$sql = 'SELECT zCity as city, zScrivenerSales FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;' ;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menu_City[$rs->fields['city']] = $rs->fields['city'];

	$tmp = @explode(',', $rs->fields['zScrivenerSales']);
		
	foreach ($tmp as $key => $value) {
		$areaSales[$rs->fields['city']]['zScrivenerSales'][$value] = getSalesName($value);
	}

	unset($tmp);

	


	$rs->MoveNext();
}

if ($_POST['default_city']) {
	$list_Area = array();
	$sql = "SELECT zZip,zArea,zScrivenerSales FROM tZipArea WHERE zCity = '".$_POST['default_city']."'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$list_Area[$rs->fields['zZip']]['area'] = $rs->fields['zArea'];

		$tmp = @explode(',', $rs->fields['zScrivenerSales']);

		foreach ($tmp as $key => $value) {
			$list_Area[$rs->fields['zZip']]['sales'][$value] = getSalesName($value);

		}
		unset($tmp);

		
		$rs->MoveNext();
	}
}

$menu_sales2 = array();

$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep = '7' AND pJob = 1";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menu_sales2[$rs->fields['pId']] = $rs->fields['pName'];


	$rs->MoveNext();
}
##
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
	
// 	$list[$rs->fields['zCity']][$rs->fields['zArea']] = getScrivener($rs->fields['zZip'],'');
	
// 	$rs->MoveNext();
// }

//
// $sql = 'SELECT zCity as city, zScrivenerSales as salesId FROM tZipArea GROUP BY zCity ORDER BY zZip ASC;' ;
// $areaSales = array() ;
// $rs = $conn->Execute($sql) ;

// while (!$rs->EOF) {
// 	$areaSales[$rs->fields['city']][] = $rs->field ;
// 	$va = array() ;
	
// 	$salesArea = '' ;
// 	foreach ($menu_sales as $k => $v) {
// 		$salesArea .= '<option value="'.$k.'"' ;
// 		if ($rs->fields['salesId'] == $k) $salesArea .= ' selected="seledted"' ;
// 		$salesArea .= '>'.$v."</option>\n" ;
// 	}
// 	$areaSales[$rs->fields['city']]['menu'] = $salesArea ;
	
// 	$rs->MoveNext() ;
// }
//print_r($areaSales) ; exit ;
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
$smarty->assign('areaSales',$areaSales);
$smarty->assign('menu_Store',$menu_Store);
$smarty->assign('menu_City',$menu_City);
$smarty->assign('list_Area',$list_Area);
$smarty->assign('default_city',$_POST['default_city']);
$smarty->assign('menu_sales2',$menu_sales2);
$smarty->display('salesScrivenerArea.inc.tpl', '', 'sales');
?>