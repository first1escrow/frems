<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once  dirname(dirname(dirname(__FILE__))).'/session_check.php' ;



// echo $today;
if ($today) {
	$tBankLoansDate =( substr($today, 0,3)+1911).substr($today, 3);
	$str .= 'AND (tBankLoansDate >= "'.$tBankLoansDate.'" AND tBankLoansDate <= "'.$tBankLoansDate.'") ';
}

if ($cId) {
	$str .= "AND tMemo = '".$cId."'";
}
// $today = "2019-09-10";
// 支出部分
$sql_tra = '
SELECT 
	tBankLoansDate as tExport_time, 
	tObjKind,
	tKind, 
	tMoney, 
	tTxt,
	tId,
	tShow,
	tObjKind2Item,
	tBank_kind,
	tObjKind2,
	tAccountName,
	tPayOk,
	tMemo
FROM 
	tBankTrans 
WHERE 
	tObjKind2 != "02" 
	AND tBank_kind != "台新" AND (tObjKind = "解除契約" OR tObjKind = "點交(結案)") AND tKind = "賣方" '.$str.'

ORDER BY 
	tExport_time 
ASC ;
' ;


//賣方備註(沒賣方不用填寫;有非賣方帳戶要填寫;//結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2))
$rs= $conn->Execute($sql_tra);
// $arr_tra[] = '' ;
$exportCount = $rs->RecordCount();

while (!$rs->EOF) {
	$CertifiedId[$rs->fields['tMemo']]['status'] = 1;//要填寫
	$ownerArr = getOwner($rs->fields['tMemo']);

	$CertifiedId[$rs->fields['tMemo']]['cCertifiedId'] = $rs->fields['tMemo'];
	// $CertifiedId[$rs->fields['tMemo']]['status'] = 0; //顯示
	$CertifiedId[$rs->fields['tMemo']]['owner'] = $ownerArr;
	
	
		if (!in_array($rs->fields['tAccountName'], $ownerArr)) { //有非賣方帳戶要填寫

			$CertifiedId[$rs->fields['tMemo']]['status'] = '';
	
		}

		if (!getOwnerBankAcc($rs->fields['tMemo'],$ownerArr)) {
			$CertifiedId[$rs->fields['tMemo']]['status'] = '';
		}

		
	
	unset($ownerArr);unset($check);
	$rs->MoveNext();
}

function getOwnerBankAcc($cId,$ownerArr){
	global $conn;

	$sql = 'SELECT 
		tAccountName
	FROM 
		tBankTrans 
	WHERE 
		tObjKind2 != "02" 
		AND tBank_kind != "台新" AND (tObjKind = "解除契約" OR tObjKind = "點交(結案)") AND tKind = "賣方" AND tMemo ="'.$cId.'"';

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$accName[] = $rs->fields['tAccountName'];

		$rs->MoveNext();
	}
	// echo $cId."<bR>";
	// print_r($accName);
	if (is_array($accName)) {
		foreach ($ownerArr as $k => $v) {
			// echo $v."<br>";
			if (!in_array($v,$accName)) { //結案有出賣方帳戶但其中有賣方未收錢(EX:賣1、賣2、賣3;出款只出給了賣1、賣2)
				return false;
			}
		}
	}
	return true;
}

$i = 0;
if ($CertifiedId) {
	// print_r($CertifiedId);
	foreach ($CertifiedId as $k => $v) {
		if ($v['status'] == '') {
			$sql = "SELECT tCertifiedId,tAnother FROM tBankTransSellerNote WHERE tCertifiedId = '".$k."'";
			$rs = $conn->Execute($sql);

			if ($rs->fields['tCertifiedId'] == '') {
				$sellerNote[$i] = $v;
				$sellerNote[$i]['Undertaker'] = getUndertaker($k);

				$i++;
			}
		}
		


	}
}



function getOwner($cId){
	global $conn;

	$sql = "SELECT cName FROM tContractOwner WHERE cCertifiedId = '".$cId."'";
	$rs = $conn->Execute($sql);

	$ownerArr[] = $rs->fields['cName'];

	$sql = "SELECT cName FROM tContractOthers WHERE cCertifiedId = '".$cId."' AND cIdentity = 2";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$ownerArr[] = $rs->fields['cName'];

		$rs->MoveNext();
	}


	return $ownerArr;
}

function getUndertaker($cId){
	global $conn;

	$sql = "SELECT
				(SELECT pName FROM tPeopleInfo WHERE pId = s.sUndertaker1) AS Undertaker
			FROM
				tContractScrivener AS cs
			LEFT JOIN
				tScrivener AS s ON s.sId = cs.cScrivener
			WHERE
				cs.cCertifiedId = '".$cId."'
				";
	$rs = $conn->Execute($sql);
		
	return $rs->fields['Undertaker'];
}

?>