<?php
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$msg = $_POST['txt'];
$lId = implode(',', $_POST['lId']);

## 名單##
$sql = "SELECT lLineId,lTargetCode FROM tLineAccount WHERE lStatus = 'Y' AND lId IN(" . $lId . ")";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}

##表情貼訊息處理##
$sql = "SELECT * FROM tLineMoji";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $code = substr($rs->fields['lCode'], 2);

    //
    $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code);

    //
    $emoticon = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');

    $msg = preg_replace("/<img .*alt" . $rs->fields['lCode'] . ".*>/", $emoticon, $msg);
    $msg = trim($msg);

    $rs->MoveNext();
}

// ##
$path = dirname(__DIR__) . '/log2';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}

$fw = fopen($path . '/linePush' . date('Ymd') . '.log', 'a+');
foreach ($list as $k => $v) {
    $url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=" . $v['lLineId'] . "&txt=" . urlencode($msg);
    fwrite($fw, $v['lTargetCode'] . $url . "\r\n");
    file_get_contents($url);
}
fclose($fw);

function hex2bin_code($code)
{
    $code = strtolower($code);
    $txt  = "";
    $i    = 0;

    do {
        $txt .= chr(hexdec($code{$i} . $code{($i + 1)}));
        $i += 2;
    } while ($i < strlen($code));

    return $txt;
}
