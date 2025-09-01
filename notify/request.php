<?php
require_once dirname(__DIR__).'/configs/config.class.php';
require_once dirname(__DIR__).'/class/SmartyMain.class.php';
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';
require_once __DIR__.'/line_notify.php';

//Notify token status
Function getNotifyStatus($tk) {
    $log = dirname(__DIR__).'/log/line_notify';
    if (!is_dir($log)) {
        mkdir($log, 0777, true);
    }
    $log .= '/notify_status_'.date("Ymd").'.log';
    
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "Authorization: Bearer {$tk}\r\n",
        ]
    ];

    $context = stream_context_create($opts);
    $json    = file_get_contents('https://notify-api.line.me/api/status', false, $context);
    
    file_put_contents($log, date("Y-m-d H:i:s").' access token = '.$tk.', json = '.$json."\n", FILE_APPEND);
    
    return json_decode($json, true) ;
}
##

$code  = $_GET['code'];
$state = $_GET['state'];

$response = '';
$alert    = '';

if (!empty($code) && !empty($state)) {
    //取得 accesss token
    $url      = 'https://notify-bot.line.me/oauth/token';

    $postdata = http_build_query(
        array(
             "grant_type"    => "authorization_code",
             "code"          => $code,
             "redirect_uri"  => $line_notify['redirect_url'],
             "client_id"     => $line_notify['client_id'],
             "client_secret" => $line_notify['client_secret'],
        )
    );

    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded",
            'content' => $postdata
        )
    );
    
    $context = stream_context_create($opts);
    $json    = file_get_contents($url, false, $context);
    ##
    
    //紀錄 access token 資訊
    $arr = json_decode($json, true);

    if (($arr['status'] == 200) && !empty($arr['access_token'])) {
        $conn = new first1DB;
        
        $sql = '
            INSERT INTO 
                `tLineNotify`
            SET
                `lCode`         = :code,
                `lStaffId`      = :staffid,
                `lDescription`  = :desc,
                `lClientId`     = :cid,
                `lClientSecret` = :cse,
                `lAccessToken`  = :tk,
                `lCreatedAt`    = :dt
        ;';

        if ($conn->exeSql($sql, [
                'code'    => uniqid(true),
                'staffid' => $_SESSION['member_id'],
                'desc'    => $state,
                'cid'     => $line_notify['client_id'],
                'cse'     => $line_notify['client_secret'],
                'tk'      => $arr['access_token'],
                'dt'      => date("Y-m-d H:i:s")])) {
            
            $gp = getNotifyStatus($arr['access_token']);

            if ($gp['status'] == 200) {
                $sql = 'UPDATE `tLineNotify` SET `lNotifyTargetType` = :tt, `lNotifyTarget` = :t WHERE `lAccessToken` = :tk;';
                $conn->exeSql($sql, [
                    'tt' => $gp['targetType'],
                    't'  => $gp['target'],
                    'tk' => $arr['access_token'],
                ]);
            }

            //註冊綁定成功
            header('Location: notify.php');
        } else {
            //註冊綁定失敗
            header('Location: notify.php?e=1');
        }
    } else {
        //Line 授權失敗
        header('Location: notify.php?e=2');
    }
    ##
    
} else {
    //請重新註冊操作
    header('Location: notify.php?e=3');
}
?>
