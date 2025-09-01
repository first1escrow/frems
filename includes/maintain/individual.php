<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

function format_element(&$conn, $individuals)
{
    if (empty($individuals)) {
        return [];
    }

    $sql    = 'SELECT bId, bStore FROM tBranch WHERE bId IN (' . implode(',', $individuals) . ')';
    $realty = $conn->all($sql);

    $individuals = [];
    foreach ($realty as $v) {
        $v['bStore']   = empty($v['bStore']) ? '未命名' : $v['bStore'];
        $individuals[] = '<span style="padding:5px;border: 1px solid;border-radius:5px;font-size:9pt;margin-right:10px;margin-top:5px;">' . $v['bStore'] . '<a href="Javascript:individual(\'DELETE\', ' . $v['bId'] . ')" style="margin-left:2px;font-size:9pt;">刪除</a></span>';
    }

    return $individuals;
}

$bId    = $_POST['bId'];
$action = $_POST['action'];
$store  = $_POST['store'];

if (empty($bId) || !is_numeric($bId)) {
    http_response_code(400);
    exit('無法取得店家資訊');
}

if (!in_array($action, ['ADD', 'DELETE'])) {
    http_response_code(400);
    exit('無法取得動作資訊');
}

if (empty($store) || !is_numeric($store)) {
    http_response_code(400);
    exit('無法取得個案回饋資訊');
}

$conn = new first1DB;

$sql    = 'SELECT bId, bIndividual FROM tBranch WHERE bId = :bId';
$realty = $conn->one($sql, ['bId' => $bId]);

if (empty($realty)) {
    http_response_code(400);
    exit('無法取得店家資訊');
}

$individuals = empty($realty['bIndividual']) ? [] : explode(',', $realty['bIndividual']);

//增加
if ($action == 'ADD') {
    $individuals[] = $store;
    $individuals   = array_unique($individuals);

    $sql = 'UPDATE tBranch SET bIndividual = :bIndividual WHERE bId = :bId';
    $conn->exeSql($sql, ['bIndividual' => implode(',', $individuals), 'bId' => $bId]);

    $individuals = format_element($conn, $individuals);

    exit(implode('', $individuals));
}

//刪除
if ($action == 'DELETE') {
    $key = array_search($store, $individuals);
    if (preg_match("/^[0-9]+$/", $key)) {
        unset($individuals[$key]);

        $sql = 'UPDATE tBranch SET bIndividual = :bIndividual WHERE bId = :bId';
        $conn->exeSql($sql, ['bIndividual' => implode(',', $individuals), 'bId' => $bId]);
    }

    $individuals = format_element($conn, $individuals);

    exit(implode('', $individuals));
}
