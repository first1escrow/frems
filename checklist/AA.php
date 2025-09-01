<?php
include_once '../openadodb.php' ;
$tVR_Code = '60001081489553';

$b = array() ;
	$sql = '
		SELECT 
			tVR_Code,
			tObjKind,
			tTxt,
			tMoney,
			tSeller, 
			tExport_time 
		FROM 
			tBankTrans 
		WHERE 
			tVR_Code="'.$tVR_Code.'" ;
	' ;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$tmpArray[] = $rs->fields;

		$rs->MoveNext();
	}

	for ($i=0; $i < count($tmpArray); $i++) { 
		$_money2 = (int)$tmpArray[$i]["tMoney"] ;
		$_total = $_money2 ;
		$_name = $tmpArray[$i]["tTxt"] ;
		
		$_y = substr($tmpArray[$i]["tExport_time"],0,4) ;
		$_m = substr($tmpArray[$i]["tExport_time"],5,2) ;
		$_d = substr($tmpArray[$i]["tExport_time"],8,2) ;
		$_date = $_y."/".$_m."/".$_d ;
		
		if ($tmpArray[$i]["tObjKind"] == '仲介服務費') {
			$_name = '' ;
			$_money2 = (int)$tmpArray[$i]['tSeller'] ;
		}
		
		if ($tmpArray[$i]["tObjKind"] == '扣繳稅款') {
			$sql = 'SELECT * FROM tExpenseDetail WHERE eCertifiedId="'.substr($tVR_Code,5,9).'";' ;

			$rs = $conn->Execute($sql);


			
			if ($rs->RecordCount() < 1) {
				$b[] = array(
						'date' => $_date,
						'money1' => '0',
						'money2' => $_money2,
						'kind' => $tmpArray[$i]["tObjKind"],
						'txt' => $_name,
						'expId' => $v['expId']
				) ;
			}
		}
		else if ($tmpArray[$i]["tObjKind"] != '調帳') {
			//主要入款紀錄
			$b[] = array(
					'date' => $_date,
					'money1' => '0',
					'money2' => $_money2,
					'kind' => $tmpArray[$i]["tObjKind"],
					'txt' => $_name,
					'expId' => $v['expId']
			) ;
			##
		}
	}

	print_r($b);
	
	// 寫入資料庫
	

#########

