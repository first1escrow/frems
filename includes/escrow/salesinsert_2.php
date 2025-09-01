<?php
include_once '../../openadodb.php' ;
//業務增加
$id = '080020095';
##
$sql="SELECT
		 cr.cBranchNum,
		 cr.cBranchNum1,
		 cr.cBranchNum2,
		 cc.cFeedbackTarget,
		 cc.cFeedbackTarget1,
		 cc.cFeedbackTarget2,
		 cc.cSpCaseFeedBackMoney,
		 cc.cSignDate,
		 (SELECT cScrivener FROM tContractScrivener AS cs WHERE cs.cCertifiedId=cr.cCertifyId) AS cScrivener
	  FROM 
	  	tContractRealestate AS cr
	  LEFT JOIN
	  	tContractCase AS cc ON cc.cCertifiedId=cr.cCertifyId
	  WHERE 
	  	cr.cCertifyId='".$id."'
	  	";

$rs = $conn->Execute($sql) ;

$list = $rs->fields;


##
//檢查是否有tContractSales.cBranch!= tContractRealestate的店編，有就刪除

$sql= "SELECT * FROM tContractSales WHERE cCertifiedId='".$id."'";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	if ($rs->fields['cBranch'] != $list['cBranchNum']) { //店更改了
				
		if ($rs->fields['cBranch'] != $list['cBranchNum1']) {
			if ($rs->fields['cBranch'] != $list['cBranchNum2']) {
						$error = 1;
						echo 'AA';
						
			}
		}

	}

	if ((($rs->fields['cTarget'] != $list['cFeedbackTarget']) &&($list['cBranchNum'] > 0)) || (($rs->fields['cTarget'] != $list['cFeedbackTarget1']) &&($list['cBranchNum1'] > 0)) || (($rs->fields['cTarget'] != $list['cFeedbackTarget2']) &&($list['cBranchNum2'] > 0))) { //回饋對象更改了				
		$error = 1;
		echo 'BB';

	}


	$rs->MoveNext();
}
$count = 0;
$total=$rs->RecordCount();
if ($list['cBranchNum'] != 0) {
	$count = $count +1;
	
}

if ($list['cBranchNum1'] != 0 && $list['cBranchNum1'] != '') {
	$count = $count +1;
	
}

if ($list['cBranchNum2'] != 0 && $list['cBranchNum1'] != '') {
	$count = $count +1;
	
}
echo $total."_".$count."\r\n";
if ($total == 0 ||($count != $total)) {$error = 1; }//沒有業務 多增加店家
##	
// echo $list["cSignDate"].":".$data_case['cSignDate'];
// if ($list["cSignDate"] != $data_case['cSignDate']) {

// 	$error = 1;

