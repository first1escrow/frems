<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/class/traits/zips.traits.php';

$city = $_POST['city'];

if (!in_array($city, Zips::getCity())) {
    exit(json_encode([
        'status'  => 400,
        'message' => 'City incorrect!!',
    ], JSON_UNESCAPED_UNICODE));
}

exit(json_encode([
    'status'  => 200,
    'message' => 'OK',
    'data'    => Zips::getDistrict($city),
], JSON_UNESCAPED_UNICODE));
