<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../includes/first1Sales.php';
include_once '../includes/sales/getSalesArea.php';
include_once '../session_check.php' ;

$sales = addslashes(trim($_POST['s']));
$city = addslashes(trim($_POST['c']));
$twhg = addslashes(trim($_POST['t']));

if (preg_match("/^[0-9]+$/",$sales) && !empty($city)) {
	$sql = 'UPDATE tZipArea SET ' ;
	
	if ($twhg == '1') $sql .= 'zSalesTwhg = "'.$sales.'"' ;
	else $sql .= 'zSales = "'.$sales.'"' ;
	
	$sql .= ' WHERE zCity = "'.$city.'" ;' ;	
	
	if ($conn->Execute($sql)) echo 'T' ;
	else echo 'F' ;
}
else {
	echo 'F' ;
}

//echo $city.'_'.$sales ;
?>