<?php
require_once dirname(__DIR__) . '/openadodb.php';
// require_once dirname(__DIR__) . '/includes/sales/getSalesArea.php';
require_once dirname(__DIR__) . '/includes/sales/getSalesAreaForPerformance.php';

$_POST = escapeStr($_POST);

$branch = $_POST['branch'];
$sales  = $_POST['sales']; //array
$cat    = $_POST['cat'];

$date = $_POST['date'];
// 處理HTML5 date輸入格式（YYYY-MM-DD）轉換為資料庫格式
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    // 如果是西元年格式（HTML5 date），直接使用
    $date = $date;
} else {
    // 如果是民國年格式，轉換為西元年
    $date = (substr($date, 0, 3) + 1911) . substr($date, 3);
}

if (empty($sales)) {
    die("請選擇業務");
}
sort($sales);

$check = 0;
foreach ($branch as $k => $v) {
    setBranchPerformanceSales($v, $sales, $date);
    setSalesRegionalAttribution($v, $sales, $date);

    $check++;
}

if ($check == count($branch)) {
    exit('成功');
}

exit('失敗');

function setSalesRegionalAttribution($branch, $sales, $date)
{
    global $conn;

    $zip = getBranchzip($branch);

    //檢查是否有設定過同時間業務，如果有就刪除
    $sql = "SELECT sId FROM tSalesRegionalAttributionForPerformance WHERE sType = '2' AND sStoreId = '" . $branch . "' AND sDate = '" . $date . "'";
    $rs  = $conn->Execute($sql);

    if (! $rs->EOF) {
        while (! $rs->EOF) {
            $sql = "DELETE FROM tSalesRegionalAttributionForPerformance WHERE sId = '" . $rs->fields['sId'] . "'";
            $conn->Execute($sql);

            $rs->MoveNext();
        }
    }

    foreach ($sales as $value) {
        $sql = "INSERT INTO tSalesRegionalAttributionForPerformance(sType,sZip,sStoreId,sSales,sDate,sCreatTime) VALUES('2','" . $zip['bZip'] . "','" . $branch . "','" . $value . "','" . $date . "','" . date('Y-m-d H:i:s') . "')";
        $conn->Execute($sql);
    }
}

function setBranchPerformanceSales($bid, $sales, $date)
{
    global $conn;

    $current_date = date('Y-m-d');
    if ($date == $current_date) {
        setBranchSales($bid, $sales);
    }

    //檢查是否有設定過同時間業務，如果有就刪除
    $sql = "SELECT bId FROM tBranchSalesForPerformanceHistory WHERE bBranch = '" . $bid . "' AND bDate = '" . $date . "';";
    $rs  = $conn->Execute($sql);

    if (! $rs->EOF) {
        $ids = [];
        while (! $rs->EOF) {
            $ids[] = $rs->fields['bId'];
            $rs->MoveNext();
        }

        $sql = "DELETE FROM tBranchSalesForPerformanceHistory WHERE bId IN (" . implode(',', $ids) . ")";
        $conn->Execute($sql);
    }

    // 判斷是否已執行切換
    $is_executed = ($date <= $current_date) ? 'Y' : 'N';

    //記錄至歷史資料記錄內
    if (! empty($sales)) {
        foreach ($sales as $value) {
            $sql = "INSERT INTO tBranchSalesForPerformanceHistory(bId, bSales, bBranch, bDate, bAction, bCreatedAt) VALUES(UUID(), '" . $value . "', '" . $bid . "', '" . $date . "', '" . $is_executed . "', NOW())";
            $conn->Execute($sql);
        }
    }
}
