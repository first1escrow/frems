<?php
include_once '../../configs/config.class.php';
include_once '../../openadodb.php' ;
include_once '../../web_addr.php' ;
include_once '../../session_check.php' ;


$radiokind = $_POST["radiokind"];
$vr_code = $_POST["vr_code"];
$_target_len = count($radiokind);
$_vr_code = substr($vr_code,5);
$smsSend = $_POST['smsSend'];
// echo $smsSend;
##
//仲介服務費處理
if ($radiokind == '買方仲介服務費') {
	$checkIden = 3; 
	$radiokind = '仲介服務費';
}elseif ($radiokind == '賣方仲介服務費') {
	$checkIden = 2;
	$radiokind = '仲介服務費';
}
##
//儲存自訂對象
if ($smsSend == 2 && $_POST["save"] != 'ok') {
	$allForm = $_POST['allForm'];

	$ra = ($radiokind == '點交')?'點交(結案)':$radiokind;


	// echo $ra;

	//清除之前設定對象
	$sql = "UPDATE tBankTranSms SET bDel = 1 WHERE bVR_Code = '".$_POST["vr_code"]."' AND bObjKind = '".$ra."' AND bBankTranId = ''";
	// echo $sql;
	$conn->Execute($sql);

	for ($i=0; $i < count($allForm); $i++) { 
		$tmp = explode('_', $allForm[$i]);
		
		$sql = "INSERT INTO
					tBankTranSms
				SET
					bVR_Code = '".$_POST["vr_code"]."',
					bObjKind = '".$ra."',
					bIden = '".$tmp[0]."',
					bName = '".$tmp[1]."',
					bMobile = '".$tmp[2]."',
					bCreatTime = '".date("Y-m-d H:i:s")."',
					bStoreId = '".$tmp[3]."'
					";
		// echo $sql."<br>";
		$conn->Execute($sql);
		unset($tmp);
	}

	unset($ra);
}



$realtyTarget = array(1 => '(買賣方)', 2 => '(賣方)', 3 => '(買方)') ;



##
// 取得合約銀行
$_vr_bank = substr($vr_code,0,5) ;
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" AND cBankVR LIKE "'.$_vr_bank.'%" ORDER BY cId ASC;' ;

$rs = $conn->Execute($sql) ;
$conBank = $rs->fields ;

$_vr_bank = $conBank['cBankName'] ;				//銀行簡稱
$bank_no = $conBank['cBankMain'] ;				//銀行總行代碼
$main_bank = $conBank['cBankMain'] ;			//銀行總行代碼
$branch_bank = $conBank['cBankBranch'] ;		//銀行分行代碼

$branch = '' ;
if ($conBank['cBankMain'] == '807') {
	$branch = $conBank['cBranchFullName'] ;
	$branch_bank = '1044' ;						//銀行分行代碼(永豐共用相同活儲帳戶)
}

$_account_name = $conBank['cAccountName'] ;		//銀行活儲帳戶
$_account_no = $conBank['cBankAccount'] ;		//銀行活儲帳號
##

$save = $_POST["save"];
$moneyOK = true;

if ($save == 'ok') {
	
	//$conn->debug =1;
	$bk = $_POST["bk"];
	//$vr_code = $_POST["vr_code"];
	
	$bank_kind = $_POST["bank_kind"];
	$target = $_POST["target"];
	$export = $_POST["export"];
	$code2 = $_POST['code2'];
	$bank3 = $_POST["bank3"] ;
	$bank4 =$_POST["bank4"];	
	$t_name = $_POST["t_name"];
	$t_buyer = $_POST["t_buyer"];
	$t_seller = $_POST["t_seller"];
	$t_account = $_POST["t_account"];
	$t_cost = $_POST["t_cost"];
	$t_money = $_POST["t_money"];
	$t_txt = $_POST["t_txt"];
	$pid = $_POST["pid"];
	$objKind = $_POST["objKind"];
	$objKind2 = $_POST['taxScrivener'];
	$email = $_POST["email"];
	$fax = $_POST["fax"];
	$change_s = $_POST["change_s"]; //調整專用 - 記錄入帳表之id
	$replace_patt = array("\r\n","\n","\r") ;
	$send = $_POST["tSend"];
	$showTxt = $_POST['bankshowtxt'];//ALTER TABLE  `tBankTrans` ADD  `tBankShowTxt` VARCHAR( 100 ) NOT NULL COMMENT  '存摺顯示文字' AFTER  `tPayTxt`
	$taxPayId = $_POST['taxPayId'];
	$storeId = $_POST['storeId'];
	$taxReturnPayId = $_POST['taxReturnPayId'];
	// $scrivenerNote = $_POST['scrivenerNote'];

	##檢查出款金額是否超過餘額
	for ($i=0; $i < count($_POST["t_money"]); $i++) { 
		$checkTranMoney += $_POST["t_money"][$i];
	}

	$_total = count($export);
	
	$idArr =array();
	
	for ($i=0;$i<$_total;$i++) { //tStoreId
		if ($_SESSION["member_name"] <> "") {
			$record["tOwner"] = $_SESSION["member_name"];
		}
		$record["tVR_Code"] = $vr_code;
		$record["tBank_kind"] = $bank_kind;
		$record["tCode"] = $export[$i];
		$record['tCode2'] = $code2[$i];
		$record["tKind"] = $target[$i] ;
		$record["tObjKind"] = $objKind[$i];

		if ($objKind2[$i] != '') {
			$record["tObjKind2"] = $objKind2[$i];
		}else{
			if ($record["tBank_kind"] == '台新' && $record["tCode"] == '03') {
				$record["tObjKind2"] = '03';
			}
		}

		
		$bank = $bank3[$i] . $bank4[$i];		
		$record["tBankCode"] = $bank;
		$record["tBuyer"] = $t_buyer[$i];
		$record["tSeller"] = $t_seller[$i];
		$record["tAccount"] = trim($t_account[$i]);
		$record["tAccountName"] = trim($t_name[$i]);
		$record["tAccountId"] = $pid[$i];
		$record["tStoreId"] = $storeId[$i];
		
		$money = $t_cost[$i] + $t_money[$i];	
		$record["tMoney"] = $money;
		
		$record["tEmail"] = $email[$i];
		$record["tFax"] = $fax[$i];
		
		$serial = substr($vr_code,5);
		$record["tMemo"] = $serial;
		
		$record["tChangeExpense"] = $change_s[$i];
		$record['tBankShowTxt'] = $showTxt[$i];

		if ($send[$i]=='') {
			$send[$i] = 0;
		}

		$record['tSend'] = $send[$i];
		// print_r($)

		$t_txt[$i] = str_replace($replace_patt,"",$t_txt[$i]) ;
		
		if ($bank_kind=="遠東") {
			$record["tTxt"] = $vr_code ." ".$t_txt[$i];
		} else if ($bank_kind=="台中銀") {
			$record["tTxt"] = $vr_code ." ".$t_txt[$i];
		} else {
			$record["tTxt"] = $t_txt[$i];
		}
		
		if ($record['tBankShowTxt'] == null) {
			$record['tBankShowTxt'] = '';
		}
		if ($_POST['datepicker'.$i]) {
			$record['tObjKind2Date'] = $_POST['datepicker'.$i];
		}

		
		
		
		$order_id = '' ;
		if ($t_money[$i] <> '' && $moneyOK) {
			// echo 

			$result = $conn->AutoExecute("tBankTrans", $record, 'INSERT');
			
			
			$order_id = $conn->Insert_ID();

			$idArr[] = $order_id;

			// echo $order_id."_";
			
			//更新明細紀錄, 變更為已出款(由於扣繳稅款只會單筆出款，所以忽略迴圈次數，當作單次處理)
			if ($taxPayId) {
				
				$taxArr = explode('_',$taxPayId) ;
				foreach ($taxArr as $k => $v) {
					$sql = 'UPDATE tExpenseDetail SET eOK="'.$order_id.'" WHERE eId="'.$v.'";' ;
					
					
					$conn->Execute($sql) ;
				}
			}
			
			

			##
			//回寫申請代墊
			if ($taxReturnPayId) {
				$taxR = explode('_', $taxReturnPayId);
				foreach ($taxR as $k => $v) {
					$sql = "UPDATE tBankTrans SET tObjKind2Item = '".$order_id."',tShow =0 WHERE tId = '".$v."'";
					// echo $sql;
					$conn->Execute($sql);
				}
			}
			//02返還公司代墊 05需帳出款至公司
			if ($record["tObjKind2"] == '02' || $record["tObjKind2"] == '04') {
				$sql = "UPDATE tBankTrans SET tShow = 0 WHERE tId = '".$order_id."'";
					// echo $sql;
				$conn->Execute($sql);
			}

			//

			
			

		}

		

		
		// echo "<pre>";
		// print_r($record);
		unset($record);
		$ok=1;

		
		
	}
//find_in_set

	if ($moneyOK) {

			if ($_POST['smsSend'] == 2) {
				$sql = "UPDATE tBankTranSms SET bBankTranId = '".@implode(',', $idArr)."' WHERE bVR_Code = '".$vr_code."' AND bObjKind = '".$objKind[0]."' AND bDel = 0 AND bBankTranId = ''";
				// echo $sql."<br>";
				$conn->Execute($sql);
			}
			
		
			header("Location: ../list2.php?ok=1");
	}
	
}
$sql = '
	SELECT
		a.cCertifiedId,
		a.cCaseMoney,
		b.cBankKey2 as o_bk1,
		b.cBankBranch2 as o_bk2,
		b.cBankAccName as o_bkname,
		b.cBankAccNumber as o_bknumber,
		b.cName as owner,
		b.cChecklistBank as o_ChecklistBank,
		c.cBankKey2 as b_bk1,
		c.cBankBranch2 as b_bk2,
		c.cBankAccName as b_bkname,
		c.cBankAccNumber as b_bknumber,
		c.cName as buyer,
		c.cChecklistBank as b_ChecklistBank,
		d.cBranchNum as cBranchNum,
		d.cBranchNum1 as cBranchNum1,
		d.cBranchNum2 as cBranchNum2,
		d.cBranchNum3 as cBranchNum3,
		d.cServiceTarget as cServiceTarget,
		d.cServiceTarget1 as cServiceTarget1,
		d.cServiceTarget2 as cServiceTarget2,
		d.cServiceTarget3 as cServiceTarget3,
		f.sAccountNum1,
		f.sAccountNum2,
		f.sAccount3,
		f.sAccount4,
		f.sAccountNum11,
		f.sAccountNum21,
		f.sAccount31,
		f.sAccount41,
		f.sAccountNum12,
		f.sAccountNum22,
		f.sAccount32,
		f.sAccount42,
		f.sEmail, 
		f.sAccountUnused,
		f.sAccountUnused1,
		f.sAccountUnused2,
		(SELECT bName FROM tBrand WHERE bId = g.bBrand) AS bBrand,
		g.bName,
		g.bStore,
		g.bAccountNum1,
		g.bAccountNum2,
		g.bAccount3,
		g.bAccount4,
		g.bAccountNum11,
		g.bAccountNum21,
		g.bAccount31,
		g.bAccount41,
		g.bAccountNum12,
		g.bAccountNum22,
		g.bAccount32,
		g.bAccount42,
		g.bAccountNum13,
		g.bAccountNum23,
		g.bAccount33,
		g.bAccount43,
		g.bAccountUnused,
		g.bAccountUnused1,
		g.bAccountUnused2,
		g.bAccountUnused3,
		CONCAT(g.bFaxArea,g.bFaxMain) as store_fax, 
		g.bEmail as store_email, 
		(SELECT bName FROM tBrand WHERE bId = h.bBrand) AS bBrandA,
		h.bName AS bNameA,
		h.bStore as bStoreA,
		h.bAccountNum1 as bAccountNum1A,
		h.bAccountNum2 as bAccountNum2A,
		h.bAccount3 as bAccount3A,
		h.bAccount4 as bAccount4A,
		h.bAccountNum11 as bAccountNum11A,
		h.bAccountNum21 as bAccountNum21A,
		h.bAccount31 as bAccount31A,
		h.bAccount41 as bAccount41A,
		h.bAccountNum12 as bAccountNum12A,
		h.bAccountNum22 as bAccountNum22A,
		h.bAccount32 as bAccount32A,
		h.bAccount42 as bAccount42A,
		h.bAccountNum13 as bAccountNum13A,
		h.bAccountNum23 as bAccountNum23A,
		h.bAccount33 as bAccount33A,
		h.bAccount43 as bAccount43A,
		h.bAccountUnused as bAccountUnusedA,
		h.bAccountUnused1 as bAccountUnused1A,
		h.bAccountUnused2 as bAccountUnused2A,
		h.bAccountUnused2 as bAccountUnused3A,
		CONCAT(h.bFaxArea,h.bFaxMain) as store_faxA, 
		h.bEmail as store_emailA,
		(SELECT bName FROM tBrand WHERE bId = i.bBrand) AS bBrandB,
		i.bName AS bNameB,
		i.bStore as bStoreB,
		i.bAccountNum1 as bAccountNum1B,
		i.bAccountNum2 as bAccountNum2B,
		i.bAccount3 as bAccount3B,
		i.bAccount4 as bAccount4B,
		i.bAccountNum11 as bAccountNum11B,
		i.bAccountNum21 as bAccountNum21B,
		i.bAccount31 as bAccount31B,
		i.bAccount41 as bAccount41B,
		i.bAccountNum12 as bAccountNum12B,
		i.bAccountNum22 as bAccountNum22B,
		i.bAccount32 as bAccount32B,
		i.bAccount42 as bAccount42B,
		i.bAccountNum13 as bAccountNum13B,
		i.bAccountNum23 as bAccountNum23B,
		i.bAccount33 as bAccount33B,
		i.bAccount43 as bAccount43B,
		i.bAccountUnused as bAccountUnusedB,
		i.bAccountUnused1 as bAccountUnused1B,
		i.bAccountUnused2 as bAccountUnused2B,
		i.bAccountUnused3 as bAccountUnused3B,
		CONCAT(i.bFaxArea,i.bFaxMain) as store_faxB, 
		i.bEmail as store_emailB,
		(SELECT bName FROM tBrand WHERE bId = j.bBrand) AS bBrandC,
		j.bName AS bNameC,
		j.bStore as bStoreC,
		j.bAccountNum1 as bAccountNum1C,
		j.bAccountNum2 as bAccountNum2C,
		j.bAccount3 as bAccount3C,
		j.bAccount4 as bAccount4C,
		j.bAccountNum11 as bAccountNum11C,
		j.bAccountNum21 as bAccountNum21C,
		j.bAccount31 as bAccount31C,
		j.bAccount41 as bAccount41C,
		j.bAccountNum12 as bAccountNum12C,
		j.bAccountNum22 as bAccountNum22C,
		j.bAccount32 as bAccount32C,
		j.bAccount42 as bAccount42C,
		j.bAccountNum13 as bAccountNum13C,
		j.bAccountNum23 as bAccountNum23C,
		j.bAccount33 as bAccount33C,
		j.bAccount43 as bAccount43C,
		j.bAccountUnused as bAccountUnusedC,
		j.bAccountUnused1 as bAccountUnused1C,
		j.bAccountUnused2 as bAccountUnused2C,
		j.bAccountUnused3 as bAccountUnused3C,
		CONCAT(j.bFaxArea,j.bFaxMain) as store_faxC, 
		j.bEmail as store_emailC,
		ci.cCertifiedMoney,
		ci.cTotalMoney,
		ci.cCommitmentMoney,
		e.cScrivener
	FROM
		tContractCase AS a
	LEFT JOIN 
		tContractOwner AS b ON a.cCertifiedId = b.cCertifiedId
	LEFT JOIN 
		tContractBuyer AS c ON a.cCertifiedId = c.cCertifiedId
	LEFT JOIN 
		tContractRealestate AS d ON a.cCertifiedId = d.cCertifyId
	LEFT JOIN 
		tContractScrivener AS e ON a.cCertifiedId = e.cCertifiedId
	LEFT JOIN 
		tScrivener AS f ON e.cScrivener = f.sId
	LEFT JOIN 
		tBranch AS g ON d.cBranchNum = g.bId 
	LEFT JOIN 
		tBranch AS h ON d.cBranchNum1 = h.bId
	LEFT JOIN 
		tBranch AS i ON d.cBranchNum2 = i.bId
	LEFT JOIN 
		tBranch AS j ON d.cBranchNum3 = j.bId
	LEFT JOIN
	    tContractIncome AS ci ON a.cCertifiedId=ci.cCertifiedId
	WHERE 
		a.cCertifiedId="'.$_vr_code.'"
