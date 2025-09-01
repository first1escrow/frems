<?php
include_once '../../configs/config.class.php';
include_once 'class/contract.class.php';
include_once 'class/brand.class.php';
include_once 'class/member.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../maintain/feedBackData.php';

$cid = trim($_POST['cid']);

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

##


##tContractRealestate.cSmsTarget  cSmsTarget1 cSmsTarget2 //仲介店簡訊 cName && 仲介店改代號

$query ='';



$tmp_branch = BranchCheck($conn,$data_realstate['cBranchNum']); //第一間店

$tmp_branch1 = BranchCheck($conn,$data_realstate['cBranchNum1']); //第二間店

// echo $tmp_branch."-".$tmp_branch1."<br><br>";


if ($tmp_branch != '-1') {
		
	$branch = $brand->GetBranch($tmp_branch);
}else
{
	$branch[0]['bId'] = $tmp_branch1;
}

$smsTarget = SmsDefault($conn,$branch[0]['bId']); //第一間店簡訊

$query .= " cSmsTarget ='".$smsTarget."',cBranchNum='".$branch[0]['bId']."',cName='".$branch[0]['bName']."',cSerialNumber='".$branch[0]['bSerialnum']."',";

$query .= " cTelArea = '".$branch[0]['bTelArea']."',cTelMain='".$branch[0]['bTelMain']."',cFaxArea='".$branch[0]['bFaxArea']."',cFaxMain='".$branch[0]['bFaxMain']."',";

$query .= " cZip='".$branch[0]['bZip']."',cAddress='".$branch[0]['bAddress']."'";

##
//第二間店
if ($data_realstate['cBranchNum1']!=0) {

	

	if ($tmp_branch1 != '-1') {
		
		$branch1 = $brand->GetBranch($tmp_branch1);
	}else
	{
		$branch1[0]['bId'] = $tmp_branch1;
	}

	$smsTarget1 = SmsDefault($conn,$branch1[0]['bId']); //第二間店簡訊

	if ($query!='') {$query .= ',';}

	$query .= " cSmsTarget1 ='".$smsTarget1."',cBranchNum1 ='".$branch1[0]['bId']."',cName1='".$branch1[0]['bName']."',cSerialNumber1='".$branch1[0]['bSerialnum']."',";

	$query .= " cTelArea1 = '".$branch1[0]['bTelArea']."',cTelMain1='".$branch1[0]['bTelMain']."',cFaxArea1='".$branch1[0]['bFaxArea']."',cFaxMain1='".$branch1[0]['bFaxMain']."',";

	$query .= " cZip1='".$branch1[0]['bZip']."',cAddress1='".$branch1[0]['bAddress']."'";

}

	if ($query!='') {
		$sql = "UPDATE tContractRealestate SET ".$query." WHERE cCertifyId='".$cid."'";
		// echo $sql."<Br>";
		$conn->Execute($sql);
	}
	
		
##

##tContractProperty

$sql = "SELECT cZip FROM tContractProperty WHERE cCertifiedId ='".$cid."' AND cItem = 0";

$rs = $conn->Execute($sql);

if ($rs->fields['cZip'] == '') {
	$sql = "SELECT cZip FROM tContractLand WHERE cCertifiedId ='".$cid."' AND cItem = 0";
	$tmp = $conn->Execute($sql);

	$sql = "UPDATE tContractProperty SET cZip ='".$tmp->fields['cZip']."' WHERE cCertifiedId ='".$cid."' AND cItem = 0";
	$conn->Execute($sql);
}


##

##tContractExpenditure
	$sql = "INSERT INTO tContractExpenditure (cCertifiedId) VALUES ('".$cid."')";
	// echo $sql."<Br>";
	$conn->Execute($sql);

##

## tContractInvoice cCertifiedId
	$sql = "INSERT INTO tContractInvoice (cCertifiedId) VALUES ('".$cid."')";
	// echo $sql."<Br>";
	$conn->Execute($sql);

##

## tContractScrivener
	//取出地政士的預設紀錄

	$sql = 'SELECT sMobile,sDefault,sSend FROM tScrivenerSms WHERE sScrivener="'.$data_scrivener['cScrivener'].'" AND sDel = 0 ORDER BY sNID,sId ASC' ;

	
	$rs = $conn->Execute($sql) ;
	

	$smsTarget = array() ;

	while (!$rs->EOF) {
		$tmp = $rs->fields ;
		if ($tmp['sDefault']==1) {
			$smsTarget[] = $tmp['sMobile'] ;
			
		}
		
		if ($tmp['sSend']==1) {
				$send[]=$tmp['sMobile'];
		}

		unset($tmp) ;
		
		$i ++ ;
		$rs->MoveNext() ;
	}
	##
	//複製到案件的預設簡訊對象
	if (count($smsTarget) > 0) {
		$sql = 'UPDATE tContractScrivener SET cSmsTarget="'.implode(',',$smsTarget).'",cSend2 = "'.@implode(',',$send).'" WHERE cCertifiedId="'.$cid.'" AND cScrivener="'.$data_scrivener['cScrivener'].'";' ;
		$conn->Execute($sql) ;
		// echo $sql."<br><br>";
	}
##
##


##
##回饋金

$cCertifiedMoney = $data_income['cTotalMoney'] *0.0006;
$sql = "UPDATE tContractIncome SET cCertifiedMoney ='".$cCertifiedMoney."' WHERE cCertifiedId ='".$cid."' ";

$conn->Execute($sql);
unset($cCertifiedMoney);
getFeedMoney('c',$cid);





echo "合約書已切換到第一經建";

function SmsDefault($conn,$bid) {
    $sql = 'SELECT bMobile FROM tBranchSms WHERE bBranch="'.$bid.'" AND bDefault="1" AND bNID NOT IN ("14","15") AND bDel = 0 ORDER BY bNID,bId ASC;' ;
    $rs = $conn->Execute($sql);

    $smsTarget = array() ; 

    while (!$rs->EOF) {
      	
      	$smsTarget[] = $rs->fields['bMobile'] ;

      	$rs->MoveNext();
    } 
    
  
    return implode(",",$smsTarget) ;
}

function BranchCheck($conn,$bid)
{

	$sql  = "SELECT bId FROM tCategoryRealty WHERE cId ='".$bid."'";

	$rs = $conn->Execute($sql);

	if ($rs->fields['bId'] ==0) {

		return -1;

	}else{
		
		return $rs->fields['bId'];
	}
	
}
?>
