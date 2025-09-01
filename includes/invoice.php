<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

$vr_code = $_POST['vr_code'];
$conn = new first1DB;

//鎖定隨案代書回饋金
if(checkFeedDateCat($vr_code) == 2) {
    $sql = "UPDATE `tContractCase` SET cFeedBackScrivenerClose = 1 WHERE cEscrowBankAccount = '$vr_code'";
    $rs = $conn->exeSql($sql);
    unset($rs);
    _writeLog($vr_code, $sql, '回饋金鎖定代書欄位');
}

function checkFeedDateCat($vr_code)
{
    global $conn;
    $sql = "SELECT cScrivener, s.sFeedDateCat FROM `tContractScrivener` AS cs LEFT JOIN `tScrivener` as s ON cs.cScrivener = s.sId WHERE cCertifiedId = '".substr($vr_code, -9)."'";
    $rs = $conn->one($sql);

    return $rs['sFeedDateCat'];
}

//開發票
$sql = "UPDATE `tBankTrans` SET tInvoice = NOW() WHERE tVR_Code = '$vr_code' AND tPayOk = 2";
$rs = $conn->exeSql($sql);
unset($rs);

function _writeLog($cId, $pattern, $reason)
{
    $txt = "===========================\r\n";
    $txt .= "Date: " . date("Y-m-d H:i:s") . "\r\n";
    $txt .= "Cid: " . $cId . "\r\n";
    $txt .= "Reason: " . $reason . "\r\n";

    $txt .= "Pattern: " . $pattern . "\r\n";
    $txt .= "===========================\r\n";

    $path = dirname(__DIR__) . '/log/feedback';

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $fw = fopen($path . '/' . date("Ymd") . '.log', 'a+');
    fwrite($fw, $txt . "\r\n");
    fclose($fw);
}

echo '已確定開發票';
