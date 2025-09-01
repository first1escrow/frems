<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

$_POST = escapeStr($_POST) ;
if ($_POST['sDate'] && $_POST['eDate']) {

	$sDate = (substr($_POST['sDate'], 0,4) +1911).substr($_POST['sDate'], 3);
	$eDate = (substr($_POST['eDate'], 0,4) +1911).substr($_POST['eDate'], 3);
	// echo $sDate;

	
	$sql = "SELECT sSend_Time,tName FROM tSMS_Log WHERE tKind ='回饋金' AND (sSend_Time >= '".$sDate." 00:00:00' AND sSend_Time <= '".$eDate." 23:59:59') ";
	// echo $sql;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$date = (substr($rs->fields['sSend_Time'], 0,4) -1911).substr($rs->fields['sSend_Time'], 4,6); 
		$list[$date]['count']++;
		$list[$date]['send'] .= ($list[$date]['send'])? ','.$rs->fields['tName']:$rs->fields['tName'];
		$rs->MoveNext();
	}

	// echo "<pre>";

	// print_r($list);
}

##
$smarty->assign('sDate',$_POST['sDate']);
$smarty->assign('eDate',$_POST['eDate']);
$smarty->assign('list', $list);
$smarty->display('casefeedbackSmsSearch.inc.tpl', '', 'report');
?> 
