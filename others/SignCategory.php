<?php
include_once '../configs/config.class.php';
include_once 'class/contract.class.php';
include_once 'class/brand.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once 'class/member.class.php';

$cid = addslashes(trim($_POST['CertifiedId']));

//預載物件
$contract = new Contract();

$brand = new Brand();

##
$data_case = $contract->GetContract($cid);
$data_realstate = $contract->GetRealstate($cid);
$data_scrivener = $contract->GetScrivener($cid);
$data_income = $contract->GetIncome($cid);

/*include 'AddCategory.php';
// include 'DeleteCategory.php';
die('+++++++++++++++');*/

##更改合約書狀態
$sql="UPDATE tContractCase SET cSignCategory='1' WHERE cCertifiedId='".$cid."'";
$conn->Execute($sql);

include 'AddCategory.php';

// die;

// if ($conn->Execute($sql)) {
	
// 	$txt='成功';
// 	if ($sign_category==1) {
// 		include 'AddCategory.php';
// 		$msg='更改為內部';
		
// 		 //
// 	}else
// 	{
// 		include 'DeleteCategory.php';
// 		$msg='更改為外部';
		
// 	}
	
// }else
// {
// 	$txt='失敗';
// }
##

##
header("Location:formCategoryList.php");

 ?>