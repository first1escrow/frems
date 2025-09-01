<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once 'writelog.php';

 $_POST = escapeStr($_POST) ;

 $sql = "UPDATE tChecklist SET cInterestHidden = 1 WHERE cCertifiedId = '".$_POST['cId']."'";
 // echo $sql;
 $rs = $conn->Execute($sql);

if ($conn->Execute($sql)) {
	echo "成功";
}else{
	echo "失敗";
}
?>