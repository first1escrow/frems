<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/brand.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['cat'] != 'S' && $_POST["cat"] != 'R') {
	die("加值身分錯誤");
}

$status = 1;

$sql = "INSERT INTO tRgBonus
			(rAccount,rIdentity,rMoney,rStatus,rName)
		VALUES
			('".$_POST["id"]."','".$_POST['cat']."','".$_POST['money']."','".$status."','".$_SESSION['member_id']."')";


if ($conn->Execute($sql)) {
	echo "加值成功";
}

?>