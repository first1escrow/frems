<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/util.class.php';
require_once dirname(dirname(__DIR__)) . '/class/confirmFeedback.class.php';

use First1\V1\ConfirmFeedback\ConfirmFeedback;
use First1\V1\Util\Util;

$code_msg = array(0 => '無資料', 1 => '確認成功', 2 => '部份成功', 3 => '確認失敗');
$output['code'] = 0;
$error_detail = [];
$error_counter = 0;

$case_data = json_decode(file_get_contents('php://input'), true);
$all_counter = count($case_data);

//更新(儲存)回饋金資料
$conn = new first1DB;

foreach ($case_data as $item) {
    $cId = $item['cid'];
    $fId = $item['fid'];
    $matches = [];

    if (!preg_match("/^\d{9}$/", $cId)) {
        $error_detail[] = 'Invalid cId(' . $cId . ')';
        $error_counter++;
    } else {
        $confirmFeedback = new ConfirmFeedback;
        $confirmFeedback->updateSalesConfirmTime($fId, $cId);
    }
}

if ($all_counter == 0) {
    $output['code'] = 0;
} else if ($error_counter == 0) {
    $output['code'] = 1;
} else {
    $output['code'] = ($error_counter == $all_counter) ? 3 : 2;
}
$output['message'] = $code_msg[$output['code']];
$output['error_detail'] = (count($error_detail) > 0) ? implode('，', $error_detail) : '';

echo json_encode($output);

exit;