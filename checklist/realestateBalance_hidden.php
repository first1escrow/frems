<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once 'writelog.php';

 $_POST = escapeStr($_POST) ;


 if ($_POST['cat'] == 'c') {
 	$col =  'cRealestateBalanceHide';
 }else{
 	$col =  'bRealestateBalanceHide';
 }

 $sql = "SELECT ".$col." FROM tChecklist WHERE cCertifiedId = '".$_POST['cId']."'";
 $rs = $conn->Execute($sql);

 if (!$rs->EOF) {
 	$val = ($rs->fields[$col] == 1)?0:1;

 	 $sql = "UPDATE tChecklist SET ".$col." = ".$val." WHERE cCertifiedId = '".$_POST['cId']."'";
	 // echo $sql;
	
	if ($conn->Execute($sql)) {
		echo "成功";
	}else{
		echo "失敗";
	}
}


?>