// }
echo $error;
die;
if ($error) {

	// echo $error;

	// $sql = "DELETE FROM tContractSales WHERE cCertifiedId = '".$id."'";
	// $conn->Execute($sql);

	if ($list['cBranchNum'] > 0) {
		// $sales[] = Sales($id,$list['cBranchNum'],$list['cFeedbackTarget'],$list['cScrivener']);
		if($list['cBranchNum'] ==505 || $list['cFeedbackTarget'] == 2){
			$sales[] = Sales($id,$list['cBranchNum'],$list['cScrivener'],$list['cFeedbackTarget'],$list['cSignDate']);
		}else{
			$sales[] = Sales($id,$list['cBranchNum'],$list['cScrivener'],$list['cFeedbackTarget'],$list['cSignDate']);
		}

	}

	if ($list['cBranchNum1'] > 0) {
		// $sales[] = Sales($id,$list['cBranchNum1'],$list['cFeedbackTarget1'],$list['cScrivener']);
		// $sales[] = Sales($id,$list['cBranchNum1'],$list['cFeedbackTarget1'],$list['cSignDate']);
		if($list['cBranchNum1']==505 || $list['cFeedbackTarget1'] == 2){
			$sales[] = Sales($id,$list['cBranchNum1'],$list['cScrivener'],$list['cFeedbackTarget1'],$list['cSignDate']);
		}else{
			$sales[] = Sales($id,$list['cBranchNum1'],$list['cScrivener'],$list['cFeedbackTarget1'],$list['cSignDate']);
		}
	}

	if ($list['cBranchNum2'] > 0) {
		// $sales[] = Sales($id,$list['cBranchNum2'],$list['cFeedbackTarget2'],$list['cScrivener']);
		if($list['cBranchNum2']==505 || $list['cFeedbackTarget2'] == 2){
			$sales[] = Sales($id,$list['cBranchNum2'],$list['cScrivener'],$list['cFeedbackTarget2'],$list['cSignDate']);
		}else{
			$sales[] = Sales($id,$list['cBranchNum2'],$list['cScrivener'],$list['cFeedbackTarget2'],$list['cSignDate']);
		}
	}

	if ($list['cSpCaseFeedBackMoney'] > 0) {
		// $sales[] = Sales($id,$list['cBranchNum2'],$list['cFeedbackTarget2'],$list['cScrivener']);
		$sales[] = Sales($id,'',$list['cScrivener'],3,$list['cSignDate']);
		
	}


	for ($i=0; $i < count($sales); $i++) { 
		
		if (is_array($sales[$i])) {
			foreach ($sales[$i] as $k => $v) {
			
				
				// $contract->AddContract_Sales($id,$v['cFeedbackTarget'],$v['Sales'],$v['branch']);
				// 	write_log('變更店家或簽約日期'.$id.':target'.$v['cFeedbackTarget'].",sales".$v['Sales'].",OLDbranch".$list['cBranchNum']."_".$list['cBranchNum1']."_".$list['cBranchNum2'],'escrowSalse');
						
			}
		}
		
	}

	
}
##########








#######

