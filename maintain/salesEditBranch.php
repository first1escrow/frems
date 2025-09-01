<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

//取得參數
$act  = trim(addslashes($_POST['op']));
$brid = trim(addslashes($_POST['brid']));
$sn   = trim(addslashes($_POST['sn']));
##

//決定資料庫操作模式
if ($act == 'add') {
    $sql = 'INSERT INTO tBranchSales (bSales, bBranch, bStage) VALUES ("' . $sn . '","' . $brid . '","1");';
}
//為新增時
else if ($act == 'del') {
    $sql = 'DELETE FROM tBranchSales WHERE bId="' . $sn . '" AND bBranch="' . $brid . '";';
}
//為刪除時
##

//編輯資料
$returns = false;
$returns = $conn->Execute($sql);
##

include_once 'salesBranchList.php';
