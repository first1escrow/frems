<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$data = array();

$sql = "SELECT pFunction FROM tPowerList WHERE pId = '".$_POST['dep']."' ORDER BY pId ASC";
// echo $sql;

$rs = $conn->Execute($sql);

if (!$rs->EOF) {
	$data['code'] = 200;
	$data['data'] = json_decode($rs->fields['pFunction']);
	# code...
}else{
	$data['code'] = 201;

}

echo json_encode($data);
?>