<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/sms/sms_function.php';

$json = $_REQUEST["json"];
$sn   = $_REQUEST["sn"];

$send   = 'y';
$sms    = new SMS_Gateway();
$jsdata = json_decode($json);

//
$sql = "UPDATE tBankTrans SET tPayOk = '1' WHERE tId IN ('" . implode("','", $jsdata->datas) . "');";
$conn->Execute($sql);

BankTransSmsLog(trim($sn), date('Y-m-d H:i:s'));

$sql = 'SELECT
            a.tId,
            a.tVR_Code,
            a.tMemo,
            a.tObjKind,
            a.tMoney,
            a.tExport_nu,
            a.tExport_time,
            c.cScrivener,
            b.cBranchNum
        FROM
            tBankTrans AS a
        INNER JOIN
            tContractRealestate AS b ON a.tMemo = b.cCertifyId
        INNER JOIN
            tContractScrivener AS c ON a.tMemo = c.cCertifiedId
        WHERE
            a.tExport_nu = "' . $sn . '" AND a.tObjKind NOT IN("仲介服務費")
            AND a.tSend != 1
        GROUP BY a.tVR_Code,a.tObjKind';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    //確認本媒體檔案是否為點交(結案)且同時出款仲介服務費
    $realty    = 0;
    $mediaCode = trim($rs->fields['tExport_nu']);
    $mediaTime = trim($rs->fields['tExport_time']);
    $tVR       = trim($rs->fields['tVR_Code']);

    $sql = 'SELECT tMoney FROM tBankTrans WHERE tObjKind IN ("點交(結案)","預售屋") AND tKind="仲介" AND tVR_Code="' . $tVR . '" AND tExport_nu="' . $mediaCode . '";';
    $_rs = $conn->Execute($sql);

    while (!$_rs->EOF) {
        $realty += $_rs->fields['tMoney'] + 1 - 1;
        $_rs->MoveNext();
    }
    $_rs = null;unset($_rs);

    SMS_Send_Log_Insert($rs->fields);

    //查詢是否有自訂簡訊發送對象
    $sql = "SELECT * FROM tBankTranSms WHERE FIND_IN_SET (" . $rs->fields['tId'] . ",bBankTranId) AND bDel = 0 AND bVR_Code = '" . $rs->fields['tVR_Code'] . "' AND bObjKind = '" . $rs->fields['tObjKind'] . "'  AND (bExport_nu = '' OR bExport_nu = '" . $mediaCode . "')";
    $_rs = $conn->Execute($sql);

    if (!$_rs->EOF) { //有
        $_t = $sms->send2(trim($rs->fields["tVR_Code"]), trim($rs->fields["tObjKind"]), $tIdArr, $mediaCode, $send, $realty, $storeId);
    } else {
        $_t = $sms->send(trim($rs->fields["tVR_Code"]), trim($rs->fields["cScrivener"]), trim($rs->fields["cBranchNum"]), trim($rs->fields["tObjKind"]), trim($rs->fields["tId"]), $send, $realty);
    }

    print_r($_t);

    StepOK($rs->fields["tId"]);

    $rs->MoveNext();

}
$tIdArr = null;unset($tIdArr);

$sql = 'SELECT
		a.tId,
		a.tVR_Code,
		a.tMemo,
		a.tObjKind,
		a.tMoney,
		a.tExport_nu,
		a.tExport_time,
		c.cScrivener,
		b.cBranchNum,
		a.tStoreId
	FROM
		tBankTrans AS a
	INNER JOIN
		tContractRealestate AS b ON a.tMemo = b.cCertifyId
	INNER JOIN
		tContractScrivener AS c ON a.tMemo = c.cCertifiedId
	WHERE
		a.tExport_nu = "' . $sn . '" AND a.tObjKind IN("仲介服務費")
		AND a.tSend != 1';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    //確認本媒體檔案是否為點交(結案)且同時出款仲介服務費
    $realty    = 0;
    $mediaCode = trim($rs->fields['tExport_nu']);
    $mediaTime = trim($rs->fields['tExport_time']);
    $tVR       = trim($rs->fields['tVR_Code']);

    SMS_Send_Log_Insert($rs->fields);

    if ($rs->fields["tObjKind"] == '仲介服務費') {
        $storeId = $rs->fields['tStoreId'];
    }

    //查詢是否有自訂簡訊發送對象
    $sql = "SELECT * FROM tBankTranSms WHERE FIND_IN_SET (" . $rs->fields['tId'] . ",bBankTranId) AND bDel = 0 AND bVR_Code = '" . $rs->fields['tVR_Code'] . "' AND bObjKind = '" . $rs->fields['tObjKind'] . "'  AND (bExport_nu = '' OR bExport_nu = '" . $mediaCode . "')";
    $_rs = $conn->Execute($sql);

    if (!$_rs->EOF) { //有
        $_t = $sms->send2(trim($rs->fields["tVR_Code"]), trim($rs->fields["tObjKind"]), $rs->fields['tId'], $mediaCode, $send, $realty, $storeId);
    } else {
        $_t = $sms->send(trim($rs->fields["tVR_Code"]), trim($rs->fields["cScrivener"]), trim($rs->fields["cBranchNum"]), trim($rs->fields["tObjKind"]), trim($rs->fields["tId"]), $send, $realty);
    }
    $storeId = null;unset($storeId);

    print_r($_t);

    StepOK($rs->fields["tId"]);

    $rs->MoveNext();
}


function BankTransSmsLog($mediaCode, $mediaTime)
{
    global $conn;

    $sql = "INSERT INTO tBankTransSmsLog (bExport_nu,bExport_time)VALUES('" . $mediaCode . "','" . $mediaTime . "')";
    return $conn->Execute($sql);
}

function SMS_Send_Log_Insert($data)
{
    global $conn;

    $sql = "INSERT INTO tSMS_Send_Log (sCertifiedId,sKind,sTransId) VALUES ('" . substr($data['tVR_Code'], -9) . "','" . $data['tObjKind'] . "','" . $data['tId'] . "')";
    return $conn->Execute($sql);
}

function StepOK($tId)
{
    global $conn;

    $sql = "UPDATE tBankTrans SET tStep1 = 1,tStep2 = 1,tStep1Name = '" . $_SESSION['member_id'] . "',tStep1Time = '" . date('Y-m-d H:i:s') . "',tStep2Name = '" . $_SESSION['member_id'] . "',tStep2Time = '" . date('Y-m-d H:i:s') . "' WHERE tId ='" . $tId . "'";
    return $conn->Execute($sql);
}
exit('200');