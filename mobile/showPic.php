<?php
//從資料庫取得圖片
include_once '../openadodb.php' ;

$_GET = escapeStr($_GET) ;
$id = $_GET['a'];

$sql = 'SELECT aPic FROM tAppNews WHERE aId = "'.$id.'" AND aDel = 0' ;

$rs = $conn->Execute($sql) ;      

//顯示圖片
if(!$rs->EOF){
	
	header("Content-type: image/jpeg") ; 
	echo base64_decode($rs->fields['aPic']) ;
}
?>