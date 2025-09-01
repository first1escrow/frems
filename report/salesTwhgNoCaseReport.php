<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/util.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

use First1\V1\Util\Util;

$conn = new first1DB;

$_POST['excel'] = empty($_POST['excel']) ? '' : $_POST['excel'];
if ($_POST['excel'] == 'OK') {
    require_once __DIR__ . '/salesTwhgNoCaseReportExcel.php';
    exit;
}

$sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pJob = 1 AND pDep = 7 ORDER BY pId ASC;';
$rs  = $conn->all($sql);

$menu_sales = [0 => '請選擇'];
foreach ($rs as $v) {
    $menu_sales[$v['pId']] = $v['pName'];
}

$menu_year = [];
for ($i = 2023; $i <= date("Y"); $i++) {
    $menu_year[$i] = ($i - 1911) . '年度';
}

$menu_season = [
    '1' => '第一季',
    '2' => '第二季',
    '3' => '第三季',
    '4' => '第四季',
];

$year   = date('Y');
$season = Util::monthToSeason(date('m'));
$sales  = 0;

// $year   = empty($year) ? date('Y') : $year;
// $season = empty($season) ? Util::monthToSeason(date('m')) : $season;
// $sales  = empty($sales) ? 0 : $sales;

$smarty->assign('year', $year);
$smarty->assign('season', $season);
$smarty->assign('sales', $sales);

$smarty->assign('menu_year', $menu_year);
$smarty->assign('menu_season', $menu_season);
$smarty->assign('menu_sales', $menu_sales);

$smarty->display('salesTwhgNoCaseReport.inc.tpl', '', 'report');
