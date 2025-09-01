<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['total'] > 0) {
	
	for ($i=0; $i < count($_POST['mId']); $i++) { 
		
		$sql = "UPDATE 
					tPeopleCaseEndError
				SET 
					`pId` = '".$_POST['pId'][$i]."',
					`pMid` = '".$_POST['mId'][$i]."',
					`pInfo` = '".$_POST['Info'][$i]."',
					`pInfoCertifiedId` = '".$_POST['InfoCid'][$i]."',
					`pInfoMsg` = '".$_POST['InfoMsg'][$i]."',
					`pCMoney` = '".$_POST['CMoney'][$i]."',
					`pCMoneyCertifiedId` = '".$_POST['CMoneyCid'][$i]."',
					`pCMoneyMsg` = '".$_POST['CMoneyMsg'][$i]."',
					`pInt` = '".$_POST['pInt'][$i]."',
					`pIntCertifiedId` = '".$_POST['pIntCid'][$i]."',
					`pIntMsg` = '".$_POST['pIntMsg'][$i]."',
					`pInv` = '".$_POST['pInv'][$i]."',
					`pInvCertifiedId` = '".$_POST['pInvCid'][$i]."',
					`pInvMsg` = '".$_POST['pInvMsg'][$i]."',
					`pEditor` = '".$_SESSION['member_id']."'
				WHERE
					pId = '".$_POST['pId'][$i]."'
				";
		$conn->Execute($sql);
		 // echo $sql."<br>";
	}
}else{

	for ($i=0; $i < count($_POST['mId']); $i++) { 

		$sql = "INSERT INTO
				`tPeopleCaseEndError` (
					`pMid`,
					`pDate`,
					`pInfo`,
					`pInfoCertifiedId`,
					`pInfoMsg`,
					`pCMoney`,
					`pCMoneyCertifiedId`,
					`pCMoneyMsg`,
					`pInt`,
					`pIntCertifiedId`,
					`pIntMsg`,
					`pInv`,
					`pInvCertifiedId`,
					`pInvMsg`,
					`pCreator`,
					`pCreatTime`,
					`pEditor`
				) VALUES(
					'".$_POST['mId'][$i]."',
					'".$search_date."',
					'".$_POST['Info'][$i]."',
					'".$_POST['InfoCid'][$i]."',
					'".$_POST['InfoMsg'][$i]."',
					'".$_POST['CMoney'][$i]."',
					'".$_POST['CMoneyCid'][$i]."',
					'".$_POST['CMoneyMsg'][$i]."',
					'".$_POST['pInt'][$i]."',
					'".$_POST['pIntCid'][$i]."',
					'".$_POST['pIntMsg'][$i]."',
					'".$_POST['pInv'][$i]."',
					'".$_POST['pInvCid'][$i]."',
					'".$_POST['pInvMsg'][$i]."',
					'".$_SESSION['member_id']."',
					'".date('Y-m-d H:i:s')."',
					'".$_SESSION['member_id']."'
				);";


		
		
		$conn->Execute($sql);
		// echo $sql."<br>";
	}

	
}

// $sql = "SELECT pId FROM tPeopleBanktransError WHERE pDate = '".$search_date."'";

// $rs = $conn->Execute($sql);

// $total=$rs->RecordCount();

// if ($total > 0) {//update
// 	# code...
// }
?>