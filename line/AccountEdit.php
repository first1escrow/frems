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

		if ($_POST['status'] == 'Y') {
			# code...
			$str = "lStage1Auth = 'Y',lStage2Auth = 'Y',";
			
		}
		//
		$sql = "UPDATE
					tLineAccount
				SET
					lTargetCode = '".$_POST['code']."',
					lNickName ='".$_POST['name']."',
					lStatus ='".$_POST['status']."',
					lCaseMobile = '".$_POST['ParentPhone']."',
					".$str."
					lCaseMobile2 = '".$_POST['ParentPhone2']."',
					lIdentity = '".$_POST['lIdentity']."',
					lWeb = '".$_POST['web']."',
					lScrivener = '".$_POST['scrivener']."',
					lbranch = '".$_POST['branch']."',
					lChildStore = '".$_POST['childStore']."'
				WHERE
					lId = '".$id."'";
		// echo $sql;
		$conn->Execute($sql);


	}

	// echo $sql;
}

$sql = "SELECT * FROM tLineAccount WHERE lId ='".$id."'";
// echo $sql;
$rs = $conn->Execute($sql);

if ($rs->fields['lStage1Auth'] == 'Y') {
	$rs->fields['lStage1Auth'] = '認證通過';
}elseif ($rs->fields['lStage1Auth'] == 'N') {
	# code...未認證
	$rs->fields['lStage1Auth'] = '未認證';
}

if ($rs->fields['lStage2Auth'] == 'Y') {
	$rs->fields['lStage2Auth'] = '認證通過';
}elseif ($rs->fields['lStage2Auth'] == 'N') {
	# code...未認證
	$rs->fields['lStage2Auth'] = '未認證';
}

$data = $rs->fields;


##店家選單##

	$sql = "SELECT *,CONCAT('SC',LPAD(sId,4,'0')) as Code FROM tScrivener WHERE sStatus = 1 ORDER BY sId ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$menu_store[$rs->fields['Code']] = $rs->fields['Code'].$rs->fields['sName']."(".$rs->fields['sOffice'].")";

		$rs->MoveNext();
	}

	$sql = "SELECT *,CONCAT((SELECT bCode FROM tBrand AS b WHERE b.bId=bBrand ),LPAD(bId,5,'0')) as Code,(SELECT bName FROM tBrand AS b WHERE b.bId=bBrand) AS brand FROM tBranch WHERE bStatus = 1 ORDER BY bId ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$menu_store[$rs->fields['Code']] = $rs->fields['Code'].$rs->fields['brand'].$rs->fields['bStore'];

		$rs->MoveNext();
	}





###
$smarty->assign('data',$data);
$smarty->assign('cat',$cat);
$smarty->assign('menu_store',$menu_store);
$smarty->assign('menu_status',array('Y'=>'有效','N'=>'凍結'));
$smarty->assign('menu_identity',array('S'=>'地政士','R'=>'仲介','B'=>'經紀人'));
$smarty->assign('menu1',array(0=>'否',1=>'是'));
$smarty->display('AccountEdit.inc.tpl', '', 'line');
?>
