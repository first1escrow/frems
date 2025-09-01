<?php
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../maintain/feedBackData.php' ;

$_POST = escapeStr($_POST) ;

$act = $_POST['act'];

if ($act =='st') {
	$data = getStore($_POST['type']);


	foreach ($data as $k => $v) {
		$option .= '<option value="'.$k.'">'.$v.'</option>';
	}

	echo $option;
}elseif ($act == 'del') {
	$sql = "UPDATE  tFeedBackMoneyReviewList SET fDelete ='1' WHERE fId ='".$_POST['id']."'";
	$conn->Execute($sql);
}


?>