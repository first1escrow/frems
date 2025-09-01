<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;

$id = $_POST['id'] ;

if (preg_match("/^\d+$/", $id)) {
	$sql = 'SELECT * FROM tAppInform WHERE id = "'.$id.'";' ;
	$rs = $conn->Execute($sql) ;
	if (!$rs->EOF) echo base64_decode($rs->fields['aContent']) ;
}
?>