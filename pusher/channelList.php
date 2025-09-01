<?php
require_once dirname(__DIR__) . '/.env.php';
require_once __DIR__ . '/vendor/autoload.php';

$pusher   = new Pusher\Pusher($env['pusher']['key'], $env['pusher']['secret'], $env['pusher']['app_id'], ['cluster' => $env['pusher']['cluster']]);
$response = $pusher->get('/channels');

if ($response['status'] == 200) {
    $channels    = $response['result']['channels'];
    $channelList = [];
    foreach ($channels as $channel => $data) {
        $channelList[] = $channel;
    }
    sort($channelList);
    print_r($channelList);
} else {
    print_r($response);
    exit('Error');
}
