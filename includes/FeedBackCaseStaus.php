<?php
include_once '../openadodb.php';
include_once '../session_check.php';

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

$_POST = escapeStr($_POST) ;
$cid = $_POST['cid'];
$status = $_POST['status'];
$type = $_POST['type'];
$scrivenStatus = ($status == 1) ? 1 : 0;


if($type == 'A') {
    $sql = "UPDATE tContractCase SET cFeedBackClose ='".$status."', cFeedBackScrivenerClose ='".$scrivenStatus."' WHERE cCertifiedId ='".$cid."'";

}
//關閉地政士
//if($type == 'S') {
//    $sql = "UPDATE tContractCase SET cFeedBackScrivenerClose ='".$status."' WHERE cCertifiedId ='".$cid."'";
//}
// echo $sql."\r\n";
$rs = $conn->Execute($sql);

_writeLog($cid, $sql, '回饋金暫時解鎖');
echo 'ok';


?>