;' ;
// echo 'sql='.$sql;
$rs = $conn->Execute($sql) ;

$total_money = $rs->fields['cTotalMoney'];
$CertifiedMoney = $rs->fields['cCertifiedMoney'];
$CommitmentMoney = $rs->fields['cCommitmentMoney'];
##賣方
//主賣方
$owner = mb_substr(n_to_w(trim($rs->fields['owner'])), 0,9);
$ownerArr[] = $rs->fields['owner'];//比對身分用

$ownerBankCount = 0;
if ($rs->fields["o_ChecklistBank"] == 0) {
	$ownerBankNameArr[] = trim($rs->fields["o_bkname"]);//比對戶名用

	$ownerBank[$ownerBankCount]['bank'] = trim($rs->fields["o_bk1"]);
	$ownerBank[$ownerBankCount]['bankBranch'] = trim($rs->fields["o_bk2"]);
	$ownerBank[$ownerBankCount]['bankAccName'] = trim($rs->fields["o_bkname"]);
	$ownerBank[$ownerBankCount]['bankAccNum'] = trim($rs->fields["o_bknumber"]);
	$ownerBankCount++;
}

##

##買方
//主買方
$buyer = mb_substr(n_to_w(trim($rs->fields['buyer'])), 0,9);
$buyerBankCount = 0;
if ($rs->fields["b_ChecklistBank"] == 0) {

	$buyerBank[$buyerBankCount]['bank'] = trim($rs->fields["b_bk1"]);
	$buyerBank[$buyerBankCount]['bankBranch'] = trim($rs->fields["b_bk2"]);
	$buyerBank[$buyerBankCount]['bankAccName'] = trim($rs->fields["b_bkname"]);
	$buyerBank[$buyerBankCount]['bankAccNum'] = trim($rs->fields["b_bknumber"]);
	// $buyerBank[$buyerBankCount]['checkBank'] = trim($rs->fields["b_ChecklistBank"]);
	$buyerBankCount++;
}


//其他買方
$sql = '
	SELECT
		cName,
		cIdentity,
		cBankAccName,
		cBankAccNum,
		cBankMain,
		cBankBranch,
		cChecklistBank
	FROM
		tContractOthers
	WHERE
		cCertifiedId="'.$_vr_code.'"
		AND cChecklistBank = 0
		AND (cIdentity="1" OR cIdentity="2")
	ORDER BY
		cId
	ASC;
' ;
$rsB = $conn->Execute($sql) ;
while (!$rsB->EOF) {

	if ($rsB->fields['cIdentity'] == 2) {
		$ownerArr[] = $rsB->fields['cName'];//比對身分用
		// $OtherOwner[] = $rsB->fields ;
		$ownerBank[$ownerBankCount]['bank'] = trim($rsB->fields["cBankMain"]);
		$ownerBank[$ownerBankCount]['bankBranch'] = trim($rsB->fields["cBankBranch"]);
		$ownerBank[$ownerBankCount]['bankAccName'] = trim($rsB->fields["cBankAccName"]);
		$ownerBank[$ownerBankCount]['bankAccNum'] = trim($rsB->fields["cBankAccNum"]);
		// $ownerBank[$ownerBankCount]['checkBank'] = trim($rsB->fields["cChecklistBank"]);
		$ownerBankCount++;
	}else if($rsB->fields['cIdentity'] == 1){
		$buyerBank[$buyerBankCount]['bank'] = trim($rsB->fields["cBankMain"]);
		$buyerBank[$buyerBankCount]['bankBranch'] = trim($rsB->fields["cBankBranch"]);
		$buyerBank[$buyerBankCount]['bankAccName'] = trim($rsB->fields["cBankAccName"]);
		$buyerBank[$buyerBankCount]['bankAccNum'] = trim($rsB->fields["cBankAccNum"]);
		// $buyerBank[$buyerBankCount]['checkBank'] = trim($rsB->fields["cChecklistBank"]);
		$buyerBankCount++;
	}
	

	
	$rsB->MoveNext() ;
}

##

//代書
$scrivenerBankCount = 0;
$_s_email = trim($rs->fields["sEmail"]);

