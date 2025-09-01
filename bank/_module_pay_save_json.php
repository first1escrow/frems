<?php
require_once dirname(__DIR__) . '/openadodb.php';
include_once dirname(__DIR__) . '/session_check.php';

$json = $_REQUEST["json"];

$jsdata = json_decode($json);
$_str   = "'" . join("','", $jsdata->datas) . "'";
$sql    = "update tBankTrans set tPayOk='1' where tId in ($_str)";
$conn->Execute($sql);