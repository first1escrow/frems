<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/class/traits/zips.traits.php';

exit(json_encode([
    'status'  => 200,
    'message' => 'OK',
    'data'    => Zips::getCity(),
], JSON_UNESCAPED_UNICODE));
