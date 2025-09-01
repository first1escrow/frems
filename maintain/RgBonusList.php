<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_GET  = escapeStr($_GET);
$_POST = escapeStr($_POST);
$id    = (!empty($_GET['id'])) ? $_GET['id'] : $_POST['acc'];

if ($_POST['date']) {
    $str = ' AND rTime >= "' . $_POST['date'] . ' 00:00:00" AND rTime <= "' . $_POST['date'] . ' 23:59:59"';
}

$sql = "SELECT *,(SELECT pName FROM tPeopleInfo WHERE pId = rName ) AS Name FROM tRgBonus WHERE rAccount = '" . $id . "' " . $str . " ORDER bY rTime DESC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $list[] = $rs->fields;

    $rs->MoveNext();
}
##
$smarty->assign('list', $list);
$smarty->assign('acc', $id);
$smarty->display('RgBonusList.inc.tpl', '', 'maintain');
