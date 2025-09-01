<?php 
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../configs/config.class.php';
include('../../openadodb.php') ;
include_once '../../session_check.php' ;
require_once('../../tcpdf/tcpdf.php');
##
//關鍵字樣板
$keywords = array (
	'扣繳稅款'=>'稅',
	'仲介服務費'=>'服務費',
	'賣方先動撥'=>'動撥',
	'調帳'=>'轉入',
	'代清償'=>'代償',
	'點交(結案)'=>array('地政士'=>'代書費','仲介'=>'服務費','賣方'=>'','買方'=>'','保證費'=>'')
) ;

##
$_POST = escapeStr($_POST) ;
// print_r($_POST);
$Category = $_POST['cat'];
$count = 0;
foreach ($_POST['CertifiedId'] as $key => $_account_id) {
	// $_account_id ='080536087';
	$checkB = 0;$checkS=0;
	##檢查是否有代墊##
	$sql = "SELECT
				tMoney,
				tKind,
				tBankCode,
				tCode,
				tCode2,
				tObjKind,
				tTxt,
				tDate,
				tAccountName,
				tAccount,
				tEmail,
				tFax,
				tObjKind2,
				tObjKind2Item,
				(SELECT bBank4_name FROM tBank WHERE bBank3=SUBSTR(tBankCode,1,3) AND bBank4='' LIMIT 1) AS Bank,
				(SELECT bBank4_name FROM tBank WHERE bBank3=SUBSTR(tBankCode,1,3) AND bBank4=SUBSTR(tBankCode,4) LIMIT 1) AS BankBranch,
				tSend
			FROM tBankTrans WHERE tMemo='".$_account_id."' AND tPayOk ='2' AND (tObjKind2 = '01' OR tObjKind2 = '02')";
			
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$TranSp[] = $rs->fields;

		$rs->MoveNext();
	}
	

	##
	
	$sql = "
		SELECT
			cc.cCertifiedId,
			cc.cCaseMoney,
			cc.cEscrowBankAccount,
			(SELECT cTrustAccountName FROM tContractBank WHERE cBankCode = cc.cBank) cTrustAccountName,
			(SELECT cBankTrustAccount FROM tContractBank WHERE cBankCode = cc.cBank) cBankTrustAccount,
			(SELECT cBankAccount2 FROM tContractBank WHERE cBankCode = cc.cBank) cBankAccount2,
			(SELECT cAccountName2 FROM tContractBank WHERE cBankCode = cc.cBank) cAccountName2,
			
			(SELECT cBankMain FROM tContractBank WHERE cBankCode = cc.cBank) cBankMain,
			(SELECT cBankFullName FROM tContractBank WHERE cBankCode = cc.cBank) cBankFullName,
			(SELECT cBranchFullName FROM tContractBank WHERE cBankCode = cc.cBank) cBranchFullName,
			co.cName as owner,
			co.cIdentifyId as o_ID,
			cb.cName as buyer,
			cb.cIdentifyId as b_ID,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cr.cBranchNum3,
			cr.cServiceTarget,
			cr.cServiceTarget1,
			cr.cServiceTarget2,
			cr.cServiceTarget3,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS brand,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum) AS comp,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS store,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS brand1,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum1) AS comp1,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS store1,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS brand2,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum2) AS comp2,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS store2,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand3) AS brand3,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum3) AS comp3,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum3) AS store3,
			(SELECT sOffice FROM tScrivener WHERE sId = cs.cScrivener) AS scrivenerOffice,
			(SELECT sName FROM tScrivener WHERE sId = cs.cScrivener) AS scrivenerName,
			ci.cTotalMoney,
			ci.cAddedTaxMoney
		FROM
			tContractCase AS cc
		LEFT JOIN 
			tContractOwner AS co ON co.cCertifiedId = cc.cCertifiedId
		LEFT JOIN
			tContractBuyer AS cb ON cb.cCertifiedId = cc.cCertifiedId
		LEFT JOIN
			tContractIncome AS ci ON ci.cCertifiedId = cc.cCertifiedId
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
		WHERE cc.cCertifiedId = '".$_account_id."'";
		// echo $sql."<br>";

	$rs = $conn->Execute($sql);
	$list[$count]= $rs->fields;

	// echo "<pre>";
	// print_r($list[$count]);
	// die;
	##銀行名稱##
	$list[$count]['cBank'] = $list[$count]['cBankFullName'] ;
	if ($list[$count]['cBankMain']=='807') {
		$list[$count]['cBank'] .= $list[$count]['cBranchFullName'] ;
	}
		
	##仲介服務對象##
	if ($list[$count]['cServiceTarget'] == 1) { //1.買賣方、2.賣方、3.買方
		$list[$count]['cServiceTarget'] = '買賣方';
	}elseif ($list[$count]['cServiceTarget'] == 2) {
		$list[$count]['cServiceTarget'] = '賣方';
	}elseif ($list[$count]['cServiceTarget'] == 3) {
		$list[$count]['cServiceTarget'] = '買方';
	}

	if ($list[$count]['cServiceTarget1'] == 1) { //1.買賣方、2.賣方、3.買方
		$list[$count]['cServiceTarget1'] = '買賣方';
	}elseif ($list[$count]['cServiceTarget1'] == 2) {
		$list[$count]['cServiceTarget1'] = '賣方';
	}elseif ($list[$count]['cServiceTarget1'] == 3) {
		$list[$count]['cServiceTarget1'] = '買方';
	}

	if ($list[$count]['cServiceTarget2'] == 1) { //1.買賣方、2.賣方、3.買方
		$list[$count]['cServiceTarget2'] = '買賣方';
	}elseif ($list[$count]['cServiceTarget2'] == 2) {
		$list[$count]['cServiceTarget2'] = '賣方';
	}elseif ($list[$count]['cServiceTarget2'] == 3) {
		$list[$count]['cServiceTarget2'] = '買方';
	}

	if ($list[$count]['cServiceTarget3'] == 1) { //1.買賣方、2.賣方、3.買方
		$list[$count]['cServiceTarget3'] = '買賣方';
	}elseif ($list[$count]['cServiceTarget3'] == 2) {
		$list[$count]['cServiceTarget3'] = '賣方';
	}elseif ($list[$count]['cServiceTarget3'] == 3) {
		$list[$count]['cServiceTarget3'] = '買方';
	}

	##建物地址##
	$sql = 'SELECT B.zCity,B.zArea,A.cAddr FROM tContractProperty AS A, tZipArea AS B WHERE A.cCertifiedId="'.$_account_id.'" AND A.cZip=B.zZip ;' ;
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$list[$count]['ContractProperty'][] = $rs->fields;
		$rs->MoveNext();
	}
	##
	$sql = "SELECT cIdentifyId FROM tContractOthers WHERE cIdentity='1' AND cCertifiedId='".$_account_id."'";
	$rs = $conn->Execute($sql);
	if ($rs->RecordCount() > 0) {
		$list[$count]['buyerO'] = '等'.($rs->RecordCount() + 1).'人' ;
	}
	
	$sql = "SELECT cIdentifyId FROM tContractOthers WHERE cIdentity='2' AND cCertifiedId='".$_account_id."'";
	$rs = $conn->Execute($sql);

	if ($rs->RecordCount() > 0) {
		$list[$count]['ownerO'] = '等'.($rs->RecordCount() + 1).'人' ;
	}
	while (!$rs->EOF) {
		//若為法人身分，則將ID填入稅款旗標ID中
		if (preg_match("/^[0-9]{8}$/",$rs->fields['cIdentifyId'])) {
			$oTaxId = $rs->fields['cIdentifyId'] ;
		}
		##
					
		//若為自然人身分，則將ID填入NHI旗標ID中
		if (preg_match("/^\w{10}$/",$rs->fields['cIdentifyId'])) {
			$oNHIId = $rs->fields['cIdentifyId'] ;
		}
		##

		$rs->MoveNext();
	}

	//若無其他賣方符合法人身分，則檢查主賣方身分證字號是否符合法人身分
	if (($oTaxId == '') && (preg_match("/^[0-9]{8}$/",$list[$count]['o_ID']))) {
		$oTaxId = $list[$count]['o_ID'] ;
	}
	##
			
	//若無其他賣方符合自然人身分，則檢查主賣方身分證字號是否符合自然人身分
	if (($oNHIId == '') && (preg_match("/^\w{10}$/",$list[$count]['o_ID']))) {
		$oNHIId = $list[$count]['o_ID'] ;
	}
	//
	##
	// $sql = "select sum(tMoney) as M,tMoney from tBankTrans where tMemo='".$_account_id."' and tPayOk='2'";
	$sql = 'SELECT 
				tMoney,
				tKind,
				tBankCode,
				tCode,
				tCode2,
				tObjKind,
				tTxt,
				tDate,
				tAccountName,
				tAccount,
				tEmail,
				tFax,
				tObjKind2,
				(SELECT bBank4_name FROM tBank WHERE bBank3=SUBSTR(tBankCode,1,3) AND bBank4="" LIMIT 1) AS Bank,
				(SELECT bBank4_name FROM tBank WHERE bBank3=SUBSTR(tBankCode,1,3) AND bBank4=SUBSTR(tBankCode,4) LIMIT 1) AS BankBranch,
				tSend
				
			FROM
				tBankTrans WHERE tMemo="'.$_account_id.'" AND tPayOk="2"  AND tObjKind2 != "01" AND tObjKind2 != "02";' ;

	$rs = $conn->Execute($sql);
	$i = 0;
	$tmpC = $rs->RecordCount();
	while (!$rs->EOF) {
		
	  	$list[$count]['BankTransMoney'] += $rs->fields["tMoney"];//取款總金額
	  	$list[$count]['BankTrans'][$i] = $rs->fields;
	  	$fg = 0 ;
	  	##

		if ($rs->fields['tKind'] == '仲介') {
	  		$checkB++;//檢查仲介數量
	  	}elseif($rs->fields['tKind'] == '地政士'){
	  		$checkS++;//檢查地政士數量
	  	}
	  	##
	  	if ($checkB > 1 && $rs->fields['tKind'] == '仲介') {
			$list[$count]['BankTrans'][$i]['font'] = 'font-weight:900;';
		}elseif ($checkS > 1  && $rs->fields['tKind'] == '地政士') {
			$list[$count]['BankTrans'][$i]['font'] = 'font-weight:900;';
		}else{
			$list[$count]['BankTrans'][$i]['font'] = '';
		}
		##
	  	switch ($rs->fields["tCode"]){
			case "01":
				$list[$count]['BankTrans'][$i]['title'] = $rs->fields['tCode2'];
			break;
			case "02":
				$list[$count]['BankTrans'][$i]['title'] = "跨行代清償";
			break;
			case "03":
				$list[$count]['BankTrans'][$i]['title'] = "聯行代清償";
			break;
			case "04":
				$list[$count]['BankTrans'][$i]['title'] = "大額繳稅";
			break;
			case "05":
				$list[$count]['BankTrans'][$i]['title'] = $rs->fields['tCode2'];
			break;
			case "06":
				$list[$count]['BankTrans'][$i]['title'] = "利息";
			break;
		}

		//比對關鍵字是否正確
		$_target = '出款項目' ;
		$patt = '' ;
		$fg = 0 ;
		$flag = 0 ;
		$_tObjKind = $rs->fields['tObjKind'] ;		//出款項目(選項)
		$_tTxt = $rs->fields['tTxt'] ;				//附言
		$_tKind = $rs->fields['tKind'] ;			//角色

		if ($_tObjKind=='點交(結案)') {
			$_target = '點交角色' ;
			$cmp = $_tObjKind ;
			if ((preg_match("/賣方/",$_tKind))||(preg_match("/買方/",$_tKind))) {		//買賣方
				if ($_tTxt!='') {
					$fg ++ ;
				}
			}
			else if (preg_match("/保證費/",$_tKind)) {		//保證費
				$_tTxt = n_to_w($_tTxt,1) ;
				if (!preg_match("/\d{9}/",$_tTxt)) {
					$fg ++ ;
				}
			}
			else {		//其他(地政士、仲介服務費)
				$patt = $keywords[$_tObjKind][$_tKind] ;
				if (!preg_match("/$patt/",$_tTxt)) {
					$fg ++ ;
				}
			}
		}else {
			$patt = $keywords[$_tObjKind] ;
	
			if (!preg_match("/$patt/",$_tTxt)) {
				$fg ++ ;
			}
		}

		if ($fg > 0) {
				$msg .=  "保證號碼：".$_account_id."\r\n" ;
				$msg .=  "\'".$rs->fields['tObjKind']."\'".$_target."與附言內容有差異!!\r\n" ;
				$msg .=  "出款對象：".$_tKind."\r\n" ;
				$msg .=  "出款金額：".$rs->fields['tMoney']."\r\n" ;
				$msg .=  "出帳建檔日期：".$rs->fields['tDate']."\r\n" ;
				$msg .=  "附言：".$rs->fields['tTxt']."\r\n" ;
		}

		if ($cmp) {
			//當利息大於20000時之處置
			$sql = 'SELECT cInterest, bInterest, cTax, bTax, cNHITax, bNHITax FROM tChecklist WHERE cCertifiedId="'.$_account_id.'" ;' ;
			$_rs = $conn->Execute($sql) ;
			$interest = 0 ;
			$tax = 0 ;
			$NHItax = 0 ;
			$str = '' ;
			
			$interest = (int)$_rs->fields['cInterest'] + (int)$_rs->fields['bInterest'] + 1 - 1 ;
			$tax = (int)$_rs->fields['cTax'] + (int)$_rs->fields['bTax'] + 1 - 1 ;
			$NHItax = (int)$_rs->fields['cNHITax'] + (int)$_rs->fields['bNHITax'] + 1 - 1 ;
			
			//利息>=20,000時，出現提醒
			if ($interest >= 20000) {
				//賣方具有自然人身分時，出現提醒
				if ($oNHIId) {
					$str = '(賣方具有自然人身分!!)' ;
				}
				##
				
				// $msg .= '注意!!\n利息 >= 20,000 元, 請確認 \"二代健保代扣\" 對象是否正確!!\n'.$str.'\n\n' ;
				$msg .=  "注意!!\r\n利息 >= 20,000 元, 請確認\'二代健保代扣\'對象是否正確!!\r\n".$str."\r\n" ;
				$str = '' ;
			}
			##
			
			//當賣方具有法人身分時，出現提醒
			if (($tax > 0) && ($oTaxId)) {
				$msg .=  "注意!!\r\n請確認\'代扣利息所得\'對象是否正確!!\r\n".$str."\r\n" ;

				$msg .= '注意!!\n請確認 \"代扣利息所得\" 之扣款對象及金額是否正確!!\n(賣方具有法人身分!!)\n\n' ;
			}
			##
	  }

		$i++;
		$rs->MoveNext();
	}

	
	##

	// 20181005
	// 如果當該案件沒有配帳過"買方服務費"
	// 但是卻出款買方服務費
	// 改筆買服出款的金額欄位後面


	$tranBuyer = 0;
	$list[$count]['checkBuyerMoney'] = true;
	$sql = 'SELECT * FROM tBankTrans WHERE tMemo="'.$_account_id.'" AND tBuyer > 0 AND tPayOk="2" ;' ;
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);
	$tranBuyer = $rs->RecordCount();

	if ($tranBuyer > 0) {
		$sql = 'SELECT  `eBuyerMoney` FROM  `tExpense` WHERE eDepAccount = "00'.$list[$count]['cEscrowBankAccount'].'" AND eBuyerMoney > 0';
		// echo $sql."<br>";
		$rs = $conn->Execute($sql);

		$list[$count]['checkBuyerMoney'] = ($rs->fields['eBuyerMoney'] > 0)?true:false;
		// echo $checkBuyerMoney;
	}
	$list[$count]['cCaseMoney'] += getTranSpMoney($_account_id);
	// echo "<pre>";
	// print_r($list);
	// die;
	##
	//買方另計稅額 買方另計稅額=入帳明細買方費用加總
		$list[$count]['buyerExtraMoney'] = 0;
		$sql = 'SELECT eBuyerMoney,eExtraMoney,id FROM tExpense WHERE eDepAccount = "00'.$list[$count]['cEscrowBankAccount'].'"';
		
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$list[$count]['buyerExtraMoney'] += ($rs->fields['eBuyerMoney']+$rs->fields['eExtraMoney']); //買方服務費 買方溢入款
			$list[$count]['buyerExtraMoney'] += getExpenseDetailSmsOther($rs->fields['id']);
			$rs->MoveNext();
		}
	//可支付餘額 可支付餘額=買方另計稅額 - 案件配帳金額買方出款項目加總
	$list[$count]['buyerExtraPay'] = 0;
	$sql = 'SELECT SUM(eMoney) AS totalMoney FROM tExpenseDetail WHERE eCertifiedId="'.$_account_id.'" AND eTarget = 3 AND (eOK !="" OR eItem = 9)' ;
	$rs = $conn->Execute($sql) ;
	$list[$count]['buyerExtraPay'] = $list[$count]['buyerExtraMoney']-$rs->fields['totalMoney'];
	// $list[$count]['buyerExtraMoneyIn'];	
	//可支付餘額服務費
	$sql = 'SELECT tMoney,tTxt FROM `tBankTrans` WHERE `tTxt` LIKE "%買方%" AND tStoreId >0 AND tMemo = "'.$_account_id.'"';
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		if (preg_match("/服務費/", $rs->fields['tTxt'])) {
			$list[$count]['buyerExtraPay'] -= $rs->fields['tMoney'];
		}
		 

		$rs->MoveNext();
	}
	##

 	// 	if ($msg) {
	// 	echo '<script>alert("'.$msg.'") ;</script>'."\n" ;
	$count++;
	
	$TranSpMoney = 0;

	//代墊要分開
	for ($i=0; $i < count($TranSp); $i++) { 
		
		
		if ($tmpC == 0 && $i == 0) {
			$count = 0;
			$list[$count] = $list[0];
		}else{

			$list[$count] = $list[($count-1)];
		}
		$list[$count]['cCaseMoney'] = $list[$count]['cCaseMoney'] - $list[$count]['BankTransMoney'];
		unset($list[$count]['BankTransMoney']);
		unset($list[$count]['BankTrans']);

		if ($TranSp[$i]['tObjKind2'] == '01') { //申請代墊 付款對象為代墊用帳戶

			$list[$count]['cBankTrustAccount'] = $list[$count]['cBankAccount2'];
			$list[$count]['cTrustAccountName'] = $list[$count]['cAccountName2'];
		}else{

			$list[$count]['cBankTrustAccount'] = $list[$count]['cBankTrustAccount'];
			$list[$count]['cTrustAccountName'] = $list[$count]['cTrustAccountName'];
		}


		$list[$count]['BankTransMoney'] += $TranSp[$i]["tMoney"];//取款總金額
	  	$list[$count]['BankTrans'][$i] = $TranSp[$i];
	  	$fg = 0 ;$checkB = 0;$checkS=0;
	  	##

		if ($TranSp[$i]['tKind'] == '仲介') {
	  		$checkB++;//檢查仲介數量
	  	}elseif($TranSp[$i]['tKind'] == '地政士'){
	  		$checkS++;//檢查地政士數量
	  	}
	  	##
	  	if ($checkB > 1 && $TranSp[$i]['tKind'] == '仲介') {
			$list[$count]['BankTrans'][$i]['font'] = 'font-weight:900;';
		}elseif ($checkS > 1  && $TranSp[$i]['tKind'] == '地政士') {
			$list[$count]['BankTrans'][$i]['font'] = 'font-weight:900;';
		}else{
			$list[$count]['BankTrans'][$i]['font'] = '';
		}
		##
	  	switch ($TranSp[$i]["tCode"]){
			case "01":
				$list[$count]['BankTrans'][$i]['title'] = $TranSp[$i]['tCode2'];
			break;
			case "02":
				$list[$count]['BankTrans'][$i]['title'] = "跨行代清償";
			break;
			case "03":
				$list[$count]['BankTrans'][$i]['title'] = "聯行代清償";
			break;
			case "04":
				$list[$count]['BankTrans'][$i]['title'] = "大額繳稅";
			break;
			case "05":
				$list[$count]['BankTrans'][$i]['title'] = $TranSp[$i]['tCode2'];
			break;
			case "06":
				$list[$count]['BankTrans'][$i]['title'] = "利息";
			break;
		}
		##
		
		// unset(var)
		// $TranSp
	}
	unset($TranSp);
	// print_r($pdf);
	// unset($list[$count],$oTaxId,$oNHIId,$fg,$_target,$patt,$flag,$_tObjKind,$_tTxt,$_tKind);
}