if ($rs->fields['sAccountUnused'] != 1 && $rs->fields["sAccountNum1"]) {
	$scrivenerBank[$scrivenerBankCount]['bank'] = trim($rs->fields["sAccountNum1"]);
	$scrivenerBank[$scrivenerBankCount]['bankBranch'] = trim($rs->fields["sAccountNum2"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs->fields["sAccount4"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccNum'] = trim($rs->fields["sAccount3"]);
	$scrivenerBankCount++;
}

if ($rs->fields['sAccountUnused1'] != 1 && $rs->fields["sAccountNum11"]) {
	$scrivenerBank[$scrivenerBankCount]['bank'] = trim($rs->fields["sAccountNum11"]);
	$scrivenerBank[$scrivenerBankCount]['bankBranch'] = trim($rs->fields["sAccountNum21"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs->fields["sAccount41"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccNum'] = trim($rs->fields["sAccount31"]);
	$scrivenerBankCount++;
}

if ($rs->fields['sAccountUnused2'] != 1 && $rs->fields["sAccountNum12"]) {
	$scrivenerBank[$scrivenerBankCount]['bank'] = trim($rs->fields["sAccountNum12"]);
	$scrivenerBank[$scrivenerBankCount]['bankBranch'] = trim($rs->fields["sAccountNum22"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs->fields["sAccount42"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccNum'] = trim($rs->fields["sAccount32"]);
	$scrivenerBankCount++;
}



$sql = "SELECT * FROM tScrivenerBank WHERE sUnUsed  = 0 AND sScrivener ='".$rs->fields['cScrivener']."'";
$rs_ss = $conn->Execute($sql);

while (!$rs_ss->EOF) {
	$scrivenerBank[$scrivenerBankCount]['bank'] = trim($rs_ss->fields["sBankMain"]);
	$scrivenerBank[$scrivenerBankCount]['bankBranch'] = trim($rs_ss->fields["sBankBranch"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccName'] = trim($rs_ss->fields["sBankAccountName"]);
	$scrivenerBank[$scrivenerBankCount]['bankAccNum'] = trim($rs_ss->fields["sBankAccountNo"]);
	$scrivenerBankCount++;

	$rs_ss->MoveNext();
}
##

//第一家仲介
$storeName = $rs->fields['bBrand']."_".$rs->fields['bStore']."_".$rs->fields['bName'];//店名
$storeId = $rs->fields['cBranchNum'];
$_store_fax = trim($rs->fields["store_fax"]);
$_store_email = trim($rs->fields["store_email"]);
$_store_target = '' ;
if ($rs->fields['cBranchNum'] > 0) {
	$_store_target = trim($rs->fields['cServiceTarget']) ;
}
$branchBankCount = 0;

if ($rs->fields['bAccountUnused'] != 1 && $rs->fields["bAccountNum1"]) {

	$branchBank[$branchBankCount]['bank'] = trim($rs->fields["bAccountNum1"]);// 店家 (第一家總行)
	$branchBank[$branchBankCount]['bankBranch'] = trim($rs->fields["bAccountNum2"]);// (第一家分行)
	$branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount4"]);// (第一家戶名)
	$branchBank[$branchBankCount]['bankAccNum'] = trim($rs->fields["bAccount3"]);// (第一家帳號)
	$branchBankCount++;
}

if ($rs->fields['bAccountUnused1'] != 1 && $rs->fields["bAccountNum11"]) {
	$branchBank[$branchBankCount]['bank'] = trim($rs->fields["bAccountNum11"]);// 店家 (第一家總行)
	$branchBank[$branchBankCount]['bankBranch'] = trim($rs->fields["bAccountNum21"]);// (第一家分行)
	$branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount41"]);// (第一家戶名)
	$branchBank[$branchBankCount]['bankAccNum'] = trim($rs->fields["bAccount31"]);// (第一家帳號)
	$branchBankCount++;	
}

if ($rs->fields['bAccountUnused2'] != 1 && $rs->fields["bAccountNum12"]) {
	$branchBank[$branchBankCount]['bank'] = trim($rs->fields["bAccountNum12"]);// 店家 (第一家總行)
	$branchBank[$branchBankCount]['bankBranch'] = trim($rs->fields["bAccountNum22"]);// (第一家分行)
	$branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount42"]);// (第一家戶名)
	$branchBank[$branchBankCount]['bankAccNum'] = trim($rs->fields["bAccount32"]);// (第一家帳號)
	$branchBankCount++;	

	
}

if ($rs->fields['bAccountUnused3'] != 1 && $rs->fields["bAccountNum13"]) {
	$branchBank[$branchBankCount]['bank'] = trim($rs->fields["bAccountNum13"]);// 店家 (第一家總行)
	$branchBank[$branchBankCount]['bankBranch'] = trim($rs->fields["bAccountNum23"]);// (第一家分行)
	$branchBank[$branchBankCount]['bankAccName'] = trim($rs->fields["bAccount43"]);// (第一家戶名)
	$branchBank[$branchBankCount]['bankAccNum'] = trim($rs->fields["bAccount33"]);// (第一家帳號)
	$branchBankCount++;	
	
	
}


##

//第二家房仲介(A)
$storeName1 = $rs->fields['bBrandA']."_".$rs->fields['bStoreA']."_".$rs->fields['bNameA'];//店名
$storeId1 = $rs->fields['cBranchNum1'];
$_store_faxA = trim($rs->fields["store_faxA"]);
$_store_emailA = trim($rs->fields["store_emailA"]);
$branchBankCount1 = 0;
$_store_target1 = '' ;
if ($rs->fields['cBranchNum1'] > 0) {
	$_store_target1 = trim($rs->fields['cServiceTarget1']) ;
}

if ($rs->fields['bAccountUnusedA'] != 1 && $rs->fields["bAccountNum1A"]) {

	$branchBank1[$branchBankCount1]['bank'] = trim($rs->fields["bAccountNum1A"]);// 店家 (第二家總行)
	$branchBank1[$branchBankCount1]['bankBranch'] = trim($rs->fields["bAccountNum2A"]);// (第二家分行)
	$branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount4A"]);// (第二家戶名)
	$branchBank1[$branchBankCount1]['bankAccNum'] = trim($rs->fields["bAccount3A"]);// (第二家帳號)
	$branchBankCount1++;
}

if ($rs->fields['bAccountUnused1A'] != 1 && $rs->fields["bAccountNum11A"]) {
	$branchBank1[$branchBankCount1]['bank'] = trim($rs->fields["bAccountNum11A"]);// 店家 (第二家總行)
	$branchBank1[$branchBankCount1]['bankBranch'] = trim($rs->fields["bAccountNum21A"]);// (第二家分行)
	$branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount41A"]);// (第二家戶名)
	$branchBank1[$branchBankCount1]['bankAccNum'] = trim($rs->fields["bAccount31A"]);// (第二家帳號)
	$branchBankCount1++;

}

if ($rs->fields['bAccountUnused2A'] != 1 && $rs->fields["bAccountNum12A"]) {
	$branchBank1[$branchBankCount1]['bank'] = trim($rs->fields["bAccountNum12A"]);// 店家 (第二家總行)
	$branchBank1[$branchBankCount1]['bankBranch'] = trim($rs->fields["bAccountNum22A"]);// (第二家分行)
	$branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount42A"]);// (第二家戶名)
	$branchBank1[$branchBankCount1]['bankAccNum'] = trim($rs->fields["bAccount32A"]);// (第二家帳號)
	$branchBankCount1++;
	
}

if ($rs->fields['bAccountUnused3A'] != 1 && $rs->fields["bAccountNum13A"]) {
	$branchBank1[$branchBankCount1]['bank'] = trim($rs->fields["bAccountNum13A"]);// 店家 (第二家總行)
	$branchBank1[$branchBankCount1]['bankBranch'] = trim($rs->fields["bAccountNum23A"]);// (第二家分行)
	$branchBank1[$branchBankCount1]['bankAccName'] = trim($rs->fields["bAccount43A"]);// (第二家戶名)
	$branchBank1[$branchBankCount1]['bankAccNum'] = trim($rs->fields["bAccount33A"]);// (第二家帳號)
	$branchBankCount1++;

}

##

//第三家房仲介(B)
$storeName2 = $rs->fields['bBrandB']."_".$rs->fields['bStoreB']."_".$rs->fields['bNameB'];//店名
$storeId2 = $rs->fields['cBranchNum2'];

$_store_faxB = trim($rs->fields["store_faxB"]);
$_store_emailB = trim($rs->fields["store_emailB"]);

$_store_target2 = '' ;
if ($rs->fields['cBranchNum2'] > 0) {
	$_store_target2 = trim($rs->fields['cServiceTarget2']) ;
}

$branchBankCount2 = 0;
if ($rs->fields['bAccountUnusedB'] != 1 && $rs->fields["bAccountNum1B"]) {
	$branchBank2[$branchBankCount2]['bank'] = trim($rs->fields["bAccountNum1B"]);// 店家 (第二家總行)
	$branchBank2[$branchBankCount2]['bankBranch'] = trim($rs->fields["bAccountNum2B"]);// (第二家分行)
	$branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount4B"]);// (第二家戶名)
	$branchBank2[$branchBankCount2]['bankAccNum'] = trim($rs->fields["bAccount3B"]);// (第二家帳號)
	$branchBankCount2++;

}

if ($rs->fields['bAccountUnused1B']  != 1 && $rs->fields["bAccountNum11B"]) {
	$branchBank2[$branchBankCount2]['bank'] = trim($rs->fields["bAccountNum11B"]);// 店家 (第二家總行)
	$branchBank2[$branchBankCount2]['bankBranch'] = trim($rs->fields["bAccountNum21B"]);// (第二家分行)
	$branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount41B"]);// (第二家戶名)
	$branchBank2[$branchBankCount2]['bankAccNum'] = trim($rs->fields["bAccount31B"]);// (第二家帳號)
	$branchBankCount2++;

}

if ($rs->fields['bAccountUnused2B'] != 1 && $rs->fields["bAccountNum12B"]) {
	$branchBank2[$branchBankCount2]['bank'] = trim($rs->fields["bAccountNum12B"]);// 店家 (第二家總行)
	$branchBank2[$branchBankCount2]['bankBranch'] = trim($rs->fields["bAccountNum22B"]);// (第二家分行)
	$branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount42B"]);// (第二家戶名)
	$branchBank2[$branchBankCount2]['bankAccNum'] = trim($rs->fields["bAccount32B"]);// (第二家帳號)
	$branchBankCount2++;

}

if ($rs->fields['bAccountUnused3B'] != 1 && $rs->fields["bAccountNum13B"]) {
	$branchBank2[$branchBankCount2]['bank'] = trim($rs->fields["bAccountNum13B"]);// 店家 (第二家總行)
	$branchBank2[$branchBankCount2]['bankBranch'] = trim($rs->fields["bAccountNum23B"]);// (第二家分行)
	$branchBank2[$branchBankCount2]['bankAccName'] = trim($rs->fields["bAccount43B"]);// (第二家戶名)
	$branchBank2[$branchBankCount2]['bankAccNum'] = trim($rs->fields["bAccount33B"]);// (第二家帳號)
	$branchBankCount2++;
	
}


//第四家房仲介(C)
$storeName3 = $rs->fields['bBrandC']."_".$rs->fields['bStoreC']."_".$rs->fields['bNameC'];//店名
$storeId3 = $rs->fields['cBranchNum3'];
$branchBankCount3 = 0;

$_store_faxC = trim($rs->fields["store_faxC"]);
$_store_emailC = trim($rs->fields["store_emailC"]);

$_store_target3 = '' ;
if ($rs->fields['cBranchNum3'] > 0) {
	$_store_target3 = trim($rs->fields['cServiceTarget3']) ;
}

if ($rs->fields['bAccountUnusedC'] != 1 && $rs->fields["bAccountNum1C"]) {
	$branchBank3[$branchBankCount3]['bank'] = trim($rs->fields["bAccountNum1C"]);// 店家 (第二家總行)
	$branchBank3[$branchBankCount3]['bankBranch'] = trim($rs->fields["bAccountNum2C"]);// (第二家分行)
	$branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount4C"]);// (第二家戶名)
	$branchBank3[$branchBankCount3]['bankAccNum'] = trim($rs->fields["bAccount3C"]);// (第二家帳號)
	$branchBankCount3++;
	
}

if ($rs->fields['bAccountUnused1C'] != 1 && $rs->fields["bAccountNum11C"]) {
	$branchBank3[$branchBankCount3]['bank'] = trim($rs->fields["bAccountNum11C"]);// 店家 (第二家總行)
	$branchBank3[$branchBankCount3]['bankBranch'] = trim($rs->fields["bAccountNum21C"]);// (第二家分行)
	$branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount41C"]);// (第二家戶名)
	$branchBank3[$branchBankCount3]['bankAccNum'] = trim($rs->fields["bAccount31C"]);// (第二家帳號)
	$branchBankCount3++;

}

if ($rs->fields['bAccountUnused2C'] != 1 && $rs->fields["bAccountNum12C"]) {
	$branchBank3[$branchBankCount3]['bank'] = trim($rs->fields["bAccountNum12C"]);// 店家 (第二家總行)
	$branchBank3[$branchBankCount3]['bankBranch'] = trim($rs->fields["bAccountNum22C"]);// (第二家分行)
	$branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount42C"]);// (第二家戶名)
	$branchBank3[$branchBankCount3]['bankAccNum'] = trim($rs->fields["bAccount32C"]);// (第二家帳號)
	$branchBankCount3++;
	
}

if ($rs->fields['bAccountUnused3C'] != 1 && $rs->fields["bAccountNum13C"]) {
	$branchBank3[$branchBankCount3]['bank'] = trim($rs->fields["bAccountNum13C"]);// 店家 (第二家總行)
	$branchBank3[$branchBankCount3]['bankBranch'] = trim($rs->fields["bAccountNum23C"]);// (第二家分行)
	$branchBank3[$branchBankCount3]['bankAccName'] = trim($rs->fields["bAccount43C"]);// (第二家戶名)
	$branchBank3[$branchBankCount3]['bankAccNum'] = trim($rs->fields["bAccount33C"]);// (第二家帳號)
	$branchBankCount3++;

}



$sql = "SELECT * FROM tBranchBank WHERE bBranch ='".$rs->fields["cBranchNum"]."' OR bBranch = '".$rs->fields["cBranchNum1"]."' OR bBranch = '".$rs->fields["cBranchNum2"]."' OR bBranch = '".$rs->fields["cBranchNum3"]."'";

$rs_bb = $conn->Execute($sql);
while (!$rs_bb->EOF) {

	if ($rs_bb->fields['bUnUsed'] == 0) {
		if ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum']) {
			// $tmpStore[] = $rs_bb->fields;
			$branchBank[$branchBankCount]['bank'] = trim($rs_bb->fields["bBankMain"]);// 店家 (第一家總行)
			$branchBank[$branchBankCount]['bankBranch'] = trim($rs_bb->fields["bBankBranch"]);// (第一家分行)
			$branchBank[$branchBankCount]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]);// (第一家戶名)
			$branchBank[$branchBankCount]['bankAccNum'] = trim($rs_bb->fields["bBankAccountNo"]);// (第一家帳號)
			$branchBankCount++;
		}elseif ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum1']) {
			
			$branchBank1[$branchBankCount1]['bank'] = trim($rs_bb->fields["bBankMain"]);// 店家 (第二家總行)
			$branchBank1[$branchBankCount1]['bankBranch'] = trim($rs_bb->fields["bBankBranch"]);// (第二家分行)
			$branchBank1[$branchBankCount1]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]);// (第二家戶名)
			$branchBank1[$branchBankCount1]['bankAccNum'] = trim($rs_bb->fields["bBankAccountNo"]);// (第二家帳號)
			$branchBankCount1++;
		}elseif ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum2']) {
			$branchBank2[$branchBankCount2]['bank'] = trim($rs_bb->fields["bBankMain"]);// 店家 (第二家總行)
			$branchBank2[$branchBankCount2]['bankBranch'] = trim($rs_bb->fields["bBankBranch"]);// (第二家分行)
			$branchBank2[$branchBankCount2]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]);// (第二家戶名)
			$branchBank2[$branchBankCount2]['bankAccNum'] = trim($rs_bb->fields["bBankAccountNo"]);// (第二家帳號)
			$branchBankCount2++;
		}elseif ($rs_bb->fields['bBranch'] == $rs->fields['cBranchNum3']) {
			$branchBank3[$branchBankCount3]['bank'] = trim($rs_bb->fields["bBankMain"]);// 店家 (第二家總行)
			$branchBank3[$branchBankCount3]['bankBranch'] = trim($rs_bb->fields["bBankBranch"]);// (第二家分行)
			$branchBank3[$branchBankCount3]['bankAccName'] = trim($rs_bb->fields["bBankAccountName"]);// (第二家戶名)
			$branchBank3[$branchBankCount3]['bankAccNum'] = trim($rs_bb->fields["bBankAccountNo"]);// (第二家帳號)
			$branchBankCount3++;
		}
		// $branch_check++;
	}
	
	

	$rs_bb->MoveNext();
}

##
//其他新增的帳戶
$sql = "SELECT * FROM tContractCustomerBank WHERE cCertifiedId='".$_vr_code."' AND cChecklistBank = 0 ORDER BY cIdentity ASC";
// echo $sql;
$rsb = $conn->Execute($sql);
while (!$rsb->EOF) {


	if ($rsb->fields['cIdentity'] == 2 || $rsb->fields['cIdentity'] == 52) {
		if ($rsb->fields['cIdentity'] == 2) { //賣方
			$ownerBankNameArr[] = trim($rsb->fields["cBankAccountName"]);//比對戶名用
		}
		
		$ownerBank[$ownerBankCount]['bank'] = trim($rsb->fields["cBankMain"]);
		$ownerBank[$ownerBankCount]['bankBranch'] = trim($rsb->fields["cBankBranch"]);
		$ownerBank[$ownerBankCount]['bankAccName'] = trim($rsb->fields["cBankAccountName"]);
		$ownerBank[$ownerBankCount]['bankAccNum'] = trim($rsb->fields["cBankAccountNo"]);
		$ownerBankCount++;
	}else if($rsb->fields['cIdentity'] == 1 || $rsb->fields['cIdentity'] == 53){
		$buyerBank[$buyerBankCount]['bank'] = trim($rsb->fields["cBankMain"]);
		$buyerBank[$buyerBankCount]['bankBranch'] = trim($rsb->fields["cBankBranch"]);
		$buyerBank[$buyerBankCount]['bankAccName'] = trim($rsb->fields["cBankAccountName"]);
		$buyerBank[$buyerBankCount]['bankAccNum'] = trim($rsb->fields["cBankAccountNo"]);
		$buyerBankCount++;
	}elseif ($rsb->fields['cIdentity'] == 3) {
		##非仲介成交可能會有仲介人需要出服務費給他
		
		$branchBank[$branchBankCount]['bank'] = trim($rsb->fields["cBankMain"]);// 店家 ( 總行)
		$branchBank[$branchBankCount]['bankBranch'] = trim($rsb->fields["cBankBranch"]);// ( 分行)
		$branchBank[$branchBankCount]['bankAccName'] = trim($rsb->fields["cBankAccountName"]);// ( 戶名)
		$branchBank[$branchBankCount]['bankAccNum'] = trim($rsb->fields["cBankAccountNo"]);// ( 帳號)
		$branchBankCount++;
	}

	$rsb->MoveNext();
}
##

###

//查詢是否曾出款"仲介服務費"
$sql = 'SELECT * FROM tBankTrans WHERE tVR_Code="'.$vr_code.'" AND tObjKind="仲介服務費";' ;
$_rs = $conn->Execute($sql) ;
$realty_charge = 0 ;
if ($_rs->RecordCount() > 0) {
	$realty_charge = 1 ;
}
unset($_rs) ;
##
//查詢利息
$sql = "SELECT cInterest,bInterest FROM tChecklist WHERE cCertifiedId ='".$_vr_code."'";
$rsCK = $conn->Execute($sql) ;

$Int = $rsCK->fields['cInterest']+$rsCK->fields['bInterest'];
$realCertifiedMoney = $CertifiedMoney-$Int;

if ($realCertifiedMoney <= 0) {
	$realCertifiedMoney = '';
}
##
//
$sql = "SELECT SUM(tMoney) AS total FROM tBankTrans WHERE tBank_kind = '台新' AND tObjKind2 = '01' AND tObjKind2Item = '' AND tVR_Code='".$vr_code."'";
$rsTT = $conn->Execute($sql);
$taishinSPMoney = $rsTT->fields['total'];
##

##
//半形<=>全形
Function n_to_w($strs, $types = '0'){  // narrow to wide , or wide to narrow
	$nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
	);
	$wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
	);
 
	if ($types == '0') {		//半形轉全形
		// narrow to wide
		$strtmp = str_replace($nt, $wt, $strs);
	}
	else {						//全形轉半形
		// wide to narrow
		$strtmp = str_replace($wt, $nt, $strs);
	}
	return $strtmp;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>無標題文件</title>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<link rel="stylesheet" href="../colorbox.css" />

<script src="../js/jquery.colorbox.js"></script>
<script>
var showOrHide=false;
$(function() {


	$(".dt" ).datepicker({ dateFormat: "yy-mm-dd" }) ;
		$( "#other_all" ).hide();
		$("#toggle_other").live("click", function(){ show_other();});  
		$(".ajax").colorbox({width:"500",height:"200"});
		$(".ajax1").colorbox();
		
		$(".ajax1").click(function() {
			var a = $(this).prop('id') ;
			
			$('.ajax1').each(function() {
				var c = $(this).prop('id') ;
				if (a != c) {
					$(this).empty() ;
				}
			}) ;
			
			$('.taxRemark').each(function() {
				var b = $(this).prop('title') ;
				if (a != b) {
					$(this).empty() ;
				}
			}) ;
		}) ;

		bank_check() ;
		Lock();
		
	$.widget( "ui.combobox", {
	    _create: function() {
	        var input,
	            self = this,
	            select = this.element.hide(),
	            selected = select.children( ":selected" ),
	            value = selected.val() ? selected.text() : "",
	            wrapper = this.wrapper = $( "<span>" )
	                .addClass( "ui-combobox" )
	                .insertAfter( select );

	        input = $( "<input>" )
	            .appendTo( wrapper )
	            .val( value )
	            .addClass( "ui-state-default ui-combobox-input" )
	            .autocomplete({
	                delay: 0,
	                minLength: 0,
	                source: function( request, response ) {
	                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
	                    response( select.children( "option" ).map(function() {
	                        var text = $( this ).text();
	                        if ( this.value && ( !request.term || matcher.test(text) ) )
	                            return {
	                                label: text.replace(
	                                    new RegExp(
	                                        "(?![^&;]+;)(?!<[^<>]*)(" +
	                                        $.ui.autocomplete.escapeRegex(request.term) +
	                                        ")(?![^<>]*>)(?![^&;]+;)", "gi"
	                                    ), "<strong>$1</strong>" ),
	                                value: text,
	                                option: this
	                            };
	                    }) );
	                },
	               select: function( event, ui ) {
	                    ui.item.option.selected = true;
	                    self._trigger( "selected", event, {
	                        item: ui.item.option
	                    });
	                    select.trigger("change");                            
	                },
	                autocomplete : function(value) {
	                	// console.log(value);
					    this.element.val(value);
					    this.input.val(value);
					},
	                change: function( event, ui ) {
	                    if ( !ui.item ) {
	                        var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
	                            valid = false;
	                        select.children( "option" ).each(function() {
	                            if ( $( this ).text().match( matcher ) ) {
	                                this.selected = valid = true;
	                                $("[name='']")
	                                return false;
	                            }
	                        });
	                        if ( !valid ) {
	                            // remove invalid value, as it didn't match anything
	                            $( this ).val( "" );
	                            select.val( "" );
	                            input.data( "autocomplete" ).term = "";
	                            return false;
	                        }
	                    }
	                    
	                   
	                    
	                }
	            })
	            .addClass( "ui-widget ui-widget-content ui-corner-left" );

	        input.data( "autocomplete" )._renderItem = function( ul, item ) {
	            return $( "<li></li>" )
	                .data( "item.autocomplete", item )
	                .append( "<a>" + item.label + "</a>" )
	                .appendTo( ul );
	        };

	        $( "<a>" )
	            .attr( "tabIndex", -1 )
	            .attr( "title", "Show All Items" )
	            .appendTo( wrapper )
	            .button({
	                icons: {
	                    primary: "ui-icon-triangle-1-s"
	                },
	                text: false
	            })
	            .removeClass( "ui-corner-all" )
	            .addClass( "ui-corner-right ui-combobox-toggle" )
	            .click(function() {
	                // close if already visible
	                if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
	                    input.autocomplete( "close" );
	                    return;
	                }

	                // work around a bug (likely same cause as #5265)
	                $( this ).blur();

	                // pass empty string as value to search for, displaying all results
	                input.autocomplete( "search", "" );
	                input.focus();
	            });
	    },

	    destroy: function() {
	        this.wrapper.remove();
	        this.element.show();
	        $.Widget.prototype.destroy.call( this );
	    }
	});
    $('.bank').combobox();


});

function serviceMoney(type,id){ //判斷是那一方的仲介服務費
	//$(this).val()
	//oserviceTarget0
	//ot_money0
	var target = $("#"+type+"serviceTarget"+id).val();
	var money = $("#"+type+"t_money"+id).val();
	var obj = $(".objKind"+id).val();
	$("."+type+"t_buyer"+id).val(0);
	$("."+type+"t_seller"+id).val(0);
	
	if (obj == '仲介服務費') {

		if (target == 'buyer') {
			$("."+type+"t_buyer"+id).val(money);
			$("."+type+"t_seller"+id).val(0);
		}else{
			$("."+type+"t_seller"+id).val(money);
			$("."+type+"t_buyer"+id).val(0);
		}

		check_money();
	}
	
	// console.log(target);
}

