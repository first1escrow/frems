<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

function getBuyerOwner(&$conn, $cId, $table)
{
    $sql = 'SELECT cCertifiedId, cIdentifyId as identify_id, cName as name FROM ' . $table . ' WHERE cCertifiedId = :cId;';
    return $conn->one($sql, ['cId' => $cId]);
}

function getOtherOwner(&$conn, $cId)
{
    $sql = 'SELECT cCertifiedId, cIdentifyId as identify_id, cName as name, cIdentity as target FROM tContractOthers WHERE cCertifiedId = :cId AND cIdentity = 2;';
    return $conn->all($sql, ['cId' => $cId]);
}

$cId = $_GET['cId'];

$conn = new first1DB;

//POST 儲存
if (! empty($_POST['cId']) && preg_match("/^\d{9}$/", $_POST['cId'])) {
    $cId = $_POST['cId'];
    // echo '<pre>';
    // print_r($_POST);

    $conn->beginTransaction();
    try {
        //刪除目前的紀錄
        $sql = 'DELETE FROM tContractTransferAreaBefore WHERE cCertifiedId = :cId;';
        $conn->exeSql($sql, ['cId' => $cId]);

        if (! empty($_POST['before'])) {
            $sql = '';
            foreach ($_POST['before'] as $v) {
                $data = explode('_', $v);
                $sql .= empty($sql) ? '' : ',';
                $sql .= '(UUID(), "' . $data[0] . '", "' . $data[1] . '", "' . $data[2] . '", "' . $data[3] . '", "' . $data[4] . '", NOW())';
                $data = null;unset($data);
            }

            $sql = 'INSERT INTO
                        tContractTransferAreaBefore
                    (
                        uuid, cCertifiedId, cTarget, cLandItem, cItem, cIdentifyId, cCreated_at
                    ) VALUES ' . $sql;
            $conn->exeSql($sql);
        }

        $conn->endTransaction();
    } catch (Exception $e) {
        $conn->cancelTransaction();
        echo $e->getMessage() . "<br>\n";
        echo $conn->debug() . "<br>\n";
    }
}

if (! preg_match("/^\d{9}$/", $cId)) {
    exit('Invalid CertifiedId Format!');
}

// $cId = '030121158';
// echo '<pre>';

//取得目前的前次紀錄
$sql    = 'SELECT cCertifiedId, cTarget, cLandItem, cItem as item, cIdentifyId FROM tContractTransferAreaBefore WHERE cCertifiedId = :cId;';
$before = $conn->all($sql, ['cId' => $cId]);
// 確保 $before 是一個陣列
if (! is_array($before)) {
    $before = [];
}
// print_r($before);exit;

//取得土地地號等相關資訊
$sql   = 'SELECT cItem, cLand1, cLand2, cLand3 FROM tContractLand WHERE cCertifiedId = :cId;';
$lands = $conn->all($sql, ['cId' => $cId]);

// 確保每個土地記錄都有 before 索引
foreach ($lands as $k => $v) {
    if (! isset($lands[$k]['before'])) {
        $lands[$k]['before'] = [];
    }
}
// print_r($lands);exit;

//取得土地地號前次資訊
$sql         = 'SELECT cLandItem, cItem, cMoveDate, cLandPrice, cPower1, cPower2 FROM tContractLandPrice WHERE cCertifiedId = :cId AND cDel = 0;';
$before_data = $conn->all($sql, ['cId' => $cId]);
// print_r($before_data);exit;

if (! empty($before_data)) {
    foreach ($lands as $k => $v) {
        foreach ($before_data as $ka => $va) {
            if (($v['cItem'] == $va['cLandItem']) && (preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $va['cMoveDate']) && ($va['cMoveDate'] != '0000-00-00')) && ! empty($va['cLandPrice'])) {
                $tmp             = explode('-', $va['cMoveDate']);
                $va['cMoveDate'] = ($tmp[0] - 1911) . '/' . str_pad($tmp[1], 2, '0', STR_PAD_LEFT);
                $tmp             = null;unset($tmp);

                $va['cLandPrice'] = number_format($va['cLandPrice']);
                $va['power']      = $va['cPower1'] . '/' . $va['cPower2'];

                $lands[$k]['before'][] = $va;
            }
        }
    }
}
$before_data = null;unset($before_data);
// print_r($lands);exit('land_after');

//取得主買賣方
$owners   = [];
$owners[] = array_merge(getBuyerOwner($conn, $cId, 'tContractOwner'), ['target' => 4]);

//取得其他買賣方
$others = [];
$others = getOtherOwner($conn, $cId);

if (! empty($others)) {
    foreach ($others as $k => $v) {
        $owners[] = [
            'identify_id' => $v['identify_id'],
            'name'        => $v['name'],
        ];
    }
}
$others = null;unset($others);

$owners = array_map(function ($item) use ($lands, $before) {
    foreach ($lands as $k => $v) {
        // 確保 'before' 索引存在
        if (! isset($v['before'])) {
            continue;
        }

        foreach ($v['before'] as $ka => $va) {
            $va['selected'] = 'N';

            foreach ($before as $kb => $vb) {
                if ($item['identify_id'] == $vb['cIdentifyId']) { //身分證號相同
                    if (($va['cLandItem'] == $vb['cLandItem']) && ($va['cItem'] == $vb['item'])) {
                        $va['selected'] = 'Y';
                        break;
                    }
                }
            }

            $v['before'][$ka] = $va;
        }

        $lands[$k] = $v;
    }
    $item['lands'] = $lands;

    return $item;
}, $owners);
// print_r($owners);exit;

// 初始化 $buyers 變數 (目前模板中有使用但沒有賦值)
$buyers = [];

$smarty->assign('cId', $cId);
$smarty->assign('owners', $owners);
$smarty->assign('buyers', $buyers);

$smarty->display('landBeforeTransferEdit.inc.tpl', '', 'escrow');