// if ($msg) {
// 	echo '<script>alert("'.$msg.'") ;</script>'."\n" ;
// }


if ($Category=='msg') {
	echo $msg;
}else{
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'cm', 'A4', true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);	
	$pdf->SetMargins('0.8', '0.8', '0.8');
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	for ($i=0; $i < count($list); $i++) { 
		$data = $list[$i];
		require 'export_list_all_pdf.php';
		unset($data);
	}

	$pdf->Output(date('YmdHi').'.pdf','I') ;
}



		// echo '<script>alert("'.$msg.'") ;</script>'."\n" ;
	
//半形 <=> 全形
Function n_to_w($strs, $types = '0') {
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
function NumtoStr($num){
	$numc	="零,壹,貳,參,肆,伍,陸,柒,捌,玖";
	$unic	=",拾,佰,仟";
	$unic1	=" 元整,萬,億,兆,京";
	
	//$numc_arr	=split("," , $numc);
	$numc_arr	= explode("," , $numc);
	//$unic_arr	=split("," , $unic);
	$unic_arr	= explode("," , $unic);
	//$unic1_arr	=split("," , $unic1);
	$unic1_arr	= explode("," , $unic1);
	
	$i = str_replace(',','',$num);#取代逗號
	$c0 = 0;
	$str=array();
	do{
		$aa = 0;
		$c1 = 0;
		$s = "";
		#取最右邊四位數跑迴圈,不足四位就全取
		$lan=(strlen($i)>=4)?4:strlen($i);
		$j = substr($i, -$lan);
		while($j>0){
			$k = $j % 10;#取餘數
			if($k > 0){
				$aa = 1;
				$s = $numc_arr[$k] . $unic_arr[$c1] . $s ;
			}elseif ($k == 0){
				if($aa == 1)	$s = "0" . $s;
			}
			$j = intval($j / 10);#只取整數(商)
			$c1 += 1;
		}
		#轉成中文後丟入陣列,全部為零不加單位
		$str[$c0]=($s=='')?'':$s.$unic1_arr[$c0];
		#計算剩餘字串長度
		$count_len=strlen($i) - 4;
		$i=($count_len > 0 )?substr($i, 0, $count_len):'';

		$c0 += 1;
	}while($i!='');
	
	#組合陣列
	foreach($str as $v)	$string .= array_pop($str);

	#取代重複0->零
	$string=preg_replace('/0+/','零',$string);

	return $string;
}

function getTranSpMoney($id){
		global $conn;

		$sql = "SELECT SUM(tMoney) AS money FROM tBankTrans WHERE tObjKind2 = '01' AND tObjKind2Item != '' AND tMemo= '".$id."' AND tPayOk = 1";
		$rs = $conn->Execute($sql);

		return $rs->fields['money'];
}
function getExpenseDetailSmsOther($id){
	global $conn;
		
	$money = 0;
	$sql = "SELECT SUM(eMoney) AS totalMoney FROM tExpenseDetailSmsOther WHERE eDel = 0 AND eExpenseId = '".$id."' AND eTitle IN('買方履保費','買方預收款項','契稅','印花稅')";
	$rs = $conn->Execute($sql);
	$money = $rs->fields['totalMoney'];
	
	return $money;
}
?>