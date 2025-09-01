<?php

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/class/getBank.php';
require_once dirname(__DIR__) . '/class/intolog.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '地政士黑名單');

//預載log物件
$logs = new Intolog();
##
$id  = ($_POST['id']) ? $_POST["id"] : $_GET['id'];
$cat = ($_POST['cat']) ? $_POST['cat'] : $_GET['cat'];
##
if ($_POST) {
    if ($cat == 'add') {
        $sql = "INSERT INTO
					tScrivenerBlackList
				SET
					sName = '" . $_POST['name'] . "',
					sIdentifyId = '" . $_POST['IdentifyId'] . "',
					sOffice = '" . $_POST['office'] . "',
					sZip = '" . $_POST['zip'] . "',
					sAddress = '" . $_POST['address'] . "',
					sCreator = '" . $_SESSION['member_id'] . "',
					sCreatTime = '" . date('Y-m-d H:i:s') . "',
					sEditor = '" . $_SESSION['member_id'] . "'";
        $conn->Execute($sql);
        $id = $conn->Insert_ID();

        header("location:formScrivenerBlackListEdit.php?act=edit&id=" . $id);
    } else if ($cat == 'mod') {
        $sql = "UPDATE
					tScrivenerBlackList
				SET
					sName = '" . $_POST['name'] . "',
					sIdentifyId = '" . $_POST['IdentifyId'] . "',
					sOffice = '" . $_POST['office'] . "',
					sZip = '" . $_POST['zip'] . "',
					sAddress = '" . $_POST['address'] . "',
					sEditor = '" . $_SESSION['member_id'] . "'
				WHERE
					sId = '" . $_POST['id'] . "'";

        $conn->Execute($sql);

    } elseif ($cat == 'del') {
        $sql = "UPDATE tScrivenerBlackList SET sDelete = 1 WHERE sId = '" . $_POST['id'] . "'";
        $conn->Execute($sql);
        //
        $scrivenerList = array();
        $sql           = "SELECT sId FROM tScrivener WHERE sBlackListId = '" . $_POST['id'] . "'";
        $rs            = $conn->Execute($sql);
        while (!$rs->EOF) {
            $scrivenerList[] = $rs->fields['sId'];
            $rs->MoveNext();
        }

        //更新地政士基本資料維護(將黑名單清除)
        $sql = "UPDATE tScrivener SET sBlackListId = 0 WHERE sBlackListId = '" . $_POST['id'] . "'";
        $conn->Execute($sql);

        //LINE、APP 要手動開啟 怕會影響到已經關閉的帳號

        $conn->close();
        //

        header("Location:scrivenerBlackList.php");
    }
}

//查詢資料
$sql = "SELECT
			*,
			(SELECT pName FROM tPeopleInfo WHERE pId = sCreator) AS creator,
			(SELECT pName FROM tPeopleInfo WHERE pId = sEditor) AS editor
		FROM
			tScrivenerBlackList WHERE sId = '" . $id . "'";
$rs   = $conn->Execute($sql);
$data = $rs->fields;

##
$smarty->assign('data', $data);
$smarty->assign('id', $id);
$smarty->assign('cat', $cat);
$smarty->assign('listCity', listCity($conn, $data['sZip'])); //聯絡地址-縣市
$smarty->assign('listArea', listArea($conn, $data['sZip'])); //聯絡地址-區域
$smarty->display('formScrivenerBlackListEdit.inc.tpl', '', 'maintain');
