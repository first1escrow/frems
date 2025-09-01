<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../../tracelog.php' ;

$_POST = escapeStr($_POST) ;

$sql = "SELECT * FROM tBankTrankBook WHERE bId ='".$_POST['id']."'";

$rs = $conn->Execute($sql);

if ($rs->fields['bStatus'] == 0 || $rs->fields['bStatus'] =='' || $rs->fields['bStatus'] == 1) { //只能在待確認儲存或新增案件時可以通過
	echo 'OK';
}else { //1:待審核 2:已審核
	echo '狀態已變更，禁止修改';
}


?>