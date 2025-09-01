<?php
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




/*##tContractCase cCaseStatus

	$sql="UPDATE tContractCase SET cCaseStatus = 2 WHERE cCertifiedId='".$cid."'";
	
	//$conn->Execute($sql);
##	*/

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

	$sql = 'SELECT sMobile,sDefault,sSend FROM tScrivenerSms WHERE sScrivener="'.$data_scrivener['cScrivener'].'" AND sDel = 0  ORDER BY sNID,sId ASC' ;

	
	$rs = $conn->Execute($sql) ;
	$rs->fields['sMobile']."<br>";

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
$sql ="SELECT 
			cc.cCertifiedId AS cCertifiedId,
			ci.cTotalMoney AS cTotalMoney,
			ci.cCertifiedMoney as cerifiedmoney,
			cr.cBranchNum AS branch,
			cr.cBranchNum1 AS branch1,
			cr.cBranchNum2 AS branch2,
			cr.cBrand AS brand,
			cr.cBrand1 AS brand1,
			cr.cBrand2 AS brand2,
			(SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS bRecall1,
			(SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS bRecall2,
			(SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS bRecall3,
			(SELECT sRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sRecall,
			(SELECT sSpRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall,						
			cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
			cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
			cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
			cc.cCaseFeedback AS cCaseFeedback,
			cc.cCaseFeedback1 AS cCaseFeedback1,
			cc.cCaseFeedback2 AS cCaseFeedback2,
			cc.cFeedbackTarget AS cFeedbackTarget,
			cc.cFeedbackTarget AS cFeedbackTarget1,
			cc.cFeedbackTarget AS cFeedbackTarget2			
	    FROM 
	   		tContractCase AS cc
	   	JOIN 
	   		tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
	   	JOIN 
	   		tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
	   	JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
	   
	   	WHERE 
	   		 cc.cCertifiedId ='".$cid."'
	   		ORDER BY  cc.cEndDate ASC";

$rs= $conn->Execute($sql);

$list[] = $rs->fields; 


	
$part = 33.33; //預設回饋比率
$cTotalMoney = $list[0]['cTotalMoney']; //總價金
$cCertifiedMoney = $list[0]['cTotalMoney']* 0.0006 ; //保證費
$recall = $list[0]['bRecall1']; //回饋比率(店家1)
$recall1 = $list[0]['bRecall2']; //回饋比率(店家2)
$recall2 = $list[0]['bRecall3']; //回饋比率(店家3)
$sRecall = $list[0]['sRecall']; //地政士回饋比率
$cFeedbackTarget = $list[0]['cFeedbackTarget'];//回饋目標

//保證費
$sql = "UPDATE tContractIncome SET cCertifiedMoney='".$cCertifiedMoney."' WHERE cCertifiedId='".$cid."'";

// echo $sql."<br>";
$conn->Execute($sql);
##

if ($list[0]['branch1']=='0') { //如果沒有店家把比率設空
		
	unset($recall1);
}

if ($list[0]['branch2']=='0') {//如果沒有店家把比率設空
		
	unset($recall2);
}

	//以最小比率做為回饋金計算比率
	if ($list[0]['branch']!=0 && $list[0]['branch1']!=0 && $list[0]['branch2']!=0) {//轉成陣列(三家仲介)

		if ($recall=='') {$recall=$part;} //配件預設
		if ($recall1=='') {$recall1=$part;}//配件預設
		if ($recall2=='') {$recall2=$part;}	//配件預設
			
		$branchArr = array($recall,$recall1,$recall2) ;			
	}
	else if ($list[0]['branch']!=0 && $list[0]['branch1']!=0 && ($list[0]['branch2']== '0')) {//轉成陣列(二家仲介)

		if ($recall=='') {$recall=$part;}//配件預設
		if ($recall1=='') {$recall1=$part;}//配件預設
			$branchArr = array($recall,$recall1) ;					
	}
	else {//轉成陣列(一家仲介)
			if ($recall=='') {$recall=$part;}//配件預設
			$branchArr = array($recall) ;							
	}
	##
	if ($cTotalMoney <= 1000000) {				//當總價金小於等於100萬時，回饋金為200

				$bFb = 200 ;
				// echo 'bFb='.$bFb ;				//總回饋金
	}
	else {										//當總價金大於100萬時，回饋金需計算求得

		if ($cFeedbackTarget == '2') {
				if ($sRecall=='') {
					$sRecall=$part;
				}
			//回饋對象為代書
				$val = $sRecall / 100 ;	 //改為百分之X

				$bFb = round($cCertifiedMoney * $val) ;		//總回饋金 
				##
		}else{
				//回饋對象為仲介身分
				if ($recall && $recall1 && $recall2) {	//三家仲介
						// $branchArr = array($recall,$recall1,$recall2) ;			//轉成陣列並由小到大排列
					sort($branchArr) ;
					$val = $branchArr[0] ;								//取出最小值做為回饋金比率
						// $val = $part ;												//當配件案件時，回饋比率強制設定為2
				}
				else if ($recall && $recall1 && ($recall2 == '')) {	//兩家仲介
						// $branchArr = array($recall,$recall1) ;					//轉成陣列並由小到大排列
					sort($branchArr) ;
					$val = $branchArr[0] ;								//取出最小值做為回饋金比率
						// $val = $part ;												//當配件案件時，回饋比率強制設定為2
				}
				else {	//只有一家仲介
						// $branchArr = array($recall) ;
						$val = $branchArr[0] ;									//當非配件時，回饋比率依據仲介店定義
				}
					##

					
				
				$val = $val/100;  //20150121
					// echo $cCertifiedMoney."-".$val."<br>";
				$bFb = round($cCertifiedMoney * $val) ;		//總回饋金 
			}
		}	

		$branch_sql = '' ;
		//完成重新計算回饋金預備寫入資料庫
		if ($cFeedbackTarget == '2') {				//回饋對象為代書
			$branch_sql .= ' cCaseFeedBackMoney="'.$bFb.'", ' ;
		}else{										//回饋對象為仲介
				//計算各家仲介回饋金
				$bMax = count($branchArr) ;				//仲介店總數
				$bq = $bFb % $bMax ;					//餘數
				$br = floor($bFb / $bMax) ;				//商數
				
				
				foreach ($branchArr as $k => $v) {
					if ($k == 0) {
						$branchArr[$k] = $br + $bq ;		//首家仲介 = 商數 + 餘數

							// echo 'cCaseFeedBackMoney'.$k.'';

						
							$branch_sql .= ' cCaseFeedBackMoney="'.($br + $bq).'", ' ;
						
					}
					else {
						$branchArr[$k] = $br ;				//其餘仲介 = 商數
						

						
							$branch_sql .= ' cCaseFeedBackMoney'.$k.'="'.$br.'", ' ;
						
						
					}
				}
				##
		}
		
		$check = 0;
		if ($list[0]['brand']!=1&&$list[0]['brand']!=49&&$list[0]['brand']!=2) {//不是優美跟台屋的//不為非仲介成交

			$check=1;
							
		}elseif ($list[0]['brand1']!=1&&$list[0]['brand1']!=49&&$list[0]['brand1']!=0&&$list[0]['brand1']!=2) {
			$check=1;
		}elseif ($list[0]['brand2']!=1&&$list[0]['brand2']!=49&&$list[0]['brand2']!=0&&$list[0]['brand2']!=2) {
			$check=1;
		}
		// echo $v['brand']." ".$v['sSpRecall']." ".$check." ";
		if($list[0]['sSpRecall']!=0&&$check==1)
		{
				// $val = $tmp['sSpRecall'] / 10000 ;				//換算為萬分之x
				$val = $list[0]['sSpRecall'] / 100; //百分之X
				// $spFb = round($cTotalMoney * $val) ;//總回饋金

				// echo $cCertifiedMoney."<br>";
				$spFb = round($cCertifiedMoney * $val);//總回饋金

				// echo $spFb."<br>";
				$branch_sql .= 'cSpCaseFeedBackMoney = "'.$spFb.'", ';
				// echo  $branch_sql."<br>";

		}
					
			##
		
	
		

		$branch_sql =substr($branch_sql, 0,-2); //去掉逗號
		// echo $cCertifiedId.":".$branch_sql."<br>";
		
		if ($branch_sql!='') {
			$sql= "UPDATE tContractCase SET ".$branch_sql." WHERE cCertifiedId='".$cid."'";
			// echo $sql.";<br>";
			$conn->Execute($sql);
		}
		

		unset($branchArr);unset($branch_sql);

##




function SmsDefault($conn,$bid) {
    $sql = 'SELECT bMobile FROM tBranchSms WHERE bBranch="'.$bid.'" AND bDefault="1" AND bDel = 0 AND bNID NOT IN ("14","15") ORDER BY bNID,bId ASC;' ;
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