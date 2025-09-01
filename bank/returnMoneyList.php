<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
include_once 'class/SmartyMain.class.php';


$_POST = escapeStr($_POST) ;




// $sql = "SELECT * FROM tBankReturnMoney WHERE bTime >= '".date("Y-m-d",strtotime('-30 day'))."'";

// $rs = $conn->Execute($sql);

// while (!$rs->EOF) {
// 	$record[] = $rs->fields;

// 	$rs->MoveNext();
// }
// ##
// $count = 0;
// for ($i=0; $i < count($record); $i++) { 
// 	$sql = "SELECT tPayOk,tExport_nu,SUM(tMoney) as M,tExport_time,tVR_Code,tBank_kind,tObjKind2 FROM tBankTrans WHERE tExport_nu = '".$record[$i]['bExport_nu']."' GROUP BY tExport_nu ";

// 	$rs = $conn->Execute($sql);
// 	$total = $rs->RecordCount();
	
// 	if ($total > 0) {
// 		$list[] = $rs->fields;
		
// 		$count++;
// 	}
// }


$sql = "SELECT tPayOk,tExport_nu,SUM(tMoney) as M,tExport_time,tVR_Code,tBank_kind,tObjKind2 FROM tBankTrans WHERE tObjKind2  = '02' AND tBankLoansDate>='".date("Y-m-d",strtotime('-30 day'))."' GROUP BY tExport_nu";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$list[] = $rs->fields;
	$rs->MoveNext();
}

##

$smarty->assign('list',$list);
$smarty->display('returnMoneyList.inc.tpl', '', 'bank');
?>