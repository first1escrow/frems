<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
header("Content-Type:text/html; charset=utf-8");
$today = date("Y-m-d") ;
//合約銀行基本資料
$_all_balance = 0 ;
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cBankMain,cId DESC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$conBank[] = $rs->fields ;
	$_all_balance += $rs->fields['cBankBalance'] + 1 - 1 ;				//所有銀行調帳金額加總
	$rs->MoveNext() ;
}
unset($rs) ;

//所有保證號碼餘額金額統計
$s = 'SELECT  SUM(cCaseMoney) AS total_money FROM tContractOwner AS A ,tContractBuyer AS B , tContractCase AS C WHERE A.cCertifiedId = B.cCertifiedId AND A.cCertifiedId=C.cCertifiedId AND cCaseMoney > 0 ;' ;
$s_total = $conn->Execute($s) ;
$_now_total = $s_total->fields["total_money"] ;							//所有建經案件總金額(未加利息)
##


//總利息-總所得稅-利息出款之總金額
$sqlx = 'SELECT * FROM tExpense WHERE eTradeCode IN ("1912","1920","1560","1785");' ;
$rsx = $conn->Execute($sqlx) ;
while (!$rsx->EOF) {
	$_eLender = (int)substr($rsx->fields["eLender"],0,-2) ;
	$_eDebit = (int)substr($rsx->fields["eDebit"],0,-2) ;
	$_t_money = $_t_money + $_eLender - $_eDebit ;

	
	$bankInt[$rsx->fields['eAccount']] += ($_eLender - $_eDebit); //cBankTrustAccount
	
	$rsx->MoveNext() ;
}  


$_now_total = $_now_total + $_t_money + $_all_balance + 1 - 1 ; 		//所有建經案件總金額(加入利息、加入所有銀行調帳金額)
##.
//所有媒體匯出檔總案件
$sql = '
	SELECT 
		tPayOk,
		tExport_nu,
		SUM(tMoney) as M,
		tExport_time,
		tBank_kind,
		tVR_Code  
	FROM
		tBankTrans 
	WHERE 
		tExport="1" 
		AND tBankLoansDate<="'.$today.'"
	GROUP BY 
		tExport_nu 
	ORDER BY 
		tExport_time 
	DESC;
' ;
$rs = $conn->Execute($sql) ;
$_total = $rs->RecordCount() ;
while (!$rs->EOF) { //cBankVR
	# code...
	if ($rs->fields['tBank_kind']== '一銀') {
		$BankCount[substr($rs->fields['tVR_Code'], 0,5)]++;
	}elseif ($rs->fields['tBank_kind'] == '台新') {
		$BankCount[substr($rs->fields['tVR_Code'], 0,5)]++;
	}elseif ($rs->fields['tBank_kind'] == '永豐') {
		$BankCount[substr($rs->fields['tVR_Code'], 0,6)]++;
	}
	


	$rs->MoveNext();
}
// print_r($BankCount);
##

