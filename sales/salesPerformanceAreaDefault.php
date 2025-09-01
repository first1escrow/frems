<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$target = $_GET['target'];
if (!preg_match("/^[S|R]$/", $target)) {
    http_response_code(400);
    exit;
}

$conn = new first1DB;

//取得縣市
$sql  = 'SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY zCity';
$city = array_column($conn->all($sql), 'zCity');

//取得在職業務
$sql   = 'SELECT pId, pName FROM tPeopleInfo WHERE pDep = 7 AND pJob = 1;';
$sales = $conn->all($sql);

$smarty->assign('target', $target);
$smarty->assign('city', $city);
$smarty->assign('sales', $sales);

$smarty->display('salesPerformanceAreaDefault.inc.tpl', '', 'sales');
