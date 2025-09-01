<?php
include_once('../openadodb.php') ;
include_once '../session_check.php' ;
include_once '../configs/config.class.php';
include_once 'writelog.php';


$order = array('2'=>'1', '1'=>'2', '31'=>'3', '4'=>'4', '42'=>'5', '43'=>'6', '3'=>'7', '32'=>'8', '33'=>'9', '5'=>'10', '52'=>'11', '53'=>'12') ;
$a = $_POST ;
/*
if ($a['mod'] == 'ok') {
	$sql = '
		UPDATE 
			tChecklistBank 
		SET 
			cIdentity="'.$a['iden'].'",
			cBankMain="'.$a['bMain'].'",
			cBankBranch="'.$a['bBranch'].'",
			cBankAccountNo="'.$a['aNo'].'",
			cBankAccountName="'.$a['aName'].'",
			cOrder="'.$order[$a['iden']].'"
		WHERE
			cId="'.$a['id'].'"
	' ;
}
else if ($a['del'] == 'ok') {
	$sql = '
		DELETE FROM
			tChecklistBank
		WHERE
			cId="'.$a['id'].'"
	' ;
}
else*/ 
if ($a['newbank'] == 'ok') {
	$sql_in = '
		INSERT INTO
			tChecklistBank
			(
				cCertifiedId,
				cIdentity,
				cBankMain,
				cBankBranch,
				cBankAccountNo,
				cBankAccountName,
				cOrder,
				cMoney
			)
		VALUES
			(
				"'.$a['cCertified'].'",
				"'.$a['new_cIdentity'].'",
				"'.$a['newBankMain'].'",
				"'.$a['newBankBranch'].'",
				"'.$a['newAccountNo'].'",
				"'.$a['newAccountName'].'",
				"'.$order[$a['new_cIdentity']].'",
				"'.$a['newAccountMoney'].'"
			) ;
	' ;
	$conn->Execute($sql_in) ;

	checklist_log('指定收受價金之帳戶-新增(保證號碼:'.$a['cCertified'].')');

}else if ($a['del'] == 'ok') {
	$sql_in = '
		DELETE FROM
			tChecklistBank
		WHERE
			cId="'.$a['id'].'"
	' ;
	$conn->Execute($sql_in) ;
	checklist_log('指定收受價金之帳戶-刪除(保證號碼:'.$a['cCertified'].')');
}elseif ($a['mod'] == 'ok') {
	$sql_in = '
		UPDATE 
			tChecklistBank
		SET
			cMoney ="'.$a['money'].'"
		WHERE
			cId="'.$a['id'].'"
	' ;
	$conn->Execute($sql_in) ;
	checklist_log('指定收受價金之帳戶-修改(保證號碼:'.$a['cCertified'].')');
}elseif ($a['hide'] == 'ok') {
	$sql_in = '
		UPDATE 
			tChecklistBank
		SET
			cHide ="'.$a['val'].'"
		WHERE
			cId="'.$a['id'].'"
	' ;
	// echo $sql_in;

	$conn->Execute($sql_in) ;
	checklist_log('指定收受價金之帳戶-隱藏(保證號碼:'.$a['cCertified'].')');
}

// echo $a['new_cIdentity'];
## 指定收受價金之帳戶 列表

if ($a['new_cIdentity']==1||$a['new_cIdentity']==33||$a['new_cIdentity']==43||$a['new_cIdentity']==53) { //買方
	$sql = '
		SELECT
			*,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as BankMain,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BankBranch
		FROM
			tChecklistBank AS a
		WHERE
			cCertifiedId="'.$a['cCertified'].'"
			AND cIdentity IN ("1","33","43","53")
		ORDER BY
			cOrder,cId
		ASC;
	' ;
}else 
{
	$sql = '
	SELECT
		*,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as BankMain,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BankBranch
	FROM
		tChecklistBank AS a
	WHERE
		cCertifiedId="'.$a['cCertified'].'"
		AND cIdentity IN ("2","31","32","42","52")
	ORDER BY
		cOrder,cId
	ASC;
' ;
}



$rs = $conn->Execute($sql) ;

while (!$rs->EOF) {


	//中文化對象身分 (2、31、32、42、52)
	switch ($rs->fields['cIdentity']) {
		case '1' : 
				$rs->fields['cIdentity'] = '買方' ;
				break ;
		case '33' :
				$rs->fields['cIdentity'] = '仲介' ;
				break ;
		case '43' :
				$rs->fields['cIdentity'] = '地政士' ;
				break ;
		case '53' :
				$rs->fields['cIdentity'] = '其他' ;
				break ;
		case '2' :
				$rs->fields['cIdentity'] = '賣方' ;
				break ;
		case '31' :
				$rs->fields['cIdentity'] = '買方' ;
				break ;
		case '32' :
				$rs->fields['cIdentity'] = '仲介' ;
				break ;
		case '42' :
				$rs->fields['cIdentity'] = '地政士' ;
				break ;
		case '52' :
				$rs->fields['cIdentity'] = '其他' ;
				break ;
		default :
				$rs->fields['cIdentity'] = '' ;
				break ;
	}
	##


	
	//結合總分行顯示
	if ($rs->fields['cBankMain'] && $rs->fields['cBankBranch']) {
		$rs->fields['bank'] = $rs->fields['BankMain'].'/'.$rs->fields['BankBranch'] ;
	}
	##
	echo '
		<div class="gap" style="float:left;width:60px;">
		<input type="text" disabled="disabled" value="'.$rs->fields['cIdentity'].'">
		</div>
		<div class="gap" style="float:left;width:180px;">
			<input type="text" disabled="disabled" value="'.$rs->fields['bank'].'">
		</div>
		<div class="gap" style="float:left;width:180px;">
			<input type="text" disabled="disabled" value="'.$rs->fields['cBankAccountNo'].'">
		</div>
		<div class="gap" style="float:left;width:180px;">
			<input type="text" disabled="disabled" value="'.$rs->fields['cBankAccountName'].'">
		</div>
		<div class="gap" style="float:left;width:80px;">
			<input type="text"  value="'.$rs->fields['cMoney'].'" name="bankMoney'.$a['type'].$rs->fields['cId'].'" style="width:70px;">
		</div>
		<div class="gap" style=""><input type="button" value="修改" onclick="modBank('.trim($rs->fields['cId']).','.$a['type'].')" />';
	if ($rs->fields['cIdentity'] == '仲介' || $rs->fields['cIdentity'] == '地政士') {
		if ($rs->fields['cHide'] == 1) {
			echo'<input type="button" value="顯示" onclick="hideBank('.trim($rs->fields['cId']).','.$a['type'].',0)" />';
		}else{
			echo'<input type="button" value="隱藏" onclick="hideBank('.trim($rs->fields['cId']).','.$a['type'].',1)" />';
		}

	}else{
		echo'
		
		<input type="button" value="刪除" onclick="delBank('.trim($rs->fields['cId']).','.$a['type'].')">';
	}
	

	echo'	</div>
	';
	
	$rs->MoveNext();
}
##
?>