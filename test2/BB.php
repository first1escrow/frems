<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;

$sql = "SELECT
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			fbm.fSales
		FROM
			tFeedBackMoney AS fbm
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId=fbm.fCertifiedId
		WHERE fbm.fDelete = 0
			";

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$list[] = $rs->fieleds;


	$rs->MoveNext();
}

foreach ($list as $k => $v) {
	$sales = array();
	if ($v['cBranchNum'] > 0) {
		$array = getBranhcSales($v['cBranchNum']);
		$sales = array_merge($sales,$array);
	}
	
	if ($v['cBranchNum1'] > 0) {
		$array = getBranhcSales($v['cBranchNum1']);
		$sales = array_merge($sales,$array);
	}

	if ($v['cBranchNum2'] > 0) {
		$array = getBranhcSales($v['cBranchNum2']);
		$sales = array_merge($sales,$array);
	}

	echo $v['fSales']."_";
	print_r($sales);
	echo "<br>";
	die;

}

function getBranhcSales($bId){
	global $conn;

	$sales = array();
	$sql = "SELECT bSales FROM tBranchSales WHERE bId = '".$bId."'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($sales, $rs->fieleds['bSales']);

		$rs->MoveNext();
	}

	return $sales;
}

?>