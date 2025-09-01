<?php
include_once('../openadodb.php') ;
include_once('../genPWD.php') ;
include_once '../session_check.php' ;

//更新重置密碼
$pwd = genPwd(12) ;

$id = $_REQUEST['id'] ;
$sql = '
	UPDATE
		tPeopleInfo
	SET
		pPassword="'.$pwd.'"
	WHERE
		pId="'.$id.'"
' ;
$conn->Execute($sql) ;
##

echo $pwd ;
?>