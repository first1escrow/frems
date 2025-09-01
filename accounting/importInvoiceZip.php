<?php

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

//上傳檔案處理
$uploaddir = __DIR__ . '/uploads/zip/';
require_once __DIR__ . '/importInvoiceuploadfile.php';

//解壓縮
$_dir = $uploaddir;
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$zip = new ZipArchive;
$res = $zip->open($_file);

$response = 'failed';
if ($res === true) {
    $zip->extractTo($_dir);
    $response = 'ok';
}
$zip->close();

$tbl = ($response == 'ok') ? '解壓縮成功' : '解壓縮失敗';

//讀取解壓縮檔案
$not_exists    = [];
$insert_failed = [];

$_files = glob($_dir . '*.pdf');
$tbl    = empty($_files) ? '無檔案' : $tbl;
if (!empty($_files)) {
    foreach ($_files as $file) {
        $invoice_id = basename($file, '.pdf');
        $invoice_id = explode('_', $invoice_id);
        $invoice_id = empty($invoice_id[1]) ? '' : $invoice_id[1];

        $after_name = $invoice_id . '.pdf';

        if (empty($invoice_id)) {
            write_log('file = ' . $file . ', invoice_id = not exists', 'import_income_zip');
            $not_exists[] = $file;
            continue;
        }

        $match = [];
        preg_match('/^([A-ZA-z]+)/i', $invoice_id, $match);
        $prefix = empty($match[1]) ? 'unknow' : $match[1];

        $_dir = dirname($uploaddir) . '/eInvoice/' . $prefix;
        if (!is_dir($_dir)) {
            mkdir($_dir, 0777, true);
        }

        if (is_file($_dir . '/' . $after_name)) {
            unlink($_dir . '/' . $after_name);
        }

        rename($file, $_dir . '/' . $after_name);

        $sql = 'UPDATE tContractInvoiceQuery SET cInvoiceFile = "' . addslashes($_dir . '/' . $after_name) . '" WHERE cInvoiceNo = "' . $invoice_id . '";';
        if (!$conn->Execute($sql)) {
            write_log('sql = ' . $sql . ', db insert failed', 'import_income_zip');
            $insert_failed[] = $sql;
        }

        $invoice_id = $after_name = $match = $prefix = $_dir = null;
        unset($invoice_id, $after_name, $match, $prefix, $_dir);

        $tbl = '上傳完成';
    }
}
$_dir = $uploaddir;
array_map('unlink', glob($_dir . '*'));

if (is_dir($_dir)) {
    rmdir($_dir);
}

if ((count($not_exists) > 0) || (count($insert_failed) > 0)) {
    $tbl = '';

    if (count($not_exists) > 0) {
        $tbl .= '無法辨識的檔案';
        foreach ($not_exists as $file) {
            $tbl_list[] = basename($file);
        }
        $tbl .= '<br>' . implode('<br>', $tbl_list) . '<br><br>';

        $tbl_list = null;unset($tbl_list);
    }

    if (count($insert_failed) > 0) {
        $tbl .= '資料庫寫入失敗';
        foreach ($insert_failed as $file) {
            $tbl_list[] = basename($file);
        }
        $tbl .= '<br>' . implode('<br>', $tbl_list) . '<br><br>';

        $tbl_list = null;unset($tbl_list);
    }
}

$tbl = empty($tbl) ? '' : '<div class="div-inline2">' . $tbl . '</div>';

$smarty->assign('show', $tbl);
