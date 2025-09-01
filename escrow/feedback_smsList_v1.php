<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_GET = escapeStr($_GET) ;

$cat = $_GET['cat'];

##選單##
$sql = "SELECT fDate FROM tFeedBackSmsLog WHERE fCategory = '".$cat."' GROUP BY fDate ORDER bY fDate DESC";

$rs = $conn->Execute($sql);
$date = ($_POST['date']) ? $_POST['date']:$rs->fields['fDate'];
while (!$rs->EOF) {
	$menu[$rs->fields['fDate']] = $rs->fields['fDate'];

	$rs->MoveNext();
}
##

$sql = "SELECT * FROM tFeedBackSmsLog WHERE fCategory = '".$cat."' AND fDate ='".$date."' ORDER bY fDate DESC";
// echo $sql;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$code = substr($rs->fields['fCode'], 0,2);
	$code2 = substr($rs->fields['fCode'],2);

	$data[$rs->fields['fCode']] = getScrivener($code,$code2);
	

	$rs->MoveNext();
}

// print_r($data);

##
function getScrivener($code,$id){
	global $conn;

	if ($code == 'SC') {
		$sql = "SELECT sName FROM tScrivener WHERE sId = '".(int)$id."'";//(SC0001)張立人
		$rs = $conn->Execute($sql);
		$txt = '('.$code.$id.')'.$rs->fields['sName'];
	}else{
		$sql = "SELECT (SELECT bName FROM tBrand AS b WHERE b.bId = bBrand) AS brand,bStore FROM tBranch WHERE bId = '".$id."'";//(TH00033)台灣房屋中壢直營店
		$rs = $conn->Execute($sql);
		$txt = $txt = '('.$code.$id.')'.$rs->fields['brand'].$rs->fields['bStore'];
	}

	return $txt;
	
	
}

##
$smarty->assign('cat',$cat);
$smarty->assign('date',$date);
$smarty->assign('data',$data);
$smarty->assign('menu',$menu);
$smarty->assign('menu_cat',array(1=>'通知簡訊',0=>'收款簡訊'));
$smarty->display('feedback_smsList_v1.inc.tpl', '', 'escrow');

?>