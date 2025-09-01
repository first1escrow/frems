<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
// require_once dirname(__DIR__) . '/opendb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$_POST = escapeStr($_POST);

// 店名選單
$sql = 'SELECT id,sname,(SELECT name FROM brand_count_main WHERE id=brand_id) AS brandName FROM brand_count WHERE brand_id != 0 AND flag = "Y" AND sname != "" ORDER BY brand_id ASC';
$rs  = $conn->Execute($sql);

$menu_branch[0] = '';
while (!$rs->EOF) {
    $menu_branch[$rs->fields['id']] = $rs->fields['brandName'] . "-" . $rs->fields['sname'];
    $rs->MoveNext();
}

//品牌
$sql           = "SELECT id,name FROM brand_count_main";
$rs            = $conn->Execute($sql);
$menu_brand    = array();
$menu_brand[0] = '';
while (!$rs->EOF) {
    $menu_brand[$rs->fields['id']] = $rs->fields['name'];
    $rs->MoveNext();
}

//地區
$sql          = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs           = $conn->Execute($sql);
$menu_city[0] = '全部';
while (!$rs->EOF) {
    $menu_city[$rs->fields['zCity']] = $rs->fields['zCity'];
    $rs->MoveNext();
}

//,'branch'=>'店家'
$menu_dateCategory = array(1 => '進案', 2 => '簽約', 3 => '結案');
$menu_tab          = array('sales' => '業務', 'brand' => '品牌', 'storearea' => '店區域(仲介、地政士)', 'brandCategory' => '品牌類別', 'branchGroup' => '仲介群組');
$menu_timeCategory = array('y' => '年', 's' => '季', 'm' => '月');
$menu_Year         = array();
for ($i = 100; $i <= (date('Y') - 1911); $i++) {
    $menu_Year[$i] = $i;
}

##
$smarty->assign('menu_dateCategory', $menu_dateCategory);
$smarty->assign('menu_tab', $menu_tab);
$smarty->assign('menu_timeCategory', $menu_timeCategory);
$smarty->assign('menu_Year', $menu_Year);
$smarty->assign('menu_branch', $menu_branch);
$smarty->assign('menu_scrivener', $menu_scrivener);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_brandCategory', $menu_brandCategory);
$smarty->assign('menu_city', $menu_city);
$smarty->assign('menu_bank', $menu_bank);
##
$smarty->assign('dateCategory', $dateCategory);
$smarty->assign('timeCategory', $timeCategory);
$smarty->assign('startYear', $startYear);
$smarty->assign('endYear', $endYear);
$smarty->assign('tab', $tab);
##

$smarty->display('brandReport.inc.tpl', '', 'report');