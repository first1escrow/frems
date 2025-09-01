<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

$sn   = $_POST['sn'];
$data = explode(',', $sn);

if (empty($data)) {
    exit('N1');
}

$conn = new first1DB;

$tf = true;
foreach ($data as $v) {
    $sql = 'DELETE FROM `tSMSWaitSend` WHERE `uuid` = :uuid;';
    if (!$conn->exeSql($sql, ['uuid' => $v])) {
        $tf = false;
    }
}

if (empty($tf)) {
    exit('N2');
}

exit('Y');
