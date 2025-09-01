<?php
// require_once dirname(dirname(__DIR__)) . '/first1DB.php';

// $tIds    = [1042086, 1042087, 1042088, 1042089, 1042090, 1042091, 1042093];
// $db_conn = new first1DB;

//取得保證號碼
$sql = 'SELECT tMemo FROM tBankTrans WHERE tId IN (' . implode(',', $tIds) . ') AND tObjKind IN ("點交(結案)", "解除契約", "建經發函終止", "預售屋") AND (tKind = "保證費" or tInvoice is not null);';
$all = $db_conn->all($sql);

if (!empty($all)) {
    $cIds = array_unique(array_column($all, 'tMemo'));

    //刪除保證號碼對應的中繼資料(中繼資料必須要為未匯出且未付款)
    $sql = 'DELETE FROM tBankTransRelay WHERE bCertifiedId IN (' . implode(',', $cIds) . ') AND bExport = 2 AND bPayOk = 2;';
    $db_conn->exeSql($sql);
}
