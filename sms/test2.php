<?php
include_once 'sms_function.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

//
$tid = $_REQUEST["tid"];
$send = $_REQUEST["yn"]; // 是否發送

$sql = "SELECT a.tVR_Code,a.tMemo,a.tObjKind,a.tMoney,c.cScrivener,b.cBranchNum FROM tBankTrans AS a INNER JOIN tContractRealestate AS b ON a.tMemo = b.cCertifyId INNER JOIN tContractScrivener AS c ON a.tMemo = c.cCertifiedId WHERE a.tId = $tid";
$rs = $conn->Execute($sql);

//
$testMail = new SMS_Gateway();
//$_t = $testMail->send('010318731','20','223','點交',142);
//$pid,$sid,$bid,$target,$tid,$ok="n"
//保證號,地政士,店,服務,表id,是否發送
if ($send == "") {$send ='n';} 
//$send = 'n' ;
//$_t = $testMail->send(trim($rs->fields["tMemo"]),trim($rs->fields["cScrivener"]),trim($rs->fields["cBranchNum"]),trim($rs->fields["tObjKind"]),$tid,$send);
$_t = $testMail->send(trim($rs->fields["tVR_Code"]),trim($rs->fields["cScrivener"]),trim($rs->fields["cBranchNum"]),trim($rs->fields["tObjKind"]),$tid,$send);
//$_t = $testMail->send('010300728','10','72','income',342);
//echo "<pre>";

echo "<br>";
if (is_array($_t)) {
	for ($i = 0 ; $i < count($_t) ; $i ++) {
		echo $_t[$i]["mName"].$_t[$i]["mMobile"]." / ";	
	}
}
else {
	echo $_t ;
}
//print_r($_t);
?>
