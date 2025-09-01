<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

if ($_SESSION['pLegalCaseNotify'] != '1') {
    exit('Invalid Access');
}

$conn = new first1DB;

$date = date("Y-m-d");

//取得紀錄
$sql     = 'SELECT a.lId, a.lCertifiecId, a.lItem, (SELECT lItem FROM tLegalItem WHERE lId = a.lItem) as item, a.lDate, a.lRemark, a.lStatus FROM tLegalNotify AS a WHERE a.lDate = :date ORDER BY lStatus DESC, lCertifiecId ASC;';
$records = $conn->all($sql, ['date' => $date]);
##

$smarty->assign('records', $records);

$smarty->display('legalNotifyInfo.inc.tpl', '', 'legal');
