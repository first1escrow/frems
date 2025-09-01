<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
// require_once dirname(__DIR__).'/opendb.php' ;
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$_POST = escapeStr($_POST);

$xls = $_POST['xls'];

$str = " flag = 'Y'";
if ($_POST['brand']) {
    $brand = array();
    foreach ($_POST['brand'] as $k => $v) {
        array_push($brand, $v);
    }

    $str .= empty($str) ? '' : " AND ";
    $str .= " brand_id IN (" . @implode(',', $brand) . ")";

    $brand = null;unset($brand);
}

if ($_POST['branch']) {
    $branch = array();
    foreach ($_POST['branch'] as $k => $v) {
        array_push($branch, $v);
    }

    $str .= empty($str) ? '' : " AND ";
    $str .= " id IN (" . @implode(',', $branch) . ")";

    $branch = null;unset($branch);
}

$sql = "SELECT sname,(SELECT name FROM brand_count_main WHERE id=brand_id) AS brandName,scompany,city,area,addr,tel FROM brand_count WHERE " . $str . " ORDER BY brand_id ASC";
$rs  = $conn->Execute($sql);

$list = array();
while (!$rs->EOF) {
    $rs->fields['addr'] = str_replace($rs->fields['city'] . $rs->fields['area'], '', $rs->fields['addr']);
    array_push($list, $rs->fields);

    $rs->MoveNext();
}

if ($xls == 1) {
    require_once __DIR__ . '/brandReport_excel.php';
    exit;
}

$smarty->assign('list', $list);
$smarty->display('brandReport_result.inc.tpl', '', 'report');