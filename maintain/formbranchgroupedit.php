<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$brand = new Brand();

$group = $brand->getGroup($_POST["id"]);

$sql = "
	SELECT
		bId,
		(SELECT bName FROM tBrand AS b WHERE b.bId=b.bBrand) as brand,
		CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode,
		bStore,
		bName,
		bStatus
	FROM
		tBranch AS b
	WHERE
		bGroup='" . $_POST["id"] . "' OR bGroup2='" . $_POST["id"] . "'
		";

$rs = $conn->Execute($sql);

// $data['bBranch'] = ($data['bBranch'] == '')?0:$data['bBranch'] ;
while (!$rs->EOF) {

    $list[$i] = $rs->fields;

    if ($list[$i]['bStatus'] == 2) {
        $list[$i]['close'] = "#CCC";

        # code...
    }

    $i++;

    $rs->MoveNext();
}

$group['bSignDate'] = ($group['bSignDate'] == '0000-00-00') ? '' : (substr($group['bSignDate'], 0, 4) - 1911) . substr($group['bSignDate'], 4);

$sql = "SELECT
			bId,
			(SELECT bName FROM tBrand AS b WHERE b.bId=b.bBrand) as brand,
			CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode,
			bStore,
			bGroup,
			bGroup2
		FROM tBranch AS b WHERE bStatus = 1";
$rs             = $conn->Execute($sql);
$menuBranch     = array();
$menuBranch2    = array();
$menuBranch[0]  = '請選擇';
$menuBranch2[0] = '請選擇';
while (!$rs->EOF) {
    $menuBranch[$rs->fields['bId']] = $rs->fields['bCode'] . $rs->fields['brand'] . $rs->fields['bStore'];

    if ($rs->fields['bGroup'] == $_POST["id"] || $rs->fields['bGroup2'] == $_POST['id']) {
        $menuBranch2[$rs->fields['bId']] = $menuBranch[$rs->fields['bId']];
    }

    $rs->MoveNext();
}

$smarty->assign('menuBranch', $menuBranch);
$smarty->assign('menuBranch2', $menuBranch2);
$smarty->assign('list', $list);
$smarty->assign('is_edit', 1);
$smarty->assign('data', $group);
$smarty->display('formbranchgroup.inc.tpl', '', 'maintain');
