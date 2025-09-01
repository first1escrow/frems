<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$brand = new Brand();
$sql   = "SELECT
			bId,
			(SELECT bName FROM tBrand AS b WHERE b.bId=b.bBrand) as brand,
			CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode,
			bStore
		FROM tBranch AS b WHERE bStatus = 1";
$rs            = $conn->Execute($sql);
$menuBranch[0] = '請選擇';
while (!$rs->EOF) {
    $menuBranch[$rs->fields['bId']] = $rs->fields['bCode'] . $rs->fields['brand'] . $rs->fields['bStore'];

    $rs->MoveNext();
}
$smarty->assign('menuBranch', $menuBranch);
$smarty->display('formbranchgroup.inc.tpl', '', 'maintain');
