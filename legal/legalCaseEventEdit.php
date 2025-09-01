<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$id = ($_POST['id'])?$_POST['id']:$_GET['id'];
$cat = ($_POST['cat'])?$_POST['cat']:$_GET['cat'];

if ($_POST) {
	if ($cat == 'add') {
		$sql = "INSERT INTO
					tLegalEvent
				SET
					lNote = '".$_POST['note']."',
					lDays = '".$_POST['day']."',
					lCreator = '".$_SESSION['member_id']."',
					lEditor = '".$_SESSION['member_id']."',
					lCreatTime = '".date('Y-m-d')."'
					";
		$conn->Execute($sql);

		$id = $conn->Insert_ID();
		// location.href = 'legalCaseEventEdit.php?cat=edit&id='.$id;

		header("Location:legalCaseEventList.php");

	}elseif ($cat == 'edit') {
		$sql = "UPDATE
					tLegalEvent
				SET
					lNote = '".$_POST['note']."',
					lDays = '".$_POST['day']."',
					lEditor = '".$_SESSION['member_id']."'
				WHERE
					lId = '".$id."'
				";	
		// echo $sql;
		$conn->Execute($sql);
		header("Location:legalCaseEventList.php");
	}
}




$sql = "SELECT * FROM tLegalEvent WHERE lId = '".$id."'";
// echo $sql;
$rs = $conn->Execute($sql);
$data = $rs->fields;
##
$menu_day = array();
for ($i=1; $i <=10 ; $i++) { 
	$menu_day[$i] = $i;
}
##
$smarty->assign('menu_day',$menu_day);
$smarty->assign('data',$data);
$smarty->assign('id',$id);
$smarty->assign('cat',$cat);
$smarty->display('legalCaseEventEdit.inc.tpl', '', 'legal');
?>