function bankphone(v,type){
	//type 1 原有的 2額外新增的
	var obj = $(".objKind"+v).val();

	if (obj == '代清償') {
		if (type == 1) {
			var bank = $(".b3_"+v).val();
			var bankB = $(".b4_"+v).val();
		}else{
			var bank = $(".b3n_"+v).val();
			var bankB = $(".b4n_"+v).val();
		}
		
		$("#bankp"+v).html('');
		
		$.ajax({
			url: '../getBankPhone.php',
			type: 'POST',
			dataType: 'html',
			data: {'bank': bank,'branch':bankB},
		})
		.done(function(txt) {
			
			$("#bankp"+v).html('電話：'+txt);
		});
	}
	
	
}

function setTxt(id,val,name,name2,mName){
	var vv = "<?=$radiokind?>";
	

	if (vv == '點交') {vv ="點交(結案)";};
	//如果是保證費要帶保號 保證費只顯示保留點交(結案) 解約終止履保 建經發函終止
	if (val == '保證費') {
		$("#"+name+id).text("<?=n_to_w(substr($vr_code,5,9))?>");
		$("#"+name2+id+" option").remove();
		$("#"+name2+id).html("<option value=\"\" selected=\"selected\">項目</option><option value=\"點交(結案)\" >點交(結案)</option><option value=\"解除契約\">解約/終止履保</option><option value=\"建經發函終止\">建經發函終止</option>");
		$("#"+mName+id).val(<?=$realCertifiedMoney?>);
	}else{
		// $("#"+mName+id).val(0);
		$("#"+name+id).text('');
		$("#"+name2+id+" option").remove();
		$("#"+name2+id).html("<option value=\"\" >項目</option><option value=\"賣方先動撥\">賣方先動撥</option><option value=\"仲介服務費\">仲介服務費</option><option value=\"代清償\">代清償</option><option value=\"點交(結案)\">點交(結案)</option><option value=\"其他\">其他</option><option value=\"調帳\">調帳</option><option value=\"解除契約\">解約/終止履保</option><option value=\"保留款撥付\">保留款撥付</option><option value=\"建經發函終止\">建經發函終止</option>");

	}
	$("#objKind"+id).val(vv);

	//地政士銀行備註顯示
	// console.log(val);
	// console.log($("#export"+id).val());
	// console.log(bank);
	bankNote($("#export"+id).val(),id);

	
}
function rel_words(name) //半形轉全形
{

	var val = $("#"+name).val();

	// alert(val);
	 $.ajax({
			url: 'replace_words.php',
			type: 'POST',
			dataType: 'html',
			data: {'txt': val},
		})
		.done(function(txt) {
		
			$("#"+name).val(txt);

		});
}
function checkowner(index,id){
	var val = $("#t_name"+index).val();
	$.ajax({
		url: '../includes/checkOwner.php',
		type: 'POST',
		dataType: 'html',
		data: {id: id,val:val},
	})
	.done(function(msg) {
		// console.log(msg);
		if (msg =='fail') {
			$("#anotherS10").show();
			
		}else{
			$("#anotherS10").hide();
			
		}
	});

	// if ($("#t_name").val() ) {}
}

function show_other(){
	//<span class="ui-icon ui-icon-triangle-1-s"></span>
	
	$("#other_all").toggle(showOrHide); 
	if ( showOrHide == true ) {
	 $( "#other_all" ).hide();
	 $("#pp").removeClass("ui-icon ui-icon-triangle-1-s").addClass("ui-icon ui-icon-triangle-1-e");
	 showOrHide = false;
	} else if ( showOrHide == false ) {
	  $( "#other_all" ).show();
	  $("#pp").removeClass("ui-icon ui-icon-triangle-1-e").addClass("ui-icon ui-icon-triangle-1-s");
	  showOrHide = true;
	}
	

}

var _pos=1;
function clone(){
	var _obj = "tr_pos_"+_pos;
	var _service_obj = "s_service_" + _pos;
	var _change_obj = "s_change_" + _pos;
	var clonedRow = $('#tr_pos').clone(true);
	clonedRow.find('#rem').attr("href","Javascript: remove_tr('" + _pos + "');");
	clonedRow.find('.b3').attr("onchange","bank_select_index(this.value,'b4_"+_pos+"')");
	clonedRow.find('.b4').attr("class","b4_"+_pos);
	clonedRow.find('input[name*="t_name"]').val("");
	clonedRow.find('input[name*="pid"]').val("");
	clonedRow.find('input[name*="t_account"]').val("");
	clonedRow.find('input[name*="t_money"]').val("");
	clonedRow.find('input[name*="t_cost"]').val("");
	clonedRow.find('#s_service').attr("id","s_service_"+_pos);
	clonedRow.find('#s_change').attr("id","s_change_"+_pos);
	clonedRow.attr( 'id', _obj );
	clonedRow.appendTo('#ttt');
	//$('#tr_pos').clone().attr( 'id',"tr_pos_"+_pos ).appendTo('#ttt');
	_pos++;
	//alert(_pos);
}
function show_service(x,y,z,n){
	//alert(x);
	//_t = _pos - 1;
	//alert(_t);
	_tg = '#'+y;
	//alert(_tg + "/" + y);
	if (x=='調帳' || x=='仲介服務費') {
			
			_obj1 = ".tn_" + z;
			_obj2 = ".ta_" + z;
			_obj3 = ".b4n_" + z;
			_obj4 = ".b3n_" + z;
			if (x=='仲介服務費') {
				$(_tg).show();
				$(_obj1).val('<?php echo $_store_name;?>');
				$(_obj2).val('<?php echo $_store_account;?>');
				$(_obj4).children().each(function(){
					if ($(this).val()=="<?php echo $store_bk1;?>"){
						//jQuery給法
						$(this).attr("selected","true"); //或是給selected也可
				 
						//javascript給法
						this.selected = true;   
					}
				});
				$(_obj3).children().each(function(){
					if ($(this).val()=="<?php echo $store_bk2;?>"){
						//jQuery給法
						$(this).attr("selected","true"); //或是給selected也可
				 
						//javascript給法
						this.selected = true;   
					}
				});
			} 
	} else {
			$(_tg).hide();
			_obj1 = ".tn_" + z;
			_obj2 = ".ta_" + z;
			_obj3 = ".b4n_" + z;
			_obj4 = ".b3n_" + z;
			if (x=='扣繳稅款') {
				$(_obj1).val('<?php echo $_s_name;?>');
				$(_obj2).val('<?php echo $_s_account;?>');
				//
				$(_obj4).children().each(function(){
					if ($(this).val()=="<?php echo $s_bk1;?>"){
						//jQuery給法
						$(this).attr("selected","true"); //或是給selected也可
				 
						//javascript給法
						this.selected = true;   
					}
				});
				//
				$(".b4n_1").children().each(function(){
					if ($(this).val()=="<?php echo $s_bk2;?>"){
						//jQuery給法
						$(this).attr("selected","true"); //或是給selected也可
				 
						//javascript給法
						this.selected = true;   
					}
				});
				//
			} else if (x=='賣方先動撥') {
				$(_obj1).val('<?php echo $_o_name;?>');
				$(_obj2).val('<?php echo $_o_account;?>');
				//
				$(_obj4).children().each(function(){
					if ($(this).val()=="<?php echo $o_bk1;?>"){
						//jQuery給法
						$(this).attr("selected","true"); //或是給selected也可
				 
						//javascript給法
						this.selected = true;   
					}
				});
				//
				$(_obj3).children().each(function(){
					if ($(this).val()=="<?php echo $o_bk2;?>"){
						//jQuery給法
						$(this).attr("selected","true"); //或是給selected也可
				 
						//javascript給法
						this.selected = true;   
					}
				});
				//
			}
	}

	if (n) {
		bank_check(n) ;
	}else{
		bank_check() ;
	}
	
}
function remove_tr(x){
	//x.remove();
	$('#tr_pos_'+x).remove();	
}
function export_select_index(x,tg) {
	if (x == '01') {		
		$('#bk').val("first");	
		$('.b3').empty();
		_t = '<option value="007">第一商業銀行</option>';
		$('.b3').append(_t);
		bank_select_index('007','b4');
	} else if (x == '03') {
		$('#bk').val("first");
		$('.b3').empty();
		_t = '<option value="007">第一商業銀行</option>';
		$('.b3').append(_t);
		bank_select_index('007','b4');
	} else if (x == '05') {
		alert(" 帳戶請寫0 , 戶名鍵入一個空白"); 
		//$('#t_account[]').val('00000000000000');
	}
	else {
		$('#bk').val('');
		//$('.b3').empty();
		var _number = Math.random();
		var url = "_b3_select.php?i=" + _number;
		 $.ajax({
			url: url,
			error: function (xhr) {
			  //alert(xhr);
			  alert("error!!");
			},
			success: function (response) {
			  $('.b3').empty();
			  $('.b3').append(response);
			}
		  });
		
	}
	
}
function bank_select_index(x, kind, y, z, w,b2) {

  var _target = $('#bk').val();
  if (_target == 'first') { x='007';}
  var _number = Math.random();
  var url = "../_bank_select.php?i=" + _number + "&bank3=" + x+"&b4="+b2;
  //alert(url);
  $.ajax({
    url: url,
    error: function (xhr) {
      //alert(xhr);
      alert("error!!");
    },
    success: function (response) {
      $("." + kind).empty();
      $("." + kind).append(response);
      setBankAutoComplete(kind);
    }
  });
  url = 'get_bank_1.php?cl=' + z + '&bk=' + x + '&cs=' + w;
  $('#'+y).attr('href',url)
  
  // console.log(w);
  if (w == 'n') {
  	bank_check('n') ;
  }else{
  	bank_check() ;
  }
  
}
function setBankAutoComplete(kind){
	$.widget( "ui.combobox", {
	    _create: function() {
	        var input,
	            self = this,
	            select = this.element.hide(),
	            selected = select.children( ":selected" ),
	            value = selected.val() ? selected.text() : "",
	            wrapper = this.wrapper = $( "<span>" )
	                .addClass( "ui-combobox" )
	                .insertAfter( select );

	        input = $( "<input>" )
	            .appendTo( wrapper )
	            .val( value )
	            .addClass( "ui-state-default ui-combobox-input" )
	            .autocomplete({
	                delay: 0,
	                minLength: 0,
	                source: function( request, response ) {
	                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
	                    response( select.children( "option" ).map(function() {
	                        var text = $( this ).text();
	                        if ( this.value && ( !request.term || matcher.test(text) ) )
	                            return {
	                                label: text.replace(
	                                    new RegExp(
	                                        "(?![^&;]+;)(?!<[^<>]*)(" +
	                                        $.ui.autocomplete.escapeRegex(request.term) +
	                                        ")(?![^<>]*>)(?![^&;]+;)", "gi"
	                                    ), "<strong>$1</strong>" ),
	                                value: text,
	                                option: this
	                            };
	                    }) );
	                },
	               select: function( event, ui ) {
	                    ui.item.option.selected = true;
	                    self._trigger( "selected", event, {
	                        item: ui.item.option
	                    });
	                    select.trigger("change");                            
	                },
	                autocomplete : function(value) {
	                	// console.log(value);
					    this.element.val(value);
					    this.input.val(value);
					},
	                change: function( event, ui ) {
	                    if ( !ui.item ) {
	                        var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
	                            valid = false;
	                        select.children( "option" ).each(function() {
	                            if ( $( this ).text().match( matcher ) ) {
	                                this.selected = valid = true;
	                                $("[name='']")
	                                return false;
	                            }
	                        });
	                        if ( !valid ) {
	                            // remove invalid value, as it didn't match anything
	                            $( this ).val( "" );
	                            select.val( "" );
	                            input.data( "autocomplete" ).term = "";
	                            return false;
	                        }
	                    }
	                    
	                   
	                    
	                }
	            })
	            .addClass( "ui-widget ui-widget-content ui-corner-left" );

	        input.data( "autocomplete" )._renderItem = function( ul, item ) {
	            return $( "<li></li>" )
	                .data( "item.autocomplete", item )
	                .append( "<a>" + item.label + "</a>" )
	                .appendTo( ul );
	        };

	        $( "<a>" )
	            .attr( "tabIndex", -1 )
	            .attr( "title", "Show All Items" )
	            .appendTo( wrapper )
	            .button({
	                icons: {
	                    primary: "ui-icon-triangle-1-s"
	                },
	                text: false
	            })
	            .removeClass( "ui-corner-all" )
	            .addClass( "ui-corner-right ui-combobox-toggle" )
	            .click(function() {
	                // close if already visible
	                if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
	                    input.autocomplete( "close" );
	                    return;
	                }

	                // work around a bug (likely same cause as #5265)
	                $( this ).blur();

	                // pass empty string as value to search for, displaying all results
	                input.autocomplete( "search", "" );
	                input.focus();
	            });
	    },

	    destroy: function() {
	        this.wrapper.remove();
	        this.element.show();
	        $.Widget.prototype.destroy.call( this );
	    }
	});
    $("." + kind).combobox();

}

function check_form() {	
	_code = $('#vr_code').val();
	if (_code.length < 14) {
		alert("專屬帳號不滿14位!");
      return false;
	}
	if ($('#vr_code').val() == "") {
      alert("專屬帳號未填寫!");
      return false;
    }
	if ($('input[name*="t_name"]').val() == "") {
      alert("戶名未填寫!");
      return false;
    }
	if ($('input[name*="t_account"]').val() == "") {
      alert("銀行帳號未填寫!");
      return false;
    }
	_taccount = $('input[name*="t_account"]').val();
	if (_taccount.length < 10 ) {
		alert("銀行帳號未滿10位!");
      return false;
	}
	if ($('input[name*="t_money"]').val() == "") {
      alert("金額未填寫!");
      return false;
    }

	return true;
}
//計算總價金*6%>(買+賣)服務費
function check_money () {

	var buy_tmp  = 0;
	var owner_tmp = 0;
	var sum = 0;
	var total =<?=$total_money?>*0.06;

	var buy_money = new Array();
	var owner_money = new Array();

	$('input:[name="t_buyer[]"]').each(function(i) { 
		if (this.value !='') {
			buy_money[i] = this.value;

		}
		 
	});

	$('input:[name="t_seller[]"]').each(function(i) { 
		if (this.value !='') {
			owner_money[i] = this.value; 
		}
		
	});

	for (var i = 0; i < buy_money.length; i++) {
		buy_tmp = buy_tmp+parseInt(buy_money[i]);
	}

	for (var i = 0; i < owner_money.length; i++) {
		owner_tmp = owner_tmp+parseInt(owner_money[i]);
	}
	

	sum = buy_tmp+owner_tmp;


	if (total<sum) {
			alert('服務費大於總價金的6%');
	}
	

}

