<?php
//從資料庫取得圖片
include_once '../openadodb.php' ;
$id = $_REQUEST['id'] ;

// if (!empty($bId)) {
// 	$sql = 'SELECT * FROM tBranchStamp WHERE 1 AND bBranchId = "'.$bId.'" ORDER BY bId DESC LIMIT 1;' ;
// 	$rs = $conn->Execute($sql) ;      

// 	//顯示圖片
// 	if(!$rs->EOF){
// 		header("Content-type: image/jpeg") ; 
// 		echo base64_decode($rs->fields['bStamp']) ;
// 	}
// }


$sql = "SELECT lPic FROM tLineMoji WHERE lCode = '".$id."'";

$rs = $conn->Execute($sql);

$total = $rs->RecordCount();

if ($total > 0) {
	header("Content-type: image/jpeg") ; 
	echo base64_decode($rs->fields['lPic']) ;
}
?>