<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/.env.php';
require_once dirname(__DIR__) . '/pusher/vendor/autoload.php';
exit('N');
$pusher   = new Pusher\Pusher($env['pusher']['key'], $env['pusher']['secret'], $env['pusher']['app_id'], ['cluster' => $env['pusher']['cluster']]);
$response = $pusher->get('/channels');

if ($response['status'] == 200) {
    $channels    = $response['result']['channels'];
    $channelList = [];
    foreach ($channels as $channel => $data) {
        $channelList[] = $channel;
    }

    $myChannel = 'first1-notify-' . $_SESSION['member_id'];
    if (!in_array($myChannel, $channelList)) {
        exit('N');
    }

    exit('Y');
}

exit('N');