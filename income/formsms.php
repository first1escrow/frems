<?php
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../class/contract.class.php';
include_once '../sms/sms_function.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once 'income_function.php' ;

$sms = new SMS_Gateway();
$contract = new Contract();


$txt = trim($_POST['txt']);

$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];

$cid = empty($_POST["cid"]) 
        ? $_GET["cid"]
        : $_POST["cid"];



$sql = '
	SELECT 
		* 
	FROM
		tExpense
	WHERE
		id="'.$id.'" ;
' ;

$rs = $conn->Execute($sql) ;
$eDepAccount = $rs->fields['eDepAccount'] ;
$eTradeCode = $rs->fields['eTradeCode'] ;

$data_case = $contract->GetRealstate($cid);
$data_sc = $contract->GetScrivener($cid);

$sendList = array();
$sendList['normal_sms'] = $_POST['normal_sms'];
$sendList['normal_sms_txt'] = $_POST['normal_sms_txt'];
$sendList['owner_sms'] = $_POST['owner_sms'];
$sendList['owner_sms_txt'] = $_POST['owner_sms_txt'];

// print_r($_POST);



if (count($_POST['normal_sms']) > 0 || $_POST['owner_sms'] > 0) {


	$list = array() ;
	//檢查是否有明細
	$target = 'income';
	if (checkExpenseDetailSms($id) == true) {
		$target = 'income2';
	}
	// echo $target."\r\n";
	$list =  $sms->sendIncome(substr($eDepAccount,2),$data_sc['cScrivener'],$data_case['cBranchNum'],$target,$id,'y',$sendList);

	// print_r($list);
	// if (count($mag) <= 0) {
	// 	echo true ;
	// }

	
}



?>
