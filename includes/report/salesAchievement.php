<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

header('Content-Type: application/json');

if($_SESSION['member_pDep'] == 7) {
    $sales = $_SESSION['member_id'];
} else {
    $sales = $_POST['sales'];
}

if (!empty($sales) && !preg_match("/^\d+$/", $sales)) {
    exit(json_encode([
        'status'  => 400,
        'message' => 'Invalid sales info.',
    ]));
}

$year = $_POST['year'];
if (!preg_match("/^\d{2,3}$/", $year)) {
    exit(json_encode([
        'status'  => 400,
        'message' => 'Invalid year format',
    ]));
}

$conn = new first1DB;

//取得比率定義
$sql  = 'SELECT sRate as rate FROM tSalesReportAchievementRatio ORDER BY sDate DESC LIMIT 1;';
$rs   = $conn->one($sql);
$rate = empty($rs['rate']) ? 0.1 : $rs['rate'];
$rate = $rate + 1;
##

$adYear = $year + 1911;
//計算目標單月
$lastYear = $adYear - 1;

$sql = 'SELECT (SUM(sCertifiedMoney)-SUM(sFeedBackMoney))*(' . $rate . ' )/12 as Total
         FROM tSalesReportAchievement AS a
         WHERE a.sSales = :sSales AND YEAR(sDate) = :adyear ;';
$rs = $conn->one($sql, ['sSales' => $sales, 'adyear' => $lastYear]);

$monthTotal = round($rs['Total']);
##

//報表
$sql = "SELECT
            s.sDate,
            i.pName,
            s.mon,
            (@SUM2 := @SUM2 + s.mon) AS total,
            (s.sCertifiedMoney - s.sFeedBackMoney) AS sCertifiedMoney,
            (@sum := @sum + s.sCertifiedMoney - s.sFeedBackMoney) AS sCertifiedMoneyTotal,
            s.lastsCertifiedMoney,
            (@SUM3 := @SUM3 + s.lastsCertifiedMoney) AS lastsCertifiedMoneyTotal
        FROM
            (
                SELECT 
                    DATE_ADD( a.sDate , INTERVAL 1 YEAR ) AS sDate,
                    (SELECT sCertifiedMoney FROM tSalesReportAchievement WHERE sSales = :sSales and sDate =  DATE_ADD( a.sDate, INTERVAL 1 YEAR )) AS sCertifiedMoney,
                    (SELECT sFeedBackMoney FROM tSalesReportAchievement WHERE sSales = :sSales and sDate =  DATE_ADD( a.sDate, INTERVAL 1 YEAR )) AS sFeedBackMoney,
                    :monthTotal AS mon,
                    (sCertifiedMoney - sFeedBackMoney) AS lastsCertifiedMoney,
                    sSales
                FROM 
                    tSalesReportAchievement AS a , (select @sum :=0) b, (select @SUM2 :=0) c, (select @SUM3 :=0) d
                WHERE 
                    a.sSales = :sSales AND YEAR(sDate) = :lastYear
            ) AS s
        LEFT JOIN 
            tPeopleInfo AS i ON s.sSales = i.pId 
        ORDER BY 
            s.sDate ASC;";

$data = $conn->all($sql, ['sSales' => $sales, 'lastYear' => $lastYear, 'monthTotal' => $monthTotal]);
##

//摘要
$sql = 'SELECT
            SUM(sCertifiedMoney) as sCertifiedMoney,
            SUM(sFeedBackMoney) as sFeedBackMoney
        FROM
            tSalesReportAchievement
        WHERE
            YEAR(sDate) = :adyear
            AND sSales = :sales
';
$summary = $conn->one($sql, ['sales' => $sales, 'adyear' => ($adYear - 1)]);
##

exit(json_encode([
    'data'    => $data,
    'summary' => $summary,
]));

// echo json_encode($data);exit;
