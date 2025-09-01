<?php
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$sid  = trim($_POST['sid']);
$bank = trim($_POST['bank']);

$sql = "SELECT * FROM tScrivener WHERE sId='" . $sid . "'";

$rs = $conn->Execute($sql);

$options = '';

$sql = "SELECT cBankFullName,cId,cBranchFullName FROM tContractBank WHERE cShow = 1 AND cId IN (" . $rs->fields['sBank'] . ")";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $options .= "<option value='" . $rs->fields['cId'] . "'>" . $rs->fields['cBankFullName'] . $rs->fields['cBranchFullName'] . "</option>";
    $rs->MoveNext();
}

echo $options;
die;
$conn->clode();
