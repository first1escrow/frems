<?php

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

//取得政府轉移棟數區間
function getGovernmentRange($year)
{
    $conn = new first1DB;

    $sql = 'SELECT sYear, sMonth, CONCAT(sYear, sMonth) AS dt FROM tSalesBuildingTransfer WHERE sYear = :year GROUP BY dt ORDER BY dt ASC;';
    $rs  = $conn->all($sql, ['year' => $year]);

    $conn = null;unset($conn);

    return empty($rs) ? [] : [$rs[0]['sMonth'], $rs[(count($rs) - 1)]['sMonth']];
}
##

//取得業務列表
function getSales()
{
    $conn = new first1DB;

    $sales = [];
    if ($_SESSION['member_pDep'] == 7) {
        $sql = 'SELECT * FROM tPeopleInfo WHERE pId = :pId;';
        $rs  = $conn->all($sql, ['pId' => $_SESSION['member_id']]);
    } else {
        $sql   = 'SELECT * FROM tPeopleInfo WHERE pDep IN (4, 7) AND pJob = 1;';
        $rs    = $conn->all($sql);
        $sales = [0 => '全部'];
    }

    if (!empty($rs)) {
        foreach ($rs as $v) {
            $sales[$v['pId']] = $v['pName'];
        }
    }

    return $sales;
}
##

$alert = '';
$sales = preg_match("/^\d+$/", $_POST['sales']) ? $_POST['sales'] : 0;

//檢核開啟人員的身分
if (($_SESSION['member_pDep'] != 7) && !in_array($_SESSION['member_id'], [1, 2, 3, 6, 48])) {
    exit('Invalid Access!!');
}
##

//強制業務僅能看自己的 id
if ($_SESSION['member_pDep'] == 7) {
    $sales = $_SESSION['member_id'];
}
##

if (in_array($_POST['xls'], ['A', 'S'])) {
    $year = $_POST['year'] + 1911;

    $range = getGovernmentRange($year);
    if (empty($range)) {
        $alert = 'alert("查無政府轉移棟數資料");';
    } else {
        $from_month = $range[0];
        $to_month   = $range[1];

        require_once __DIR__ . '/salesPerformanceAnalysis.php';
    }

    $conn = $rs = $_POST = $range = null;
    unset($conn, $rs, $range, $_POST);
}

//年度選擇
$year       = empty($year) ? (date("Y") - 1911) : $year;
$gov_period = [];

for ($i = date("Y"); $i >= 2021; $i--) {
    $year_option[($i - 1911)] = ($i - 1911) . '年度';

    //政府移轉棟數
    $gov_period[($i - 1911)] = getGovernmentRange($i);
    ##
}
##

//業務列表
$sales_option = getSales();
// echo '<pre>';
// print_r($sales_option);exit;
##

$smarty->assign('year', $year);
$smarty->assign('year_option', $year_option);
$smarty->assign('sales', $sales);
$smarty->assign('sales_option', $sales_option);
$smarty->assign('gov_period', $gov_period);
$smarty->display('salesPerformance.inc.tpl', '', 'report');
