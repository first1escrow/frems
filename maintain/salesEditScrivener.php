<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';

$tlog = new TraceLog();

//取得參數
$act   = trim(addslashes($_POST['op']));
$sid   = trim(addslashes($_POST['sid']));
$sn    = trim(addslashes($_POST['sn']));
$check = trim(addslashes($_POST['check']));
##

if ($check != 1) {

    $sid = '-1';
}

//決定資料庫操作模式
if ($act == 'add') {
    $sql = 'INSERT INTO tScrivenerSales (sSales, sScrivener, sStage) VALUES ("' . $sn . '", "' . $sid . '", "1");';
}
//為新增時
else if ($act == 'del') {
    $sql = 'DELETE FROM tScrivenerSales WHERE sId="' . $sn . '" AND sScrivener="' . $sid . '";';
}
//為刪除時

$tlog->insertWrite($_SESSION['member_id'], $sql, '新增/刪除負責業務對象');
##

//編輯資料
$returns = false;
$returns = $conn->Execute($sql);
##

require_once __DIR__ . '/salesScrivenerList.php';
