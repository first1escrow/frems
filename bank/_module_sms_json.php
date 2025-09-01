<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_web_addr = preg_replace("/http\:\/\//","",$web_addr) ;
include_once '../sms/sms_function.php' ;
//

$testMail = new SMS_Gateway();

$json = $_REQUEST["json"];
$jsdata = json_decode($json);
$_str = implode('","',$jsdata->datas) ;

$sql = '
	SELECT 
		a.tId,
		a.tVR_Code,
		a.tMemo,
		a.tObjKind,
		a.tMoney,
		a.tExport_nu,
		a.tExport_time,
		a.tBankLoansDate,
		c.cScrivener,
		b.cBranchNum ,
		a.tStoreId
	FROM 
		tBankTrans AS a 
	INNER JOIN 
		tContractRealestate AS b ON a.tMemo = b.cCertifyId 
	INNER JOIN 
		tContractScrivener AS c ON a.tMemo = c.cCertifiedId 
	WHERE 
		a.tId IN ("'.$_str.'");
' ;
// echo $sql;
$rs = $conn->Execute($sql);
BankTransSmsLog(trim($rs->fields['tExport_nu']),trim($rs->fields['tExport_time']));
while( !$rs->EOF ) {
	$tId = array();

	//確認本媒體檔案是否為點交(結案)且同時出款仲介服務費
	$realty = 0 ;
	$mediaCode = trim($rs->fields['tExport_nu']) ;
	$mediaTime = trim($rs->fields['tExport_time']);
	$tVR = trim($rs->fields['tVR_Code']) ;
	
	$sql = 'SELECT tMoney FROM tBankTrans WHERE tObjKind IN ("點交(結案)","預售屋") AND tKind="仲介" AND tVR_Code="'.$tVR.'" AND tExport_nu="'.$mediaCode.'";' ;
	$_rs = $conn->Execute($sql) ;
	while (!$_rs->EOF) {
		$realty += $_rs->fields['tMoney'] + 1 - 1 ;
		$_rs->MoveNext() ;
	}
	#
	unset($_rs);
	SMS_Send_Log_Insert($rs->fields);
	##

	if ($rs->fields["tObjKind"] == '仲介服務費') {
			$storeId = $rs->fields['tStoreId'];
	}

	// echo $storeId ;
	//查詢是否有自訂簡訊發送對象
	
	$sql = "SELECT * FROM tBankTranSms WHERE FIND_IN_SET (".$rs->fields['tId'].",bBankTranId) AND bDel = 0 AND bVR_Code = '".$rs->fields['tVR_Code']."' AND bObjKind = '".$rs->fields['tObjKind']."'  AND (bExport_nu = '' OR bExport_nu = '".$mediaCode."')"; //

	// echo $sql."\r\n";
	$_rs = $conn->Execute($sql) ;

	if (!$_rs->EOF) {
		// echo "AA\r\n";
		
		##
		// setExport_nu($rs->fields['tVR_Code'],$rs->fields['tObjKind'],$mediaCode,$tId);
		//send2($pid,$target,$date,$ok="n",$realty=0)
		// echo 'sendO';
		$_t = $testMail->send2(trim($rs->fields["tVR_Code"]),trim($rs->fields["tObjKind"]),$rs->fields['tId'],$mediaCode,'y',$realty,$storeId) ;
		

		
	}else{
		// echo"BB\r\n";
		$_t = $testMail->send(trim($rs->fields["tVR_Code"]),trim($rs->fields["cScrivener"]),trim($rs->fields["cBranchNum"]),trim($rs->fields["tObjKind"]),trim($rs->fields["tId"]),'y',$realty) ;

	}
	// print_r($_t);
	##
	echo $_t ;
	StepOK($rs->fields["tId"]);
	unset($bId);unset($tIdArr);unset($storeId);
	$rs->MoveNext();

}
// die;
// echo $_t ;
// print_r($_t);
function setSend(){
	global $conn;
}
function BankTransSmsLog($mediaCode,$mediaTime){
	global $conn;
	$sql = "INSERT INTO tBankTransSmsLog (bExport_nu,bExport_time)VALUES('".$mediaCode."','".$mediaTime."')";
	$conn->Execute($sql);
}

function SMS_Send_Log_Insert($data){
	global $conn;

	$sql = "INSERT INTO tSMS_Send_Log (sCertifiedId,sKind,sTransId) VALUES ('".substr($data['tVR_Code'],-9)."','".$data['tObjKind']."','".$data['tId']."')";
	$conn->Execute($sql);
}

function StepOK($tId){
	global $conn;
	// for ($i=0; $i < count($_POST['tId']); $i++) { 
		// echo 'sms_send_'.$_POST['tId']."<br>";
		// if ($_POST['cat'] == 1 || $_POST['cat'] == 3) {
			// if ($_POST['sms_send_'.$_POST['tId'][$i]]) {
				// echo $_POST['sms_send_'.$_POST['tId'][$i]]."_";
				// $sql = "UPDATE tBankTrans SET tStep1 = 1 WHERE tId ='".$_POST['tId'][$i]."'";
				// $sql = "UPDATE tBankTrans SET  WHERE tId ='".$tId."'";
				// $conn->Execute($sql);
				// echo $sql."<br>";
			// }
			//審核人都要寫上
			// $sql = "UPDATE tBankTrans SET tStep1Name = '".$_SESSION['member_id']."',tStep1Time = '".date('Y-m-d H:i:s')."' WHERE tId ='".$_POST['tId'][$i]."'";
			$sql = "UPDATE tBankTrans SET tStep1 = 1,tStep2 = 1,tStep1Name = '".$_SESSION['member_id']."',tStep1Time = '".date('Y-m-d H:i:s')."',tStep2Name = '".$_SESSION['member_id']."',tStep2Time = '".date('Y-m-d H:i:s')."' WHERE tId ='".$tId."'";
			// echo $sql;
			$conn->Execute($sql);
		// }

	// }
}

// function setExport_nu($vr_code,$objKind,$mediaCode,$tId){
// 	global $conn;

// 	$sql = "UPDATE tBankTranSms SET bExport_nu = '".$mediaCode."' WHERE bVR_Code = '".$vr_code."' AND bObjKind = '".$objKind."' AND (bExport_nu = '' OR bExport_nu = '".$mediaCode."') AND bDel = 0 AND bBankTranId IN(".@implode(',',$tId).")";

// 	$conn->Execute($sql);
// }
// echo $sql;

?>