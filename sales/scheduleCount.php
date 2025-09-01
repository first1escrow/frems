<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tab = $_REQUEST['tab'];
if (!preg_match("/^[1|2]$/uis", $tab)) {
    $tab = 1;
}

$years = $_REQUEST['years'];
if (!preg_match("/^\d{4}$/uis", $years)) {
    $years = date("Y");
}

$months = $_REQUEST['months'];
if (!preg_match("/^\d{1,2}$/uis", $months)) {
    $months = str_pad(date("m"), 2, '0', STR_PAD_LEFT);
}

$sql = 'SELECT * FROM tPeopleInfo WHERE pDep = "7" AND pJob = "1" ORDER BY pId ASC;';
if (in_array($_SESSION['member_id'], [6, 13])) { //20240312 家津要求可以查看所有業務的行程(包含已離職的人員)
    $sql = 'SELECT * FROM tPeopleInfo WHERE pDep = "7" ORDER BY pId ASC;';
}
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $staff[$rs->fields['pId']] = array('pId' => $rs->fields['pId'], 'pName' => $rs->fields['pName']);
    $rs->MoveNext();
}

require_once __DIR__ . '/' . ($tab == 1) ? 'scheduleYear.php' : 'scheduleMonth.php';

$yr = '';
for ($i = date("Y"); $i > 2011; $i--) {
    $yr .= '<option value="' . $i . '"';
    if ($i == $years) {
        $yr .= ' selected="selected"';
    }

    $yr .= '>' . $i . "</option>\n";
}

$mn .= '<option value="' . str_pad($months, 2, '0', STR_PAD_LEFT) . '"';
$mn .= ' selected="selected"';
$mn .= '>' . str_pad($months, 2, '0', STR_PAD_LEFT) . "</option>\n";

$smarty->assign('tab', $tab);
$smarty->assign('yr', $yr);
$smarty->assign('mn', $mn);
$smarty->assign('tables', $tables);
$smarty->display('scheduleCount.inc.tpl', '', 'sales');