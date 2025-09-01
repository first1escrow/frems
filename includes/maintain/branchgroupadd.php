<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/brand.class.php';
include_once '../../session_check.php' ;

$contract = new Brand();
$contract->AddGroup($_POST);

echo "儲存完成";
?>
