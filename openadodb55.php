<?php
require_once '/var/www/html/lib/rc4/crypt.php';
require_once __DIR__.'/adodb5/adodb.inc.php';
require_once __DIR__.'/includes/lib.php';
require_once __DIR__.'/.env.php';

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime',  86400);

$conn55 = ADONewConnection('mysqli');

$conn55->Connect($env['db']['55']['host'], $env['db']['55']['username'], $env['db']['55']['password'], $env['db']['55']['database']) or die("fail to connect adodb 55");
//$conn55->debug =1;
$conn55->SetFetchMode(ADODB_FETCH_ASSOC);
$conn55->Execute("SET NAMES 'utf8'");
// $conn55->Execute("SET CHARACTER SET utf8");
$conn55->Execute("SET CHARACTER_SET_CLIENT=utf8");
$conn55->Execute("SET CHARACTER_SET_RESULTS='utf8'");

$rc = new crypt() ;

?>