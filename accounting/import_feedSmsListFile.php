<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Reader/Excel5.php';

$cat = $_POST['cat'];
# 設定檔案存放目錄位置

$uploaddir = __DIR__ . '/excel/feedbacksms/';
if (!is_dir($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}

$today = date('YmdHis');
##

// # 設定檔案名稱
$uploadfile = $uploaddir . $today . '.xlsx';

if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile)) {
    $xls = $uploadfile;

} else {
    die("檔案上傳錯誤");
}
##

//讀取 excel 檔案
$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);

//檔案名稱
$objPHPExcel  = $objReader->load($xls);
$currentSheet = $objPHPExcel->getSheet(0); //讀取第一個工作表(編號從 0 開始)
$allLine      = $currentSheet->getHighestRow(); //取得總列數

$i = 0;
for ($excel_line = 2; $excel_line <= $allLine; $excel_line++) {
    $list[$i]['code'] = trim($currentSheet->getCell("A{$excel_line}")->getValue());

    $i++;
    unset($ck);
}
$ck = 0;
for ($i = 0; $i < count($list); $i++) {
    //tFeedBackSmsLog
    $sql = "INSERT INTO tFeedBackSmsLog (fCode,fDate,fCategory) VALUES ('" . $list[$i]['code'] . "','" . $today . "','" . $cat . "')";
    if (!$conn->Execute($sql)) {
        $ck = 1;
    }
}

if ($ck == 0) {
    $msg = '上傳成功';
} else {
    $msg = '上傳失敗';
}
##寫入資料庫
//msg:找無對應值 msg2:有寫入過 msg3:寫入失敗 msg4:資料有缺

$smarty->assign('show', $msg);
##
