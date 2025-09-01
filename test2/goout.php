<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;

//100686570.100686672.100686774.100686876
header("Content-Type:text/html; charset=utf-8");
// $cId = array('100686570','100686672','100686774','100686876');

// $cId = array('081168370','100686071','100685974','100686275','100686479');
$cId = array('081168472','100686173','081168676');


 
 


$Undertaker = '杜芝玲';
$data = array();
foreach ($cId as $value) {
	$sql = "SELECT
				cBankKey2 ,
				cBankBranch2,
				cBankAccName,
				cBankAccNumber,
				cName,
				cChecklistBank,
				cBankMoney
			FROM
				tContractOwner
			WHERE cCertifiedId = '".$value."'";

	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$data[$value][$i]['tVR_Code'] = '60001'.$value;
		$data[$value][$i]['tBank_kind'] = '一銀';
		$data[$value][$i]['tCode'] = ($rs->fields['cBankKey2'] == '007')?'01':'02';
		$data[$value][$i]['tCode2'] = ($rs->fields['cBankKey2'] == '007')?'聯行轉帳':'跨行代清償';
		$data[$value][$i]['tKind'] = '賣方';
		$data[$value][$i]['tObjKind'] = '點交(結案)';
		$data[$value][$i]['tMemo'] = $value;

		
		$data[$value][$i]['tBankCode']  = $rs->fields['cBankKey2'].$rs->fields['cBankBranch2'];
		$data[$value][$i]['tAccount'] = $rs->fields['cBankAccNumber'];
		$data[$value][$i]['tAccountName']  = $rs->fields['cBankAccName'];
		$data[$value][$i]['tMoney'] = $rs->fields['cBankMoney'];
		$i++;

		$sql = "SELECT * FROM tContractCustomerBank WHERE cCertifiedId='".$value."' AND cChecklistBank = 0 AND cIdentity = 2 ORDER BY cIdentity ASC";

		$rs2 = $conn->Execute($sql);

		while (!$rs2->EOF) {

			if ($rs2->fields['cBankAccountNo'] != '') {
				$data[$value][$i]['tVR_Code'] = '60001'.$value;
				$data[$value][$i]['tBank_kind'] = '一銀';
				$data[$value][$i]['tCode'] = ($rs2->fields['cBankMain'] == '007')?'01':'02';
				$data[$value][$i]['tCode2'] = ($rs2->fields['cBankMain'] == '007')?'聯行轉帳':'跨行代清償';
				$data[$value][$i]['tKind'] = '賣方';
				$data[$value][$i]['tObjKind'] = '點交(結案)';
				$data[$value][$i]['tMemo'] = $value;

				
				$data[$value][$i]['tBankCode']  = $rs2->fields['cBankMain'].$rs2->fields['cBankBranch'];
				$data[$value][$i]['tAccount'] = $rs2->fields['cBankAccountNo'];
				$data[$value][$i]['tAccountName']  = $rs2->fields['cBankAccountName'];
				$data[$value][$i]['tMoney'] = $rs2->fields['cBankMoney'];


				
				
				$i++;
			}
			
			$rs2->MoveNext();
		}

		
		$rs->MoveNext();
	}


}


foreach ($data as $key => $value) {
	foreach ($value as $k => $v) {

		$sql= "INSERT INTO
				tBankTrans
			SET
				tVR_Code = '".$v['tVR_Code']."',
				tBank_kind = '".$v['tBank_kind']."',
				tCode = '".$v['tCode']."',
				tCode2 = '".$v['tCode2']."',
				tKind = '".$v['tKind']."',
				tObjKind = '".$v['tObjKind']."',
				tMemo = '".$v['tMemo']."',
				tBankCode = '".$v['tBankCode']."',
				tAccount = '".$v['tAccount']."',
				tAccountName = '".$v['tAccountName']."',
				tMoney = '".$v['tMoney']."',
				tOwner = '".$Undertaker."'
			";


		echo $sql.";<br>";
		// die;
		# code...
	}

	
}



?>