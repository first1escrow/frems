<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$id = empty($_GET['id']) ? $_POST['id'] : $_GET['id'];
$cat = empty($_GET['cat']) ? $_POST['cat'] : $_GET['cat']; //1add 2modify 3delete
// echo $id;

if ($_POST['cat']) {

	if ($_POST['cat'] == 2) { //修改

		
		
		//aPassword = '".$_POST['password']."',
		$sql = "UPDATE
					tAppAccount2020
				SET
					aName = '".$_POST['name']."',
					aStatus = '".$_POST['status']."',
					aParentId = '".$_POST['ParentId']."',
					aParentPhone = '".$_POST['ParentPhone']."',
					aIdentity = '".$_POST['identity']."',
					aDeviceId = '".$_POST['DeviceId']."',
					aModel = '".$_POST['Model']."',
					aOK = '".$_POST['ideOk']."',
					aSmsOption = '".$_POST['sms']."',
					aMemo = '".$_POST['memo']."',
					aGroup = '".$_POST['group']."'
				WHERE
					aId = '".$_POST['id']."'
				";
		
		$conn->Execute($sql);


	}elseif ($_POST['cat'] == 1) { //新增
		

		$sql = "INSERT INTO
					tAppAccount2020
					(
						aAccount,
						aName,
						aPassword,
						aStatus,
						aParentId,
						aParentPhone,
						aIdentity,
						aOK,
						aSmsOption,
						aMemo,
						aGroup
					)
				VALUES(
					'".$_POST['account']."',
					'".$_POST['name']."',
					'".$_POST['password']."',
					'".$_POST['status']."',
					'".$_POST['ParentId']."',
					'".$_POST['ParentPhone']."',
					'".$_POST['identity']."',					
					'".$_POST['ideOk']."',
					'".$_POST['sms']."',
					'".$_POST['memo']."',
					'".$_POST['group']."'

					)";
		// echo $sql;
		// die;
		$conn->Execute($sql);
		$id = $conn->Insert_ID();

	}elseif ($_POST['cat'] == 3) {
		// $sql = "UPDATE
		// 			tAppNews
		// 		SET
		// 			aDel = 1,
		// 			aEditor = '".$_SESSION['member_id']."'
		// 		WHERE
		// 			aId = '".$id."'";
				
		// $conn->Execute($sql);

		// echo "<script>parent.$.fn.colorbox.close();</script>";
	}

	// echo $sql;
}

$sql = "SELECT * FROM tAppAccount2020 WHERE aId ='".$id."'";
$rs = $conn->Execute($sql);




###
$smarty->assign('data',$rs->fields);
$smarty->assign('cat',$cat);
$smarty->assign('menu',array(0=>'無',1=>'有'));
$smarty->assign('menu_status',array(1=>'有效',2=>'凍結'));
$smarty->assign('menu_identity',array(1=>'地政士',2=>'仲介'));
$smarty->assign('menu_OK',array(1=>'完成',''=>'未完成認證'));
$smarty->assign('menu_sms',array(1=>'手機簡訊',2=>'APP',3=>'手機簡訊+APP')); //1=手機簡訊、2=APP、3=手機簡訊+PP
$smarty->display('mobileAccountEdit.inc.tpl', '', 'mobile');
?>
