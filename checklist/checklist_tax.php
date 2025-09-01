<?php
include_once '../openadodb.php';

##新增
$identity = addslashes(trim($_POST['identity']));
$type = addslashes(trim($_POST['type']));
$cid = addslashes(trim($_POST['cCertifiedId']));

$title = addslashes(trim($_POST['title']));
$tax = addslashes(trim($_POST['tax']));
$taxRemark = addslashes(trim($_POST['taxRemark']));

$otitle = addslashes(trim($_POST['otitle']));
$otax = addslashes(trim($_POST['otax']));
$otaxRemark = addslashes(trim($_POST['otaxRemark']));
##
##刪除
$id = addslashes(trim($_POST['id']));
##

switch ($type) {
	case 'add':

		
			if ($identity==1) {
				if ($title) {
				   add($conn,$cid,$identity,$type,$title,$tax,$taxRemark);
				}
				
			}else
			{

				if ($otitle) {
					add($conn,$cid,$identity,$type,$otitle,$otax,$otaxRemark);
				}
				
			}
			
		
		
		break;
	case 'delete': 
		delete_tax($conn,$id);
		
		break;

}


function add($conn,$cid,$identity,$type,$title,$tax,$taxRemark)
{
	$sql="INSERT INTO tChecklistOther (cCertifiedId,cIdentity,cTaxTitle,cTax,cTaxRemark)  VALUES ('".$cid."','".$identity."','".$title."','".$tax."','".$taxRemark."')";


	$conn->Execute($sql);
		
	
}

function delete_tax($conn,$id)
{
	$sql="DELETE FROM tChecklistOther WHERE cid='".$id."'";
	$conn->Execute($sql);
}
die();
?>