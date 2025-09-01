<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$city = $_POST['city'];
if (empty($city)) {
    http_response_code(400);
    exit;
}

$conn = new first1DB;
$sql  = 'SELECT
            zZip,
            zArea,
            zPerformanceSales,
            (SELECT pName FROM tPeopleInfo WHERE pId = zPerformanceSales) AS zPerformanceSalesName,
            zPerformanceScrivenerSales,
            (SELECT pName FROM tPeopleInfo WHERE pId = zPerformanceScrivenerSales) AS zPerformanceScrivenerSalesName
        FROM
            tZipArea
        WHERE
            zCity = :city
        ORDER BY
            zArea
        ASC;';
$area = $conn->all($sql, ['city' => $city]);

if (empty($area)) {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');
exit(json_encode($area, JSON_UNESCAPED_UNICODE));
