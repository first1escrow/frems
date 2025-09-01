<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$path = dirname(__DIR__) . '/log2';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}
$fw = fopen($path . '/scrivenrPresentDel.log', 'a+');

$_POST = escapeStr($_POST);

if ($_POST['id']) {
    $sql = "UPDATE tScrivenerLevel SET sStatus=0,sMoney=0,sReceipt=0,sGift=0,sReceipt=0,sIdentify=0,sName = '',sIdentify='0',sIdentifyIdNumber='',sTicket='',sZip='',sAddress='' WHERE sId = '" . $_POST['id'] . "'";
    fwrite($fw, "" . $_POST['id'] . "\r\n");
    if ($conn->Execute($sql)) {
        echo 'ok';
    } else {
        echo 'error';
    }
}

fclose($fw);
