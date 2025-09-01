<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/brand.class.php';
require_once dirname(__DIR__) . '/class/sms.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/first1Sales.php';
require_once dirname(__DIR__) . '/genPWD.php';

$brand = new Brand();

if ($_SESSION['member_pDep'] == 7) {
    $address_disabled = 'distable';
}
##
$sms_target = '';
if ($_SESSION['member_pDep'] == 7) {
    $sms_target = 'distable';
}

$menu_categoryidentify     = $brand->GetCategoryIdentify();
$menu_categoryrealestate   = $brand->GetCategoryRealestate();
$menu_categorybranchstatus = $brand->GetCategoryBranchStatus();
$menu_categoryidentify     = $brand->GetCategoryIdentify();
$menu_categoryrecall       = $brand->GetCategoryRecall();
$list_ppl                  = $brand->GetPeopleList();
$menu_ppl                  = $brand->ConvertOption($list_ppl, 'pId', 'pName');
$list_categorybank_twhg    = $brand->GetCategoryBank(array(8, 77));
$menu_categorybank_twhg    = $brand->ConvertOption($list_categorybank_twhg, 'cId', 'cBankName');
$list_brand                = $brand->GetBrandList(array(8, 77));
$menu_brand                = $brand->ConvertOption($list_brand, 'bId', 'bName');
$data_sms                  = $brand->GetSmsBranch($_POST["id"]);
$menu_bank                 = $brand->GetBankMenuList();

##群組
$group = $brand->GetGroupList();

//20220721
$menu_group[0] = '請選擇';
for ($i = 0; $i < count($group); $i++) {
    $menu_group[$group[$i]['bId']] = $group[$i]['bName'];
}

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

##

$menu_emailreceive     = array('1' => '有');
$menu_message          = array('1' => '有');
$menu_cashierorderhas  = array('1' => '有');
$menu_bServiceOrderHas = array('1' => '有');
//$data = array('bRecall' => '33.33','bPassword' => '123456') ;//改成百分之33 //預設密碼

$data = array('bRecall' => '33.33', 'bPassword' => genPwd()); //改成百分之33 //預設密碼

//20241223 服務費先行撥付同意書賦予預設值
$accdis_disabled = 0;

//個案傭金回饋
$sql = 'SELECT bId, bName FROM `tIndividualFeedBack`';
$rs  = $conn->GetAll($sql);

$menu_branchowner = [];
if (!empty($rs)) {
    $menu_branchowner[0] = '---';
    foreach ($rs as $k => $v) {
        $menu_branchowner[$v['bId']] = 'BM' . str_pad($v['bId'], 5, "0", STR_PAD_LEFT) . ' ' . $v['bName'];
    }
}
$rs = null;unset($rs);

$smarty->assign('address_disabled', $address_disabled);
$smarty->assign('sms_target', $sms_target);
$smarty->assign('menu_smsStyle', array('0' => '預設', '1' => '簽約日+買方姓名+賣方姓名+門牌+(店家簡訊固定文字)+服務費內容'));
$smarty->assign('menu_rg', array('1' => '是', '0' => '否'));
$smarty->assign('data_feedData_count', count($data_feedData));
$smarty->assign('today', $today);
$smarty->assign('menu_cstatus', array('1' => '是'));
$smarty->assign('is_edit', 0);
$smarty->assign('menu_BackDocument', array(1 => '身分證', 2 => '存摺', 3 => '登記事項卡'));
$smarty->assign('menu_mark', array('0' => '不標記', '1' => '標記'));
$smarty->assign('menu_group', $menu_group);
$smarty->assign('menu_categoryidentify', $menu_categoryidentify);
$smarty->assign('menu_categoryrealestate', $menu_categoryrealestate);
$smarty->assign('menu_categorybranchstatus', $menu_categorybranchstatus);
$smarty->assign('menu_categoryidentify', $menu_categoryidentify);
$smarty->assign('menu_categoryrecall', $menu_categoryrecall);
$smarty->assign('menu_categorybank_twhg', $menu_categorybank_twhg);
$smarty->assign('menu_emailreceive', $menu_emailreceive);
$smarty->assign('menu_message', $menu_message);
$smarty->assign('menu_cashierorderhas', $menu_cashierorderhas);
$smarty->assign('menu_bServiceOrderHas', $menu_bServiceOrderHas);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_bank', $menu_bank);
$smarty->assign('menu_ppl', $menu_ppl);
$smarty->assign('menu_sales', $menu_sales);
$smarty->assign('menu_note', array('' => '請選擇', 'INV' => 'INV', 'REC' => 'REC'));
$smarty->assign('data_sms', $data_sms);
$smarty->assign('data', $data);
$smarty->assign('addBranch', '1');
$smarty->assign('imgStampNew', '1');
$smarty->assign('listCity', listCity($conn)); //聯絡地址-縣市
$smarty->assign('listArea', listArea($conn)); //聯絡地址-區域
// $smarty->assign('listCity3', listCity($conn)) ;    //回饋金聯絡地址-縣市
// $smarty->assign('listArea3', listArea($conn)) ;    //回饋金聯絡地址-區域
// $smarty->assign('listCity2', listCity($conn)) ;    //回饋金戶籍地址-縣市
// $smarty->assign('listArea2', listArea($conn)) ;    //回饋金戶籍地址-區域
$smarty->assign('FeedCity', listCity($conn)); //回饋金-縣市
$smarty->assign('menu_feedDateCat', array(0 => '季', 1 => '月'));
$smarty->assign('channel_menu', ['A' => 'A', 'B' => 'B']);
$smarty->assign('menu_branchowner', $menu_branchowner); //個案傭金回饋
$smarty->assign('accdis_disabled', $accdis_disabled); //解匯帳戶不給經辦人員修改

$smarty->display('formbranch.inc.tpl', '', 'maintain');
