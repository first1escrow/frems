<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once '../../session_check.php' ;

$contract = new Contract();

$contract->AddPropertyItem($_POST);

//print_r($_POST);
//print_r($_GET);

header('Location: ../../escrow/listbuyowner.php');

?>
