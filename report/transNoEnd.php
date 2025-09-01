<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once dirname(__DIR__).'/includes/lib/contractBank.php';

//
Function dateFormat($dt) {
	$tmp = array() ;
	//echo $dt ; exit ;
	if (preg_match("/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})/",$dt,$tmp)) {
		$dt = ($tmp[1] - 1911).'-'.$tmp[2].'-'.$tmp[3] ;
	}
	else $dt = '' ;
	//echo $dt ; exit ;
	return $dt ;
}
##


/**
 * 2022-06-15
 * 取得所有合約銀行資訊
 * Function getContractBank() {}
 * Path: /includes/lib/contractBank.php
 */


$member = $_SESSION['member_id'];
if ($member == 39) {
	$member = 1;
}
$tbl = '' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;
$year = $_POST['listDate'] ;
$checkRemind = $_GET['s'];
//if (!preg_match("/^[0-9]{2,3}$/",$year)) $year = date("Y") ;
if (!$year) $year = date("Y") ;
//echo 'YR='.$year ;
//2015開始有
$sql = '
	SELECT
		a.cCertifiedId,
		a.cApplyDate,
		a.cSignDate,
		(SELECT sName FROM tStatusCase WHERE a.cCaseStatus = sId) as status,
		b.tBankLoansDate as TransDate,
		b.tMoney as TransMoney
	FROM
		tContractCase AS a,
		tBankTrans AS b 
	WHERE
		a.cCertifiedId = b.tMemo
		AND a.cSignDate >= "2015-01-01 00:00:00"
		AND a.cSignDate <= "'.$year.'-12-31 23:59:59"
		AND a.cCaseStatus = "2"
		AND b.tKind = "保證費"
		AND b.tAccount IN ("27110351738","10401810001889","10401810001889","96988000000008")
		AND b.tPayOk = "1"
	
;' ;
$rs = $conn->Execute($sql) ;

//取得所有合約銀行資訊
$banks = getContractBank();
##

$list = array() ;
$i = 0 ;
while (!$rs->EOF) {
	$rs->fields['cApplyDate'] = dateFormat($rs->fields['cApplyDate']) ;
	$rs->fields['cSignDate'] = dateFormat($rs->fields['cSignDate']) ;
	$rs->fields['TransDate'] = dateFormat($rs->fields['TransDate']) ;
	//$rs->fields[''] = dateFormat($rs->fields['']) ;
	
    $sql_arr = array();
	foreach ($banks as $v) {
    	$sql_arr[] = 'a.bAccount = "'.substr($v['cBankVR'], 0, 5).$rs->fields['cCertifiedId'].'"';
	}

	// $sql = '
	// 	SELECT
	// 		c.pName,
	// 		b.sUndertaker1
	// 	FROM
	// 		tBankCode AS a
	// 	JOIN
	// 		tScrivener AS b ON a.bSID = b.sId
	// 	JOIN
	// 		tPeopleInfo AS c ON b.sUndertaker1 = c.pId
	// 	WHERE
	// 		SUBSTR(a.bAccount,6) = "'.$rs->fields['cCertifiedId'].'"
	// ;' ;

	$sql = '
		SELECT
			c.pName,
			b.sUndertaker1
		FROM
			tBankCode AS a
		JOIN
			tScrivener AS b ON a.bSID = b.sId
		JOIN
			tPeopleInfo AS c ON b.sUndertaker1 = c.pId
		WHERE
			'.implode(' OR ', $sql_arr).'
	;' ;
	$sql_arr = NULL;
	unset($sql_arr);
    // exit($sql);
	$rel = $conn->Execute($sql) ;
	$rs->fields['staff'] = $rel->fields['pName'] ;
	
	$colorIndex = '#F8ECE9' ;
	
	
	if ($member == $rel->fields['sUndertaker1'] || $_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 1) {
		if ($i % 2 == 0) $colorIndex = '' ;
	
		$tbl .= '
		<tr style="background-color:'.$colorIndex.';">
			<td>'.($i+1).'</td>
			<td><a href="#" onclick="contract(\''.$rs->fields['cCertifiedId'].'\')">'.$rs->fields['cCertifiedId'].'</a></td>
			<td>'.$rs->fields['cApplyDate'].'</td>
			<td>'.$rs->fields['cSignDate'].'</td>
			<td>'.$rs->fields['status'].'</td>
			<td style="text-align:right;">'.number_format($rs->fields['TransMoney']).'</td>
			<td>'.$rs->fields['TransDate'].'</td>
			<td>'.$rs->fields['staff'].'</td>
		</tr>
		' ;
		$list[] = $rs->fields ;
	
		$i ++ ;
	}
	
	$rs->MoveNext() ;
}
// print_r($list) ; exit ;

if (count($list) <= 0) {
	$tbl = '
		<tr style="background-color:;">
			<td colspan="8">查無相關案件</td>
		</tr>
	' ;
}
##

$smarty->assign('tbl',$tbl) ;
$smarty->assign('yr',$year) ;
$smarty->assign('checkRemind',$checkRemind);
$smarty->display('transNoEnd.inc.tpl', '', 'report') ;
?>