$sql = '
		SELECT
			id,
			eTradeDate,
			eDebit,
			eLender,
			eChangeMoney,
			eStatusIncome,
			eBuyerMoney,
			eExtraMoney,
			eDepAccount,
			(SELECT sName FROM tCategoryIncome WHERE sId=a.eStatusRemark) as sName,
			eRemarkContent
		FROM 
			tExpense AS a 
		WHERE 
			eDepAccount="00'.$tVR_Code.'" 
			AND eTradeStatus="0" 
			AND ePayTitle<>"網路整批" 
		ORDER BY
			eLastTime
		ASC;
	' ; //eTradeDate,eTradeNum
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$_money1 = (int)substr($rs->fields["eLender"],0,13) ; 									// 存入
		$_money2 = (int)substr($rs->fields["eDebit"],0,13) ; 									// 支出
		$_buyer = (int)substr($rs->fields["eBuyerMoney"],0,13) ; 								// 扣除買方服務費
		$_buyer2 = (int)$rs->fields['eExtraMoney'];
		$tmp_check = 0;//1 買方服務費  2買方溢入款
		if ($_buyer > 0) { $_money1 = $_money1 - $_buyer ; $tmp_check += 1;} //
		if ($_buyer2 > 0) { $_money1 = $_money1 - $_buyer2; $tmp_check += 2;}
		// echo $tmp_check."_";
		$_total = $_money1 - $_money2 ;
		$_y = substr($rs->fields["eTradeDate"],0,3) + 1911 ;
		$_m = substr($rs->fields["eTradeDate"],3,2) ;
		$_d = substr($rs->fields["eTradeDate"],5,2) ;
		$_date = $_y."/".$_m."/".$_d ;

		//if ($tmp["sName"] != '----' && $tmp["eStatusIncome"] !="3" ) { 				// kind 不明不顯示
		if ($rs->fields["eStatusIncome"] !="3") { 												// 調帳交易不顯示
			$arr[] =  array(
							'date' => $_date,
							'money1' => $_money1,
							'money2' => $_money2,
							'total' => $_total,
							'kind' => $rs->fields['sName'],
							'txt' => $rs->fields['eRemarkContent'],
							'expId' => $rs->fields['id'],
							'check' => $tmp_check
			) ;
		}
		
		$rs->MoveNext();
	}

	
	// die;
	//設定 tExpenseDetail 變更出款日期
	$sql = 'SELECT tExport_time FROM tBankTrans WHERE tVR_Code="'.$tVR_Code.'" AND tObjKind="扣繳稅款";' ;
	$rs = $conn->Execute($sql);

	$tmp_date = explode("-",substr($rs->fields['tExport_time'],0,10)) ;
	if (count($tmp_date) > 0) {
		$exp_date = implode('/',$tmp_date) ;
	}
	unset($tmp_date) ;
	##
	
	foreach ($arr as $k => $v) {
		//$totalM = 0 ;
		
		//取得明細部分買方分配總金額並將賣方入帳金額扣除買方支出
		$sql = 'SELECT SUM(eMoney) as M FROM tExpenseDetail WHERE eExpenseId="'.$v['expId'].'" AND eTarget="3"; ' ;
		$rs = $conn->Execute($sql);
		$v['money1'] -= (int)$rs->fields['M'] ;		//扣除買方明細加總金額
		unset($tmp) ;
		##
	
		//取出賣方明細部分出款
		$sql = '
			SELECT 
				*,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as kind,
				(SELECT tBankLoansDate FROM  tBankTrans WHERE tId=a.eOK) AS tBankLoansDate
			FROM 
				tExpenseDetail AS a
			WHERE 
				eExpenseId="'.$v['expId'].'" 
				AND eTarget="2";
		' ;
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$money2 = (int)$rs->fields['eMoney'] ;
			//$totalM += $money2 ;
			if (!$exp_date) {
				$exp_date = $v['date'] ;
			}

			$tmp_date = explode("-",substr($rs->fields['tBankLoansDate'],0,10));
			$rs->fields['tBankLoansDate'] =  $tmp_date[0]."/".$tmp_date[1]."/".$tmp_date[2];
			unset($tmp_date);
			
			$a[] = array(
						'date' => $rs->fields['tBankLoansDate'],
						'money1' => 0,
						'money2' => $money2,
						'kind' => $rs->fields['kind'],
						'expId' => $v['eExpenseId']
			) ;
			
			$rs->MoveNext();
		}

		
		##
		
		//主要入款紀錄
		$sql = "SELECT * FROM tExpenseDetailSms WHERE eExpenseId = '".$v['expId']."'";
		$rs = $conn->Execute($sql);

		$in_check = 0;
		if ($rs->fields['eSignMoney'] > 0) {
			$in_check = 1; //有輸入金額
			$a[] = array(
					'date' => $v['date'],
					'money1' => $rs->fields['eSignMoney'],
					'money2' => 0,
					'kind' => '簽約款',
					'txt' => '',
					'expId' => $v['expId']
			) ;
		}

		if ($rs->fields['eAffixMoney'] > 0) {
			$in_check = 1; //有輸入金額
			$a[] = array(
					'date' => $v['date'],
					'money1' => $rs->fields['eAffixMoney'],
					'money2' => 0,
					'kind' => '用印款',
					'txt' => '',
					'expId' => $v['expId']
			) ;
		}

		if ($rs->fields['eDutyMoney'] > 0) {
			$in_check = 1; //有輸入金額
			$a[] = array(
					'date' => $v['date'],
					'money1' => $rs->fields['eDutyMoney'],
					'money2' => 0,
					'kind' => '完稅款',
					'txt' => '',
					'expId' => $v['expId']
			) ;
		}

		if ($rs->fields['eEstimatedMoney'] >0) {
			$in_check = 1; //有輸入金額
			$a[] = array(
					'date' => $v['date'],
					'money1' => $rs->fields['eEstimatedMoney'],
					'money2' => 0,
					'kind' => '尾款',
					'txt' => '',
					'expId' => $v['expId']
			) ;
		}

		if ($rs->fields['eEstimatedMoney2'] > 0) {
			$in_check = 1; //有輸入金額
			$a[] = array(
					'date' => $v['date'],
					'money1' => $rs->fields['eEstimatedMoney2'],
					'money2' => 0,
					'kind' => '尾款差額',
					'txt' => '',
					'expId' => $v['expId']
			) ;
		}

		if ($rs->fields['eCompensationMoney'] > 0) { //
			$in_check = 1; //有輸入金額
			$a[] = array(
					'date' => $v['date'],
					'money1' => $rs->fields['eCompensationMoney'],
					'money2' => 0,
					'kind' => '代償後餘額',
					'txt' => '',
					'expId' => $v['expId']
			) ;
		}

		



		$sql = "SELECT * FROM tExpenseDetailSmsOther WHERE eExpenseId = '".$v['expId']."' AND eDel = 0";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$in_check = 1; //有輸入金額
			if (!preg_match("/買方應付款項/", $rs->fields['eTitle'])&&!preg_match("/買方預收款項/", $rs->fields['eTitle'])&&!preg_match("/買方履保費/", $rs->fields['eTitle']) &&!preg_match("/契稅/", $rs->fields['eTitle'])&&!preg_match("/印花稅/", $rs->fields['eTitle'])) {
				$a[] = array(
					'date' => $v['date'],
					'money1' => $rs->fields['eMoney'],
					'money2' => 0,
					'kind' => $rs->fields['eTitle'],
					'txt' => '',
					'expId' => $v['expId']
				) ;
			}

			$rs->MoveNext();
		}

		

		unset($tmp,$tmp2);

		if ($in_check == 0) {
			$tmp = explode('+', $v['txt']);
			// print_r($tmp);
			for ($i=0; $i < count($tmp); $i++) { 
				if ($v['check'] == 1) {//1 買方服務費  2買方溢入款
				

					if (preg_match("/買方/", $tmp[$i]) && preg_match("/服務費/", $tmp[$i])) {
						unset($tmp[$i]);
					}
				}elseif ($v['check'] == 2) {
					if (preg_match("/買方溢入款/", $tmp[$i])) {
						unset($tmp[$i]);
					}
				}elseif ($v['check'] == 3) {

					if (preg_match("/買方/", $tmp[$i]) && preg_match("/服務費/", $tmp[$i])) {
						unset($tmp[$i]);
					}else if (preg_match("/買方溢入款/", $tmp[$i])) {
						unset($tmp[$i]);
					}
				}
			}
			// sort($tmp);
			if ($v['txt'] !='') {
				$v['txt'] = @implode('+', $tmp);
			}
			
			unset($tmp);
			// asort($a);
			$a[] = array(
				'date' => $v['date'],
				'money1' => $v['money1'],
				'money2' => $v['money2'],
				'kind' => $v['kind'],
				'txt' => $v['txt'],
				'expId' => $v['expId']
			) ;
		}
		unset($in_check);
		
		##
	}
	unset($arr) ;


	print_r($a);

?>