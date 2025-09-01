<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';

if ($_SESSION['member_job'] != '1') {
    header('Location: http://' . $GLOBALS['DOMAIN']);
}

$smarty->display('listbranchgroup.inc.tpl', '', 'maintain');
