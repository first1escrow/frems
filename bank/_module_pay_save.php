<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

//
$jsdata = json_decode($_POST["json"]);

if (empty($jsdata)) {
    exit('401');
}

//
$conn = new first1DB;
$sql  = "UPDATE tBankTrans SET tPayOk = '1', tSend = '1' WHERE tId IN ('" . implode("','", $jsdata->datas) . "');";
$conn->exeSql($sql);

// 通知業務案件已結案(綁定項目為回饋金)，收據須盡速繳回
$db_conn = new first1DB;

$sql = 'SELECT tMemo FROM tBankTrans WHERE tId IN (' . implode(',', $jsdata->datas) . ') AND tObjKind IN ("點交(結案)", "解除契約", "建經發函終止", "預售屋");';
$all = $db_conn->all($sql);

if (!empty($all)) {
    $cIds = array_unique(array_column($all, 'tMemo'));
    $vars = implode('_', $cIds);
    $cIds = null;unset($cIds);
}

$db_conn = $cIds = $all = $vars = $cmd = null;
unset($db_conn, $cIds, $all, $vars, $cmd);

exit('200');
