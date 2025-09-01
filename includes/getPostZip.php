<?php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/class/traits/zips.traits.php';

$city     = $_POST['city'];
$district = $_POST['district'];

if (!in_array($city, Zips::getCity())) {
    exit(json_encode([
        'status'  => 400,
        'message' => 'City incorrect!!',
    ], JSON_UNESCAPED_UNICODE));
}

$_district = array_values(Zips::getDistrict($city));
if (!in_array($district, $_district)) {
    exit(json_encode([
        'status'  => 400,
        'message' => 'District incorrect!!',
    ], JSON_UNESCAPED_UNICODE));
}
$_district = null;unset($_district);

exit(json_encode([
    'status'  => 200,
    'message' => 'OK',
    'data'    => Zips::getZipByName($city, $distrct),
], JSON_UNESCAPED_UNICODE));