function recal(type,id) {
	
	var balanceMoney = "<?=trim($rs->fields["cCaseMoney"])?>" ;
	var _balance = parseInt(balanceMoney) ;
	
	// alert(_balance);
	$('input[name*="t_money"]').each(function() {
		var str = $(this).val() ;
		if(str) { 
			_balance = _balance - parseInt(str) ;
		}
	}) ;
	//objKind
	
	$('#caseMoney').html(_balance) ;
	
	serviceMoney(type,id);
}
function waring_msg() {
	alert('請確認附言內容跟出款項目相符!') ;
}
function Lock(){
	var array = "input,select,textarea";
	
	
	$(".lock").each(function() {
		// $(this).val();
		if ($(this).val()) {
			$(".lock"+$(this).val()).find(array).each(function() {
				$(this).attr('disabled', true);
				// console.log();
			});
		}
		
	});

}
function unLock(id){
	var array = "input,select,textarea";

	$(".lock"+id).find(array).each(function() {
		$(this).attr('disabled', false);
				// console.log();
	});

	$(".lseller"+id).attr('disabled',true);//ot_seller
	$(".lbuyer"+id).attr('disabled',true);//ot_buyer
}
/* 檢查銀行是否為聯行或跨行 */
function bank_check(n) {
	var index = 0 ;

	if ($("#export"+index).val() == "04" || $("#export"+index).val() == "05") { //大額繳稅跟臨櫃領現不用檢查
		return false;
	}
	
	var mainBank = $('[name="contractBank"]').val() ;		//合約銀行代碼
	$('[name="bank3[]"]').each(function() {
		var _export = '02' ;
		var bk_no = $(this).val() ;
		var _export_name ='跨行代清償';
		var checkCode = $("#export"+index).val();
		


		if (bk_no) {
			if (bk_no == mainBank) {
				_export = item_check(index,n) ;
				if (_export=='03') {
					_export_name ='聯行代清償';
				}else if(_export=='01'){
					_export_name ='聯行轉帳';
				}
				
			}
		}


		
		set_item(index,_export,_export_name) ;
		
		index = index + 1 ;
	}) ;
}
////

/* 確認交易項目是否為代清償或扣繳稅款 */
function item_check(id,n) {
	var index = 0 ;
	var _export = '01' ;
	var code ='';
	
	
	if (n) {
		if ($(".ta_"+id).val() != '' && $(".ta_"+id).val() != undefined) {
			code = $(".ta_"+id).val().substr(3,3); //055 050 是還款帳戶所以是聯行代清償(03) 永豐限定
		}
		
		// console.log(code);
	}else{
		if ($(".bb_"+id).val() != '' && $(".bb_"+id).val() != undefined) {
			code = $(".bb_"+id).val().substr(3,3); //055 050 是還款帳戶所以是聯行代清償(03) 永豐限定
		}
	
	}
	var mainBank = $('[name="contractBank"]').val() ;		//合約銀行代碼
	// console.log(code);
	$('[name="objKind[]"]').each(function() {
		if (index == id) {
			
			var objKind = $(this).val() ;
				// console.log(id+'_'+objKind+'_'+mainBank+'_'+code);
			if (objKind == '代清償' && mainBank == "807" && (code == '055' || code =='050')) {
				_export = '03' ;
			}
			if (objKind == '代清償' && mainBank != "807") {
				_export = '03' ;
			}
			//else if (objKind == '扣繳稅款') {
			//	_export = '04' ;
			//}
		}
		index = index + 1 ;
	}) ;
	return _export ;
}
////

/* 設定交易類別 */
function set_item(no,ex,exn) {
	var index = 0 ;
	var bank = "<?=$_vr_bank?>";
	//alert('第'+no+'組,項目='+ex) ;
	
	$('#export'+no).val(ex) ;
	//同值不同項目
	$('#export'+no+' option').each(function() {
		if ($(this).text() == '聯行轉帳' && ex =='01') {
			$(this).attr('selected', true);

		}else if($(this).text() == '臨櫃開票' && ex =='05'){
			$(this).attr('selected', true);
		}
			
		bankNote(ex,no);


	}) ;
	$('#code2'+no).val(exn) ;
}
////
/*取得交易名稱*/
function setCode2(name,v){

	var val = $("#"+name).find(":selected").text();
	var bank = "<?=$_vr_bank?>";
	 
	$("#code2"+v).val(val);

	bankNote($("#"+name).val(),v);
	
	if ($("#"+name).val() == '04' || $("#"+name).val() == '05') {
		bankAccountAuto(v,'');
		if ($("#"+name).val() == '04') {
			$("#taxScrivener"+v).attr('disabled', 'disabled');
			$("#taxScrivener"+v).hide();
		}
	}else{
		$("#taxScrivener"+v).show();
		$("#taxScrivener"+v).attr('disabled', 'none');
	}


	      			
}
function bankAccountAuto(id,cat){
	$.ajax({
		url: '../getMainBankAccount.php',
		type: 'POST',
		dataType: 'html',
		data: {id: $("[name='vr_code']").val(),cat:cat},
	}).done(function(msg) {
		// console.log(msg);
		var obj = jQuery.parseJSON(msg);
		if (obj.msg != 1) {
			$(".b3_"+id).combobox('destroy');
			$(".b3_"+id).val(obj.Bank);
			setBankAutoComplete("b3_"+id);

			$(".b4_"+id).combobox('destroy');
			bank_select_index(obj.Bank, "b4_"+id, "branch_"+id, id,'',obj.BankBranch);

			$("#t_name"+id).val(obj.AccName);
			$(".bb_"+id).val(obj.Acc);
		}
		
		
	});
	
	
}
function bankNote(code,id){
	var bank = "<?=$_vr_bank?>";


	if ((code =='01' || code =='02') && $("#target"+id).val() == '地政士' && bank == '永豐') {
		$('#Note'+id).show() ;
		$('#bankshowtxt'+id).replaceWith('<input type="text" name="bankshowtxt[]" id="bankshowtxt'+id+'" maxlength="6" value="<?=$owner.$buyer?>">') ;
			
	}else if((code =='01' || code =='02') && $("#target"+id).val() == '地政士' && bank == '台新'){
		$('#Note'+id).show() ;
		$('#bankshowtxt'+id).replaceWith('<input type="text" name="bankshowtxt[]" id="bankshowtxt'+id+'" maxlength="6" value="<?=$owner.$buyer?>">') ;
	}else{
		$('#Note'+id).hide() ;
		$('#bankshowtxt'+id).val('') ;
		$('#bankshowtxt'+id).replaceWith('<input type="hidden" name="bankshowtxt[]" id="bankshowtxt'+id+'" maxlength="6">') ;
			// console.log($('#bankshowtxt'+no).attr('type'));
	}
}
/* 檢核送出建檔 */
function go() {

	var realtyCharge = <?=$realty_charge?> ;
	$('[name="objKind[]"]').each(function() { 
		var str = $(this).val() ;
		if (str == '仲介服務費') {
			realtyCharge = realtyCharge + 1 ;

		}
	}) ;
	<?php if ($radiokind == '點交'): ?>
		if (realtyCharge <= 0) {
			$('#realtyC').html('<h1 style="font-weight:bold;color:red;text-align:center;width:100%;">"仲介服務費"</h1><h3 style="text-align:center;width:100%;">尚未出款!!</h3>') ;
			$('#realtyC').dialog({
				
				modal: true,
				buttons: {
					"繼續出款": function() {
						$(this).dialog("close") ;
						if (checkCommitmentMoney()) {
							$('#form1').submit() ;
							// alert('OK');
						}
						
					},
					"取消返回": function() {
						$(this).dialog("close") ;
					}
				}
			}) ;
		}
		else {
			$("#sub").hide();
			$('#form1').submit() ;
		}
	<?php elseif($radiokind == '扣繳稅款'): ?>
		var check = 0;
		
		$(".taxScrivener").each(function(index,val) {	
			// console.log($("#export"+index).val()+'_'+$(this).val());
			check = 1;
			if($(this).val() != '' || $("#export"+index).val() =='04'){ //大額繳稅不用代墊
				check = 1;
			}

			if ($(this).val() == '02') {
				if ($("[name='datepicker"+index+"']").val() == '') {
					check = 2;
				}
			}
		});
		// console.log(check);
		if (check == 1) {
			// console.log(check);
			if (checkCommitmentMoney()) {
				// alert('OK');
				$("#sub").hide();
				$('#form1').submit() ;
			}
		}else{
			if ("<?=$_vr_bank?>" == '台新') {
				if (check == '2') {
					alert('請選擇日期');
				}else{
					alert("請選擇");
				}
			}else{
				if (checkCommitmentMoney()) {
					// alert('OK');
					$("#sub").hide();
					$('#form1').submit() ;
				}
				
			}
			// 
		}
	<?php else: ?>	
		if (checkCommitmentMoney()) {
			// alert('OK');
			$("#sub").hide();
			$('#form1').submit() ;
		}
		
	<?php endif ?>


	
	// var id = $('[name="certifiedid"]', opener.document).val();
	// $('form[name=form_edit] input[name=id]', opener.document).val(id);

	// $('form[name=form_edit]', opener.document).submit();

	

			
}
function checkCommitmentMoney(){
	if ("<?=$CommitmentMoney?>" > 0) {
		var _balance = parseInt($("#caseMoney").text()) ;
		var CommitmentMoney = parseInt("<?=$CommitmentMoney?>");
		// console.log(_balance+'_'+CommitmentMoney);
		if (_balance < CommitmentMoney) {
			alert("餘額少於承諾書金額!!");

			// return false;

		}
	}

	return true;
}
function checkScrivenerTax(id){
	
	if ($("#taxScrivener"+id).val() == '02') {
		$("#exportTax").hide();
		$("#returnTax").show();
		bankAccountAuto(id,2);
	}else if($("#taxScrivener"+id).val() == '03'){
		$("#exportTax").show();
		$("#returnTax").hide();
		bankAccountAuto(id,3);
	}else if($("#taxScrivener"+id).val() == '04'){
		bankAccountAuto(id,4);
	}else{
		$("#exportTax").show();
		$("#returnTax").hide();
		
		bankAccountAuto(id,1);
	}
	// $("#taxScrivener"+id).val();

	// if ($("#taxScrivener"+id).val() == '02') {
	// 	$("#exportTax").hide();
	// 	$("#returnTax").show();
	// 	bankAccountAuto(id,2);
	// }else if($("#taxScrivener"+id).val() == '04'){
	// 	bankAccountAuto(id,4);
	// }else{
	// 	$("#exportTax").show();
	// 	$("#returnTax").hide();
	// 	// bankAccountAuto(id,1);
	// }
}

function checkChoiceDetail(id,cat){
	if ($("#taxScrivener"+id).val() =='' && "<?=$_vr_bank?>" == '台新') {
		// alert("請選擇代墊項目");
		// return false;
	}

	if (cat == '2') {
		var url = "getTaxDetail.php?cid=<?=$_vr_code?>&t=tm<?=$j?>"+id;
	}else{
		var url = "getExpenseDetail.php?cid=<?=$_vr_code?>&t=tm"+id+"&cat="+$("#taxScrivener"+id).val();
	}

	
    $.colorbox({href:url}) ;
               
}
////
</script>
<style>
.font9 {
	font-size: 9pt;
}
.ui-combobox {
    position: relative;
    display: inline-block;
}
.ui-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    /* adjust styles for IE 6/7 */
    *height: 1.5em;
    *top: 0.1em;
    width: 20px;
}
.ui-combobox-input {
    margin: 0;
    padding: 0.1em;
    width:160px;
}
.ui-autocomplete {
    width:160px;
    max-height: 300px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    /* add padding to account for vertical scrollbar */
    padding-right: 20px;
}
.ui-autocomplete {
    width:160px;
    max-height: 300px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    /* add padding to account for vertical scrollbar */
    padding-right: 20px;
    font-size: 12px;
}
.ui-autocomplete-input {
    width:120px;
    font-size: 12px;
}

</style>
</head>

<body>
<div style="width:1600px; margin-bottom:5px; height:22px; background-color: #CCC">
<div style="float:left;margin-left: 10px;"> <font color=red><strong>建檔</strong></font> </div>
<div style="float:left;margin-left: 10px;"> <a href="../list2.php">待修改資料</a> </div>
<?php if ($_SESSION["member_id"] != '1' and $_SESSION["member_id"] != '5' ) { ?>
<div style="float:left; margin-left: 10px;"> <a href="../list.php">未審核列表</a></div>
<?php } ?>
</div>
<form id="form1" name="form1" method="post" action="">
<table width="1600" border="0">
 <tr>
      <td colspan="2">*保證號碼  <?php echo $vr_code;?>       
        <input name="vr_code" type="hidden" id="vr_code" value="<?php echo $vr_code;?>" />
        <input type="hidden" name="radiokind" value="<?=$radiokind?>" />
      <input name="save" type="hidden" id="save" value="ok" /></td>
      <td >
        配合銀行 【<?=$_vr_bank.$branch?>】
		<input type="hidden" name="bank_kind" value="<?=$_vr_bank?>">
		<input type="hidden" name="contractBank" value="<?=$bank_no?>">
	  </td>
      <td align="center">
		目前帳戶餘額：<?=trim($rs->fields["cCaseMoney"])?>
		<?php if ($taishinSPMoney > 0): ?>
			<font color="red">(<?=$taishinSPMoney?>元未返還)</font>
		<?php endif ?>
		<?php if ($CommitmentMoney > 0): ?>
			<font color="red">(承諾書金額：<?=$CommitmentMoney?>元)</font>
		<?php endif ?>
		<input type="hidden" name="Balance" value="<?=($taishinSPMoney+$rs->fields["cCaseMoney"])?>" />
			
		預計出帳後餘額：<span id="caseMoney"><?php echo trim($rs->fields["cCaseMoney"]);?></span>
		
		</td>
    </tr>
