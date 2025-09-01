<?php
require_once dirname(__DIR__).'/configs/config.class.php';
require_once dirname(__DIR__).'/class/SmartyMain.class.php';
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';
require_once __DIR__.'/line_notify.php';

//Revoke notify
Function revokeNotify($tk) {
    $log = dirname(__DIR__).'/log/line_notify';
    if (!is_dir($log)) {
        mkdir($log, 0777, true);
    }
    $log .= '/notify_revoke_'.date("Ymd").'.log';
    
    $opts = [
        "http" => [
            "method" => "POST",
            "header" => "Authorization: Bearer {$tk}\r\n".
                        "Content-Type: application/x-www-form-urlencoded\r\n",
        ]
    ];

    $context = stream_context_create($opts);
    $json = file_get_contents('https://notify-api.line.me/api/revoke', false, $context);
    
    file_put_contents($log, date("Y-m-d H:i:s").' access token = '.$tk.', json = '.$json."\n", FILE_APPEND);
    
    return json_decode($json, true);
}
##

$sn = $_POST['sn'];
if (empty($sn)) {
    exit('NG1');
}

//
$conn = new first1DB;
$sql  = 'SELECT `lCode`, `lAccessToken` FROM `tLineNotify` WHERE `lCode` = :sn;';
$rs   = $conn->one($sql, ['sn' => $sn]);
##

if ($rs) {
    $res = revokeNotify($rs['lAccessToken']);
    if ($res['status'] == 200) {
        $sql = 'DELETE FROM `tLineNotify` WHERE `lCode` = :id;';
        if ($conn->exeSql($sql, ['id' => $sn])) {
            exit('OK');
        } else {
            exit('NG2');
        }
    } else {
        exit('NG3');
    }
} else {
    exit('NG4');
}
?>