<?php
require_once '/var/www/html/lib/rc4/crypt.php';
require_once __DIR__.'/adodb5/adodb.inc.php';
require_once __DIR__.'/includes/lib.php';
require_once __DIR__.'/.env.php';

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime',  86400);

$conn108 = ADONewConnection('mysqli');

$conn108->Connect($env['db']['108']['host'], $env['db']['108']['username'], $env['db']['108']['password'], $env['db']['108']['database']) or die("fail to connect adodb 108");
//$conn->debug =1;
$conn108->SetFetchMode(ADODB_FETCH_ASSOC);
$conn108->Execute("SET NAMES 'utf8'");
$conn108->Execute("SET CHARACTER SET utf8");
$conn108->Execute("SET CHARACTER_SET_RESULTS='utf8'");

?>