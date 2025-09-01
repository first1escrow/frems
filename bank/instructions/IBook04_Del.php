<?php
include_once '../../openadodb.php' ;
$_POST = escapeStr($_POST) ;


$id = $_POST['id'];


$sql = "UPDATE tBankTrankBookDetail SET bDel = 1 WHERE bId = '".$id."'";


if ($conn->Execute($sql)) {
	echo 'OK';
}

?>