<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';

$paybycase = new First1\V1\PayByCase\PayByCase;

$rs = $paybycase->salesConfirmList($_POST['cid']);

if(false === $rs) {
    echo '重算失敗';
}

if(true === $rs) {
    echo '請確認是否有回饋給地政士';
}

if(null === $rs) {
    echo '已重算';
}










