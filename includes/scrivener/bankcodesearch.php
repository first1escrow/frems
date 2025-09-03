<?php
include_once dirname(dirname(__DIR__)) . '/class/getAddress.php';
include_once dirname(dirname(__DIR__)) . '/openadodb.php';

/**
 * 取得地政士資訊
 */
function GetScrivenerInfo($id)
{
    global $conn;

    $sql = "SELECT * FROM `tScrivener` Where sId = '" . $id . "' Order by sId;   ";
    $rs  = $conn->Execute($sql);
    return $rs->fields;
}

/**
 * 取得合約銀行保證號碼資訊
 */
function GetBankCode($id, $bc = '8')
{
    global $conn;

    $bankcode = [];
    $conBank  = [];

    $conBank = getBC($bc);
    if ($conBank !== false && isset($conBank['cBankVR'])) {
        $bc = $conBank['cBankVR'] . '%';
    } else {
        $bc = '%'; // 提供一個預設值
    }

    $sql = "SELECT * FROM `tBankCode` where bUsed = '0' AND bDel = 'n' AND bSID = '" . $id . "' AND bAccount LIKE '" . $bc . "' ORDER BY bVersion DESC";
    $rs  = $conn->Execute($sql);

    $result = [];
    while (! $rs->EOF) {
        $result[] = $rs->fields;
        $rs->MoveNext();
    }

    foreach ($result as $k => $v) {
        $bankcode[$v['bAccount']]['bAccount'] = $v['bAccount'];
        $bankcode[$v['bAccount']]['bVersion'] = $v['bVersion'];

        //99986
        if (preg_match("/^99986/", $v['bAccount'])) {
            $bankcode[$v['bAccount']]['branch'] = 6; // 城中
        }
    }

    return $bankcode;
}

/**
 * 取得合約銀行資訊
 */
function getBC($bc)
{
    global $conn;

    $sql = 'SELECT cBankCode,cBankVR FROM tContractBank WHERE cBankCode="' . $bc . '";';
    $rs  = $conn->Execute($sql);

    return $rs->fields;
}

if (! isset($_POST["id"])) {
    die('Error: Missing required parameter "id".'); // 提供明確的錯誤訊息
}

$result1 = GetScrivenerInfo($_POST["id"]);
// 確保 $result1 是陣列
if ($result1 === false) {
    $result1 = []; // 初始化為空陣列，避免後續錯誤
}

$result2 = GetBankCode($_POST["id"], isset($_POST["bc"]) ? $_POST["bc"] : '');

$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS sales FROM tScrivenerSales WHERE sScrivener = '" . $_POST["id"] . "'";
$rs  = $conn->Execute($sql);

// 安全地設置 sales 欄位
$result1['sales'] = ($rs && isset($rs->fields['sales'])) ? $rs->fields['sales'] : '';
// 安全地設置其他欄位
$result1['sAddress'] = isset($result1['sZip1']) ? filterCityAreaName($conn, $result1['sZip1'], isset($result1['sAddress']) ? $result1['sAddress'] : '') : '';
$result1['sCity']    = isset($result1['sZip1']) ? getCityName($conn, $result1['sZip1']) : '';
$result1['sArea']    = isset($result1['sZip1']) ? getAreaName($conn, $result1['sZip1']) : '';

exit(json_encode([$result1, $result2], JSON_UNESCAPED_UNICODE));
