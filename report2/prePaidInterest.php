<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

if (preg_match("/^\d{4}[\-|\/]{1}\d{2}[\-|\/]{1}\d{2}$/", $_POST['fromDate']) && preg_match("/^\d{4}[\-|\/]{1}\d{2}[\-|\/]{1}\d{2}$/", $_POST['toDate'])) {
    require_once dirname(__DIR__) . '/includes/report2/prePaidInterest.php';
    exit;
}

$fromDate = preg_match("/^\d{4}[\-|\/]{1}\d{2}[\-|\/]{1}\d{2}$/", $_POST['fromDate']) ? $_POST['fromDate'] : date("Y-m-d", strtotime('-1day'));
$toDate   = preg_match("/^\d{4}[\-|\/]{1}\d{2}[\-|\/]{1}\d{2}$/", $_POST['toDate']) ? $_POST['toDate'] : date("Y-m-d", strtotime('-1day'));

$smarty->assign('fromDate', $fromDate);
$smarty->assign('toDate', $toDate);

$smarty->display('prePaidInterest.inc.tpl', '', 'report2');
