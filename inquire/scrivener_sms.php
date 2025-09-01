<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once 'sms/sms_function.php';
include_once 'class/contract.class.php';

$sms = new SMS_Gateway();
$contract = new Contract();

if(empty($_GET['cid']))
{
	$_GET['cid']=$_POST['cid'];
}

$cid=addslashes(trim($_GET['cid']));
$subject=addslashes(trim($_POST['subject']));
$total=addslashes(trim($_POST['total']));
$check=addslashes(trim($_POST['check']));

//彈跳訊息顯示用
if($check==1)
{


	$sql="SELECT bAccount,bSID FROM tBankCode WHERE bAccount LIKE '%".$cid."'";

	$rs = $conn->Execute($sql) ;

	$data_case = $contract->GetRealstate($cid);

	$data_sc = $contract->GetScrivener($cid);

	$sql="SELECT cSmsTarget  FROM tContractScrivener WHERE cCertifiedId =".$cid." AND cScrivener =".$data_sc['cScrivener']."";

	 // echo $sql."<br>";
	$tmp=$conn->Execute($sql);
	$check=mb_strlen($tmp->fields['cSmsTarget'], "utf-8");

	unset($tmp);
	
	if($check < 10)
	{

		die('1');
	}

	$scrivener = $sms->send_scrivener( $rs->fields['bAccount'], $data_sc['cScrivener'], $data_case['cBranchNum'], $subject, $total, 'n', 0);

	
	die();
}




//寄送簡訊
if($subject)
{
		
	$sql="SELECT bAccount,bSID FROM tBankCode WHERE bAccount LIKE '%".$cid."'";

	$rs = $conn->Execute($sql) ;

	$data_case = $contract->GetRealstate($cid);

	$data_sc = $contract->GetScrivener($cid);

	$scrivener = $sms->send_scrivener( $rs->fields['bAccount'], $data_sc['cScrivener'], $data_case['cBranchNum'], $subject, $total, 'n', 0);

// 	echo "<pre>";
//  print_r($scrivener);
// echo "</pre>";
	die();
	header("location:scrivener_sms.php?cid=".$cid."");
	die($scrivener);
	
}



$smarty->assign('cid',$cid) ;
$smarty->assign('text',$text);
$smarty->display('scrivener_sms.inc.tpl', '', 'inquire');

?>