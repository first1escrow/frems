<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once '../../session_check.php' ;

$contract = new Contract();
print_r($_POST['land_land1']);
$contract->AddLand2($_POST, 1);
//if ( $contract->checkland($_POST["certifiedid"], 1) ) {
//    $contract->SaveLand($_POST, 1);
//} else {
//    $contract->AddLand2($_POST, 1);
//}

echo "儲存完成";
?>
