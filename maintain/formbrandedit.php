<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/openadodb.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '查看特定仲介品牌明細');

$brand                  = new Brand();
$data                   = $brand->GetBrand($_POST["id"]);
$list_categorybank_twhg = $brand->GetCategoryBank(array(8, 77));
$menu_categorybank_twhg = $brand->ConvertOption($list_categorybank_twhg, 'cId', 'cBankName');
$menu_scrivener         = array('1' => '有');

$data['bBank']      = explode(",", $data['bBank']);
$data['bScrivener'] = explode(",", $data['bScrivener']);

##
$sql                    = "SELECT sName FROM tScrivener WHERE sId ='" . $data['bScrivenerFeed'] . "'"; //bScrivenerFeed
$rs                     = $conn->Execute($sql);
$data['bScrivenerFeed'] = $rs->fields['sName'] . "(比率:" . $data['bScrivenerRecall'] . "%)";
##
$sql = "SELECT
            bId,
            (SELECT bName FROM tBrand AS b WHERE b.bId=b.bBrand) as brand,
            CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode,
            bStore
        FROM
            tBranch AS b
        WHERE
            bBrand = '" . $_POST['id'] . "'";
$rs            = $conn->Execute($sql);
$menuBranch[0] = '請選擇';
while (!$rs->EOF) {
    $menuBranch[$rs->fields['bId']] = $rs->fields['bCode'] . $rs->fields['brand'] . $rs->fields['bStore'];

    $rs->MoveNext();
}
$data['bSignDate'] = ($data['bSignDate'] == '0000-00-00') ? '' : (substr($data['bSignDate'], 0, 4) - 1911) . substr($data['bSignDate'], 4);
##

$smarty->assign('country', listCity($conn, $data['bZip'])); //聯絡地址-縣市
$smarty->assign('area', listArea($conn, $data['bZip'])); //聯絡地址-區域
$smarty->assign('is_edit', 1);
$smarty->assign('data', $data);
$smarty->assign('menu_categorybank_twhg', $menu_categorybank_twhg);
$smarty->assign('menu_scrivener', $menu_scrivener);
$smarty->assign('menuBranch', $menuBranch);
$smarty->display('formbrand.inc.tpl', '', 'maintain');
