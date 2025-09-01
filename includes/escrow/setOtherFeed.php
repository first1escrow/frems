<?php
include_once '../../openadodb.php';
include_once '../../session_check.php' ;

$_POST = escapeStr($_POST) ;

$sId = array();

$type = $_POST['type'];
$cat = $_POST['cat'];

if ($type == 69) {
	if ($_POST['brand'] == 69 || $_POST['brand1'] == 69 || $_POST['brand2'] == 69) {
		$sql = "SELECT bScrivenerFeed,bScrivenerRecall FROM tBrand WHERE bId = 69";
		$rs = $conn->Execute($sql);
		$rs->fields['bScrivenerRecall'] = $rs->fields['bScrivenerRecall']/100;
		$msg[]= $rs->fields['bScrivenerFeed']."_".$rs->fields['bScrivenerRecall'];
	}


	if (is_array($msg)) {
		echo implode(',', $msg);
	}else{ //(更改後沒有符合，去查詢同保證號碼下的)
		$sql = "SELECT bScrivenerFeed FROM tBrand WHERE bScrivenerFeed != 0";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$data[] = $rs->fields;
			$rs->MoveNext();
		}
		$msg[] = 'del';
		for ($i=0; $i < count($data); $i++) { 
			// $sql = "UPDATE tFeedBackMoney SET fDelete = 1 WHERE fType = 1 AND fCertifiedId ='".$_POST['cId']."' AND fStoreId ='".$data[$i]['bScrivenerFeed']."'";
			$sql = "SELECT fId FROM tFeedBackMoney WHERE fType = 1 AND fCertifiedId ='".$_POST['cId']."'  AND fStoreId ='".$data[$i]['bScrivenerFeed']."' AND fDelete =0 AND fCreatEditor NOT IN('吳佩琦','--------') ";
			// echo $sql;
			$rs = $conn->Execute($sql);
			$msg[] = $rs->fields['fId'];
			if ($_POST['c']) {
				$sql = "UPDATE tFeedBackMoney SET fMark = 1 WHERE fId ='".$rs->fields['fId']."'";
				$conn->Execute($sql);
			}
			
		}


		if (is_array($msg)) {
			echo implode(',', $msg);
		}
	}
}elseif ($cat == 'g') { //群組
	$total = 0;
	if ($_POST['branch'] > 0) {
		$checkBranch[] = $_POST['branch'];
	}

	if ($_POST['branch1'] > 0) {
		$checkBranch[] = $_POST['branch1'];
	}

	if ($_POST['branch2'] > 0) {
		$checkBranch[] = $_POST['branch2'];
	}

	// print_r($_POST);

	if (is_array($checkBranch)) {
		$sql = "SELECT bId FROM tBranch WHERE bId IN(".implode(',', $checkBranch).") AND bGroup = '".$type."'";
		// echo $sql;
		$rs = $conn->Execute($sql);
		$total += $rs->RecordCount();
	}
	// echo $total;
	// die;

	$sql = "SELECT bRecall,bBranch FROM tBranchGroup WHERE bId ='".$type."'";

	$rs = $conn->Execute($sql);
	$recall = $rs->fields['bRecall'];
	$storeId = $rs->fields['bBranch'];

	if ($total > 0) {
		$recall = (count($checkBranch) != $total)? (($recall/2)/100):($recall/100); //有它排要除以2 

		$sql = "SELECT fId FROM tFeedBackMoney WHERE fType = 2 AND fCertifiedId ='".$_POST['cId']."'  AND fStoreId ='".$storeId."' AND fDelete =0 AND fCreatEditor NOT IN('吳佩琦','--------') ";
			// echo $sql;
		// echo $sql;
		// die;
			$rs = $conn->Execute($sql);
			
			if ($rs->fields['fId'] == '') {
				$msg[]= $storeId."_".$recall;
				echo implode(',', $msg);
			}else{
				$msg[]= $storeId."_".$recall;
				echo implode(',', $msg);
				// if (is_array($checkBranch)) {
				// 	$msg[]= 'del';
				// 	$msg[]= @implode('_', $checkBranch);
				// 	echo @implode(',', $msg);
				// }
			}
			
		
	}else{
		if (is_array($checkBranch)) {
					$msg[]= 'del';
					$msg[]= @implode('_', $checkBranch);
					echo @implode(',', $msg);
				}
	}

	//店ID _ 回饋比率
	
}elseif ($cat == 'b') {//群義
	$total = 0;
	if ($_POST['branch'] > 0) {
		$checkBranch[] = $_POST['branch'];
	}

	if ($_POST['branch1'] > 0) {
		$checkBranch[] = $_POST['branch1'];
	}

	if ($_POST['branch2'] > 0) {
		$checkBranch[] = $_POST['branch2'];
	}

	if (is_array($checkBranch)) {
		$sql = "SELECT bId FROM tBranch WHERE bId IN(".implode(',', $checkBranch).") AND bBrand = '".$type."'";

		$rs = $conn->Execute($sql);
		$total += $rs->RecordCount();
	}

	

	$sql = "SELECT bRecall,bBranch FROM tBrand WHERE bId ='".$type."'";
	// echo $sql;
	$rs = $conn->Execute($sql);
	$recall = $rs->fields['bRecall'];
	$storeId = $rs->fields['bBranch'];

	if ($total > 0) {

		
		$recall = (count($checkBranch) != $total)? (($recall/2)/100):($recall/100); //有它排要除以2 
		$sql = "SELECT fId FROM tFeedBackMoney WHERE fType = 2 AND fCertifiedId ='".$_POST['cId']."'  AND fStoreId ='".$storeId."' AND fDelete =0 AND fCreatEditor NOT IN('吳佩琦','--------') ";
			// echo $sql;
			$rs = $conn->Execute($sql);
			
			if ($rs->fields['fId'] == '') {
				$msg[]= $storeId."_".$recall;
				echo implode(',', $msg);
			}else{
				$msg[]= $storeId."_".$recall;
				echo implode(',', $msg);
				// if (is_array($checkBranch)) {
				// 	$msg[]= 'del';
				// 	$msg[]= @implode('_', $checkBranch);
				// 	echo @implode(',', $msg);
				// }
			}
			
		
	}else{
		if (is_array($checkBranch)) {
					$msg[]= 'del';
					$msg[]= @implode('_', $checkBranch);
					$msg[] = 'b';
					echo @implode(',', $msg);
				}
		
	}

}


?>