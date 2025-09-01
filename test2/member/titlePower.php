<?php
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;

$_POST = escapeStr($_POST) ;
$id = $_POST['id'];
$sql = "SELECT * FROM tPowerList WHERE pId ='".$id."'";
$rs = $conn->Execute($sql);
$json = $rs->fields['pFunction'];
echo $json;
?>