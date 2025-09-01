<?php

include_once 'configs/config.class.php';
include_once 'class/contract.class.php';
include_once 'openadodb.php' ;


$contract = new Contract();

$sql = 'SELECT 
			ccase.cCertifiedId,
			ccase.cCaseFeedback,
			ccase.cCaseFeedback1,
			ccase.cCaseFeedback2,
			ccase.cFeedbackTarget,
			ccase.cFeedbackTarget1,
			ccase.cFeedbackTarget2,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cs.cScrivener 
		FROM 
			tContractCase AS ccase 
		LEFT JOIN 
			tContractRealestate AS cr
		ON
			ccase.cCertifiedId = cr.cCertifyId
		LEFT JOIN
			tContractScrivener AS cs
		ON
			cs.cCertifiedId = ccase.cCertifiedId
		';

$rs = $conn->Execute($sql) ;

while (!$rs->EOF) {
	if( $rs->fields['cBranchNum'] >0)
	{
		$sql1=check($rs->fields['cBranchNum'],$rs->fields['cScrivener']);
		$data=$conn->Execute($sql1);
		while (!$data->EOF) 
		{
			$sales=$data->fields['Sales'];
			$contract->AddContract_Sales($rs->fields['cCertifiedId'],$rs->fields['cFeedbackTarget'],$sales,$rs->fields['cBranchNum']);
			echo $rs->fields['cCertifiedId'].",".$sales.",".$rs->fields['cBranchNum']."<br>";
			$data->MoveNext() ;
		}
			 
	}

	if ($rs->fields['cBranchNum1'] >0)
	{
		$sql1=check($rs->fields['cBranchNum1'],$rs->fields['cScrivener']);
		
		$data=$conn->Execute($sql1);
		
		while (!$data->EOF) 
		{
			$sales=$data->fields['Sales'];
			$contract->AddContract_Sales($rs->fields['cCertifiedId'],$rs->fields['cFeedbackTarget1'],$sales,$rs->fields['cBranchNum1']);
			echo $rs->fields['cCertifiedId'].",".$sales.",".$rs->fields['cBranchNum1']."<br>";
			$data->MoveNext() ;
		}
	}

	if( $rs->fields['cBranchNum2'] >0)
	{
		$sql1=check($rs->fields['cBranchNum2'],$rs->fields['cScrivener']);
		
		$data=$conn->Execute($sql1);
		while (!$data->EOF) 
		{
		  $sales=$data->fields['Sales'];
		  $contract->AddContract_Sales($rs->fields['cCertifiedId'],$rs->fields['cFeedbackTarget2'],$sales,$rs->fields['cBranchNum2']);
		  echo $rs->fields['cCertifiedId'].",".$sales.",".$rs->fields['cBranchNum2']."<br>";
		  $data->MoveNext() ;
		}
	}

	

	$rs->MoveNext() ;
}

function check($branch,$scrivener)
{

	
		if($branch!=505)//1房仲 2地政士
		{
			
			$sql1='SELECT
						a.bId,
						a.bSales AS Sales,
						(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName
					FROM
						tBranchSales AS a,
						tBranch AS b
					WHERE
						bBranch='.$branch.' AND
						b.bId=a.bBranch 
						';			
		}else
		{
			$sql1='SELECT
				a.sId,
				a.sSales AS Sales,
				(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName
			FROM
				tScrivenerSales AS a,
				tScrivener AS b
			WHERE
				a.sScrivener='.$scrivener.' AND
				b.sId=a.sScrivener
			ORDER BY
				sId
			ASC';			
		}

		
	return $sql1;
}

?>