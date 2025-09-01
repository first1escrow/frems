<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
##
$year = date('Y');

// print_r($_POST);

##
//年
$menuYear = array();
for ($i=110; $i <= (date('Y')-1910) ; $i++) { 
	$menuYear[$i] = $i;
}
//月
$menuMonth = array();
for ($i=1; $i <= 12; $i++) { 
	$menuMonth[str_pad($i, 2,0,STR_PAD_LEFT)] = $i;
}
//地政士
$menuScrivener = array();

$sql = "SELECT sId,sName,CONCAT('SC', LPAD(sId,4,'0')) as sCode2,sBirthday FROM tScrivener WHERE sStatus = 1 AND sBirthday !='0000-00-00' ORDER BY MONTH(sBirthday),DAY(sBirthday) ";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuScrivener[$rs->fields['sId']] = $rs->fields['sCode2'].$rs->fields['sName']."(".$rs->fields['sBirthday'].")";

	$rs->MoveNext();
}
##
$smarty->assign('year',($year-1911));
$smarty->assign('month',date('m'));
$smarty->assign('menuYear',$menuYear);
$smarty->assign('menuMonth',$menuMonth);
$smarty->assign('menuScrivener',$menuScrivener);
$smarty->display('ScrivenerPresentApplyLock.inc.tpl', '', 'scrivener');
?>
