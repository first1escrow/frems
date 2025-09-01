<?php
require_once __DIR__ . '/adodb5/adodb.inc.php';
require_once __DIR__ . '/includes/lib.php';
require_once __DIR__ . '/.env.php';

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 86400);

$conn22 = ADONewConnection('mysqli');

$conn22->Connect($env['db']['197']['host'], $env['db']['197']['username'], $env['db']['197']['password'], $env['db']['197']['contractDB']) or die("fail to connect adodb 197");
//$conn22->Connect($env['db']['22']['host'], $env['db']['22']['username'], $env['db']['22']['password'], $env['db']['22']['database']) or die("fail to connect adodb 22");

//$conn->debug =1;
$conn22->SetFetchMode(ADODB_FETCH_ASSOC);
$conn22->Execute("SET NAMES 'utf8'");
$conn22->Execute("SET CHARACTER SET utf8");
$conn22->Execute("SET CHARACTER_SET_RESULTS='utf8'");
