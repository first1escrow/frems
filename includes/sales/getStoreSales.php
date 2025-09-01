<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

//
$store = $_POST['store'];
if (!preg_match("/^[A-Z]{2}\d{4,5}$/", $store)) {
    exit;
}
##

//
$code = substr($store, 0, 2);
$id   = (int) substr($store, 2);
##

$conn = new first1DB;

//
if ($code == 'SC') {
    $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId = a.sSales) as sales FROM tScrivenerSalesForPerformance AS a WHERE sScrivener = :id;';
} else {
    $sql = 'SELECT (SELECT pName FROM tPeopleInfo WHERE pId = a.bSales) as sales FROM tBranchSalesForPerformance AS a WHERE bBranch = :id;';
}

$rs = $conn->one($sql, ['id' => $id]);

exit(empty($rs) ? '' : $rs['sales']);
