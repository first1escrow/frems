<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$target         = $_POST['target'];
$selected_sales = $_POST['sales'];
$selected_area  = $_POST['area'];
$switch_date    = empty($_POST['switch_date']) ? null : $_POST['switch_date'];

if (empty($target) || empty($selected_sales) || empty($selected_area) || empty($switch_date)) {
    http_response_code(400);
    exit('NG');
}

$conn = new first1DB;

$target_field = $target === 'S' ? 'zPerformanceScrivenerSales' : 'zPerformanceSales';
$target_type  = $target === 'S' ? 'S' : 'R';
$current_date = date('Y-m-d');

// 如果切換日期小於或等於當天，直接更新 tZipArea
if ($switch_date <= $current_date) {
    foreach ($selected_area as $area) {
        // 更新 tZipArea
        $sql = 'UPDATE tZipArea SET ' . $target_field . ' = :sales WHERE zZip = :area;';
        $conn->exeSql($sql, ['sales' => $selected_sales, 'area' => $area]);
    }
}

// 寫入歷史記錄到 tZipAreaPerformanceSalesHistory
foreach ($selected_area as $area) {
    // 先取得區域資訊
    $sql       = 'SELECT zCity, zArea, zZone FROM tZipArea WHERE zZip = :area';
    $area_info = $conn->one($sql, ['area' => $area]);

    if ($area_info) {
        // 判斷是否已執行切換
        $is_executed = ($switch_date <= $current_date) ? 'Y' : 'N';

        // 檢查是否有設定過同時間業務，如果有就刪除
        $sql = "SELECT id FROM tZipAreaPerformanceSalesHistory WHERE zZip = '" . $area . "' AND zDate = '" . $switch_date . "'";
        $ids = $conn->all($sql);
        if (! empty($ids)) {
            $ids = array_column($ids, 'id');
            $sql = "DELETE FROM tZipAreaPerformanceSalesHistory WHERE id IN (" . implode(',', $ids) . ")";
            $conn->exeSql($sql);
        }

        // 插入歷史記錄
        $history_sql = 'INSERT INTO tZipAreaPerformanceSalesHistory
                        (zZip, zCity, zArea, zZone, zDate, zType, zSales, zAction, zCreatedAt)
                        VALUES (:zZip, :zCity, :zArea, :zZone, :zDate, :zType, :zSales, :zAction, NOW())';

        $history_params = [
            'zZip'    => $area,
            'zCity'   => $area_info['zCity'],
            'zArea'   => $area_info['zArea'],
            'zZone'   => $area_info['zZone'],
            'zDate'   => $switch_date,
            'zType'   => $target_type,
            'zSales'  => $selected_sales,
            'zAction' => $is_executed,
        ];

        $conn->exeSql($history_sql, $history_params);
    }
}

exit('OK');

//撈取指定縣市、日期與代書或仲介個郵遞區號的最後一筆歷史記錄
// SELECT t1 . *
//     FROM tZipAreaPerformanceSalesHistoryt1;
// INNER JOIN(
//     SELECT zZip, MAX(zDate) as maxDate;
//     FROM tZipAreaPerformanceSalesHistory;
//     WHERE zCity = '南投縣' and zDate <= '2025-10-25';
//     GROUP BYzZip
// )t2 ONt1 . zZip  = t2 . zZip and t1 . zDate  = t2 . maxDate;
// WHERE t1 . zCity = '南投縣' and zType = 'R';
