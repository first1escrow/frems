<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$contract = new Contract();
// $cat = escapeStr($_POST['cat']) ;
$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];

$id = escapeStr($id) ;

$cat = empty($_POST["cat"]) 
        ? $_GET["cat"]
        : $_POST["cat"];

$cat = escapeStr($cat) ;


if ($_POST['cat'] == 'add') {
	
	if ($id == '') {
		
		$sql = "INSERT INTO tBankInfo(bBank,bPhoneArea,bPhone,bPhoneExt,bUrl,bNote,bModifyName) VALUES('".$_POST['bank']."','".$_POST['phoneArea']."','".$_POST['phone']."','".$_POST['phoneExt']."','".$_POST['bankUrl']."','".$_POST['bankNote']."','".$_SESSION['member_id']."')";

		$conn->Execute($sql);

		$id = $conn->Insert_ID(); 
	}

	$cat = 'mod';//新增後轉頁面
	

}else if ($_POST['cat'] == 'mod'){
		$sql = "UPDATE tBankInfo SET  bBank ='".$_POST['bank']."',bPhoneArea = '".$_POST['phoneArea']."',bPhone ='".$_POST['phone']."',bPhoneExt ='".$_POST['phoneExt']."',bUrl = '".$_POST['bankUrl']."',bNote ='".$_POST['bankNote']."',bModifyName='".$_SESSION['member_id']."' WHERE bId ='".$id."'";
		$conn->Execute($sql);
}

$sql = "SELECT * FROM tBankInfo WHERE bId ='".$id."'";
// echo $sql;
$rs = $conn->Execute($sql);
$data = $rs->fields;
##
$menu_bank = $contract->GetBankMenuList();


###
$smarty->assign('menu_bank',$menu_bank);
$smarty->assign("data",$data);
$smarty->assign('cat',$cat);
$smarty->assign('id',$id);
$smarty->display('bankInfoEdit.inc.tpl', '', 'other');
?>
