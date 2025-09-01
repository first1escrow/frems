<?php
require_once __DIR__ . '/.env.php';
require_once __DIR__ . '/class/slack.class.php';

use First1\V1\Notify\Slack;

function lineNotify($msg)
{
    global $env;

    //20241009: 因應 line notify 關閉，改用 slack 通知
    Slack::channelSend($msg);

    $key = $env['line_notify']['token'];

    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $key,
    ];

    $message = [
        'message' => $msg,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://notify-api.line.me/api/notify");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($message));
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// set_error_handler(function($errno, $errostr, $file, $line) {
// lineNotify($errostr . " in {$file}:{$line}");
// exit('error End');
// });
