<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$cId = $_POST['cId'];

if (!preg_match("/^\d{9}$/", $cId)) {
    exit('INVALID ID FORMAT');
}

$conn = new first1DB;

// $sql = 'SELECT tId FROM tUploadFile WHERE tCertifiedId = :cId;';
$sql = 'SELECT a.tId FROM tUploadFile AS a JOIN tFeedBackMoneyPayByCase AS b ON a.tCertifiedId = b.fCertifiedId WHERE a.tCertifiedId = :cId;';
$rs  = $conn->one($sql, ['cId' => $cId]);

if (empty($rs['tId'])) {
    exit('OK');
}

exit('CASE PUBLISHED');