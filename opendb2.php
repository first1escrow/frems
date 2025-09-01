<?php
require_once __DIR__ . '/includes/lib.php';
require_once __DIR__ . '/.env.php';

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 86400);

$link = mysqli_connect($env['db']['197']['host'], $env['db']['197']['username'], $env['db']['197']['password'], $env['db']['197']['database'], $env['db']['197']['port']);

if (! $link) {
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    exit;
}

mysqli_query($link, 'SET NAMES utf8');
mysqli_query($link, 'SET CHARACTER_SET_CLIENT=utf8');
mysqli_query($link, 'SET CHARACTER_SET_RESULTS=utf8');
