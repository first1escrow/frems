<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php';

$sql = "SELECT cEndDate,cCertifiedId,cr.cBranchNum,cc.cFeedbackTarget FROM tContractCase AS cc LEFT JOIN tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId WHERE cr.cBranchNum = 505 AND cc.cFeedbackTarget = 1 ORDER BY cEndDate DESC";
$rs= $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {

	$sql_update  = "UPDATE tContractCase SET cFeedbackTarget = 2 WHERE cCertifiedId = '".$rs->fields['cCertifiedId']."';";

	echo $sql_update."<br>";

	$i++;
	$rs->MoveNext();
}

echo $i;
?>