</table>
<?php

 	
	 switch( $radiokind ){
		case "賣方先動撥":
		 	//主賣方
			
			$i = 1 ; $index = 0;
			for ($c=0; $c < count($ownerBank); $c++) { 
				$_a[$index] = '賣方' ;	
				$_an[$index] = $ownerBank[$c]['bankAccName'];
				$_ac[$index] = $ownerBank[$c]['bankAccNum'];
				$_ab3[$index] = $ownerBank[$c]['bank'];
				$_ab4[$index] = $ownerBank[$c]['bankBranch'];
				$index++;
			}

			$i = count($_a) ;
			##
		break;
		case "點交":
			
			$index = 0 ;
			
			//賣方
			for ($c=0; $c < count($ownerBank); $c++) { 
				$_a[$index] = '賣方' ;	
				$_an[$index] = $ownerBank[$c]['bankAccName'];
				$_ac[$index] = $ownerBank[$c]['bankAccNum'];
				$_ab3[$index] = $ownerBank[$c]['bank'];
				$_ab4[$index] = $ownerBank[$c]['bankBranch'];
				$index++;
			}

			//買方
			for ($c=0; $c < count($buyerBank); $c++) { 
				$_a[$index] = '買方' ;	
				$_an[$index] = $buyerBank[$c]['bankAccName'];
				$_ac[$index] = $buyerBank[$c]['bankAccNum'];
				$_ab3[$index] = $buyerBank[$c]['bank'];
				$_ab4[$index] = $buyerBank[$c]['bankBranch'];
				$index++;
			}

			##
			//代書帳戶名稱
			
			for ($c=0; $c < count($scrivenerBank); $c++) { 
				$_an[$index] = $scrivenerBank[$c]['bankAccName'];	//帳戶
				$_ac[$index] = $scrivenerBank[$c]['bankAccNum'];	//帳號	
				$_ab3[$index] = $scrivenerBank[$c]['bank'] ;		//銀行總行代碼	
				$_ab4[$index] = $scrivenerBank[$c]['bankBranch'];		//分行代碼
				$_af[$index] = '' ;	//代書傳真電話
				$_ae[$index] = $_s_email ;//代書E-Mail
				$_a[$index] = '地政士' ;//身分title
				$index++;
			}
			
			
			
			##
			//第一建經活儲、
			$_tmp = $_account_name ;
			$_tmp1 = $_account_no ;
			
			$_an[$index] =  $_account_name ;//帳戶
			$_ac[$index] = $_account_no ;//帳號

			$_ab3[$index] = $main_bank ;//總行代碼
			$_ab4[$index] = $branch_bank ;//分行代碼
			$_af[$index] = '' ;//傳真電話
			$_ae[$index] = '' ;//E-Mail
			$_a[$index] = '保證費';
			unset($_tmp1) ;
			unset($_tmp) ;
			$index++;
			##
			//第一家仲介
			for ($c=0; $c < count($branchBank); $c++) { 
				$_sn[$index] =$storeName; //店家名稱
				$_si[$index] = $storeId;//店家編號
				$_st[$index] = $_store_target;//店家服務對象
				$_an[$index] = $branchBank[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_fax ;//傳真電話
				$_ae[$index] = $_store_email ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target] ;//身分title
				$index ++ ;
			}

			##
			//第二間仲介
			for ($c=0; $c < count($branchBank1); $c++) { 
				$_sn[$index] =$storeName1; //店家名稱
				$_si[$index] = $storeId1;//店家編號
				$_st[$index] = $_store_target1;//店家服務對象
				$_an[$index] = $branchBank1[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank1[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank1[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank1[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxA ;//傳真電話
				$_ae[$index] = $_store_emailA ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target1] ;//身分title
				$index ++ ;
			}


			//第三家仲介
			for ($c=0; $c < count($branchBank2); $c++) { 
				$_sn[$index] =$storeName2; //店家名稱
				$_si[$index] = $storeId2;//店家編號
				$_st[$index] = $_store_target2;//店家服務對象
				$_an[$index] = $branchBank2[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank2[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank2[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank2[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxB ;//傳真電話
				$_ae[$index] = $_store_emailB ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target2] ;//身分title
				$index ++ ;
			}

			

			//第四家仲介
			for ($c=0; $c < count($branchBank3); $c++) { 
				$_sn[$index] =$storeName3; //店家名稱
				$_si[$index] = $storeId3;//店家編號
				$_st[$index] = $_store_target3;//店家服務對象
				$_an[$index] = $branchBank3[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank3[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank3[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank3[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxC ;//傳真電話
				$_ae[$index] = $_store_emailC ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target3] ;//身分title
				$index ++ ;
			}

			##
			$i = count($_a) ;

			
			##
			
		break;
			
		case "解除契約":
			$index = 0 ;
			//賣方
			for ($c=0; $c < count($ownerBank); $c++) { 
				$_a[$index] = '賣方' ;	
				$_an[$index] = $ownerBank[$c]['bankAccName'];
				$_ac[$index] = $ownerBank[$c]['bankAccNum'];
				$_ab3[$index] = $ownerBank[$c]['bank'];
				$_ab4[$index] = $ownerBank[$c]['bankBranch'];
				$index++;
			}
			

			//買方
			for ($c=0; $c < count($buyerBank); $c++) { 
				$_a[$index] = '買方' ;	
				$_an[$index] = $buyerBank[$c]['bankAccName'];
				$_ac[$index] = $buyerBank[$c]['bankAccNum'];
				$_ab3[$index] = $buyerBank[$c]['bank'];
				$_ab4[$index] = $buyerBank[$c]['bankBranch'];
				$index++;
			}
			

			//代書
			//代書帳戶名稱
			
			for ($c=0; $c < count($scrivenerBank); $c++) { 
				$_an[$index] = $scrivenerBank[$c]['bankAccName'];	//帳戶
				$_ac[$index] = $scrivenerBank[$c]['bankAccNum'];	//帳號	
				$_ab3[$index] = $scrivenerBank[$c]['bank'] ;		//銀行總行代碼	
				$_ab4[$index] = $scrivenerBank[$c]['bankBranch'];		//分行代碼
				$_af[$index] = '' ;	//代書傳真電話
				$_ae[$index] = $_s_email ;//代書E-Mail
				$_a[$index] = '地政士' ;//身分title
				$index++;
			}

			##
			//第一建經活儲帳戶、帳號
			// $_tmp = $_account_name ; 
			// $_tmp1 = $_account_no ;
			
			$_an[$index] =  $_account_name ;//帳戶
			$_ac[$index] = $_account_no ;//帳號
			$_ab3[$index] = $main_bank ;//銀行總行代碼
			$_ab4[$index] = $branch_bank ;//銀行分行代碼
			$_af[$index] = '' ;//傳真電話
			$_ae[$index] = '' ;//email
			$_a[$index] = '保證費';
			$index ++ ;
			// unset($_tmp) ;
			##

			//第一家仲介
			for ($c=0; $c < count($branchBank); $c++) { 
				$_sn[$index] =$storeName; //店家名稱
				$_si[$index] = $storeId;//店家編號
				$_st[$index] = $_store_target;//店家服務對象
				$_an[$index] = $branchBank[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_fax ;//傳真電話
				$_ae[$index] = $_store_email ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target] ;//身分title
				$index ++ ;
			}

			##
			//第二間仲介
			for ($c=0; $c < count($branchBank1); $c++) { 
				$_sn[$index] =$storeName1; //店家名稱
				$_si[$index] = $storeId1;//店家編號
				$_st[$index] = $_store_target1;//店家服務對象
				$_an[$index] = $branchBank1[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank1[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank1[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank1[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxA ;//傳真電話
				$_ae[$index] = $_store_emailA ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target1] ;//身分title
				$index ++ ;
			}


			//第三家仲介
			for ($c=0; $c < count($branchBank2); $c++) { 
				$_sn[$index] =$storeName2; //店家名稱
				$_si[$index] = $storeId2;//店家編號
				$_st[$index] = $_store_target2;//店家服務對象
				$_an[$index] = $branchBank2[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank2[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank2[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank2[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxB ;//傳真電話
				$_ae[$index] = $_store_emailB ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target2] ;//身分title
				$index ++ ;
			}

			

			//第四家仲介
			for ($c=0; $c < count($branchBank3); $c++) { 
				$_sn[$index] =$storeName3; //店家名稱
				$_si[$index] = $storeId3;//店家編號
				$_st[$index] = $_store_target3;//店家服務對象
				$_an[$index] = $branchBank3[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank3[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank3[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank3[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxC ;//傳真電話
				$_ae[$index] = $_store_emailC ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target3] ;//身分title
				$index ++ ;
			}
			
			$i = count($_a) ;
			##
			
		break;

		case "扣繳稅款":
			$index = 0;
		 	// $s_index = 0 ;
			// if ($s_bk11) { $s_index = 1 ; }
			//代書帳戶名稱
			
			for ($c=0; $c < count($scrivenerBank); $c++) { 
				$_an[$index] = $scrivenerBank[$c]['bankAccName'];	//帳戶
				$_ac[$index] = $scrivenerBank[$c]['bankAccNum'];	//帳號	
				$_ab3[$index] = $scrivenerBank[$c]['bank'] ;		//銀行總行代碼	
				$_ab4[$index] = $scrivenerBank[$c]['bankBranch'];		//分行代碼
				$_af[$index] = '' ;	//代書傳真電話
				$_ae[$index] = $_s_email ;//代書E-Mail
				$_a[$index] = '地政士' ;//身分title
				$index++;
			}
			
			//print_r($_a) ;
			$i = count($_a) ;
		break;
		case "仲介服務費":
			$index = 0;
			//$_a = 身分集合

			//第一家仲介
			for ($c=0; $c < count($branchBank); $c++) { 
				$_sn[$index] =$storeName; //店家名稱
				$_si[$index] = $storeId;//店家編號
				$_st[$index] = $_store_target;//店家服務對象
				$_an[$index] = $branchBank[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_fax ;//傳真電話
				$_ae[$index] = $_store_email ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target] ;//身分title
				$index ++ ;
			}

			##
			//第二間仲介
			for ($c=0; $c < count($branchBank1); $c++) { 
				$_sn[$index] =$storeName1; //店家名稱
				$_si[$index] = $storeId1;//店家編號
				$_st[$index] = $_store_target1;//店家服務對象
				$_an[$index] = $branchBank1[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank1[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank1[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank1[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxA ;//傳真電話
				$_ae[$index] = $_store_emailA ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target1] ;//身分title
				$index ++ ;
			}


			//第三家仲介
			for ($c=0; $c < count($branchBank2); $c++) { 
				$_sn[$index] =$storeName2; //店家名稱
				$_si[$index] = $storeId2;//店家編號
				$_st[$index] = $_store_target2;//店家服務對象
				$_an[$index] = $branchBank2[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank2[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank2[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank2[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxB ;//傳真電話
				$_ae[$index] = $_store_emailB ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target2] ;//身分title
				$index ++ ;
			}

			

			//第四家仲介
			for ($c=0; $c < count($branchBank3); $c++) { 
				$_sn[$index] =$storeName3; //店家名稱
				$_si[$index] = $storeId3;//店家編號
				$_st[$index] = $_store_target3;//店家服務對象
				$_an[$index] = $branchBank3[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank3[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank3[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank3[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxC ;//傳真電話
				$_ae[$index] = $_store_emailC ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target3] ;//身分title
				$index ++ ;
			}
			##
			
			$i = count($_a) ;
		break;
		case "代清償":
		 //    $bank3 = "";
			// $bank4 = "";
			// $_t_name = "";
			// $_t_account = "";
			$_a[0]="";
			$_an[0] = "";	
			$_ac[0] = "";
			$_ab3[0] = "";
			$_ab4[0] = "";
			$i=1;
		break;

		case '保留款撥付':
		 	
			$index = 0 ;
			
			//賣方
			//賣方
			for ($c=0; $c < count($ownerBank); $c++) { 
				$_a[$index] = '賣方' ;	
				$_an[$index] = $ownerBank[$c]['bankAccName'];
				$_ac[$index] = $ownerBank[$c]['bankAccNum'];
				$_ab3[$index] = $ownerBank[$c]['bank'];
				$_ab4[$index] = $ownerBank[$c]['bankBranch'];
				$index++;
			}
			
			##
			//買方
			for ($c=0; $c < count($buyerBank); $c++) { 
				$_a[$index] = '買方' ;	
				$_an[$index] = $buyerBank[$c]['bankAccName'];
				$_ac[$index] = $buyerBank[$c]['bankAccNum'];
				$_ab3[$index] = $buyerBank[$c]['bank'];
				$_ab4[$index] = $buyerBank[$c]['bankBranch'];
				$index++;
			}
			
			##
			//代書帳戶名稱
			for ($c=0; $c < count($scrivenerBank); $c++) { 
				$_an[$index] = $scrivenerBank[$c]['bankAccName'];	//帳戶
				$_ac[$index] = $scrivenerBank[$c]['bankAccNum'];	//帳號	
				$_ab3[$index] = $scrivenerBank[$c]['bank'] ;		//銀行總行代碼	
				$_ab4[$index] = $scrivenerBank[$c]['bankBranch'];		//分行代碼
				$_af[$index] = '' ;	//代書傳真電話
				$_ae[$index] = $_s_email ;//代書E-Mail
				$_a[$index] = '地政士' ;//身分title
				$index++;
			}

			//第一建經活儲、
			$_tmp = $_account_name ;
			$_tmp1 = $_account_no ;
			
			$_an[$index] =  $_account_name ;//帳戶
			$_ac[$index] = $_account_no ;//帳號

			$_ab3[$index] = $main_bank ;//總行代碼
			$_ab4[$index] = $branch_bank ;//分行代碼
			$_af[$index] = '' ;//傳真電話
			$_ae[$index] = '' ;//E-Mail
			$_a[$index] = '保證費';
			unset($_tmp1) ;
			unset($_tmp) ;
			$index++;
			##

			//第一家仲介
			for ($c=0; $c < count($branchBank); $c++) { 
				$_sn[$index] =$storeName; //店家名稱
				$_si[$index] = $storeId;//店家編號
				$_st[$index] = $_store_target;//店家服務對象
				$_an[$index] = $branchBank[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_fax ;//傳真電話
				$_ae[$index] = $_store_email ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target] ;//身分title
				$index ++ ;
			}

			##
			//第二間仲介
			for ($c=0; $c < count($branchBank1); $c++) { 
				$_sn[$index] =$storeName1; //店家名稱
				$_si[$index] = $storeId1;//店家編號
				$_st[$index] = $_store_target1;//店家服務對象
				$_an[$index] = $branchBank1[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank1[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank1[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank1[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxA ;//傳真電話
				$_ae[$index] = $_store_emailA ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target1] ;//身分title
				$index ++ ;
			}


			//第三家仲介
			for ($c=0; $c < count($branchBank2); $c++) { 
				$_sn[$index] =$storeName2; //店家名稱
				$_si[$index] = $storeId2;//店家編號
				$_st[$index] = $_store_target2;//店家服務對象
				$_an[$index] = $branchBank2[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank2[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank2[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank2[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxB ;//傳真電話
				$_ae[$index] = $_store_emailB ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target2] ;//身分title
				$index ++ ;
			}

			

			//第四家仲介
			for ($c=0; $c < count($branchBank3); $c++) { 
				$_sn[$index] =$storeName3; //店家名稱
				$_si[$index] = $storeId3;//店家編號
				$_st[$index] = $_store_target3;//店家服務對象
				$_an[$index] = $branchBank3[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank3[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank3[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank3[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxC ;//傳真電話
				$_ae[$index] = $_store_emailC ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target3] ;//身分title
				$index ++ ;
			}
			##
			
			$i = count($_a) ;
			##
			
		break;
		case '建經發函終止':
			$index = 0 ;

			//賣方
			for ($c=0; $c < count($ownerBank); $c++) { 
				$_a[$index] = '賣方' ;	
				$_an[$index] = $ownerBank[$c]['bankAccName'];
				$_ac[$index] = $ownerBank[$c]['bankAccNum'];
				$_ab3[$index] = $ownerBank[$c]['bank'];
				$_ab4[$index] = $ownerBank[$c]['bankBranch'];
				$index++;
			}

			//買方
			for ($c=0; $c < count($buyerBank); $c++) { 
				$_a[$index] = '買方' ;	
				$_an[$index] = $buyerBank[$c]['bankAccName'];
				$_ac[$index] = $buyerBank[$c]['bankAccNum'];
				$_ab3[$index] = $buyerBank[$c]['bank'];
				$_ab4[$index] = $buyerBank[$c]['bankBranch'];
				$index++;
			}

			//代書
			//代書帳戶名稱
			for ($c=0; $c < count($scrivenerBank); $c++) { 
				$_an[$index] = $scrivenerBank[$c]['bankAccName'];	//帳戶
				$_ac[$index] = $scrivenerBank[$c]['bankAccNum'];	//帳號	
				$_ab3[$index] = $scrivenerBank[$c]['bank'] ;		//銀行總行代碼	
				$_ab4[$index] = $scrivenerBank[$c]['bankBranch'];		//分行代碼
				$_af[$index] = '' ;	//代書傳真電話
				$_ae[$index] = $_s_email ;//代書E-Mail
				$_a[$index] = '地政士' ;//身分title
				$index++;
			}

			//第一建經活儲帳戶、帳號
			// $_tmp = $_account_name ; 
			// $_tmp1 = $_account_no ;
			
			$_an[$index] =  $_account_name ;//帳戶
			$_ac[$index] = $_account_no ;//帳號
			$_ab3[$index] = $main_bank ;//銀行總行代碼
			$_ab4[$index] = $branch_bank ;//銀行分行代碼
			$_af[$index] = '' ;//傳真電話
			$_ae[$index] = '' ;//email
			$_a[$index] = '保證費';
			// unset($_tmp) ;
			$index++;
			##

			//第一家仲介
			for ($c=0; $c < count($branchBank); $c++) { 
				$_sn[$index] =$storeName; //店家名稱
				$_si[$index] = $storeId;//店家編號
				$_st[$index] = $_store_target;//店家服務對象
				$_an[$index] = $branchBank[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_fax ;//傳真電話
				$_ae[$index] = $_store_email ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target] ;//身分title
				$index ++ ;
			}

			##
			//第二間仲介
			for ($c=0; $c < count($branchBank1); $c++) { 
				$_sn[$index] =$storeName1; //店家名稱
				$_si[$index] = $storeId1;//店家編號
				$_st[$index] = $_store_target1;//店家服務對象
				$_an[$index] = $branchBank1[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank1[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank1[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank1[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxA ;//傳真電話
				$_ae[$index] = $_store_emailA ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target1] ;//身分title
				$index ++ ;
			}


			//第三家仲介
			for ($c=0; $c < count($branchBank2); $c++) { 
				$_sn[$index] =$storeName2; //店家名稱
				$_si[$index] = $storeId2;//店家編號
				$_st[$index] = $_store_target2;//店家服務對象
				$_an[$index] = $branchBank2[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank2[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank2[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank2[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxB ;//傳真電話
				$_ae[$index] = $_store_emailB ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target2] ;//身分title
				$index ++ ;
			}

			

			//第四家仲介
			for ($c=0; $c < count($branchBank3); $c++) { 
				$_sn[$index] =$storeName3; //店家名稱
				$_si[$index] = $storeId3;//店家編號
				$_st[$index] = $_store_target3;//店家服務對象
				$_an[$index] = $branchBank3[$c]['bankAccName'] ;//帳戶
				$_ac[$index] = $branchBank3[$c]['bankAccNum'];//帳號
				$_ab3[$index] = $branchBank3[$c]['bank'];//銀行總行代碼
				$_ab4[$index] = $branchBank3[$c]['bankBranch'];//分行代碼
				$_af[$index] = $_store_faxC ;//傳真電話
				$_ae[$index] = $_store_emailC ;//E-Mail
				$_a[$index] = '仲介'.$realtyTarget[$_store_target3] ;//身分title
				$index ++ ;
			}
			##
			
			
			$i = count($_a) ;
			##
		break;
	 }
	 //echo $bank3."/".$bank4;
	if ($i==0) {
	 	$i = 1;
	 	# code...
	 }
	 
?>

<?php for ($j=0;$j<$i;$j++) { ?>
<!-- 點交出款
	地政士跟買賣方拉開距離
	仲介服務費拉到保證費下方 -->
	<?php if ($_a[$j] == '地政士' && ($radiokind == '點交' || $radiokind == '解除契約' || $radiokind == '保留款撥付' || $radiokind == '建經發函終止')): ?>
		<div style="margin-top: 5px;width:1600px;">&nbsp;</div>
	<?php endif ?>

<div style="border:1px dotted #999999; width:1600px; margin:3px;">
<?php
	
	if ($scrivenerBankCount > 1 && $_a[$j] == '地政士') {
		$color = 'rgb(218,242,142)';
		
	}elseif (($branchBankCount+$branchBankCount1+$branchBankCount2+$branchBankCount2) > 1 && preg_match("/^仲介/",$_a[$j])) {
		$color = 'rgb(255,255,170)';
	}else{
		$color = 'rgb(255,255,255)';
	}
	$lock = ($_st[$j] == $checkIden || $_st[$j] == 1)? '':"$j";
	
	

	?>
	<div style="display:block;" >
		<?=$_sn[$j]?>

		
		<?php
		// print_r($_si);
				if ($radiokind == '仲介服務費') { 

					if ($lock != '') { ?>
						<input type="button" value="解鎖" onclick="unLock(<?=$j?>)" />
					<?php } ?>
						
					
					<input type="hidden" value="<?=$lock?>" id="service<?=$j?>" class="lock"/>
		<?php	}
		?>
		
		<!-- <input type="button" value="" /> -->
	</div>
	
	
<table width="1600" border="0" class="font12" id="ttt" cellpadding="0" cellspacing="0">
	
    <tr id="tr_pos" style='background-color:<?=$color?>' class="lock<?=$j?>">
      	<td width="132">
      		<input type="hidden" name="storeId[]" value="<?=$_si[$j]?>" />
      		<input type="hidden" name="smsSend"  value="<?=$smsSend?>" />
	      	<label for="target[]"></label>
	        *
	        <select name="target[]" id="target<?=$j?>" onchange="setTxt(<?=$j?>,this.value,'t_txt','objKind','ot_money')" >
	          	<option value="">角色選擇</option>
	         	<option value="賣方" <?php if ( $_a[$j] == '賣方') { echo 'selected="selected"';}?>>賣方</option>
	          	<option value="買方" <?php if ( $_a[$j] == '買方') { echo 'selected="selected"';}?>>買方</option>
	          	<option value="地政士" <?php if ( $_a[$j] == '地政士') { echo 'selected="selected"';}?>>地政士</option>
	          	<option value="仲介" style="background-color:yellow;" <?php if (preg_match("/^仲介/",$_a[$j])) { echo 'selected="selected"';}?>>仲介<?php if (preg_match("/^仲介/",$_a[$j])) { echo mb_substr($_a[$j],2,100,"utf-8") ; } ?></option>
	          	<option value="保證費" <?php if ( $_a[$j] == '保證費') { echo 'selected="selected"';}?>>保證費</option>
	      	</select>
	     	<br />
	      
	      	*

	      	<input type="hidden" name="code2[]" id="code2<?=$j?>" value="" />
		    <select name="export[]" id="export<?=$j?>" onchange="setCode2('export<?=$j?>',<?=$j?>)" >
		        <option value="" selected="selected">交易類別</option>
		        <option value="01">聯行轉帳</option>
		        <option value="01">虛轉虛</option>
		        <option value="02">跨行代清償</option>
				<option value="03">聯行代清償</option>
		        <option value="04">大額繳稅</option>
		        <option value="05">臨櫃開票</option>
		        <option value="05">臨櫃領現</option>
		        <option value="06">利息</option>
		    </select>
		    <br />
		    *
		    <select name="objKind[]" id="objKind<?=$j?>" class="objKind<?=$j?>" onchange="show_service(this.value,'s_service_<?php echo $j;?>')" >
		        <option value="" selected="selected" >項目</option>
		        <?php
		        		if ( $_a[$j] != '保證費'){ ?>
		        			<option value="賣方先動撥" <?php if ( $radiokind == '賣方先動撥') { echo 'selected="selected"';}?>>賣方先動撥</option>
					        <option value="仲介服務費" <?php if ( $radiokind == '仲介服務費') { echo 'selected="selected"';}?>>仲介服務費</option>
					        <?php
					        if ( $radiokind == '扣繳稅款') {
					        ?>
					        <option value="扣繳稅款" <?php if ( $radiokind == '扣繳稅款') { echo 'selected="selected"';}?>>扣繳稅款</option>
					        <?php } ?>
					        <option value="代清償" <?php if ( $radiokind == '代清償') { echo 'selected="selected"';}?>>代清償</option>
					        

		        <?php 	}
		        ?>
		        
		        <option value="點交(結案)" <?php if ( $radiokind == '點交') { echo 'selected="selected"';}?>>點交(結案)</option>
		         <?php
		        		if ( $_a[$j] != '保證費'){ ?>
		        <option value="其他">其他</option>
		        <option value="調帳">調帳</option>
		        <?php 	}
		        ?>
		        <option value="解除契約" <?php if ( $radiokind == '解除契約') { echo 'selected="selected"';}?>>解約/終止履保</option>
		        <?php
		        		if ( $_a[$j] != '保證費'){ ?>
		        <option value="保留款撥付" <?php if ( $radiokind == '保留款撥付') { echo 'selected="selected"';}?>>保留款撥付</option>
		    	<?php 	}
		        ?>
		    	<option value="建經發函終止"<?php if ( $radiokind == '建經發函終止') { echo 'selected="selected"';}?>>建經發函終止</option>
		    </select>
		    <?php if (($radiokind == '扣繳稅款' || $radiokind == '賣方先動撥' || $radiokind == '代清償') && $_vr_bank == '台新'): ?>
		    	<br />*
		    	<select name="taxScrivener[]" id="taxScrivener<?=$j?>" class="taxScrivener" onchange="checkScrivenerTax(<?=$j?>)">
		    		<option value="">特殊項目</option>
		    		<option value="01">申請公司代墊</option>
		    		<option value="02">返還公司代墊</option>
		    		<option value="03" selected="selected">不用代墊</option>
		    		<option value="04">申請代理出款</option>
		    		<option value="05">公司代裡出款</option>
		    	</select>
		    <?php endif ?>
      	</td>
      	<td width="231">
      		
      		*解匯行
	        <label for="bank3[]"></label>
	        <?php
				$sql = "select * from tBank where bCode not in ('1','7') and bBank4 = '' and bBank3 !='000' AND bOK = 0 order by  bBank3 asc ";
				$rs2 = $conn->CacheExecute(1,$sql);
			?>
	        <select name="bank3[]" id="bank3[]" class="bank b3_<?php echo $j;?>" onchange="bank_select_index(this.value,'b4_<?php echo $j;?>','branch_<?php echo $j;?>','<?php echo $j;?>','')" style=" width:110px;" >
	          <option value="" >選擇銀行</option>
	          <?php while( !$rs2->EOF ) {?>
	          <option value="<?php echo $rs2->fields["bBank3"];?>" <?php if (trim($rs2->fields["bBank3"]) == $_ab3[$j]) { echo 'selected="selected"';} ?> > <?php echo '('.$rs2->fields["bBank3"].')'.trim($rs2->fields["bBank4_name"]);?></option>
	          <?php
			 $rs2->MoveNext();
			} 
			?>
	        </select>
			<!-- <a href="get_bank.php?cl=<?php echo $j;?>" class="ajax font9">選擇</a> -->
	        <?php
			$sql = "select * from tBank where bCode not in ('1','7') and bBank4 != '' and bBank3='".$_ab3[$j]."'  order by bCodeTitle , bBank3 asc ";
			$rs3 = $conn->CacheExecute(1,$sql);
			?>
	        <label for="bank4[]"><br />
	        *分行別</label>
	        <select name="bank4[]" id="bank4[]" style="width:130px;" class="bank b4_<?php echo $j;?>" onchange="bankphone(<?php echo $j;?>,1)" >
	         <option value="" >選擇分行</option>
	         <?php while( !$rs3->EOF ) {?> 
	         <option value="<?php echo $rs3->fields["bBank4"];?>" <?php if (trim($rs3->fields["bBank4"]) == $_ab4[$j]) { echo 'selected="selected"';} ?> > <?php echo '('.$rs3->fields['bBank4'].')'.trim($rs3->fields["bBank4_name"]);?></option>
	         <?php
			 $rs3->MoveNext();
			} 
			?> 
	        </select>
			<!-- <a id="branch_<?php echo $j;?>" href="#" class="ajax font9">選擇</a> -->
			<br />
			<span id="bankp<?php echo $j;?>" style="color:#FF0000;">
				
			</span>
        </td>
     	<td width="197">
     	 	
     	 	*戶名
        	<label for="t_name[]"></label>
        	<?php 
        		$str = ($_a[$j] == '賣方' && $_vr_bank != '台新' && ($radiokind == '點交' || $radiokind == '解除契約'))?"onkeyup=\"checkowner('".$j."','".substr($vr_code,5)."')\"":"";
        		// echo $radiokind;
        		
        	 ?>
	      	<input name="t_name[]" type="text" id="t_name<?php echo $j;?>" size="14" <?=$str?> onblur="rel_words('t_name<?php echo $j;?>')" value="<?php echo n_to_w($_an[$j]);?>" />
	     
		    <br />
		    *帳號
		    <label for="t_account[]"></label>
		    <input name="t_account[]" type="text" id="t_account[]" class="bb_<?php echo $j;?>" size="14" maxlength="14" onkeyup="bank_check()" value="<?php echo $_ac[$j];?>" />
      	</td>
      	<td width="285"><div id="s_change" style="display:block;">調帳選擇<select name="change_s[]" id="change_s[]" >
          	<option value="">請選擇入帳記錄</option>
          	<?php
			$dates = date("Ymd",mktime(0,0,0,date("m"),(date("d")-10),date("Y"))) ;	//顯示十日內的紀錄
			//$dates = '20131101' ;
			$dates = (substr($dates,0,4)-1911).substr($dates,4,2).substr($dates,6,2) ;
			$sql= "select * from tExpense where eTradeDate>='$_dates' and eStatusIncome=1 and eTradeStatus=0 and ePayTitle not like '%網路整批%' order by eTradeDate,eTradeNum";
			$rsx = $conn->Execute($sql);
			while( !$rsx->EOF ) {
			?>
	          <option value="<?php echo $rsx->fields["id"];?>"><?php echo $rsx->fields["eTradeDate"]." / ".substr($rsx->fields["eDepAccount"],2) . " / " . (int)substr($rsx->fields["eLender"],0,-2)."元";?></option>
	          <?php
				$rsx->MoveNext();
	  		} 
			?>  
	          </select>
	        <br /></div>*金額NT$
	        <label for="t_money[]"></label>
	        <input name="t_money[]" <?php if ($radiokind == '扣繳稅款') { echo 'class="taxM" readonly="readonly" ' ; } ?>type="text" <?php if ($_a[$j] == '仲介') { echo 'style="background-color:yellow;text-align:right;" '; } else { echo 'style="text-align:right;" ' ; } ?>id="ot_money<?php echo $j;?>" onKeyUp="recal('o',<?php echo $j;?>)"  size="10" title="tm<?=$j?>" value="<?php echo ($_a[$j] == '保證費')?$realCertifiedMoney:'';?>"/>
	        元
	        <input name="t_cost[]" type="hidden" id="t_cost[]" value="0" />
	        <?php if ($radiokind == '扣繳稅款'): ?>
	        	<span id='exportTax'>
	        		<a href='#' id='taxM<?=$j?>'  style='font-size:9pt;' onclick="checkChoiceDetail(<?php echo $j;?>,1)">選擇出款</a>
	        		<input type='hidden' class='taxRemark' name='taxPayId' value='' title='taxM<?=$j?>' class="lock">
	        	</span>
	        	<span id='returnTax' style="display:none;">
					<a href='#' id='taxM<?=$j?>'  style='font-size:9pt;' onclick="checkChoiceDetail(<?php echo $j;?>,2)">選擇出款</a>
					<input type='hidden' class='taxRemark' name='taxReturnPayId' value='' title='taxM<?=$j?>' class="lock">
					<br />繳稅日期
					<input type="text" name="datepicker<?php echo $j;?>" value="<?=$rs->fields["tObjKind2Date"]?>" class="dt" style="width:100px;"/>
	        	</span>

	        <?php endif ?>
			
	        <br />
	        <div id="s_service_<?php echo $j;?>" style="display:<?php if ( $radiokind == '仲介服務費') { echo 'block';} else { echo 'none';}?>;">
	         <!--  買方服務費:
	          <label for="t_buyer[]"></label>
	          <input name="t_buyer[]" type="text" style="text-align:right;" id="t_buyer[]" size="10" onblur="check_money()" />
	          元<br />
	          賣方服務費:
	          <label for="t_seller[]"></label>
	          <input name="t_seller[]" type="text" style="text-align:right;" id="t_seller[]" size="10" onblur="check_money()"/>
	          元 -->

	           對象:
	          	<select name="serviceTarget[]" id="oserviceTarget<?php echo $j;?>" onchange="serviceMoney('o',<?php echo $j;?>)" >
	          	<?php
	          	if ($_st[$j] == 1) {
	          		if ($checkIden == 2) {
	          			$selected = 'selected=selected';
	          			$selected1 = '';
	          		}elseif ($checkIden == 3) {
						$selected = '';
		          		$selected1 = 'selected=selected';
					}
	          			
	          			
	          	}else if ($_st[$j] == 2) {
	          		$selected = 'selected=selected';
	          		$selected1 = '';
					
				}elseif ($_st[$j] == 3) {
					$selected = '';
	          		$selected1 = 'selected=selected';
				} ?>

	          	<option value="owner" <?=$selected?>>賣方服務費</option>
	          	<option value="buyer" <?=$selected1?>>買方服務費</option>
	          		
	         	</select><br />

	          買方服務費:
	          <input  type="text" style="text-align:right;" class="ot_buyer<?php echo $j;?> lbuyer<?php echo $j;?>" size="10" disabled/><br />
	          <input type="hidden" name="t_buyer[]" class="ot_buyer<?php echo $j;?>"/>
	          賣方服務費:
	          <input  type="text" style="text-align:right;" class="ot_seller<?php echo $j;?> lseller<?php echo $j;?>" size="10" disabled/><br />
	          <input type="hidden" name="t_seller[]" class="ot_seller<?php echo $j;?>"/>
	         
	        </div>
     	</td>
     
      	<td width="182" >
	      	證號
	      	<label for="pid[]"></label>
	      	<input name="pid[]" type="text" id="pid[]" size="10" />
	      
	      	<br />
	      	EMail 
	      	<label for="email[]"></label>
	      	<input name="email[]" type="text" id="email[]" size="13" value="<?php echo $_ae[$j];?>" />
	      	<br />
	      	FAX 
	      	<label for="fax[]"></label>
	      	<input name="fax[]" type="text" id="fax[]" size="15" value="<?php echo $_af[$j];?>" />
	    </td>
	    <td width="167">*附言(勿按ENTER換行)<br />
	        <label for="t_cost[]"></label>
	        <label for="t_txt[]"></label>
	        <textarea name="t_txt[]" id="t_txt<?=$j?>" cols="20" class="t_txt" rows="5" onblur="rel_words('t_txt<?=$j?>')" ><?php if ($_a[$j] == '保證費') { echo n_to_w(substr($vr_code,5,9));}

	        		if ($radiokind == '仲介服務費') {
	        			
						$tmpTxtArr = explode('_', $_sn[$j]);

						$tmpTxtArr[1] = str_replace('直營店', '', $tmpTxtArr[1]);
						$tmpTxtArr[1] = str_replace('特許加盟店', '', $tmpTxtArr[1]);
						$tmpTxtArr[1] = str_replace('加盟店', '', $tmpTxtArr[1]);
						echo $tmpTxtArr[1];
						unset($tmpTxtArr);
	        		}
	        	?></textarea>
        </td>
        <td width="130" align="center">
        	<!-- 不發送簡訊<br>
        	<input type="checkbox" name="tSend[]" id="tSend[]" value="1" /> -->
        	<?php if ($smsSend == 1): ?>
        		<input type="hidden" name="tSend[]" value="1" />
        	<?php else: ?>
        		<input type="hidden" name="tSend[]" value="0" />
        	<?php endif ?>

        </td>
        <?php
        if ($_vr_bank == '永豐' || $_vr_bank == '台新') { ?>
        	
        <td width="130" >
			<div id="Note<?=$j?>">存摺備註欄<br>(限聯行轉帳且字數為六個字)</div>
        	<input type="text" name="bankshowtxt[]" id="bankshowtxt<?=$j?>" maxlength="6" value="<?=$owner.$buyer?>" />
        </td>
        <?php } ?>
        <td>
        	<!-- 官網代書用備註
        	<textarea name="scrivenerNote[]" id="" cols="20" rows="5"></textarea> -->
        </td>
    </tr>
    
    
  </table>
</div>  
<?php } ?>
<div id="toggle_other" ><div style="float:left;">新增</div><div id="pp" class="ui-icon ui-icon-triangle-1-e"></div></div>
<div id="other_all">
<?php 
$index = $i - 1 ; 
for ($j=1;$j<=3;$j++){ 
?>
<div style="border:1px dotted #900; width:1600px; margin:3px;" id="o_<?php echo $j;?>">
<table width="1600" border="0" class="font12" id="ttt">
    <tr id="tr_pos">
   	 <input type="hidden" name="storeId[]" value="" />
      	<td width="132"><label for="target[]"></label>
        	*
	        <select name="target[]" id="target<?=($j+$index)?>" onchange="setTxt(<?=($j+$index)?>,this.value,'t_txt','objKind','nt_money')">
	          <option value="">角色選擇</option>
	          <option value="賣方" <?php if ( $target[$i] == 'seller') { echo 'selected="selected"';}?>>賣方</option>
	          <option value="買方" <?php if ( $target[$i] == 'buyer') { echo 'selected="selected"';}?>>買方</option>
	          <option value="地政士" <?php if ( $target[$i] == 'scrivener') { echo 'selected="selected"';}?>>地政士</option>
	          <option value="仲介" style="background-color:yellow;" <?php if ( $target[$i] == 'realestate') { echo 'selected="selected"';}?>>仲介</option>
	          <option value="保證費" <?php if ( $target[$i] == 'guarantee') { echo 'selected="selected"';}?>>保證費</option>
		    </select>
		    <br />
      
	    	*
	    	<input type="hidden" name="code2[]" id="code2<?=($j+$index)?>" value="" />
		    <select name="export[]" id="export<?=($j+$index)?>" onchange="setCode2('export<?=($j+$index)?>',<?=($j+$index)?>)">
		        <option value="" selected="selected">交易類別</option>
		        <option value="01">聯行轉帳</option>
		        <option value="01">虛轉虛</option>
		        <option value="02">跨行代清償</option>
				<option value="03">聯行代清償</option>
		        <option value="04">大額繳稅</option>
		        <option value="05">臨櫃開票</option>
		        <option value="05">臨櫃領現</option>
		        <option value="06">利息</option>
		    </select>
		      <br />
		    *
		    <select name="objKind[]" id="objKind<?=($j+$index)?>" class="objKind<?php echo $j;?>" onchange="show_service(this.value,'s_serviceN_<?php echo $j;?>','<?php echo $j;?>','n')">
		        <option value="" selected="selected">項目</option>
		        <option value="賣方先動撥">賣方先動撥</option>
		        <option value="仲介服務費">仲介服務費</option>
		        <!--<option value="扣繳稅款">扣繳稅款</option>-->
		        <option value="代清償">代清償</option>
		        <option value="點交(結案)">點交(結案)</option>
		        <option value="其他">其他</option>
		        <option value="調帳">調帳</option>
		        <option value="解除契約">解約/終止履保</option>
		        <option value="保留款撥付">保留款撥付</option>
		        <option value="建經發函終止">建經發函終止</option>
		    </select>
        </td>
    	<td width="228">*解匯行
	        <label for="bank3[]"></label>
	        <?php
				$sql = "select * from tBank where bCode not in ('1','7') and bBank4 = '' order by bCodeTitle , bBank3 asc ";
				$rs2 = $conn->CacheExecute(1,$sql);
			?>
	        <select name="bank3[]" id="bank3[]" class="bank b3n_<?php echo $j;?>" onchange="bank_select_index(this.value,'b4n_<?php echo $j;?>','branchn_<?php echo $j;?>','<?php echo $j;?>','n')" style=" width:110px;">
	          <option value="" >選擇銀行</option>
	          <?php while( !$rs2->EOF ) {?>
	          <option value="<?php echo $rs2->fields["bBank3"];?>"  > <?php echo '('.$rs2->fields['bBank3'].')'.trim($rs2->fields["bBank4_name"]);?></option>
	          <?php
			 $rs2->MoveNext();
			} 
			?>
	        </select>
			<!-- <a href="get_bank.php?cl=<?php echo $j;?>&cs=n" class="ajax font9">選擇</a> -->
	        <?php
			$sql = "select * from tBank where bCode not in ('1','7') and bBank4 != '' and bBank3='$bank3' and bOK = 0 order by bCodeTitle , bBank3 asc ";
			$rs3 = $conn->CacheExecute(1,$sql);
			?>
	        <br /><label for="bank4[]"></label>*分行別
	        <select name="bank4[]" id="bank4[]" style="width:130px;" class="bank b4n_<?php echo $j;?>" onchange="bankphone(<?php echo $j;?>,2)">
	        <option value="" >選擇分行</option>
	         <?php while( !$rs3->EOF ) {?> 
	         <option value="<?php echo $rs3->fields["bBank4"];?>"  > <?php echo '('.$rs3->fields['bBank4'].')'. trim($rs3->fields["bBank4_name"]);?></option>
	         <?php
			 $rs3->MoveNext();
			} 
			?> 
	        </select>
			<!-- <a id="branchn_<?php echo $j;?>" href="#" class="ajax font9">選擇</a><br /> -->
			<span id="bankp<?php echo $j;?>"></span>
        </td>
    	<td width="200">*戶名
       		<label for="t_name[]"></label>
	      	<input name="t_name[]" type="text" id="t_namen<?php echo $j;?>" size="14" class="tn_<?php echo $j;?>" onblur="rel_words('t_namen<?php echo $j;?>')"/>
	     
	      	<br />
	     	*帳號
	        <label for="t_account[]"></label>
		    <input name="t_account[]" type="text" id="t_account[]" size="14" maxlength="14" class="ta_<?php echo $j;?>" onkeyup="bank_check('n')"/>
		</td>
		<td width="285">
			<div id="s_change" style="display:block;">調帳選擇
				<select name="change_s[]" id="change_s[]">
				    <option value="">請選擇入帳記錄</option>
				    <?php
						$dates = date("Ymd",mktime(0,0,0,date("m"),(date("d")-10),date("Y"))) ;	//顯示十日內的紀錄
						$dates = (substr($dates,0,4)-1911).substr($dates,4,2).substr($dates,6,2) ;
						$sql= "select * from tExpense where eTradeDate>='$_dates' and eStatusIncome=1 and eTradeStatus=0 and ePayTitle not like '%網路整批%' order by eTradeDate,eTradeNum";
						$rsx = $conn->Execute($sql);
						while( !$rsx->EOF ) {
						?>
				          <option value="<?php echo $rsx->fields["id"];?>"><?php echo substr($rsx->fields["eDepAccount"],2) . " / " . (int)substr($rsx->fields["eLender"],0,-2)."元";?></option>
				          <?php
							$rsx->MoveNext();
				  		} 
					?>  
			    </select>
			    <br />
		    </div>
		    *金額NT$
		    <label for="t_money[]"></label>
		    <input name="t_money[]" type="text" id="nt_money<?php echo $j;?>" style="text-align:right;" onKeyUp="recal('n',<?php echo $j;?>)"  size="10" />元
		    <input name="t_cost[]" type="hidden" id="t_cost[]" value="0" />
		    <br />
		    <div id="s_serviceN_<?php echo $j;?>" style="display:none;">
	          	<!-- 買方服務費:
	        	<label for="t_buyer[]"></label>
	        	<input name="t_buyer[]" type="text" id="t_buyer[]" style="text-align:right;" size="10" onblur="check_money()"/>元<br />   
	          	賣方服務費:
	         	 <label for="t_seller[]"></label>
	          	<input name="t_seller[]" type="text" id="t_seller[]" style="text-align:right;" size="10" onblur="check_money()"/>元
	           -->
	           	對象:<select name="serviceTarget[]" id="nserviceTarget<?php echo $j;?>" onchange="serviceMoney('n',<?php echo $j;?>)">
	          		<option value="owner" selected>賣方服務費</option>
	          		<option value="buyer">買方服務費</option>
	         	 </select><br />
	         	買方服務費:
		        <input  type="text" style="text-align:right;" class="nt_buyer<?php echo $j;?>" size="10" disabled/><br />
		        <input type="hidden" name="t_buyer[]" class="nt_buyer<?php echo $j;?>"/>
		       	賣方服務費:
		        <input  type="text" style="text-align:right;" class="nt_seller<?php echo $j;?>" size="10" disabled/><br />
		        <input type="hidden" name="t_seller[]" class="nt_seller<?php echo $j;?>"/>

	           	<!-- 買方服務費:<input name="t_buyer[]" type="text" id="nt_buyer<?php echo $j;?>" style="text-align:right;" size="10" disabled/>元<br />   
	          	賣方服務費:<input name="t_seller[]" type="text" id="nt_seller<?php echo $j;?>" style="text-align:right;" size="10" disabled/>元<br />
	           
	             -->
	        </div>
      	</td>
     
	    <td width="182" >
		    證號
		    <label for="pid[]"></label>
		    <input name="pid[]" type="text" id="pid[]" size="10" />
		      
		    <br />
		    EMail 
		    <label for="email[]"></label>
		    <input name="email[]" type="text" id="email[]" size="13" />
		    <br />
		    FAX 
		    <label for="fax[]"></label>
		    <input name="fax[]" type="text" id="fax[]" size="15"/>
	    </td> 
        <td width="167">*附言(勿按ENTER換行)<br />
	        <label for="t_cost[]"></label>
	        <label for="t_txt[]"></label>
	        <textarea name="t_txt[]" id="t_txt<?=($j+$index)?>" cols="20" rows="5" onblur="rel_words('t_txt<?=($j+$index)?>')"></textarea>
	       
        </td>
        <td width="130" align="center">
        	<!-- 不發送簡訊<br>
        	<input type="checkbox" name="tSend[]" id="" value="1" /> -->
        	<?php if ($smsSend == 1): ?>
        		<input type="hidden" name="tSend[]" value="1" />
        	<?php else: ?>
        		<input type="hidden" name="tSend[]" value="0" />
        	<?php endif ?>
        	
        </td>
        <td width="130" >
        	<div id="Note<?=($j+$index)?>">存摺備註欄<br>(限聯行轉帳且字數為六個字)</div>
        	
        	<input type="text" name="bankshowtxt[]" id="bankshowtxt<?=($j+$index)?>" maxlength="6" value="<?=$owner.$buyer?>"/>
        </td>
        <td>
        	<!-- 官網代書用備註
        	<textarea name="scrivenerNote[]" id="" cols="20" rows="5"></textarea> -->
        </td>
    </tr>
    
  </table>
</div>  
<?php } ?>
</div>
<p>
  <input type="button" value="送出" onclick="go()" id="sub" />
</p>
</form>
<p>
注意: <br />
1 角色選擇,交易類別,解匯行,分行別,戶名,帳號,金額,附言皆為必填欄位. <br />
2 出額金額,跨行上限為 500萬,一銀則不限.<br />
3 帳號,金額一律以半形數字輸入.<br />
4 附言中,一律以全形中文來輸入. <br />
5 傳真號碼一律以半形數字輸入,不需輸入其他符號. ex: 0227522793<br />
6 一銀虛擬轉虛擬 ,交易類別選聯行轉帳 , 戶名寫:第一商業銀行受託信託財產專戶－第一建經<br />
7 交易類別:臨櫃開票 戶名留一格空白,銀行帳號填入14碼0 (00000000000000).<br />
8 台銀開票帳戶：台銀/忠孝 &nbsp;&nbsp;帳號：053001144289&nbsp;&nbsp;戶名：第一建築經理股份有限公司
</p>
<div id="realtyC"></div>
</body>
</html>
