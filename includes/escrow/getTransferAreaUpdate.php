<?php
header('Content-Type: application/json');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$cId = $_POST['cId'];
if (! preg_match("/^\d{9}$/", $cId)) {
    http_response_code(400);
    exit('Invalid certified ID');
}

$target = $_POST['target'];
if (! in_array($target, ['1', '2', '3', '4', '5'])) {
    http_response_code(400);
    exit('Unknown target');
}

$identifyId = $_POST['identifyId'];
if (empty($target)) {
    http_response_code(400);
    exit('Unknown target');
}

$type = $_POST['type'];
if (empty($type)) {
    http_response_code(400);
    exit('Invalid type');
}

$item = $_POST['item'];
if (empty($item)) {
    http_response_code(400);
    exit('Unknown item');
}

$power1 = $_POST['power1'];
if (empty($power1)) {
    http_response_code(400);
    exit('Unknown power1');
}

$power2 = $_POST['power2'];
if (empty($power2)) {
    http_response_code(400);
    exit('Unknown power2');
}

$before = isset($_POST['before']) ? $_POST['before'] : '';

$conn = new first1DB;

$conn->beginTransaction();

try {
                                                                                                                               //刪除所有該案件對應的身分證號使用者權利範圍紀錄
    $sql = 'DELETE FROM tContractTransferArea WHERE cCertifiedId = :cId AND cTarget = :target AND cIdentifyId = :identifyId;'; //先清除所有紀錄，成功後再新增
    $conn->exeSql($sql, ['cId' => $cId, 'target' => $target, 'identifyId' => $identifyId]);

    //刪除對應的前次紀錄
    $sql = 'DELETE FROM tContractTransferAreaBefore WHERE cCertifiedId = :cId AND cIdentifyId = :identify_id;';
    $conn->exeSql($sql, ['cId' => $cId, 'identify_id' => $identifyId]); //刪除所有相關的前次紀錄

    //新增依據身分證號使用者紀錄的權利範圍
    $values = [];
    foreach ($type as $k => $v) {
        if (empty($power1[$k]) || empty($power2[$k])) { //如果無權利範圍數值則略過不存
            continue;
        }

        $values[] = '(UUID(), "' . $cId . '", "' . $target . '", "' . $identifyId . '", "' . $v . '", "' . $item[$k] . '", "' . $power1[$k] . '", "' . $power2[$k] . '", NOW())';
    }

    //無權利範圍紀錄直接回應成功
    if (empty($values)) {
        $conn->endTransaction();
        exit(json_encode(['code' => 200, 'message' => 'OK'], JSON_UNESCAPED_UNICODE));
    }

    //單筆寫入新增紀錄
    $sql = 'INSERT INTO tContractTransferArea (uuid, cCertifiedId, cTarget, cIdentifyId, cTransferType, cTransferItem, cTranferPower1, cTranferPower2, cCreated_at) VALUES ' . implode(',', $values) . ';';
    $conn->exeSql($sql);

    //新增身分證號使用者前次紀錄
    if (! empty($before)) {
        $values = [];
        foreach ($before as $v) {
            list($_identify_no, $_land_item, $_item) = explode('-', $v);

            $values[] = '(UUID(), "' . $cId . '", "' . $target . '", "' . $_land_item . '", "' . $_item . '", "' . $_identify_no . '", NOW())';

            $_identify_no = $_land_item = $_item = null;
            unset($_identify_no, $_land_item, $_item);
        }

        $sql = 'INSERT INTO
                    tContractTransferAreaBefore
                (
                    uuid,
                    cCertifiedId,
                    cTarget,
                    cLandItem,
                    cItem,
                    cIdentifyId,
                    cCreated_at
                ) VALUES ' . implode(', ', $values) . ';';
        $conn->exeSql($sql);
    }

    $conn->endTransaction();

    exit(json_encode(['code' => 200, 'message' => 'OK'], JSON_UNESCAPED_UNICODE));
} catch (Exception $e) {
    $conn->cancelTransaction();

    http_response_code(400);
    exit('System error(' . $e->getMessage() . ')');
}
