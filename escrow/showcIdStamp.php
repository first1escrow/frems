<?php
//從資料庫取得圖片
require_once dirname(__DIR__).'/first1DB.php';

$bId = $_REQUEST['bId'];
$cId = $_REQUEST['cId'];

if (!empty($bId) && preg_match("/^\d+$/", $bId)) {
	$conn = new first1DB();

	$sql = 'SELECT `bStamp` FROM `tBranchStamp` WHERE `bBranchId` = :bId;';
	$rs = $conn->one($sql, ['bId' => $bId]);

	//顯示圖片
	if(!empty($rs)){
		header("Content-type: image/jpeg"); 
		echo base64_decode($rs['bStamp']);
	}
}
?>