<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../report/getBranchType.php';

$_POST = escapeStr($_POST) ;
// $sDate = (substr($_POST['sDate'], 0,3)+1911).substr($_POST['sDate'], 3);
// $eDate = (substr($_POST['eDate'], 0,3)+1911).substr($_POST['eDate'], 3);
$sDate = date('Y-m')."-01";
$eDate = date('Y-m-d');

##
//因有未結案先行出履保費所以增加條件[tra.tObjKind = "其他" AND tKind="保證費"]20151113
$sql = 'SELECT cBankAccount,cBankCode FROM tContractBank WHERE cOrder > 0' ;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$bank_acc[] = $rs->fields['cBankAccount'];
	$bank_code[] = $rs->fields['cBankCode'];
	$rs->MoveNext();
}

$bankAccStr .= '"'.implode('","', $bank_acc).'"' ;
$bankCodeStr .= implode(',', $bank_code) ;
unset($bank_acc);unset($bank_code);

// if ($_POST) {
	$data['sinopacCase'] = 0; //永豐
	$data['firstCase'] = 0;//一銀
	$data['taishinCase'] = 0;//台新
	// $data['bankCase'] = 0;//銀行總件數
	$data['otherCase'] = 0;//其他品牌+非仲介
	$data['sinopacMoney'] = 0; //永豐餘額
	$data['firstMoney'] = 0;//一銀餘額
	$data['taishinMoney'] = 0;//台新餘額
	// $data['bankMoney'] = 0;//總餘額
	$data['MonthTotalMoney'] = 0; //當月總合計
	$data['MonthTotalCount'] = 0; //當月結案數
	####
	//簽約件數
	$sql = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ; //005030342 電子合約書測試用沒有刪的樣子
	if ($sql) { $sql .= " AND " ; }
	$sql .= ' cas.cCaseStatus<>"8" ' ;
	if ($sql) { $sql .= " AND " ; }
	$sql .= '( cas.cSignDate >= "'.$sDate.' 00:00:00" AND cas.cSignDate <= "'.$eDate.' 23:59:59")' ;

	$sql ='
	SELECT 
		cas.cCertifiedId as CertifiedId, 
		cas.cBank as bank,
		rea.cBrand as brand,
		rea.cBrand1 as brand1,
		rea.cBrand2 as brand2,
		rea.cBrand2 as brand3,
		rea.cBranchNum as branch,
		rea.cBranchNum1 as branch1,
		rea.cBranchNum2 as branch2,
		rea.cBranchNum3 as branch3
	FROM 
		tContractCase AS cas 
	LEFT JOIN 
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
	WHERE
	'.$sql.' 
	GROUP BY
		cas.cCertifiedId
	;
	' ;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$type = branch_type($conn,$rs->fields);
		if($type == 'O' || $type == '3'){ //他牌+非仲
			$data['otherCase']++ ;

		}

		if ($rs->fields['bank'] == 8) {
			$data['firstCase']++;
		}elseif ($rs->fields['bank'] == 77 || $rs->fields['bank'] == 80) {
			$data['sinopacCase']++;
		}elseif ($rs->fields['bank'] == 68) {
			$data['taishinCase']++;
		}

		unset($type);
		$rs->MoveNext();
	}
	###
	//買賣總價金
	$sql = "SELECT SUM(cTotalMoney) AS totalMoney FROM tContractIncome";
	$rs = $conn->Execute($sql);
	$data['totalMoney'] = $rs->fields['totalMoney'];
	######
	//餘額
	//合約銀行基本資料
	$_all_balance = 0 ;
	$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cBankMain,cId DESC;' ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$conBank[] = $rs->fields ;
		// $_all_balance += $rs->fields['cBankBalance'] + 1 - 1 ;				//所有銀行調帳金額加總
		$rs->MoveNext() ;
	}
	unset($rs) ;

	// //所有保證號碼餘額金額統計
	// $s = 'SELECT  SUM(cCaseMoney) AS total_money FROM tContractOwner AS A ,tContractBuyer AS B , tContractCase AS C WHERE A.cCertifiedId = B.cCertifiedId AND A.cCertifiedId=C.cCertifiedId AND cCaseMoney > 0 ;' ;
	// $s_total = $conn->Execute($s) ;
	// $_now_total = $s_total->fields["total_money"] ;							//所有建經案件總金額(未加利息)
	##


	//總利息-總所得稅-利息出款之總金額
	$sqlx = 'SELECT * FROM tExpense WHERE eTradeCode IN ("1912","1920","1560","1785");' ;
	$rsx = $conn->Execute($sqlx) ;
	while (!$rsx->EOF) {
		$_eLender = (int)substr($rsx->fields["eLender"],0,-2) ;
		$_eDebit = (int)substr($rsx->fields["eDebit"],0,-2) ;
		// $_t_money = $_t_money + $_eLender - $_eDebit ;

		
		$bankInt[$rsx->fields['eAccount']] += ($_eLender - $_eDebit); //cBankTrustAccount
		
		$rsx->MoveNext() ;
	}  
	// $_now_total = $_now_total + $_t_money + $_all_balance + 1 - 1 ; 		//所有建經案件總金額(加入利息、加入所有銀行調帳金額)

	//各合約銀行銀行端與建經系統端餘額資料
	$bank_total = 0 ;
	for ($i = 0 ; $i < count($conBank) ; $i ++) {
		$bMoney = 0; //計算銀行金額
		##
		
		//各銀行(分行)相關帳務金額
		$sql = '
			SELECT 
				SUM(cCaseMoney) as total_money 
			FROM 
				tContractCase
			WHERE 
				cBank="'.$conBank[$i]['cBankCode'].'" 
				AND cCaseMoney > 0 ;
		' ;
		
		$rs = $conn->Execute($sql) ;

		$bMoney = $rs->fields['total_money']+$bankInt[$conBank[$i]['cBankTrustAccount']]+$conBank[$i]['cBankBalance'];//計算後台總金額

		if ($conBank[$i]['cBankCode'] == 8) {
			$data['firstMoney'] = $bMoney ;		
		}elseif ($conBank[$i]['cBankCode'] == 77 || $conBank[$i]['cBankCode'] == 80) {
			$data['sinopacMoney'] += $bMoney ;		
		}elseif ($conBank[$i]['cBankCode'] == 68) {
			$data['taishinMoney'] = $bMoney ;		
		}

		
		
	}
	######
	//結案數 419464 ->代墊回存  //436979 -> 不能計入保證費

	$sql = '
	SELECT 
		SUM(tra.tMoney)	AS total,
		COUNT(tra.tMoney) AS count
	FROM 
		tBankTrans AS tra 
	WHERE 
		tra.tExport="1"
		AND tra.tPayOk="1"
		AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
		AND ((tra.tObjKind IN ("點交(結案)","解除契約","建經發函終止")) OR (tra.tObjKind = "其他" AND tKind="保證費"))
		AND tra.tAccount IN('.$bankAccStr .')
		AND
		(tra.tBankLoansDate>="'.$sDate.'" AND tra.tBankLoansDate<="'.$eDate.'") AND tra.tId NOT IN(419464,436979,451978);' ;
		
		
	
	$rs = $conn->Execute($sql);
	// echo $rs->fields['total'];
	// print_r($rs);
	// 當月總合計($$$)
	$data['MonthTotalMoney'] = (int)$rs->fields['total'];
	$data['MonthTotalCount'] = (int)$rs->fields['count'];
	##
	//無履保費出款但有出利息
	$sql = '
		SELECT
			COUNT(cas.cCertifiedId) AS count,
			(SELECT cInterestMoney FROM tContractBuyer WHERE cCertifiedId = cas.cCertifiedId) AS bInterestMoney,
			(SELECT cInterestMoney FROM tContractOwner WHERE cCertifiedId = cas.cCertifiedId) AS oInterestMoney,
			(SELECT SUM(cInterestMoney) FROM tContractOthers WHERE cCertifiedId = cas.cCertifiedId GROUP BY cCertifiedId) AS otherInterestMoney,
			(SELECT SUM(cInterestMoney) FROM tContractInterestExt WHERE cCertifiedId = cas.cCertifiedId) AS exInterestMoney
		FROM
			tContractCase AS cas
		WHERE
			cas.cBankList>="'.$sDate.'"
			AND cas.cBankList<="'.$eDate.'"
			AND cas.cBankList<>""
			AND cas.cBank IN ('.$bankCodeStr.')
			AND cas.cCertifiedId NOT IN("060316077");' ;

		// 	if ($_SESSION['member_id'] == 6) {
		// 	echo $sql;
		// }
		
	$rs = $conn->Execute($sql);
	$data['MonthTotalCount'] += (int)$rs->fields['count'];

	$data['MonthTotalCount']++;//060316077 數量要算 

	$data['MonthTotalMoney'] -= ($rs->fields['bInterestMoney']+$rs->fields['oInterestMoney']+$rs->fields['otherInterestMoney']+$rs->fields['exInterestMoney']);

	if (date('Y-m') == '2019-12') {
		$data['MonthTotalMoney'] -= 1737;//出了履保費後才要利息，不算代墊所以只能直接扣除20191205 保證號碼: 007005261

		$data['MonthTotalMoney'] -=4595;//060316077 實際扣了4595
	}
	
	

	##
	$extraMoney = $data['MonthTotalMoney'];//誤入金額


	
	
// }


//



// echo $sql;
##
// $smarty->assign("sDate",$_POST['sDate']);
// $smarty->assign("eDate",$_POST['eDate']);

$smarty->assign("sDate",$sDate);
$smarty->assign("eDate",$eDate);
$smarty->assign("data",$data);
$smarty->display('totalReport.inc.tpl', '', 'report');
?>