<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['total'] > 0) {
	
	for ($i=0; $i < count($_POST['mId']); $i++) { 
		
		$sql = "UPDATE 
					tPeopleBanktransError
				SET 
					pBasic ='".$_POST['basic'][$i]."',
					pBasicMsg ='".$_POST['basic_msg'][$i]."',
					pBanktran ='".$_POST['banktran'][$i]."',
					pBanktranMsg ='".$_POST['banktran_msg'][$i]."',
					pMoney ='".$_POST['money'][$i]."',
					pMoneyMsg ='".$_POST['money_msg'][$i]."',
					pBankBranch ='".$_POST['bankBranch'][$i]."',
					pBankBranchMsg ='".$_POST['bankBranch_msg'][$i]."',
					pTxt ='".$_POST['txt'][$i]."',
					pTxtMsg ='".$_POST['txt_msg'][$i]."',
					pAccount ='".$_POST['account'][$i]."',
					pAccountMsg ='".$_POST['account_msg'][$i]."',
					pAccountName ='".$_POST['accountName'][$i]."',
					pAccountNameMsg ='".$_POST['accountName_msg'][$i]."',
					pOther ='".$_POST['other'][$i]."',
					pOtherMsg ='".$_POST['other_msg'][$i]."',
					pEnd ='".$_POST['end'][$i]."',
					pEndMsg ='".$_POST['end_msg'][$i]."',
					pEndTotal = '".$_POST['end_total'][$i]."',
					pSp_msg ='".$_POST['sp_msg'][$i]."'
				WHERE
					pId = '".$_POST['pId'][$i]."'
				";
		$conn->Execute($sql);
		 // echo $sql."<br>";
	}
}else{

	for ($i=0; $i < count($_POST['mId']); $i++) { 
		$sql = "INSERT INTO 
				tPeopleBanktransError (
					pMid,
					pDate,
					pBasic,
					pBasicMsg,
					pBanktran,
					pBanktranMsg,
					pMoney,
					pMoneyMsg,
					pBankBranch,
					pBankBranchMsg,
					pTxt,
					pTxtMsg,
					pAccount,
					pAccountMsg,
					pAccountName,
					pAccountNameMsg,
					pOther,
					pOtherMsg,
					pEnd,
					pEndMsg,
					pEndTotal,
					pSp_msg,
					pCreatTime
			) VALUES (
				'".$_POST['mId'][$i]."',
				'".$search_date."',
				'".$_POST['basic'][$i]."',
				'".$_POST['basic_msg'][$i]."',
				'".$_POST['banktran'][$i]."',
				'".$_POST['banktran_msg'][$i]."',
				'".$_POST['money'][$i]."',
				'".$_POST['money_msg'][$i]."',
				'".$_POST['bankBranch'][$i]."',
				'".$_POST['bankBranch_msg'][$i]."',
				'".$_POST['txt'][$i]."',
				'".$_POST['txt_msg'][$i]."',
				'".$_POST['account'][$i]."',
				'".$_POST['account_msg'][$i]."',
				'".$_POST['accountName'][$i]."',
				'".$_POST['accountName_msg'][$i]."',
				'".$_POST['other'][$i]."',
				'".$_POST['other_msg'][$i]."',
				'".$_POST['end'][$i]."',
				'".$_POST['end_msg'][$i]."',
				'".$_POST['end_total'][$i]."',
				'".$_POST['sp_msg'][$i]."',
				'".date('Y-m-d H:i:s')."'
			)";
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