<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$id = ($_POST['id'])?$_POST['id']:$_GET['id'];

if ($_POST) {
	

	// echo count($_POST['newendDay'])."_".count($_POST['newnote']);

	// echo "<pre>";
	// print_r($_POST['newendDay']);

	// echo "<pre>";
	// print_r($_POST['newnote']);

	// echo "<pre>";
	// print_r($_POST['newday']);
	
	for ($i=0; $i < count($_POST['newendDay']); $i++) { 
		
		if ($_POST['newnote'][$i] !='' && $_POST['newendDay'][$i] != '' && $_POST['newday'][$i] > 0) {
			$sql = "
				INSERT INTO
					tLegalCaseDetail
				SET
					lCertifiedId = '".$_POST['id']."',
					lDay = '".$_POST['newday'][$i]."',
					lEndDay = '".$_POST['newendDay'][$i]."',
					lNote = '".$_POST['newnote'][$i]."',
					lCreator = '".$_SESSION['member_id']."',
					lCreatTime = '".date('Y-m-d H:i:s')."',
					lEditor = '".$_SESSION['member_id']."'
			";	
			// echo $sql."<br>";
			$conn->Execute($sql);
		}
	}

	for ($i=0; $i < count($_POST['lId']); $i++) { 
		$sql = "UPDATE
					tLegalCaseDetail
				SET 
					lDay = '".$_POST['day'][$i]."',
					lEndDay = '".$_POST['endDay'][$i]."',
					lNote = '".$_POST['note'][$i]."',
					lEditor = '".$_SESSION['member_id']."'
				WHERE 
					lId = '".$_POST['lId'][$i]."'";
		// echo $sql;
		$conn->Execute($sql);
	}

	
		// echo $sql;
	// 
	
}

##
$sql = "SELECT * FROM tLegalCase WHERE lCertifiedId = '".$id."'";
$rs = $conn->Execute($sql);
$case = $rs->fields;

##
$caseDetail = array();

$sql = "SELECT
			*
		FROM
			tLegalCaseDetail 
		WHERE
			lCertifiedId = '".$id."'";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	array_push($caseDetail, $rs->fields);

	$rs->MoveNext();
}

##
$menu_day = array();
$menu_day[0] = '請選擇天數';

for ($i=1; $i <=10 ; $i++) { 
	$menu_day[$i] = $i;
}
##
$smarty->assign('menu_day',$menu_day);
$smarty->assign('case',$case);
$smarty->assign('caseDetail',$caseDetail);
$smarty->assign('id',$id);
$smarty->display('legalCaseEdit.inc.tpl', '', 'legal');
?>