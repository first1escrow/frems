<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/datalist.class.php';
require_once dirname(__DIR__) . '/session_check.php';

if (session_status() != 2) {
    session_start();
}

//取得所有地政士與仲介aotocomplete清單
$stores = [];

$_datalist = new Datalist;
$stores    = ($_SESSION['member_pDep'] == 7) ? $_datalist->Scrivener($_SESSION['member_id']) : $_datalist->Scrivener();
$_datalist = null;unset($_datalist);

$smarty->assign('stores', $stores);
$smarty->display('caseFeedbackPayByCase.inc.tpl', '', 'accounting');
