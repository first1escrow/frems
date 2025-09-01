<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$str = 'lc.lDel = 0';
if ($_POST['CaseStatus'] > 0) {
	if ($str) {$str .= " AND ";}
	$str .= "lc.lStatus ='".$_POST['CaseStatus']."'";
	$caseStatus = $_POST['CaseStatus'];
}else{
	$caseStatus = 1;
	if ($str) {$str .= " AND ";}
	$str .= "lc.lStatus ='1'";
}

$sql = "SELECT
			lc.lCertifiedId,
			lcd.lId AS detailId,
			lcd.lEndDay AS detailEndDay,
			lcd.lNote AS detailNote,
			lcd.lStatus AS detailStatus
		FROM
			tLegalCase AS lc
		LEFT JOIN
			tLegalCaseDetail AS lcd ON lc.lCertifiedId=lcd.lCertifiedId
		WHERE ".$str." ORDER BY lcd.lId ASC";

$rs = $conn->Execute($sql);
$list = array();

while (!$rs->EOF) {
	$list[$rs->fields['lCertifiedId']]['lCertifiedId'] = $rs->fields['lCertifiedId'];

	if (!is_array($list[$rs->fields['lCertifiedId']]['data'])) {
			$list[$rs->fields['lCertifiedId']]['data'] = array();
		}

	if ($rs->fields['detailId']) {

		

		$caseDetail = array();
		$caseDetail = $rs->fields;

		//狀態判別
		if ($caseDetail['detailStatus'] == 1) {
			$caseDetail['detailStatus'] = '已完成';
		}else{
			$caseDetail['detailStatus'] = '未完成';
			//是否過期

			if (strtotime($caseDetail['detailEndDay']) < strtotime(date('Y-m-d'))) {
				$caseDetail['detailEndStatus'] = '已過期';
				$caseDetail['detailColor'] = "#FF7878";
			}
		}



		array_push($list[$rs->fields['lCertifiedId']]['data'], $caseDetail);
	}
	
	// $list[] = $rs->fields;
	// array_push($list, $rs->fields);

	$rs->MoveNext();
}

if ($_POST['xls'] != '') {
	require_once 'legalCaseListExcel.php';
	die;
}


##
$smarty->assign('menu_Status',array(1=>'進行中',2=>'已結束(移轉經辦)'));//0=>'全部',
$smarty->assign('list',$list);
$smarty->assign('CaseStatus',$caseStatus);
$smarty->display('legalCaseList.inc.tpl', '', 'legal');
?>