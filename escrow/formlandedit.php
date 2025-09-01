<?php
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../class/advance.class.php';
include_once '../class/contract.class.php';
include_once '../class/scrivener.class.php';
include_once '../class/member.class.php';
include_once '../session_check.php';
include_once '../tracelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查看合約書建物詳細資料');

$bitem         = trim($_POST['bitem']);
$cSignCategory = trim($_POST['cSignCategory']);

$contract           = new Contract();
$list_categorybuild = $contract->GetCategoryBuild();
$menu_categorybuild = $contract->ConvertOption($list_categorybuild, 'cId', 'cName');

if ($contract->CheckContractProperty($_POST["id"], $bitem)) {
    for ($i = 0; $i < 12; $i++) {
        $contract->AddContractProperty($_POST["id"], $i, $bitem);
    }
}

$data = $contract->GetContractProperty($_POST["id"], $bitem);

$total = count($data);

$menu_category = [0 => '------', 1 => '主建物', 2 => '附屬建物', 3 => '共有部份'];

// $data_case = $contract->GetContract($_POST["id"]);

$smarty->assign('menu_category', $menu_category);
$smarty->assign('menu_categorybuild', $menu_categorybuild);
$smarty->assign('cCertifiedId', $_POST["id"]);
$smarty->assign('bitem', $bitem);                 //第幾個建物
$smarty->assign('cSignCategory', $cSignCategory); //合約書位置
$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->display('formlandedit.inc.tpl', '', 'escrow');
