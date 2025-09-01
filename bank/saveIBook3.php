<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../../tracelog.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['Date'] != '000-00-00' && $_POST['Date'] != '') {
	$tmp = explode('-', $_POST['oDate']);

	$_POST['Date'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
}

if ($_POST['oDate'] != '000-00-00' && $_POST['oDate'] != '') {
	$tmp = explode('-', $_POST['oDate']);

	$_POST['oDate'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
}

	
$item = 0;

for ($i=0; $i < count($_POST['item']); $i++) { 
	# code...
	$item = $item+$_POST['item'][$i];
}

//
if ($_POST['bank'] == 1) {
	$code = '60001';
}elseif ($_POST['bank'] == 4) {
	$code = '99985';
}elseif ($_POST['bank'] == 6) {
	$code = '99986';
}
$_POST['CertifiedId'] = $code.$_POST['CertifiedId'];

$_POST['oBank'] = $_POST['oBank'].$_POST['oBank2'];

if ($_POST['type'] == 'add') {
	$sql = "INSERT INTO
				tBankTrankBook 
			(
				bCertifiedId,
				bBank,
				bCategory,
				bMoney,
				breName,
				breIdentifyId,
				bReBank,
				bItem,
				bODate,
				bObank,
				bEaccountName,
				bEaccount,
				bEmoney,
				bCaccountName,
				bCaccount,
				bCmoney,
				bOther,
				bCreatTime,
				bCreatName,
				bCreatorId
			)VALUES(
				'".$_POST['CertifiedId']."',
				'".$_POST['bank']."',
				'".$_POST['Category']."',
				'".$_POST['money']."',
				'".$_POST['reName']."',
				'".$_POST['reIdentifyId']."',
				'".$_POST['reBank']."',
				'".$item."',
				'".$_POST['oDate']."',
				'".$_POST['oBank']."',
				'".$_POST['EaccountName']."',
				'".$_POST['Eaccount']."',
				'".$_POST['Emoney']."',
				'".$_POST['CaccountName']."',
				'".$_POST['Caccount']."',
				'".$_POST['Cmoney']."',
				'".$_POST['Other']."',
				'".date('Y-m-d H:i:s')."',
				'".$_SESSION['member_name']."',
				'".$_SESSION['member_id']."'
			)";

	// echo $sql;
	
	if ($conn->Execute($sql)) {
		$lastId = $conn->Insert_ID();
		echo $lastId;
	}

	 
}else{

	

	$sql = "UPDATE
			tBankTrankBook
		SET 
			bCertifiedId = '".$_POST['CertifiedId']."',
			bBank = '".$_POST['bank']."',
			bCategory = '".$_POST['Category']."',
			bMoney = '".$_POST['money']."',
			breName ='".$_POST['reName']."',
			breIdentifyId ='".$_POST['reIdentifyId']."',
			bModifyName ='".$_SESSION['member_name']."',
			bModifyDate ='".date('Y-m-d H:i:s')."',
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
}




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