<?php

include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/brand.class.php';
include_once '../../tracelog.php' ;
include_once '../../openadodb.php';

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '編修仲介群組') ;

$brand = new Brand();
$_POST['signDate'] = (substr($_POST['signDate'], 0,3)+1911).substr($_POST['signDate'], 3);
$brand->SaveGroup($_POST);


if ($data['signDate'] != $_POST['signDate'] || $_POST['bRecall'] != $data['bRecall'] || $_POST['TargetBranch'] != $data['bBranch']) {

	##先查詢群組的店家
	$sql = "SELECT bId FROM tBranch WHERE bGroup = '".$_POST['id']."'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$branchData[] = $rs->fields['bId'];

		$rs->MoveNext();
	}
	##
	
	$startDate = date("Y-m-d", strtotime($_POST['signDate']."+1 day"));

	$recall = $_POST['bRecall']/100;
	$store = $_POST['TargetBranch'];

	$str .= " AND cc.cSignDate >= '".$startDate." 00:00:00' AND (cBranchNum IN (".@implode(',', $branchData).") OR cBranchNum1 IN (".@implode(',', $branchData).") OR cBranchNum2 IN (".@implode(',', $branchData)."))";
	unset($branchData);

	$sql ="SELECT 
            cc.cCertifiedId AS cCertifiedId,
            ci.cTotalMoney AS cTotalMoney,
            ci.cCertifiedMoney AS cerifiedmoney,
            cr.cBrand AS brand,
            cr.cBrand1 AS brand1,
            cr.cBrand2 AS brand2,
            cr.cBranchNum AS branch,
            cr.cBranchNum1 AS branch1,
            cr.cBranchNum2 AS branch2,
            (SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum) AS branchgroup,
            (SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum1) AS branchgroup1,
            (SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum2) AS branchgroup2
        FROM 
            tContractCase AS cc
        JOIN tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
        JOIN tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
        JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
        WHERE 
             ci.cTotalMoney != 0 AND cc.cCaseFeedBackModifier = '' AND ci.cCertifiedMoney !=0 AND cc.cFeedBackClose = 0 ".$str."
        ";
      
     // echo $sql."\r\n";
       
        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
        	$groupTotalCount = 0;
        	$groupCount = 0;//品牌

        	if ($rs->fields['branchgroup'] > 0) { 
	        	if ($rs->fields['branchgroup'] == $_POST['id']) {
	        		$groupCount++;
	        	}
	        	$groupTotalCount++;
	        }

	        if ($rs->fields['branchgroup1'] > 0) {
	        	if ($rs->fields['branchgroup1'] == $_POST['id']) {
	        		$groupCount++;
	        	}
	        	$groupTotalCount++;
	        }

	        if ($rs->fields['branchgroup2'] > 0) {
	        	if ($rs->fields['branchgroup2'] == $_POST['id']) {
	        		$groupCount++;
	        	}
	        	$groupTotalCount++;
	        }



	        $money = round((($rs->fields['cerifiedmoney'] * $recall)*$groupCount)/$groupTotalCount) ;//回饋金 配件要平分

        	if ($rs->fields['branchgroup'] == $_POST['id'] || $rs->fields['branchgroup1'] == $_POST['id'] || $rs->fields['branchgroup2'] == $_POST['id']) {
        		// echo 'A'.$_POST['id'];
        		if (!checkBrandFeed($rs->fields['cCertifiedId'],$_POST['TargetBranch'])) { //檢查是否有資料
        			addFeedbackmoney($rs->fields['cCertifiedId'],$_POST['TargetBranch'],$money);
	        		$CertifiedId[] = $rs->fields['cCertifiedId'];
	        	}else{
	        		updateFeedbackmoney($rs->fields['cCertifiedId'],$_POST['TargetBranch'],$money);
	        		$CertifiedId[] = $rs->fields['cCertifiedId'];
	        	}
        	}
        	

        	
        	$rs->MoveNext();
        }


}

function checkBrandFeed($cId,$branch){
	global $conn;

	if (!$branch) {
		return false;
	}

	$sql = "SELECT fId FROM tFeedBackMoney WHERE fType = '2' AND fDelete = 0 AND fCertifiedId = '".$cId."' AND fStoreId = '".$branch."'";
	$rs = $conn->Execute($sql);
	$total = $rs->RecordCount();

	if ($total > 0) {
		return true; //有資料 不要新增
	}else{
		return false; //
	}
	

}
function addFeedbackmoney($cId,$branch,$money){
	global $conn;

	$sales = array();
	//先取得業務
	$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$branch."'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($sales, $rs->fields['bSales']);


		$rs->MoveNext();
	}

	$sql = "INSERT INTO
				tFeedBackMoney
			SET
				fType = 2,
				fCertifiedId = '".$cId."',
				fStoreId = '".$branch."',
				fMoney = '".$money."',
				fSales = '".@implode(',', $sales)."'
			";
	// echo $sql;
	$conn->Execute($sql);
	

}
function updateFeedbackmoney($cId,$branch,$money){
	global $conn;

	
	//
	$sql = "UPDATE
				tFeedBackMoney
			SET
				fMoney = '".$money."'
			WHERE
				fType = 2,
				fCertifiedId = '".$cId."',
				fStoreId = '".$branch."',
			";
	// echo $sql;
	$conn->Execute($sql);
	

}
###
if (is_array($CertifiedId)) {
	echo '回饋異動案件：'.implode(',', $CertifiedId)."\r\n";
}

echo "儲存完成";
?>
