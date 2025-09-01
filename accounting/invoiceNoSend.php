<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$id = explode(',', $_POST['id']);

$ck_id = explode(',', $_POST['ck']);



$msg = 0;

//顯示在畫面上的
for ($i=0; $i < count($id); $i++) { 

	

	$sql = "UPDATE tContractInvoiceQuery SET cNoSend = '0' WHERE cId='".$id[$i]."'";

	$conn->Execute($sql);
	
}


//有勾選的
for ($i=0; $i < count($ck_id); $i++) { 
	
	$sql = "UPDATE tContractInvoiceQuery SET cNoSend = '1' WHERE cId='".$ck_id[$i]."'";

	if (!$conn->Execute($sql)) {
		$msg = 1;
	}
	

}
if ($msg == 1) {
	echo '更新失敗';
}else{
	echo '更新成功';
}

die;

?>