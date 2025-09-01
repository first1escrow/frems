<?php
require_once __DIR__ . '/trigger.php';

$event = 'first1-notify';

$message = [
    'alert' => 'Hello World',
];

trigger($event . '-6', $event, json_encode($message));
