<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../session_check.php' ;
require_once dirname(dirname(__FILE__)).'/includes/IDCheck.php' ;
include_once '../openadodb.php' ;


header("Content-Type:text/html; charset=utf-8"); 


// fwrite($fw, "編號,店名,業務,狀態,第二季是否有回饋\r\n");
// echo "編號,店名,業務,狀態,第二季是否有回饋\r\n";

if ($_POST) {
	$_POST = escapeStr($_POST) ;
	$zipArr = array();
	$year = $_POST['year'];
	$season = $_POST['season'];
	$identity = $_POST['identity'];
	// print_r($_POST);

	if ($_POST['area'] != '') {		
		$zip = $_POST['area'];
		$str = ($identity == 1)?" AND sCpZip1 = '".$_POST['area']."'":" AND bZip = '".$_POST['area']."'";
	}elseif ($_POST['country'] != '') {
		// echo 'city';
		$sql = "SELECT zZip FROM tZipArea WHERE zCity = '".$_POST['country']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			array_push($zipArr, $rs->fields['zZip']);

			$rs->MoveNext();
		}

		$str = ($identity == 1)?" AND sCpZip1 IN (".@implode(',', $zipArr).")":" AND bZip IN (".@implode(',', $zipArr).")";
	}

	unset($zipArr);
	// echo $str."<bR>";
	// die;
	// print_r($_POST);

	if ($identity == 1) {
		$sql = "SELECT sId,CONCAT(sName,'-',sOffice) AS storeName,CONCAT('SC',LPAD(sId,4,'0')) AS code,sStatus AS status,sMobileNum FROM tScrivener WHERE sStatus != 3 ".$str." ORDER BY sId ASC";
		// echo $sql;
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			if ($rs->fields['status'] == 1) {
				$rs->fields['status'] = '啟用';
			}elseif ($rs->fields['status'] == 2) {
				$rs->fields['status'] = '停用';
			}elseif ($rs->fields['status'] == 4) {
				$rs->fields['status'] = '未簽約';
			}
			// $rs->fields
			$list[] = $rs->fields;


			$rs->MoveNext();
		}


		foreach ($list as $k => $v) {
			
			if ($v['sMobileNum'] == '') {
				$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS salesName FROM tScrivenerSales WHERE sScrivener = '".$v['sId']."'";
				// echo $sql;
				$rs = $conn->Execute($sql);
				while (!$rs->EOF) {
					$sales[] = $rs->fields['salesName'];	

					$rs->MoveNext();
				}
				$v['sales'] = @implode(',', $sales);
				unset($sales);

				// $check = feedbackMoney2($conn,$v['sId'],$year,$season);
				$check = getFeedBackMoney($v['sId'],$year,$season,1);
				if ($check > 0) {
					$v['feed'] = '有';
				}else{
					$v['feed'] = '無';
				}

				$notfind[] = $v;
			}

				

			// }
		}
		unset($list);
	}
	


	if ($identity == 2) {
		##仲介##

		$sql = "SELECT bId,CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),'-',LPAD(bId,5,'0')) AS code,CONCAT((Select bName From `tBrand` c Where c.bId = bBrand ),bStore) AS storeName,bStatus AS status FROM tBranch WHERE  bId != '1372' AND bCategory = 1 ".$str." ORDER bY bId ASC"; //
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			if ($rs->fields['status'] == 1) {
				$rs->fields['status'] = '啟用';
			}elseif ($rs->fields['status'] == 2) {
				$rs->fields['status'] = '停用';
			}elseif ($rs->fields['status'] == 3) {
				$rs->fields['status'] = '暫停';
			}
			$list[] = $rs->fields;


			$rs->MoveNext();
		}

		foreach ($list as $k => $v) {
			$sql = "SELECT * FROM tBranchSms WHERE bBranch = '".$v['bId']."' AND (bNID = 12 OR bNID = 13)";
			// echo $sql."\r\n";
			$rs = $conn->Execute($sql);

			if ($rs->EOF) {
				$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS salesName FROM tBranchSales WHERE bBranch = '".$v['bId']."'";
				$rs = $conn->Execute($sql);
				while (!$rs->EOF) {
					$sales[] = $rs->fields['salesName'];

					$rs->MoveNext();
				}

				$v['sales'] = @implode(',', $sales);
				unset($sales);

				// $check = feedbackMoney($conn,$v['bId'],$year,$season);
				$check = getFeedBackMoney($v['bId'],$year,$season,2);
				
				if ($check > 0) {
					$v['feed'] = '有';
				}else{
					$v['feed'] = '無';
				}

				$notfind[] = $v;
				
			}

		}

		unset($list);
	}


	include_once 'searchPhoneExcel.php';
}


#


//縣市
$sql = 'SELECT zCity FROM tZipArea WHERE 1=1  GROUP BY zCity ORDER BY zZip,zCity ASC;' ;
$rs = $conn->Execute($sql);
$citys = '<option selected="selected" value="">全部</option>'."\n" ;
while (!$rs->EOF) {
		$citys .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;

	$rs->MoveNext();
}
##
$this_year = date("Y") - 1911 ;
$this_month = date("m") ;

// 年度
$sales_year = '' ;
for ($i = $this_year + 2 ; $i > $this_year - 100 ; $i --) {
	$sales_year .= '<option value="'.($i + 1911).'"' ;
	if ($i == $this_year) { $sales_year .= ' selected="selected"' ; }
	$sales_year .= '>'.$i."</option>\n" ;
}
##

function getFeedBackMoney($sn,$yr,$se,$cat){
	global $conn;

	if ($se == 1) {
		$col = 'fS1';
	}elseif ($se == 2) {
		$col = 'fS2';
	}elseif ($se == 3) {
		$col = 'fS3';
	}elseif ($se == 4) {
		$col = 'fS4';
	}

	$sql = "SELECT * FROM tFeedBack WHERE fYear = '".$yr."' AND fType = '".$cat."'";
	$rs = $conn->Execute($sql);

	if ($rs->EOF) { //nodata
		return 0;
	}else{
		return $rs->fields[$col];
	}
}
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
			tra.tObjKind IN ("點交(結案)","解除契約")
			AND tra.tAccount IN ("'.$contractBank.'")
			AND cs.cScrivener="'.$sn.'"
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
		
		if ($tmp['cFeedbackTarget'] == 2 && $tmp['cCaseFeedback'] == 0) {
			$fbM += $tmp['cCaseFeedBackMoney'] ;

			if ($se == 3) {
				
			}
			
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
// ##
$smarty->assign('menu_year',$sales_year);
$smarty->assign('menu_season', array(
									'1' => '第一季', 
									'2' => '第二季',
									'3' => '第三季',
									'4' => '第四季'
									));
$smarty->assign('citys',$citys);
$smarty->display('searchPhone.inc.tpl', '', 'report');
?>