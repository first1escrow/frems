<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['searchTxt']) {
	$str = "AND (bBank LIKE '%".$_POST['searchTxt']."%' OR bNote LIKE '%".$_POST['searchTxt']."%')";
}



$sql = "SELECT
			*,
			(SELECT bBank4_name FROM tBank WHERE bBank3 =bBank AND bBank4 ='') AS bankName,
			(SELECT pName FROM tPeopleInfo WHERE pId = bModifyName) AS pName
		FROM tBankInfo WHERE bDel = 0 ".$str." ORDER BY bId ASC";
// echo $sql;
$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	# code...

	$list[$i] = $rs->fields;
	$list[$i]['bNote'] = nl2br($list[$i]['bNote']);
	$i++;
	$rs->MoveNext();
}

###
$smarty->assign('list',$list);
$smarty->display('bankInfoList.inc.tpl', '', 'other');
?>
