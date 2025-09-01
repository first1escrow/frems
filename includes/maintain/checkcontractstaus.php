<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

$_POST = escapeStr($_POST) ;
$bId = trim($_POST['bId']);
$type = trim($_POST['type']);



if ($type=='b') {
	$sql = "
		SELECT
			cc.cCertifiedId
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
		WHERE
			(cc.cCaseStatus = 2 or cc.cCaseStatus = 10)  AND (cr.cBranchNum='".$bId."' OR cr.cBranchNum1='".$bId."' OR cr.cBranchNum2='".$bId."')";
	$rs = $conn->Execute($sql);

	$total=$rs->RecordCount();

	if ($total > 0) {
		// echo '有進行中案件，禁止更改狀態';
		echo '1';
	}
}elseif ($type=='s') {
	$sql = "
		SELECT
			cc.cCertifiedId
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
		WHERE
			(cc.cCaseStatus = 2  or cc.cCaseStatus = 10) AND cs.cScrivener ='".$bId."'";
		// echo $sql;
	$rs = $conn->Execute($sql);

	$total = $rs->RecordCount();



	if ($total > 0) {
		echo '1';
	}
}elseif ($type == 'bsames') { //store company brand 有查詢到非本店的案件

	if ($_POST['store'] != '') { //可能不會填寫店名
		$str = " AND bStore ='".$_POST['store']."'";
	}

	$sql = "SELECT
				*,
				(SELECT bName FROM tBrand AS br WHERE br.bId = bBrand) AS brand,
				CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as bCode
			FROM
				tBranch WHERE bStatus = 1 AND bBrand ='".$_POST['brand']."' AND bId !='".$bId."'".$str;

	$rs = $conn->Execute($sql);
	
	//如果有相同法人名稱和相同的店名，則不能儲存
	if ($_POST['company'] != '' && $rs->fields['bName'] == $_POST['company'] && $rs->fields['bStore'] == $_POST['store']) { 
		echo $rs->fields['bCode'].$rs->fields['brand'].$rs->fields['bStore']."(".$rs->fields['bName'].")";
	}
}elseif ($type == 'bsamea') {

	$sql = "SELECT
				 *,
				(SELECT bName FROM tBrand AS br WHERE br.bId = bBrand) AS brand,
				CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as bCode
			FROM
				tBranch WHERE bStatus = 1 AND bStore ='".$_POST['store']."' AND bBrand ='".$_POST['brand']."'";

	$rs = $conn->Execute($sql);

	//如果有相同法人名稱，則不能儲存
	if ($_POST['company'] != '' && $rs->fields['bName'] == $_POST['company']) { 
		echo $rs->fields['bCode'].$rs->fields['brand'].$rs->fields['bStore']."(".$rs->fields['bName'].")";
	}
}






die;
?>