//各合約銀行銀行端與建經系統端餘額資料
$bank_total = 0 ;
for ($i = 0 ; $i < count($conBank) ; $i ++) {
	//計算總餘額
	$bank_sql = '
		SELECT 
			tExpense.eTotal,
			tExpense.eTradeDate 
		FROM 
			tExpense 
		WHERE 
			eAccount="'.$conBank[$i]['cBankTrustAccount'].'" 
		ORDER BY 
			id 
		DESC 
		LIMIT 1;
	' ;
	echo $bank_sql."<br>";
	$rs_bank = $conn->Execute($bank_sql) ;
	$conBank[$i]['eTotal'] = substr($rs_bank->fields['eTotal'],0,-2) + 1 - 1 ;		//銀行端帳戶餘額
	$bank_total += substr($rs_bank->fields['eTotal'],0,-2) + 1 - 1 ;				//所有銀行餘額加總
	$_T = $_now_total - $bank_total ;												//計算總差額
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
	echo $sql."<br>";
	
	$rs = $conn->Execute($sql) ;
	$conBank[$i]['now_total'] = $rs->fields['total_money'] ;		//計算後台總金額
	
	
	$f_t_money = $bankInt[$conBank[$i]['cBankTrustAccount']];
	$conBank[$i]['now_total'] = $conBank[$i]['now_total'] + $f_t_money + $conBank[$i]['cBankBalance'] ; 	//案件金額+利息收入(建經總金額)
	$conBank[$i]['diff'] = $conBank[$i]['now_total'] - $conBank[$i]['eTotal'] ;		//差異金額
	##
	
	
	$conBank[$i]['total_record'] = $BankCount[$conBank[$i]['cBankVR']];
	##
	//西門加總
	if ($conBank[$i]['cBankCode'] == 77 || $conBank[$i]['cBankCode'] == 80) {
		$sinopac['total_record'] += $conBank[$i]['total_record'];
		$sinopac['now_total'] += $conBank[$i]['now_total'];		
		$sinopac['eTotal'] += $conBank[$i]['eTotal'];
		$sinopac['diff'] += $conBank[$i]['diff'];
	}
}
#

echo '<div style="padding-left:60px;">媒體檔匯出 共 '.number_format($_total).' 筆,( 目前系統總餘額：' ;
if ($_now_total != $bank_total ) { 
	echo '<font color=red>'.number_format($_now_total).'</font>';
}
else { 
	echo number_format($_now_total) ;
}
echo ' / 銀行總餘額：'.number_format($bank_total).' / 相差： ' ;
if ($_T <0 ) {
	echo '<a class="iframe3" href="/bank/report/check_income.php">'.number_format($_T).'</a>' ;
} 
else {
	echo number_format($_T) ;
}

echo ')</div>
<div style="height:6px;">&nbsp;</div>
' ;


for ($i = 0 ; $i < count($conBank) ; $i ++) {
	$str = $conBank[$i]['cBankName'] ;
	if ($conBank[$i]['cBankMain'] == '807') {
		$str .= $conBank[$i]['cBranchName'] ;
	}
	
	echo '<div style="padding-left:60px;">媒體檔('.$str.')匯出 共 '.number_format($conBank[$i]['total_record']).' 筆,( 目前系統總餘額：' ;
	if ($conBank[$i]['eTotal'] != $conBank[$i]['now_total']) {
		echo '<font color=red>'.number_format($conBank[$i]['now_total']).'</font>' ;
	} 
	else { 
		echo number_format($conBank[$i]['now_total']) ;
	}
	echo ' / 銀行總餘額：'.number_format($conBank[$i]['eTotal']).' / 相差： ' ;
	
	if ($conBank[$i]['diff'] < 0) { 
		echo '<a class="iframe3" href="/bank/report/check_income_'.$conBank[$i]['cBankAlias'].'.php' ;
		
		if ($conBank[$i]['cBankMain'] == '807') {
			echo '?v='.$conBank[$i]['cBankVR'] ;
		}
		
		echo '">'.number_format($conBank[$i]['diff']).'</a>' ;
	}
	else {
		echo number_format($conBank[$i]['diff']) ;
	}
	
	echo ')</div>
	' ;
}
echo '<div style="padding-left:60px;">媒體檔(永豐[全])匯出 共 '.number_format($sinopac['total_record']).' 筆,( 目前系統總餘額：' ;
if ($_now_total != $bank_total ) { 
	echo '<font color=red>'.number_format($sinopac['now_total']).'</font>';
}
else { 
	echo number_format($sinopac['now_total']) ;
}
echo ' / 銀行總餘額：'.number_format($sinopac['eTotal']).' / 相差： ' ;

	echo number_format($sinopac['diff']) ;


echo ')</div>
<div style="height:6px;">&nbsp;</div>
' ;





?>
<script>
	$(document).ready(function(){
			$(".iframe3").colorbox({
					iframe:true, width:"450", height:"500"					

				});
	});
</script>