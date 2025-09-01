<?php
require_once dirname(__DIR__) . '/lib/rc4/crypt.php';
require_once __DIR__ . '/includes/lib.php';
require_once __DIR__ . '/.env.php';

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 86400);

$link = @mysql_connect($env['db']['197']['host'], $env['db']['197']['username'], $env['db']['197']['password']);

mysql_select_db($env['db']['197']['database']);
mysql_query('SET NAMES utf8');
mysql_query('SET CHARACTER_SET_CLIENT=utf8');
mysql_query('SET CHARACTER_SET_RESULTS=utf8');

$rc = new crypt();