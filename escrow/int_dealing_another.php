<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../tracelog.php' ;
include_once 'class/getAddress.php' ;
include_once 'class/contract.class.php';
include_once 'class/getBank.php' ;
require_once dirname(dirname(__FILE__)).'/includes/IDCheck.php' ;

$contract = new Contract();

$id = $_POST['id'];//tContractInterestExt id
$type = $_POST['type'];//身分別(顯示用)
$int_another_iden = $_POST['iden'];//表_ID
// echo $int_another_iden;
$cId = $_POST['cId'];//14碼
$cCertifiedId = $_POST['CertifiedId'];
$cSignCategory = $_POST['cSignCategory'];
$tmp = explode('_', $int_another_iden);
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
		$tmp = explode('_', $_POST['iden']);
		$tbl = $tmp[0];
		$tbid=$tmp[1];
		$sql='';
		unset($tmp);
		if ($_POST['int_another_pdate'][$i] !='0000-00-00' ) {
			$tmp = explode('-', $_POST['int_another_pdate'][$i]);
			$y = $tmp[0]+1911;
			$m = $tmp[1];
			$d = $tmp[2];
			$_POST['int_another_pdate'][$i] = $y."-".$m."-".$d;
			unset($tmp);
		}
		

		

		//
		if ($_POST['another_id'][$i]=='' && $_POST['int_another_name'][$i] !='') {
			# code...
			
			// $sql ="
			// 		INSERT INTO  
			// 			tContractInterestExt
			// 			(
							
			// 				`cCertifiedId`, 
			// 				`cDBName`,
			// 				`cTBId`,
			// 				`cName`,
			// 				`cIdentifyId`,
			// 				`cMobileNum`,
			// 				`cRegistZip`,
			// 				`cRegistAddr`,
			// 				`cBaseZip`,
			// 				`cBaseAddr`,
			// 				`cBankMain`,
			// 				`cBankBranch`,
			// 				`cBankAccName`,
			// 				`cBankAccNum`,
			// 				`cCountryCode`,
			// 				`cTaxTreatyCode`,
			// 				`cResidentLimit`,
			// 				`cPaymentDate`,
			// 				`cNHITax`
			// 			)VALUES(
			// 			'".$cCertifiedId."',
			// 			'".$tbl."',
			// 			'".$tbid."',
			// 			'".$_POST['int_another_name'][$i]."',
			// 			'".$_POST['int_another_IdentifyId'][$i]."',
			// 			'".$_POST['int_another_phone'][$i]."',
			// 			'".$_POST['int_another_zip'][$i]."',
			// 			'".$_POST['int_another_addr'][$i]."',
			// 			'".$_POST['int_another_czip'][$i]."',
			// 			'".$_POST['int_another_caddr'][$i]."',
			// 			'".$_POST['int_another_bank'][$i]."',
			// 			'".$_POST['int_another_bankbranch'][$i]."',
			// 			'".$_POST['int_another_bankaccname'][$i]."',
			// 			'".$_POST['int_another_bankaccnumber'][$i]."',
			// 			'".$_POST['fcountry'][$i]."',
			// 			'".$_POST['int_another_ftax'][$i]."',
			// 			'".$_POST['int_another_rlimit'.$i]."',
			// 			'".$_POST['int_another_pdate'][$i]."',
			// 			'".$_POST['int_another_NHITax'.$i]."'
						
						
						
			// 		)";
				$sql ="
					INSERT INTO  
						tContractInterestExt
						(	
							`cCertifiedId`, 
							`cDBName`,
							`cTBId`,
							`cName`,
							`cIdentifyId`,
							`cPostId`,
							`cMobileNum`,	
							`cBaseZip`,
							`cBaseAddr`,
							`cCountryCode`,
							`cTaxTreatyCode`,
							`cResidentLimit`,
							`cPaymentDate`,
							`cNHITax`
						)VALUES(
						'".$cCertifiedId."',
						'".$tbl."',
						'".$tbid."',
						'".$_POST['int_another_name'][$i]."',
						'".trim($_POST['int_another_IdentifyId'][$i])."',
						'".$_POST['int_another_postid'][$i]."',
						'".$_POST['int_another_phone'][$i]."',		
						'".$_POST['int_another_czip'][$i]."',
						'".$_POST['int_another_caddr'][$i]."',						
						'".$_POST['fcountry'][$i]."',
						'".$_POST['int_another_ftax'][$i]."',
						'".$_POST['int_another_rlimit'.$i]."',
						'".$_POST['int_another_pdate'][$i]."',
						'".$_POST['int_another_NHITax'.$i]."'
	
					)";

				// echo $sql."<br>";
			$conn->Execute($sql);
			
		}elseif ($_POST['another_id'][$i] !='') {

			
			// $sql ="
			// 	UPDATE
			// 		tContractInterestExt
			// 	SET 
			// 		cName='".$_POST['int_another_name'][$i]."',
			// 		cIdentifyId='".$_POST['int_another_IdentifyId'][$i]."',
			// 		cMobileNum='".$_POST['int_another_phone'][$i]."',
			// 		cRegistZip='".$_POST['int_another_zip'][$i]."',
			// 		cRegistAddr='".$_POST['int_another_addr'][$i]."',
			// 		cBaseZip='".$_POST['int_another_czip'][$i]."',
			// 		cBaseAddr='".$_POST['int_another_caddr'][$i]."',					
			// 		cBankMain='".$_POST['int_another_bank'][$i]."',
			// 		cBankBranch='".$_POST['int_another_bankbranch'][$i]."',
			// 		cBankAccName='".$_POST['int_another_bankaccname'][$i]."',
			// 		cBankAccNum = '".$_POST['int_another_bankaccnumber'][$i]."',
			// 		cCountryCode = '".$_POST['fcountry'][$i]."',
			// 		cTaxTreatyCode = '".$_POST['int_another_ftax'][$i]."',
			// 		cResidentLimit ='".$_POST['int_another_rlimit'.$i]."',
			// 		cPaymentDate = '".$_POST['int_another_pdate'][$i]."',
			// 		cNHITax = '".$_POST['int_another_NHITax'.$i]."'
			// 	WHERE
			// 		cCertifiedId='".$cCertifiedId."'  AND cId ='".$_POST['another_id'][$i]."'
			// ";

			$sql ="
				UPDATE
					tContractInterestExt
				SET 
					cName='".$_POST['int_another_name'][$i]."',
					cIdentifyId='".trim($_POST['int_another_IdentifyId'][$i])."',
					cPostId='".$_POST['int_another_postid'][$i]."',
					cMobileNum='".$_POST['int_another_phone'][$i]."',
					cBaseZip='".$_POST['int_another_czip'][$i]."',
					cBaseAddr='".$_POST['int_another_caddr'][$i]."',					
					cCountryCode = '".$_POST['fcountry'][$i]."',
					cTaxTreatyCode = '".$_POST['int_another_ftax'][$i]."',
					cResidentLimit ='".$_POST['int_another_rlimit'.$i]."',
					cPaymentDate = '".$_POST['int_another_pdate'][$i]."',
					cNHITax = '".$_POST['int_another_NHITax'.$i]."'
				WHERE
					cCertifiedId='".$cCertifiedId."'  AND cId ='".$_POST['another_id'][$i]."'
			";
			// echo $sql."<br>";
			$conn->Execute($sql);

			if ($_POST['inv_id'][$i] !=0) { //判段是否為發票聯動的對象
					$sql ="
					UPDATE
						tContractInvoiceExt
					SET 
						
						cName='".$_POST['int_another_name'][$i]."',
						cIdentifyId='".trim($_POST['int_another_IdentifyId'][$i])."',
						cInvoiceZip='".$_POST['int_another_czip'][$i]."',
						cInvoiceAddr='".$_POST['int_another_caddr'][$i]."',
						cPhone = '".$_POST['int_another_phone'][$i]."'
					WHERE
					cCertifiedId='".$cCertifiedId."'  AND cId ='".$_POST['inv_id'][$i]."'
				";
				$conn->Execute($sql);
			}
			
			//  
		}


	}

	$sql = "UPDATE tContractCase SET `cLastEditor` =  '".$_SESSION['member_id']."', `cLastTime` =  now() WHERE cCertifiedId ='".$cCertifiedId."'";
	// echo $sql;
	$conn->Execute($sql);
	// echo "<pre>";
	// 		print_r($_POST['int_another_donate']);
	// 		echo "</pre>";

}

