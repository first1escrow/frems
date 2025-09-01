<?php
include_once '../openadodb.php' ;

function checkExpenseDetailSms($id){
	global $conn;
	$check = 0;

	$sql = "SELECT * FROM tExpenseDetailSms WHERE eExpenseId = '".$id."'";
	$rs = $conn->Execute($sql);
	$total=$rs->RecordCount();
	if ($total > 0) {
		$data = $rs->fields;
		
		if ($data['eSignMoney'] > 0) {$check = 1;}
		if ($data['eAffixMoney'] > 0) {$check = 1;}
		if ($data['eDutyMoney'] > 0) {$check = 1;}
		if ($data['eEstimatedMoney'] > 0) {$check = 1;;}
		if ($data['eEstimatedMoney2'] > 0) {$check = 1;}
		if ($data['eCompensationMoney'] > 0) {$check = 1;}
		if ($data['eServiceFee'] > 0) { $check = 1;}
		if ($data['eExtraMoney'] > 0) { $check = 1;}
	
	}

	$sql = "SELECT * FROM tExpenseDetailSmsOther WHERE eExpenseId = '".$id."' AND eDel = 0";
	$rs = $conn->Execute($sql);
	$total=$rs->RecordCount();

	if ($total > 0) {
		$check = 1;
	}

	if ($check == 1) {
		return true;
	}else{
		return false;
	}

	

}
?>