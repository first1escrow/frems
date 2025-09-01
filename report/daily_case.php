<?php
include_once dirname(__DIR__) . '/configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/openadodb.php' ;
include_once dirname(__DIR__) . '/session_check.php' ;


$smarty->display('daily_case.inc.tpl', '', 'report') ;