//取得其他

$sql ="SELECT * FROM tContractInterestExt WHERE cCertifiedId='".$cCertifiedId."' AND cDBName='".$iden."' AND cTBId='".$iden_cid."' ".$query." ORDER BY cId";
// echo $sql;
$index = 0;
$tmp = $conn->Execute($sql);

$total=$tmp->RecordCount();

while (!$tmp->EOF) {
	$another[$index] = $tmp->fields ;
	$another[$index]['row'] = $index;
	$another[$index]['city'] = listCity($conn,$tmp->fields['cRegistZip']);
	$another[$index]['area'] = listArea($conn,$tmp->fields['cRegistZip']);

	$another[$index]['ccity'] = listCity($conn,$tmp->fields['cBaseZip']);
	$another[$index]['carea'] = listArea($conn,$tmp->fields['cBaseZip']);

	$another[$index]['menu_branch']  = getBankBranch($conn,$tmp->fields['cBankMain'],$tmp->fields['cBankBranch']) ;

	$another[$index]['checkIDImg'] = (checkUID($tmp->fields['cIdentifyId']))?'<img src="/images/ok.png">':'<img src="/images/ng.png">';

	if ($another[$index]['cPaymentDate']!='0000-00-00' ) {
		$tmp2 = explode('-', $another[$index]['cPaymentDate']);
		$another[$index]['cPaymentDate'] = ($tmp2[0]-1911)."-".$tmp2[1]."-".$tmp2[2];
		unset($tmp2);
	}
	
	$index++;
	$tmp->MoveNext();
}

