<?php

function getData($arr)
{
	global $conn;
	//保證號碼 發票姓名 出款日 對象別(買賣仲介地政士)  承辦人
	
	//出款日
	$sql = "SELECT 
				cc.cBankList,
				cc.cEscrowBankAccount,
				(SELECT pName FROM tPeopleInfo AS p WHERE p.pId =s.sUndertaker1) AS pName 
			FROM
				tContractCase AS cc
			LEFT JOIN 
				tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
			LEFT JOIN 
				tScrivener AS s ON s.sId = cs.cScrivener
			WHERE
				cc.cCertifiedId ='".$arr['cCertifiedId']."'";
	$rs = $conn->Execute($sql);
	$tmp = $rs->fields;
	$arr['pName'] = $rs->fields['pName'];

	$sql = " SELECT tKind,tBankLoansDate FROM tBankTrans  WHERE tVR_Code='".$tmp['cEscrowBankAccount']."' AND tKind ='保證費'";
	$rs = $conn->Execute($sql);
	$arr['cCertifyDate'] = $rs->fields['tBankLoansDate'] ;

	if ($tmp['cBankList'] != '') {
		$tmp2 = explode('-',$tmp['cBankList']); 
		$arr['cCertifyDate'] = ($tmp[0] - 1911).'-'.str_pad($tmp[1],2,'0',STR_PAD_LEFT).'-'.str_pad($tmp[2],2,'0',STR_PAD_LEFT) ;
	}
	unset($tmp2);unset($tmp);

	

	
	if ($arr['cTB'] == 'tContractBuyer' || $arr['cTB'] == 'tContractOthers_B' || $arr['cTB'] == 'tContractInvoiceExt_B') {
		$arr['cTB'] = '買方';
	}elseif ($arr['cTB'] == 'tContractOwner' || $arr['cTB'] == 'tContractOthers_O' || $arr['cTB'] == 'tContractInvoiceExt_O') {
		$arr['cTB'] = '賣方';
	}elseif ($arr['cTB'] == 'tContractRealestate_R' || $arr['cTB'] == 'tContractRealestate_R1' || $arr['cTB'] == 'tContractRealestate_R2') {
		$arr['cTB'] = '仲介';
	}elseif ($arr['cTB'] == 'tContractScrivener') {
		$arr['cTB'] = '地政士';
	}

	return $arr;
}

function checkprint($arr)
{
	global $conn;
	$tmp = explode('_', $arr['cTB']);

	if ($tmp[0] == 'tContractRealestate' ) {
		$sql = "SELECT * FROM ".$tmp[0]." WHERE cCertifyId ='".$arr['cCertifiedId']."'";
		$rs = $conn->Execute($sql);

		if ($tmp[1] == 'R') {
			
			$type = $rs->fields['cInvoicePrint'];
			$type2 = $rs->fields['cInvoiceDonate'];
		}elseif ($tmp[1] == 'R1') {
			$type = $rs->fields['cInvoicePrint1'];
			$type2 = $rs->fields['cInvoiceDonate1'];
		}elseif ($tmp[1] == 'R2') {
			$type = $rs->fields['cInvoicePrint2'];
			$type2 = $rs->fields['cInvoiceDonate2'];
		}


	}elseif ($tmp[0] == 'tContractScrivener') {
		$sql = "SELECT * FROM ".$tmp[0]." WHERE cCertifiedId ='".$arr['cCertifiedId']."'";

		$rs = $conn->Execute($sql);

		$type = $rs->fields['cInvoicePrint'];
		$type2 = $rs->fields['cInvoiceDonate'];

	}else{
		$sql = "SELECT * FROM ".$tmp[0]." WHERE cId ='".$arr['cTargetId']."'";
		$rs = $conn->Execute($sql);

		$type = $rs->fields['cInvoicePrint'];
		$type2 = $rs->fields['cInvoiceDonate'];

	}


	if ($rs->fields['cInvoicePrint'] == '') {
		$type = 'N';
	}

	if (preg_match("/^\d{8}$/",$arr['cIdentifyId'])) {
		$type = 'Y';
	}
	// if (preg_match("/^\d{8}$/",$arr['cIdentifyId'])) { //法人一定印發票
	// 	$type = 'Y';
	// }

	if ($type2 == 1) { //捐贈一定不印發票
		$type = 'N';
	}
	
	unset($tmp);
	return $type;
}
function checktax($val)
{
	if (preg_match("/^\d{8}$/",$val)) {
					
		$AQ = '三聯' ;
	}else{
		$AQ = '二聯' ;
	}
	return $AQ;
}

?>