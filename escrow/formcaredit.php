<?php
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../openadodb.php';

$id = empty($_POST["id"] ?? '')
? ($_GET["id"] ?? '')
: $_POST["id"];

$cSignCategory = trim($_POST['cSignCategory'] ?? '');

$sql = " SELECT * FROM  `tContractParking` Where `cCertifiedId` = '" . $id . "' ";

$rs   = $conn->Execute($sql);
$data = []; // 初始化陣列

while (! $rs->EOF) {

    $data[] = $rs->fields;

    $rs->MoveNext();
}

// 確保每個 data 項目都有必要的字段
foreach ($data as $key => $item) {
    // 如果 cBelong 不存在，設定預設值
    if (! isset($data[$key]['cBelong'])) {
        $data[$key]['cBelong'] = '';
    }
    // 如果 cOwner 不存在，設定預設值
    if (! isset($data[$key]['cOwner'])) {
        $data[$key]['cOwner'] = '';
    }
    // 如果 cOwnerType 不存在，設定預設值
    if (! isset($data[$key]['cOwnerType'])) {
        $data[$key]['cOwnerType'] = '';
    }
}

if (! isset($data[0]['cId']) || ! $data[0]['cId']) {
    $type = 'add';
} else {
    $type = '';
}

##停車場類別
$category = [
    '1' => '坡道平面式',
    '2' => '昇降平面式',
    '3' => '坡道機械式',
    '4' => '昇降機械式',
];
##
##

##權屬

$owner_type = [
    '1' => '有獨立權狀',
    '2' => '持分併入公共設施',
];

##

##權屬

$ground = [
    '1' => '地上',
    '2' => '地下',
];

##

##權屬

$owner = [

    '3' => '須承租繳租金',
    '4' => '需定期抽籤',
    '5' => '需排隊等候',
    '6' => '其他',
];

##

// 初始化權屬檢查狀態
$owner_check = '';

$smarty->assign('data', $data);
$smarty->assign('type', $type);
$smarty->assign('owner', $owner);
$smarty->assign('Category', $category);
$smarty->assign('Ownertype', $owner_type);
$smarty->assign('Ground', $ground);
$smarty->assign('check', $owner_check);
$smarty->assign('cCertifiedId', $id);
$smarty->assign('cSignCategory', $cSignCategory);
$smarty->display('formcaredit.inc.tpl', '', 'escrow');
