<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';

$certifiedId  = $_REQUEST['certifiedId'];

$conn = new first1DB;

$sql = 'DELETE FROM `tUploadFile` WHERE tCertifiedId = ' . $certifiedId;
$rs = $conn->exeSql($sql);

if($rs) {
    echo 200;
} else {
    echo 400;
}


