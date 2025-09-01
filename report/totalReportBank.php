<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../report/getBranchType.php';

$_POST = escapeStr($_POST) ;
// $sDate = (substr($_POST['sDate'], 0,3)+1911).substr($_POST['sDate'], 3);
// $eDate = (substr($_POST['eDate'], 0,3)+1911).substr($_POST['eDate'], 3);
$sDate = date('Y-m')."-01";
$eDate = date('Y-m-d');

$sql = "SELECT cBankCode,cBankName,cBranchName FROM tContractBank WHERE cShow = 1 ORDER BY cOrder ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	if ($rs->fields['cBankCode'] == 80) {//永豐一起算
		$rs->fields['cBankCode'] = 77 ;
	}
	$bank[$rs->fields['cBankCode']]['BankName'] = $rs->fields['cBankName'].$rs->fields['cBranchName'];
	// $bank[$rs->fields['cBankCode']]['BranchName'] = $rs->fields['cBranchName'];
	// $bank[$rs->fields['cBankCode']]['count'] = 0;
	$rs->MoveNext();
}

$yearArr = array();
## 一銀/永豐/台新，一個年度分別約有多少筆案件（結案）
$sql = "SELECT cEndDate,cBank FROM tContractCase WHERE cCaseStatus NOT IN (2,6,8) AND cEndDate != '0000-00-00 00:00:00'";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$year = substr($rs->fields['cEndDate'], 0,4);
	
		// $data[$rs->fields['cBank']][$year]++;
		$yearArr[$year] = $year;
		if ($rs->fields['cBank'] == 80) { //永豐一起算
			$rs->fields['cBank'] = 77 ;
		}
		$bank[$rs->fields['cBank']][$year]++;
	
	

	$rs->MoveNext();
}

sort($yearArr);


// echo "<pre>";
// print_r($bank);
##
$smarty->assign("yearArr",$yearArr);
$smarty->assign("bank",$bank);
$smarty->assign('data',$data);
$smarty->display('totalReportBank.inc.tpl', '', 'report');
?>