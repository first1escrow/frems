<?php
require_once dirname(dirname(__DIR__)) . '/.env.php';
require_once dirname(dirname(__DIR__)) . '/class/slack.class.php';
require_once dirname(dirname(__DIR__)) . '/libs/SFTP/Net/SFTP.php';

use First1\V1\Notify\Slack;

$slack = Slack::getInstance(
    $env['slack']['token'],
    $env['slack']['channel_token']
);
$channel = 'C08RWMRAVRC'; // Slack channel ID for SFTP errors

if (! function_exists('syncFile')) {
    function syncFile($username, $password, $ip, $port, $remoteFile, $localFile)
    {
        global $slack, $channel;

        define('NET_SFTP_LOGGING', NET_SFTP_LOG_COMPLEX);

        $sftp = new NET_SFTP($ip, $port);
        if (! $sftp->login($username, $password)) {
            $message = "SFTP連線失敗：帳號: {$username}\nIP: {$ip}\nPort: {$port}\n錯誤訊息: " . $sftp->getLastSFTPError();
            $slack->chatPostMessage($message, $channel);

            exit($message);
        }

        $path     = dirname($remoteFile);
        $filename = basename($remoteFile);

        $message = 'SFTP 同步檔案！(path: ' . $path . '、 filename: ' . $filename . ')';
        $slack->chatPostMessage($message, $channel);

        $sftp->mkdir($path);
        $sftp->chdir($path);

        return $sftp->put($filename, $localFile, NET_SFTP_LOCAL_FILE);
    }
}

$message = 'SFTP 同步檔案！(' . print_r($argv, true) . ')';
$slack->chatPostMessage($message, $channel);

$attachmentFile = isset($argv[1]) ? base64_decode($argv[1]) : null;
if (empty($attachmentFile) || ! file_exists($attachmentFile)) {
    $message = '查無 SFTP 同步檔案！(' . print_r($argv, true) . ')';
    $message .= "\n請確認附件檔案是否存在於指定路徑：{$attachmentFile}";

    // 發送 Slack 通知
    $slack->chatPostMessage($message, $channel);

    exit($message);
}

$sftp_setting = $env['sftp'];

foreach ($sftp_setting as $key => $value) {
    if ($key != SERVERIP) {
        syncFile($value['account'], $value['password'], $key, $value['port'], $attachmentFile, $attachmentFile);
    }
}
