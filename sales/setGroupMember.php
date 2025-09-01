<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

// print_r($_POST);
// header("Content-Type:text/html; charset=utf-8"); 
$sql = "UPDATE 
			tSalesGroup
		SET
			sManager = '".$_POST['group_leader']."',
			sMember = '".@implode(',', $_POST['group_member'])."',
			sSalesReport = '".$_POST['signStore']."',
			sCalendarNotify = '".$_POST['calendarNotify']."'
		WHERE
			sCity = '".$_POST['group_city']."'";
// echo $sql;

$conn->Execute($sql);

// 
// 
header("location:salesBranchArea.php#tabs-group");
?>