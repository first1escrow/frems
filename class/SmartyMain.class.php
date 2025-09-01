<?php
require_once dirname(__DIR__) . '/libs/smarty/libs/Smarty.class.php';

$smarty = new Smarty\Smarty();

$smarty->setLeftDelimiter('<{');
$smarty->setRightDelimiter('}>');
$smarty->setCompileDir($GLOBALS['FILE_PATH'] . 'templates_c/');
$smarty->setCacheDir($GLOBALS['FILE_PATH'] . 'cache/');
$smarty->setTemplateDir([
    ''                => $GLOBALS['FILE_PATH'] . 'templates/',
    'escrow'          => $GLOBALS['FILE_PATH'] . 'templates/escrow/',
    'member'          => $GLOBALS['FILE_PATH'] . 'templates/member/',
    'scrivener'       => $GLOBALS['FILE_PATH'] . 'templates/scrivener/',
    'income'          => $GLOBALS['FILE_PATH'] . 'templates/income/',
    'flow'            => $GLOBALS['FILE_PATH'] . 'templates/flow/',
    'inquire'         => $GLOBALS['FILE_PATH'] . 'templates/inquire/',
    'maintain'        => $GLOBALS['FILE_PATH'] . 'templates/maintain/',
    'accounting'      => $GLOBALS['FILE_PATH'] . 'templates/accounting/',
    'report'          => $GLOBALS['FILE_PATH'] . 'templates/report/',
    'report2'         => $GLOBALS['FILE_PATH'] . 'templates/report2/',
    'smstxt'          => $GLOBALS['FILE_PATH'] . 'templates/smstxt/',
    'charts'          => $GLOBALS['FILE_PATH'] . 'templates/charts/',
    'others'          => $GLOBALS['FILE_PATH'] . 'templates/others/',
    'www'             => $GLOBALS['FILE_PATH'] . 'templates/www/',
    'actives'         => $GLOBALS['FILE_PATH'] . 'templates/actives/',
    'calendar'        => $GLOBALS['FILE_PATH'] . 'templates/calendar/',
    'sales'           => $GLOBALS['FILE_PATH'] . 'templates/sales/',
    'banktrans'       => $GLOBALS['FILE_PATH'] . 'templates/banktrans/',
    'bank'            => $GLOBALS['FILE_PATH'] . 'templates/bank/',
    'mobile'          => $GLOBALS['FILE_PATH'] . 'templates/mobile/',
    'line'            => $GLOBALS['FILE_PATH'] . 'templates/line/',
    'payment'         => $GLOBALS['FILE_PATH'] . 'templates/payment/',
    'contract'        => $GLOBALS['FILE_PATH'] . 'templates/contract/',
    'undertaker'      => $GLOBALS['FILE_PATH'] . 'templates/undertaker/',
    'legal'           => $GLOBALS['FILE_PATH'] . 'templates/legal/',
    'staff'           => $GLOBALS['FILE_PATH'] . 'templates/staff/',
    'notify'          => $GLOBALS['FILE_PATH'] . 'templates/notify/',
    'HR'              => $GLOBALS['FILE_PATH'] . 'templates/HR/',
    'includes/escrow' => $GLOBALS['FILE_PATH'] . 'templates/includes/escrow/',
]);

$smarty->caching = false;
$smarty->setCacheLifetime(1);
