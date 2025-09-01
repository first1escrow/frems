<?php
include_once dirname(dirname(dirname(__FILE__))).'/configs/config.class.php';
include_once dirname(dirname(dirname(__FILE__))).'/class/SmartyMain.class.php';
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$undertaker = $_SESSION['member_id'];

##test value##
if ($undertaker == 6) {
	$undertaker = 22 ;
}
// $undertaker = 19;
#####


$sql = "SELECT
			aa.aId,
			aa.aAccount,
			aa.aName,
			aa.aParentId,
			s.sOffice
		FROM
			tAppAccount AS aa,
			tScrivener AS s
		WHERE
			SUBSTR(aa.aParentId,3)=s.sId  
			AND sUndertaker1 = '".$undertaker."'
			AND s.sStatus = '1'
			AND aa.aStatus = 1
			AND aa.aIdentity = 1
			AND aa.aOK = 1
		ORDER BY aa.aParentId ASC
		
		";
// echo $sql;
$rs = $conn->Execute($sql);

// echo $name;
$j = 0;
while (!$rs->EOF) {
	$list[$rs->fields['aParentId']]['no'] = $rs->fields['aParentId'];
	$list[$rs->fields['aParentId']]['office'] = $rs->fields['sOffice'];
	$list[$rs->fields['aParentId']]['data'][] = $rs->fields;
	if ($j == 0) {
		$targetAcc = $rs->fields['aId'];
		$name = $rs->fields['aName'];
		$j++;
	}

	$rs->MoveNext();
}

###
if ($_GET['target']) { //儲存之後轉頁回來
	$targetAcc = $_GET['target'];
}
##
// //訊息預設為第一個人(先預設7天內訊息)
// // $date
// $sql = "SELECT * FROM tAppMessages WHERE aAccount ='".$targetAcc."' AND TO_DAYS(NOW()) - TO_DAYS(aCreateTime) <= 7 ORDER BY id ASC ";
// // echo $sql;
// $rs = $conn->Execute($sql);
// while (!$rs->EOF) {
// 	$message[] = $rs->fields;

// 	$rs->MoveNext();
// }

###
$smarty->assign('list',$list);
$smarty->assign('name',$name);
$smarty->assign('message',$message);
$smarty->assign('targetAcc',$targetAcc);
$smarty->display('messageManager.inc.tpl', '', 'mobile');
?>
