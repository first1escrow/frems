<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$cid = trim($_POST['cId']);


$sql =  "UPDATE tContractInvoiceQuery SET cObsolete ='Y',cQuery='N' WHERE cId = '".$cid."'";

// echo $sql;
$conn->Execute($sql);

echo 1;
?>