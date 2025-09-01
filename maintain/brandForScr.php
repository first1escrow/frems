<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_GET = escapeStr($_GET);
$sId  = $_GET['sId'];

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查詢品牌回饋代書');
$brand = new Brand();

//仲介選單
$list_brand = $brand->GetBrandList(array(8, 77));

$menu_brand    = $brand->ConvertOption($list_brand, 'bId', 'bName');
$menu_brand[0] = '請選擇';
ksort($menu_brand);
###
$sql = "SELECT * FROM tScrivenerFeedSp WHERE sScrivener ='" . $sId . "' AND sDel =0";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    # code...
    $list[] = $rs->fields;
    $rs->MoveNext();
}
####

$smarty->assign('sId', $sId);
$smarty->assign('list', $list);
$smarty->assign('menu_brand', $menu_brand);
$smarty->display('brandForScr.inc.tpl', '', 'maintain');
