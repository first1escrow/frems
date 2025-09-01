<?php

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

//傳入參數確認
$date = '';
if ($upload_format == 'winA') {
    $date = $_POST['winDate'];
    if (!preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $date)) {
        die('日期格式錯誤');
    }
}

$winning_type = trim(substr($upload_format, 3));

//上傳檔案處理
$uploaddir = __DIR__ . '/uploads/Winning/';
require_once __DIR__ . '/importInvoiceuploadfile.php';

//讀取檔案
$data = file($_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

//內容解析
$msg  = [];
$msg2 = [];

if (empty($data)) {
    die("檔案有問題");
}

$conn = new first1DB;

$cnt = 0;
foreach ($data as $v) {
    $company = $invoiceDate = $invoiceNo = $fromDate = $toDate = null;
    unset($company, $invoiceDate, $invoiceNo, $fromDate, $toDate);

    $company     = trim(substr($v, 0, 8));
    $invoiceDate = trim(substr($v, 8, 7)); //必為偶數年月
    $invoiceNo   = trim(substr($v, 15, 10));

    $date_year  = substr($invoiceDate, 0, 3);
    $date_month = substr($invoiceDate, 3);
    $fromDate   = $date_year . '/' . str_pad(((int) $date_month - 1), 2, '0', STR_PAD_LEFT) . '/01';
    $toDate     = $date_year . '/' . $date_month . '/31';

    $date_year = $date_month = null;
    unset($date_year, $date_month);

    if (empty($company) || ($company != '53549920')) {
        continue;
    }

    //更新同期發票中獎資料為已開獎
    if ($cnt == 0) {
        $sql = 'UPDATE tContractInvoiceQuery SET cWinning = "Y" WHERE cInvoiceDate >= :fromDate ANd cInvoiceDate <= :toDate AND cWinning = "N";';
        $conn->exeSql($sql, ['fromDate' => $fromDate, 'toDate' => $toDate]);
        $cnt = 1;
    }

    //更新資料
    $sql = 'SELECT cId FROM tContractInvoiceQuery WHERE cInvoiceNo = :no;';
    $rs  = $conn->one($sql, ['no' => $invoiceNo]);
    if (empty($rs['cId'])) {
        $msg[] = $invoiceNo;
        continue;
    }

    $sql = 'UPDATE tContractInvoiceQuery SET cWinning = :win, cWinningDate = :date, cWinningType = :type WHERE cId = :id;';
    $conn->exeSql($sql, ['id' => $rs['cId'], 'type' => $winning_type, 'date' => $date, 'win' => 'W']);
    if (empty($conn->exeSql($sql, ['id' => $rs['cId'], 'type' => $winning_type, 'date' => $date, 'win' => 'W']))) {
        $msg2[] = $invoiceNo;
    }
}

##顯示有問題的
$tbl = '<div class="div-inline"><table class="tb" cellpadding="10" cellspacing="10">';

if (!empty($msg)) {
    $tbl .= '<tr><th>無對應資料</th></tr>';

    for ($i = 0; $i < count($msg); $i++) {
        $tbl .= '<tr><td>發票號碼：' . $msg[$i] . '</td></tr>';
    }
}

if (!empty($msg2)) {
    $tbl .= '<tr><th>寫入失敗</th></tr>';

    for ($i = 0; $i < count($msg2); $i++) {
        $tbl .= '<tr><td>發票號碼：' . $msg2[$i] . '</td></tr>';
    }
}

$tbl .= '</table><br></div>';

if (empty($msg) && empty($msg2)) {
    $tbl = '<div class="div-inline2">上傳成功</div>';
}

$msg  = empty($msg) ? '' : implode(';', $msg);
$msg2 = empty($msg2) ? '' : implode(';', $msg2);

write_log($msg . ',' . $msg2, 'import_income_winning');

$smarty->assign('show', $tbl);
