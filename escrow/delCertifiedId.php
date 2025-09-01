<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
$cId = addslashes($_POST['cId']) ;
if (preg_match("/^[0-9]{14}$/",$cId)) {
	$sql = 'UPDATE tBankCode SET bDel="y",bEditPerson = "'.$_SESSION['member_id'].'",bEditDate="'.date('Y-m-d H:i:s').'" WHERE bAccount="'.$cId.'";' ;
	if ($conn->Execute($sql)) echo 'T' ;
	else echo 'F' ;
}
else echo 'F' ;
?>