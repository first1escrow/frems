<?php
require_once dirname(__DIR__) . '/.env.php';
require_once __DIR__ . '/vendor/autoload.php';

$pusher = new Pusher\Pusher($env['pusher']['key'], $env['pusher']['secret'], $env['pusher']['app_id'], ['cluster' => $env['pusher']['cluster']]);

$cnt = 2;
$pusher->trigger('first1-feedback-sms', 'sms-shorturl', '已轉換數量：' . number_format($cnt));
// $pusher->trigger('first1-feedback-sms', 'sms-shorturl', '');
