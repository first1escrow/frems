<?php
include_once '../openadodb.php' ;

$_POST = escapeStr($_POST) ;

$sql = "SELECT * FROM tBank WHERE bBank3 = '".$_POST['bank']."' AND bBank4 = '".$_POST['branch']."'";

$rs = $conn->Execute($sql);

echo $rs->fields['bBank_area']."-".$rs->fields['bBank_tel'];
?>