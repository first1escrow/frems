<?php
require_once dirname(__DIR__) . '/.env.php';
require_once __DIR__ . '/vendor/autoload.php';

function trigger($channel, $event, $message)
{
    global $env;

    if (empty($channel) || empty($event) || empty($message)) {
        return false;
    }

    $pusher = new Pusher\Pusher($env['pusher']['key'], $env['pusher']['secret'], $env['pusher']['app_id'], ['cluster' => $env['pusher']['cluster']]);
    return $pusher->trigger($channel, $event, $message);
}
