<?php
include_once '../../openadodb.php' ;

//100686570.100686672.100686774.100686876
header("Content-Type:text/html; charset=utf-8");
// $cId = array('100686570','100686672','100686774','100686876');

// $cId = array('081168370','100686071','100685974','100686275','100686479');
// $cId = array('081168472','100686173','081168676');

$cId = array('100067055');

$sql = "SELECT * FROM tContractBank WHERE cShow = 1";
$rs = $conn->Execute($sql);
$bank = array();
while (!$rs->EOF) {
	$bank[$rs->fields['cBankCode']] = $rs->fields;
	$rs->MoveNext();
}

$Undertaker = '';
$data = array();
foreach ($cId as $value) {
	$sql = "SELECT
				cc.cEscrowBankAccount,
				cc.cBank,
				co.cBankKey2 ,
				co.cBankBranch2,
				co.cBankAccName,
				co.cBankAccNumber,
				co.cName,
				co.cChecklistBank,
				co.cBankMoney,
				p.pName AS undertaker
			FROM
				tContractOwner AS co
			JOIN 
				tContractCase AS cc ON cc.cCertifiedId=co.cCertifiedId
			JOIN
				tContractScrivener AS cs ON cs.cCertifiedId=co.cCertifiedId
			JOIN
				tScrivener AS s ON s.sId = cs.cScrivener
			JOIN
				tPeopleInfo AS p ON p.pId = s.sUndertaker1
			WHERE
				cc.cCertifiedId = '".$value."'";
	
	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		

		$data[$value][$i]['tVR_Code'] = $rs->fields['cEscrowBankAccount'];
		$data[$value][$i]['tBank_kind'] = $bank[$rs->fields['cBank']]['cBankName'];
		$data[$value][$i]['tCode'] = ($rs->fields['cBankKey2'] == $bank[$rs->fields['cBank']]['cBankMain'])?'01':'02';
		$data[$value][$i]['tCode2'] = ($rs->fields['cBankKey2'] == $bank[$rs->fields['cBank']]['cBankMain'])?'聯行轉帳':'跨行代清償';
		$data[$value][$i]['tKind'] = '賣方';
		$data[$value][$i]['tObjKind'] = '點交(結案)';
		$data[$value][$i]['tMemo'] = $value;

		
		$data[$value][$i]['tBankCode']  = $rs->fields['cBankKey2'].$rs->fields['cBankBranch2'];
		$data[$value][$i]['tAccount'] = $rs->fields['cBankAccNumber'];
		$data[$value][$i]['tAccountName']  = $rs->fields['cBankAccName'];
		$data[$value][$i]['tMoney'] = $rs->fields['cBankMoney'];
		$data[$value][$i]['undertaker'] = $rs->fields['undertaker'];
		$i++;

		$sql = "SELECT * FROM tContractCustomerBank WHERE cCertifiedId='".$value."' AND cChecklistBank = 0 AND cIdentity = 2 ORDER BY cId ASC";

		$rs2 = $conn->Execute($sql);

		while (!$rs2->EOF) {

			if ($rs2->fields['cBankAccountNo'] != '') {
				$data[$value][$i]['tVR_Code'] = $rs->fields['cEscrowBankAccount'];
				$data[$value][$i]['tBank_kind'] = $bank[$rs->fields['cBank']]['cBankName'];
				$data[$value][$i]['tCode'] = ($rs2->fields['cBankMain'] == $bank[$rs->fields['cBank']]['cBankMain'])?'01':'02';
				$data[$value][$i]['tCode2'] = ($rs2->fields['cBankMain'] == $bank[$rs->fields['cBank']]['cBankMain'])?'聯行轉帳':'跨行代清償';
				$data[$value][$i]['tKind'] = '賣方';
				$data[$value][$i]['tObjKind'] = '點交(結案)';
				$data[$value][$i]['tMemo'] = $value;

				
				$data[$value][$i]['tBankCode']  = $rs2->fields['cBankMain'].$rs2->fields['cBankBranch'];
				$data[$value][$i]['tAccount'] = $rs2->fields['cBankAccountNo'];
				$data[$value][$i]['tAccountName']  = $rs2->fields['cBankAccountName'];
				$data[$value][$i]['tMoney'] = $rs2->fields['cBankMoney'];
				$data[$value][$i]['undertaker'] = $rs->fields['undertaker'];
		


				
				
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
				tOwner = '".$v['undertaker']."'
			";


		echo $sql.";<br>";
		// die;
		# code...
	}

	
}



?>