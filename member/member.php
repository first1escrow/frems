<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$save  = $_REQUEST['save'];
$alert = '';

//取得登入身分
$sql    = 'SELECT * FROM tPeopleInfo WHERE pId="' . $_SESSION['member_id'] . '";';
$rs     = $conn->Execute($sql);
$member = $rs->fields;
$rs     = null;unset($rs);

//變更儲存密碼
if ($save == 'ok') {
    $memberOld = $_REQUEST['memberOld'];
    $memberNew = $_REQUEST['memberNew'];

    if ($memberOld == $member['pPassword']) {
        $sql = '
			UPDATE
				tPeopleInfo
			SET
				pPassword="' . $memberNew . '"
			WHERE
				pId="' . $_SESSION['member_id'] . '" ;
		';
        $conn->Execute($sql);
        $alert = 'alert("密碼更新成功!!新密碼將於下次登入時生效!!") ;';
    } else {
        $alert = 'alert("登入密碼不正確!!") ;';
    }
}

//是否可編修員工功能表
$more = '';
if ($member['pStaffManage'] == '1') {
    $more = '<a href="memberTable.php" >查看更多...</a>';
}

$smarty->assign('member', $member);
$smarty->assign('alert', $alert);
$smarty->assign('more', $more);

$smarty->display('member.inc.tpl', '', 'member');
