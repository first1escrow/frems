<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
// print_r($_POST);exit;
$sales = $_POST['sales'];
$store = $_POST['store'];
$date  = preg_match("/^\d{4}\-\d{1,2}\-\d{1,2}$/", $_POST['date']) ? $_POST['date'] : '0000-00-00';

//
if (!preg_match("/^\d+$/", $sales)) {
    exit('NG(1)');
}
##

//
$store = $_POST['store'];
if (!preg_match("/^[A-Z]{2}\d{4,5}$/", $store)) {
    exit('NG(2)');
}
##

//
$code = substr($store, 0, 2);
$id   = (int) substr($store, 2);
##
// echo 'code = ' . $code . "\n";
// echo 'store = ' . $id . "\n";
// exit;
$conn = new first1DB;

if ($code == 'SC') {
    /*** 20230107 新增當全區業務轉換時，紀錄轉換的時間 **/
    //取得zip
    $sql = 'SELECT sCpZip1 as zip FROM tScrivener WHERE sId = :id;';
    $rs  = $conn->one($sql, ['id' => $id]);
    $zip = $rs['zip'];

    $rs = null;unset($rs);
    ##

    //紀錄轉換的時間
    $sql = 'INSERT INTO tSalesRegionalAttributionForPerformance SET sType = 1, sDate = :date, sZip = :zip, sStoreId = :store, sSales = :sales, sCreatTime = NOW();';
    $conn->exeSql($sql, [
        'date'  => $date,
        'zip'   => $zip,
        'store' => $id,
        'sales' => $sales,
    ]);

    $zip = null;unset($zip);
    ##

    //更新店家業務
    $sql = 'UPDATE tScrivenerSalesForPerformance SET sSales = :sales WHERE sScrivener = :id;';
    ##
} else {
    /*** 20230107 新增當全區業務轉換時，紀錄轉換的時間 **/
    //取得zip
    $sql = 'SELECT bZip as zip FROM tBranch WHERE bId = :id;';
    $rs  = $conn->one($sql, ['id' => $id]);
    $zip = $rs['zip'];

    $rs = null;unset($rs);
    ##

    //紀錄轉換的時間
    $sql = 'INSERT INTO tSalesRegionalAttributionForPerformance SET sType = 2, sDate = :date, sZip = :zip, sStoreId = :store, sSales = :sales, sCreatTime = NOW();';
    $conn->exeSql($sql, [
        'date'  => $date,
        'zip'   => $zip,
        'store' => $id,
        'sales' => $sales,
    ]);

    $zip = null;unset($zip);
    ##

    //更新店家業務
    $sql = 'UPDATE tBranchSalesForPerformance SET bSales = :sales WHERE bBranch = :id;';
    ##
}

if ($conn->exeSql($sql, ['sales' => $sales, 'id' => $id])) {
    exit('OK');
} else {
    exit('NG');
}