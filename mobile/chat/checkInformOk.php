<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;
$id = $_POST['id'] ;

if (preg_match("/^\d+$/", $id)) {
	$sql = 'UPDATE tAppInform SET aProcessOK = "Y" WHERE id = "'.$id.'";' ;
	$rs = $conn->Execute($sql) ;
	echo 'ok' ;
}
?>