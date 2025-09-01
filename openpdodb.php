<?php
require_once dirname(__DIR__) . '/lib/rc4/crypt.php';
require_once __DIR__ . '/includes/lib.php';
require_once __DIR__ . '/.env.php';

$dsn = 'mysql:host=' . $env['db']['197']['host'] . ';port=' . $env['db']['197']['port'] . ';dbname=' . $env['db']['197']['database'];

try {
    $pdolink = new PDO($dsn, $env['db']['197']['username'], $env['db']['197']['password'], [PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\'']);
    $pdolink->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
} catch (PDOException $e) {
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}

$rc = new crypt();
