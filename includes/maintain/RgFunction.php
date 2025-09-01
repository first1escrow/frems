<?php


function setRgMoney($id){
	global $conn;

	$dateStart = date('Y-m')."-01 00:00:00";
	$dateEnd = date('Y-m')."-31 23:59:59";

	$sql=  "SELECT SUM(rCharge) AS Total FROM tRgSend WHERE rStatus = 3 AND rAccount ='".$id."' AND rSendDateTime >='".$dateStart."' AND rSendDateTime <='".$dateEnd."'";

	$rs = $conn->Execute($sql);

	$money1 = $rs->fields['Total']; //已花費的金額

	$sql = "SELECT rRgMoney FROM tRgMoney WHERE rAccount ='".$id."' AND rDate >='".$dateStart."' AND rDate <= '".$dateEnd."'";
	$rs = $conn->Execute($sql);
	$rgMoney = $rs->fields['rRgMoney'];//餘額
	// $sql = "SELECT sRgBalance FROM tScrivener WHERE sId = '".(int)substr($id, 2)."' ";
	// $rs = $conn->Execute($sql);
	// $balance = $rs->fields['sRgBalance'];

	$sql = "SELECT SUM(rMoney) AS bonus FROM tRgBonus WHERE rAccount ='".$id."' AND rTime >='".$dateStart."' AND rTime <='".$dateEnd."' AND rStatus = 1";
	$rs = $conn->Execute($sql);
	$bonus = $rs->fields['bonus'];//已加值的金額


	$balance = $rgMoney - $money1 + $bonus;

	$sql= "UPDATE tRgMoney SET rRgBalance = '".$balance."',rRgBonus='".$bonus."' WHERE rAccount = '".$id."' AND rDate >='".$dateStart."' AND rDate <= '".$dateEnd."'";
	$conn->Execute($sql);

	return $balance;
}

?>