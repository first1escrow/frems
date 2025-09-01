<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$list = array();
$mainList = array();

$sql = "SELECT * FROM tPeopleInfoAuthority WHERE pDelete = 0 ORDER BY pLevel,pId ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$rs->fields['pAuthority2'] = ($rs->fields['pAuthority2'])?unserialize($rs->fields['pAuthority2']):array();


	if ($rs->fields['pLevel'] == 0) {
		$list_main[] = $rs->fields;
	}else{
		$list_branch[$rs->fields['pGroup']][] = $rs->fields;
	}
	
	

	$rs->MoveNext();
}
// // echo "<pre>";
// header("Content-Type:text/html; charset=utf-8"); 

// echo "<pre>";
// print_r($list_branch);
##
$smarty->assign("list_main",$list_main);
$smarty->assign("list_branch",$list_branch);
$smarty->display('memberDepAuthority.inc.tpl', '', 'member');
?>
