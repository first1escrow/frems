<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$upload_format = $_POST['upload_format'];

switch ($upload_format) {
    case 'check':
        require_once __DIR__ . '/importInvoiceFile.php';
        break;
    case 'identity':
        require_once __DIR__ . '/importInvoiceZip.php';
        break;
    case 'winY':
    case 'winA':
    case 'winX':
    case 'winZ1':
    case 'winZ2':
        require_once __DIR__ . '/importInvoiceWin.php';
        break;
    default:
        break;
}

$smarty->display('import_income.inc.tpl', '', 'accounting');
