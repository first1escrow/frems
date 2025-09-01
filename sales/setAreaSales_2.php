<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once '../includes/first1Sales.php';
include_once '../includes/sales/getSalesArea.php';
include_once '../session_check.php' ;

$_GET = escapeStr($_GET) ;
$_POST = escapeStr($_POST) ;
$city = (!empty($_GET['city'])) ? $_GET['city']:$_POST['city'];
$sales = $_POST['sales'];
$sales_type = $_POST['sales_type'];
$zip = $_POST['zip'];
// $city = $_POST['city'];

// print_r($_POST);

if (is_array($sales_type)) {
	$col = Array();

	foreach ($sales_type as $k => $v) {
		if ($v == 1) {
			$col[]= "zSalesTwhg = '".@implode(',', $sales)."'";
		}else if($v == 2){
			$col[]= "zSales = '".@implode(',', $sales)."'";
		}elseif ($v == 3) {
			$col[]= "zScrivenerSales = '".@implode(',', $sales)."'";
		}
	}
}

$ok = 0;

if (is_array($zip)) {
	foreach ($zip as $k => $v) {
		
		$sql = "UPDATE tZipArea SET ".@implode(',', $col)."  WHERE zZip = '".$v."'";
		// echo $sql."\r\n";

		if ($conn->Execute($sql)) {
			$ok++;
		}
		
		
	}
}

if ($ok == count($zip)) {
	

	echo '成功';
}else{
	echo '失敗';
}
// die;

// $sql = "SELECT * FROM tZipArea WHERE zZip = ".@implode(',', $city)."";
// $rs = $conn->Execute($sql);

// while (!$rs->) {
// 	# code...
// }

?>