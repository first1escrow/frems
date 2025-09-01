<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/maintain/feedBackData.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/IDCheck.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Reader/Excel5.php';


#上傳檔案處理
//$uploaddir = __DIR__ . '/uploads/excel/';
$uploaddir = __DIR__ . DIRECTORY_SEPARATOR .'uploads'. DIRECTORY_SEPARATOR .'excel'. DIRECTORY_SEPARATOR;

# 設定檔案存放目錄位置
if (!is_dir($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}

# 設定檔案名稱
$uploadfile = $_FILES['file']['name'];
$uploadfile = $uploaddir . $uploadfile;


if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    $_file = $uploaddir . $_FILES["file"]["name"];
} else {
    exit('檔案上傳錯誤');
}

//讀取 excel 檔案
$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);

//檔案名稱
$objPHPExcel  = $objReader->load($_file);
$currentSheet = $objPHPExcel->getSheet(0); //讀取第一個工作表(編號從 0 開始)
$allLine      = $currentSheet->getHighestRow(); //取得總列數

for ($i = 2; $i <= $allLine; $i++ ) {
    $sId = trim($currentSheet->getCell("A{$i}")->getValue()); //編號
    $sStoreCode = substr($sId, 0, 2);
    $sId = substr($sId, 2);

    $timerange =trim($currentSheet->getCell("D{$i}")->getValue()); //結算時間
    $endTime = explode("~", $timerange);

    $date1 = date_create(substr($endTime[0],0,3) + 1911 . substr($endTime[0], 3));
    $date1 = date_format($date1,"Y-m-d");

    $date2 = date_create(substr($endTime[1],0,3) + 1911 . substr($endTime[1], 3));
    $date2 = date_format($date2,"Y-m-d");

    $sFeedBackMoneyTotal = trim($currentSheet->getCell("F{$i}")->getValue()); //回饋金額

    $sql = "UPDATE 
                tStoreFeedBackMoneyFrom 
            SET 
                sLock = '0', sDelete = '1', sDeleteName = '".$_SESSION['member_id']."' 
            WHERE 
                sStoreCode = '".$sStoreCode."' 
              AND sStoreId = '".$sId."' 
              AND sFeedBackMoneyTotal = '".$sFeedBackMoneyTotal."'
              AND sEndTime = '".$date1."'
              AND sEndTime2 = '".$date2."'
                ";

    $conn->Execute($sql);
}

//預載log物件
$logs = new Intolog();
##

$_POST            = escapeStr($_POST);
$bank             = $_POST['bk']; //查詢銀行系統
$bStoreClass      = $_POST['sc']; //查詢店身份 (總店:1、單店:2)
$sales_year       = $_POST['sy']; //查詢回饋年度
$sales_season     = $_POST['se']; //查詢回饋季
$sales_year_end   = $_POST['sales_year_end'];
$sales_season_end = $_POST['sales_season_end'];
$certifiedid      = $_POST['cd']; //查詢保證號碼
$bCategory        = $_POST['bc']; //查詢仲介商類型 (加盟:1、直營:2)
$branch           = $_POST['br'];
$scrivener        = $_POST['scr'];
$storeSearch      = $_POST['bck'];
$filetype         = $_POST['ft'];
$status           = $_POST['status'];
$act              = $_POST['act'];
$brand            = $_POST['bd'];
$timeCategory     = $_POST['timeCategory'];
##


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
