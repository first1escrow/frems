<?php
//require_once dirname(__DIR__).'/openadodb.php';
include_once '../openadodb.php' ;

$sql = 'SELECT * FROM tFeedBackMoneyReview WHERE fApplyTime >= "2022-05-01 00:00:00" and fApplyTime <= "2022-05-31 23:59:59";';
$conn->Execute($sql);

$list = array();
while (!$rs->EOF) {
    $list[] = $rs->fields;

    $rs->MoveNext();
}

print_r($list);
