<?php
header('Content-Type: application/json');

require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

//取得轉移範圍資訊
function getUserTransferData(&$conn, $cId, $identifyId, $type, $item)
{
    $sql = 'SELECT cTranferPower1 as power1, cTranferPower2 as power2 FROM tContractTransferArea WHERE cCertifiedId = :cId AND cIdentifyId = :identifyId AND cTransferType = :type AND cTransferItem = :item;';
    $rs  = $conn->one($sql, ['cId' => $cId, 'identifyId' => $identifyId, 'type' => $type, 'item' => $item]);

    return empty($rs) ? [] : $rs;
}

//前次
function getTransferAreaBefore(&$conn, $cId, $land_item, $identifyId)
{
    $sql = 'SELECT cCertifiedId, cLandItem, cItem, cMoveDate, cLandPrice, cPower1, cPower2 FROM tContractLandPrice WHERE cCertifiedId = :cId AND cLandItem = :item AND cDel = 0;';
    $rs  = $conn->all($sql, ['cId' => $cId, 'item' => $land_item]);

    if (empty($rs)) {
        return [];
    }

    foreach ($rs as $k => $v) {
        //過濾未正確設定前次的紀錄
        if (empty($v['cLandPrice']) && empty($v['cPower1']) && empty($v['cPower2']) && (empty($v['cMoveDate']) || ($v['cMoveDate'] == '0000-00-00'))) {
            unset($rs[$k]);
            continue;
        }

        //是否已選取本前次
        $v['checked'] = empty(checkTransferAreaBefore($conn, $v['cCertifiedId'], $v['cLandItem'], $v['cItem'], $identifyId)) ? 'N' : 'Y';

        //前次範圍
        $v['power'] = '';
        if (!empty($v['cPower1']) && !empty($v['cPower2'])) {
            $v['power'] = $v['cPower1'] . '/' . $v['cPower2'];
        }

        //前次日期
        $v['date'] = '';
        if (!empty($v['cMoveDate'])) {
            $tmp       = explode('-', $v['cMoveDate']);
            $v['date'] = ($tmp[0] - 1911) . '-' . $tmp[1];
            $tmp       = null;unset($tmp);
        }

        $rs[$k] = $v;
    }

    return array_values($rs);
}

//是否已選取該前次
function checkTransferAreaBefore(&$conn, $cId, $land_item, $item, $identifyId)
{
    $sql = 'SELECT uuid FROM tContractTransferAreaBefore WHERE cCertifiedId = :cCertifiedId AND cLandItem = :cLandItem AND cItem = :cItem AND cIdentifyId = :cIdentifyId;';
    return empty($conn->one($sql, ['cCertifiedId' => $cId, 'cLandItem' => $land_item, 'cItem' => $item, 'cIdentifyId' => $identifyId])) ? false : true;
}

$cId = $_POST['cId'];
if (!preg_match("/^\d{9}$/", $cId)) {
    http_response_code(400);
    exit('Invalid Certified Id');
}

$identifyId = $_POST['identifyId'];
if (empty($identifyId)) {
    http_response_code(400);
    exit('Invalid Identify Id');
}

$target = $_POST['target'];
if (!in_array($target, ['1', '2', '3', '4', '5'])) { //1=其他買、2=其他賣、3=主買、4=主賣、5=其他買方登記人
    http_response_code(400);
    exit('Invalid Target');
}

$conn = new first1DB;

$data = [];

//土地
$sql   = 'SELECT cItem, cLand3 FROM tContractLand WHERE cCertifiedId = :cId AND cLand3 <> "";';
$lands = $conn->all($sql, ['cId' => $cId]);

if (!empty($lands)) {
    foreach ($lands as $v) {
        $record = [
            'type' => 'L',
            'no'   => $v['cLand3'],
            'item' => $v['cItem'],
        ];

        $record = array_merge($record, getUserTransferData($conn, $cId, $identifyId, $record['type'], $record['item']));

        if (in_array($target, [2, 4])) { //賣方才有
            $record['before'] = getTransferAreaBefore($conn, $cId, $v['cItem'], $identifyId);
        }

        $data[] = $record;
        $record = null;unset($record);
    }
}
$lands = null;unset($lands);

//建物
$sql      = 'SELECT cItem, cBuildNo FROM tContractProperty WHERE cCertifiedId = :cId AND cBuildNo <> "";';
$building = $conn->all($sql, ['cId' => $cId]);

if (!empty($building)) {
    foreach ($building as $v) {
        $record = [
            'type' => 'B',
            'no'   => $v['cBuildNo'],
            'item' => $v['cItem'],
        ];

        $data[] = array_merge($record, getUserTransferData($conn, $cId, $identifyId, $record['type'], $record['item']));
        $record = null;unset($record);
    }
}
$building = null;unset($building);

exit(json_encode(['code' => 200, 'message' => 'OK', 'data' => $data], JSON_UNESCAPED_UNICODE));
