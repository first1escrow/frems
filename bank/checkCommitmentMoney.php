<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$bankTranMoney = $_POST['money'];//未審核的出款金額



$sql = "SELECT
			cc.cCaseMoney,
			ci.cCommitmentMoney
		FROM
			tContractIncome AS ci
		LEFT JOIN
			tContractCase AS cc ON cc.cCertifiedId=ci.cCertifiedId
		WHERE
			cc.cCertifiedId = '".$_POST['cId']."'";

$rs = $conn->Execute($sql);

$caseMoney = $rs->fields['cCaseMoney'] - $bankTranMoney; //餘額-未審核出款金額 
// $data['money'] = $rs->fields['cCaseMoney']."-".$bankTranMoney."=".$caseMoney."_".$rs->fields['cCommitmentMoney'];
if (($rs->fields['cCommitmentMoney'] > $caseMoney) && $rs->fields['cCommitmentMoney'] > 0) {
	$data['code'] = 201;
	$data['codeMsg'] = '餘額少於承諾書金額' ;
}else{
	$data['code'] = 200;
	$data['codeMsg'] = '正常' ;
}

echo json_encode($data);
?>