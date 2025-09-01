<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$cid = trim($_POST['cid']);

$status = trim($_POST['status']);

$sql = "UPDATE tContractCase SET cInvoiceClose ='".$status."' WHERE cCertifiedId ='".$cid."'";

$rs = $conn->Execute($sql);

echo 'ok';


?>