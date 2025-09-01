<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$conn = new first1DB;

$target = $_GET['target'];
$zip    = $_GET['zip'];

if (!in_array($target, ['R', 'S']) || !preg_match("/^\d{3}[a-z]{0,1}$/", $zip)) {
    http_response_code(400);
    exit('<h1>error</h1>');
}

//業務清單
$menu_sales = [0 => ''];
$sql        = "SELECT pId,pName FROM tPeopleInfo WHERE pDep = '7' AND pJob = 1";
$rs         = $conn->all($sql);

foreach ($rs as $v) {
    $menu_sales[$v['pId']] = $v['pName'];
}
##

//顯示各區域業務
$data = [];
$sql  = 'SELECT * FROM tZipArea WHERE zZip = :zip;';
$rs   = $conn->one($sql, ['zip' => $zip]);

$data = [
    'zip'  => $rs['zZip'],
    'city' => $rs['zCity'],
    'area' => $rs['zArea'],
];

if ($target == 'S') {
    $data = array_merge($data, [
        'sales' => $rs['zPerformanceScrivenerSales'],
        'name'  => $menu_sales[$rs['zPerformanceScrivenerSales']],
    ]);
} else {
    $data = array_merge($data, [
        'sales'    => $rs['zPerformanceSales'],
        'name'     => $menu_sales[$rs['zPerformanceSales']],
        'twhg'     => $v['zPerformanceSalesTwhg'],
        'twhgName' => $menu_sales[$rs['zPerformanceSalesTwhg']],
    ]);
}
##

$smarty->assign('target', $target);
$smarty->assign('zip', $zip);
$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('data', $data);

$smarty->display('salesPerformanceAreaDetail.inc.tpl', '', 'sales');
