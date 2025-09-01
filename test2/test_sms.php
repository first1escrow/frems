<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
// include_once 'sms/sms_function_manually.php';
// include_once 'sms_function.php';
include_once 'sms_function.php';
include_once '../openadodb.php' ;
$sms = new SMS_Gateway();
@session_start();
header("Content-Type:text/html; charset=utf-8"); 

//194
$sn = '62674e6758b07';
$sql=  'SELECT 
		a.tId,
		a.tVR_Code,
		a.tMemo,
		a.tObjKind,
		a.tMoney,
		a.tExport_nu,
		a.tExport_time,
		c.cScrivener,
		b.cBranchNum 
	FROM 
		tBankTrans AS a 
	INNER JOIN 
		tContractRealestate AS b ON a.tMemo = b.cCertifyId 
	INNER JOIN 
		tContractScrivener AS c ON a.tMemo = c.cCertifiedId 
	WHERE
		a.tExport_nu = "'.$sn.'" AND a.tObjKind NOT IN("仲介服務費")
		AND a.tSend != 1
	GROUP BY a.tVR_Code,a.tObjKind';

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
// 	//確認本媒體檔案是否為點交(結案)且同時出款仲介服務費
	$realty = 0 ;
	$mediaCode = trim($rs->fields['tExport_nu']) ;
	$mediaTime = trim($rs->fields['tExport_time']);
	$tVR = trim($rs->fields['tVR_Code']) ;
	
	$sql = 'SELECT tMoney FROM tBankTrans WHERE tObjKind IN ("點交(結案)","預售屋") AND tKind="仲介" AND tVR_Code="'.$tVR.'" AND tExport_nu="'.$mediaCode.'";' ;
	$_rs = $conn->Execute($sql) ;
	while (!$_rs->EOF) {
		$realty += $_rs->fields['tMoney'] + 1 - 1 ;
		$_rs->MoveNext() ;
	}
	unset($_rs);

	echo $tVR."_".$realty."<br>";

	$rs->MoveNext();
}
// $tmp = $sms->send('96988100025321' , '59', '4714', '扣繳稅款', '749126', 'n', 0);
// $tmp = $sms->sendIncome('96988110000868',35,229,'income',757837,'n');


// $tmp = $sms->send('60001070040149' , '130', '2378', '預售屋', '323542', 'n', 0);

// $tmp = $sms->send('60001101715339' , '1239', '2464', '扣繳稅款', '737888', 'n', 0);
// 
// $tmp = $sms->send('60001100351248' , '298', '1171', '點交(結案)', '735084', 'n', 0);

// $tmp = $sms->send('60001100058318' , '903', '505', '代清償', '737568', 'n', 0);

// $tmp = $sms->send('60001101756440' , '119', '505', '賣方先動撥', '737512', 'n', 0);

// $tmp = $sms->send('96988100000694' , '1991', '3003', '保留款撥付', '620122', 'n', 0);
##
// $tmp = $sms->send('96988100055953' , '76', '4431', 'income', '641141', 'n', 0);

// $tmp = $sms->send('96988100099223' , '112', '418', 'income2', '649995', 'n', 0);
// $tmp = $sms->send('96988100063255' , '1852', '408', 'cheque', '32825', 'n', 0);

// $tmp = $sms->send('96988100081280' , '345', '226', 'chequetaisin', '2225', 'n', 0);

// $tmp = $sms->send('96988100044940' , '952', '1254', 'income', '640146', 'n', 0);

// $tmp = $sms->send('60001090715385' , '652', '494', '扣繳稅款', '666497', 'n', 0);

// $tmp = $sms->send('60001101184015' , '1144', '4283', '點交(結案)', '646566', 'n', 0);

// $tmp = $sms->send('96988100060261' , '574', '1444', '解除契約', '662645', 'n', 0);

// $tmp = $sms->send('96988100056035' , '76', '4431', '仲介服務費', '672690', 'n', 0);

// $tmp = $sms->send('96988090032545' , '95', '908', '仲介服務費', '673164', 'n', 0);

// $tmp = $sms->send('60001100683711' , '225', '3934', '代清償', '666405', 'n', 0);

// $tmp = $sms->send('60001100865748' , '1067', '2993', '賣方先動撥', '666405', 'n', 0);


// $tmp = $sms->send('60001100124742' , '1065', '1283', '保留款撥付', '657441', 'n', 0);
// $tmp = $sms->send('96988100073537' , '1120', '3503', '預售屋', '665342', 'n', 0);
//
//自訂簡訊發送對象
// $tmp = $sms->send2('96988090030954','扣繳稅款',525090,'5f4f2ab77ef16','n','','') ;
// $tmp = $sms->send2('60001090257951','點交(結案)',497833,'5ee860fe1a689','n','','') ;
// $tmp = $sms->send2('60001090557941','點交(結案)',537599,'5f7e7457a54c7','n',59344,2993) ;


// $sql = "SELECT * FROM tBankTranSms WHERE FIND_IN_SET (666713,bBankTranId) AND bDel = 0 AND bVR_Code = '96988100055984' AND bObjKind = '仲介服務費'  AND (bExport_nu = '' OR bExport_nu = '6125d2329a226')";
// 	// echo $sql."\r\n";
// 	$_rs = $conn->Execute($sql) ;

// 	if (!$_rs->EOF) { //有
// 		// echo 'AA';
// 		$tmp = $sms->send2('96988100055984','仲介服務費',666713,'6125d2329a226','n',50000,2814) ;
// 	}
// 	
// 	
// $tmp = $sms->send('96988100157275' , '19', '86', '代清償', '778386', 'n', 0);

$tmp = $sms->send('96988100037232' , '202', '2173', '點交(結案)', '782559', 'n', 0);

echo "<pre>";
 print_r($tmp);
echo "</pre>";


// $sms->send2(trim($rs->fields["tVR_Code"]),trim($rs->fields["tObjKind"]),$rs->fields['tId'],$mediaCode,$send,$realty,$storeId) ;
//$sms->send('14碼保證號碼' , '地政士id', '仲介店id', 'cheque', 'tExpense_cheque id', 'n', 0);
?>
