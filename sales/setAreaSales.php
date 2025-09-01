<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../includes/first1Sales.php';
include_once '../includes/sales/getSalesArea.php';
include_once '../session_check.php' ;

$_GET = escapeStr($_GET) ;
$_POST = escapeStr($_POST) ;
$city = (!empty($_GET['city'])) ? $_GET['city']:$_POST['city'];

// if ($_POST['cat'] == 's') {
// 	foreach ($_POST as $key => $value) {
// 		// echo $key."<bR>";
// 		if (preg_match("/ScrivenerSales/", $key)) {
// 			$tmp = explode('_', $key);

// 			$sql = "UPDATE tZipArea SET z".$tmp[0]." = '".$value."' WHERE zZip ='".$tmp[1]."'";
// 			$conn->Execute($sql);
// 			// echo $sql."<br>";
// 		}
// 	}

// 	echo "<script>alert('更新成功');</script>";

// }elseif($_POST['cat'] == 'b'){
// 	// echo "<pre>";
// 	// print_r($_POST);
// 	// echo "</pre>";

// 	foreach ($_POST as $key => $value) {
// 		// echo $key."<bR>";
// 		if (preg_match("/Sales/", $key)) {
// 			$tmp = explode('_', $key);
// 			if ($tmp[0] != 'ScrivenerSales') {
// 				$sql = "UPDATE tZipArea SET z".$tmp[0]." = '".$value."' WHERE zZip ='".$tmp[1]."'";
// 				$conn->Execute($sql);
// 			}
			
// 			// echo $sql."<br>";
// 		}
// 	}
// 	echo "<script>alert('更新成功');</script>";
// }

$areaSales = array() ;
$sql = 'SELECT zZip, zArea, zSales, zSalesTwhg,zScrivenerSales FROM tZipArea WHERE zCity = "'.$city.'" ORDER BY zZip ASC;' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	// $list[] = $rs->fields;
	$tmp = @explode(',', $rs->fields['zSales']);
		
	foreach ($tmp as $key => $value) {
		$areaSales[$rs->fields['zArea']]['sales'][$value] = getSalesName($value);
	}

	unset($tmp);

	$tmp = @explode(',', $rs->fields['zSalesTwhg']);
		
	foreach ($tmp as $key => $value) {
		$areaSales[$rs->fields['zArea']]['salesTW'][$value] = getSalesName($value);
	}

	unset($tmp);

	$tmp = @explode(',', $rs->fields['zScrivenerSales']);
		
	foreach ($tmp as $key => $value) {
		$areaSales[$rs->fields['zArea']]['zScrivenerSales'][$value] = getSalesName($value);
	}

	unset($tmp);

	$rs->MoveNext();
}
##
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pJob = 1 AND pDep IN (4,7)";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$menuSales[$rs->fields['pId']] = $rs->fields['pName'];

	$rs->MoveNext();
}


function getSalesName($pId){
	global $conn;

	$sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$pId."'";
	$rs = $conn->Execute($sql);


	return $rs->fields['pName'];
}
// print_r($list);
##
$smarty->assign('cat',$_GET['cat']);
$smarty->assign('city',$_GET['city']);
$smarty->assign('list',$list);
$smarty->assign('menuSales',$menuSales);
$smarty->assign('areaSales',$areaSales);
$smarty->display('setAreaSales.inc.tpl', '', 'sales');
?>