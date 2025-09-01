<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../sms/sms_function_manually.php' ;

$sms = new SMS_Gateway();

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$id = ($_POST['id'])? $_POST['id']:$_GET['id'];
$cat = ($_POST['cat'])? $_POST['cat']:$_GET['cat'];

if ($_POST['code'] && $_POST['name']) {
	if ($cat == 'add') {
		$sql = "INSERT INTO
					tGift (gCode,gName,gMoney,gCreator,gCreatTime,gLastEditor,sTop)
				VALUES
					('".$_POST['code']."','".$_POST['name']."','".$_POST['money']."','".$_SESSION['member_id']."','".date('Y-m-d H:i:s')."','".$_SESSION['member_id']."','".$_POST['top']."')";
		$conn->Execute($sql);
		$id = $conn->Insert_ID(); 
		$cat = 'edit';
	}else{
		$sql = "UPDATE tGift SET gCode = '".$_POST['code']."', gName = '".$_POST['name']."',gMoney = '".$_POST['money']."',sTop ='".$_POST['top']."' WHERE gId = '".$id."'";
		$conn->Execute($sql);
	}
	// echo $sql;
}



$sql = "SELECT * FROM tGift WHERE gId = ".$id;

$rs = $conn->Execute($sql);
$data = $rs->fields;

##
$smarty->assign('menu_option',array(0=>'否',1=>'是'));
$smarty->assign('data',$data);
$smarty->assign('cat',$cat);
$smarty->display('PresentEdit.inc.tpl', '', 'scrivener');
?>