function Sales($id,$branch,$scrivener,$cFeedbackTarget,$date)
{
	global $conn;


	//沒有簽約日 就用現在的把
	if ($date == '0000-00-00 00:00:00') {
		if($branch==505 || $cFeedbackTarget == 2){
						//地政士業務
			$sql='SELECT
					a.sId,
					a.sSales AS Sales,
					(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
					b.sOffice
				FROM
					tScrivenerSales AS a,
					tScrivener AS b
				WHERE
					a.sScrivener='.$scrivener.' AND
					b.sId=a.sScrivener
				ORDER BY
					sId
				ASC';
			
		}else{
			$sql='SELECT
						a.bId,
						a.bSales AS Sales,
						(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
						b.bName,
						b.bStore
					FROM
						tBranchSales AS a,
						tBranch AS b
					WHERE
						bBranch='.$branch.' AND
						b.bId=a.bBranch 

					ORDER BY
						bId
					ASC';
					
		}

		$rs = $conn->Execute($sql) ;
		$i = 0;
		while (!$rs->EOF) {

			$list[$i]['Sales'] = $rs->fields['Sales'];
			$list[$i]['cFeedbackTarget'] = $cFeedbackTarget;
			$list[$i]['branch'] = $branch;


			$i++;
			$rs->MoveNext() ;
		}

	}else{
		if($branch==505 || $cFeedbackTarget == 2 || $cFeedbackTarget == 3){
			$type  = 1;	
			$store = $scrivener;
		}else{
			$type  = 2;		
			$store = $branch;
		}
		$sql = "SELECT sCreatTime,sSales FROM tSalesAreaLog WHERE sType = '".$type."' AND sBranch ='".$store."' AND sCreatTime <= '".$date."' ORDER BY sCreatTime DESC LIMIT 1";
			$rs = $conn->Execute($sql) ;
			$i = 0;
			while (!$rs->EOF) {

				$list[$i]['Sales'] = $rs->fields['sSales'];
				$list[$i]['cFeedbackTarget'] = $cFeedbackTarget;
				$list[$i]['branch'] = $branch;

				if ($cFeedbackTarget == 3) {
					$list[$i]['branch'] = $store;
				}


				$i++;
				$rs->MoveNext() ;
			}


		if ($i == 0) {
			if($branch==505 || $cFeedbackTarget == 2){
						//地政士業務
				$sql='SELECT
						a.sId,
						a.sSales AS Sales,
						(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
						b.sOffice
					FROM
						tScrivenerSales AS a,
						tScrivener AS b
					WHERE
						a.sScrivener='.$scrivener.' AND
						b.sId=a.sScrivener
					ORDER BY
						sId
					ASC';
				
			}else{
				$sql='SELECT
							a.bId,
							a.bSales AS Sales,
							(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
							b.bName,
							b.bStore
						FROM
							tBranchSales AS a,
							tBranch AS b
						WHERE
							bBranch='.$branch.' AND
							b.bId=a.bBranch 

						ORDER BY
							bId
						ASC';
						
			}

			$rs = $conn->Execute($sql) ;
			
			while (!$rs->EOF) {

				$list[$i]['Sales'] = $rs->fields['Sales'];
				$list[$i]['cFeedbackTarget'] = $cFeedbackTarget;
				$list[$i]['branch'] = $branch;


				$i++;
				$rs->MoveNext() ;
			}
		}

		

	}


	

		// print_r($list);

		return $list;
}



##
// if($realestate_branch  != $data_realestate->fields['cBranchNum']||$cFeedbackTarget != $data_realestate->fields['cFeedbackTarget'])
// {
// 	$sql="DELETE FROM tContractSales WHERE cCertifiedId='".$_POST['certifiedid']."' AND cBranch = ".$data_realestate->fields['cBranchNum'];//刪除原有的

// 	$conn->Execute($sql);

// 	##仲介店一
// 	if(!empty($_POST['realestate_branchnum']))
// 	{
// 		if($_POST['realestate_branchnum']==505 || $cFeedbackTarget == 2)
// 		{
// 						//地政士業務
// 			$sql='SELECT
// 					a.sId,
// 					a.sSales AS Sales,
// 					(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
// 					b.sOffice
// 				FROM
// 					tScrivenerSales AS a,
// 					tScrivener AS b
// 				WHERE
// 					a.sScrivener='.$_POST['scrivener_id'].' AND
// 					b.sId=a.sScrivener
// 				ORDER BY
// 					sId
// 				ASC';
			
// 		}else
// 		{
// 			$sql='SELECT
// 						a.bId,
// 						a.bSales AS Sales,
// 						(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
// 						b.bName,
// 						b.bStore
// 					FROM
// 						tBranchSales AS a,
// 						tBranch AS b
// 					WHERE
// 						bBranch='.$_POST['realestate_branch'].' AND
// 						b.bId=a.bBranch 

// 					ORDER BY
// 						bId
// 					ASC';
					
// 		}

// 			$rs = $conn->Execute($sql) ;

// 		while (!$rs->EOF) {

// 					$contract->AddContract_Sales($_POST['certifiedid'],$_POST['cFeedbackTarget'],$rs->fields['Sales'],$_POST['realestate_branch']);
// 					write_log('程式帶'.$_POST['certifiedid'].':target'.$_POST['cFeedbackTarget'].",sales".$rs->fields['Sales'].",branch".$_POST['realestate_branch'],'escrowSalse');
// 					$rs->MoveNext() ;
// 				}

// 	}
// }

// if($realestate_branch1  !=$data_realestate->fields['cBranchNum1'] ||$cFeedbackTarget1 != $data_realestate->fields['cFeedbackTarget1'])
// {
// 	$sql="DELETE FROM tContractSales WHERE cCertifiedId='".$_POST['certifiedid']."' AND cBranch = ".$data_realestate->fields['cBranchNum1'];//刪除原有的

// 	$conn->Execute($sql);

// 	##仲介店二

// 	if(!empty($_POST['realestate_branchnum1']))
// 	{
// 		if($_POST['realestate_branchnum1']==505 || $cFeedbackTarget1 == 2)
// 		{
// 						//地政士業務
// 			$sql='SELECT
// 					a.sId,
// 					a.sSales AS Sales,
// 					(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
// 					b.sOffice
// 				FROM
// 					tScrivenerSales AS a,
// 					tScrivener AS b
// 				WHERE
// 					a.sScrivener='.$_POST['scrivener_id'].' AND
// 					b.sId=a.sScrivener
// 				ORDER BY
// 					sId
// 				ASC';
			
// 		}else
// 		{
// 			$sql='SELECT
// 						a.bId,
// 						a.bSales AS Sales,
// 						(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
// 						b.bName,
// 						b.bStore
// 					FROM
// 						tBranchSales AS a,
// 						tBranch AS b
// 					WHERE
// 						bBranch='.$_POST['realestate_branch1'].' AND
// 						b.bId=a.bBranch 

// 					ORDER BY
// 						bId
// 					ASC';
					
// 		}
			
// 			$rs = $conn->Execute($sql) ;

// 		while (!$rs->EOF) {
// 					$contract->AddContract_Sales($_POST['certifiedid'],$_POST['cFeedbackTarget1'],$rs->fields['Sales'],$_POST['realestate_branch1']);
// 					write_log('程式帶'.$_POST['certifiedid'].':target'.$_POST['cFeedbackTarget1'].",sales".$rs->fields['Sales'].",branch".$_POST['realestate_branch1'],'escrowSalse');
// 					$rs->MoveNext() ;
// 				}
// 	}
	
// }

// if($realestate_branch2 !=$data_realestate->fields['cBranchNum2'] ||$cFeedbackTarget2 != $data_realestate->fields['cFeedbackTarget2'])
// {
// 	$sql="DELETE FROM tContractSales WHERE cCertifiedId='".$_POST['certifiedid']."' AND cBranch = ".$data_realestate->fields['cBranchNum2'];//刪除原有的

// 	$conn->Execute($sql);

// 	##仲介店 三

// 	if(!empty($_POST['realestate_branchnum2']))
// 	{
// 		if($_POST['realestate_branchnum2']==505 || $cFeedbackTarget2 == 2)
// 		{
// 						//地政士業務
// 			$sql='SELECT
// 					a.sId,
// 					a.sSales AS Sales,
// 					(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
// 					b.sOffice
// 				FROM
// 					tScrivenerSales AS a,
// 					tScrivener AS b
// 				WHERE
// 					a.sScrivener='.$_POST['scrivener_id'].' AND
// 					b.sId=a.sScrivener
// 				ORDER BY
// 					sId
// 				ASC';
			
// 		}else
// 		{
// 			$sql='SELECT
// 						a.bId,
// 						a.bSales AS Sales,
// 						(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
// 						b.bName,
// 						b.bStore
// 					FROM
// 						tBranchSales AS a,
// 						tBranch AS b
// 					WHERE
// 						bBranch='.$_POST['realestate_branch2'].' AND
// 						b.bId=a.bBranch 

// 					ORDER BY
// 						bId
// 					ASC';
					
// 		}
			
// 			$rs = $conn->Execute($sql) ;

// 		while (!$rs->EOF) {
// 					$contract->AddContract_Sales($_POST['certifiedid'],$_POST['cFeedbackTarget2'],$rs->fields['Sales'],$_POST['realestate_branch2']);
// 					write_log('程式帶'.$_POST['certifiedid'].':target'.$_POST['cFeedbackTarget2'].",sales".$rs->fields['Sales'].",branch".$_POST['realestate_branch2'],'escrowSalse');
// 					$rs->MoveNext() ;
// 				}
// 	}
	
// }
// ##