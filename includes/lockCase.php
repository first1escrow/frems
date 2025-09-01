<?php
// include_once 'setSales_run_end.php' ;
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$sales_year = $_POST['sales_year'] ;				//查詢回饋年度
$sales_season = $_POST['sales_season'] ;			//查詢回饋季




//取得合約銀行帳號
$_sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" GROUP BY cBankAccount ORDER BY cId ASC;' ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$conBank[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}

$conBank_sql = implode('","',$conBank) ;


if ($sales_year && $sales_season) {	
	switch ($sales_season) {
		case 'S1' : 
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-01-01" AND tra.tBankLoansDate<="'.$sales_year.'-03-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-01-01" AND cBankList<="'.$sales_year.'-03-31"' ;
				$sales_season1 = '第1季' ;
				break ;
		case 'S2' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-04-01" AND tra.tBankLoansDate<="'.$sales_year.'-06-30"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-04-01" AND cBankList<="'.$sales_year.'-06-30"' ;
				$sales_season1 = '第2季' ;
				break ;
		case 'S3' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-07-01" AND tra.tBankLoansDate<="'.$sales_year.'-09-30"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-07-01" AND cBankList<="'.$sales_year.'-09-30"' ;
				$sales_season1 = '第3季' ;
				break ;
		case 'S4' :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-10-01" AND tra.tBankLoansDate<="'.$sales_year.'-12-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-10-01" AND cBankList<="'.$sales_year.'-12-31"' ;
				$sales_season1 = '第4季' ;
				break ;
		default :
				$date_range = ' tra.tBankLoansDate>="'.$sales_year.'-'.$sales_season.'-01" AND tra.tBankLoansDate<="'.$sales_year.'-'.$sales_season.'-31"' ;
				$contractDate = ' cBankList>="'.$sales_year.'-'.$sales_season.'-01" AND cBankList<="'.$sales_year.'-'.$sales_season.'-31"' ;
				$sales_season1 = preg_replace("/^0/","",$sales_season).'月份' ;
				break ;
	}
	$_cond .= ' AND '.$date_range ;
}
// ###

$_sql = '
	SELECT 
		tra.tMemo as cCertifiedId
	FROM
		tBankTrans AS tra
	JOIN
		tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=tra.tMemo
	WHERE
		
		tra.tAccount IN ("'.$conBank_sql.'")
		AND tra.tKind = "保證費"
		'.$_cond.' 
	GROUP BY
		tra.tMemo
	ORDER BY
		tra.tExport_time
	ASC ;
' ;

// echo $_sql ; exit ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$cid_arr[] = $rs->fields['cCertifiedId'] ;

	$rs->MoveNext();
}

// ##
// // print_r($cid_arr);
//取出範圍內未收履保費但仍要回饋(有利息)的案件
if ($contractDate) $_sql = 'SELECT cCertifiedId FROM tContractCase WHERE '.$contractDate.$_cond2;
else $_sql = 'SELECT cCertifiedId FROM tContractCase WHERE cBankList<>"" '.$_cond2.' ORDER cEndDate ASC ;' ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$cid_arr[] = $rs->fields['cCertifiedId'] ;

	$rs->MoveNext();
}


// echo "<pre>";
// print_r($cid_arr);
$cid_max = count($cid_arr);
$cid_count = 0;
for ($i = 0 ; $i < $cid_max ; $i ++) {
	
	// $sql = "SELECT * FROM tContractCase WHERE cCertifiedId = '".$cid_arr."'";
	$sql = "UPDATE tContractCase SET cFeedBackClose = 1 WHERE cCertifiedId = '".$cid_arr[$i]."'";

	// echo $sql."\r\n";
	$conn->Execute($sql);

	if ($conn->Execute($sql)) {
		$cid_count++;
	}else{
		$error[] = $cid_arr[$i];
	}
	
}

$fw = fopen('../log2/lockCase'.date('Ymd').'.log', 'a+');
fwrite($fw, json_encode($cid_arr));
fclose($fw);

if ($cid_max == $cid_count) {
	echo '成功';
}else{
	echo @implode(',', $error)."更改失敗";
}

?>