<?php
include_once '../../openadodb.php';
include_once '../../session_check.php' ;
 // $_POST = escapeStr($_POST) ;

 // $sql = "DELETE FROM tFeedBackData WHERE fId = '".$_POST['no']."' ";




 $sql = "UPDATE tFeedBackData SET fStatus = 1 WHERE fId = '".$_POST['no']."'";

 if ($conn->Execute($sql)) {

 	$sql = "SELECT * FROM tFeedBackData WHERE fStoreId = '".$_POST['bId']."' AND fStatus = 0 AND fType = 2";

 	$rs = $conn->Execute($sql);

 	$total=$rs->RecordCount();
 	
 	if ($total == 0) {
 		$sql = "UPDATE tBranch SET bCooperationHas = 0 WHERE bId ='".$_POST['bId']."'";
 		$conn->Execute($sql);
 	}else{
 		$ck = 0;

 		while (!$rs->EOF) {
 			if ($rs->fields['fAccountName']) {
 				$ck = 1;
 			}

 			$rs->MoveNext();
 		}

 		if ($ck == 0) {
 			$sql = "UPDATE tBranch SET bCooperationHas = 0 WHERE bId ='".$_POST['bId']."'";
 			$conn->Execute($sql);
 		}
 		
 	}

 	echo 'OK';
 }
 




?>