if ($total==0) {
	$another = array();


}


//取得總行(1)選單
$menu_bank = $contract->GetBankMenuList();
##

//取得分行(1)選單

##


//國籍代碼
$list_countrycode = $contract->GetCountryCode();
$menu_countrycode = array();


$menu_countrycode = $contract->ConvertOption($list_countrycode, 'cCode', 'cCountry');
array_unshift($menu_countrycode,'請選擇');

//是否匯出進銷檔，匯出就不能改 20150918
	$sql = "SELECT cInvoiceClose FROM  tContractCase WHERE cCertifiedId = '".$cCertifiedId."'";

	$rs = $conn->Execute($sql);

	$close = $rs->fields['cInvoiceClose'];//
##
##
$smarty->assign('close',$close);
$smarty->assign('id',$id);
$smarty->assign('cId',$cId);
$smarty->assign('index',$index);
$smarty->assign('type',$type);
$smarty->assign('iden',$int_another_iden);
$smarty->assign('int_another_country',listCity($conn,''));
$smarty->assign('int_another_area',listArea($conn,''));
$smarty->assign('type',$type);
$smarty->assign('data_another', $another) ;
$smarty->assign('cCertifiedId', $cCertifiedId) ;
$smarty->assign('cSignCategory', $cSignCategory) ;

$smarty->assign('menu_bank',$menu_bank);
$smarty->assign('owner_menu_branch',$owner_menu_branch);
$smarty->assign('buyer_menu_branch',$buyer_menu_branch);
$smarty->assign('menu_countrycode',$menu_countrycode);//國籍代碼
$smarty->display('int_dealing_another.inc.tpl', '', 'escrow') ;
?>