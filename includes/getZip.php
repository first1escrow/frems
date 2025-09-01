<?php
include_once '../session_check.php' ;
include_once '../openadodb.php';

header("Content-Type:text/html; charset=utf-8"); 
$_GET = escapeStr($_GET) ;

$city = $_GET['city'];
$area = $_GET['area'];
$type = $_GET['type'];//格式 JSON
// echo $type;

$str = '';
if (!empty($city) ) {
	$str = " zCity = '".$city."'";
}else{
	$data = array();
	$data[0]['id'] = 0;
	$data[0]['text'] = '全部';
	if ($type == 'json') {
	
		echo json_encode($data);
	}
	die;
}

$sql = "SELECT zZip,zArea,zCity FROM tZipArea WHERE  ".$str;


$rs = $conn->Execute($sql);
$data = array();
$data[0]['id'] = 0;
$data[0]['text'] = '全部';
$i = 1;
while (!$rs->EOF) {
	if (!empty($city)) {
		
		$data[$i]['id'] = $rs->fields['zZip'];
		$data[$i]['text'] = $rs->fields['zArea'];
		$i++;
	}
	
	// array_push($area, $rs->fields);

	
	$rs->MoveNext();
}

if ($type == 'json') {
	
	echo json_encode($data);
}
$conn->close();
?> 