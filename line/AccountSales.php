<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$id = empty($_GET['id']) ? $_POST['id'] : $_GET['id'];

if ($_POST['id']) {

	$sql = "UPDATE tCustomerSales SET cStatus = 1,cEditor = '".$_SESSION['member_id']."' WHERE cId ='".$_POST['id']."'";
	$conn->Execute($sql);
}

if ($_POST['name'] && $_POST['mobile']) {
	$sql = "INSERT INTO tCustomerSales (cName,cMobile,cCreator,cCreatTime) VALUES('".$_POST['name']."','".$_POST['mobile']."','".$_SESSION['member_id']."','".date('Y-m-d H:i:s')."')";
	$conn->Execute($sql);
}

if ($_SESSION['member_cLine'] != 1 ) {
	$str = " AND cCreator = '".$_SESSION['member_id']."'";
}

$sql = "SELECT * FROM tCustomerSales WHERE cStatus = 0 ".$str." ORDER BY cId DESC";
// echo $sql;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$data[] = $rs->fields;


	$rs->MoveNext();
}



$smarty->assign('data',$data);

$smarty->display('AccountSales.inc.tpl', '', 'line');
?>
