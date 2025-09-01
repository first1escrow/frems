<?php
require_once dirname(__DIR__).'/configs/config.class.php';
require_once dirname(__DIR__).'/class/SmartyMain.class.php';
require_once dirname(__DIR__).'/first1DB.php';

if (session_status() != 2) {
	session_start();
}

$staff_name    = $_SESSION['member_name'];
$staff_account = $_SESSION['member_acc'];
$staff_id      = $_SESSION['member_id'];

$conn = new first1DB;

if ($_POST['save_ok']) {
	$fax_num = '';

	if ($_POST['FaxArea'] && $_POST['FaxNum1'] && $_POST['FaxNum2']) {
		$fax_num = $_POST['FaxArea'].'-'.$_POST['FaxNum1'].$_POST['FaxNum2'];
	}

	$sql = 'UPDATE tPeopleInfo SET pExt = :pExt, pFaxNum = :pFaxNum WHERE pId = :pId;';
	$conn->exeSql($sql, [
		'pExt'		=> $_POST['pExt'],
		'pFaxNum'	=> $fax_num,
		'pId'		=> $staff_id,
	]);
}

// 員工基本資料
$sql = 'SELECT * FROM `tPeopleInfo` WHERE `pId` = :pId;';
$tmp = $conn->one($sql, ['pId' => $staff_id]);

$staff_ext = $tmp['pExt'];
if ($tmp['pFaxNum']) {
	$staff_faxarea = substr($tmp['pFaxNum'],0,2);
	$staff_fax1    = substr($tmp['pFaxNum'],3,4);
	$staff_fax2    = substr($tmp['pFaxNum'],7,4);	
}

$smarty->assign("staff_name",    $staff_name);
$smarty->assign("staff_account", $staff_account);
$smarty->assign("staff_ext",     $staff_ext);
$smarty->assign("staff_faxarea", $staff_faxarea);
$smarty->assign("staff_fax1",    $staff_fax1);
$smarty->assign("staff_fax2",    $staff_fax2);

$smarty->display('staff.inc.tpl', '', 'staff');
?>
