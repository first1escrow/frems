<?php
include_once '../openadodb.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_REQUEST), '恢復保證號碼狀態為未使用') ;

$cid = trim($_REQUEST['cid']) ;
$cid = '100153241';


if($msg==1){
	echo '已有入帳資料';

	die();
// $msg =$chk['tbl'].'有入帳資料'."\n";
}else{
	
	$arr = array() ;
	
	//建立刪除保證號碼會關連到的資料表
	$i = 0 ;


	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractScrivener' ;
	
	$arr[$i]['cid'] = 'cCertifyId' ;
	$arr[$i++]['tbl'] = 'tContractRealestate' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractPropertyObject' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractProperty' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractOwner' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractOthers' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractLand' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractInvoice' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractInvoiceExt' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractInterestExt' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractIncome' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractExpenditure' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractCase' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractBuyer' ;
	
	$arr[$i]['cid'] = 'oCertifiedId' ;
	$arr[$i++]['tbl'] = 'tChecklistOlist' ;
	
	$arr[$i]['cid'] = 'bCertifiedId' ;
	$arr[$i++]['tbl'] = 'tChecklistBlist' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tChecklistBank' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ; //點交表銀行清單
	$arr[$i++]['tbl'] = 'tChecklist' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractSales' ;
	
	$arr[$i]['cid'] = 'bCheck_id' ;
	$arr[$i++]['tbl'] = 'tBranchSms' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractParking' ;

	$arr[$i]['cid'] = 'cCertifyId' ;
	$arr[$i++]['tbl'] = 'tContractFurniture' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractAscription' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractSpecial' ;
	
	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractPhone' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractRent' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractInvoiceQuery' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractInterestExt' ;

	$arr[$i]['cid'] = 'cCertifiedId' ;
	$arr[$i++]['tbl'] = 'tContractInvoiceExt' ;

	$arr[$i]['cid'] = 'cCertifiedId';
	$arr[$i++]['tbl'] = 'tContractCustomerBank';

	$arr[$i]['cid'] = 'cCertifiedId';
	$arr[$i++]['tbl'] = 'tContractLandPrice';
	##
	
	//開始刪除
	 foreach ($arr as $k => $v) {
		$sql = 'DELETE FROM '.$v['tbl'].' WHERE '.$v['cid'].'="'.$cid.'";' ;
		echo $sql."\r\n";
		// echo 'sql='.$sql ;
		
		// if ($conn->Execute($sql)) {
		// 	// echo "..........完成\n" ;
		// }
		// else {
		// 	// echo "..........失敗\n" ;
		// 	$fail=1;
		// }
	 }
	// ##

	 // if($fail==1)
	 // {
	 // 	echo "恢復失敗";
	 // 	die();
	 // }

	 $sql = "UPDATE tContractNote SET cDel = 1 WHERE cCertifiedId = '".$cid."'";
	 echo $sql.";\r\n";
	 // $conn->Execute($sql);
	
	//將虛擬帳號恢復為未使用
	$sql = 'UPDATE tBankCode SET bUsed="0" WHERE bAccount LIKE "%'.$cid.'";' ;
	echo $sql."\r\n";
	// echo "<hr>\n".'sql='.$sql ;
	// if ($conn->Execute($sql)) {
	// 	echo "保證號碼已恢復未使用狀態\n" ;
	// }
	// else {
	// 	echo "保證號碼恢復失敗\n" ;
	// }
	// ##
	 // }
}
?>