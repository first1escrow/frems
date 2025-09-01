<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/scrivener.class.php';
include_once '../../session_check.php' ;

$scrivener = new Scrivener();
$scrivener->SaveScrivener($_POST);

echo "儲存完成";
?>
