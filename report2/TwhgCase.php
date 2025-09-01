<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
if ($_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 1 || $_SESSION['member_id'] == 3) {
	
	if ($_POST['people']) {
		$str = ' AND s.sUndertaker1 = '.$_POST['people'];
	}

	
}else{
	$str = ' AND s.sUndertaker1 = '.$_SESSION['member_id'];
}



##

$sql  = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6) AND pBankTrans IN(1,2) AND pId!=6 AND pJob = 1  ORDER BY pId ASC ";
$rs = $conn->Execute($sql);
$menuPeople[0] = '全部';
while (!$rs->EOF) {
	
	$menuPeople[$rs->fields['pId']] = $rs->fields['pName']; //選項

	$rs->MoveNext();
}

$sql = "SELECT 
			tc.tCertifiedId,
			tc.tStatusNote,
			tc.tTwhgBranchName,
			tc.tTwhgScrivener,
			tc.tTwhgSignDate,
			s.sName,
			CONCAT('SC',LPAD(s.sId,4,'0')) as Code,
			(SELECT pName FROM tPeopleInfo WHERE pId=s.sUndertaker1) AS Undertaker
		FROM
			tTwhgCase AS tc
		LEFT JOIN 
			tScrivener AS s ON s.sId=tc.tScrivener
		WHERE
			tc.tStatus = 1 ".$str." ORDER BY tc.tTwhgSignDate DESC";
			// echo $sql;
$rs = $conn->Execute($sql);
$i= 0;
while (!$rs->EOF) {
	
	$list[$i] = $rs->fields;
	if (preg_match("/簽約/", $rs->fields['tStatusNote'])) {
		$list[$i]['err'] = 1;
	}
	$i++;
	$rs->MoveNext();
}
##
##

$smarty->assign('menuPeople',$menuPeople);
$smarty->assign('people',$people);
$smarty->assign('list',$list);
$smarty->display('TwhgCase.inc.tpl', '', 'report2');
?>