<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/class/member.class.php';

$member = new Member();

$member->Logout();

//header('Location: http://first.twhg.com.tw/');
include_once '../../session_check.php' ;

?>
