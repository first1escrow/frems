<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$cId   = $_POST['cId'];
$other = $_POST['other'];
$to    = $_POST['to'];

$arr = [];
if (empty($cId) || !preg_match("/^\d{9}$/", $cId)) {
    exit(json_encode($arr));
}

$conn = new first1DB;

if (!empty($other) && ($other == 'Y')) { //取得其他買賣方 ID
    $sql = 'SELECT `cIdentifyId` FROM `tContractOthers` WHERE `cCertifiedId` = :cId AND `cIdentity` IN (1, 2);';
    $rs  = $conn->all($sql, ['cId' => $cId]);
} else { //買 or 賣方 ID
    $to  = ($to == 'b') ? 1 : 2; //1:買、2:賣
    $sql = 'SELECT `cIdentifyId` FROM `tContractOthers` WHERE `cCertifiedId` = :cId AND `cIdentity` = :to;';
    $rs  = $conn->all($sql, ['cId' => $cId, 'to' => $to]);
}

$conn = null;
unset($conn);

if (empty($rs)) {
    exit(json_encode($arr));
}

foreach ($rs as $v) {
    if (!empty($v['cIdentifyId'])) {
        $arr[] = $v['cIdentifyId'];
    }
}

exit(json_encode($arr));
