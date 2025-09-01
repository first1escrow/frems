<?php
include_once '../openadodb.php';

##新增
$identity = addslashes(trim($_POST['identity']));
$cid = addslashes(trim($_POST['cCertifiedId']));

$type = addslashes(trim($_POST['type']));

$remark = addslashes(trim($_POST['remark']));

##
##刪除
$id = addslashes(trim($_POST['id']));
##

switch ($type) {
	case 'add':

			add($conn,$cid,$identity,$remark);
		break;
	case 'delete': 
		delete_tax($conn,$id);
		
		break;

}


function add($conn,$cid,$identity,$remark)
{
	$sql="INSERT INTO tChecklistRemark (cCertifiedId,cIdentity,cRemark)  VALUES ('".$cid."','".$identity."','".$remark."')";

	// echo $sql;
	$conn->Execute($sql);
		
	
}

function delete_tax($conn,$id)
{
	$sql="DELETE FROM tChecklistRemark WHERE cId='".$id."'";
	// echo $sql;
	$conn->Execute($sql);
}

die();
?>