<?php
// require_once dirname(dirname(__DIR__)) . '/first1DB.php';
// $cBankRelay = 'N';
// $tIds       = [1232029, 1232030, 1232031]; //非代墊利息案件
// $tIds = [1232010, 1232009, 1232000, 1231714]; //代墊利息案件
// $tIds    = [1232010, 1232029, 1232031]; //部分代墊利息案件
// $db_conn = new first1DB;

$fh = dirname(dirname(__DIR__)) . '/log/bank/payForBalance';
if (!is_dir($fh)) {
    mkdir($fh, 0777, true);
}
$fh .= '/bankRelay_' . date('Ymd') . '.php';

$sql = 'SELECT tId, tVR_Code, tObjKind, tAccount FROM tBankTrans WHERE tId IN (' . implode(',', $tIds) . ') AND tObjKind = "代墊利息";';
$all = $db_conn->all($sql);
file_put_contents($fh, date('Y-m-d H:i:s') . PHP_EOL . 'Sql: ' . $sql . PHP_EOL . 'Result:' . PHP_EOL . print_r($all, true), FILE_APPEND);

if (!empty($all)) {
    $cIds = array_unique(array_column($all, 'tAccount'));

    $sql = 'UPDATE tContractCase SET cBankRelay = "' . $cBankRelay . '" WHERE cEscrowBankAccount IN ("' . implode('","', $cIds) . '") AND cBankRelay != "C";';
    $db_conn->exeSql($sql);

    file_put_contents($fh, 'Update Sql: ' . $sql . PHP_EOL, FILE_APPEND);

    $cIds = $all = null;
    unset($cIds, $all);
}

file_put_contents($fh, PHP_EOL, FILE_APPEND);