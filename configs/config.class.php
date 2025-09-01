<?php
require_once dirname(__DIR__) . '/.env.php';

define('FIRST198', '/home/first198/firstschedule');

//$GLOBALS['DOMAIN']  = 'first.twhg.com.tw';
$GLOBALS['DOMAIN']    = 'first.nhg.tw';
$GLOBALS['FILE_PATH'] = dirname(__DIR__) . "/";

$GLOBALS['WEB_STAGE']     = "https://www.first1.com.tw/";
$GLOBALS['WEB_STAGE_SSL'] = "https://escrow.first1.com.tw/";

ini_set('include_path', ini_get('include_path') . ':' . $GLOBALS['FILE_PATH']);

/**/
//log path
$GLOBALS['LOG_PATH']         = '/var/www/html/first.twhg.com.tw/log/';
$GLOBALS['FILE_PATH_UPLOAD'] = '/var/www/html/escrow.first1.com.tw/upload';
$GLOBALS['web_upload']       = '/var/www/html/www.first1.com.tw';
$GLOBALS['webssl_upload']    = '/var/www/html/escrow.first1.com.tw';

/* ESCROW Datable */
$GLOBALS['DB_ESCROW_USER']     = $env['db']['197']['username'];
$GLOBALS['DB_ESCROW_PASSWORD'] = $env['db']['197']['password'];
$GLOBALS['DB_ESCROW_NAME']     = $env['db']['197']['database'];
$GLOBALS['DB_ESCROW_LOCATION'] = $env['db']['197']['host'];
$GLOBALS['DB_ESCROW_PORT']     = $env['db']['197']['port'];

// 確保 session 正確啟動
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
