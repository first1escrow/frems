<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$sql = "SELECT * FROM tLegalEvent WHERE lDel = '0'";
$rs = $conn->Execute($sql);
$list = array();
while (!$rs->EOF) {
	// $list[] = $rs->fields;
	array_push($list,$rs->fields);

	$rs->MoveNext();
}


// print_r($list);
$smarty->assign('list',$list);
$smarty->display('importCaseEvent.inc.tpl', '', 'legal');
?>