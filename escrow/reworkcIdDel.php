<?php
include_once '../openadodb.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_REQUEST), '恢復保證號碼狀態為未使用(刪除)') ;

$cid = trim(addslashes($_REQUEST['cid'])) ;
	
if ($cid) {

	$chk = array();
	
	$i=0;
	// $chk[$i]['tbl'] = 'tBankInterest';
	// $chk[$i++]['col'] = 'tAccount';

	$chk[$i]['tbl'] = 'tBankTrans';
	$chk[$i++]['col'] = 'tVR_Code';

	$chk[$i]['tbl'] = 'tExpense';
	$chk[$i++]['col'] = 'eAccount';

	$chk[$i]['tbl'] = 'tExpense_cheque';
	$chk[$i++]['col'] = 'eAccount';


	foreach ($chk as $key => $value) {
		$sql = 'SELECT '.$value['col'].' FROM '.$value['tbl'].' WHERE '.$value['col'].' LIKE "%'.$cid.'"';

		$rs=$conn->Execute($sql);

		 $total = $rs->RecordCount();
	    if ($total > 0) {
	        
	        
	        $msg = 1;
	        break;
	    }
		
	}

	if($msg==1){
		echo '已有入帳資料';

		die();
	// $msg =$chk['tbl'].'有入帳資料'."\n";
	}else{

		//將虛擬帳號恢復為未使用
		$sql = 'UPDATE tBankCode SET bDel="n" WHERE bAccount LIKE "%'.$cid.'";' ;
		// echo "<hr>\n".'sql='.$sql ;
		if ($conn->Execute($sql)) {
			echo "保證號碼已恢復未使用狀態\n" ;
		}
		else {
			echo "保證號碼恢復失敗\n" ;
		}
		##
		
	}
}
?>