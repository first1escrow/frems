<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
##
// 年度季別
$_POST = escapeStr($_POST) ;

$sql = "UPDATE tStoreFeedBackMoneyFrom SET sCaseCloseTime = '0000-00-00',sStatus = 2 WHERE sId = '".$_POST['id']."'";


if ($conn->Execute($sql)) {
	echo 1;
}else{
	echo 0;
}

?>