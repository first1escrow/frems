<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$cat = empty($_POST["cat"]) 
        ? $_GET["cat"]
        : $_POST["cat"];

if ($_POST['ok'] == 1) {
	// $sql = "UPDATE tNote SET nContent ='".$_POST['Note']."',nModifyId = '".$_SESSION['member_id']."' WHERE nCategory = '".$cat."'";
	$sql = "INSERT INTO tNote (nCategory,nContent,nModifyId) VALUES ('".$cat."','".$_POST['Note']."','".$_SESSION['member_id']."')";

	$conn->Execute($sql);
}

if ($_POST['delid']) {
	$sql = "UPDATE tNote SET nStatus ='1',nModifyId = '".$_SESSION['member_id']."' WHERE nId = '".$_POST['delid']."'";
	
	$conn->Execute($sql);
}

$sql = "SELECT * FROM tNote WHERE nCategory ='".$cat."' AND nStatus=0 ORDER BY nId DESC";

$rs = $conn->Execute($sql);
// $data = $rs->fields;
while (!$rs->EOF) {
	$data[] = $rs->fields;

	$rs->MoveNext();
}

##
$smarty->assign('cat',$cat);
$smarty->assign('data',$data);
$smarty->display('salesNote.inc.tpl', '', 'sales');
?>