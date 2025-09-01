<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../tracelog.php' ;
include_once 'class/getAddress.php' ;
require_once dirname(dirname(__FILE__)).'/includes/IDCheck.php' ;

$id = $_POST['id'];//tContractInvoiceExt id
$type = $_POST['type'];//身分別(顯示用)
$inv_another_iden = $_POST['iden'];//表_ID
$cId = $_POST['cId'];//14碼
$cCertifiedId = $_POST['CertifiedId'];
$cSignCategory = $_POST['cSignCategory'];
$tmp = explode('_', $inv_another_iden);
$iden = $tmp[0];
$iden_cid = $tmp[1];

if ($id) {
	$query = "AND cId = '".$id."'";
}

unset($tmp);
##
// echo $_POST['cId'];

$check = $_POST['check'];

if ($check==1) {

	
	for ($i=0; $i < count($_POST['row']); $i++) { 
		// branch cId、cId_1、cId_2 another_id
		$tmp = explode('_', $_POST['inv_another_iden'][$i]);
		$tbl = $tmp[0];
		$tbid=$tmp[1];
		$sql='';
		

		//
		if ($_POST['another_id'][$i]=='' && $_POST['inv_another_name'][$i] !='') {
			# code...
			
			$sql ="
					INSERT INTO  
						tContractInvoiceExt
					(
						cCertifiedId,
						cDBName,
						cTBId,
						cName,
		 				cIdentifyId,
						cInvoiceZip,
						cInvoiceAddr,
						cPhone
					)VALUES(
						'".$cCertifiedId."',
						'".$tbl."',
						'".$tbid."',
						'".$_POST['inv_another_name'][$i]."',
						'".trim($_POST['inv_another_IdentifyId'][$i])."',
						'".$_POST['inv_another_zip'][$i]."',
						'".$_POST['inv_another_addr'][$i]."',
						'".$_POST['inv_another_phone'][$i]."'
						
						
					)";
				// echo $sql;
			$conn->Execute($sql);

			$tmp_id = $conn->Insert_ID(); 

			$sql2 ="
					INSERT INTO  
						tContractInterestExt
						(	
							`cCertifiedId`, 
							`cDBName`,
							`cTBId`,
							`cName`,
							`cIdentifyId`,
							`cMobileNum`,	
							`cBaseZip`,
							`cBaseAddr`,
							`cResidentLimit`,
							`cNHITax`,
							`cInvId`
						)VALUES(
						'".$cCertifiedId."',
						'".$tbl."',
						'".$tbid."',
						'".$_POST['inv_another_name'][$i]."',
						'".trim($_POST['inv_another_IdentifyId'][$i])."',
						'".$_POST['inv_another_phone'][$i]."',		
						'".$_POST['inv_another_zip'][$i]."',
						'".$_POST['inv_another_addr'][$i]."',
						'0',
						'0',
						'".$tmp_id."'
	
					)";
			$conn->Execute($sql2);

			

			$tmp_id2 = $conn->Insert_ID(); 

			$sql = "UPDATE tContractInvoiceExt SET cIntId='".$tmp_id2."' WHERE cId='".$tmp_id."'";

			$conn->Execute($sql);

			unset($tmp_id);unset($tmp_id2);
			
		}elseif ($_POST['another_id'][$i] !='') {

			if ($_POST['inv_another_donate'][$_POST['another_id'][$i]]=='') {
				$_POST['inv_another_donate'][$_POST['another_id'][$i]]=0;
			}
			$sql ="
				UPDATE
					tContractInvoiceExt
				SET 
					
					cName='".$_POST['inv_another_name'][$i]."',
					cIdentifyId='".trim($_POST['inv_another_IdentifyId'][$i])."',
					cInvoiceZip='".$_POST['inv_another_zip'][$i]."',
					cInvoiceAddr='".$_POST['inv_another_addr'][$i]."',
					cPhone = '".$_POST['inv_another_phone'][$i]."'
				WHERE
				cCertifiedId='".$cCertifiedId."'  AND cId ='".$_POST['another_id'][$i]."'
			";
			// echo $sql."<br>";
			$conn->Execute($sql);

			if ($_POST['int_id'][$i]!=0) {
				$sql ="	
					UPDATE
						tContractInterestExt
					SET 
						cName='".$_POST['inv_another_name'][$i]."',
						cIdentifyId='".trim($_POST['inv_another_IdentifyId'][$i])."',
						cMobileNum='".$_POST['inv_another_phone'][$i]."',
						cBaseZip='".$_POST['inv_another_zip'][$i]."',
						cBaseAddr='".$_POST['inv_another_addr'][$i]."'					
						
					WHERE
						cCertifiedId='".$cCertifiedId."'  AND cId ='".$_POST['int_id'][$i]."'
				";
				// echo $sql."<br>";
				$conn->Execute($sql);
			}
			
		}


	}

	// echo "<pre>";
	// 		print_r($_POST['inv_another_donate']);
	// 		echo "</pre>";
$sql = "UPDATE tContractCase SET `cLastEditor` =  '".$_SESSION['member_id']."', `cLastTime` =  now() WHERE cCertifiedId ='".$cCertifiedId."'";
	// echo $sql;
	$conn->Execute($sql);
}

//取得其他

$sql ="SELECT * FROM tContractInvoiceExt WHERE cCertifiedId='".$cCertifiedId."' AND cDBName='".$iden."' AND cTBId='".$iden_cid."' ".$query." ORDER BY cId";
// echo $sql;
$index = 0;
$tmp = $conn->Execute($sql);

while (!$tmp->EOF) {
	$another[$index] = $tmp->fields ;
	$another[$index]['row'] = $index;
	$another[$index]['city'] = listCity($conn,$tmp->fields['cInvoiceZip']);
	$another[$index]['area'] = listArea($conn,$tmp->fields['cInvoiceZip']);

	$another[$index]['checkIDImg'] = (checkUID($tmp->fields['cIdentifyId']))?'<img src="/images/ok.png">':'<img src="/images/ng.png">';

	$index++;
	$tmp->MoveNext();
}

//是否匯出進銷檔，匯出就不能改 20150918
	$sql = "SELECT cInvoiceClose FROM  tContractCase WHERE cCertifiedId = '".$cCertifiedId."'";

	$rs = $conn->Execute($sql);

	$close = $rs->fields['cInvoiceClose'];//

##
$smarty->assign('close',$close);
$smarty->assign('id',$id);
$smarty->assign('cId',$cId);
$smarty->assign('index',$index);
$smarty->assign('type',$type);
$smarty->assign('iden',$inv_another_iden);
$smarty->assign('inv_another_country',listCity($conn,''));
$smarty->assign('inv_another_area',listArea($conn,''));
$smarty->assign('type',$type);
$smarty->assign('data_another', $another) ;
$smarty->assign('cCertifiedId', $cCertifiedId) ;
$smarty->assign('cSignCategory', $cSignCategory) ;
$smarty->display('inv_dealing_another.inc.tpl', '', 'escrow') ;
?>