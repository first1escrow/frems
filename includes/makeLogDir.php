<?php

//Log
$_dir = dirname(__DIR__) . '/log/sms/log2';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/log/backstage/' . date("Ym");
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//log2
$_dir = dirname(__DIR__) . '/log2/line';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/log2/otherFeed';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/log2/out2';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/log2/sms_error';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/log2/smscheck';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/log2/twhg';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//accounting
$_dir = dirname(__DIR__) . '/accounting/log';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//actives
$_dir = dirname(__DIR__) . '/actives/excel';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//appHandler
$_dir = dirname(__DIR__) . '/appHandler/data';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//bank
$_dir = dirname(__DIR__) . '/bank/output';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/bank/report/.trLog';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/bank/report/case_close';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/bank/report/excel';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/bank/report/log';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//card
$_dir = dirname(__DIR__) . '/card/log';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//checklist
$_dir = dirname(__DIR__) . '/checklist/log';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//contractFile
$_dir = dirname(__DIR__) . '/contractFile';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//includes/log
$_dir = dirname(__DIR__) . '/includes/log';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//inquire/.trLog
$_dir = dirname(__DIR__) . '/inquire/.trLog';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//public
$_dir = dirname(__DIR__) . '/public';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//sms
$_dir = dirname(__DIR__) . '/sms/log2';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/sms/log';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//templates_c
$_dir = dirname(__DIR__) . '/templates_c';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

//test2
$_dir = dirname(__DIR__) . '/test2/log';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}

$_dir = dirname(__DIR__) . '/test2/report/txt';
if (!is_dir($_dir)) {
    mkdir($_dir, 0777, true);
}
