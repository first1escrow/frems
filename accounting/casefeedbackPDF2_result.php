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

$_POST            = escapeStr($_POST);
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
$act              = $_POST['act'];
$brand            = $_POST['bd'];
$timeCategory     = $_POST['timeCategory'];
##

// $_debug = true;

if ($act == 'pdf') {
    require_once __DIR__ . '/casefeedbackPDF2_resultPDF.php';
}

require_once dirname(__DIR__) . '/includes/accounting/casefeedbackPDF2_result.php';

# 搜尋資訊
$smarty->assign('bank', $bank);
$smarty->assign('bStoreClass', $bStoreClass);
$smarty->assign('sales_year', $sales_year);
$smarty->assign('sales_year_end', $sales_year_end);
$smarty->assign('sales_season', $sales_season);
$smarty->assign('sales_season_end', $sales_season_end);
$smarty->assign('certifiedid', $certifiedid);
$smarty->assign('bCategory', $bCategory);
$smarty->assign('branch', $branch);
$smarty->assign('scrivener', $scrivener);
$smarty->assign('storeSearch', $storeSearch);
$smarty->assign('filetype', $filetype);
$smarty->assign('status', $status);
$smarty->assign('act', $act);
$smarty->assign('brand', $brand);
$smarty->assign('timeCategory', $timeCategory);
# 搜尋資訊
$smarty->assign('list', $list);
$smarty->display('casefeedbackPDF2_result.inc.tpl', '', 'accounting');
