<?php
include_once('../openadodb.php') ;
include_once '../session_check.php' ;
include_once '../configs/config.class.php';
include_once 'writelog.php';


$a = $_POST;


if ($a['del'] == 'ok') {
	$sql = '
		DELETE FROM
			tChecklistBank
		WHERE
			cId="'.$a['id'].'"
	' ;
}
if ($sql) {
	$conn->Execute($sql) ;
	checklist_log('指定收受價金之帳戶-刪除(保證號碼:'.$a['cid'].')');
}




##
?>