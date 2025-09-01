<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/util.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

$conn = new first1DB;

//檢核傳入參數
$post_year   = preg_match("/^\d{4}$/", $_POST['year']) ? $_POST['year'] : '';
$post_season = preg_match("/^[1|2|3|4]{1}$/", $_POST['season']) ? $_POST['season'] : '';
$post_sales  = preg_match("/^\d+$/", $_POST['sales']) ? $_POST['sales'] : '';

// $post_year   = '2023';
// $post_season = '2';
// $post_sales  = '38';

if (empty($post_year) || empty($post_season) || empty($post_sales)) {
    exit('No POST');
}
##

//季別轉換為起訖日期
$date_from_to = First1\V1\Util\Util::seasonToMonth($post_season);

$date_from = $post_year . '-' . str_pad($date_from_to[0], 2, '0', STR_PAD_LEFT) . '-01';
$date_to   = $post_year . '-' . str_pad($date_from_to[2], 2, '0', STR_PAD_LEFT) . '-31';

$date_from_to = null;unset($date_from_to);
##

//取得所有啟用的台屋仲介店
$sql = 'SELECT
            a.bId
        FROM
            tBranch AS a
        JOIN
            tBranchSalesForPerformance AS b ON a.bId = b.bBranch
        WHERE
            a.bBrand = 1
            AND a.bStatus = 1
            AND b.bSales = :sales
        ORDER BY bId ASC;';
$stores = array_column($conn->all($sql, ['sales' => $post_sales]), 'bId');
##

//取得期間內的店家
$sql = 'SELECT
            a.cCertifiedId,
            a.cApplyDate,
            a.cCaseStatus,
            b.cBrand,
            b.cBrand1,
            b.cBrand2,
            b.cBrand3,
            b.cBranchNum,
            b.cBranchNum1,
            b.cBranchNum2,
            b.cBranchNum3
        FROM
            tContractCase AS a
        JOIN
            tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId
        WHERE
            a.cApplyDate >= :from_date
            AND a.cApplyDate <= :to_date
            AND (b.cBrand = 1 OR b.cBrand1 = 1 OR b.cBrand2 = 1 OR b.cBrand3 = 1);';
$cases = $conn->all($sql, ['from_date' => $date_from . ' 00:00:00', 'to_date' => $date_to . ' 00:00:00']);

$realty  = array_column($cases, 'cBranchNum');
$realty1 = array_column($cases, 'cBranchNum1');
$realty2 = array_column($cases, 'cBranchNum2');
$realty3 = array_column($cases, 'cBranchNum3');

$realty     = array_unique(array_merge($realty, $realty1, $realty2, $realty3));
$has_income = array_filter($realty, function ($value) {
    return $value > 0;
});

$realty = $realty1 = $realty2 = $realty3 = null;
unset($realty, $realty1, $realty2, $realty3);

sort($has_income);

// echo '<pre>';
// print_r($has_income);
// print_r($stores);
// exit('stores');
##

//確認店家是否有進案
$results = array_diff($stores, $has_income);
// print_r($results);
// exit('results');
##

//顯示內容明細
$sql = 'SELECT
            a.bId,
            a.bStore,
            b.bCode,
            CONCAT(b.bCode, LPAD(a.bId, 5, "0")) as code,
            b.bName
        FROM
            tBranch AS a
        JOIN
            tBrand AS b ON a.bBrand = b.bId
        WHERE
            a.bId IN (' . implode(',', $results) . ');';
$list = $conn->all($sql);
##

exit(json_encode($list, JSON_UNESCAPED_UNICODE));
