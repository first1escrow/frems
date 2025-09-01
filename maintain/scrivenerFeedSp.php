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
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查詢地政士特殊回饋');
$brand = new Brand();

// //仲介選單
// $list_brand = $brand->GetBrandList(array(8, 77));

// $menu_brand = $brand->ConvertOption($list_brand, 'bId', 'bName');
// $menu_brand[0] = '請選擇';
// ksort($menu_brand);
###
$sql = "SELECT sSpRecall2 FROM tScrivener WHERE sId ='" . $sId . "'";
$rs  = $conn->Execute($sql);

$list = json_decode($rs->fields['sSpRecall2']);

// while (!$rs->EOF) {
//     # code...
//     $data =
//     $rs->MoveNext();
// }
####

$smarty->assign('sId', $sId);
$smarty->assign('list', $list);
$smarty->assign('menu_brand', $menu_brand);
$smarty->display('scrivenerFeedSp.inc.tpl', '', 'maintain');
