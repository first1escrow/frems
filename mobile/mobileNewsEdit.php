<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_GET = escapeStr($_GET) ;
// $_POST = escapeStr($_POST) ;

$id = empty($_GET['id']) ? $_POST['id'] : $_GET['id'];
$cat = empty($_GET['cat']) ? $_POST['cat'] : $_GET['cat']; //1add 2modify 3delete
// echo $_POST['cat'];
if ($_POST['cat']) {

	$filename = $_FILES['image']['name'] ;
	$tmpname = $_FILES['image']['tmp_name'] ;
	$filetype = $_FILES['image']['type'] ;
	$filesize = $_FILES['image']['size'] ;    
	$file = NULL ;
	$url = '';
	if($_FILES['image']['error']==0) $file = base64_encode(file_get_contents($tmpname)) ;
	
			// move_uploaded_file(source file, target file);
	
	if ($_POST['cat'] == 2) { //修改
		if ($file != NULL) {
			$str = 'aPic ="'.$file.'",';
		}

		$sql = "UPDATE
					tAppNews
				SET
					aTitle = '".$_POST['title']."',
					aContent = '".$_POST['content']."',
					".$str."
					aEditor = '".$_SESSION['member_id']."'
				WHERE
					aId = '".$id."'
				";
		$conn->Execute($sql);

	}elseif ($_POST['cat'] == 1) { //新增
		$sql = "INSERT INTO
				tAppNews 
				(
					aTitle,
					aContent,
					aPic,
					aCreator,
					aCreatDate
				)VALUES(
					'".$_POST['title']."',
					'".$_POST['content']."',
					'".$file."',
					'".$_SESSION['member_id']."',
					'".date('Y-m-d H:i:s')."'
				) ";
		$conn->Execute($sql);
		$id = $conn->Insert_ID();
	}elseif ($_POST['cat'] == 3) {
		$sql = "UPDATE
					tAppNews
				SET
					aDel = 1,
					aEditor = '".$_SESSION['member_id']."'
				WHERE
					aId = '".$id."'";
				
		$conn->Execute($sql);

		echo "<script>parent.$.fn.colorbox.close();</script>";
	}

	// echo $sql;
}



$sql = "SELECT
			aId,
			aTitle,
			aContent,
			aPic,
			(SELECT pName FROM tPeopleInfo WHERE pId = aCreator) AS aCreator,
			(SELECT pName FROM tPeopleInfo WHERE pId = aEditor) AS aEditor,
			aModifyDate,
			aCreatDate
		FROM
			tAppNews
		WHERE
			aDel = 0 AND aId ='".$id."'";
$rs = $conn->Execute($sql);
###
$smarty->assign('data',$rs->fields);
$smarty->assign('cat',$cat);
$smarty->display('mobileNewsEdit.inc.tpl', '', 'mobile');
?>
