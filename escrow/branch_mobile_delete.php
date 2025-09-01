<?php

include_once '../openadodb.php' ;

$mobile = trim($_POST['mol']) ;
$id = trim($_POST['id']) ;
$branch = trim($_POST['branch']) ;

//更新備勾選的

$sql="SELECT b.cBranchNum,b.cBranchNum1,b.cBranchNum2,b.cSmsTarget,b.cSmsTarget1,b.cSmsTarget2,b.cCertifyId FROM tBranchSms As a ,tContractRealestate AS b WHERE a.bDel = 0 AND a.bCheck_id=b.cCertifyId  AND a.bId =".$id;

$rs=$conn->Execute($sql);

if($rs->fields['cBranchNum']==$branch)
{
	$tmp = explode(',', $rs->fields['cSmsTarget']);
	$index= '';
	
}elseif ($rs->fields['cBranchNum1']==$branch) {
	$tmp = explode(',', $rs->fields['cSmsTarget1']);
	$index= 1;
}elseif ($rs->fields['cBranchNum2']==$branch) {
	$tmp = explode(',', $rs->fields['cSmsTarget2']);
	$index= 2;
}

for ($i=0; $i <count($tmp) ; $i++) { 
		
		if($mobile == $tmp[$i])
		{
			unset($tmp[$i]);
		}
}

$phone = implode(',', $tmp);
unset($tmp);



$sql = 'UPDATE tContractRealestate SET cSmsTarget'.$index.'="'.$phone.'" WHERE cCertifyId="'.$rs->fields['cCertifyId'].'"' ;
$conn->Execute($sql);


$sql="DELETE FROM tBranchSms WHERE bId =".$id;
$conn->Execute($sql);

//要返回的畫面
echo 'formcasesmsrealty.php?bid='.$branch.'&cid='.$rs->fields['cCertifyId'].'&ok=1&in='.($index+1);

?>