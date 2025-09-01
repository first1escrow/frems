<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/brand.class.php';
include_once '../../session_check.php' ;
include_once '../../openadodb.php';

$contract = new Brand();

$_POST = escapeStr($_POST) ;
// print_r($_POST);
switch ($_POST['cat']) {
	case 'checkB':
		$msg = checkBrand($_POST['code']);
		break;
	
	default:
		# code...
		break;
}

echo $msg;

function checkBrand($v){
	global $conn;
	// echo 'GO';
	$sql = "SELECT * FROM tBrand WHERE bCode ='".$v."'";
	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();

	if ($max > 0) {
		return "0";//已被使用過
	}else{
		return "1";//可以使用
	}
}

?>
