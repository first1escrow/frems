<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';

$_GET     = escapeStr($_GET);
$menuYear = array();
$year     = ($_GET['year']) ? $_GET['year'] : date('Y');
for ($i = 2016; $i <= date('Y') + 1; $i++) {
    $menuYear[$i] = ($i - 1911);
}

$smarty->assign('menuYear', $menuYear);
$smarty->assign("year", $year);
$smarty->display('IBookManagerList.inc.tpl', '', 'bank');
