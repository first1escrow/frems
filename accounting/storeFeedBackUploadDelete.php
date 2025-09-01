<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$id = $_POST['id'];

// print_r($_POST['id']);
$count = 0;
if (!empty($id) && is_array($id)) {

	foreach ($id as $v) {
		$sql = "UPDATE tStoreFeedBackMoneyFrom_Record SET sDel = 1 WHERE sId ='".$v."' ";
		if ($conn->Execute($sql)) {
			$count++;
		}
	}
	

	if ($count == count($id)) {
		echo "刪除成功";
	}
}else{
	echo "沒有勾選，請勾選";
}
?>