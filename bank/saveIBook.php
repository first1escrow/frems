<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../../tracelog.php' ;

$_POST = escapeStr($_POST) ;

$tmp = explode('-', $_POST['Date']);

if ($_POST['Date'] != '000-00-00') {
	$_POST['Date'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
}

if (is_array($_POST['dName'])) {
	for ($i=0; $i < count($_POST['did']); $i++) { 
		// echo $_POST['did'][$i]."_".$_POST['dName'][$i]."_".$_POST['dMoney'][$i]."\r\n" ;
		if ($_POST['did'][$i] =='' && ($_POST['dName'][$i] !='' || $_POST['dMoney'][$i] != '')) {
			$sql = "INSERT INTO 
						tBankTrankBookDetail (
							bTrankBookId,
							bName,
							bMoney,
							bCreatTime
						)VALUES(
							'".$_POST['bId']."',
							'".$_POST['dName'][$i]."',
							'".$_POST['dMoney'][$i]."',
							'".date('Y-m-d H:i:s')."'
						)";
			// echo $sql;
			$conn->Execute($sql);
		}else{
			$sql = "UPDATE
						tBankTrankBookDetail
					SET 
						bName ='".$_POST['dName'][$i]."',
						bMoney ='".$_POST['dMoney'][$i]."'
					WHERE
						bId ='".$_POST['did'][$i]."'";
			// echo $sql;
			$conn->Execute($sql);
					
		}
	}
}else{
	if ($_POST['did'] =='' && ($_POST['dName'] !='' || $_POST['dMoney'] != '')) {
			$sql = "INSERT INTO 
						tBankTrankBookDetail (
							bTrankBookId,
							bTicketNo,
							bMoney,
							bCreatTime
						)VALUES(
							'".$lastId."',
							'".$_POST['ticketNo']."',
							'".$_POST['dMoney']."',
							'".date('Y-m-d H:i:s')."'
						)";
			// echo $sql;
			$conn->Execute($sql);
		}else{
			$sql = "UPDATE
						tBankTrankBookDetail
					SET 
						bTicketNo = '".$_POST['ticketNo']."',
						bName ='".$_POST['dName']."',
						bMoney ='".$_POST['dMoney']."'
					WHERE
						bId ='".$_POST['did']."'";
			// echo $sql;
			$conn->Execute($sql);
					
	}
}


	


if ($_POST['BookId'] != '' && $_POST['Date'] != '000-00-00') {
	$str = "bStatus = '1',";
	$str .="bModifyName2 = '".$_SESSION['member_name']."',
			bModifyDate2 = '".date('Y-m-d H:i:s')."',";
}

$sql = "UPDATE
			tBankTrankBook
		SET 
			bDate ='".$_POST['Date']."',
			bMoney ='".$_POST['money']."',
			bBookId='".$_POST['BookId']."',
			bCount = '".$_POST['count']."',
			".$str."
			bModifyName = '".$_SESSION['member_name']."',
			bModifyDate = '".date('Y-m-d H:i:s')."',
			breName ='".$_POST['reName']."',
			breIdentifyId ='".$_POST['reIdentifyId']."',
			bToCertifiedId = '".$_POST['ToCertified']."',
			ToCertifiedFirst = '".$_POST['ToCertifiedFirst']."',
			bReBank ='".$_POST['reBank']."',
			bItem ='".$item."',
			bODate = '".$_POST['oDate']."',
			bObank = '".$_POST['oBank']."',
			bEaccountName = '".$_POST['EaccountName']."',
			bEaccount = '".$_POST['Eaccount']."',
			bEmoney = '".$_POST['Emoney']."',
			bCaccountName = '".$_POST['CaccountName']."',
			bCaccount = '".$_POST['Caccount']."',
			bCmoney = '".$_POST['Cmoney']."',
			bOther = '".$_POST['Other']."'
		WHERE 
			bId ='".$_POST['bId']."'
		";

if ($conn->Execute($sql)) {
	echo 'OK';
}
$lastId = $_POST['bId'];
##補通訊##
if (is_array($_POST['NEaccountName'])) { //錯誤帳戶新增
	
	for ($i=0; $i < count($_POST['NEaccountName']); $i++) { 
		if ($_POST['NEaccountName'][$i] != '') {
				$sql = "INSERT INTO
					tBankTrankBookDetail
				(
					bTrankBookId,
					bCat,
					bEaccountName,
					bEaccount,
					bEmoney,
					bCreatTime
				)  VALUES(
					'".$lastId."',
					1,
					'".$_POST['NEaccountName'][$i]."',
					'".$_POST['NEaccount'][$i]."',
					'".$_POST['NEmoney'][$i]."',
					'".date("Y-m-d H:i:s")."'
				) ";
			
			$conn->Execute($sql);
		}
		
	}

	
}

if (is_array($_POST['MEaccountName'])) {

	for ($i=0; $i < count($_POST['MEaccountName']); $i++) { 
		$sql = "UPDATE
				tBankTrankBookDetail
			SET
				bEaccountName = '".$_POST['MEaccountName'][$i]."',
				bEaccount  = '".$_POST['MEaccount'][$i]."',
				bEmoney  = '".$_POST['MEmoney'][$i]."'
			WHERE
				bId = '".$_POST['eId'][$i]."'
			";
			
		$conn->Execute($sql);
	}

	
}


if (is_array($_POST['NCaccountName'])) { //正確帳戶新增
	
	for ($i=0; $i < count($_POST['NCaccountName']); $i++) { 
		if ($_POST['NCaccountName'][$i] != '') {
				$sql = "INSERT INTO
					tBankTrankBookDetail
				(
					bTrankBookId,
					bCat,
					bEaccountName,
					bEaccount,
					bEmoney,
					bCreatTime
				)  VALUES(
					'".$lastId."',
					2,
					'".$_POST['NCaccountName'][$i]."',
					'".$_POST['NCaccount'][$i]."',
					'".$_POST['NCmoney'][$i]."',
					'".date("Y-m-d H:i:s")."'
				) ";
			
			$conn->Execute($sql);
		}
	}

	
}
if (is_array($_POST['MCaccountName'])) {

	for ($i=0; $i < count($_POST['MCaccountName']); $i++) { 
		$sql = "UPDATE
				tBankTrankBookDetail
			SET
				bEaccountName = '".$_POST['MCaccountName'][$i]."',
				bEaccount  = '".$_POST['MCaccount'][$i]."',
				bEmoney  = '".$_POST['MCmoney'][$i]."'
			WHERE
				bId = '".$_POST['cId'][$i]."'
			";
			
		$conn->Execute($sql);
	}

	
}

?>