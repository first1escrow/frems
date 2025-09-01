<?php
include_once '../openadodb.php' ;
include_once 'sms_function_manually.php' ;
include_once '../configs/config.class.php';
include_once '../session_check.php' ;

##
$_POST = escapeStr($_POST) ;
$cId = $_POST['cId'];
$cat = $_POST['cat'];
##
if (strlen($cId) != 9) {
	$tmp['Code'] = 201;
	$tmp['errorMsg'] = '';
	echo json_encode($tmp);
	die;
}

##

$sql = "SELECT
			cc.cEscrowBankAccount,
			cs.cCertifiedId,
			cs.cScrivener,
			cr.cBranchNum
		FROM
			tContractCase AS cc,
			tContractRealestate AS cr,
			tContractScrivener AS cs
		WHERE
			cc.cCertifiedId = cr.cCertifyId
			AND cc.cCertifiedId = cs.cCertifiedId
			AND cr.cCertifyId = '".$cId."'";

$rs = $conn->Execute($sql);

$total = $rs->RecordCount();

if ($total == 0) {
	$tmp['Code'] = 202;
	$tmp['errorMsg'] = '查無此保證號碼，請建檔後再寄送簡訊';
	echo json_encode($tmp);
	die;
}
##
$sms = new SMS_Gateway();


switch ($cat) {
	case 'cheque':
		$array = $sms->send($rs->fields["cEscrowBankAccount"] , $rs->fields["cScrivener"], $rs->fields["cBranchNum"], $cat,$mobile,'n');
		$array['Code'] = 200;
		echo json_encode($array);

		break;
	
	default:
		# code...
		break;
}



// $array = $sms->send( $rs->fields["CertifiedId"] , $sid, $rs->fields["cBranchNum"], 'cheque', $id, 'n', 0);


?>