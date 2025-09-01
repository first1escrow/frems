<?php
include_once('../openadodb.php') ;
include_once '../session_check.php' ;

//取得合約銀行資料
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
$conBank = array() ;
while (!$rs->EOF) {
	$conBank[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

$sql = '
	SELECT
		*
	FROM
		tExpense
	WHERE
		id="'.$_POST['id'].'" ;
' ;
$rs = $conn->Execute($sql) ;

$msg = '' ;
while(!$rs->EOF) {
/*
	if ($rs->fields['eAccount']=='10401810001999') {
		$msg .= '永豐銀行' ;
		$msg .= $rs->fields['eSummary'] ;
		if ($rs->fields['eRemark']) {
			$msg .= ' / ' ;
		}
		$msg .= $rs->fields['eRemark'] ;
	}
	else if ($rs->fields['eAccount']=='20680100135997') {
		$msg .= $rs->fields['ePayTitle'] ;
		if ($rs->fields['eRemarkContent']) {
			$msg .= ' / ' ;
		}
		$msg .= $rs->fields['eRemarkContent'] ;
	}
	//else if ($rs->fields['eAccount']=='27110352556') {
	else {
		$msg .= $rs->fields['ePayTitle'] ;
		if ($rs->fields['eRemarkContent']) {
			$msg .= ' / ' ;
		}
		$msg .= $rs->fields['eRemarkContent'] ;
	}
*/
	//建立入賬銀行明細資料
	foreach ($conBank as $k => $v) {
		if ($rs->fields['eAccount'] == $v['cBankTrustAccount']) {
			
			
			//若對象為永豐時，則加註分行別
			if ($v['cBankAccount']=='10401810001889') {
				$msg .= '('.$v['cBankName'].$v['cBranchName'].')' ;
				$msg .= $rs->fields['eSummary'] ;
				if ($rs->fields['eRemark']) {
					$msg .= ' / ' ;
				}
				$msg .= $rs->fields['eRemark'] ;
			}
			##
			
			//若對象為其他銀行時
			else {
				$msg .= '('.$v['cBankName'].')'.$rs->fields['ePayTitle'] ;
				if ($rs->fields['eRemarkContent']) {
					$msg .= ' / ' ;
				}
				$msg .= $rs->fields['eRemarkContent'] ;
			}
			##
			
		}
	}

	$rs->MoveNext() ;
}

echo $msg ;
//echo $num ;
?>