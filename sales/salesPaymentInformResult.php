<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/IDCheck.php';

//預載log物件
$logs = new Intolog();
##

$_POST = escapeStr($_POST);

$bank             = $_POST['bank']; //查詢銀行系統
$bStoreClass      = $_POST['bStoreClass']; //查詢店身份 (總店:1、單店:2)
$sales_year       = $_POST['sales_year']; //查詢回饋年度
$sales_season     = $_POST['sales_season']; //查詢回饋季
$sales_year_end   = $_POST['sales_year_end'];
$sales_season_end = $_POST['sales_season_end'];
$certifiedid      = $_POST['certifiedid']; //查詢保證號碼
$bCategory        = $_POST['bCategory']; //查詢仲介商類型 (加盟:1、直營:2)
$branch           = $_POST['branch'];
$scrivener        = $_POST['scrivener'];
$storeSearch      = $_POST['bck'];
$filetype         = $_POST['filetype'];
$status           = $_POST['status'];
$caseStatus       = $_POST['caseStatus'];
$act              = $_POST['act'];
$brand            = $_POST['bd'];
$timeCategory     = $_POST['timeCategory'];
##

//確認是否為業務查詢
$sales = ($_SESSION['member_pDep'] == 7) ? $_SESSION['member_id'] : '';
##

//確認是否有指定查詢？若無、則查詢所有類別
$bCategory = (empty($scrivener) && empty($branch)) ? '1,2,3' : '';
##

//處理多筆地政士查詢
if (!empty($scrivener)) {
    $_arr = explode(',', $scrivener);
    foreach ($_arr as $k => $v) {
        // $v        = substr($v, 2);
        $_arr[$k] = (int) $v;
    }
    $scrivener = implode(',', $_arr);

    $_arr = null;unset($_arr);
}
##

//處理多筆仲介查詢
if (!empty($branch)) {
    $_arr = explode(',', $branch);
    foreach ($_arr as $k => $v) {
        // $v        = substr($v, 2);
        $_arr[$k] = (int) $v;
    }
    $branch = implode(',', $_arr);

    $_arr = null;unset($_arr);
}
##

$timeCategory = 0;
$_exception   = '總管理'; //過濾店名稱有"總管理"字樣的店家
// $_debug       = true; //顯示 sql 語法

require_once dirname(__DIR__) . '/includes/accounting/casefeedbackPDF2_result.php';

# 搜尋資訊
$smarty->assign('list', $list);

$smarty->display('salesPaymentInfomResult.inc.tpl', '', 'sales');
