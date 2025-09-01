<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$tt = date("Y-m-d", strtotime("-1 day"));
$mm = (int) substr($tt, 5, 2);
$dd = (int) substr($tt, 8, 2);

$arr = [];
$arr = explode('-', $tt);
$dt  = ($arr[0] - 1911) . '-' . $arr[1] . '-' . $arr[2];
$arr = null;unset($arr);

//一銀利率
$firstRate    = 0;
$firstPercent = '';

$sql = 'SELECT * FROM tBankInterest WHERE tAccount LIKE "60001%" AND tTime = "' . $tt . '" ORDER BY tTime DESC LIMIT 1;';
$rs  = $conn->Execute($sql);

$firstRate = '－';
if (! $rs->EOF) {
    $firstRate = $rs->fields['tRate'];
    $firstRate *= 100;
    $firstPercent = '%';
}

//永豐利率
$sinopacRate    = 0;
$sinopacPercent = '';
$sql            = 'SELECT * FROM tBankInterest WHERE tAccount LIKE "9998%" AND tTime = "' . $tt . '" ORDER BY tTime DESC LIMIT 1;';
$rs             = $conn->Execute($sql);

$sinopacRate = '－';
if (! $rs->EOF) {
    $sinopacRate = $rs->fields['tRate'];
    $sinopacRate *= 100;
    $sinopacPercent = '%';
}

//台新利率
$taishinRate    = 0;
$taishinPercent = '';
$sql            = 'SELECT * FROM tBankInterest WHERE tAccount LIKE "96988%" AND tTime = "' . $tt . '" ORDER BY tTime DESC LIMIT 1;';
$rs             = $conn->Execute($sql);

$taishinRate = '－';
if (! $rs->EOF) {
    $taishinRate = $rs->fields['tRate'];
    $taishinRate *= 100;
    $taishinPercent = '%';
}

//
$smarty->assign('dt', $dt);
$smarty->assign('mm', $mm);
$smarty->assign('dd', $dd);

$smarty->assign('firstRate', $firstRate);
$smarty->assign('firstPercent', $firstPercent);

$smarty->assign('sinopacRate', $sinopacRate);
$smarty->assign('sinopacPercent', $sinopacPercent);

$smarty->assign('taishinRate', $taishinRate);
$smarty->assign('taishinPercent', $taishinPercent);

$smarty->display('interestList.inc.tpl', '', 'inquire');
