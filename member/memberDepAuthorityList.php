<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;



if ($_GET) {
	$_GET = escapeStr($_GET) ;
	$dep = $_GET['dep'];
	
}


if ($_POST) {
	$_POST = escapeStr($_POST) ;

	// echo "<pre>";
	// print_r($_POST);
	// header("Content-Type:text/html; charset=utf-8"); 
	$sql = "UPDATE tPowerList SET pFunction = '".json_encode($_POST)."' WHERE pId = '".$dep."'";

	$conn->Execute($sql);

	if ($conn->Execute($sql)) {
		$msg = '更新成功';
	}
	
	// echo $sql."\r\n";

	// foreach ($_POST['id'] as $k => $v) {
	// 	// $expId = explode('_', $v);

		
	// 	// $expchild = explode('_', string)
	// 	unset($expId);
	// }

	// $sql = "UPDATE tPowerList SET pTitle = '".."' WHERE ";
}


##全部權限
$list_main = array();
$list_branch = array();

$sql = "SELECT * FROM tPeopleInfoAuthority WHERE pDelete = 0 ORDER BY pLevel,pId ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$rs->fields['pAuthority2'] = ($rs->fields['pAuthority2'])?unserialize($rs->fields['pAuthority2']):array();


	if ($rs->fields['pLevel'] == 0) {
		$list_main[] = $rs->fields;
	}else{
		$list_branch[$rs->fields['pGroup']][] = $rs->fields;
	}
	
	

	$rs->MoveNext();
}
##
$menuDep[0] = '請選擇';

if ($_SESSION['member_id'] != 6) {
	$str = ' pId NOT IN (1,2,3,4)';
}else{
	$str = ' 1=1';
}

$sql = "SELECT * FROM tPowerList WHERE ".$str;
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuDep[$rs->fields['pId']] = $rs->fields['pTitle'];

	if ($dep == $rs->fields['pId']) {

		$data = json_decode($rs->fields['pFunction'],true);

	}
	
	

	$rs->MoveNext();
}



// header("Content-Type:text/html; charset=utf-8"); 

// 0:無權限;1:可查詢;2:可上傳+查詢

// $QQ[0] = '無權限';
// $QQ[1] = '可查詢';
// $QQ[2] = '可上傳+查詢';

// echo serialize($QQ);
// echo "<pre>";
// 	print_r($data);
##
$smarty->assign('data',$data);
$smarty->assign('msg',$msg);
$smarty->assign('dep',$dep);
$smarty->assign("menuDep",$menuDep);
$smarty->assign("list_main",$list_main);
$smarty->assign("list_branch",$list_branch);
$smarty->display('memberDepAuthorityList.inc.tpl', '', 'member');
?>
