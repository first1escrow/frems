<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$id = ($_POST['id'])?$_POST['id']:$_GET['id'];
$cat = ($_POST['cat'])?$_POST['cat']:$_GET['cat'];

if ($_POST) {
	if ($cat == 'del') {
		$sql = "UPDATE
				tLegalCaseDetail
			SET
				lDel = 1
			WHERE
				lId ='".$id."'";

		$conn->Execute($sql);
	}
	

}
?>