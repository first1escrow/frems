<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../../tracelog.php' ;

$_POST = escapeStr($_POST) ;

$tmp = explode('-', $_POST['date']);

$_POST['date'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];


	for ($i=0; $i < count($_POST['did']); $i++) { 
		// echo $_POST['did'][$i]."_".$_POST['dName'][$i]."_".$_POST['dMoney'][$i]."\r\n" ;
		if ($_POST['did'][$i] =='' && ($_POST['dName'][$i] !='' || $_POST['dMoney'][$i] != '')) {
			$sql = "INSERT INTO 
						tBankTrankBookDetail (
							bTrankBookId,
							bName,
							bMoney,
							bStop,
							bCreatTime
						)VALUES(
							'".$_POST['bId']."',
							'".$_POST['dName'][$i]."',
							'".$_POST['dMoney'][$i]."',
							'".$_POST['dStop'][$i]."',
							'".date('Y-m-d H:i:s')."'
						)";
			// echo $sql;
			$conn->Execute($sql);
		}else{
			$sql = "UPDATE
						tBankTrankBookDetail
					SET 
						bName ='".$_POST['dName'][$i]."',
						bMoney ='".$_POST['dMoney'][$i]."',
						bStop = '".$_POST['dStop'][$i]."'
					WHERE
						bId ='".$_POST['did'][$i]."'";
			// echo $sql;
			$conn->Execute($sql);
					
		}
	}


$sql = "UPDATE
			tBankTrankBook
		SET 
			breName ='".$_POST['reName']."',
			breIdentifyId ='".$_POST['reIdentifyId']."',
			bToCertifiedId = '".$_POST['ToCertified']."',
			ToCertifiedFirst = '".$_POST['ToCertifiedFirst']."',
			bModifyName ='".$_SESSION['member_name']."',
			bModifyDate ='".date('Y-m-d H:i:s')."',
			bReBank ='".$_POST['reBank']."',
			bCount ='".$_POST['count']."',
			bMoney = '".$_POST['money']."',
			bContractID = '".$_POST['ContractId']."',
			bSpNote1 = '".$_POST['SpNote1']."',
			bSpNote2 = '".$_POST['SpNote2']."'
		WHERE 
			bId ='".$_POST['bId']."'
		";
// echo $sql;
if ($conn->Execute($sql)) {
	echo 'OK';
}

?>