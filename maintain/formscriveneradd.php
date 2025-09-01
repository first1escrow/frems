<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/class/getBank.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/first1Sales.php';
require_once dirname(__DIR__) . '/genPWD.php';

$data              = array('sRecall' => '33.33', 'sStatus' => 1); //改成百分之33
$data['sBrand'][0] = 2;

$data['sPassword'] = genPwd(); //預設密碼

$data['sRg']              = 0;
$data['sBank']            = [5]; //台新建北
$data['sFeedbackMark']    = 0;
$data['sSmsLocationMark'] = 0;
$data['sScrivenerSystem'] = ''; //代書系統預設值
$data['sScrivenerSystemOther'] = ''; //代書系統其他說明預設值

$scrivener = new Scrivener();

$list_ppl   = $scrivener->GetPeopleList();
$menu_ppl   = $scrivener->ConvertOption($list_ppl, 'pId', 'pName');
$list_brand = $scrivener->GetBrandList();

$menu_invoice = $scrivener->GetCategoryInvoice();
$menu_status  = $scrivener->GetCategoryScrivenerStatus();
$menu_bank    = $scrivener->GetBankMenuList();

$menu_categoryrecall   = $scrivener->GetCategoryRecall();
$menu_categoryidentify = $scrivener->GetCategoryIdentify();
$menu_accunused        = array('1' => '是');

$today = DateChange(date('Y-m-d'));
function DateChange($date)
{
    $date = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $date));
    $tmp  = explode('-', $date);

    if (preg_match("/0000/", $tmp[0])) {$tmp[0] = '000';} else { $tmp[0] -= 1911;}

    $date = $tmp[0] . '-' . $tmp[1] . '-' . $tmp[2];
    unset($tmp);
    return $date;
}
##
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE (pDep = 4 OR pDep = 7) AND pJob = 1";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $menu_sales[$rs->fields['pId']] = $rs->fields['pName'];

    $rs->MoveNext();
}
// $menu_brand = array(2 => '非仲介成交',1=> '台灣房屋',49=>'優美地產',69=>'幸福家不動產',75=>'飛鷹地產');
$sql = "SELECT bId,bName FROM tBrand WHERE bContract = 1";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $menu_brand[$rs->fields['bId']] = $rs->fields['bName'];

    $rs->MoveNext();
}
##

//合約銀行
$menu_contractbank = array();
$sql               = "SELECT cBankFullName,cBranchFullName,cId FROM tContractBank WHERE cShow = 1 AND cId != 4"; //永豐用城中
$rs                = $conn->Execute($sql);
while (!$rs->EOF) {
    $menu_contractbank[$rs->fields['cId']] = $rs->fields['cBankFullName'] . $rs->fields['cBranchFullName'];

    $rs->MoveNext();
}
##

//取得地政士選單 20221012
$menu_scriveners = $scrivener->GetListScrivener();
$menu_scriveners = $scrivener->ConvertOption($menu_scriveners, 'sId', 'sOffice', true);

$smarty->assign('menu_scriveners', $menu_scriveners); //地政士選單
##

$smarty->assign('sms_target', '');
$smarty->assign('menu_mark', array('0' => '不標記', '1' => '標記'));
$smarty->assign('menu_contractbank', $menu_contractbank);
$smarty->assign('menu_sbackDoc', array(1 => '身分證', 2 => '存摺', 3 => '變更帳戶'));
$smarty->assign('data_feedData_count', count($data_feedData));
$smarty->assign('today', $today);
$smarty->assign('menu_cstatus', array('1' => '是'));
$smarty->assign('data', $data);
$smarty->assign('sOptions', array(1 => '加盟', 2 => '直營'));
$smarty->assign('menu_choice', array('1' => '是', '0' => '否'));
$smarty->assign('is_edit', 0);
$smarty->assign('menu_bank', $menu_bank);
$smarty->assign('menu_status', $menu_status);
$smarty->assign('menu_invoice', $menu_invoice);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_ppl', $menu_ppl);
$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('menu_note', array('' => '請選擇', 'INV' => 'INV', 'REC' => 'REC'));
$smarty->assign('menu_incomecategory', array('' => '請選擇', '9A-13' => '9A-13', '9A-76' => '9A-76'));
$smarty->assign('data', $data);
$smarty->assign('addScrivener', '1');
$smarty->assign('listCity', listCity($conn)); //聯絡地址-縣市
$smarty->assign('listArea', listArea($conn)); //聯絡地址-區域
$smarty->assign('listCity2', listCity($conn)); //公司地址-縣市
$smarty->assign('listArea2', listArea($conn)); //公司地址-區域
$smarty->assign('menu_accunused', $menu_accunused);
$smarty->assign('menu_categoryidentify', $menu_categoryidentify);
$smarty->assign('menu_categoryrecall', $menu_categoryrecall);
$smarty->assign('FeedCity', listCity($conn)); //回饋金-縣市
$smarty->assign('menu_feedDateCat', array(0 => '季', 1 => '月', 2 => '隨案'));

$smarty->assign('new_scrivener', date("Y-m-d"));

$smarty->display('formscrivener.inc.tpl', '', 'maintain');