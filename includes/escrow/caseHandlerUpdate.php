<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$id = $_POST['id'];
if (!preg_match("/^\d{9}$/", $id)) {
    exit('Invalid id format');
}

$to = $_POST['to']; //1:轉給法務 2:返還經辦
if (!preg_match("/^[1|2]{1}$/", $to)) {
    exit('Invalid target');
}

$vrCode = $_POST['vr_code'];
if (!preg_match("/^\d{14}$/", $vrCode)) {
    exit('Invalid vr_code format');
}

$legalAllow = ($to == 1) ? 1 : 0;

$conn = new first1DB;

$sql = 'UPDATE tBankTrans SET tLegalAllow = :allow WHERE tMemo = :cId AND tOk = "2" AND tPayOk = "2" AND tLegalAllow <> "2";';
$conn->exeSql($sql, ['cId' => $id, 'allow' => $legalAllow]);

$sql = 'UPDATE tBankTrans SET tLegalAllow = :allow WHERE tVR_Code = "55006110050011" AND  tAccount = :account AND tOk = "2" AND tPayOk = "2" AND tLegalAllow <> "2";';
$conn->exeSql($sql, ['account' => $vrCode, 'allow' => $legalAllow]);

if ($to == 1) {
    //列管列表
    $sql = 'INSERT INTO `tLegalCase` SET lCertifiedId = :cId,  lTranserTime = NOW();';
    $conn->exeSql($sql, ['cId' => $id]);
}


$sql = 'UPDATE tContractCase SET cCaseHandler = :to WHERE cCertifiedId = :cId;';
echo $conn->exeSql($sql, ['to' => $to, 'cId' => $id]) ? 'OK' : 'NG';
