<?php

include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once '../../web_addr.php' ;
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;


$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$cat = empty($_POST["cat"]) 
        ? $_GET["cat"]
        : $_POST["cat"];

$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];
// filter_input()
        // print_r($_POST);
if ($_POST['Bank']) {

	$area = implode(',', $_POST['Area']);

	if ($cat == 'add') {
		//bLink2 = '".$_POST['link2']."',
		$sql = "INSERT INTO
					tBankBannerArea
					(
						bBank,
						bBankName,
						bBankName2,
						bArea
					)
				VALUES
					(
						'".$_POST['Bank']."',
						'".$_POST['BankName']."',
						'".$_POST['BankName2']."',
						'".$area."'
					)";
		
		$conn->Execute($sql);
		$cat = 'mod';
		$id = $conn->Insert_ID(); 


		
	}elseif ($cat == 'mod') {
		
		$sql = "UPDATE
					tBankBannerArea
				SET
					bBank = '".$_POST['Bank']."',
					bBankName = '".$_POST['BankName']."',
					bBankName2 = '".$_POST['BankName2']."',
					bArea = '".$area."'
				WHERE
					bId ='".$id."'
				";
		// echo $sql;
		// die;
		$conn->Execute($sql);
		
	}

	// echo $sql;
}
##
$sql = "SELECT * FROM tBankBannerArea WHERE bId = '".$id."'";

$rs = $conn->Execute($sql);
$data = $rs->fields;


if ($data['bArea']) {
	$checkCity = explode(',', $data['bArea']); //空為全部都要
}


// if ($data['bArea'] == '') {
// 	$checkCity2 = true;
// }
// $data['bArea']
$sql = "SELECT * FROM tZipArea GROUP BY zCity ORDER bY nid ASC";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$checked = '';
	if (is_array($checkCity)) {
		if (in_array($rs->fields['zCity'], $checkCity)) {
			$checked = 'checked=checked';
		}
	}else{
		$checked = 'checked=checked';
	}

	$menu_city[$rs->fields['zCity']] = $checked;

	$rs->MoveNext();
}


##
$smarty->assign('cat',$cat);
$smarty->assign('data',$data);
$smarty->assign('id',$id);
$smarty->assign('menu_city', $menu_city);
$smarty->display('BannerBankEdit.inc.tpl', '', 'www');
?>