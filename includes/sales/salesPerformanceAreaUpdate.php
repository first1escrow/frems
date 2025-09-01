<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

//結束(中斷)處理
function exitHandler($status, $message)
{
    exit(
        json_encode([
            'status'  => $status,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE)
    );
}
##

//更新地政士區域預設業務
function updateDefaultScriveners($zip, $sales)
{
    global $conn;

    $sql = 'UPDATE tZipArea SET zPerformanceScrivenerSales = :sales WHERE zZip = :zip;';
    return $conn->exeSql($sql, ['sales' => $sales, 'zip' => $zip]);
}
##

//更新仲介區域預設業務
function updateDefaultRealty($zip, $sales)
{
    global $conn;

    $sql = 'UPDATE tZipArea SET zPerformanceSales = :sales WHERE zZip = :zip;';
    return $conn->exeSql($sql, ['sales' => $sales, 'zip' => $zip]);
}
##

//更新區域內地政士所屬業務
function updateScriveners($zip, $sales, $date)
{
    global $conn;

    $sql        = 'SELECT sId FROM tScrivener WHERE sCpZip1 = :zip;';
    $scriveners = $conn->all($sql, ['zip' => $zip]);

    if (empty($scriveners)) {
        return;
    }

    foreach ($scriveners as $scrivener) {
        $sql = 'INSERT INTO
                    tScrivenerSalesForPerformance
                (
                    sId,
                    sSales,
                    sScrivener,
                    sCreatedAt
                )
                VALUES
                (
                    UUID(),
                    ' . $sales . ',
                    ' . $scrivener['sId'] . ',
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                    sSales = ' . $sales . ';';
        $conn->exeSql($sql);

        //20230107 新增當全區業務轉換時，紀錄轉換的時間
        $sql = 'INSERT INTO tSalesRegionalAttributionForPerformance SET sType = 1, sDate = :date, sZip = :zip, sStoreId = :store, sSales = :sales, sCreatTime = NOW();';
        $conn->exeSql($sql, [
            'date'  => $date,
            'zip'   => $zip,
            'store' => $scrivener['sId'],
            'sales' => $sales,
        ]);
        ##
    }

    return true;
}
##

//更新區域內仲介所屬業務
function updateRealty($zip, $sales, $date)
{
    global $conn;

    $sql    = 'SELECT bId FROM tBranch WHERE bZip = :zip;';
    $realty = $conn->all($sql, ['zip' => $zip]);

    if (empty($realty)) {
        return;
    }

    foreach ($realty as $k => $branch) {
        $sql = 'INSERT INTO
                    tBranchSalesForPerformance
                (
                    bId,
                    bSales,
                    bBranch,
                    bCreatedAt
                )
                VALUES
                (
                    UUID(),
                    ' . $sales . ',
                    ' . $branch['bId'] . ',
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                    bSales = ' . $sales . ';';
        $conn->exeSql($sql);

        //20230107 新增當全區業務轉換時，紀錄轉換的時間
        $sql = 'INSERT INTO tSalesRegionalAttributionForPerformance SET sType = 2, sDate = :date, sZip = :zip, sStoreId = :store, sSales = :sales, sCreatTime = NOW();';
        $conn->exeSql($sql, [
            'date'  => $date,
            'zip'   => $zip,
            'store' => $branch['bId'],
            'sales' => $sales,
        ]);
        ##
    }

    return true;
}
##

header('Content-Type: application/json');

$target = $_POST['target'];
$zip    = $_POST['zip'];
$sales  = $_POST['sales'];
$store  = $_POST['store'];
$date   = preg_match("/^\d{4}\-\d{1,2}\-\d{1,2}$/", $_POST['date']) ? $_POST['date'] : '0000-00-00';

if (!in_array($target, ['R', 'S'])) {
    exitHandler(400, '未指定欲轉移業務的類別(地政士或仲介)');
}

if (!preg_match("/^\d{3}\w{0,1}$/i", $zip)) {
    exitHandler(400, '未指定欲轉移的區域');
}

if (!preg_match("/^\d+$/", $sales)) {
    exitHandler(400, '未指定轉移的業務');
}

$store = ($store == 'ALL') ? $store : null;

$conn = new first1DB;

//
if ($target == 'S') {
    updateDefaultScriveners($zip, $sales);

    if ($store == 'ALL') {
        updateScriveners($zip, $sales, $date);
    }
    $scriveners = null;unset($scriveners);
} else {
    updateDefaultRealty($zip, $sales);

    if ($store == 'ALL') {
        updateRealty($zip, $sales, $date);
    }
    $realty = null;unset($realty);
}
##

$target = $zip = $sales = $store = null;
unset($target, $zip, $sales, $store);

exitHandler(200, 'OK');