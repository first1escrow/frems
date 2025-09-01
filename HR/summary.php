<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/includes/staffHRBeginDate.php';

$dep = $_SESSION['member_pDep'];
if (empty($dep) || ! is_numeric($dep)) {
    throw new Exception('department data abnormal');
}

if (! empty($_POST['download'])) {
    if (! empty($_POST['report']) && ($_POST['report'] == '1') && (! empty($_POST['from']) && preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $_POST['from'])) && (! empty($_POST['to']) && preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $_POST['to']))) {
        require_once __DIR__ . '/summaryExcelStaff.php';
    }

    if (! empty($_POST['report']) && ($_POST['report'] == '2') && (! empty($_POST['date']) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $_POST['date']))) {
        require_once __DIR__ . '/summaryExcelDate.php';
    }

    if (! empty($_POST['report']) && ($_POST['report'] == '3') && (! empty($_POST['applyCheckFrom']) && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['applyCheckFrom'])) && (! empty($_POST['applyCheckTo']) && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['applyCheckTo']))) {
        require_once __DIR__ . '/summaryExcelCheckIn.php';
    }

    if (! empty($_POST['report']) && ($_POST['report'] == '4')) {
        require_once __DIR__ . '/summaryExcelStaffLeave.php';
    }

    $_POST = null;unset($_POST);
    exit;
}

$conn = new first1DB;

$staffSelected = 0;

$sql = 'SELECT pId, pName, pJob FROM tPeopleInfo WHERE pId NOT IN (2, 6, 7, 8, 16, 66) AND pDep = :dept;';
if (($dep == 9) || in_array($_SESSION['member_id'], [2, 3, 13, 129])) {
    $sql = 'SELECT pId, pName, pJob FROM tPeopleInfo WHERE pId NOT IN (2, 6, 7, 8, 16, 66);';
}
$staffOptions = $conn->all($sql, ['dept' => $dep]);

//20250328 取得報表鎖定日期
$sql      = 'SELECT sDate FROM tStaffLockDate WHERE 1 ORDER BY sDate DESC LIMIT 1;';
$lockDate = $conn->one($sql)['sDate'];

$conn = $rs = null;
unset($conn, $rs);

$smarty->assign('from', date('Y-m-01'));
$smarty->assign('to', date('Y-m-t'));

$smarty->assign('staffOptions', $staffOptions);
$smarty->assign('staffSelected', $staffSelected);
$smarty->assign('date', date('Y-m-d'));
$smarty->assign('BEGINDATE', substr(BEGINDATE, 0, 10));
$smarty->assign('lockDate', $lockDate);

$smarty->display('summary.inc.tpl', '', 'HR');
