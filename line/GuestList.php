<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_REQUEST = escapeStr($_REQUEST) ;


$sql = "SELECT lId,lNickName,lFollowTime,lLineId FROM tLineGuest AS lg WHERE lUnfollowTime = '0000-00-00 00:00:00' ORDER BY lId DESC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	if (!checkLineAccount($rs->fields['lLineId'])) {
		$list[] = $rs->fields;
	}
	

	$rs->MoveNext();
}

function checkLineAccount($lId){
	global $conn;

	$sql = "SELECT * FROM tLineAccount WHERE lLineId = '".$lId."'";
	// echo $sql;
	$rs = $conn->Execute($sql);

	if ($rs->fields['lLineId']) {
		return true;
	}else{
		return false;
	}
	

}
###

$smarty->assign('list',$list);
$smarty->display('GuestList.inc.tpl', '', 'line');
?>
