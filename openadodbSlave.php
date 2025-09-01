<?php
require_once dirname(__DIR__) . '/lib/rc4/crypt.php';
require_once __DIR__ . '/includes/lib.php';
require_once __DIR__ . '/adodb5/adodb.inc.php';
require_once __DIR__ . '/.env.php';

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 86400);

$conn = ADONewConnection('mysqli');

$conn->port = $env['db']['197']['port'];
$conn->Connect($env['db']['197']['host'], $env['db']['197']['username'], $env['db']['197']['password'], $env['db']['197']['database']) or die("fail to connect adodb 197");
//$conn->debug =1;

$conn->SetFetchMode(ADODB_FETCH_ASSOC);
$conn->Execute("SET NAMES 'utf8'");
// $conn->Execute("SET CHARACTER SET utf8");
$conn->Execute("SET CHARACTER_SET_CLIENT=utf8");
$conn->Execute("SET CHARACTER_SET_RESULTS='utf8'");
// $conn->Execute('SET GLOBAL interactive_timeout=120');
// $conn->Execute('SET GLOBAL wait_timeout=120');

// $rc = new crypt();
