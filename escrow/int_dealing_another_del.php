<?php
include_once '../openadodb.php' ;

$id = addslashes($_POST['id']);
$cid = addslashes($_POST['cCertifiedId']);
$cCertifiedId = substr($cid,5) ;
$cSignCategory = addslashes($_POST['cSignCategory']);




$sql = "SELECT * FROM tContractInterestExt  WHERE cId='".$id."'";

$rs= $conn->Execute($sql);

if ($rs->fields['cDBName'] == 'tContractBuyer' || $rs->fields['cDBName'] == 'tContractOthersB') {

	$sql = "UPDATE tContractBuyer SET cInvoiceMoney = 0,cInvoiceDonate=0  WHERE cId = '".$rs->fields['cTBId']."' AND cCertifiedId = '".$cCertifiedId."'";
	 // echo $sql."<br>";
	$conn->Execute($sql);

	$sql = "UPDATE tContractOthers SET cInvoiceMoney = 0,cInvoiceDonate=0  WHERE  cCertifiedId = '".$cCertifiedId."' AND cIdentity=1";
		 //$conn->Execute($sql);
	$conn->Execute($sql);

	$sql = "UPDATE tContractInterestExt SET cInvoiceMoney = 0,cInvoiceDonate=0 WHERE (cDBName='tContractBuyer' OR cDBName ='tContractOthersB') AND cCertifiedId = '".$cCertifiedId."'";

	
	$conn->Execute($sql);


}elseif ($rs->fields['cDBName'] == 'tContractOwner' || $rs->fields['cDBName'] == 'tContractOthersO') {

	$sql = "UPDATE tContractOwner SET cInvoiceMoney = 0,cInvoiceDonate=0  WHERE cId = '".$rs->fields['cTBId']."' AND cCertifiedId = '".$cCertifiedId."'";
	 // echo $sql."<br>";
	$conn->Execute($sql);

	$sql = "UPDATE tContractOthers SET cInvoiceMoney = 0,cInvoiceDonate=0  WHERE  cCertifiedId = '".$cCertifiedId."' AND cIdentity=2";
	$conn->Execute($sql);

	$sql = "UPDATE tContractInterestExt SET cInvoiceMoney = 0,cInvoiceDonate=0 WHERE (cDBName='tContractOwner' OR cDBName ='tContractOthersO') AND cCertifiedId = '".$cCertifiedId."'";

	
	$conn->Execute($sql);
	 // echo $sql."<br>";
	 // echo $sql."<br>";
}elseif ($rs->fields['cDBName'] == 'tContractRealestate' || $rs->fields['cDBName'] == 'tContractRealestate1' || $rs->fields['cDBName'] == 'tContractRealestate2') {
	
	$sql = "UPDATE tContractRealestate SET cInvoiceMoney = 0 ,cInvoiceMoney1 = 0,cInvoiceMoney2 = 0,cInvoiceDonate=0,cInvoiceDonate1=0,cInvoiceDonate2=0 WHERE cId = '".$rs->fields['cTBId']."' AND cCertifiedId = '".$cCertifiedId."'";

	$conn->Execute($sql);


	$sql = "UPDATE tContractInterestExt SET cInvoiceMoney = 0,cInvoiceDonate=0 WHERE (cDBName='tContractRealestate' OR cDBName ='tContractRealestate1' OR cDBName ='tContractRealestate2') AND cCertifiedId = '".$cCertifiedId."'";

	
	$conn->Execute($sql);

}elseif ($rs->fields['cDBName'] == 'tContractScrivener') {
	
	$sql = "UPDATE tContractScrivener SET cInvoiceMoney = 0,cInvoiceDonate=0  WHERE cId = '".$rs->fields['cTBId']."' AND cCertifiedId = '".$cCertifiedId."'";
	$conn->Execute($sql);

	$sql = "UPDATE tContractInterestExt SET cInvoiceMoney = 0,cInvoiceDonate=0 WHERE cDBName='tContractScrivener'  AND cCertifiedId = '".$cCertifiedId."'";
	$conn->Execute($sql);
	 // echo $sql."<br>";
}







$sql = "DELETE FROM tContractInterestExt  WHERE cId='".$id."'";

 // echo $sql;
$conn->Execute($sql);

$sql = "UPDATE tContractCase SET `cLastEditor` =  '".$_SESSION['member_id']."', `cLastTime` =  now() WHERE cCertifiedId ='".$cCertifiedId."'";
	// echo $sql;
	$conn->Execute($sql);

header("location:int_dealing.php?cCertifiedId=".$cid."&cSignCategory=".$cSignCategory."");



?>