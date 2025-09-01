<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

// $escrow2Url = 'http://10.10.1.199';
// $escrow2Url = 'http://escrow2.local';
// $escrow2Url .= '/backEnd/escrowDocument';

// $smarty->assign('escrow2Url', $escrow2Url);

$smarty->display('webManage.inc.tpl', '', 'www');
