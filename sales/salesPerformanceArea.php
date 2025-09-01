<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
// require_once dirname(__DIR__) . '/class/datalist.class.php';
require_once dirname(__DIR__) . '/class/traits/zips.traits.php';

$conn = new first1DB;

//業務清單
$menu_sales = [0 => ''];
$sql        = "SELECT pId,pName FROM tPeopleInfo WHERE pDep = '7' AND pJob = 1";
$rs         = $conn->all($sql);

foreach ($rs as $v) {
    $menu_sales[$v['pId']] = $v['pName'];
}
##

//顯示各區域業務
$scrivener = $realty = [];
$sql       = 'SELECT * FROM tZipArea ORDER BY zZip ASC;';
$rs        = $conn->all($sql);

foreach ($rs as $v) {
    $_tmp = [
        'zip'  => $v['zZip'],
        'city' => $v['zCity'],
        'area' => $v['zArea'],
    ];

    $scrivener[] = array_merge($_tmp, ['sales' => $menu_sales[$v['zPerformanceScrivenerSales']]]);
    $realty[]    = array_merge($_tmp, ['sales' => $menu_sales[$v['zPerformanceSales']], 'twhg' => $menu_sales[$v['zPerformanceSalesTwhg']]]);
}
##

//取得所有地政士與仲介aotocomplete清單
// $stores = [];

// $_datalist = new Datalist;
// $stores    = $_datalist->All();

// $_datalist = null;unset($_datalist);
##

//20230215 取得縣市
$menu_city = Zips::getCity();
##

$smarty->assign('target', $_target);
$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('scrivener', $scrivener);
$smarty->assign('realty', $realty);
// $smarty->assign('stores', $stores);
$smarty->assign('menu_city', $menu_city);

$smarty->display('salesPerformanceArea.inc.tpl', '', 'sales');
