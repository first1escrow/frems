<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$from_date     = empty($_POST['fromDate']) ? null : $_POST['fromDate'];
$to_date       = empty($_POST['toDate']) ? null : $_POST['toDate'];
$export_status = empty($_POST['exportStatus']) ? null : $_POST['exportStatus'];
$scriveners    = empty($_POST['scrivener']) ? null : $_POST['scrivener'];

require_once dirname(__DIR__) . '/includes/accounting/caseFeedbackPayByCaseList.php';

$cIds = array_column($list, 'fCertifiedId');
$fTargetIds = array_column($list, 'fTargetId');

$smarty->assign('list', $list);
$smarty->assign('cIds', implode('_', $cIds));
$smarty->assign('fTargetIds', implode('_', $fTargetIds));
$smarty->display('caseFeedbackPayByCaseResult.inc.tpl', '', 'accounting');
