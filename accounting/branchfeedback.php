<?php

include_once '../openadodb.php' ;
// include_once '../session_check.php' ;

$yr = htmlspecialchars($_REQUEST['y']) ; 
$sn = htmlspecialchars($_REQUEST['sn']) ;
$type = htmlspecialchars($_POST['type']);


// $sn = 430;
// $type = 'branch';
//430



function getOtherfeedMoney($sn,$type,$yr,$seF,$seT,$contractBank){
	global $conn;
	
	
	$money = 0;

	$sql = 'SELECT 
			fb.fMoney,
			tra.tMemo as cCertifiedId
		FROM
			tFeedBackMoney AS fb
		JOIN
			tBankTrans AS tra ON tra.tMemo = fb.fCertifiedId
		JOIN
			tContractCase AS cas ON cas.cCertifiedId=fb.fCertifiedId
		WHERE
			fDelete = 0 AND fType ="'.$type.'" AND fStoreId = "'.$sn.'"
			AND tra.tObjKind IN ("點交(結案)","解除契約")
			AND tra.tAccount IN ("'.$contractBank.'")		
			AND tra.tBankLoansDate>="'.$yr.'-'.$seF.'-01" AND tra.tBankLoansDate<="'.$yr.'-'.$seT.'-31"
			AND cas.cCaseStatus IN ("3",  "4")
		GROUP BY
			tra.tMemo
		ORDER BY
			tra.tExport_time
		ASC ;';

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		
		$money += $rs->fields['fMoney'];
		
		$rs->MoveNext();
	}

	return $money;
}
//每季回饋金查詢(FOR branch)
function feedbackMoney($conn,$sn,$yr,$se) {
	switch ($se) {
		case '1' :
				$seF = '01' ;
				$seT = '03' ;
				break ;
		case '2' :
				$seF = '04' ;
				$seT = '06' ;
				break ;
		case '3' :
				$seF = '07' ;
				$seT = '09' ;
				break ;
		case '4' :
				$seF = '10' ;
				$seT = '12' ;
				break ;
		default :
				break ;
	}

	//取得所有合約銀行活儲帳號
	$contractBank = '' ;
	$sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		
		$conBank[] = $rs->fields['cBankAccount'] ;

		$rs->MoveNext();
	}
	
	$contractBank = implode('","',$conBank) ;

	$sql = '
		SELECT 
			tra.tMemo as cCertifiedId,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cas.cCaseFeedBackMoney,
			cas.cCaseFeedBackMoney1,
			cas.cCaseFeedBackMoney2,
			cas.cCaseFeedback as cCaseFeedback,
			cas.cCaseFeedback1 as cCaseFeedback1,
			cas.cCaseFeedback2 as cCaseFeedback2,
			cas.cFeedbackTarget as cFeedbackTarget,
			cas.cFeedbackTarget1 as cFeedbackTarget1,
			cas.cFeedbackTarget2 as cFeedbackTarget2
		FROM
			tBankTrans AS tra
		JOIN
			tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
		JOIN
			tContractRealestate AS cr ON cr.cCertifyId=tra.tMemo
		WHERE
			tra.tObjKind IN ("點交(結案)","解除契約")
			AND tra.tAccount IN ("'.$contractBank.'")
			AND (cr.cBranchNum="'.$sn.'" OR cr.cBranchNum1="'.$sn.'" OR cr.cBranchNum2="'.$sn.'")
			AND tra.tBankLoansDate>="'.$yr.'-'.$seF.'-01" AND tra.tBankLoansDate<="'.$yr.'-'.$seT.'-31"
			AND cas.cCaseStatus IN ("3",  "4")
		GROUP BY
			tra.tMemo
		ORDER BY
			tra.tExport_time
		ASC ;
	' ;
	
	
	$rs = $conn->Execute($sql) ;
	
	$fbM = 0 ;
	while (!$rs->EOF) {
		$tmp = $rs->fields ;
		
		if ($tmp['cBranchNum'] == $sn && $tmp['cFeedbackTarget'] == 1 && $tmp['cCaseFeedback'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney'] ;
		}
		
		if ($tmp['cBranchNum1'] == $sn && $tmp['cFeedbackTarget1'] == 1 && $tmp['cCaseFeedback1'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney1'] ;
		}
		else if ($tmp['cBranchNum2'] == $sn && $tmp['cFeedbackTarget2'] == 1 && $tmp['cCaseFeedback2'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney2'] ;
		}
		
		unset($tmp) ;

		

	
		$rs->MoveNext() ;
	}
	

	//取出範圍內未收履保費但仍要回饋(有利息)的案件
	$_sql = '
			SELECT 
				cc.cCertifiedId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cc.cCaseFeedBackMoney,
				cc.cCaseFeedBackMoney1,
				cc.cCaseFeedBackMoney2,
				cc.cCaseFeedback as cCaseFeedback,
				cc.cCaseFeedback1 as cCaseFeedback1,
				cc.cCaseFeedback2 as cCaseFeedback2,
				cc.cFeedbackTarget as cFeedbackTarget,
				cc.cFeedbackTarget1 as cFeedbackTarget1,
				cc.cFeedbackTarget2 as cFeedbackTarget2 
			FROM 
				tContractCase AS cc
			JOIN
				tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
		 	WHERE 
		 		cc.cBankList>="'.$yr.'-'.$seF.'-01" AND cc.cBankList<="'.$yr.'-'.$seT.'-31"';

		 		
		 		 

	$rs = $conn->Execute($_sql);
	while (!$rs->EOF) {
		
		$tmp = $rs->fields ;
		
		if ($tmp['cBranchNum'] == $sn && $tmp['cFeedbackTarget'] == 1 && $tmp['cCaseFeedback'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney'] ;
		}
		
		if ($tmp['cBranchNum1'] == $sn && $tmp['cFeedbackTarget1'] == 1 && $tmp['cCaseFeedback1'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney1'] ;
		}
		else if ($tmp['cBranchNum2'] == $sn && $tmp['cFeedbackTarget2'] == 1 && $tmp['cCaseFeedback2'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney2'] ;
		}



		$rs->MoveNext();
	}
	

	//其他回饋
	$tmp = getOtherfeedMoney($sn,2,$yr,$seF,$seT,$contractBank);

	$fbM += $tmp;
	
	return $fbM ;
}
##

//每季回饋金查詢(FOR scrivener)
function feedbackMoney2($conn,$sn,$yr,$se) {
	switch ($se) {
		case '1' :
				$seF = '01' ;
				$seT = '03' ;
				break ;
		case '2' :
				$seF = '04' ;
				$seT = '06' ;
				break ;
		case '3' :
				$seF = '07' ;
				$seT = '09' ;
				break ;
		case '4' :
				$seF = '10' ;
				$seT = '12' ;
				break ;
		default :
				break ;
	}

	//取得所有合約銀行活儲帳號
	$contractBank = '' ;
	$sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		
		$conBank[] = $rs->fields['cBankAccount'] ;

		$rs->MoveNext();
	}
	
	$contractBank = implode('","',$conBank) ;

	$sql = '
		SELECT 
			tra.tMemo as cCertifiedId,
			cs.cScrivener,
			cas.cCaseFeedBackMoney,
			cas.cCaseFeedBackMoney1,
			cas.cCaseFeedBackMoney2,
			cas.cCaseFeedback as cCaseFeedback,
			cas.cCaseFeedback1 as cCaseFeedback1,
			cas.cCaseFeedback2 as cCaseFeedback2,
			cas.cFeedbackTarget as cFeedbackTarget,
			cas.cFeedbackTarget1 as cFeedbackTarget1,
			cas.cFeedbackTarget2 as cFeedbackTarget2,
			cas.cSpCaseFeedBackMoney as cSpCaseFeedBackMoney
		FROM
			tBankTrans AS tra
		JOIN
			tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
		JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=tra.tMemo
		WHERE
			tra.tKind = "保證費"
			AND tra.tAccount IN ("'.$contractBank.'")
			AND cs.cScrivener="'.$sn.'"
			AND tra.tBankLoansDate>="'.$yr.'-'.$seF.'-01" AND tra.tBankLoansDate<="'.$yr.'-'.$seT.'-31"
		GROUP BY
			tra.tMemo
		ORDER BY
			tra.tExport_time
		ASC ;
	' ;
	
	
	$rs = $conn->Execute($sql) ;
	
	$fbM = 0 ;
	while (!$rs->EOF) {
		$tmp = $rs->fields ;
		
		if ($tmp['cFeedbackTarget'] == 2 && $tmp['cCaseFeedback'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney'] ;

			if ($se == 3) {
				
			}
			
		}

		if ($tmp['cFeedbackTarget1'] == 2 && $tmp['cCaseFeedback1'] == 0 && $tmp['cCaseFeedBackMoney1'] > 0) {
			$fbM += $tmp['cCaseFeedBackMoney1'] ;
		}

		if ($tmp['cFeedbackTarget2'] == 2 && $tmp['cCaseFeedback2'] == 0 && $tmp['cCaseFeedBackMoney2'] > 0) {
			$fbM += $tmp['cCaseFeedBackMoney2'] ;
		}


		
		if ($tmp['cSpCaseFeedBackMoney'] > 0) {
			$fbM += $tmp['cSpCaseFeedBackMoney'] ;
		}
		
			
		unset($tmp) ;
	
		$rs->MoveNext() ;
	}

	//取出範圍內未收履保費但仍要回饋(有利息)的案件
	$_sql = '
			SELECT 
				cc.cCertifiedId,
				cs.cScrivener,
				cc.cCaseFeedBackMoney,
				cc.cCaseFeedBackMoney1,
				cc.cCaseFeedBackMoney2,
				cc.cCaseFeedback as cCaseFeedback,
				cc.cCaseFeedback1 as cCaseFeedback1,
				cc.cCaseFeedback2 as cCaseFeedback2,
				cc.cFeedbackTarget as cFeedbackTarget,
				cc.cFeedbackTarget1 as cFeedbackTarget1,
				cc.cFeedbackTarget2 as cFeedbackTarget2,
				cc.cSpCaseFeedBackMoney as cSpCaseFeedBackMoney
			FROM 
				tContractCase AS cc
			JOIN
				tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		 	WHERE 
		 		cc.cBankList>="'.$yr.'-'.$seF.'-01" AND cc.cBankList<="'.$yr.'-'.$seT.'-31"
		 	AND cs.cScrivener="'.$sn.'"
		 	';

		 	
		 		 

	$rs = $conn->Execute($_sql);
	while (!$rs->EOF) {
		
		$tmp = $rs->fields ;
		
		if ($tmp['cFeedbackTarget'] == 2 && $tmp['cCaseFeedback'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney'] ;
			if ($se == 3) {
				
			}
		}

		if ($tmp['cSpCaseFeedBackMoney'] != 0) {
			$fbM += $tmp['cSpCaseFeedBackMoney'] ;
		}


		$rs->MoveNext();
	}
	
	//其他回饋
	$tmp = getOtherfeedMoney($sn,1,$yr,$seF,$seT,$contractBank);

	$fbM += $tmp;

	return $fbM ;
}
##

if ($type == 'branch') {
	$str = '' ;
	//蒐集每季回饋金額
	for ($s = 1 ; $s <= 4 ; $s ++) {
		$str .= feedbackMoney($conn,$sn,$yr,$s).',' ;
	}
	$str = preg_replace("/,$/","",$str) ;
	
	##
}elseif ($type =='scrivener') {
	$str = '';

	for ($s = 1 ; $s <= 4 ; $s ++) {
		$str .= feedbackMoney2($conn,$sn,$yr,$s).',' ;
	}
	$str = preg_replace("/,$/","",$str) ;

}



//$str = $fb['FBS1'].','.$fb['FBS2'].','.$fb['FBS3'].','.$fb['FBS4'] ;

echo $str ;
?>