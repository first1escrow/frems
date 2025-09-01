<?php
exit();
header("Access-Control-Allow-Origin: *") ;
require_once dirname(dirname(__FILE__)).'/openadodb.php' ;

$pid = $_REQUEST['pid'] ;
//pExpenseIncome
$notice = array() ;
if (preg_match("/^\d+$/",$pid)) {
		//20220414 玟君跟佩琪共用代書
	


	if ($pid == '6'){
		$staff = '' ;
	}else {

		if ($pid == '12') {
			$pid = 1;
		}
		$staff = 'AND c.sUndertaker1 = "'.$pid.'"';
	} 
	
	//跑馬燈顯示
	$sql = '
		SELECT
			*
		FROM
			tExpense AS a
		JOIN
			tBankCode AS b ON SUBSTR(a.eDepAccount,3) = b.bAccount
		JOIN
			tScrivener AS c ON b.bSID = c.sId
		WHERE
			a.eStatusIncome="1"
			AND a.eTradeStatus = "0"
			AND a.eDebit = "000000000000000"
			'.$staff.'
		ORDER BY
			a.id
		ASC
	;' ;
	
	
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$notice[] = substr($rs->fields['eDepAccount'],7).'('.number_format((int)substr($rs->fields['eLender'],0,-2)).')_'.$rs->fields['id'] ;
		$rs->MoveNext() ;
	}
	##
	$conn->close();
	// echo $sql ;
	if (count($notice) > 0) echo json_encode($notice) ;
}

?>